<?php
namespace TencentCloudBase\Storage;

// require_once 'src/consts/code.php';

// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
use TencentCloudBase\Consts\Code;
use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Utils\TcbBase;
use \Exception;

class TcbStorage extends TcbBase
{

  protected $config;

  function __construct($config)
  {
    parent::__construct($config);
  }

  public function uploadFile($options = [])
  {
    $params = array(
      'action' => 'storage.uploadFile',
      'path' => $options['cloudPath'],
      'file' => $options['fileContent']
    );

    $args = array();
    $args['params'] = $params;
    $args['method'] = 'post';
    $args['headers'] = array();
    $result = $this->cloudApiRequest($args);

    //
    if (array_key_exists('code', $result)) {
      // throw new TcbException($result['code'], $result['message'], $result['requestId']);
      return $result;
    }
    return [
      'fileID' => $result['data']['fileID'],
      'requestId' => $result['requestId'],
      // 'code' => $result['data']['message']
    ];
  }

  public static function encodeURI($url)
  {
    // http://php.net/manual/en/function.rawurlencode.php
    // https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/encodeURI
    $unescaped = array(
      '%2D' => '-', '%5F' => '_', '%2E' => '.', '%21' => '!', '%7E' => '~',
      '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')'
    );
    $reserved = array(
      '%3B' => ';', '%2C' => ',', '%2F' => '/', '%3F' => '?', '%3A' => ':',
      '%40' => '@', '%26' => '&', '%3D' => '=', '%2B' => '+', '%24' => '$'
    );
    $score = array(
      '%23' => '#'
    );
    return strtr(rawurlencode($url), array_merge($reserved, $unescaped, $score));
  }

  public function getTempFileURL($options = [])
  {

    if (!array_key_exists('fileList', $options) || !is_array($options['fileList'])) {
      // throw new TcbException(Code::INVALID_PARAM, '参数fileList类型必须是数据');
      return [
        'code' => Code::INVALID_PARAM,
        'message' => "fileList必须是非空的数组"
      ];
    }

    $fileList = $options['fileList'];
    $processFiles = array();

    foreach ($fileList as $file) {
      if (is_array($file)) {
        if (
          !array_key_exists('fileID', $file) ||
          !array_key_exists('maxAge', $file)
        ) {
          // throw new TcbException(Code::INVALID_PARAM, 'fileList的元素必须是包含fileID和maxAge的对象');
          return [
            'code' => Code::INVALID_PARAM,
            'message' => "fileList的元素必须是包含fileID和maxAge的对象"
          ];
        }

        array_push($processFiles, array(
          'fileid' => $file['fileID'],
          'max_age' => $file['maxAge']
        ));
      } elseif (is_string($file)) {
        array_push($processFiles, array(
          'fileid' => $file
        ));
      } else {
        // throw new TcbException(Code::INVALID_PARAM, 'fileList的元素必须是字符串');
        return [
          'code' => Code::INVALID_PARAM,
          'message' => "fileList的元素必须是字符串"
        ];
      }
    }

    $args = array();
    // $args['action'] = 'storage.batchGetDownloadUrl';
    $args['params'] = array(
      'file_list' => $processFiles,
      'action' => 'storage.batchGetDownloadUrl'
    );
    $args['method'] = 'post';
    $args['headers'] = array("content-type" => "application/json");

    $result = $this->cloudApiRequest($args);
    // print_r($result);

    // 如果 code存在，证明报错了
    if (array_key_exists('code', $result)) {
      // throw new TcbException($result['code'], $result['requestId']);
      return $result;
    }

    $tmpFiles = [];

    foreach ($result['data']['download_list'] as $file) {
      $tmpFiles = array_merge($tmpFiles, [
        [
          'code' => $file['code'],
          'fileID' => $file['fileID'],
          'tempFileURL' => isset($file['tempFileURL']) ? $file['tempFileURL'] : ""
        ]
      ]);
    }

    return [
      'fileList' => $tmpFiles,
      'requestId' => $result['requestId']
    ];
  }

  public function deleteFile($options = [])
  {

    if (!array_key_exists('fileList', $options) || !is_array($options['fileList'])) {
      throw new TcbException(Code::INVALID_PARAM, '参数fileList类型必须是数据');
    }

    $fileList = $options['fileList'];

    foreach ($fileList as $file) {
      if (!is_string($file)) {
        throw new TcbException(Code::INVALID_PARAM, 'fileList的元素必须是非空的字符串');
      }
    }

    $args = array();
    // $args['action'] = 'storage.batchDeleteFile';
    $args['params'] = array(
      'fileid_list' => $fileList,
      'action' => 'storage.batchDeleteFile'
    );
    $args['method'] = 'post';
    $args['headers'] = array("content-type" => "application/json");

    try {
      $result = $this->cloudApiRequest($args);

      // 如果 code 和 message 存在，证明报错了
      if (array_key_exists('code', $result)) {
        // throw new TcbException($result->code, $result->message, $result->RequestId);
        return $result;
      }

      return [
        'fileList' => $result['data']['delete_list'],
        'requestId' => $result['requestId']
      ];
    } catch (Exception $e) {
      throw new TcbException($e->getErrorCode(), $e->getMessage());
    }
  }

  public function downloadFile($options = [])
  {
    $fileID = $options['fileID'];
    $tempFilePath = array_key_exists('tempFilePath', $options) ?  $options['tempFilePath'] : null;

    $tmpUrlRes = $this->getTempFileURL([
      "fileList" => [
        ["fileID" => $fileID, "maxAge" => 600]
      ]
    ]);

    if (count($tmpUrlRes['fileList']) == 0) {
      return [
        'code' => 'NO_FILE_EXISTS',
        'message' => '没有获取任何文件'
      ];
    }

    $res = $tmpUrlRes['fileList'][0];

    if ($res['code'] != 'SUCCESS') {
      return $res;
    }

    $tmpUrl = $res['tempFileURL'];
    $tmpUrl = self::encodeURI($tmpUrl);

    try {
      $file = file_get_contents($tmpUrl);

      if ($file !== false) {
        if (isset($tempFilePath)) {
          file_put_contents($tempFilePath, $file);
          return [
            // 'requestId' => $tmpUrlRes['requestId'],
            'fileContent' => null,
            'message' => '文件下载完成'
          ];
        } else {
          return [
            'fileContent' => $file,
            'message' => '文件下载完成'
            // 'requestId' => $tmpUrlRes['requestId']
          ];
        }
      } else {
        return [
          'message' => '文件下载失败',
          // 'requestId' => $tmpUrlRes['requestId']
        ];
      }
    } catch (Exception $e) {
      throw new TcbException($e->getErrorCode(), $e->getMessage());
    }
  }
}

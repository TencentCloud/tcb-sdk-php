<?php
namespace TencentCloudBase\Utils;

use TencentCloudBase\Utils\Auth;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use function GuzzleHttp\json_decode;
// use const TencentCloudBase\Consts\Code::EVENTID_NUM;
use TencentCloudBase\Consts\Code;

class TcbBase
{
  protected $config;

  public function __construct($config)
  {
    $this->config = $config;
  }

  /**
   * ??ms???
   */
  public static function getMilTimeseconds()
  {
    return (int)(microtime(true) * 1000);
  }

  /**
   * ??eventId
   */
  public static function getEventId()
  {

    $timeStamp = self::getMilTimeseconds();
    // ??5?eventId
    $eventIdRand = rand(pow(10, (Code::EVENTID_NUM - 1)), pow(10, Code::EVENTID_NUM) - 1);
    $eventId = $timeStamp . '_' . $eventIdRand;
    return $eventId;
  }
  /**
   * ??seqId
   */
  public static function getSeqId()
  {
    $seqId = "";
    // ???????TCB_SEQID todo
    if (array_key_exists('TCB_SEQID', getenv())) {
      $seqId = getenv('TCB_SEQID');
    }

    $timeStamp = self::getMilTimeseconds();

    $seqId = $seqId !== "" ? $seqId . $timeStamp : (self::getEventId());
    return $seqId;
  }

  /**
   * ??????????
   * 
   */
  public static function sortArrayKey(&$arr)
  {
    if (is_array($arr)) {
      ksort($arr);
      foreach ($arr as $key => $value) {
        self::sortArrayKey($arr[$key]);
      }
    }
  }

  /**
   * multipart data transfer
   */

  public static function multiPartDataTran($rawData)
  {
    $multipartData = array();
    foreach ($rawData as $key => $value) {
      array_push($multipartData, array('name' => $key, 'contents' => $value));
    }
    return $multipartData;
  }

  public function cloudApiRequest($args = [
    'action' => null,
    'params' => [],
    'method' => null,
    'headers' => [],
    'timeout' => null,
    'config' => null
  ])
  {
    //
    $config = empty($args['config']) ? $this->config : $args['config'];
    $protocol =  "https";
    $pathname = "/admin";
    $params = $args['params'];
    $defaultTimeout = 15;
    $composerJson = json_decode(file_get_contents(realpath(__DIR__ . '/../../composer.json')), true);
    $sdk_version = $composerJson['version'];
    $dataVersion = $composerJson['extra']['dataVersion'];


    $paramsAdd = array('timestamp' => self::getMilTimeseconds(), 'eventId' => self::getEventId());
    if (array_key_exists('envName', $config)) {
      $paramsAdd['envName'] = $config['envName'];
    }

    if (array_key_exists('isHttp', $config)) {
      $protocol = $config['isHttp'] ? 'http' : 'https';
    }
    //
    $method = $args['method'] ? $args['method'] : 'get';
    $params = array_merge($params, $paramsAdd);

    // ??params??null?key
    foreach ($params as $key => $value) {
      if ($value === null) {
        unset($params[$key]);
      }
    }

    // params.file ?wx.openApi??requestData ??????????
    $file = null;
    $requestData = null;
    if ($params['action'] === 'storage.uploadFile') { // ????unset??
      $file = $params['file'];
      unset($params['file']);
    }
    if ($params['action'] === 'wx.openApi') {
      $requestData = $params['requestData'];
      unset($params['requestData']);
    }

    // $params ?key????
    ksort($params);
    $authObj = array(
      'SecretId' => $config['secretId'],
      'SecretKey' => $config['secretKey'],
      'Method' => $method,
      'pathname' => $pathname,
      'Query' => $params,
      'Headers' => array_merge($args['headers'], array('user-agent' => 'tcb-php-sdk/' . $sdk_version))
    );

    $auth = new Auth($authObj);

    $authorization = $auth->getAuth();

    $params['authorization'] = $authorization;

    if ($file) {
      $params['file'] = $file;
    }
    if ($requestData) {
      $params['requestData'] = $requestData;
    }

    if (array_key_exists('sessionToken', $config) && $config['sessionToken']) {
      $params['sessionToken'] = $config['sessionToken'];
    }

    $params['sdk_version'] = $sdk_version; // todo 


    // ??url
    $url = $protocol . "://tcb-admin.tencentcloudapi.com" . $pathname;

    // $url = "http://localhost:8002/admin";
    // $url = "http://118.126.68.63/admin";


    if (getenv('TENCENTCLOUD_RUNENV') === "SCF") {
      $protocol = "http";
      $url = $protocol . "://tcb-admin.tencentyun.com" . $pathname;
    }

    if ($params['action'] === "wx.api" || $params['action'] === "wx.openApi") {
      $url = $protocol . "://tcb-open.tencentcloudapi.com" . $pathname;
    }

    $realUrl = isset($config['serviceUrl']) ? (!empty($config['serviceUrl']) ? $config['serviceUrl'] : $url) : $url;

    // $realUrl = $realUrl . '?eventId=' . $params['eventId'] . '&seqId=' . self::getSeqId();

    // database??? ?config.transMidData ?true
    $service = explode('.', $params['action'])[0];

    $params['dataVersion'] = $dataVersion;
    if ($service === 'database' && isset($config['databaseMidTran']) && $config['databaseMidTran'] === true) {
      $params['databaseMidTran'] = true;
    }

    $opts = array();

    // opts timeout
    $opts['timeout'] = $defaultTimeout;

    if (isset($config['timeout'])) {
      $opts['timeout'] = $config['timeout'] ? $config['timeout'] : $defaultTimeout;
    }

    if (isset($args['timeout'])) {
      $opts['timeout'] = $args['timeout'] ? $args['timeout'] : $opts['timeout'];
    }

    // opts query
    $optsQuery = [
      'eventId' => $params['eventId'],
      'seqId' => self::getSeqId()
    ];

    if ($params['action'] === 'storage.uploadFile') {
      // multipart ??????
      $opts = array('headers' => $authObj['Headers'], 'multipart' => self::multiPartDataTran($params), 'query' => $optsQuery, 'expect' => false);
    } else {
      if ($method === 'post') {
        $opts = array('headers' => $authObj['Headers'], 'json' => $params, 'query' => $optsQuery);
      } else { // get
        // todo
        $opts = array('headers' => $authObj['Headers'], 'query' => array_merge($optsQuery, $params));
      }
    }

    if (isset($config['proxy'])) {
      $opts['proxy'] = $config['proxy'];
    }

    // $opts['proxy'] = "http://web-proxy.tencent.com:8080";
    // opts ???key???????????queryParams????
    self::sortArrayKey($opts);

    $http_client = new HttpClient(["base_uri" => $realUrl]);
    try {
      // $resp = $http_client->post($pathname, $opts);
      $resp = $http_client->request($authObj['Method'], $realUrl, $opts);
      $resultBody = $resp->getBody();
      $resultCode = $resp->getStatusCode();
      if ($resultCode === 200) {
        $bodyStr = $resultBody->getContents();
        $bodyResult = json_decode($bodyStr, true);
        return $bodyResult;
      }
    } catch (RequestException $e) {
      echo $e->getRequest();
      echo $e->getMessage();
    }
  }
}

<?php
namespace TencentCloudBase\Functions;

// require_once 'src/consts/code.php';

use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Utils\TcbBase;
// use const TencentCloudBase\Consts\Code::FUNCTIONS_NAME_REQUIRED;
use TencentCloudBase\Consts\Code;

use \Exception;

class TcbFunctions extends TcbBase
{

  protected $config;

  function __construct($config)
  {
    parent::__construct($config);
  }

  public function callFunction($options)
  {

    if (!array_key_exists('name', $options)) {
      throw new TcbException(Code::FUNCTIONS_NAME_REQUIRED, '函数名不能为空');
    }

    // 环境变量中取wxCloudApiToken
    $wxCloudApiToken = getenv('WX_API_TOKEN') ? getenv('WX_API_TOKEN') : '';

    $name = $options['name'];
    $data = array_key_exists('data', $options) ? $options['data'] : array();

    $args = array();
    // $args['action'] = 'functions.invokeFunction';

    $args['params'] = array(
      'action' => 'functions.invokeFunction',
      'function_name' => $name,
      'request_data' => json_encode($data),
      'wxCloudApiToken' => $wxCloudApiToken
    );

    $args['method'] = 'post';
    $args['headers'] = array("content-type" => "application/json");

    try {
      $result = $this->cloudApiRequest($args);

      // 如果 code 和 message 存在，证明报错了
      if (array_key_exists('code', $result)) {
        throw new TcbException($result['code'], $result['message'], $result['RequestId']);
      }

      return [
        'requestId' => $result['requestId'],
        'result' => json_decode($result['data']['response_data']),
      ];
    } catch (Exception $e) {
      throw new TcbException($e->getErrorCode(), $e->getMessage());
    }
  }
}

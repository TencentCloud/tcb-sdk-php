<?php
namespace TencentCloudBase\Utils;

use TencentCloudBase\Utils\TcbBase;

/**
 * database 
 * 
 */
class Request extends TcbBase
{
  // private $config;
  protected $config;
  public function __construct($config)
  {
    parent::__construct($config);
  }

  /**
   * 发送请求
   *
   * @param api   - 接口
   * @param data  - 参数
   */
  public function send($api, $data)
  {
    $params = array_merge($data, array('action' => $api));
    $args = array();

    $args['params'] = $params;
    $args['method'] = 'post';
    $args['headers'] = array('content-type' => 'application/json');
    $result = $this->cloudApiRequest($args);
    // 

    return $result;
  }


  /**
   * 发送中间格式请求
   *
   * @param api   - 接口
   * @param data  - 参数
   */
  public function sendMidData($api, $data)
  {
    $params = array_merge($data, array('action' => $api));
    $args = array();

    $args['params'] = $params;
    $args['method'] = 'post';
    $args['headers'] = array('content-type' => 'application/json');
    $args['config'] = array_merge($this->config, array('databaseMidTran' => true));

    $result = $this->cloudApiRequest($args);
    // 
    return $result;
  }
}

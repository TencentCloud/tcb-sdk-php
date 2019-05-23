<?php
/**
 * 签名类
 */
namespace TencentCloudBase\Utils;

use TencentCloudBase\Utils\TcbException;


class Auth
{
  protected $authOpts;

  function __construct($authOpts)
  {
    $this->authOpts = $authOpts;
  }

  // 将array内key升序排序
  public static function sortArrayKey(&$arr)
  {
    if (is_array($arr)) {
      ksort($arr);
      foreach ($arr as $key => $value) {
        self::sortArrayKey($arr[$key]);
      }
    }
  }

  // 获取关联数组的key
  public static function getSortKey(&$arr)
  {
    self::sortArrayKey($arr);
    $list = array();
    foreach ($arr as $key => $value) {
      if ($value !== null) {
        array_push($list, $key);
      }
    }
    // sort($list);
    return $list;
  }

  // 编码URI字符串
  public static function encodeUriStr($str)
  {
    $revert = array('!' => '%21', '*' => '%2A', "'" => "%27", '(' => '%28', ')' => '%29');
    return strtr(rawurlencode($str), $revert);
  }

  // 关联数组转字符串 key1=value1&key2=value2...形式
  public static function obj2Str($obj)
  {
    $list = array();
    foreach ($obj as $key => $value) {
      $val = $value === null ? "" : $value;
      if (!is_string($val)) {
        $val = json_encode($val, JSON_UNESCAPED_UNICODE); // json_encode 会将/ 转为\/
        $val = str_replace("\\/", "/", $val);
      }
      $key = self::encodeUriStr(strtolower($key));
      $val = self::encodeUriStr($val);
      $val = $val ? $val : "";
      array_push($list, $key . '=' . $val);
    }
    return join("&", $list);
  }

  // 构造authorization
  public function getAuth()
  {
    $authOpts = $this->authOpts;
    $authOpts = $authOpts ? $authOpts : array();

    $SecretId = array_key_exists('SecretId', $authOpts) ? $authOpts['SecretId'] : null;
    $SecretKey = array_key_exists('SecretKey', $authOpts) ? $authOpts['SecretKey'] : null;
    $method = strtolower($authOpts['Method'] ? $authOpts['Method'] : 'get');
    $pathname = $authOpts['pathname'] ? $authOpts['pathname'] : '/';
    $queryParams = unserialize(serialize($authOpts['Query'] ? $authOpts['Query'] : array()));
    $headers = unserialize(serialize($authOpts['Headers'] ? $authOpts['Headers'] : array()));
    strpos($pathname, '/') !== 0 && ($pathname = $pathname . '/');
    if (empty($SecretId)) {
      throw new TcbException(INVALID_PARAM, 'miss param secretId');
    }
    if (empty($SecretKey)) {
      throw new TcbException(INVALID_PARAM, 'miss param secretKey');
    }
    // 签名有效起止时间
    $now = time() - 1;
    // $now = 1557372175;
    $exp = $now;
    $exp += 900;

    // authorization参数列表
    $qSignAlgorithm = "sha1";
    $qAk = $SecretId;
    $qSignTime = $now . ';' . $exp;
    $qKeyTime = $now . ';' . $exp;
    $qHeaderList = strtolower(join(";", self::getSortKey($headers)));
    $qUrlParamList = strtolower(join(";", self::getSortKey($queryParams)));

    // 步骤1，计算SignKey
    $signKey = hash_hmac('sha1', $qKeyTime, $SecretKey);

    //步骤2，构成FormatString
    $str1 = self::obj2Str($queryParams);
    $str2 = self::obj2Str($headers);

    $formatString = join("\n", array($method, $pathname, $str1, $str2, ""));

    // 步骤3，计算stringToSign 
    $sha1Str = sha1($formatString);
    $stringToSign = join("\n", array("sha1", $qSignTime, $sha1Str, ""));

    // 步骤4， 计算signature
    $qSignature = hash_hmac('sha1', $stringToSign, $signKey);

    // 步骤5，构造authorization
    $authorization = join("&", array(
      "q-sign-algorithm=" . $qSignAlgorithm,
      "q-ak=" . $qAk,
      "q-sign-time=" . $qSignTime,
      "q-key-time=" . $qKeyTime,
      "q-header-list=" . $qHeaderList,
      "q-url-param-list=" . $qUrlParamList,
      "q-signature=" . $qSignature
    ));

    return $authorization;
  }
}

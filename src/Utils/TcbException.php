<?php
namespace TencentCloudBase\Utils;

// use TencentCloud\Common\Exception\TencentCloudSDKException;

class TcbException extends \Exception
{
  private $requestId;

  private $errorCode;

  public function __construct($code = "", $message = "",  $requestId = "")
  {
    parent::__construct($message, 0);
    // parent::__construct($code, $message,  $requestId);
    $this->errorCode = $code;
    $this->requestId = $requestId;
  }

  /**
   * 返回请求id
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }

  /**
   * 返回错误码
   * @return string
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }

  /**
   * 格式化输出异常码，异常信息，请求id
   * @return string
   */
  public function __toString()
  {
    return "[" . __CLASS__ . "]" . " code:" . $this->errorCode .
      " message:" . $this->getMessage() .
      " requestId:" . $this->requestId;
  }
}

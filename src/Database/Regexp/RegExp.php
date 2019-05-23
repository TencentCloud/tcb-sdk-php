<?php
namespace TencentCloudBase\Database\Regexp;

use TencentCloudBase\Utils\TcbException;

class RegExp
{

  private $regex;
  private $options;

  function __construct($opts = ['regexp' => '', 'options' => ''])
  {
    if (empty($opts['regexp'])) {
      throw new TcbException(INVALID_PARAM, 'regexp must be a string');
    }
    $this->regex = isset($opts['regexp']) ? $opts['regexp'] : '';
    $this->options = isset($opts['options']) ? $opts['options'] : '';
  }

  public function parse()
  {
    return array('$regex' => $this->regex, '$options' => $this->options);
  }
}

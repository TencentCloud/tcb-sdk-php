<?php
namespace TencentCloudBase\Database\ServerDate;

class ServerDate
{

  public $offset;

  function __construct($options = ['offset' => 0])
  {
    $this->offset = isset($options['offset']) ? $options['offset'] : 0;
  }

  public function parse()
  {
    return array('$date' => array('offset' => $this->offset));
  }
}

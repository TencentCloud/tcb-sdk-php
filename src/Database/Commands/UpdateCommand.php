<?php
namespace TencentCloudBase\Database\Commands;


class UpdateCommand
{
  public $_actions = array();

  public function __construct($_actions, $step)
  {
    $this->_actions = array();
    if (gettype($_actions) === 'array' && count($_actions) > 0) {
      $this->_actions = $_actions;
    }
    if (gettype($step) === 'array' && count($step) > 0) {
      array_push($this->_actions, $step);
    }
  }
}

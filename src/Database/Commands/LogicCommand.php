<?php
namespace TencentCloudBase\Database\Commands;

class LogicCommand
{
  public $_actions = array();

  function __construct($_actions, $step)
  {
    $this->_actions = array();
    if (gettype($_actions) === 'array' && count($_actions) > 0) {
      $this->_actions = $_actions;
    }
    if (gettype($step) === 'array' && count($step) > 0) {
      array_push($this->_actions, $step);
    }
  }

  public function or()
  {
    $arguments = func_get_args();
    /**
     * or 操作符的参数可能是 逻辑操作对象/逻辑操作对象数组
     * _.or([_.gt(10), _.lt(100)])
     */
    if (gettype($arguments[0]) === 'array') {
      $arguments = $arguments[0];
    }
    array_unshift($arguments, '$or');
    return new LogicCommand($this->_actions, $arguments);
  }

  public function and()
  {
    $arguments = func_get_args();
    /**
     * or 操作符的参数可能是 逻辑操作对象/逻辑操作对象数组
     * _.or([_.gt(10), _.lt(100)])
     */
    if (gettype($arguments[0]) === 'array') {
      $arguments = $arguments[0];
    }
    array_unshift($arguments, '$and');
    return new LogicCommand($this->_actions, $arguments);
  }
}

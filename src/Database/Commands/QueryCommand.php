<?php
namespace TencentCloudBase\Database\Commands;

use TencentCloudBase\Database\Commands\LogicCommand;

class QueryCommand extends LogicCommand
{
  function __construct($_actions, $step)
  {
    parent::__construct($_actions, $step);
  }

  public function eq($val)
  {
    return $this->and(new QueryCommand(array(), array('$eq', $val)));
  }

  public function neq($val)
  {
    return $this->and(new QueryCommand(array(), array('$neq', $val)));
  }

  public function gt($val)
  {
    return $this->and(new QueryCommand(array(), array('$gt', $val)));
  }

  public function gte($val)
  {
    return $this->and(new QueryCommand(array(), array('$gte', $val)));
  }

  public function lt($val)
  {
    return $this->and(new QueryCommand(array(), array('$lt', $val)));
  }

  public function lte($val)
  {
    return $this->and(new QueryCommand(array(), array('$lte', $val)));
  }

  public function in($val)
  {
    return $this->and(new QueryCommand(array(), array('$in', $val)));
  }

  public function nin($val)
  {
    return $this->and(new QueryCommand(array(), array('$nin', $val)));
  }
}

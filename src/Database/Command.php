<?php
namespace TencentCloudBase\Database;

// require_once "src/database/constants.php";

use TencentCloudBase\Database\Geo\LineString;
use TencentCloudBase\Database\Geo\MultiLineString;
use TencentCloudBase\Database\Geo\MultiPoint;
use TencentCloudBase\Database\Geo\MultiPolygon;
use TencentCloudBase\Database\Geo\Point;
use TencentCloudBase\Database\Geo\Polygon;
use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Database\Utils\Format;
use TencentCloudBase\Database\Commands\LogicCommand;
use TencentCloudBase\Database\Commands\QueryCommand;
use TencentCloudBase\Database\Commands\UpdateCommand;
// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
use TencentCloudBase\Consts\Code;

use \TypeError;

class Command
{

  /**
   * Query and Projection Operators
   * https://docs.mongodb.com/manual/reference/operator/query/
   * @param target
   */

  public function eq($val)
  {
    $val = Format::dataFormat($val);
    return new QueryCommand([], ['$eq', $val]);
  }

  public function neq($val)
  {
    $val = Format::dataFormat($val);
    return new QueryCommand([], ['$neq', $val]);
  }

  public function lt($val)
  {
    $val = Format::dataFormat($val);
    return new QueryCommand([], ['$lt', $val]);
  }

  public function lte($val)
  {
    $val = Format::dataFormat($val);
    return new QueryCommand([], ['$lte', $val]);
  }

  public function gt($val)
  {
    $val = Format::dataFormat($val);
    return new QueryCommand([], ['$gt', $val]);
  }

  public function gte($val)
  {
    $val = Format::dataFormat($val);
    return new QueryCommand([], ['$gte', $val]);
  }

  public function in($arr)
  {
    $arr = Format::dataFormat($arr);
    return new QueryCommand([], ['$in', $arr]);
  }

  public function nin($arr)
  {
    $arr = Format::dataFormat($arr);
    return new QueryCommand([], ['$nin', $arr]);
  }

  public function geoNear($val)
  {
    $isObject = is_object($val);
    $isArray = is_array($val);

    if (!$isObject && !$isArray) { // 不是object 与 array类型， 直接报错
      throw new TcbException(
        Code::INVALID_PARAM,
        '"val" must be of type array or object. Received type ' . gettype($val)
      );
    }

    $geometry = $isObject ? $val->geometry : $val['geometry'];

    $resultGeometry = [
      'geometry' => $geometry->toJSON()
    ];

    $hasMaxDistance = $isObject ? isset($val->maxDistance) : isset($val['maxDistance']);
    $hasMinDistance = $isObject ? isset($val->minDistance) : isset($val['minDistance']);

    if ($hasMaxDistance) {
      $maxDistance = $isObject ? $val->maxDistance : $val['maxDistance'];
      $resultGeometry['maxDistance'] = $maxDistance;
    }

    if ($hasMinDistance) {
      $minDistance = $isObject ? $val->minDistance : $val['minDistance'];
      $resultGeometry['minDistance'] = $minDistance;
    }

    // $maxDistance = $isObject ? $val->maxDistance : $val['maxDistance'];
    // $minDistance = $isObject ? $val->minDistance : $val['minDistance'];

    if (!($geometry instanceof Point)) {
      throw new TcbException(
        Code::INVALID_PARAM,
        '"geometry" must be of type Point. Received type ' . gettype($geometry)
      );
    }
    if (($hasMaxDistance && !is_numeric($maxDistance))) {
      throw new TypeError(
        '"maxDistance" must be of type Number. Received type"' . gettype($maxDistance)
      );
    }
    if (($hasMinDistance && !is_numeric($minDistance))) {
      throw new TypeError(
        '"minDistance" must be of type Number. Received type' . gettype($minDistance)
      );
    }

    // $resultGeometry = [
    //   'geometry' => $geometry->toJSON(),
    //   'maxDistance' => $maxDistance,
    //   'minDistance' => $minDistance
    // ];

    return new QueryCommand([], ['$geoNear', $resultGeometry]);
  }

  public function geoWithin($val)
  {
    $isObject = is_object($val);
    $isArray = is_array($val);

    if (!$isObject && !$isArray) { // 不是object 与 array类型， 直接报错
      throw new TcbException(
        Code::INVALID_PARAM,
        '"val" must be of type array or object. Received type ' . gettype($val)
      );
    }
    $geometry = $isObject ? $val->geometry : $val['geometry'];

    if (
      !($geometry instanceof MultiPolygon) &&
      !($geometry instanceof Polygon)
    ) {
      throw new TypeError(
        '"geometry" must be of type Polygon or MultiPolygon. Received type' . gettype($geometry)
      );
    }

    $resultGeometry = [
      'geometry' => $geometry->toJSON(),
    ];
    return new QueryCommand([], ['$geoWithin', $resultGeometry]);
  }

  public function geoIntersects($val)
  {
    $isObject = is_object($val);
    $isArray = is_array($val);

    if (!$isObject && !$isArray) { // 不是object 与 array类型， 直接报错
      throw new TcbException(
        Code::INVALID_PARAM,
        '"val" must be of type array or object. Received type ' . gettype($val)
      );
    }
    $geometry = $isObject ? $val->geometry : $val['geometry'];

    if (
      !($geometry instanceof Point) &&
      !($geometry instanceof LineString) &&
      !($geometry instanceof Polygon) &&
      !($geometry instanceof MultiPoint) &&
      !($geometry instanceof MultiLineString) &&
      !($geometry instanceof MultiPolygon)
    ) {
      throw new TypeError(
        '"geometry" must be of type Point, LineString, Polygon, MultiPoint, MultiLineString or MultiPolygon. Received type ' . gettype($geometry)
      );
    }

    $resultGeometry = [
      'geometry' => $geometry->toJSON(),
    ];

    return new QueryCommand([], ['$geoIntersects', $resultGeometry]);
  }

  function  or()
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
    return new LogicCommand([], $arguments);
  }

  function  and()
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
    return new LogicCommand([], $arguments);
  }

  public function set($val)
  {
    $val = Format::dataFormat($val);
    return new UpdateCommand([], ['$set', $val]);
  }

  public function remove()
  {
    return new UpdateCommand([], ['$remove']);
  }

  public function inc($val)
  {
    $val = Format::dataFormat($val);
    return new UpdateCommand([], ['$inc', $val]);
  }

  public function mul($val)
  {
    $val = Format::dataFormat($val);
    return new UpdateCommand([], ['$mul', $val]);
  }

  public function push($val)
  {
    $val = Format::dataFormat($val);
    return new UpdateCommand([], ['$push', $val]);
  }

  public function pop()
  {
    return new UpdateCommand([], ['$pop']);
  }

  public function shift()
  {
    return new UpdateCommand([], ['$shift']);
  }

  public function unshift($val)
  {
    $val = Format::dataFormat($val);
    return new UpdateCommand([], ['$unshift', $val]);
  }
}

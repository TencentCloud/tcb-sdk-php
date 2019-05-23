<?php
namespace TencentCloudBase\Database\Geo;

// require_once "src/consts/code.php";


use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Database\Geo\Point;
// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
use TencentCloudBase\Consts\Code;



/**
 * 地理位置
 */
class LineString
{
  /**
   * 多个point
   * 
   */
  public $points = [];

  /**
   * 初始化
   *
   * @param [Integer] $longitude
   * @param [Integer] $latitude
   */
  function __construct(array $points)
  {
    if (gettype($points) !== 'array') {
      throw new TcbException(Code::INVALID_PARAM, 'points must be of type Point. Receive type' . gettype($points));
    }

    if (count($points) < 2) {
      throw new TcbException(Code::INVALID_PARAM, '"points" must contain 2 points at least');
    }

    foreach ($points as $point) {
      if (!($point instanceof Point)) {
        throw new TcbException(Code::INVALID_PARAM, 'point must be of type Point. Receive type' . gettype($points)); // 工具方法gettype
      }
    }

    $this->points = $points;
  }

  public function toJSON()
  {
    return array('type' => 'LineString', 'coordinates' => array_map(function ($item) {
      return $item->toJSON()['coordinates'];
    }, $this->points));
  }


  public static function validate($lineString)
  {
    if (!isset($lineString['type']) || !isset($lineString['coordinates'])) {
      return false;
    }

    if ($lineString['type'] !== 'LineString' || gettype($lineString['coordinates']) !== 'array') {
      return false;
    }

    foreach ($lineString['coordinates'] as $point) {
      if (!is_numeric($point[0]) || !is_numeric($point[1])) {
        return false;
      }
    }
    return true;
  }

  public static function isClosed(LineString $lineString)
  {
    $firstPoint = $lineString->points[0];
    $lastPoint = $lineString->points[count($lineString->points) - 1];

    if ($firstPoint == $lastPoint) {
      return true;
    }
    return false;
  }
}

<?php
namespace TencentCloudBase\Database\Geo;

// require_once "src/database/constants.php";
// require_once "src/consts/code.php";

// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
use TencentCloudBase\Consts\Code;
use TencentCloudBase\Database\Geo\Polygon;
use TencentCloudBase\Utils\TcbException;

/**
 * 地理位置
 */
class MultiPolygon
{
  /**
   * 多个point
   * 
   */
  public $polygons = [];

  /**
   * 初始化
   *
   * @param [Integer] $longitude
   * @param [Integer] $latitude
   */
  function __construct(array $polygons)
  {
    if (gettype($polygons) !== 'array') {
      throw new TcbException(Code::INVALID_PARAM, '"polygons" must be of type Polygon[]. Received type' . gettype($polygons));
    }

    if (count($polygons) === 0) {
      throw new TcbException(Code::INVALID_PARAM, 'MultiPolygon must contain 1 polygon at least');
    }

    foreach ($polygons as $polygon) {
      if (!($polygon instanceof Polygon)) {
        throw new TcbException(Code::INVALID_PARAM, '"polygon" must be of type Polygon[]. Received type' . gettype($polygon));
      }
    }

    $this->polygons = $polygons;
  }

  public function toJSON()
  {
    return array('type' => 'MultiPolygon', 'coordinates' => array_map(function ($item) {
      return array_map(function ($item) {
        return array_map(function ($item) {
          return array($item->longitude, $item->latitude);
        }, $item->points);
      }, $item->lines);
    }, $this->polygons));
  }


  public static function validate($multiPolygon)
  {
    if (!isset($multiPolygon['type']) || !isset($multiPolygon['coordinates'])) {
      return false;
    }
    if ($multiPolygon['type'] !== 'MultiPolygon' || gettype($multiPolygon['coordinates']) !== 'array') {
      return false;
    }

    foreach ($multiPolygon['coordinates'] as $polygon) {
      foreach ($polygon as $line) {
        foreach ($line as $point) {
          if (!is_numeric($point[0]) || !is_numeric($point[1])) {
            return false;
          }
        }
      }
    }
    return true;
  }
}

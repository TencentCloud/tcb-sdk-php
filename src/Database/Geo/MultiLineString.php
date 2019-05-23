<?php
namespace TencentCloudBase\Database\Geo;

// require_once "src/database/constants.php";
// require_once "src/consts/code.php";

// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
use TencentCloudBase\Consts\Code;
use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Database\Geo\LineString;


/**
 * 地理位置
 */
class MultiLineString
{
  /**
   * 多个point
   * 
   */
  public $lines = [];

  /**
   * 初始化
   *
   * @param [Integer] $longitude
   * @param [Integer] $latitude
   */
  function __construct(array $lines)
  {
    if (gettype($lines) !== 'array') {
      throw new TcbException(Code::INVALID_PARAM, '"lines" must be of type LineString[]. Received type' . gettype($lines));
    }

    if (count($lines) === 0) {
      throw new TcbException(Code::INVALID_PARAM, 'Polygon must contain 1 linestring at least');
    }

    foreach ($lines as $line) {
      if (!($line instanceof LineString)) {
        throw new TcbException(Code::INVALID_PARAM, '"lines" must be of type LineString[]. Received type' . gettype($line));
      }
    }

    $this->lines = $lines;
  }

  public function toJSON()
  {
    return array('type' => 'MultiLineString', 'coordinates' => array_map(function ($item) {
      return array_map(function ($item) {
        return array($item->longitude, $item->latitude);
      }, $item->points);
    }, $this->lines));
  }


  public static function validate($multiLineString)
  {
    if (!isset($multiLineString['type']) || !isset($multiLineString['coordinates'])) {
      return false;
    }

    if ($multiLineString['type'] !== 'MultiLineString' || gettype($multiLineString['coordinates']) !== 'array') {
      return false;
    }

    foreach ($multiLineString['coordinates'] as $line) {
      foreach ($line as $point) {
        if (!is_numeric($point[0]) || !is_numeric($point[1])) {
          return false;
        }
      }
    }
    return true;
  }
}

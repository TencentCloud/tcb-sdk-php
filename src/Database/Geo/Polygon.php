<?php
namespace TencentCloudBase\Database\Geo;

// require_once "src/database/constants.php";
// require_once "src/consts/code.php";

use TencentCloudBase\Database\Geo\LineString;
use TencentCloudBase\Utils\TcbException;
// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
use TencentCloudBase\Consts\Code;


/**
 * 地理位置
 */
class Polygon
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
      if (!LineString::isClosed($line)) {

        $readbleStr = join(" ", array_map(function ($item) {
          return $item->toReadableString();
        }, $line->points));

        throw new TcbException(Code::INVALID_PARAM, 'LineString ' . $readbleStr . ' is not a closed cycle');
      }
    }

    $this->lines = $lines;
  }

  public function toJSON()
  {
    return array('type' => 'Polygon', 'coordinates' => array_map(function ($item) {
      return array_map(function ($item) {
        return array($item->longitude, $item->latitude);
      }, $item->points);
    }, $this->lines));
  }


  public static function validate($polygon)
  {
    if (!isset($polygon['type']) || !isset($polygon['coordinates'])) {
      return false;
    }

    if ($polygon['type'] !== 'Polygon' || gettype($polygon['coordinates']) !== 'array') {
      return false;
    }

    foreach ($polygon['coordinates'] as $line) {
      if (!self::isCloseLineString(($line))) {
        return false;
      }

      foreach ($line as $point) {
        if (!is_numeric($point[0]) || !is_numeric($point[1])) {
          return false;
        }
      }
    }
    return true;
  }

  public static function isCloseLineString($lineString)
  {
    $firstPoint = $lineString[0];
    $lastPoint = $lineString[count($lineString) - 1];

    if ($firstPoint[0] !== $lastPoint[0] || $firstPoint[1] !== $lastPoint[1]) {
      return false;
    }
    return true;
  }
}

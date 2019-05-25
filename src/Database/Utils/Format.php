<?php
namespace TencentCloudBase\Database\Utils;

use TencentCloudBase\Database\Geo\LineString;
use TencentCloudBase\Database\Geo\MultiLineString;
use TencentCloudBase\Database\Geo\MultiPoint;
use TencentCloudBase\Database\Geo\MultiPolygon;
use TencentCloudBase\Database\Geo\Point;
use TencentCloudBase\Database\Geo\Polygon;
use \DateTime;
use TencentCloudBase\Database\ServerDate\ServerDate;
use TencentCloudBase\Database\Regexp\RegExp;

class Format
{

  public static function checkSpecialClass($data)
  {
    if (!is_object($data)) {
      return '';
    }
    if ($data instanceof Point || $data instanceof LineString || $data instanceof Polygon || $data instanceof MultiPoint || $data instanceof MultiLineString || $data instanceof MultiPolygon) {
      return 'Geo';
    }
    if ($data instanceof RegExp) {
      return 'regExp';
    }
    if ($data instanceof ServerDate) {
      return 'serverDate';
    }

    if ($data instanceof DateTime) {
      return 'DateTime';
    }

    return 'object';
  }

  public static function is_assoc($arr)
  {
    return array_keys($arr) !== range(0, count($arr) - 1);
  }

  public static function checkSpecial(&$data)
  {
    if (is_object($data)) {
      if (self::checkSpecialClass($data) === 'Geo') {
        return $data->toJSON();
      } else if (self::checkSpecialClass($data) === 'regExp') {
        return $data->parse();
      } else if (self::checkSpecialClass($data) === 'serverDate') {
        return $data->parse();
      } else if (self::checkSpecialClass($data) === 'DateTime') {
        // 获取datetime ms
        $timeMicroSeconds = $data->format('u');
        // $timeMillSeconds = floor($timeMicroSeconds / 1000);

        $timeSeconds = $data->getTimestamp();
        $realTime = floor(($timeSeconds * 1000000 + $timeMicroSeconds) / 1000);
        return  [
          '$date' => $realTime
        ];
      }
      return $data;
    } else if (is_array($data)) {
      foreach ($data as $key => $item) {
        $data[$key] = self::checkSpecial($data[$key]);
      }
      return $data;
    }
    return $data;
  }

  public static function dataFormat($data)
  {
    $data = self::checkSpecial($data);
    return $data;
  }
}

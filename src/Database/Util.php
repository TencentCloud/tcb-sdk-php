<?php
namespace TencentCloudBase\Database;

// use const TencentCloudBase\Database\Constants\FieldType;
use TencentCloudBase\Database\Constants;
use TencentCloudBase\Database\Geo\LineString;
use TencentCloudBase\Database\Geo\MultiLineString;
use TencentCloudBase\Database\Geo\MultiPoint;
use TencentCloudBase\Database\Geo\MultiPolygon;
use TencentCloudBase\Database\Geo\Point;
use TencentCloudBase\Database\Geo\Polygon;
use TencentCloudBase\Utils\TcbException;
use \DateTime;

class Util
{
  /**
   * 
   * 检查array是否为关联数组
   */
  private static function is_assoc($arr)
  {
    if (!is_array($arr)) {
      return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
  }

  /**
   *
   * 格式化字段
   *
   * 主要是递归数组和对象，把地理位置和日期时间转换为js对象。
   *
   * @param document
   * @internal
   */
  private static function formatField($document)
  {
    // $keys = array_keys($document);
    $protoField = array();
    $arrayFlag = self::is_assoc($document);

    foreach ($document as $key => $value) {
      $type = Util::whichType($value);
      $realVal = null;

      switch ($type) {
        case Constants::FieldType["GeoPoint"]:
          $realVal = new Point($value['coordinates'][0], $value['coordinates'][1]);
          break;
        case Constants::FieldType["GeoLineString"]:
          $realVal = new LineString(array_map(function ($item) {
            return new Point($item[0], $item[1]);
          }, $value['coordinates']));
          break;
        case Constants::FieldType["GeoPolygon"]:
          $realVal = new Polygon(array_map(function ($line) {
            return new LineString(array_map(function ($item) {
              return new Point($item[0], $item[1]);
            }, $line));
          }, $value['coordinates']));
          break;
        case Constants::FieldType["GeoMultiPoint"]:
          $realVal = new MultiPoint(array_map(function ($item) {
            return new Point($item[0], $item[1]);
          }, $value['coordinates']));
          break;
        case Constants::FieldType["GeoMultiLineString"]:
          $realVal = new MultiLineString(array_map(function ($line) {
            return new LineString(array_map(function ($item) {
              return new Point($item[0], $item[1]);
            }, $line));
          }, $value['coordinates']));
          break;
        case Constants::FieldType["GeoMultiPolygon"]:
          $realVal = new MultiPolygon(array_map(function ($polygon) {
            return new Polygon(array_map(function ($line) {
              return new LineString(array_map(function ($item) {
                return new Point($item[0], $item[1]); // [lng, lat]前后位置待确认
              }, $line));
            }, $polygon));
          }, $value['coordinates']));
          break;
        case Constants::FieldType["Timestamp"]:
          $realVal = $value['$timestamp'] * 1000; // getTimestamp是否能调
          break;
        case Constants::FieldType["Object"]:
        case Constants::FieldType["Array"]:
          $realVal = self::formatField($value);
          break;
        case Constants::FieldType["ServerDate"]:
          // 将ms时间戳分为s与ms部分
          $microSecondsTime = $value['$date'] * 1000;
          $timeSeconds = floor($value['$date'] / 1000);
          // $microSecondsPart = $microSecondsTime - $timeSeconds * 1000000;
          // 取us字符串后6位即可

          $microSecondsStr = strval($microSecondsTime);
          $microSecondsPart = substr($microSecondsStr, -6);
          // $realVal = $value['$date']; // 直接返回时间戳？
          $realVal = new DateTime(date('Y-m-d H:i:s.' . $microSecondsPart, $timeSeconds));
          break;
        default:
          $realVal = $value;
      }
      if ($arrayFlag) { // 判断document是索引数组还是关联数组
        $protoField[$key] = $realVal;
      } else {
        array_push($protoField, $realVal);
      }
    }

    return $protoField;
  }

  public static function formatResDocumentData($documents = [])
  {
    return array_map(function ($document) {
      return self::formatField($document);
    }, $documents);
  }

  /**
   * 查看数据类型
   *
   * @param [Array] $obj
   * @return [String]
   */
  public static function whichType($obj)
  {
    $type = gettype($obj);

    if (self::is_assoc($obj)) {
      if (isset($obj['$timestamp'])) {
        return Constants::FieldType['Timestamp'];
      } else if (isset($obj['$date'])) {
        return Constants::FieldType['ServerDate'];
      } else if (Point::validate($obj)) {
        return Constants::FieldType['GeoPoint'];
      } else if (LineString::validate($obj)) {
        return Constants::FieldType['GeoLineString'];
      } else if (Polygon::validate($obj)) {
        return Constants::FieldType['GeoPolygon'];
      } else if (MultiPoint::validate($obj)) {
        return Constants::FieldType['GeoMultiPoint'];
      } else if (MultiLineString::validate($obj)) {
        return Constants::FieldType['GeoMultiLineString'];
      } else if (MultiPolygon::validate($obj)) {
        return Constants::FieldType['GeoMultiPolygon'];
      }
    }
    // if ($obj instanceof Point) {
    // 	return Constants::FieldType['GeoPoint'];
    // } elseif ($obj instanceof DateTime) {
    // 	return Constants::FieldType['Timestamp'];
    // } elseif ($obj instanceof Command) {
    // 	return Constants::FieldType['Command'];
    // } elseif ($obj instanceof ServerDate) {
    // 	return Constants::FieldType['ServerDate'];
    // }

    if ($type === 'integer' || $type === 'double') {
      return Constants::FieldType["Number"];
    }

    return ucfirst($type);
    // return $type;
  }

  /**
   * 生成文档ID, 为创建新文档使用
   *
   * @return string
   */
  public static function generateDocId()
  {
    $chars = "ABCDEFabcdef0123456789";
    $autoId = "";
    for ($i = 0; $i < 24; $i++) {
      $index = rand(0, strlen($chars));
      $test = substr($chars, $index, 1);
      $autoId = $autoId . $test;
    }
    return $autoId;
  }
}

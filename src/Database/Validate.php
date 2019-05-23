<?php
namespace TencentCloudBase\Database;

// require_once "src/consts/code.php";
// require_once "src/database/constants.php";

// use const TencentCloudBase\Database\Constants\FieldType;
// use const TencentCloudBase\Database\Constants\ErrorCode;
// use const TencentCloudBase\Database\Constants\OrderDirectionList;
// use const TencentCloudBase\Database\Constants\WhereFilterOpList;
use TencentCloudBase\Database\Constants;

// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
// use const TencentCloudBase\Consts\Code::INVALID_RANGE;
// use const TencentCloudBase\Consts\Code::INVALID_TYPE;
// use const TencentCloudBase\Consts\Code::DirectionError;
// use const TencentCloudBase\Consts\Code::INVALID_FIELD_PATH;
// use const TencentCloudBase\Consts\Code::OpStrError;
// use const TencentCloudBase\Consts\Code::CollNameError;
// use const TencentCloudBase\Consts\Code::DocIDError;
use TencentCloudBase\Consts\Code;


use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Database\Util;


/**
 * 校验模块
 */
class Validate
{
  /**
   * 检测地址位置的点
   *
   * @param point   - 经纬度
   * @param degree  - 数值
   */

  /**
   * 检测地址位置的点 function
   *
   * @param [Integer] $point
   * @param [Integer] $degree
   * @return boolean
   */
  public static function isGeopoint($point, $degree)
  {
    if (Util::whichType($degree) !== Constants::FieldType["Number"]) {
      throw new TcbException(Code::INVALID_TYPE, "Geo Point must be number type");
    }

    // 位置的绝对值
    $degreeAbs = abs($degree);

    if ($point === "latitude" && $degreeAbs > 90) {
      throw new TcbException(Code::INVALID_RANGE, "latitude should be a number ranges from -90 to 90");
    } else if ($point === "longitude" && $degreeAbs > 180) {
      throw new TcbException(Code::INVALID_RANGE, "longitude should be a number ranges from -180 to 180");
    }

    return true;
  }


  public static function isNumber($num)
  {
    return is_int($num) || is_long($num) || is_double($num) || is_float($num);
  }


  public static function isInteger($param, $num)
  {
    if (!(is_int($num) || is_long($num))) {
      throw new TcbException(Code::INVALID_PARAM, $param . '' . Constants::ErrorCode['IntergerError']);
    }
    return true;
  }


  /**
   * 是否为合法的排序字符
   *
   * @param [String] $direction
   * @return boolean
   */
  public static function isFieldOrder($direction)
  {
    if (!in_array($direction, Constants::OrderDirectionList)) {
      throw new TcbException(Code::DirectionError, '排序字符不合法');
    }
    return true;
  }

  /**
   * 是否为合法的字段地址
   *
   * 只能是连续字段名+英文点号
   *
   * @param path
   */
  /**
   * 是否为合法的字段地址
   * 只能是连续字段名+英文点号

   * @param [String] $path
   * @return boolean
   */
  public static function isFieldPath($path)
  {
    if (!preg_match('/^[a-zA-Z0-9-_\.]/', $path)) {
      throw new TcbException(Code::INVALID_FIELD_PATH, '字段地址不合法');
    }
    return true;
  }

  /**
   * 是否为合法操作符
   *
   * @param [type] $op
   * @return boolean
   */
  public static function isOperator($op)
  {
    if (!in_array($op, Constants::WhereFilterOpList)) {
      throw new TcbException(Code::OpStrError, "操作符不合法");
    }
    return true;
  }

  /**
   * 集合名称是否正确
   *
   * 只能以数字字母开头
   * 可以包含字母数字、减号、下划线
   * 最大长度32位
   *
   * @param string $name
   * @return boolean
   */
  public static function isCollName(string $name)
  {
    if (!preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9-_]){1,32}$/', $name)) {
      throw new TcbException(Code::CollNameError, '集合名称不合法');
    }
    return true;
  }

  /**
   * DocID 格式是否正确
   *
   * @param string $docId
   * @return boolean
   */
  public static function isDocID(string $docId)
  {
    if (!preg_match('/^([a-fA-F0-9]){24}$/', $docId)) {
      throw new TcbException(Code::DocIDError, 'DocID 格式不合法');
    }
    return true;
  }
}

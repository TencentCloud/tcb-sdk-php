<?php
// namespace TencentCloudBase\Database\Constants;
namespace TencentCloudBase\Database;

class Constants
{
  /**
   * 常量模块
   *
   * @author heyli
   */

  /**
   * 错误码
   */
  const ErrorCode = [
    'DocIDError' => '文档ID不合法',
    'CollNameError' => '集合名称不合法',
    'OpStrError' => '操作符不合法',
    'DirectionError' => '排序字符不合法',
    'IntergerError' => 'must be integer'
  ];

  /**
   * 字段类型
   */
  const FieldType = [
    'String' => 'String',
    'Number' => 'Number',
    'Object' => 'Object',
    'Array' => 'Array',
    'Boolean' => 'Boolean',
    'Null' => 'Null',
    'GeoPoint' => 'GeoPoint',
    'GeoLineString' => 'GeoLineString',
    'GeoPolygon' => 'GeoPolygon',
    'GeoMultiPoint' => 'GeoMultiPoint',
    'GeoMultiLineString' => 'GeoMultiLineString',
    'GeoMultiPolygon' => 'GeoMultiPolygon',
    'Timestamp' => 'Date',
    'Command' => 'Command',
    'ServerDate' => 'ServerDate',
  ];


  /**
   * 排序方向列表
   */
  const OrderDirectionList = ['desc', 'asc'];

  /**
   * 操作符列表
   */
  const WhereFilterOpList = ['<', '<=', '==', '>=', '>'];

  /**
   * 操作符别名
   */
  const Opeartor = [
    'lt' => '<',
    'gt' => '>',
    'lte' => '<=',
    'gte' => '>=',
    'eq' => '=='
  ];

  /**
   * 操作符映射
   * SDK => MongoDB
   */
  const OperatorMap = [
    Opeartor['eq'] => '$eq',
    Opeartor['lt'] => '$lt',
    Opeartor['lte'] => '$lte',
    Opeartor['gt'] => '$gt',
    Opeartor['gte'] => '$gte'
  ];

  const UpdateOperatorList = [
    '$set',
    '$inc',
    '$mul',
    '$unset',
    '$push',
    '$pop',
    '$unshift',
    '$shift',
    '$currentDate',
    '$each',
    '$position'
  ];

  // 数据库相关常量
  const INTERNAL_TYPE = [
    'UNSET_FIELD_NAME' => 'UNSET_FIELD_NAME',
    'UPDATE_COMMAND' => 'UPDATE_COMMAND',
    'QUERY_COMMAND' => 'QUERY_COMMAND',
    'LOGIC_COMMAND' => 'LOGIC_COMMAND',
    'GEO_POINT' => 'GEO_POINT',
    'SERVER_DATE' => 'SERVER_DATE'
  ];

  // 查询运算符
  const QUERY_COMMANDS_LITERAL = [
    "EQ" => 'eq',
    "NEQ" => 'neq',
    "GT" => 'gt',
    "GTE" => 'gte',
    "LT" => 'lt',
    "LTE" => 'lte',
    "IN" => 'in',
    "NIN" => 'nin',
  ];

  // 逻辑运算符
  const LOGIC_COMMANDS_LITERAL = [
    "O_AND" => "and",
    "O_OR" => "or",
    "O_NOT" => "not",
    "O_NOR" => "nor"
  ];

  // 更新运算符
  const UPDATE_COMMANDS_LITERAL = [
    "SET" => "set",
    "REMOVE" => "remove",
    "INC" => "inc",
    "MUL" => "mul",
    "PUSH" => "push",
    "POP" => "pop",
    "SHIFT" => "shift",
    "UNSHIFT" => "unshift",
  ];

  const O_AND = "and";
  const O_OR = "or";
  const O_NOT = "not";
  const O_NOR = "nor";

  const EQ = 'eq';
  const NEQ = 'neq';
  const GT = 'gt';
  const GTE = 'gte';
  const LT = 'lt';
  const LTE = 'lte';
  const IN = 'in';
  const NIN = 'nin';

  const SET = "set";
  const REMOVE = "remove";
  const INC = "inc";
  const MUL = "mul";
  const PUSH = "push";
  const POP = "pop";
  const SHIFT = "shift";
  const UNSHIFT = "unshift";
}

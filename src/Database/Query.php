<?php
namespace TencentCloudBase\Database;

// require_once "src/consts/code.php";
// require_once "src/database/constants.php";
// use const TencentCloudBase\Consts\Code::INVALID_PARAM;
use TencentCloudBase\Consts\Code;
use TencentCloudBase\Database\Validate;
use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Database\Util;

use TencentCloudBase\Utils\Request;
use TencentCloudBase\Database\Utils\Format;



class Query
{

  /**
   * Db 的引用
   *
   */
  protected $_db;

  /**
   * 集合
   *
   * @var [CollectionReference]
   */
  protected $_coll;

  /**
   * 过滤条件
   *
   * @var [Array]
   */
  private $_fieldFilters;

  /**
   * 排序条件
   *
   * @var Array
   */
  private $_fieldOrders;

  /**
   * 查询条件
   *
   * @var Array
   */
  private $_queryOptions;

  /**
   * 
   * request
   */
  private $_request;
  /**
   * 初始化
   *
   * @param [Db] $db                      - 数据库的引用
   * @param [CollectionReference] $coll   - 集合名称
   * @param [Array] $fieldFilters         - 过滤条件
   * @param [Array] $fieldOrders          - 排序条件
   * @param [Array] $queryOptions         - 查询条件
   */
  public function __construct($db, $coll, $fieldFilters = [], $fieldOrders = [], $queryOptions = [])
  {
    $this->_db = $db;
    $this->_coll = $coll;
    $this->_fieldFilters = $fieldFilters;
    $this->_fieldOrders = isset($fieldOrders) ? $fieldOrders : [];
    $this->_queryOptions = isset($queryOptions) ? $queryOptions : [];
    $this->_request = new Request($this->_db->config);
  }

  /**
   * 发起请求获取文档列表
   * 默认获取集合下全部文档数据
   * 可以把通过 `orderBy`、`where`、`skip`、`limit`设置的数据添加请求参数上
   *
   * @return Array
   */
  public function get()
  {
    // $args = [];
    // $args["action"] = "database.queryDocument";
    $params = [
      "collectionName" => $this->_coll,
    ];

    $newOder = [];

    // 处理排序条件
    if ($this->_fieldOrders) {
      foreach ($this->_fieldOrders as $order) {
        array_push($newOder, $order);
      }
    }

    if (count($newOder) > 0) {
      $params["order"] = $newOder;
    }
    // 处理过滤条件
    if ($this->_fieldFilters) {
      $params["query"] = $this->_fieldFilters;
    }

    // 处理查询条件
    if (isset($this->_queryOptions["offset"])) {
      $params["offset"] = $this->_queryOptions["offset"];
    }

    if (isset($this->_queryOptions["limit"])) {
      $params["limit"] = $this->_queryOptions["limit"] < 100 ? $this->_queryOptions["limit"] : 100;
    } else {
      $params["limit"] = 100;
    }

    if (isset($this->_queryOptions["projection"])) {
      $params["projection"] = $this->_queryOptions["projection"];
    }

    // dataFormat 处理

    $res = $this->_request->sendMidData('database.queryDocument', $params);

    if (isset($res["code"])) {
      return $res;
    } else {
      $documents = Util::formatResDocumentData($res["data"]["list"]);
      $result = [
        "data" => $documents,
        "requestId" => $res["requestId"],
      ];

      if (isset($res["TotalCount"])) {
        $result["total"] = $res["TotalCount"];
      }
      if (isset($res["Limit"])) {
        $result["limit"] = $res["Limit"];
      }
      if (isset($res["Offset"])) {
        $result["offset"] = $res["Offset"];
      }
      return $result;
    }
  }

  /**
   * 获取总数
   *
   * @return Array
   */
  public function count()
  {
    // $args = [];
    // $args["action"] = "database.countDocument";
    $params = [
      "collectionName" => $this->_coll,
    ];

    // 处理过滤条件
    if ($this->_fieldFilters) {
      $params["query"] = $this->_fieldFilters;
    }

    $res = $this->_request->sendMidData('database.countDocument', $params);

    if (isset($res["code"])) {
      return $res;
    } else {
      $result = [
        "requestId" => $res["requestId"],
        "total" => $res["data"]["total"],
      ];
      return $result;
    }
  }

  /**
   * 查询条件
   *
   * @param [Array] $query
   * @return Query
   */
  public function where($query) // 类型约束
  {
    return new Query(
      $this->_db,
      $this->_coll,
      Format::dataFormat($query),
      $this->_fieldOrders,
      $this->_queryOptions
    );
  }

  /**
   * 设置排序方式
   *
   * @param [String] $fieldPath       字段路径
   * @param [String] $directionStr    排序方式
   * @return Query
   */
  public function orderBy($fieldPath, $directionStr)
  {
    Validate::isFieldPath($fieldPath);
    Validate::isFieldOrder($directionStr);

    $newOrder = [
      "field" => $fieldPath,
      "direction" => $directionStr,
    ];

    array_push($this->_fieldOrders, $newOrder);
    // $combinedOrders = array_merge($this->_fieldOrders, $newOrder);

    return new Query(
      $this->_db,
      $this->_coll,
      $this->_fieldFilters,
      $this->_fieldOrders,
      $this->_queryOptions
    );
  }

  /**
   * 设置查询条数
   *
   * @param [Integer] $limit
   * @return Query
   */
  public function limit($limit)
  {
    Validate::isInteger("limit", $limit);

    $option = $this->_queryOptions;
    $option["limit"] = $limit;

    return new Query(
      $this->_db,
      $this->_coll,
      $this->_fieldFilters,
      $this->_fieldOrders,
      $option
    );
  }

  /**
   * 设置偏移量
   *
   * @param [Integer] $offset
   * @return Query
   */
  public function skip($offset)
  {
    Validate::isInteger("offset", $offset);

    $option = $this->_queryOptions;
    $option["offset"] = $offset;

    return new Query(
      $this->_db,
      $this->_coll,
      $this->_fieldFilters,
      $this->_fieldOrders,
      $option
    );
  }

  /**
   * 发起请求批量更新文档
   *
   * @param [Array] $data
   * @return Array
   */
  public function update($data = [])
  {
    if (!isset($data) || !is_array($data)) {
      throw new TcbException(Code::INVALID_PARAM, "参数必需是非空对象");
    }

    if (isset($data["_id"])) {
      throw new TcbException(Code::INVALID_PARAM, "不能更新_id的值");
    }

    // $args = [];
    // $args["action"] = "database.updateDocument";
    $params = [
      "collectionName" => $this->_coll,
      "query" => $this->_fieldFilters,
      "multi" => true,
      "merge" => true,
      "upsert" => false,
      "data" => Format::dataFormat($data),
      "interfaceCallSource" => 'BATCH_UPDATE_DOC'
    ];

    $res = $this->_request->sendMidData('database.updateDocument', $params);

    if (isset($res["code"])) {
      return $res;
    } else {
      $result = [
        "updated" => $res["data"]["updated"],
        "upsertId" => $res["data"]["upserted_id"], // 这里需和node sdk字段保持一致
        "requestId" => $res["requestId"],
      ];

      return $result;
    }
  }

  /**
   * 指定要返回的字段
   *
   * @param [Array] $projection
   * @return Query
   */
  public function field(array $projection)
  {
    foreach ($projection as $k => $v) {
      if ($projection[$k]) {
        $projection[$k] = 1;
      } else {
        $projection[$k] = 0;
      }
    }

    $option = $this->_queryOptions;
    $option["projection"] = $projection;

    return new Query(
      $this->_db,
      $this->_coll,
      $this->_fieldFilters,
      $this->_fieldOrders,
      $option
    );
  }

  /**
   * 条件删除文档
   *
   * @return Array
   */
  public function remove()
  {

    if (count(array_keys($this->_queryOptions)) > 0) {
      echo '`offset`, `limit` and `projection` are not supported in remove() operation';
    }

    if (count($this->_fieldOrders)) {
      echo '`orderBy` is not supported in remove() operation';
    }
    // $args = [];
    // $args["action"] = "database.deleteDocument";
    $params = [
      "collectionName" => $this->_coll,
      "query" => $this->_fieldFilters,
      "multi" => true,
    ];

    $res = $this->_request->sendMidData("database.deleteDocument", $params);

    if (isset($res["code"])) {
      return $res;
    } else {
      $result = [
        "deleted" => $res["data"]["deleted"],
        "requestId" => $res["requestId"],
      ];

      return $result;
    }
  }
}

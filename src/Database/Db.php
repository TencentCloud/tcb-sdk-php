<?php
namespace TencentCloudBase\Database;

// require_once 'src/consts/code.php';

use TencentCloudBase\Database\Geo\LineString;
use TencentCloudBase\Database\Geo\MultiLineString;
use TencentCloudBase\Database\Geo\MultiPoint;
use TencentCloudBase\Database\Geo\MultiPolygon;
use TencentCloudBase\Database\Geo\Point;
use TencentCloudBase\Database\Geo\Polygon;
use TencentCloudBase\Utils\TcbException;
use TencentCloudBase\Database\CollectionReference;
use TencentCloudBase\Database\ServerDate\ServerDate;
use TencentCloudBase\Database\Regexp\RegExp;

use TencentCloudBase\Database\Command;
use TencentCloudBase\Utils\Request;
// use const TencentCloudBase\Consts\Code::EMPTY_PARAM;
use TencentCloudBase\Consts\Code;

class Db
{

  public $config;
  public $command;

  function __construct($config)
  {
    // parent::__construct($config);
    $this->config = $config;
    $this->command = new Command();
  }

  /**
   * 获取serverDate对象
   *
   */
  public function serverDate($options = ["offset" => 0])
  {
    $offset = $options["offset"];
    return new ServerDate(["offset" => $offset]);
  }

  /**
   * 获取RegExp对象
   *
   */
  public function RegExp($opts = ['regexp' => '', 'options' => ''])
  {
    return new RegExp($opts);
  }

  /**
   * 获取RegExp对象
   *
   */
  public function Point($longitude = null, $latitude = null)
  {
    return new Point($longitude, $latitude);
  }


  /**
   * 获取RegExp对象
   *
   */
  public function MultiPoint($points = [])
  {
    return new MultiPoint($points);
  }

  /**
   * 获取RegExp对象
   *
   */
  public function LineString($points = [])
  {
    return new LineString($points);
  }

  /**
   * 获取RegExp对象
   *
   */
  public function MultiLineString($lines = [])
  {
    return new MultiLineString($lines);
  }

  /**
   * 获取RegExp对象
   *
   */
  public function Polygon($lines = [])
  {
    return new Polygon($lines);
  }

  /**
   * 获取RegExp对象
   *
   */
  public function MultiPolygon($polygons = [])
  {
    return new MultiPolygon($polygons);
  }

  /**
   * 获取集合的引用
   *
   * @param collName - 集合名称
   */
  public function collection($collName = null)
  {
    if (!isset($collName)) {
      throw new TcbException(Code::EMPTY_PARAM, "Collection name is required");
    }

    return new CollectionReference($this, $collName);
  }

  /**
   * 创建集合
   *
   * @param [String] $collName
   * @return Array
   */
  public function createCollection($collName = null)
  {
    // if (!isset($collName)) {
    //     throw new TencentCloudSDKException(Code::EMPTY_PARAM, "Collection name is required");
    // }

    $request = new Request($this->config);

    $params = [
      "collectionName" => $collName
    ];

    $result = $request->send('database.addCollection', $params);

    return $result;
  }
}

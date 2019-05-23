<?php
namespace TencentCloudBase\Database;

use TencentCloudBase\Database\Query;
use TencentCloudBase\Database\DocumentReference;

/**
 * 集合模块，继承 Query 模块
 */
class CollectionReference extends Query
{

  /**
   * 初始化
   *
   * @param [TcbDatabase] $db
   * @param [String] $coll
   */
  public function __construct($db, $coll)
  {
    parent::__construct($db, $coll);
  }

  /**
   * 获取文档的引用
   *
   * @param [String] $docID
   * @return DocumentReference
   */
  public function doc($docID = null)
  {
    return new DocumentReference($this->_db, $this->_coll, $docID);
  }

  // TODO 补充 return
  /**
   * 添加一篇文档
   *
   * @param [Array] $data
   * @return void
   */
  public function add($data = [], $callback = null)
  {
    $docRef = $this->doc();
    return $docRef->create($data, $callback);
  }

  public function __get($name)
  {
    // 读取集合名字
    if ($name === 'name') {
      return $this->_coll;
    }
  }
}

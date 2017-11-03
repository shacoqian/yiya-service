<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/27
 * Time: 下午12:35
 */

namespace App\Util;

use Illuminate\Support\Facades\DB;

class ModelUtil
{
  public static function getTableStructure ($tableName) {
    return DB::select("show full columns from {$tableName}");
  }

  public static function getTableFields($tableName) {
    $tableStructure = self::getTableStructure($tableName);
    $filed = [];
    foreach($tableStructure as $row) {
      $filed[] = $row->Field;
    }

    return array_flip($filed);
  }

  /**
   * 生成插入数据库的数据
   * @param $tableName
   * @param $excludeField 排除的字段
   * @param $data
   */
  public static function generateData($tableName, $excludeField = [], $data) {
    $filed = self::getTableFields($tableName);
    $_data = [];

    foreach($excludeField as $f) {
      if (array_key_exists($f,$filed)) {
        unset($filed[$f]);
      }
    }

    $filed = array_flip($filed);
    foreach ($filed as $f) {
      if (isset($data[$f]))
        $_data[$f] = $data[$f];
    }

    return $_data;
  }
}
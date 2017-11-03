<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/26
 * Time: 下午12:42
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomerFinanceModel extends Model
{
  public static $tableName = 'customer_finance';

  public static function insert($data) {
    return DB::table(self::$tableName)->insert($data);
  }
}
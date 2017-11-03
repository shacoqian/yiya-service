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

class CustomerInfoModel extends Model
{
  public static $tableName = 'customer_info';
  protected $table = 'customer_info';

  public static function insert($data) {
    return DB::table(self::$tableName)->insert($data);
  }

  public static function getCustomerInfo($account_id) {
      return self::where(['account_id' => $account_id])->first();
  }
}
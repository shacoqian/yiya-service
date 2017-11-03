<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/22
 * Time: 下午5:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreModel extends Model {

    protected $table = 'store';

    protected $fillable = ['company_id', 'name', 'code', 'address',
        'region_ids', 'system_store_id', 'created_at', 'updated_at', 'disabled'];



    //判断仓库名是否被占用
    public static function is_name_repeat($name, $company_id) {
        return self::where(['name' => $name, 'company_id' => $company_id])->first();
    }

    //判断仓库编号是否被占用
    public static function is_code_repeat($code, $company_id) {
        return self::where(['code' => $code, 'company_id' => $company_id])->first();
    }

    //判断仓库名是否被占用
    public static function is_name_repeat_not_self($name, $company_id, $store_id) {
        return self::where(['name' => $name, 'company_id' => $company_id])
            ->where('store_id', '!=', $store_id)
            ->first();
    }

    //判断仓库编号是否被占用
    public static function is_code_repeat_not_self($code, $company_id, $store_id) {
        return self::where(['code' => $code, 'company_id' => $company_id])
            ->where('store_id', '!=', $store_id)
            ->first();
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/22
 * Time: 下午5:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReapAddressList extends Model {

    protected $table = 'customer_reap_address_list';

    protected $fillable = [
        'region_id',
        'reap_name',
        'reap_phone',
        'region_name',
        'reap_phone',
        'region_name',
        'address',
        'is_default',
        'disabled',
        'goods_id',
        'account_id'
    ];


    //根据region_id和account_id判断收货地址是否存在
    public static function is_address_exist($account_id, $region_id) {
        return self::where(['account_id' => $account_id, 'address_id' => $region_id])->first();
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/22
 * Time: 下午5:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderGoodsModel extends Model {

    protected $table = 'order_goods';


    //根据订单ID获取商品清单列表
    public static function goods($order_id) {
        return self::where(['order_id' => $order_id])->get();
    }

}
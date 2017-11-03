<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/30
 * Time: 上午12:04
 */

namespace App\Models;


use App\Util\DateUtil;
use App\Util\ModelUtil;
use App\Util\StringUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class OrderDeliverModel extends Model
{
    public $table = 'order_deliver';

    public static function getList($order_id) {
        return DB::table('order_deliver')
            ->select(
                'order_deliver.*',
                'express_info.express_company_name',
                'express_info.status',
                'express_info.logisticCode',
                'express_info.reason',
                'order_deliver_detail.send_deliver_time',
                'order_deliver_detail.deliver_quantity'
            )
            ->leftJoin('express_info', 'order_deliver.order_deliver_id', '=', 'express_info.order_deliver_id')
            ->leftJoin('order_deliver_detail', 'order_deliver_detail.order_deliver_id', '=', 'order_deliver.order_deliver_id')
            ->where(['order_deliver.order_id' => $order_id])
            ->get();
    }

    //获取出库商品
    public static function getDeliverGoods($deliver_no) {
        return DB::table('order_deliver_detail')
            ->select(
                'order_goods.*',
                'order_deliver_detail.order_deliver_id',
                'order_deliver_detail.deliver_quantity'
            )
            ->leftJoin('order_goods', function($join){
                $join->on('order_deliver_detail.order_id', '=', 'order_goods.order_id')
                    ->on('order_deliver_detail.goods_standard_id', '=', 'order_goods.goods_standard_id');
            })
            ->where(['order_deliver_detail.order_deliver_no' => $deliver_no])
            ->get();
    }
}
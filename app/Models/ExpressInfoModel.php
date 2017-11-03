<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/22
 * Time: 下午5:08
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDeliverModel;
use App\Models\OrderModel;

class ExpressInfoModel extends Model {

    protected $table = 'express_info';


    //回调修改物流信息
    public static function updateExpress($data) {
        return self::where(['shipperCode' => $data['shipperCode'], 'logisticCode' => $data['logisticCode']])
            ->update($data);
    }


    //根据物流信息 更改发货及订单状态
    public static function changeDeliver($order_deliver_id) {
        //获取当前发货信息
        $deliverInfo = OrderDeliverModel::where(['order_deliver_id' => $order_deliver_id])->first();

        OrderDeliverModel::where(['order_deliver_id' => $order_deliver_id])
            ->update(['deliver_status' => 3]);

        //获取其他的发货信息
        $order_delivers = OrderDeliverModel::where(['order_id' => $deliverInfo->order_id]);
        $order_status = 0;
        foreach($order_delivers as $v) {
            if ($v->deliver_status != 3) {
                $order_status = 1;
            }
        }
        //修改订单
        if ($order_status === 0) {
            OrderModel::where(['order_id' => $deliverInfo->order_id])->update(['order_status' => 5]);
        }
    }

}
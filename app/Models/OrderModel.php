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

class OrderModel extends Model {

    protected $table = 'order';

    // 订单审核状态
    const ORDER_AUDIT_STATUS_1 = '待订单审核';
    const ORDER_AUDIT_STATUS_2 = '待财务审核';
    const ORDER_AUDIT_STATUS_3 = '待出库审核';
    const ORDER_AUDIT_STATUS_4 = '已完成';
    const ORDER_AUDIT_STATUS_5 = '已作废';


    public static $status = [
        1 => [1, 2, 3, 4],
        2 => [5],
        3 => [6],
    ];

    const DELIVER_RECORD = [
        0 => '备货中',
        1 => '部分出库',
        2 => '部分发货',
        4 => '已出库',
        5 => '待发货',
        110 => '已出库'

    ];

    //创建订单
    public static function createOrder($orders) {
        return DB::transaction(function () use ($orders) {
            $order_ids = [];
            foreach($orders as $order) {
                $orderGoods = $order['order_goods'];
                unset($order['order_goods']);
                $order_id = DB::table('order')->insertGetId($order);
                foreach($orderGoods as $v) {
                    $v['order_id'] = $order_id;
                    DB::table('order_goods')->insertGetId($v);
                }
                $order_ids[] = $order_id;
            }

            return $order_ids;
        });
    }


    //获取订单列表
    public static function orderList($account_id, $status, $page, $size) {
        $query = DB::table('order')
            ->leftJoin('customer_reap_address_list', 'order.address_id', '=', 'customer_reap_address_list.address_id')
            ->where('order.account_id', '=', $account_id)
            ->whereIn('order.order_status', self::$status[$status])
            ->where('order.disabled', '=', 0);
        $count = $query->count();
        $data = $query ->orderBy('order.order_id', 'desc')
            ->skip(($page -1) * $size)
            ->take($size)
            ->get();
        return [$count, $data];
    }

    //获取订单详情
    public static function orderDetail($order_id) {
        return DB::table('order')
            ->select(
                'order.*',
                'customer_reap_address_list.reap_name',
                'customer_reap_address_list.reap_phone',
                'customer_reap_address_list.region_name',
                'customer_reap_address_list.address',
                'customer_reap_address_list.region_id',
                'customer.customer_name'
            )
            ->leftJoin('customer_reap_address_list', 'order.address_id', '=', 'customer_reap_address_list.address_id')
            ->leftJoin('customer', 'order.account_id', '=', 'customer.account_id')
            ->where('order.order_id', '=', $order_id)
            ->where('order.disabled', '=', 0)
            ->first();
    }

    //获取出库发货状态
    public static function getDeliverStatus($deliver_detail_status) {
        $status = explode(',', $deliver_detail_status);
        $statusInfo = [];
        foreach($status as $v) {
            if (isset(self::DELIVER_RECORD[$v]))
                $statusInfo[] = self::DELIVER_RECORD[$v];
        }
        return implode('/', $statusInfo);
    }
}
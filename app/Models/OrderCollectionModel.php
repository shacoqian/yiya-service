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
use App\Models\OrderPayInfoViewModel;

class OrderCollectionModel extends Model {

    protected $table = 'order_collection_cash';

    //创建打款记录
    //修改订单状态

    public static function createOrderPay($data) {
        DB::beginTransaction();
        try{
            $id = DB::table('order_collection_cash')->insertGetId($data);
            if ($id) {
                //修改订单状态
                OrderPayInfoViewModel::changePayStatus($data['order_id']);
                DB::commit();
                return true;
            } else {
                DB::rollback();
                return false;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    //获取打款记录列表
    public static function payList($order_id) {
        return DB::table('order_collection_cash')
            ->select(
                'finance_bank_account.bank_account_name',
                'finance_bank_account.bank_name',
                'finance_bank_account.bank_number',
                'order_collection_cash.*',
                'order.amount as order_amount',
                'order.freight',
                'order.order_no',
                'order.order_status'
            )
            ->leftJoin('finance_bank_account', 'order_collection_cash.bank_account_id', '=', 'finance_bank_account.bank_account_id')
            ->leftjoin('order', 'order_collection_cash.order_id', '=', 'order.order_id')
            ->where(['order_collection_cash.order_id' => $order_id])

            ->get();
    }

    //获取订单的已支付金额
//    public static function getPaidAmount($order_id) {
//        $cashes = self::where(['order_id' => $order_id])
//            ->whereIn('collection_status', [1, 2])->get();
//
//        $paid_amount = 0;
//        foreach($cashes as $v) {
//            $paid_amount += $v->amount;
//        }
//        return $paid_amount;
//    }
}
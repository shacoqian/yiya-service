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
use App\Models\OrderModel;

class OrderPayInfoViewModel extends Model {

    protected $table = 'order_pay_info_view';


    public static function changePayStatus($order_id) {
        $info = self::where(['order_id' => $order_id])->first();
        //存在的情况下才修改 不存在证明没支付 没支付就不需要变更数据
        $status = 1;
        if ($info) {
            $amount = $info->amount + $info->freight;
            if ($amount > $info->paid_amount) {
                $status = 3;
            } else {
                $status = 2;
            }

            if ($status == $info->pay_status) {
                $status = 0;
            }
        }
        if ($status) {
            OrderModel::where(['order_id' => $order_id])->update([
                'pay_status' => $status
            ]);
        }
    }

}
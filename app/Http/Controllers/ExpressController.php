<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/9/21
 * Time: 上午10:12
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

use App\Extensions\Express\Express;

use App\Models\ExpressInfoModel;

/**
 * 公用接口
 *
 * @tags 公用接口
 */

class ExpressController extends Controller {

    public function callback() {
        if (! empty($_POST)) {
            $data = Express::resolveData($_POST);
            if (! $data) {
                echo json_encode(Express::getReturn());
                exit;
            }

            foreach($data as $v) {
                $express = ExpressInfoModel::where(['logisticCode' => $v['logisticCode']])->first();
                if ($express) {
                    if (! ExpressInfoModel::updateExpress($v)) {
                        Log::info('快递回调写入错误！', $v);
                    }

                    if ($v['status'] == 3) {
                        ExpressInfoModel::changeDeliver($express->order_deliver_id);
                    }

                } else {
                    echo json_encode(Express::getReturn(false));
                    exit;
                }
            }

            echo json_encode(Express::getReturn(true));
            exit;

        }
    }

    /**
     * @method get
     * @desc 快递订阅
     * @param string $token token header
     * @param string  $shipperCode 物流公司代码
     * @param string $logisticCode 物流单号
     * @path /api/express/subscribe
     */
    public function subscribe(Request $request) {
        $shipperCode = $request->input('shipperCode', '');
        $logisticCode = $request->input('logisticCode', '');
        if (! $shipperCode) {
            return $this->fail([], '物流公司代码不能为空！');
        }
        if (! $logisticCode) {
            return $this->fail([], '物流单号不能为空！');
        }

        ExpressInfoModel::where(['shipperCode' => $shipperCode, 'logisticCode' => $logisticCode])
            ->update(['success' => 1]);
        return $this->success(Express::subscribe($shipperCode, $logisticCode));
    }

    public function instant(Request $request) {
        $shipperCode = $request->input('shipperCode', '');
        $logisticCode = $request->input('logisticCode', '');
        return $this->success(Express::instant($shipperCode, $logisticCode));
    }




}
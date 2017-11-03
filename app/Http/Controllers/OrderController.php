<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/9/4
 * Time: 上午11:39
 */

namespace App\Http\Controllers;

use App\Models\OrderGoodsModel;
use Illuminate\Http\Request;

use App\Models\CustomerReapAddressList;
use App\Models\GoodsStandardModel;
use App\Models\OrderModel;
use App\Models\FinanceBankAccount;
use App\Models\OrderCollectionModel;
use App\Models\CartModel;
use App\Models\OrderRecordModel;
use App\Models\OrderDeliverModel;
use App\Models\ExpressInfoModel;
use App\Models\GoodsStoreSettingModel;
use App\Models\OrderPayInfoViewModel;

use App\Util\StringUtil;
use App\Util\DateUtil;
use App\Util\UserUtil;


/**
 * 订单相关
 * @tags 订单相关
 *
 */

class OrderController extends Controller {

    /**
     * @method get
     * @desc 分页获取订单列表
     * @param string $token token header
     * @param integer $page 页数
     * @param integer $size 每页数量
     * @param integer $status 1进行中2已完成3已作废
     *
     * @path /api/order/lists
     *
     * @return {"status":1,"result":{"data":[{"order_id":9,"order_no":"CY-20170904081018091474923","account_id":null,"amount":0,"freight":null,"bulk":null,"pay_status":"\u5f85\u4ed8\u6b3e","order_status":"\u5f85\u5ba1\u6838","create_time":"2017-09-04 08:10:18","disabled":null,"number":2,"address_id":0,"id":null,"region_id":null,"reap_name":null,"reap_phone":null,"region_name":null,"address":null,"is_default":null,"created_at":null,"updated_at":null},{"order_id":9,"order_no":"CY-20170904081018091474923","account_id":null,"amount":0,"freight":null,"bulk":null,"pay_status":"\u5f85\u4ed8\u6b3e","order_status":"\u5f85\u5ba1\u6838","create_time":"2017-09-04 08:10:18","disabled":null,"number":2,"address_id":0,"id":null,"region_id":null,"reap_name":null,"reap_phone":null,"region_name":null,"address":null,"is_default":null,"created_at":null,"updated_at":null}],"page":1,"size":10,"count":8},"message":"\u83b7\u53d6\u6570\u636e\u6210\u529f\uff01"}
     */
    public function lists(Request $request) {

        list($page, $size) = $this->paging($request->input());
        $status = $request->input('status', 1);
        $account_id = UserUtil::getAccountId();

        list($count, $data) = OrderModel::orderList($account_id, $status, $page, $size);
        return $this->success([
            'data' => $data,
            'page' => $page,
            'size' => $size,
            'count' => $count,
            'data' => $data
        ], '获取数据成功！');

    }

    /**
     * @method post
     * @desc 提交订单
     * @param string $token token header
     * @param string $order_remarks 订单备注
     *
     * @path /api/order/create
     *
     * @return {"status":1,"result":{"order_id":"CY-201709040725190917615664"},"message":"\u521b\u5efa\u8ba2\u5355\u6210\u529f\uff01"}
     */
    public function create(Request $request) {
        $account_id = UserUtil::getAccountId();
        $cartInfo = CartModel::getCartInfo($account_id);
        if (! $cartInfo) {
            return $this->fail([], '购物车信息不存在！');
        }

        //购物车的信息
        $cartInfo = json_decode($cartInfo, true);


        //收货地址的信息
        $address = CustomerReapAddressList::where([
            'account_id' => $account_id,
            'disabled' => 0,
            'is_default' => 1
        ])->first();

        if (! $address) {
            return $this->fail([], '收货地址不存在，请先设置一个默认地址！');
        }

        $goodsStandardIds = [];
        foreach($cartInfo as $k =>  $v) {
            $goodsStandardIds[] = $k;
        }


        $provinceId = explode(',', $address->region_id)[0];
        $goodsStandardInfo = GoodsStandardModel::getGoodsStandardList($goodsStandardIds)->toArray();
        //获取goods_id
        $goods_ids = [];
        foreach($goodsStandardInfo as $v) {
            $goods_ids[] = $v->goods_id;
        }

        $storeInfo = GoodsStoreSettingModel::getStoresInfo($goods_ids, $goodsStandardIds, $provinceId);
        $orders = []; //几个订单
        $orderNo = [];//订单号

        foreach( $storeInfo as $store_id =>  $standard_ids) {
            $orderGoods = [];
            $order_no = StringUtil::generateOrderNo();
            $goods_number = 0;
            foreach($standard_ids as $standard_id) {
                $goods_number += $cartInfo[$standard_id];
            }


            $goodsStandardInfo = GoodsStandardModel::getGoodsStandardList($standard_ids);
            $amount = 0;
            foreach($goodsStandardInfo as $v) {
                if (! in_array($v->goods_standard_id, $standard_ids)) {
                    return $this->fail([], '商品不存在，规格id' . $v->goods_standard_id);
                }
                $orderGoods[] = [
                    'goods_id' => $v->goods_id,
                    'goods_standard_id' => $v->goods_standard_id,
                    'main_image' => $v->main_image,
                    'goods_code' => $v->goods_code,
                    'goods_name' => $v->goods_name,
                    'standard_content' => $v->standard_content,
                    'buy_quantity' => $cartInfo[$v->goods_standard_id],
                    'remain_deliver_quantity' => $cartInfo[$v->goods_standard_id],
                    'unit_name' => $v->unit_name,
                    'price' => $v->price,
                    'created_at' => DateUtil::now(),
                    'first_level_benefit' => $v->first_level_benefit,
                    'second_level_benefit' => $v->second_level_benefit,
                ];
                $amount += $v->price * $cartInfo[$v->goods_standard_id];
            }

            $orders[] = [
                'order_no' => $order_no,
                'account_id' => $account_id,
                'store_id' => $store_id,
                'create_time' => date('Y-m-d H:i:s', time()),
                'goods_type_number' => count($standard_ids),
                'goods_number' => $goods_number,
                'company_id' => UserUtil::getCompanyId(),
                'pay_status' => 1, //待支付
                'address_id' => $address->address_id,
                'order_remarks' => $request->input('order_remarks', ''),
                'amount' => $amount,
                'order_goods' => $orderGoods
            ];

            $orderNo[] = $order_no;

        }



        //插入订单表的数据


        if ($ids = OrderModel::createOrder($orders)) {
            //清空购物车
            CartModel::clearCart($account_id);
            //记录日志
            foreach($ids as $id) {
                OrderRecordModel::create(
                    $id,
                    1
                );
            }

            return $this->success(['order_id' => implode(',', $orderNo)], '创建订单成功！');
        } else {
            return $this->fail([], '创建订单失败！');
        }
    }

    /**
     * @method get
     * @desc 订单详情
     * @param string $token token header
     * @param integer $order_id 订单id path
     *
     * @path /api/order/{order_id}/detail
     *
     * @return {"status":1,"result":{"order_id":9,"order_no":"CY-20170904081018091474923","account_id":25,"amount":0,"freight":null,"bulk":null,"pay_status":"\u5f85\u4ed8\u6b3e","order_status":"\u5f85\u5ba1\u6838","create_time":"2017-09-04 08:10:18","disabled":0,"number":2,"address_id":11,"id":11,"region_id":"4,5,6","reap_name":"\u7528\u6237\u540d","reap_phone":"\u624b\u673a\u53f7","region_name":"\u6d59\u6c5f\u7701,\u676d\u5dde\u5e02,\u6c5f\u5e72\u533a","address":"\u680b\u6881\u8def87-2\u53f7","is_default":1,"created_at":"2017-08-29 06:37:34","updated_at":2017},"message":"\u83b7\u53d6\u6570\u636e\u6210\u529f\uff01"}
     */
    public function detail(Request $request, $order_id) {
        $account_id = UserUtil::getAccountId();
        $res = OrderModel::orderDetail($order_id);
        if ($res) {
            $res = get_object_vars($res);
            if ($res['account_id'] != $account_id) {
                return $this->fail([], '订单为空！');
            }
            $res['deliver_detail_status'] = OrderModel::getDeliverStatus($res['deliver_detail_status']);

            $orderPayInfo = OrderPayInfoViewModel::where(['order_id' => $order_id])->first();
            if ($orderPayInfo) {
                $res['paid_amount'] = $orderPayInfo->paid_amount;
            } else {
                $res['paid_amount'] = 0;
            }
            $res['unpaid'] = $res['amount'] + $res['freight'] - $res['paid_amount'];
            return $this->success($res, '获取数据成功！');
        } else {
            return $this->fail([],'订单不存在！');
        }
    }

    /**
     * @method get
     *
     * @desc 获取商品清单
     * @param string $token token header
     * @param integer $order_id 订单id path
     *
     * @path /api/order/{order_id}/goods
     *
     */
    public function goods(Request $request, $order_id) {
        $res = OrderGoodsModel::goods($order_id);
        $info = [];
        foreach($res as $k => $v) {
            $images = json_decode($v->main_image, true);

            $standard_content = json_decode($v->standard_content, true);
            $standardInfo = [];
            if ($standard_content) {
                $standard = [];
                foreach($standard_content as $val) {
                    $standard[] = $val;
                }
                $standardInfo = $standard;
            }

            $info[$k]['goods_id'] = $v->goods_id;
            $info[$k]['goods_standard_id'] = $v->goods_standard_id;
            $info[$k]['main_image'] = $images ? $images['url'] : '';
            $info[$k]['number'] = $v->buy_quantity;
            $info[$k]['standard_content'] = implode(',', $standardInfo);
            $info[$k]['goods_name'] = $v->goods_name;
            $info[$k]['price'] = $v->price;
            $info[$k]['goods_unit_name'] = $v->unit_name;
        }
        return $this->success($info, '获取数据成功！');
    }

    /**
     * @method post
     * @desc  订单支付
     * @param string $token token header
     * @param integer $order_id 订单id path
     * @param integer $pay_type 支付类型1线下支付
     * @param string $cash_order_image 打款单路径
     * @param string $pay_amount 支付金额
     * @param integer $bank_account_id 收款银行账户ID
     *
     * @path /api/order/{order_id}/pay
     */
    public function orderPay(Request $request, $order_id) {

        $amount = intval($request->input('pay_amount', 0));
        if ($amount == 0) {
            return $this->fail(null, '支付金额不能为空！');
        }

        $company_id = UserUtil::getCompanyId();

        $params = $request->only( 'bank_account_id', 'pay_type', 'cash_order_image', 'pay_amount');
        if ($params['pay_type'] != 1) {
            return $this->fail([], '支付方式错误！');
        }

        $order = OrderModel::where(['order_id' => $order_id, 'disabled' => 0])->first();
        if (! $order) {
            return $this->fail([], '要支付的订单不存在！');
        }

        if ($order->pay_status == 2) {
            return $this->fail([], '订单已经支付过了，不能重复支付！');
        }

        $bankInfo = FinanceBankAccount::where(
            [
                'company_id' => $company_id,
                'bank_account_id' => $params['bank_account_id']
            ]
        )->first();
        
        if ($bankInfo == null) {
            return $this->fail([], '打款账户不存在！');
        }
        $images = explode('/', $params['cash_order_image']);
        $cash_order_image = json_encode([
            'path' => $params['cash_order_image'],
            'name' => end($images)
        ]);


        //创建打款记录
        $data = [
            'order_id' => $order_id,
            'transaction_no' => StringUtil::generatePayNo(),
            'account_id' => UserUtil::getAccountId(),
            'company_id' => UserUtil::getCompanyId(),
            'amount' => $amount,
            'bank_account_id' => $bankInfo->bank_account_id,
            'pay_type' => $params['pay_type'],
            'created_time' => date('Y-m-d H:i:s'),
            'cash_date' => date('Y-m-d'),
            'cash_order_image' => $cash_order_image
        ];

        if (OrderCollectionModel::createOrderPay($data)) {
            //记录日志
            OrderRecordModel::create(
                $order_id,
                2
            );
            return $this->success([], '订单支付成功！');
        } else {
            return $this->fail([], '订单支付失败！');
        }
    }

    /**
     * @method get
     * @desc 作废订单
     * @param string $token token header
     * @param integer $order_id 订单ID
     *
     * @path /api/order/{order_id}/cancel
     */
    public function cancel(Request $request, $order_id) {
        $order = OrderModel::where(['order_id' => $order_id, 'account_id' => UserUtil::getAccountId()])->first();
        if (! $order) {
            return $this->fail([], '订单不存在！');
        }

        if ($order->pay_status != 1) {
            return $this->fail([], '订单已经支付，不能作废！');
        }

        if (
            OrderModel::where(['order_id' => $order_id, 'account_id' => UserUtil::getAccountId()])
            ->update(['order_status' => 6])
        ) {
            //记录日志
            OrderRecordModel::create(
                $order_id,
                9
            );
            return $this->success([], '作废订单成功！');
        } else {
            return $this->fail([], '作废订单失败！');
        }
    }

    /**
     * @method get
     * @desc 获取订单操作记录
     * @param string $token token header
     * @param integer $order_id 订单id path
     *
     * @path /api/order/{order_id}/records
     */
    public function records(Request $request , $order_id) {
        $record = OrderRecordModel::getList(UserUtil::getCompanyId(), $order_id);
        return $this->success($record);
    }


    /**
     * @method get
     * @desc 获取打款信息列表
     * @param string $token token header
     * @param integer $order_id 订单id path
     * @path /api/order/{order_id}/payLists
     */
    public function payLists(Request $request, $order_id) {
        $payList = OrderCollectionModel::payList($order_id);
        $newData = [
            'payable' => 0, //应付金额
            'paid' => 0,
        ];
        foreach($payList as $v) {
            if ($newData['payable'] === 0) {
                $newData['payable'] = $v->order_amount + $v->freight;
            }

            if (! isset($newData['order_id'])) {
                $newData['order_id'] = $v->order_id;
            }

            $newData['paid'] += $v->amount;
            $newData['payList'][] = $v;
        }
        $newData['unpaid'] = $newData['payable'] - $newData['paid'];
        return $this->success($newData);
    }

    /**
     * @method get
     * @desc 出库/发货记录
     * @param string $token token header
     * @param integer $order_id 订单id path
     * @path /api/order/{order_id}/deliver
     */
    public function deliver(Request $request, $order_id) {
        $deliverInfo = OrderDeliverModel::getList($order_id);
        $delivers = [];
        foreach($deliverInfo as $v) {
            if (! isset($delivers[$v->deliver_no])) {
                $delivers[$v->deliver_no] = [
                    'number' => 1,
                    'deliver_no' => $v->deliver_no,
                    'created_time' => $v->created_time,
                    //发货时间
                    'send_time' => $v->send_deliver_time,
                    'express_company_name' => $v->express_company_name,
                    'logisticCode' => $v->logisticCode,
                    'reason' => $v->reason,
                    'order_id' => $v->order_id,
                    'order_deliver_id' => $v->order_deliver_id,
                    'deliver_status' => $v->deliver_status
                ];
            } else {
                $delivers[$v->deliver_no]['number'] += 1;
            }
        }
        $delivers = array_values($delivers);
        return $this->success($delivers);
    }

    /**
     * @method get
     * @desc 出库发货商品列表
     * @param string $token token header
     * @param string $deliver_no 出库单号 path
     * @path /api/deliver/{deliver_no}/goods
     */
    public function deliverGoods(Request $request, $deliver_no) {
        $goodsInfo = OrderDeliverModel::getDeliverGoods($deliver_no);
        $info = [];
        foreach($goodsInfo as $k => $v) {
            $images = json_decode($v->main_image, true);

            $standard_content = json_decode($v->standard_content, true);
            $standardInfo = [];
            if ($standard_content) {
                $standard = [];
                foreach($standard_content as $val) {
                    $standard[] = $val;
                }
                $standardInfo = $standard;
            }

            $info[$k]['goods_id'] = $v->goods_id;
            $info[$k]['goods_standard_id'] = $v->goods_standard_id;
            $info[$k]['main_image'] = $images ? $images['url'] : '';
            $info[$k]['number'] = $v->deliver_quantity;
            $info[$k]['standard_content'] = implode(',', $standardInfo);
            $info[$k]['goods_name'] = $v->goods_name;
            $info[$k]['price'] = $v->price;
            $info[$k]['goods_unit_name'] = $v->unit_name;
        }
        return $this->success($info);
    }

    /**
     * @method get
     * @desc 获取物流信息
     * @param string $token token header
     * @param integer $order_deliver_id 出库ID path
     * @path /api/order/{order_deliver_id}/express
     */
    public function express(Request $request, $order_deliver_id) {
        $expressInfo = ExpressInfoModel::where(['order_deliver_id' => $order_deliver_id])->first();
        if ($expressInfo)
            if ($expressInfo->traces) {
                $expressInfo->traces = json_decode($expressInfo->traces, true);
            } else {
                $expressInfo->traces = null;
            }
        return $this->success($expressInfo);
    }

}
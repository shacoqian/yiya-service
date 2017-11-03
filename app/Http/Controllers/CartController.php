<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/9/16
 * Time: 下午1:54
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Util\UserUtil;

use App\Models\GoodsStandardModel;
use App\Models\CartModel;

/**
 * 菜单相关api
 * @tags 商品相关
 *
 */

class CartController extends Controller {

    /**
     * @method post
     * @desc 加入购物车
     * @param string $token token header
     * @param integer $goods_standard_id 规格商品ID path
     * @param integer $number 数量
     *
     * @path /api/cart/{goods_standard_id}/add
     */
    public function add(Request $request, $goods_standard_id) {
        $goods_standard_id = $goods_standard_id;
        $account_id = UserUtil::getAccountId();
        $number = intval($request->input('number', 0 ));
        if (! $number) {
            return $this->fail([], '数量不能为空！');
        }

        //从redis中获取他的购物车信息
        $cartInfo = CartModel::getCartInfo($account_id);
        if ($cartInfo) {
            $cartInfo = json_decode($cartInfo, true);
            if (isset($cartInfo[$goods_standard_id])) {
                $cartInfo[$goods_standard_id] += $number;
            } else {
                $cartInfo[$goods_standard_id] = $number;
            }
        } else {
            $cartInfo = [];
            $cartInfo[$goods_standard_id] = $number;
        }


        $res = CartModel::setCart($account_id, json_encode($cartInfo));
        if ($res) {
            return $this->success([], '加入购物车成功！');
        } else {
            return $this->fail([], '加入购物车失败！');
        }

    }


    /**
     * @method get
     * @desc 获取购物车商品列表
     * @param string $token token header
     *
     * @path /api/cart/lists
     */
    public function lists(Request $request) {
        $account_id = UserUtil::getAccountId();
        $cartInfo = CartModel::getCartInfo($account_id);
        if (! $cartInfo) {
            return $this->success([], '购物车没有信息！');
        }
        $cartInfo = json_decode($cartInfo, true);

        $goods_standard_ids = [];
        foreach($cartInfo as $k => $v) {
            $goods_standard_ids[] = $k;
        }

        $goods_standard_info = GoodsStandardModel::getGoodsStandardList($goods_standard_ids);
        $info = [];
        foreach($goods_standard_info as $k => $v) {
            $images = json_decode($v->main_image, true);
            $standard = json_decode($v->standard_content, true);
            $standardInfo = [];
            if ($standard) {
                foreach($standard as $style) {
                    $standardInfo[] = $style;
                }
            }

            $info[$k]['goods_id'] = $v->goods_id;
            $info[$k]['goods_standard_id'] = $v->goods_standard_id;
            $info[$k]['main_image'] = $images ? $images['url'] : '';
            $info[$k]['number'] = $cartInfo[$v->goods_standard_id];
            $info[$k]['standard_content'] = implode(',', $standardInfo);
            $info[$k]['goods_name'] = $v->goods_name;
            $info[$k]['price'] = $v->price;
            $info[$k]['goods_unit_name'] = $v->goods_unit_name;
        }

        return $this->success($info);
    }

    /**
     * @desc 修改购物车数量
     * @method get
     * @param string $token token header
     * @param integer $goods_standard_id 规格iD
     * @param integer $number 修改数量
     *
     * @path /api/cart/{goods_standard_id}/edit
     */
    public function edit(Request $request, $goods_standard_id) {
        $account_id = UserUtil::getAccountId();
        $cartInfo = CartModel::getCartInfo($account_id);
        if (! $cartInfo) {
            return $this->fail([], '购物车信息不存在！');
        }

        $number = intval($request->input('number', 0));

        $cartInfo = json_decode($cartInfo, true);
        if (isset($cartInfo[$goods_standard_id])) {
            $cartInfo[$goods_standard_id] = $number;
        } else {
            return $this->fail([], '要修改的商品信息不存在！');
        }
        $boolen = CartModel::setCart($account_id, json_encode($cartInfo));
        if ($boolen) {
            return $this->success([], '修改购物车信息成功！');
        } else {
            return $this->fail([], '修改购物车信息失败！');
        }
    }

    /**
     * @desc 获取购物车数量
     * @method get
     * @param string $token token header
     * @path /api/cart/number
     */
    public function number() {
        $cartInfo = CartModel::getCartInfo(UserUtil::getAccountId());
        if (! $cartInfo) {
            return $this->success(['number' => 0]);
        }
        $cartInfo = json_decode($cartInfo, true);
        return $this->success(['number' => count($cartInfo)]);
    }

    /**
     * @desc 删除购物车信息
     * @method get
     * @param string $token token header
     * @param integer $goods_standard_id 规格ID
     * @path /api/cart/{goods_standard_id}/delete
     */
    public function deleteByGoodsStandardId(Request $request, $goods_standard_id) {
        $account_id = UserUtil::getAccountId();
        $cartInfo = CartModel::getCartInfo($account_id);
        if (! $cartInfo) {
            return $this->fail([], '购物车信息不存在！');
        }
        $cartInfo = json_decode($cartInfo, true);
        if (isset($cartInfo[$goods_standard_id])) {
            unset($cartInfo[$goods_standard_id]);
        } else {
            return $this->fail([], '此商品信息不存在！');
        }

        $boolen = CartModel::setCart($account_id, json_encode($cartInfo));
        if ($boolen) {
            return $this->success([], '删除购物车信息成功！');
        } else {
            return $this->fail([], '删除购物车信息失败！');
        }
    }

    /**
     * @method get
     * @desc 清空购物车
     * @param string $token token header
     *
     * @path /api/cart/clear
     */
    public function clear(Request $request) {
        $account_id = UserUtil::getAccountId();
        $res = CartModel::clearCart($account_id);
        if ($res) {
            return $this->success([], '清空购物车成功！');
        } else {
            return $this->fail([], '清空购物车失败！');
        }
    }

}
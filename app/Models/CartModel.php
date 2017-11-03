<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/22
 * Time: 下午5:08
 */

namespace App\Models;

use Illuminate\Support\Facades\Redis;
use Log;

class CartModel  {

    public static function getKey($account_id) {
        return env('CART_REDIS_KEY') . $account_id;
    }

    //获取购物车信息
    public static function getCartInfo($account_id) {
        $key = self::getKey($account_id);
        return Redis::get($key);
    }


    //设置购物车信息
    public static function setCart($account_id, $cartInfo) {
        try {
            $key = self::getKey($account_id);
            Redis::set($key, $cartInfo);
            Redis::expire($key, strtotime('+5 days'));
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    //清空购物车信息
    public static function clearCart($account_id) {
        try {
            $key = self::getKey($account_id);
            Redis::del($key);
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }
    }


    //根据收货地址省份 和购买的商品 来

}
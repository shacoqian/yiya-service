<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/27
 * Time: 下午3:50
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class GoodsStoreSettingModel extends Model
{

    protected $table = 'goods_store_setting';

    //根据收货地址区域和规格ID分类获取仓库信息

    //添加商品 默认按照goods_id判断规格在哪个仓库  也有单独设置
    public static function getStoresInfo($goods_ids, $standard_ids, $province_id) {
        $storeSettingInfo =  self::select('goods_standard_id', 'store_id')
            ->where(['region_id' => $province_id, 'disabled' => 0])->
            whereIn('goods_id', $goods_ids)
            ->get();
        $storeInfo = [];
        foreach($storeSettingInfo as $v) {
            $storeInfo[$v->goods_standard_id] = $v->store_id;
        }

        $standard_store = [];
        foreach($standard_ids as $v) {
            if (isset($storeInfo[$v])) {
                $standard_store[$storeInfo[$v]][] = $v;
            } else {
                $standard_store[$storeInfo[0]][] = $v;
            }
        }
        return $standard_store;
    }



}
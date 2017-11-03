<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/27
 * Time: 下午3:48
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GoodsStandardModel extends Model
{

    protected $table = 'goods_standard';


    //根据规格id获取商品信息
    public static function getGoodsStandardList($ids) {
        return DB::table('goods_standard')
            ->leftJoin('goods', 'goods_standard.goods_id', '=', 'goods.goods_id')
            ->leftJoin('goods_unit', 'goods.goods_unit_id', '=', 'goods_unit.goods_unit_id')
            ->whereIn('goods_standard.goods_standard_id', $ids)
            ->get();
    }


    //获取商品规格
    public static function getStandardList($goods_id) {
        return DB::table('goods_standard')
            ->select(
                'goods_standard.*',
                'goods_inventory_view.inventory',
                'goods.goods_unit_name'
            )
            ->leftJoin(
                'goods_inventory_view',
                'goods_standard.goods_standard_id',
                '=',
                'goods_inventory_view.goods_standard_id'
            )
            ->leftJoin(
                'goods',
                'goods.goods_id',
                '=',
                'goods_standard.goods_id'
            )
            ->where([
                'goods_standard.goods_id' => $goods_id,
                'goods_standard.disabled' => 0, //没删除
                'goods_standard.shelf_status' => 1 //上架
            ])->get();

    }

}
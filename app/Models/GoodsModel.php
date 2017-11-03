<?php
/**
 * Created by PhpStorm.
 * User: guohao
 * Date: 2017/8/27
 * Time: 下午3:47
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Util\ModelUtil;



class GoodsModel extends Model {

    public static $tableName = 'goods';

    protected $table = 'goods';

    public static function goodsList($company_id, $page, $size, $keywords = null, $category_id = null) {
        $query = DB::table(self::$tableName)
            ->leftJoin(
                'goods_standard',
                'goods.goods_id', '=', 'goods_standard.goods_id'
            )
            ->select('goods.*', 'goods_standard.main_image')
            ->where('goods.company_id', '=', $company_id)
            ->where('goods.disabled', '=', 0)
            ->where('goods.shelf_status_bak', '=', 0)
            ->where('goods_standard.goods_standard_id', '=', function($q){
                $q->select('goods_standard_id')
                    ->where('goods_id', '=', DB::raw('goods.goods_id'))
                    ->from('goods_standard')->limit(1);
            });
        if ($category_id) {
            $query->where('goods.goods_category_parent_id', 'like', '0-' . $category_id . '%');
        }

        if ($keywords) {
            $query->where(function($q) use ($keywords) {
                $q->orWhere('goods.goods_name', 'like', '%' . $keywords . '%');
            });
        }

        $count = $query->count();
        $res = $query->orderBy('goods.goods_sort', 'asc')
            ->orderBy('goods.goods_id', 'desc')
            ->skip(($page-1)*$size)
            ->take($size)
            ->get();

        return [$count, $res];
    }

}
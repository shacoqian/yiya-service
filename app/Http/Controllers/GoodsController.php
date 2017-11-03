<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/28
 * Time: 下午2:48
 */

namespace App\Http\Controllers;
use App\Models\GoodsImageModel;
use App\Models\GoodsModel;
use App\Models\GoodsStandardModel;
use Illuminate\Http\Request;
use App\Util\UserUtil;

/**
 * 菜单相关api
 * @tags 商品相关
 *
 */


class GoodsController extends  Controller {

    /**
     * @method get
     *
     * @desc 分页获取接口列表
     *
     * @param string $token token header
     * @param string $keywords 关键词
     * @param integer $goods_category_id 分类id
     * @param integer $page 分页
     * @param integer $size 每页数量
     *
     * @path /api/goods/lists
     */
    public function lists(Request $request) {
        $category_id = $request->input('goods_category_id', null);
        $keywords = $request->input('keywords', null);
        list($page, $size) = $this->paging($request->input());
        list($count, $goods) = GoodsModel::goodsList(UserUtil::getCompanyId(), $page, $size, $keywords, $category_id);

        foreach($goods as &$v) {
            $main_images = json_decode($v->main_image, true);
            if ($main_images) {
                $v->main_image = $main_images['url'];
            } else {
                $v->main_image = '';
            }
        }

        return $this->success([
            'count' => $count,
            'page' => $page,
            'size' => $size,
            'data' => $goods
        ], '请求数据成功');
    }

    /**
     * @method get
     * @desc 根据商品id 获取商品的规格
     * @param string $token token header
     * @param string $goods_id 商品id path
     *
     * @path /api/goods/{goods_id}/standard
     */
    public function getStandard(Request $request, $goods_id) {
        $res = GoodsStandardModel::getStandardList($goods_id);
        if (! $res) {
            return $this->fail([], '该商品没有可销售的规格！');
        }

        $standardList = [];
        $standard = [];
        $newStandard = [];
        foreach($res as $k => $v) {
            $v = get_object_vars($v);
            $standardStyle = [];
            $images = json_decode($v['main_image'], true);
            if ($images) {
                $v['main_image'] = $images['url'];
            }
            $v['standard_content'] = json_decode($v['standard_content'], true);
            foreach($v['standard_content'] as $key => $val) {
                if (! isset($standard[$key])) {
                    $standard[$key] = [$val];
                } else {
                    if (! in_array($val, $standard[$key])) {
                        $standard[$key] = array_merge($standard[$key], [$val]);
                    }
                }

                if (! in_array($val, $standardStyle)) {
                    $standardStyle[] = $val;
                }
            }
            $v['standard_content'] = $standardStyle;
            $v['inventory'] = $v['inventory'] ? $v['inventory'] : 0;
            $standardList[$k] = $v;
        }

        foreach($standard as $key => $value) {
            $newStandard[] = [
                'standardName' => $key,
                'standardStyle' =>$value
            ];
        }

        return $this->success([
            'data' => $standardList,
            'standard' => $newStandard
        ], '获取数据成功!');
    }

    /**
     * @method get
     * @desc 获取商品详情
     * @param string $token token header
     * @param integer $goods_id 商品ID path
     *
     * @path /api/goods/{goods_id}/detail
     */
    public function detail(Request $request, $goods_id) {
        $goods = GoodsModel::where(['goods_id' => $goods_id, 'disabled' => 0])->first();
        if ($goods) {
            $goods = $goods->toArray();
            $goodsImages = GoodsImageModel::where(['goods_id' => $goods_id, 'disabled' => 0])->get();
            $images = [];
            foreach($goodsImages as $v) {
                $imageInfo = json_decode($v->image_path, true);
                if ($imageInfo) {
                    $images[] = isset($imageInfo['url']) ? $imageInfo['url'] : '';
                }
            }
            $goods['images'] = $images;
            return $this->success($goods);
        } else {
            return $this->fail([], '商品信息不存在!');
        }


    }

}
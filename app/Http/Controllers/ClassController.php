<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/23
 * Time: 下午1:47
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GoodsClassModel;
use App\Util\UserUtil;

/**
 * 菜单相关api
 * @tags 商品相关
 *
 */

class ClassController extends Controller {
    /**
     * @method get
     * @desc 获取菜单列表
     *
     * @param string $token token header
     *
     * @path /api/classes
     *
     * @return {"status":1,"result":[{"goods_class_id":1,"parent_class_id":0,"name":"test","level":null,"create_time":null,"disabled":0,"customer_id":null,"company_id":1}],"message":"\u83b7\u53d6\u6570\u636e\u6210\u529f\uff01"}
     *
     */

    public function classes() {
        $res = GoodsClassModel::where(['company_id' => UserUtil::getCompanyId(), 'disabled' => 0, 'parent_category_id' => 0 ])->get();
        return $this->success($res, '获取数据成功！');
    }


}
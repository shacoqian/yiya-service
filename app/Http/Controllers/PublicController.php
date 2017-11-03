<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/26
 * Time: 下午4:36
 */

namespace  App\Http\Controllers;

use App\Models\RegionModel;
use Illuminate\Http\Request;

use App\Models\VersionModel;

/**
 * 公用接口
 *
 * @tags 公用接口
 */

class PublicController extends  Controller {

    /**
     * @method get
     *
     * @desc 获取全部省市区数据
     *
     * @path /api/public/region/all
     *
     */
    public function regionAll() {
        $res = RegionModel::all();
        return $this->success($res, '获取数据成功！');
    }

    /**
     * @method get
     *
     * @desc 获取省份数据
     *
     * @path /api/public/provinces
     */
    public function provinces() {
        $res = RegionModel::where(['parent_id' => 1])->get();
        return $this->success($res, '获取数据成功！');
    }

    /**
     * @method get
     *
     * @desc 根据父id获取子区域数据
     * @param intger $id 父id path
     * @path /api/public/{id}/regions
     */
    public function citys(Request $request, $id) {
        $res = RegionModel::where(['parent_id' => $id])->get();
        return $this->success($res, '获取数据成功！');
    }

    /**
     * @method get
     *
     * @desc 获得所有区域信息
     * @path /api/public/getRegions
     */
    public function regions() {
        return $this->success(RegionModel::getFormatRegions());
    }

    /**
     * @method get
     * @desc 检查版本更新
     * @param string $token token header
     * @param string $version 版本号
     * @param string $platform 平台（android,ios）
     *
     * @path /api/version
     */
    public function version(Request $request) {
        $version = $request->input('version', '');
        $platform = $request->input('platform', 'android');

        $versionInfo = VersionModel::where(['platform' => $platform])->first();
        if ($versionInfo) {
            $versionInfo = $versionInfo->toArray();
            if ($this->getUpdateStatus($version, $versionInfo['version'])) {
                $versionInfo['forceUpdate'] = $versionInfo['forceUpdate'];
                $versionInfo['needUpdate'] = 1;
            } else {
                $versionInfo['forceUpdate'] = 0;
                $versionInfo['needUpdate'] = 0;
            }
            return $this->success($versionInfo);
        } else {
            return $this->fail(null, '平台不存在！');
        }
    }

    //判断是否需要升级
    protected function getUpdateStatus($appVesion, $newVersion) {
        $appInfo = explode('.', $appVesion);
        $newInfo = explode('.', $newVersion);

        foreach($newInfo as $k => $v) {
            if (isset($appInfo[$k]) && $v > $appInfo[$k]) {
                return true;
            }
        }
        return false;
    }

}
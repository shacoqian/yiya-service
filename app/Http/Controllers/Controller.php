<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Util\UserUtil;
use Illuminate\Contracts\Auth\Factory as Auth;

class Controller extends BaseController
{
    protected  $_user;

    public $size = 10;

    public function __construct(Auth $auth)
    {
        UserUtil::setUser($auth);
    }


  /**
     * 操作成功
     * @param $data
     * @param $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = [], $msg = '数据请求成功！')
    {
        return response()->json(['status'=>1,'result'=>$data,'message'=>$msg]);
    }

    /**
     * 操作失败
     * @param $data
     * @param $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function fail($data = null,$msg = '数据请求失败！')
    {
        $data = null;
        return response()->json(['status' => 0, 'result' => $data, 'message' => $msg]);
    }

    //分页参数转换
    protected function paging($params) {
        $size = isset($params['size']) ? intval($params['size']) : $this->size;
        $page = isset($params['page']) ? intval($params['page']) : 1;
        $page = $page < 1 ? 1 : $page;
        return [$page, $size];
    }
}

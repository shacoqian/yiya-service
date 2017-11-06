<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/23
 * Time: 下午1:47
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Models\UsersModel;

/**
 * 用户相关API
 * @tags 用户相关
 *
 */

use App\Rpc\WxRpc;


class AccountController extends Controller {

    /**
     * @method post
     * @description 用户登录
     * @param string $account_name 用户名
     * @param string $password 密码
     * @path /api/login
     * @return {"status":1,"result":{"account_id":25,"account_name":"qian","password":"e10adc3949ba59abbe56e057f20f883e","account_type":3,"role_ids":"","nick_name":"\u6211\u54ea\u77e5\u9053","creater":0,"parent_account_id":null,"created_at":null,"disabled":0,"phone":null,"salt":null,"company_id":1,"email":null,"qq":null,"updated_at":null,"login_time":1504076647,"token":"c4cfa7b9087f0e187c570b9b6854fce6"},"message":"\u767b\u5f55\u6210\u529f"}
     */
    public function login(Request $request) {
        $code = $request->input('code');
        $res = WxRpc::get('/sns/jscode2session', [
            'js_code' => $code
        ]);
        if (! empty($res)) {
            //查询数据库 新增或者修改
            UsersModel::setWxKeyInfo($res['appid'], $res['session_key']);
            //存入redis
            $redisKey = md5($res['app_id']);
            Redis::set($redisKey, json_encode([
                'app_id' => $res['app_id'],
                'session_key' => $res['session_key']
            ]));
            return $this->success(['token' => $redisKey], '登录成功');
        } else {
            return $this->fail([], '登录失败！');
        }


        //Redis::set('a', '123456');
        //Redis::expire('a', time() + 3600);

    }




}
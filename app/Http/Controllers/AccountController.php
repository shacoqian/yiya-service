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
use App\Models\AccountModel;
use App\Models\CustomerReapAddressList;
use App\Models\CustomerModel;
use App\Models\CompanyModel;
use App\Util\UserUtil;

/**
 * 用户相关API
 * @tags 用户相关
 *
 */

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
        return $this->success([
            'aa' => 11,
            'bb' => 22
        ]);
    }




}
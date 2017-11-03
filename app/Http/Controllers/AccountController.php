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
        $account = $request->input('account_name', '');
        $password = $request->input('password', '');
        $info = AccountModel::where(['account_name' => $account, 'disabled' => 0, 'account_type' => 3])->first();

        if (! $info) {
            return $this->fail([], '用户名错误！');
        }

        if (md5($password . $info->salt) !== $info->password) {
            return $this->fail([], '密码错误！');
        }

        //生成token
        $token = md5($info->account_id . microtime());
        $data = $info->toArray();
        $data['login_time'] = time();

        $data = array_merge($data, [
            'name' => '',
            'company_name' => '',
            'customer_name' => '',
            'logo' => ''
        ]);
        //获取客户信息
        $customer_info = CustomerModel::getCustomerDetail($info->account_id);
        if (! empty($customer_info)) {
            $data['name'] = $customer_info->name;
            $data['company_name'] = $customer_info->company_name;
            $data['customer_name'] = $customer_info->customer_name;
            $logo = json_decode($customer_info->logo, true);
            $data['logo'] = isset($logo['url']) ? $logo['url'] : '';
        }

        Redis::set($token, json_encode($data));
        Redis::expire($token, time() + 86400 * 30);
        $data['token'] = $token;
        unset($data['password']);

        //写入redis
        return $this->success($data, '登录成功');
    }

    /**
     * @method get
     * @desc 退出登录
     * @param string $token token header
     *
     * @path /api/loginOut
     *
     * @return {"status":1,"result":[],"message":"\u9000\u51fa\u767b\u5f55\u6210\u529f\uff01"}
     */
    public function loginOut(Request $request) {
        $token = $request->header(env('SECURITY_TOKEN'));
        Redis::del($token);
        return $this->success([],'退出登录成功！');
    }


    /**
     * @method get
     * @desc 获取用户收货地址列表
     * @param string $token token header
     * @path /api/account/address/lists
     */
    public function addressList(Request $request) {
        $account_id = UserUtil::getAccountId();
        $result = CustomerReapAddressList::where(['account_id' => $account_id, 'disabled' => 0])->get();
        return $this->success($result, '获取数据成功！');
    }

    /**
     * @method get
     * @desc 获取用户的默认收货地址
     * @param string $token token header
     * @path /api/account/address/default
     *
     *
     */
    public function addressDefault(Request $request) {
        $account_id = UserUtil::getAccountId();
        $res = CustomerReapAddressList::where(['account_id' => $account_id, 'is_default' => 1])->first();
        return $this->success($res, '获取数据成功！');
    }

    /**
     *
     * @method post
     * @desc 新增收货地址
     * @param string $token token header
     * @param string $account_id 用户id path
     *
     * @param string $region_id 区域id
     * @param string $reap_name 收货人
     * @param string $reap_phone 手机号
     * @param string $region_name 区域名
     * @param string $address 详细地址
     * @param integer $is_default 是否为默认收货地址
     *
     * @path /api/account/address/add
     */
    public function addressCreate(Request $request) {
        $account_id = UserUtil::getAccountId();
        $params = $request->only('region_id', 'reap_name', 'reap_phone', 'region_name',
            'reap_phone', 'region_name', 'address', 'is_default');
        $params['account_id'] = $account_id;
        if ($params['is_default'] == 1) {
            CustomerReapAddressList::where(['account_id' => $account_id])->update(['is_default' => 0]);
        }

        if (CustomerReapAddressList::create($params)) {
            return $this->success([], '添加收货地址成功！');
        } else {
            return $this->fail([], '添加收货地址失败！');
        }
    }

    /**
     * @method post
     * @desc 修改收货地址
     * @param string $token token header
     * @param integer $id 收货地址id path
     *
     * @param string $region_id 区域Id
     * @param string $region_name 用户名
     * @param string $reap_phone 手机号
     * @param string $reap_name 收货人
     * @param string $address 详细地址
     * @param integer $is_default 是否为默认收货地址
     *
     * @path /api/account/{id}/address/update
     */
    public function addressUpdate(Request $request, $id) {
        $params = filter_values($request->only('region_id', 'reap_name', 'reap_phone', 'region_name',
            'reap_phone', 'region_name', 'address', 'is_default'));
        $res = CustomerReapAddressList::where(['address_id' => $id, 'disabled' => 0])->first();
        if ($res) {
            $account_id = $res->account_id;
            if (isset($params['is_default']) && $params['is_default'] == 1) {
                CustomerReapAddressList::where(['account_id' => $account_id])->update(['is_default' => 0]);
            } else {
                $count = CustomerReapAddressList::where(['account_id' => $account_id, 'disabled' => 1, 'is_default' => 1])->count();
                if (! $count) {
                    return $this->fail([], '必须先设置一个默认收货地址');
                }
            }
            CustomerReapAddressList::where(['address_id' => $id])->update($params);
            return $this->success([], '修改收货信息成功！');
        } else {
            return $this->fail([], '收货信息不存在!');
        }

    }

    /**
     * @method delete
     * @desc 删除收货地址
     * @param string $token token header
     * @param integer $id 收货地址id path
     *
     * @path /api/account/{id}/address/delete
     */
    public function addressDelete(Request $request, $id) {
        $res = CustomerReapAddressList::where(['address_id' => $id, 'disabled' => 0])->first();
        if ($res) {
            if ($res->is_default == 1) {
                return $this->fail([], '请先取消默认收货地址');
            }
            CustomerReapAddressList::find($id)->update(['disabled' => 1]);
            return $this->success([], '删除收货地址成功！');
        } else {
            return $this->fail([], '收货地址信息不存在!');
        }
    }

    /**
     * @method put
     * @desc 设置默认收货地址
     * @param string $token token header
     * @param integer $id 收货地址id path
     *
     * @path /api/account/{id}/set/default
     */
    public function setAddressDefault(Request $request, $id) {
        $res = CustomerReapAddressList::where(['address_id' => $id, 'disabled' => 0])->first();
        if ($res) {
            CustomerReapAddressList::where(['account_id' => $res->account_id])->update(['is_default' => 0]);
            CustomerReapAddressList::where(['address_id' => $id])->update(['is_default' => 1]);
            return $this->success([], '设置默认收货地址成功！');
        } else {
            return $this->fail([], '收货地址信息不存在!');
        }
    }

    /**
     * @method get
     * @desc 获取客户信息
     * @param string $token token header
     * @path /api/account/info
     *
     * @return {"status":1,"result":{"customer_id":15,"customer_name":"\u4e50\u864e\u6c5f\u897f\u603b\u4ee3\u740615-1-2","account_id":null,"role_id":7,"parent_account_id":"0-4","code":null,"sign_start_time":null,"sign_end_time":null,"post_code":null,"fax":null,"region_id":null,"region_name":null,"address":null,"logistics_code":null,"standby_info":null,"ct_create_time":null,"ct_update_time":null,"disabled":"0","name":null,"phone":null,"position":null,"email":null,"contract_telephone":null,"qq":null,"invoice_title":null,"identify_number":null,"telephone":null,"open_account_name":null,"open_account_bank":null,"account_bank_number":null},"message":"\u83b7\u53d6\u6570\u636e\u6210\u529f\uff01"}
     */
    public function customerInfo(Request $request) {
        $account_id = UserUtil::getAccountId();
        $info = CustomerModel::customer_info($account_id);
        return $this->success($info, '获取数据成功！');
    }

    /**
     * @method post
     * @desc 修改客户信息
     * @param string $token token header
     * @param integer $account_id 用户id path
     * @param string $region_id 区域id（英文逗号分隔）
     * @param string $region_name 区域名字（/分隔）
     * @param string $customer_address 详细地址
     * @param string $post_code 邮编
     * @param string $fax 传真
     * @param string $phone 手机
     * @param string $invoice_title 发票抬头
     * @param string $identify_number 纳税人识别号
     * @param string $address 财务信息地址
     * @param string $telephone 财务信息电话
     * @param string $open_account_name 开户名称
     * @param string $open_account_bank 开户行
     * @param string $account_bank_number 银行卡号
     *
     * @path /api/account/info/edit
     */
    public function customerInfoEdit(Request $request) {
        $account_id = UserUtil::getAccountId();

        $customer = filter_values(
            $request->only(
                'customer_address',
                'region_id',
                'region_name'
            )
        );

        $customer_info = filter_values(
            $request->only(
                'post_code',
                'phone',
                'fax'
            )
        );

        $customer_finance = filter_values(
            $request->only(
                'invoice_title',
                'identify_number',
                'telephone',
                'open_account_name',
                'open_account_bank',
                'account_bank_number'
            )
        );

        $res = CustomerModel::editCustomer($account_id,$customer, $customer_info, $customer_finance);
        if ($res) {
            return $this->success();
        } else {
            return $this->fail();
        }
    }

    /**
     * @method post
     * @desc 修改密码
     * @param string $token token header
     * @param $old_password 旧密码
     * @param $new_password 新密码
     * @param $new_password_confirmation 确认密码
     * @path /api/account/password/edit
     */
    public function editPassword(Request $request) {
        $oldPassword = $request->input('old_password');
        $user = UserUtil::getUser();
//        var_dump($user);exit;
        if (generate_password($oldPassword, $user['salt']) != $user['password']) {
            return $this->fail([], '旧密码不正确！');
        }

        $newPassword = $request->input('new_password');

        if (AccountModel::where(['account_id' => $user['account_id']])->update([
            'password' => generate_password($newPassword, $user['salt'])
        ])) {
            return $this->success([], '密码修改成功！');
        } else {
            return $this->fail([], '密码修改失败!');
        }
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 17-5-5
 * Time: 上午10:10
 */

namespace App\Http\Requests;


Class AccountFormRequest extends FormRequest {
    public function rules() {
        return [
            ['account_name', 'required', 'message' => '用户名不能为空', 'on' => ['login']],
            ['password', 'required', 'message' => '密码不能为空', 'on' => ['login']],
            ['region_id', 'required', 'message' => '区域id不能为空', 'on' => ['addressCreate']],
            ['reap_name', 'required', 'message' => '收货人姓名不能为空', 'on' => ['addressCreate']],
            ['reap_phone', 'required', 'message' => '收货人手机不能为空', 'on' => ['addressCreate']],
            ['region_name', 'required', 'message' => '区域名字不能为空', 'on' => ['addressCreate']],
            ['address', 'required', 'message' => '详细地址不能为空', 'on' => ['addressCreate']],


            ['old_password', 'required', 'message' => '旧密码不能为空', 'on' => ['editPassword']],
            ['new_password', 'confirmed', 'message' => '两次密码不同', 'on' => ['editPassword']],
            ['new_password_confirmation', 'required', 'message' => '确认密码不能为空', 'on' => ['editPassword']]
        ];

    }
}
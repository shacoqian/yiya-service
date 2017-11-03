<?php
namespace App\Http\Requests;


Class CustomerFormRequest extends FormRequest {
  public function rules() {
    return [
        ['customer_name', 'required', 'message' => '客户名称不能为空', 'on' => ['insert']],
        ['role_id', 'required', 'message' => '请选择客户级别', 'on' => ['insert', 'update']],
        ['name', 'required', 'message' => '姓名不能为空', 'on' => ['insert']],
        ['phone', 'required', 'message' => '手机号不能为空', 'on' => ['insert']],
//        ['nick_name', 'required', 'message' => '姓名不能为空', 'on' => ['add']],
//        ['phone', 'required', 'message' => '手机号不能为空', 'on' => ['add']],
//        ['phone', 'regex:/^1[34578][0-9]{9}$/', 'message' => '请输入正确的手机号', 'on' => ['add', 'edit']],
//        ['email', 'email', 'message' => '必须输入正确的邮箱账号', 'on' => ['add', 'edit']],
//        ['disabled', 'in:0,1', 'message' => '发送状态不对', 'on' => ['disabled']],
    ];

  }
}
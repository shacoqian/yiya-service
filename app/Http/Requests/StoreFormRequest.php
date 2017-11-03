<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 17-5-5
 * Time: 上午10:10
 */

namespace App\Http\Requests;


Class StoreFormRequest extends FormRequest {
    public function rules() {
        return [
            ['name', 'required', 'message' => '仓库名称不能为空！', 'on' => ['add']],
            ['code', 'required', 'message' => '仓库编码不能为空！', 'on' => ['add']],
            ['region_ids', 'required', 'message' => '区域信息不能为空！', 'on' => ['add']],
            ['address', 'required', 'message' => '详细地址不能为空！', 'on' => ['add']],
            ['disabled', 'required', 'message' => '禁用参数不能为空！', 'on' => ['disabled']],
            ['disabled', 'in:0,1', 'message' => '禁用参数只能是0，1', 'on' => ['disabled']],

        ];

    }
}
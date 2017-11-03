<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/23
 * Time: 下午6:32
 */

namespace App\Http\Controllers;

use App\Swagger\Swagger as Doc;

class SwaggerController extends Controller {

    /**
     * @info title 订货APP api
     * @info description 订货APP api文档
     * @info version 1.0.0
     */
    public function json() {

        $dirname = __DIR__;
        Doc::$pre_url = '';
        Doc::$rootNameSpace = '\\App\Http\Controllers';
        Doc::$disabled = ['Controller'];
        Doc::load($dirname);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/31
 * Time: 下午3:27
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Extensions\Upload\ImageUpload;
use App\Models\ExpressCompanyModel;

use App\Extensions\Express\Express;

use App\Models\ExpressInfoModel;

use Log;


class TestController extends Controller {

    public function index(Request $request) {

        //post 上传
        $file = new ImageUpload($request, 'photo', '');

        //base64上传
        //$file = new ImageUpload($request, 'photo', 'base64', 'jpg');
        if ($file->getError() === 0) {
            $res = $file->move();
            if ($file->getError() === 0) {
                var_dump($res);
            }
        }
//
        var_dump($file->getErrorMessage());
    }

    public function logTest() {
        Log::info('我是info', ['a' => 123, 'b'=> 456]);
        Log::info('我是info2');

        Log::warning('我是warning', ['a' => 123, 'b'=> 456]);

        Log::error('我是error', ['a' => 123, 'b'=> 456]);
    }


    public function set_cookie() {
        $res = setcookie('test', 'test', time() + 360000, '/', 'qf.yqplan.com');
        var_dump($res);
        echo 'ok';
    }



    //读取快递公司信息

    public function express() {

        ExpressInfoModel::changeDeliver(8);

    }
}
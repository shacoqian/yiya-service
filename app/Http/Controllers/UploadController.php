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

/**
 * 用户相关API
 * @tags 公用接口
 *
 */

class UploadController extends Controller {

    /**
     * @method post
     *
     * @desc form图片上传
     * @param string $token token header
     * @param resource $photo 上传的文件
     *
     * @path /api/public/image/upload
     * @return {"status":1,"result":{"path":"2017\/09\/03\/15044198475729.png","url":"http:\/\/localhost:8111\/canyou\/2017\/09\/03\/15044198475729.png"},"message":"\u4e0a\u4f20\u56fe\u7247\u6210\u529f\uff01"}
     */
    public function imageUpload(Request $request) {
        $file = new ImageUpload($request, 'photo', '');
        if ($file->getError() === 0) {
            $res = $file->move();
            if ($file->getError() === 0) {
                return $this->success($res, '上传图片成功！');
            }
        }
        return $this->fail([], $file->getErrorMessage());
    }

    /**
     * @method post
     *
     * @desc form图片上传
     * @param string $token token header
     * @param resource $photo 上传图片的base64编码
     * @param string $ext 文件的扩展名
     *
     * @path /api/public/base64image/upload
     * @return {"status":1,"result":{"path":"2017\/09\/03\/15044198965984.png","url":"http:\/\/localhost:8111\/canyou\/2017\/09\/03\/15044198965984.png"},"message":"\u4e0a\u4f20\u56fe\u7247\u6210\u529f\uff01"}
     */
    public function base64ImageUpload(Request $request) {
        $file = new ImageUpload($request, 'photo', 'base64', $request->input('ext', ''));
        if ($file->getError() === 0) {
            $res = $file->move();
            if ($file->getError() === 0) {
                return $this->success($res, '上传图片成功！');
            }
        }
        return $this->fail([], $file->getErrorMessage());
    }

}
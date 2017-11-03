<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/31
 * Time: 下午3:12
 */

namespace App\Exceptions\Upload;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUpload  {

    public function __construct(Request $request)
    {
        parent::__construct();
    }



}
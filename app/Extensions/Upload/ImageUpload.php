<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/31
 * Time: 下午3:12
 */

namespace App\Extensions\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUpload  extends Upload {

    private $_file;

    public function __construct(Request $request, $key, $type = null, $ext = 'jpg')
    {
        $this->_config = Config::get('filesystem.images');
        $this->request = $request;
        $this->key = $key;
        if (! $this->empty_key()) return false;

        if ($type == 'base64') {
            if (! $this->base64Init()) {
                return false;
            }
        } else {
            if (! $this->init()) return false;
        }
        if ($this->_file === null) {
            return false;
        }
        parent::__construct($this->_file);
    }

    public function init() {
        $this->_file = $this->request->file($this->key);
        if (! $this->hasFile()) return false;
        if (! $this->isValid()) return false;
        return true;
    }

    public function base64Init() {
        if (!$file = $this->request->input($this->key, '')) {
            $this->_errors = self::NO_IMAGES_UPLOAD;
            return false;
        }
        $ext = $this->request->input('ext', '');
        if (! $ext) {
            $this->_errors = self::NO_ALLOW_EXT;
            return false;
        }
        $photo = base64_decode($file);

        $filename =  '/tmp/' . time() . rand(1,1000) . '.' . $ext;
        if (! file_put_contents($filename, $photo)) {
            $this->_errors = self::WRITE_FILE_ERROR;
            return false;
        }
        $this->_file = new UploadedFile($filename, $filename, null, null, null, true);
        return true;
    }


}
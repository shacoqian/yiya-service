<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/31
 * Time: 下午3:12
 */

namespace App\Extensions\Upload;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload  {

    protected $path;
    protected $_config = [];
    protected $_errors = 0;
    protected $file;
    protected $ext;
    protected $key;
    protected $request;

    const EMPTY_KEY = 1;
    const NO_IMAGES_UPLOAD = 2;
    const INVALID_IMAGE = 3;
    const NO_ALLOW_EXT = 4;
    const SIZE_TO_LARGE = 5;
    const MKDIR_FAILD = 6;
    const UNKNOW_ERROR = 7;
    const WRITE_FILE_ERROR = 8;

    const ERROR_INFO = [
        self::EMPTY_KEY => '上传文件的key不能为空！',
        self::NO_IMAGES_UPLOAD => '没有要上传的文件',
        self::INVALID_IMAGE => '无效的图片',
        self::NO_ALLOW_EXT => '不允许的扩展名',
        self::SIZE_TO_LARGE => '上传文件过大',
        self::MKDIR_FAILD => '新建文件夹失败',
        self::UNKNOW_ERROR => '未知错误',
        self::WRITE_FILE_ERROR => '写入文件失败！'
    ];

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
        $this->init();
    }

    private function init() {
        if (! $this->validate_exts()) return false;
        if (! $this->validate_size()) return false;
        $this->path = static_dir() . $this->_config['path'];
        return true;
    }

    //验证上传图片的key是否为空
    protected function empty_key() {
        if (empty($this->key)) {
            $this->_errors = self::EMPTY_KEY;
            return false;
        }
        return true;
    }

    //验证是否有图片上传
    protected function hasFile() {
        if (! $this->request->hasFile($this->key)) {
            $this->_errors = self::NO_IMAGES_UPLOAD;
            return false;
        }
        return true;
    }

    //验证图片是否上传成功
    protected function isValid() {
        if (! $this->request->file($this->key)->isValid()) {
            $this->_errors = self::INVALID_IMAGE;
            return false;
        }
        return true;
    }

    //验证图片类型是否正确
    protected function validate_exts() {
        $this->ext = strtolower($this->file->getClientOriginalExtension());
        if (! in_array($this->ext, $this->_config['fileType'])) {
            $this->_errors = self::NO_ALLOW_EXT;
            return false;
        }
        return true;
    }

    //验证文件的大小
    public function validate_size() {
        $size = $this->file->getClientSize();
        if ($size/1048576 > $this->_config['size']) {
            $this->_errors = self::SIZE_TO_LARGE;
            return false;
        }
        return true;
    }


    //生成文件路径和文件名
    public function getFileName() {
        $time = time();
        $year = date('Y', $time);
        $month = date('m', $time);
        $day = date('d', $time);
        $path = $year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . $day;
        if (! is_dir($this->path . $path)) {
            if (! mkdir($this->path . $path, 0755,true)) {
                $this->_errors = self::MKDIR_FAILD;
                return false;
            }
        }
        $filename =  $time . rand(1000, 9999) . '.' . $this->ext;
        return [$path, $filename];
    }

    public function move() {
        if(! list($path, $filename) = $this->getFileName()) {
            return false;
        }

        try {
            $this->file->move($this->path . $path, $filename);
            if ($this->file->getError() === 0) {
                $path = $path . DIRECTORY_SEPARATOR . $filename;
                return ['path' => $path, 'url' => static_url($path)];
            } else {
                $this->_errors[] = [$this->file->getErrorMessage()];
                return false;
            }
        } catch (\Exception $e) {
            $this->_errors = self::UNKNOW_ERROR;
            var_dump($e->getMessage());
            return false;
        }
    }

    public function getError() {
        return $this->_errors;
    }

    public function getErrorMessage() {
        return isset(self::ERROR_INFO[$this->_errors]) ? self::ERROR_INFO[$this->_errors] : '';
    }

}
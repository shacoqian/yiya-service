<?php

/**
 * 获取随机字符串
 * @param $length
 * @return string
 */
if (!function_exists('generate_string')) {
    function generate_string($length = 4)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghjkmnprstuvwxyACDEFGHJKLMNPQRSTUVWXYZ34567@';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $string;
    }
}



/**
 * 生成token
 * @param $account_id
 * @return string
 */
if (!function_exists('generate_token')) {
    function generate_token($account_id)
    {
       return md5($account_id . microtime());
    }
}

/**
 * 生成密码
 * @param $password
 * @param $salt
 * @return string
 */
if (!function_exists('generate_password')) {
    function generate_password($password, $salt)
    {
        return md5($password . $salt);
    }
}

/**
 * 过滤数组中的空值（不包含0）
 * @param $params array
 * @return array
 */
if (!function_exists('filter_values')) {
    function filter_values($params = array())
    {
        return array_filter($params, function($value){
            return ! empty($value) || $value === 0 || $value === '0';
        });
    }
}

/**
 * 获取静态文件的目录
 * @param $dir string
 * @return string
 */
if (!function_exists('static_dir')) {
    function static_dir($dir = 'static_file')
    {
        $path = app()->basePath() . '/../' . $dir;
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return realpath($path);
    }
}

/**
 * 获取静态资源的URL
 * @param $path string 图片的路径
 * @return string
 */
if (!function_exists('static_url')) {
    function static_url($path)
    {
        $config = Illuminate\Support\Facades\Config::get('filesystem.images');
        return $config['pre_url'] . $config['path'] . $path;
    }
}
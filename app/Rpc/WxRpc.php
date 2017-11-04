<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/11/4
 * Time: 下午5:27
 */

namespace App\Rpc;

class WxRpc {

    private static $base_url;
    private static $app_id;
    private static $app_secret;
    private static $client;
    private static $grant_type = 'authorization_code';

    public static function getBase() {
        self::$client = Rpc::getInstance();
        self::$base_url = config('app.wx_base_url');
        self::$app_id = config('app.wx_app_id');
        self::$app_secret = config('app.wx_secret');
    }


    public static function post($uri, $params) {
        return self::send('POST', $uri, $params);
    }

    public static function get($uri, $params) {
        return self::send('GET', $uri, $params);
    }

    public static function send($method, $uri, $params) {
        self::getBase();
        $url = self::getUrl($uri);
        $params = self::getParams($params);
        $response = self::$client->request($method, $url, $params);
        if ($response->getStatusCode == 200) {
            return json_decode($response->getBody(),true);
        } else {
            return false;
        }
    }

    public static function getUrl($uri) {
        echo self::$base_url;exit;
        return self::$base_url . $uri;
    }

    public static function getParams($params) {
        return array_merge([
            'appid' => self::$app_id,
            'secret' => self::$app_secret,
            'grant_type' => self::$grant_type,
        ], $params);
    }

}
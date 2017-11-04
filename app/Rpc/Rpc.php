<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/11/4
 * Time: 下午5:06
 */

namespace App\Rpc;

use GuzzleHttp\Client;

class Rpc {

    public static $client;


    public static function getInstance() {
        if (! self::$client || ! self::$client instanceof Client) {
            self::$client = new Client();
        }
        return self::$client;
    }

    public static function post($uri, $params) {

    }


}
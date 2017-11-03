<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/9/19
 * Time: 下午1:55
 */

namespace App\Extensions\Express;

use Log;

class Express {

    const EXPRESS_KEY = 'f71a22c5-052b-4c8e-bfde-70e46269ac80';
    //订阅回调方式
    const SUBSCRIBE_URL = 'http://api.kdniao.cc/api/dist';
    //即时查询方式
    const QUERY_URL = 'http://api.kdniao.cc/Ebusiness/EbusinessOrderHandle.aspx';

    const EBUSINESSID = '1304621';

    const REQUEST_TYPE = '101';

    public static  $error = null;

    //订阅
    public static function subscribe($shipperCode=123, $logisticCode=456789) {
        $url_info = self::getUrlInfo(self::SUBSCRIBE_URL);
        $post_data = self::getPostData($shipperCode, $logisticCode, 1008);
        $post_data = self::httpData($url_info, $post_data);
        $data = json_decode(self::send($url_info, $post_data), true);
        return $data ? $data : false;
    }

    //即时查询
    public static function instant($shipperCode=123, $logisticCode=456789) {
        $url_info = self::getUrlInfo(self::QUERY_URL);
        $post_data = self::getPostData($shipperCode, $logisticCode, 1002);
        $post_data = self::httpData($url_info, $post_data);
        $data = json_decode(self::send($url_info, $post_data), true);
        return $data ? $data : false;
    }



    public static function getPostData($shipperCode, $logisticCode, $type) {
        $requestData = json_encode([
            'ShipperCode' => $shipperCode,
            'LogisticCode' => $logisticCode
        ]);

        $datas = array(
            'EBusinessID' => self::EBUSINESSID,
            'RequestType' => $type,
            'RequestData' => urlencode($requestData),
            'DataType' => '2',
        );
        $datas['DataSign'] = self::encrypt($requestData, self::EXPRESS_KEY);

        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        return $post_data;
    }

    //获取URL信息
    public static function getUrlInfo($url) {
        $url_info = parse_url($url);
        if(empty($url_info['port'])) {
            $url_info['port']=80;
        }
        return $url_info;
    }

    //发送数据
    public static function  httpData($url_info, $post_data) {
        $data = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $data.= "Host:" . $url_info['host'] . "\r\n";
        $data.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $data.= "Content-Length:" . strlen($post_data) . "\r\n";
        $data.= "Connection:close\r\n\r\n";
        $data.= $post_data;
        return $data;
    }


    //加密数据
    public static function encrypt($data) {
        return urlencode(base64_encode(md5($data . self::EXPRESS_KEY)));
    }

    public static function send($urlInfo, $postData) {
        $fd = fsockopen($urlInfo['host'], $urlInfo['port']);
        fwrite($fd, $postData);
        $gets = "";
        //去除头部
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }

        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);
        return $gets;
    }

    //获取默认返回信息
    public static function getReturn($status = false) {
        return [
            'EBusinessID' => Express::EBUSINESSID,
            'UpdateTime' => date('Y-m-d H:i:d', time()),
            'Success' => $status ? true : false,
            'Reason' => self::$error
        ];
    }

    //解析回调内容
    public static function resolveData($data) {
        if ($data['DataSign'] != base64_encode(md5($data['RequestData'] . Express::EXPRESS_KEY))) {
            self::$error = 'key验证错误！';
            return false;
        }

        $callbackInfo = json_decode($data['RequestData'], true);
        if (! $callbackInfo) {
            self::$error = '返回信息错误！';
            return false;
        }
        $return = [];
        foreach($callbackInfo['Data'] as $v) {
            $success = 2;
            if (isset($v['Success']) && ! $v['Success'] ) {
                $success = 3;
            }
            $return[] = [
                'eBusinessID' => $v['EBusinessID'],
                'shipperCode' => $v['ShipperCode'],
                'logisticCode' => $v['LogisticCode'],
                'traces' => isset($v['Traces']) ? json_encode($v['Traces']) : '',
                'pushTime' => $callbackInfo['PushTime'],
                'status' => isset($v['State']) ? $v['State'] : '',
                'success' => $success,
                'reason' => isset($v['Reason']) ? $v['Reason'] : '',
            ];
        }

        return $return;
    }



}
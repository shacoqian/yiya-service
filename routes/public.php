<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 2017/8/26
 * Time: 下午12:43
 */

$app->get('/api/public/region/all', ['uses' => "PublicController@regionAll"]);

$app->get('/api/public/getRegions', [ 'uses' => "PublicController@regions"]);

//上传图片
$app->post('/api/public/image/upload', ['middleware' => ['auth'], 'uses' => "UploadController@imageUpload"]);
$app->post('/api/public/base64image/upload', ['middleware' => ['auth'], 'uses' => "UploadController@base64ImageUpload"]);


//版本升级
$app->get('/api/version', ['uses' => "PublicController@version"]);
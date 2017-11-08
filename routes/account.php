<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 2017/8/26
 * Time: 下午12:43
 */

//登录
$app->get('/api/login', ['uses' => "AccountController@login"]);

$app->post('/api/setUserInfo', ['middleware' => ['auth'], 'uses' => "AccountController@setUserInfo"]);


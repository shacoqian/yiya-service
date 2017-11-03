<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 2017/8/26
 * Time: 下午12:43
 */
//登录
$app->post('/api/login', ['middleware' => ['form:AccountFormRequest'], 'uses' => "AccountController@login"]);
$app->get('/api/loginOut', ['middleware' =>['uses' => "AccountController@loginOut"]]);

$app->get('/api/account/address/lists', ['middleware' => ['auth'], 'uses' => "AccountController@addressList"]);
$app->get('/api/account/address/default', ['middleware' => ['auth'], 'uses' => "AccountController@addressDefault"]);
$app->post('/api/account/address/add', ['middleware' => ['auth', 'form:AccountFormRequest'], 'uses' => "AccountController@addressCreate"]);
$app->post('/api/account/{id}/address/update', ['middleware' => ['auth', 'form:AccountFormRequest'], 'uses' => "AccountController@addressUpdate"]);
$app->delete('/api/account/{id}/address/delete', ['middleware' => ['auth'], 'uses' => "AccountController@addressDelete"]);
$app->put('/api/account/{id}/set/default', ['middleware' => ['auth'], 'uses' => "AccountController@setAddressDefault"]);
$app->get('/api/account/info', ['middleware' => ['auth'], 'uses' => "AccountController@customerInfo"]);


$app->post('/api/account/info/edit', ['middleware' => ['auth'], 'uses' => "AccountController@customerInfoEdit"]);


$app->post('/api/account/password/edit', ['middleware' => ['auth', 'form:AccountFormRequest'], 'uses' => "AccountController@editPassword"]);



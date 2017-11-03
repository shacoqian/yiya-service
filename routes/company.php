<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 2017/8/26
 * Time: 下午12:43
 */

$app->get('/api/company/bank/list', ['middleware' => ['auth'], 'uses' => "CompanyController@bankList"]);
$app->get('/api/company/default/bank', ['middleware' => ['auth'], 'uses' => "CompanyController@bankDefault"]);

<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 2017/8/26
 * Time: 下午12:43
 */


$app->post('/api/cart/{goods_standard_id}/add', ['middleware' => ['auth'], 'uses' => "CartController@add"]);
$app->get('/api/cart/lists', ['middleware' => ['auth'], 'uses' => "CartController@lists"]);


$app->get('/api/cart/{goods_standard_id}/edit', ['middleware' => ['auth'], 'uses' => "CartController@edit"]);
$app->get('/api/cart/{goods_standard_id}/delete', ['middleware' => ['auth'], 'uses' => "CartController@deleteByGoodsStandardId"]);
$app->get('/api/cart/clear', ['middleware' => ['auth'], 'uses' => "CartController@clear"]);

$app->get('/api/cart/number', ['middleware' => ['auth'], 'uses' => "CartController@number"]);
<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 2017/8/26
 * Time: 下午12:43
 */
//登录
$app->get('/api/goods/lists', ['middleware' => ['auth'], 'uses' => "GoodsController@lists"]);
$app->get('/api/classes', ['middleware' => ['auth'], 'uses' => "ClassController@classes"]);

$app->get('/api/goods/{good_id}/standard', ['middleware' => ['auth'], 'uses' => "GoodsController@getStandard"]);


$app->get('/api/goods/{goods_id}/detail', ['middleware' => ['auth'], 'uses' => "GoodsController@detail"]);
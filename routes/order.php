<?php
/**
 * Created by PhpStorm.
 * User: qianfeng
 * Date: 2017/8/26
 * Time: 下午12:43
 */
$app->post('/api/order/create', ['middleware' => ['auth'], 'uses' => "OrderController@create"]);
$app->get('/api/order/lists', ['middleware' => ['auth'], 'uses' => "OrderController@lists"]);
$app->get('/api/order/{order_id}/detail', ['middleware' => ['auth'], 'uses' => "OrderController@detail"]);
$app->get('/api/order/{order_id}/goods', ['middleware' => ['auth'], 'uses' => "OrderController@goods"]);

$app->post('/api/order/{order_id}/pay', ['middleware' => ['auth'], 'uses' => "OrderController@orderPay"]);

$app->get('/api/order/{order_id}/cancel', ['middleware' => ['auth'], 'uses' => "OrderController@cancel"]);

$app->get('/api/order/{order_id}/records', ['middleware' => ['auth'], 'uses' => "OrderController@records"]);

$app->get('/api/order/{order_id}/payLists', ['middleware' => ['auth'], 'uses' => "OrderController@payLists"]);

$app->get('/api/order/{order_id}/deliver', ['middleware' => ['auth'], 'uses' => "OrderController@deliver"]);

$app->get('/api/deliver/{deliver_no}/goods', ['middleware' => ['auth'], 'uses' => "OrderController@deliverGoods"]);

$app->get('/api/order/{order_deliver_id}/express', ['middleware' => ['auth'], 'uses' => "OrderController@express"]);


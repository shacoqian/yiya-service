<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$dir = __DIR__;
$files = glob("{$dir}/[!^web]*.php");
foreach ($files as $file) {
  include_once($file);
}

$app->get('/', function () use ($app) {
    return $app->version();
});

//test
$app->get('/api/default', ['middleware' => ['auth','form:TestFormRequest'], 'uses' => "DefaultController@index"]);
$app->get('/api/database', ['uses' => "DefaultController@database"]);
$app->get('/api/redis', ['uses' => "DefaultController@redis"]);



$app->get('/api/express', ['uses' => "TestController@express"]);

//test file

$app->get('/api/test', ['uses' => 'TestController@logTest']);

//swagger
$app->get('/swagger', ['uses' => "SwaggerController@json"]);

$app->post('/api/callback', ['uses' => "ExpressController@callback"]);
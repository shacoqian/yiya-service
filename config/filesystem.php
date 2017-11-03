<?php
/**
 * Created by PhpStorm.
 * User: FengQian
 * Date: 2017/8/31
 * Time: 下午3:42
 */

return [
    'images' => [
        'fileType' => [
            'jpg',
            'jpeg',
            'png'
        ],
        // 多少MB
        'size' => 2,
        'path' => '/canyou/',
        'pre_url' => env('STATIC_URL', '')
    ]

];
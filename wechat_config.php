<?php
$config = [
    'app_id' => '',
    'secret' => '',
    'token' => '',
 	'aes_key' => '',
    'response_type' => 'array',
   'log' => [
        'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
        'channels' => [
            // 测试环境
            'dev' => [
                'driver' => 'daily',
                'path' => __DIR__.'/tmp/wechat.log',
                'level' => 'debug',
            ],
            // 生产环境
            'prod' => [
                'driver' => 'daily',
                'path' =>__DIR__.'/tmp/wechat.log',
                'level' => 'info',
            ],
        ],
    ],
];

<?php
$url_dir='https://tools.ifking.cn/cross/'; //网址目录，以/结尾
$db=mysqli_connect("127.0.0.1","用户名","密码","数据库名"); 
$config = [
    'app_id' => '',
    'secret' => '',
    'token' => '',
 	'aes_key' => '',
    'response_type' => 'array',
   'log' => [
        'default' => 'prod', // 默认使用的 channel，生产环境可以改为下面的 prod
        'channels' => [
            // 测试环境
            'dev' => [
                'driver' => 'single',
                'path' => __DIR__.'/tmp/wechat.log',
                'level' => 'debug',
            ],
            // 生产环境
            'prod' => [
                'driver' => 'single',
                'path' =>__DIR__.'/tmp/wechat.log',
                'level' => 'info',
            ],
        ],
    ],
];
if (mysqli_connect_errno($db)) 
{ 
    echo "连接 MySQL 失败: " . mysqli_connect_error(); 
} 
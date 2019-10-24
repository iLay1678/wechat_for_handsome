<?php
$url_dir='https://tools.ifking.cn/cross/'; //网址目录，以/结尾
$mysql_conf = array(
    'host'    => '127.0.0.1:3306', 
    'db'      => '', 
    'db_user' => '', 
    'db_pwd'  => '', 
    );

try{
$db=new PDO("mysql:host=" . $mysql_conf['host'] . ";dbname=" . $mysql_conf['db'], $mysql_conf['db_user'], $mysql_conf['db_pwd']);
}catch(PDOException $e){
    die('数据库连接失败:' . $e->getMessage());
}
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

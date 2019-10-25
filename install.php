<?php
if(file_exists(__DIR__.'/install.lock')){
    die("已经安装过了，如要重新安装请删除install.lock");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' ){
    $host = $_POST['host'];
    $port = $_POST['port'];
    $dbuser = $_POST['dbuser'];
    $dbpass = $_POST['dbpass'];
    $dbname = $_POST['dbname'];
    $app_id=$_POST['app_id'];
    $secret=$_POST['secret'];
    $token=$_POST['token'];
    $aes_key=$_POST['aes_key'];
    $url_dir=$_POST['url_dir'];
    $config="<?php
\$mysql_conf = array(
    'host'    => '$host:$port', 
    'db'      => '$dbname', 
    'db_user' => '$dbuser', 
    'db_pwd'  => '$dbpass', 
    );
try{
\$db=new PDO('mysql:host=' . \$mysql_conf['host'] . ';dbname=' . \$mysql_conf['db'], \$mysql_conf['db_user'], \$mysql_conf['db_pwd']);
}catch(PDOException \$e){
    die('数据库连接失败:' . \$e->getMessage());
}

\$config = [
    'app_id' => '$app_id',
    'secret' => '$secret',
    'token' => '$token',
 	'aes_key' => '$aes_key',
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
];";
$sql="DROP TABLE IF EXISTS `cross`;
CREATE TABLE `cross`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `timecode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `cid` int(11) NULL DEFAULT NULL,
  `mid` int(11) NULL DEFAULT NULL,
  `msg_type` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;";
    try{
$db=new PDO("mysql:host=" . $host.":" .$port. ";dbname=" . $dbname, $dbuser, $dbpass);
if($db->query($sql)){
    file_put_contents(__DIR__.'/config.php',$config);
    file_put_contents(__DIR__.'/install.lock','');
    die("1");
}
}catch(PDOException $e){
    die('数据库连接失败:' ."mysql:host=" . $host.":" .$port. ";dbname=" . $dbname. $dbuser.$dbpass. $e->getMessage());
}
    die("参数错误");
}
?>

<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
    <title>安装</title>
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/2.1.3/weui.css">
    <script src="./zepto.min.js"></script>
</head>
<body ontouchstart="">
    <div class="page">
        <form class="weui-form" id="form">
            <div class="weui-form__text-area">
                <h2 class="weui-form__title">安装</h2>
                <div class="weui-form__desc">
                    wechat_for_handsome
                </div>
            </div>
            <div class="weui-form__control-area">
                <div class="weui-cells__group weui-cells__group_form">

                    <div class="weui-cells weui-cells_form">

                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">数据库地址</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="host" class="weui-input" placeholder="数据库地址" value="localhost">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">端口</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="port"  class="weui-input" value="3306">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">数据库用户名</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="dbuser" class="weui-input" placeholder="数据库用户名" value="">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">数据库密码</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="dbpass" class="weui-input" type="dbpass" placeholder="数据库密码" value="">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">数据库名</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="dbname" class="weui-input" placeholder="数据库名" type="" value="">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">app_id</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="app_id" class="weui-input" placeholder="公众号appid" value="">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">secret</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="secret" class="weui-input" placeholder="公众号secret" value="">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">token</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="token" class="weui-input" placeholder="公众号验证token" value="">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">aes_key</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="aes_key" class="weui-input" placeholder="公众号aes_key" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="weui-form__opr-area">
                <a href="javascript:;" class="weui-btn weui-btn_primary" id="bind">安装</a>
            </div>

            <div class="weui-form__extra-area">
                <div class="weui-footer">
                    <p class="weui-footer__links">
                        <a href="https://ifking.cn" class="weui-footer__link">我若为王</a>
                    </p>
                    <p class="weui-footer__text">
                        Copyright © 2008-2019 iLay
                    </p>
                </div>
            </div>
        </form>
        <div class="js_dialog" id="Dialog" style="opacity: 0; display: none;">
            <div class="weui-mask"></div>
            <div class="weui-dialog">
                <div class="weui-dialog__bd" id="msg">
                    提示
                </div>
                <div class="weui-dialog__ft">
                    <a href="javascript:$('#Dialog').fadeOut(200);" class="weui-dialog__btn weui-dialog__btn_primary">确定</a>
                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript">
        $(function() {
            var $toast = $('#js_toast');
            var $Dialog = $('#Dialog');
            var $msg = $('#msg');
            $('#bind').on('click', function() {
                $('#bind').addClass('weui-btn_loading');
                $.post('<?php echo  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';echo $_SERVER["HTTP_HOST"].dirname($_SERVER['SCRIPT_NAME']);?>/install.php', $('#form').serialize(), function(response) {
                    if (response == '1') {
                        $msg.html('安装成功');
                    }else {
                        $msg.html(response);
                    }
                    $Dialog.fadeIn(200);
                    $('#bind').removeClass('weui-btn_loading');
                })
            });
            function onBridgeReady() {
                WeixinJSBridge.call('hideOptionMenu');
            }

            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                    document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                }
            } else {
                onBridgeReady();
            }
        });
    </script>
</body>
</html>

<?php
include 'config.php';
$openid = isset($_GET['openid'])?$_GET['openid']:$_POST['openid'];
$arr = $db->query("SELECT * FROM `cross` WHERE openid='{$openid}'")->fetch();
$url = $arr['url'];
$cid = $arr['cid'];
$mid = $arr['mid'];
$timecode = $arr['timecode'];
$id = $arr['id'];
if ($_POST['openid']!=null):
    $url = $_POST['url'];
    $cid = $_POST['cid'];
    $mid = $_POST['mid'];
    $mid = $_POST['mid'];
    $timecode = $_POST['timecode'];
    if (!isset($id)) {
        if ($db->query("INSERT INTO `cross` (openid, url,timecode,cid,mid) VALUES ('{$openid}', '{$url}','{$timecode}', '{$cid}','{$mid}')")) {
            die('1');
        }
    } else {
        if ($db->query("update `cross` set url='$url', timecode='$timecode', cid='$cid', mid='$mid' where openid='$openid'")) {
             die('2');
        }
    }
    die();
else:
?>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
    <title>时光机绑定</title>
    <link rel="stylesheet" href="https://res.wx.qq.com/open/libs/weui/2.1.3/weui.css">
    <script src="./zepto.min.js"></script>
</head>
<body ontouchstart="">
    <div class="page">
        <form class="weui-form" id="form">
            <div class="weui-form__text-area">
                <h2 class="weui-form__title">时光机绑定</h2>
                <div class="weui-form__desc">
                    handsome主题时光机绑定
                </div>
            </div>
            <div class="weui-form__control-area">
                <div class="weui-cells__group weui-cells__group_form">

                    <div class="weui-cells weui-cells_form">

                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">网址</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="url" class="weui-input" placeholder="你的博客网址" value="<?php echo $url;
                                ?>">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">openid</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="openid" readonly class="weui-input" value="<?php echo $openid;
                                ?>">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">时光机编码</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="timecode" class="weui-input" placeholder="时光机编码" value="<?php echo $timecode;
                                ?>">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">cid</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="cid" class="weui-input" type="number" placeholder="微信绑定时光机cid" value="<?php echo $cid;
                                ?>">
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd">
                                <label class="weui-label">mid</label>
                            </div>
                            <div class="weui-cell__bd">
                                <input id="js_input" name="mid" class="weui-input" placeholder="微信绑定的分类mid:" type="number" value="<?php echo $mid;
                                ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="weui-form__opr-area">
                <a href="javascript:;" class="weui-btn weui-btn_primary" id="bind">绑定</a>
            </div>

            <div class="weui-form__extra-area">
                <div class="weui-footer">
                    <p class="weui-footer__links">
                        <p class="weui-footer__links"> <a href="https://ifking.cn/p/113.html" class="weui-footer__link">搭建教程</a>
                    </p>
                    <p class="weui-footer__text">
                         Copyright © 2019-<?php echo date("Y",time());?> iLay
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
                    <a href="javascript:$('#Dialog').fadeOut(200);" class="weui-dialog__btn weui-dialog__btn_primary">知道了</a>
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
                $.post('<?php echo  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';echo $_SERVER["HTTP_HOST"].dirname($_SERVER['SCRIPT_NAME']);?>/bind.php', $('#form').serialize(), function(response) {
                    if (response == '1') {
                        $msg.html('绑定成功');
                    } else if (response == '2') {
                        $msg.html('修改成功');
                    } else {
                        $msg.html('失败，请检查输入参数是否正确');
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
<?php endif;?>

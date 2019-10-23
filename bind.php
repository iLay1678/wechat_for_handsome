<?php
include 'config.php';
$openid = isset($_GET['openid'])?$_GET['openid']:$_POST['openid'];
$arr = $db->query("SELECT * FROM `cross` WHERE openid='{$openid}'")->fetch_array();
$url = $arr['url'];
$cid = $arr['cid'];
$mid = $arr['mid'];
$mid = $arr['mid'];
$timecode = $arr['timecode'];
$id=$arr['id'];
if (isset($_POST['url'])) {
    $url = $_POST['url'];
    $cid = $_POST['cid'];
    $mid = $_POST['mid'];
    $mid = $_POST['mid'];
    $timecode = $_POST['timecode'];
    if (!isset($id)) {
    $db->query("INSERT INTO `cross` (openid, url,timecode,cid,mid) VALUES ('{$openid}', '{$url}','{$timecode}', '{$cid}','{$mid}')");
}else{
    $db->query("update `cross` set url='$url', timecode='$timecode', cid='$cid', mid='$mid' where openid='$openid'");
}
}
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
        <form class="weui-form" action="<?php echo $url_dir;
            ?>bind.php" method="post">
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
                                <input id="js_input" name="openid" class="weui-input" value="<?php echo $openid;
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
                <input type="submit" class="weui-btn weui-btn_primary weui-btn_disabled" id="showTooltips">
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
        <div id="js_toast" style="display: none;">
            <div class="weui-mask_transparent"></div>
            <div class="weui-toast">
                <i class="weui-icon-success-no-circle weui-icon_toast"></i>
                <p class="weui-toast__content">
                    已完成
                </p>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            var $toast = $('#js_toast');
            var $input = $('#js_input');
            $input.on('input', function() {
                if ($input.val()) {
                    $('#showTooltips').removeClass('weui-btn_disabled');
                } else {
                    $('#showTooltips').addClass('weui-btn_disabled');
                }
            });
            $('#showTooltips').on('click', function() {
                if ($(this).hasClass('weui-btn_disabled')) return;
                // toptips的fixed, 如果有`animation`, `position: fixed`不生效
                $('.page.cell').removeClass('slideIn');
                $toast.fadeIn(100);
                setTimeout(function () {
                    $toast.fadeOut(100);
                }, 2000);
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
            if (isWeixin) {
                window.alert = function(name) {
                    var iframe = document.createElement("IFRAME");
                    iframe.style.display = "none";
                    iframe.setAttribute("src", 'data:text/plain,');
                    document.documentElement.appendChild(iframe);
                    window.frames[0].window.alert(name);
                    iframe.parentNode.removeChild(iframe);
                }
            }
        });
    </script>
</body>
</html>
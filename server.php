<?php
require __DIR__.'/vendor/autoload.php';
include 'config.php';
use EasyWeChat\Factory;

function request_post($url = '', $post_data = array()) {
    if (empty($url) || empty($post_data)) {
        return false;
    }

    $o = "";
    foreach ($post_data as $k => $v) {
        $o.= "$k=" . urlencode($v). "&" ;
    }
    $post_data = substr($o,0,-1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();
    //初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);
    //抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);
    //post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');
    $data = curl_exec($ch);
    //运行curl
    curl_close($ch);

    return $data;
}

function push($content,$msg_type,$url,$timecode,$cid,$mid) {
    $desp = array('cid' => $cid,'mid' => $mid, 'content' => $content,'action' => "send_talk",'time_code' => md5($timecode),'msg_type' => $msg_type,'token' => 'weixin');
    $res = request_post($url, $desp);
    return $res;
}
function curl($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_REFERER, $url);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}
function update() {
    $shasum = curl('https://raw.githubusercontent.com/iLay1678/wechat_for_handsome/master/shasum.txt');

    file_put_contents("update.log","获取最新特征库...\n", FILE_APPEND);
    file_put_contents("update.log",$shasum."\n\n", FILE_APPEND);
    $shasum = explode("\n", $shasum);
    array_pop($shasum);
    file_put_contents("update.log","开始检查本地文件...\n", FILE_APPEND);
    foreach ($shasum as $remote) {
        list($remote_sha256, $filename) = explode('  ', $remote);
        if (!file_exists(__DIR__.'/'.$filename) ||
            !hash_equals(hash('sha256', file_get_contents(__DIR__.'/'.$filename)), $remote_sha256)) {
            file_put_contents("update.log","下载     ".$filename, FILE_APPEND);
            $url = 'https://raw.githubusercontent.com/iLay1678/wechat_for_handsome/master/'.substr($filename, 1);

            if (file_put_contents(__DIR__.'/'.$filename, curl($url))) {
                file_put_contents("update.log"," (OK)\n", FILE_APPEND);
            } else {
                file_put_contents("update.log","\n下载失败，错误信息: $url\n", FILE_APPEND);
            }
        } else {
            file_put_contents("update.log","无需更新  ".$filename."\n", FILE_APPEND);
        }
    }
    file_put_contents("update.log","\n\n更新完成", FILE_APPEND);
    $result = file_get_contents("update.log");
    unlink("update.log");
    return $result;
    //die();
}
$app = Factory::officialAccount($config);
$app->server->push(function ($message) {
    include 'config.php';
    $http_type=((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
    $openid = $message['FromUserName'];
    $arr = $db->query("SELECT * FROM `cross` WHERE openid='{$openid}'")->fetch();
    $url = $arr['url'];
    $cid = $arr['cid'];
    $mid = $arr['mid'];
    $timecode = $arr['timecode'];
    $content = $arr['content'];
    switch ($message['Content']) {
        case 'openid':
            return $message['FromUserName'];
            break;
        case "更新":
            return update();
            break;
        case "绑定":
            if (isset($url)) {
                return "<a href='".$http_type.$_SERVER["HTTP_HOST"].dirname($_SERVER['SCRIPT_NAME'])."/bind.php?openid=$openid'>您已绑定，点击查看或修改</a>";
            } else {
                return "<a href='".$http_type.$_SERVER["HTTP_HOST"].dirname($_SERVER['SCRIPT_NAME'])."/bind.php?openid=$openid'>点击绑定</a>";
            }
            break;
        case "解除绑定":case "解绑":
            if ($db->query("DELETE FROM `cross` WHERE openid='{$openid}'")) {
                return "已经解除绑定";
            } else {
                return "操作失败，未知错误";
            }
            break;
        case "帮助":
            return '1.发送 绑定 进行绑定或修改绑定信息
2.向时光机发送消息
支持文字、图片、地理位置、链接四种消息类型。
其他消息类型等后续开发，暂不支持（如果发送了，会提示不支持该类型的，如语音消息）。
如果发送的是图片会自动将图片存放到typecho 的 usr/uploads/time 目录下。
支持发送私密说说。只需要在发送内容前加入#即可。 举例发送：#这是私密的说说，仅发送者可见。
连续发送多条信息
发送【开始】，开始一轮连续发送
发送【结束】，结束当前轮的发送
3.发送文章
输入【发文章】，开始文章发送，支持多条消息，支持多条消息图文混合
输入【结束】，结束文章发送
4.其他操作
发送 博客收到你的博客地址的链接
发送 发博客收到发博文的字的链接
发送 解除绑定 或 解绑 可删除掉你的绑定信息
发送 帮助 查看帮助信息
5.<a href=\'https://handsome2.ihewro.com/#/wechat?id=向时光机发送消息\'>图文教程</a>';
            break;
        default:
            if (isset($timecode)) {
                switch ($message['Content']) {
                    case '文章':
                        return '<a href=\''.$url.'/admin/write-post.php\'>发布文章</a>';
                        break;
                    case "博客":
                        return '<a href=\''.$url.'\'>打开博客</a>';
                        break;
                    default:
                        switch ($message['Content']) {
                            case '发文章':
                                $msg_type = 'mixed_post';
                                $db->query("update `cross` set msg_type='$msg_type',content='' where openid='$openid'");
                                return "开启博文构造模式，请继续发送消息，下面的消息最后将组成一篇完整的文章发送到博客，发送『结束』结束本次发送，发送『取消』取消本次发送~";
                                break;
                            case "取消":
                                $db->query("update `cross` set msg_type='',content='' where openid='$openid'");
                                return "已取消发送";
                                break;    
                            case "开始":
                                $msg_type = 'mixed_talk';
                                $db->query("update `cross` set msg_type='$msg_type',content='' where openid='$openid'");
                                return "当前处于混合消息模式，请继续，发送『结束』结束本次发送，发送『取消』取消本次发送~";
                                break;
                            case "结束":
                                $arr = $db->query("SELECT * FROM `cross` WHERE openid='{$openid}'")->fetch();
                                $str = $arr['content'];
                                if($str==null){
                                    $db->query("update `cross` set msg_type='',content='' where openid='$openid'");
                                    return "已结束，本次操作未发送任何信息~";
                                    exit();
                                }
                                $msg_type = $arr['msg_type'];
                                $arr = mb_split('@',$str);
                                $m = count($arr);
                                for ($i = 0;$i < $m-1;$i++) {
                                    $con[$i] = mb_split('->',$arr[$i]);
                                }
                                $m1 = count($con);
                                for ($m = 0;$m < $m1;$m++) {
                                    $result[$m] = array('type' => $con[$m][0],'content' => $con[$m][1]);
                                }
                                $content = array('results' => $result);
                                $status = push(json_encode($content),$msg_type,$url,$timecode,$cid,$mid);
                                $db->query("update `cross` set msg_type='',content='' where openid='$openid'");
                                switch ($status) {
                                    case "1":
                                        return "biubiubiu~发送成功";
                                        break;
                                    case "-3":
                                        return "身份验证失败";
                                        break;
                                    default:
                                        return $status;
                                }
                                break;
                            default:
                                $arr = $db->query("SELECT * FROM `cross` WHERE openid='{$openid}'")->fetch();
                                $buffer = $arr['content'];
                                $type = $arr['msg_type'];
                                switch ($message['MsgType']) {
                                    case "location":
                                        $content = $message['Location_X'] . "#" . $message['Location_Y'] . "#" . $message['Label'] . "#http://restapi.amap.com/v3/staticmap?location=" . $message['Location_Y'] . "," . $message['Location_X'] . "&zoom=10&size=750*300&markers=mid,,A:" . $message['Location_Y'] . "," . $message['Location_X'] . "&key=2a5048a9ad453654e037b6a68abd13c4";
                                        $msg_type = "location";
                                        if ($type == 'mixed_talk' || $type == 'mixed_post') {
                                            $content = $buffer.$msg_type."->".$content."@";
                                        }
                                        $db->query("update `cross` set content='$content' where openid='$openid'");
                                        break;
                                    case "image":
                                        $content = $message['PicUrl'];
                                        $msg_type = "image";
                                        if ($type == 'mixed_talk' || $type == 'mixed_post') {
                                            $content = $buffer.$msg_type."->".$content."@";
                                        }
                                        $db->query("update `cross` set content='$content' where openid='$openid'");
                                        break;
                                    case "link":
                                        $content = $message['Title']."#".$message['Description']."#".$message['Url'];
                                        $msg_type = "link";
                                        if ($type == 'mixed_talk' || $type == 'mixed_post') {
                                            $content = $buffer.$msg_type."->".$content."@";
                                        }
                                        $db->query("update `cross` set content='$content' where openid='$openid'");
                                        break;
                                    case "text":
                                        if (substr($message['Content'],0,1) == "#") {
                                            $content = '[secret]'.substr($message['Content'],1).'[/secret]';
                                        } else {
                                            $content = $message['Content'];
                                        }
                                        $msg_type = "text";
                                        if ($type == 'mixed_talk' || $type == 'mixed_post') {
                                            $content = $buffer.$msg_type."->".$content."@";
                                        }
                                        $db->query("update `cross` set content='$content' where openid='$openid'");
                                        break;
                                    default:
                                        return "不支持的消息类型";
                                        exit();
                                }
                                $arr = $db->query("SELECT * FROM `cross` WHERE openid='{$openid}'")->fetch();
                                $content = $arr['content'];
                                $type = $arr['msg_type'];
                                switch ($type) {
                                    case 'mixed_post':case 'mixed_talk':
                                        return "请继续，发送『结束』结束本次发送，发送『取消』取消本次发送~";
                                        break;
                                    default:
                                        $status = push($content,$msg_type,$url,$timecode,$cid,$mid);
                                        $db->query("update `cross` set msg_type='',content='' where openid='$openid'");
                                        switch ($status) {
                                            case "1":
                                                return "biubiubiu~发送成功";
                                                break;
                                            case "-1":
                                                return "请求参数错误";
                                                break;
                                            case "-2":
                                                return "信息缺失";
                                                break;
                                            case "-3":
                                                return "身份验证失败";
                                                break;
                                            default:
                                                return $status;
                                        }
                                }
                        }
                }
            } else {
                return "<a href='".$http_type.$_SERVER["HTTP_HOST"].dirname($_SERVER['SCRIPT_NAME'])."/bind.php?openid=$openid'>您还未绑定，点击绑定</a>";
            }
    }

});

$response = $app->server->serve();

// 将响应输出
$response->send();

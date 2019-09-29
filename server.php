<?php 
require __DIR__.'/vendor/autoload.php';
require_once 'Config.class.php'; 
include 'wechat_config.php';
use EasyWeChat\Factory;

 function request_post($url = '', $post_data = array()) {
        if (empty($url) || empty($post_data)) {
            return false;
        }
        
        $o = "";
        foreach ( $post_data as $k => $v ) 
        { 
            $o.= "$k=" . urlencode( $v ). "&" ;
        }
        $post_data = substr($o,0,-1);  

        $postUrl = $url;
        $curlPost = $post_data;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }

function push($content,$msg_type) {	
  		$C = new Config('config'); 
 		$url = $C->get('url', '');
        $desp = array('cid' => $C->get('cid', ''),'mid' => $C->get('mid', ''), 'content' => $content,'action'=>"send_talk",'time_code'=>md5($C->get('time_code', '')),'msg_type'=>$msg_type,'token' => 'weixin');
        $res = request_post($url, $desp);
        return $res;
}
 function curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_REFERER, $url);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
 function update() {$shasum = curl('https://raw.githubusercontent.com/iLay1678/wechat_for_handsome/master/shasum.txt');

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
 file_put_contents("update.log","\n\n更新完成\n\n支持绑定多个微信号，支持发布文章\n\n请重新修改配置文件", FILE_APPEND); 
$result= file_get_contents("update.log");
unlink("update.log");
return $result;
        //die();
 }
$app = Factory::officialAccount($config);
$app->server->push(function ($message) {
   $C = new Config('config'); 
  if($message['Content'] == "openid"){
  return $message['FromUserName'];
  }if($message['Content'] == "更新"){
  return update();
  }
 if($message['Content'] == "帮助"){
  return '<a href=\'https://handsome2.ihewro.com/#/wechat?id=向时光机发送消息\'>打开帮助</a>';
  }
  if($message['Content'] == "文章"){
  return '<a href=\''.$C->get('url', '').'/admin/write-post.php\'>发布文章</a>';
  }
  if($message['Content'] == "博客"){
  return '<a href=\''.$C->get('url', '').'\'>打开博客</a>';
  }
  if($message['Content'] == "文章"){
  return '<a href=\''.$C->get('url', '').'/admin/write-post.php\'>发布文章</a>';
  }
 if(in_array($message['FromUserName'],$C->get('openid', '0'))){
 $C = new Config('config'); 
 if ($message['Content'] == "开始"||$C->get('flag', '0')==1) {
    $status = "当前处于混合消息模式，请继续，发送『结束』结束本次发送~";
    $C->set('flag', '1');  
   $C->set('ok', '0');
    $C->save(); 
    } 
 if ($message['Content'] == "发文章"||$C->get('flag', '0')==1) {
    $status = "当前处于发送文章模式，请继续，发送『完成』即可发布文章~";
    $C->set('flag', '1');  
   $C->set('ok', '0');
    $C->save(); 
    }
  if ($message['Content'] == "结束") {
     $C->set('flag', '2');
    $C->set('ok', '1');
     $C->set('content', '');
     $C->save(); 
    }
      if ($message['Content'] == "完成") {
     $C->set('flag', '3');
    $C->set('ok', '1');
     $C->set('content', '');
     $C->save(); 
    }
  $flag= $C->get('flag', '2'); 
  if($flag==2){
   $msg_type = "mixed_talk";
    $C->set('content', '');
    $C->set('flag', '0');
    $C->set('msg_type', $msg_type);
    $C->save();    
  }
   if($flag==3){
   $msg_type = "mixed_post";
    $C->set('content', '');
    $C->set('flag', '0');
    $C->set('msg_type', $msg_type);
    $C->save();    
  }
  if($flag==1){
        if ($message['MsgType'] == "location") {
            $content = $message['Location_X'] . "#" . $message['Location_Y'] . "#" . $message['Label'] . "#http://restapi.amap.com/v3/staticmap?location=" . $message['Location_Y'] . "," . $message['Location_X'] . "&zoom=10&size=750*300&markers=mid,,A:" . $message['Location_Y'] . "," . $message['Location_X'] . "&key=2a5048a9ad453654e037b6a68abd13c4";
        }
        if ($message['MsgType'] == "image") {
            $content = $message['PicUrl'];
        }
     if ($message['MsgType'] == "link") {
            $content = $message['Title']."#".$message['Description']."#".$message['Url'];
        }
        if ($message['MsgType'] == "text") {
          if(substr($message['Content'],0,1)=="#")
          {
           $content = '[secret]'.substr($message['Content'],1).'[/secret]';
          }else{
           $content = $message['Content'];
          }   
        }
    $buffer=$message['MsgType']."->".$content."@";
   file_put_contents("content.txt", $buffer, FILE_APPEND);   
  }
  if($flag==0) {
        $msg_type = $message['MsgType'];
        if ($msg_type == "location") {
            $content = $message['Location_X'] . "#" . $message['Location_Y'] . "#" . $message['Label'] . "#http://restapi.amap.com/v3/staticmap?location=" . $message['Location_Y'] . "," . $message['Location_X'] . "&zoom=10&size=750*300&markers=mid,,A:" . $message['Location_Y'] . "," . $message['Location_X'] . "&key=2a5048a9ad453654e037b6a68abd13c4";
        }
        if ($msg_type == "image") {
            $content = $message['PicUrl'];
        }
    if ($message['MsgType'] == "link") {
            $content = $message['Title']."#".$message['Description']."#".$message['Url'];
        }
        if ($msg_type == "text") {
             if(substr($message['Content'],0,1)=="#")
          {
           $content = '[secret]'.substr($message['Content'],1).'[/secret]';
          }else{
           $content = $message['Content'];
          }   
        }
    $C->set('content', $content);
    $C->set('msg_type', $msg_type);
    $C->save(); 
    }
  if($C->get('ok', '0')==1){
    if(file_exists("content.txt")){
     $file_path = "content.txt";
if(file_exists($file_path)){
$str = file_get_contents($file_path);//将整个文件内容读入到一个字符串中
$str = str_replace("text->开始@","",$str);
$str = str_replace("text->发文章@","",$str);
$arr=mb_split('@',$str);
$m=count($arr);
 for($i=0;$i<$m-1;$i++){
 $con[$i]=mb_split('->',$arr[$i]); 
 }
$m1=count($con);
 for($m=0;$m<$m1;$m++){
 $result[$m]=array('type'=>$con[$m][0],'content'=>$con[$m][1]);
 }
  $content=array('results'=>$result);
} 
    $content=json_encode($content);
    unlink('content.txt');
    }else{
    $content=$C->get('content', '');  
    }
    $status = push($content , $C->get('msg_type', ''));
  }
    if ($status == "1") {
        $status = "biubiubiu~发送成功";
    }
    return $status;
 }else{
  return "没有操作权限！";
 }
});

$response = $app->server->serve();

// 将响应输出
$response->send();

# 微信公众号服务端 for handsome主题

## 项目地址

[https://github.com/iLay1678/wechat_for_handsome][1]

## 微信公众号服务端 for handsome主题
## *本项目需要配合handsome主题的时光机使用*
## 更新日志

2019.4.18 初始版本

2019.4.19 添加私密说说功能，使用方法同官方，添加链接消息支持，添加更新功能。可以通过发送‘更新’到公众号更新，也可以通过 网址/update.php 更新 

2019.9.29 修复5.31版本混合消息发送bug，修复ua显示错误问题,支持绑定多个微信，支持发布文章

## 环境需求

PHP >= 7.1

PHP cURL 扩展

PHP OpenSSL 扩展

PHP SimpleXML 扩展

PHP fileinfo 拓展

## 食用方法
 1、拷贝项目到你的服务器
 
2、修改wechat_config.php文件中的app_id、secret、token、aes_key 相关信息自行从微信公众号后台获取

3、在公众号后台服务器配置填写服务器地址为 项目所在网址/server.php 并启用服务器配置

4、 修改config.php文件中的url,cid,time_code

|   字段    |         说明          |
| --------- | --------------------- |
| url       | 你的博客地址           |
| cid       | 时光机页面的cid，不要加引号，直接填数字        |
|mid        |要发布文章的分类mid，不要加引号，直接填数字 |
| time_code | 时光机验证编码 |

5、向公众号发送消息试试，是不是提示 ‘没有操作权限’ 不要紧，请看下一步
 
6、设置管理员openid，防止其他关注者调用
openid位于config.php中的openid字段，获取方法：向你的公众号发送openid,即可收到你的openid


 配置完成，尽情使用吧！使用方法同handsome官方时光机发送公众号


  [1]: https://github.com/iLay1678/wechat_for_handsome

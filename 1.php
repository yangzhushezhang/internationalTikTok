<?php
header("Content-Type: text/html; charset=utf-8");
function Post($data, $target) {
    $url_info = parse_url($target);
    $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
    $httpheader .= "Host:" . $url_info['host'] . "\r\n";
    $httpheader .= "Content-Type:application/x-www-form-urlencoded\r\n";
    $httpheader .= "Content-Length:" . strlen($data) . "\r\n";
    $httpheader .= "Connection:close\r\n\r\n";
    //$httpheader .= "Connection:Keep-Alive\r\n\r\n";
    $httpheader .= $data;

    $fd = fsockopen($url_info['host'], 80);
    fwrite($fd, $httpheader);
    $gets = "";
    while(!feof($fd)) {
        $gets .= fread($fd, 128);
    }
    fclose($fd);
    return $gets;
}

$target = "http://sms.106jiekou.com/utf8/sms.aspx";
//替换成自己的测试账号,参数顺序和wenservice对应
$post_data = "account=wangyi&password=wy011620.&mobile=17314937586&content=".rawurlencode("您的订单编码：4557。如需帮助请联系客服。");

echo $gets = Post($post_data, $target);

//采用UTF-8编码,要将文件另存为UTF-8格式
//请自己解析$gets字符串并实现自己的逻辑
//100 表示成功,其它的参考文档

//100 发送成功
//101 验证失败 102 手机号码格式不正确
//103 会员级别不够
//104 内容未审核
//105 内容过多或无合适匹配通道
//106 账户余额不足
//107 Ip受限
//108 手机号码发送太频繁，请换号或隔天再发
//109 帐号被锁定
//110 手机号发送频率持续过高，黑名单屏蔽数日
//120 系统升级
?>

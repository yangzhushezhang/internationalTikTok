<?php


require 'autoload.php';


use EasySwoole\HttpClient\HttpClient;

try {
    $client = new  HttpClient("http://8.136.97.179:9999/index.php?mod=taskboard&op=list&tbid=1");


    $res = $client->get();


    var_dump($res->getBody());
} catch (\EasySwoole\HttpClient\Exception\InvalidUrl $e) {
}//实例化Htpp客户端

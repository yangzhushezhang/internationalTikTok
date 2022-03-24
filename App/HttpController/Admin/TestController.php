<?php


namespace App\HttpController\Admin;


use EasySwoole\RedisPool\RedisPool;

class TestController extends Base
{


    function GetBaPrice()
    {
        $client = new \EasySwoole\HttpClient\HttpClient("https://api1.binance.com/api/v3/ticker/price?symbol=ETHUSDT");
        $client->setTimeout(5);
        $client->setConnectTimeout(10);
        $result = $client->get();
        $result = $result->getBody();
//        if (json_decode($result, true)) {
//            $data = json_decode($result, true);
//            $redis = RedisPool::defer('redis');
//            $this->writeJson(200, [], $data['price']);
//        }

        $this->response()->write($result);


    }
}
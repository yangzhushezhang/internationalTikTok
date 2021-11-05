<?php


namespace App\HttpController\Coroutines;


use App\HttpController\Model\ConfigModel;
use App\HttpController\Model\CwCollectModel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;

class Cw_Check extends AbstractProcess
{


    protected function run($arg)
    {


        $Telegram_token = "";
        $Telegram_chartId = "";
        go(function () use ($Telegram_chartId, $Telegram_token) {

            while (true) {

                DbManager::getInstance()->invoke(function ($client) use ($Telegram_token,$Telegram_chartId){
                    $max_time = CwCollectModel::invoke($client)->max('created_at');
                    if (time() - $max_time > 3600) {  # 一个小时没有 数据  发送通知
                        $text = "时间:" . date("Y/m/d H:i:s", time()) . "   宝宝采集程序已经出现了异常,请前去检查";
                        $url = "https://api.telegram.org/bot" . $Telegram_token . "/sendMessage?chat_id=" . $Telegram_chartId . "&text=" . $text;
                        $client = new \EasySwoole\HttpClient\HttpClient($url);//实例化Htpp客户端
                        $response = $client->get();

                    }

                });

                \co::sleep(600);   # 没三秒检查一次是否有新的用户进来!
            }

        });
    }
}
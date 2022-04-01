<?php


namespace App\HttpController\Process;


use App\HttpController\Model\AdminModel;
use App\HttpController\Model\FansTModel;
use App\HttpController\Model\UidTModel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;

class SetUidTProcess extends AbstractProcess
{


    protected function run($arg)
    {
        go(function () {

            while (true) {
                DbManager::getInstance()->invoke(function ($client) {

                    $res = UidTModel::invoke($client)->all(['uid' => 0]);
                    $one = AdminModel::invoke($client)->get(['id' => 1]);
                    if ($res) {
                        foreach ($res as $re) {

                            try {
                                $headers = [
                                    'authority' => 'www.tiktok.com',
                                    'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="98", "Google Chrome";v="98"',
                                    'sec-ch-ua-mobile' => '?0',
                                    'sec-ch-ua-platform' => '"macOS"',
                                    'upgrade-insecure-requests' => '1',
                                    'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.109 Safari/537.36',
                                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                                    'sec-fetch-site' => 'none',
                                    'sec-fetch-mode' => 'navigate',
                                    'sec-fetch-user' => '?1',
                                    'sec-fetch-dest' => 'document',
                                    'accept-language' => 'zh-CN,zh;q=0.9',
                                    'cookie' => $one['cookies'],
                                ];
                                $client1 = new \EasySwoole\HttpClient\HttpClient();
                                $client1->setTimeout(10);
                                $client1->setConnectTimeout(10);
                                $client1->setHeaders($headers);
                                $client1->setUrl("https://www.tiktok.com/" . $re['username']);
                                $result = $client1->get();
                                $html = $result->getBody();
                                preg_match("/u=(\d+)/", $html, $matches);
                                if ($matches[1]) {
                                    //更新入库
                                    UidTModel::invoke($client)->where(['id' => $re['id']])->update(['uid' => $matches[1]]);
                                } else {
                                    var_dump("用户名:" . $re['username']);
                                    var_dump("没有采集到 uid");
                                }
                            } catch (\Throwable $exception) {
                                var_dump($exception->getMessage());
                            }

                        }
                    }

                });
                \co::sleep(600);

            }

        });
    }
}
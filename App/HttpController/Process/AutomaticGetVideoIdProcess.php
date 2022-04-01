<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitorVideoModel;
use App\Log\LogHandel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\RedisPool;

/***
 * Class AutomaticGetVideoIdProcess
 * @package App\HttpController\Process
 *   获取链接 视频 id   ()   --   手机采集上来的 链接 不一定全部或能 获取视频的 id  所有这个是 查漏的进程
 */
class AutomaticGetVideoIdProcess extends AbstractProcess
{
    protected function run($arg)
    {
        var_dump("AutomaticGetVideoIdProcess 进程启动...");
        go(function () {
            while (true) {
                \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
                    $data = $redis->lPop("AutomaticGetVideoIdProcess");
                    if ($data) {
                        $re = json_decode($data, true);
                        DbManager::getInstance()->invoke(function ($client) use ($re) {
                            $vID = $this->GetUidFormURL($re['url']);
                            if ($vID) {
                                $one = MonitorVideoModel::invoke($client)->get(['vID' => $vID]);
                                if ($one) {
                                    MonitorVideoModel::invoke($client)->destroy(['id' => $re['id']]);
                                } else {
                                    MonitorVideoModel::invoke($client)->where(['id' => $re['id']])->update(['vID' => $vID]);
                                }
                            }
                        });
                        \co::sleep(1); # 一小时执行一次
                    }
                }, "redis");
            }
        });





    }

    function GetUidFormURL($url)
    {

        $log = new LogHandel();
        try {

//            var_dump($url);
            if (!$url || $url == "" || empty($url)) {
                return false;
            }
            $url = trim($url);
            #new 一个对象
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            #禁止重定向
            $client->enableFollowLocation(0);
            #正则 id
            $pattern = "/\d{19}/";
            $response = $client->get();
            if (!$response) {
                var_dump($response);
                $log->log('获取抖音的url id 失败 $response 返回为false');
                return false;
            }


            $data = $response->getBody();
            if (!$data) {
                var_dump($data);
                $log->log('获取抖音的url id 失败 $data 返回为false');
                return false;
            }
            preg_match($pattern, $data, $match);
            if (!isset($match[0])) {
                $log->log('获取抖音的url id 失败');
                return false;
            }
            return $match[0];
        } catch (\Throwable $exception) {
            $log->log('获取抖音的url id 异常' . $exception);
            var_dump($exception);
            return false;
        }


    }
}
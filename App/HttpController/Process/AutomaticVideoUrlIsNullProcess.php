<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitorVideoModel;
use App\HttpController\Task\GetVideoReleaseTimeTask;
use App\Log\LogHandel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\RedisPool;


/**
 * Class AutomaticVideoUrlIsNullProcess
 * @package App\HttpController\Process
 * 进程 检查  视频链接 是 评论时间  --
 */
class AutomaticVideoUrlIsNullProcess extends AbstractProcess
{


    protected function run($arg)
    {
        go(function () {
            var_dump('AutomaticVideoUrlIsNullProcess 进程启动...');
            while (true) {
                try {
                    \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
                        $data = $redis->lPop("AutomaticVideoUrlIsNullProcess");
                        if ($data) {
                            $re = json_decode($data, true);
                            DbManager::getInstance()->invoke(function ($client) use ($re) {
                                if (!empty($re['up_name'])) {
                                    $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
                                    $task->async(new GetVideoReleaseTimeTask(['id' => $re['id'], 'vid' => $re['vID'], 'username' => $re['up_name']]));
                                } else {
                                    $content = $this->GetUidFormURL($re['url']);
                                    if ($content && count($content) == 3) {
                                        MonitorVideoModel::invoke($client)->where(['id' => $re['id']])->update(['up_name' => $content[1]]);
                                        $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
                                        $task->async(new GetVideoReleaseTimeTask(['id' => $re['id'], 'vid' => $re['vID'], 'username' => $content[1]]));
                                    }
                                }
                            });
                            \co::sleep(1);
                        }
                    }, "redis");
                } catch (\Throwable $e) {
                    var_dump("AutomaticVideoUrlIsNullProcess 进程 异常:" . $e->getMessage());
                }
                \co::sleep(60);  //4 秒一个号 分钟进行一次采集
            }

        });





    }


    function GetUidFormURL($url)
    {

        $log = new LogHandel();
        try {
            if (!$url || $url == "" || empty($url)) {
                return false;
            }
            $url = trim($url);
            #new 一个对象
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            #禁止重定向
            $client->enableFollowLocation(0);
            #正则 id
            $pattern = "/(@.*)\/video\/(\d{19})/";
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

//            var_dump($match);

            if (!isset($match[0])) {
                $log->log('获取抖音的url id 失败');
                return false;
            }
            return $match;
        } catch (\Throwable $exception) {
            $log->log('获取抖音的url id 异常' . $exception);
            var_dump($exception);
            return false;
        }


    }
}
<?php


namespace App\HttpController\Process;


use App\HttpController\Model\CookiesModel;
use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitorVideoModel;
use App\HttpController\Task\AutomaticFanCollectionTask;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\Redis\Exception\RedisException;
use EasySwoole\RedisPool\RedisPool;

/**
 * Class AutomaticFanCollectionProcess
 * @package App\HttpController\Process
 * 自动采集粉丝 进程   多个服务器 共享   --
 */
class AutomaticFanCollectionProcess extends AbstractProcess
{

    protected function run($arg)
    {
        var_dump("AutomaticFanCollectionProcess 进程启动...");
        go(function () {
            while (true) {
                try {
                    #选择进程数量
                    \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
                        $processNum = AutomaticFanCollectionProcessNum;
                        for ($i = 0; $i < $processNum; $i++) {
                            $data = $redis->lPop("AutomaticFanCollectionProcess");
                            if ($data) {
                                #开启异步处理 采集粉丝
                                $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
                                $task->async(new AutomaticFanCollectionTask(json_decode($data, true)));
                                \co::sleep(1);
                            }
                        }
                    }, 'redis');
                    \co::sleep(60);
                } catch (\Throwable $exception) {
                    var_dump("进程 AutomaticFanCollectionProcess 抛出异常:" . $exception->getMessage());
                }
            }
        });
    }

}
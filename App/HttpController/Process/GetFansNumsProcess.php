<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Task\GetFansNumsTask;
use App\HttpController\Task\GetVideoReleaseTimeTask;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\RedisPool;

/**
 * Class GetFansNumsProcess
 * @package App\HttpController\Process
 *    对 粉丝的 详情补充
 */
class GetFansNumsProcess extends AbstractProcess
{
    protected function run($arg)
    {
        var_dump("GetFansNumsProcess 正在运行");
        go(function () {
            while (true) {
                \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
                    $data = $redis->lPop("GetFansNumsProcess");
                    $data=false;
                    if ($data) {
                        $re = json_decode($data, true);
                        DbManager::getInstance()->invoke(function ($client) use ($redis, $re) {
                            #对数据进行 查询
                            $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
                            $task->async(new GetFansNumsTask(['id' => $re['id'], 'username' => $re['unique_id']]));
                        });
                    }
                }, "redis");
                \co::sleep(5);  //20秒采集一
            }
        });



    }


}
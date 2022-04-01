<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Task\GetFansNumsTask;
use App\HttpController\Task\GetVideoReleaseTimeTask;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\RedisPool;

class GetFansNumsProcess extends AbstractProcess
{

    protected function run($arg)
    {
        var_dump("GetFansNumsProcess 正在运行");
        go(function () {
            while (true) {
                \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
                    DbManager::getInstance()->invoke(function ($client) use ($redis) {
                        $res = MonitorFansModel::invoke($client)->limit(5)->all(['status' => 0, 'fans_nums' => -1]);
                        if ($res) {
                            foreach ($res as $re) {

                                #判断  这条数据是否有其他人在使用
//                                if ($redis->get("FANS_NUM_" . $res['uid'])) {
//                                    continue;
//                                }
//                                $redis->set("FANS_NUM_" . $res['uid'], "status");  #  写进redis 代表我已经在使用了
                                #对数据进行 查询
                                $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
                                $task->async(new GetFansNumsTask(['id' => $re['id'], 'username' => $re['unique_id']]));
                            }
                        }
                    });
                }, "redis");
                \co::sleep(5);  //20秒采集一
            }
        });
    }
}
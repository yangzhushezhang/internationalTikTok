<?php


namespace App\HttpController\Process;


use App\HttpController\Model\DyUidModel;
use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitorVideoModel;
use App\HttpController\Task\ClearFansDatasTask;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\RedisPool;

class Init extends AbstractProcess
{

    protected function run($arg)
    {
        var_dump("初始化进程");
        go(function () {
            var_dump("AutomaticGetVideoIdProcess 初始化进程启动...");
            while (true) {
                DbManager::getInstance()->invoke(function ($client) {
                    $res = MonitorVideoModel::invoke($client)->limit(10000)->where('vID', NULL, 'IS')->all();
                    if ($res) {
                        $redis = RedisPool::defer('redis');
                        foreach ($res as $re) {
                            $redis->rPush("AutomaticGetVideoIdProcess", json_encode($re->toRawArray()));
                        }
                    }
                });
                \co::sleep(15 * 60); # 一小时执行一次
            }
        });
        go(function () {
            var_dump("GetFansNumsProcess 初始化进程启动...");
            while (true) {
                DbManager::getInstance()->invoke(function ($client) {
                    $res = MonitorFansModel::invoke($client)->limit(10000)->all(['status' => 0, 'fans_nums' => -1]);
                    if ($res) {
                        $redis = RedisPool::defer('redis');
                        foreach ($res as $re) {
                            $redis->rPush("GetFansNumsProcess", json_encode($re->toRawArray()));
                        }
                    }
                });
                \co::sleep(15 * 60); # 半小时
            }
        });
        go(function () {
            var_dump("AutomaticVideoUrlIsNullProcess 初始化进程启动...");
            while (true) {
                DbManager::getInstance()->invoke(function ($client) {
                    $res = MonitorVideoModel::invoke($client)->limit(10000)->where('release_time', NULL, 'IS')->all();
                    if ($res) {
                        $redis = RedisPool::defer('redis');
                        foreach ($res as $re) {
                            $redis->rPush("AutomaticVideoUrlIsNullProcess", json_encode($re->toRawArray()));
                        }
                    }
                });
                \co::sleep(15 * 60); # 一小时执行一次
            }
        });
        go(function () {
            DbManager::getInstance()->invoke(function ($client) {
                $res = MonitorVideoModel::invoke($client)->where(['status' => 6])->all();
                if ($res) {
                    $redis = RedisPool::defer('redis');
                    foreach ($res as $re) {
                        $redis->rPush("AutomaticFanCollectionProcess", json_encode($re->toRawArray()));
                        $res = MonitorVideoModel::invoke($client)->where(['id' => $re['id']])->update(['status' => 3]);;
                    }
                }
            });
        });


        go(function () {
            var_dump("清除Fans * 开始");
            while (true) {
                $redis = RedisPool::defer('redis');
                $cursor = 0;
                //每次迭代都会设置一次$cursor,为0代表迭代完成
                $keys = $redis->scan($cursor, 'Fans_*', 1000);
                if ($keys) {
                    if ($keys && count($keys) > 0) {
                        foreach ($keys as $key) {
                            $data_array = explode("_", $key);
                            $res = DyUidModel::create()->get(['uid' => $data_array[1]]);
                            if (!$res) {
                                DyUidModel::create()->data(['uid' => $data_array[1]])->save();
                            }
                            $redis->del($key);
                        }
                    }
                }
                \co::sleep(0.1); # 一小时执行一次
            }
        });

    }
}
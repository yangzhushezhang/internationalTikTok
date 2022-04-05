<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitorVideoModel;
use App\HttpController\Task\RecognizeFacesTask;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\DbManager;
use EasySwoole\Redis\Exception\RedisException;
use EasySwoole\RedisPool\RedisPool;

/**
 * Class FaceRecognitionProcess
 * @package App\HttpController\Process
 *
 * 人脸识别进程
 */
class FaceRecognitionProcess extends AbstractProcess
{
    protected function run($arg)
    {
        var_dump("FaceRecognitionProcess 进程启动");
        go(function () {
            while (true) {
                DbManager::getInstance()->invoke(function ($client) {
                    $res = MonitorFansModel::invoke($client)->where('sex', NULL, 'IS')->limit(FaceRecognitionProcessNum)->all(['ifuse' => 1]);   // 10 可以修改  标识多少并发
                    if ($res) {
                        $redis = RedisPool::defer('redis');
                        foreach ($res as $re) {
                            #修改使用的状态
                            MonitorFansModel::invoke($client)->where(['id' => $re['id']])->update(['ifuse' => 2]);
                            if (!$redis->hExists("FaceRecognitionTotal", Date("Y-m-d", time()))) {
                                $redis->hSet("FaceRecognitionTotal", Date("Y-m-d", time()), 1);
                            } else {
                                $count = $redis->hGet("FaceRecognitionTotal", Date("Y-m-d", time()));
                                $redis->hSet("FaceRecognitionTotal", Date("Y-m-d", time()), $count + 1);
                            }
                            $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
                            $task->async(new RecognizeFacesTask(['id' => $re['id'], 'image_url' => $re['image_url']]));

                        }
                    }
                });
                \co::sleep(1);
            }
        });


    }


}
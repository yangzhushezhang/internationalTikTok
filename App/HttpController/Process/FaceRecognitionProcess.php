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
                    $res = MonitorFansModel::invoke($client)->where('sex', NULL, 'IS')->limit(2)->all();   // 10 可以修改  标识多少并发
                    if ($res) {
                        foreach ($res as $re) {
                            $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
//                            投递异步任务
                            $task->async(new RecognizeFacesTask(['id' => $re['id'], 'image_url' => $re['image_url']]));



                        }
                    }
                });
                \co::sleep(1);
            }
        });

    }


}
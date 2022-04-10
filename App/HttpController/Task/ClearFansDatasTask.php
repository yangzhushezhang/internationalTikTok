<?php


namespace App\HttpController\Task;


use App\HttpController\Model\DyName;
use App\HttpController\Model\DyUidModel;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Task\AbstractInterface\TaskInterface;
use TencentCloud\Cdb\V20170320\Models\VerifyRootAccountRequest;

class ClearFansDatasTask implements TaskInterface
{

    function run(int $taskId, int $workerIndex)
    {
        try {
            // TODO: Implement run() method.
            $redis = RedisPool::defer('redis');
            $cursor = 0;
            $data = [];
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
        } catch (\Throwable $exception) {
            var_dump($exception->getMessage());
        }
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }
}
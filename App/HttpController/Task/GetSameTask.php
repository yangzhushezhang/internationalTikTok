<?php


namespace App\HttpController\Task;


use EasySwoole\Task\AbstractInterface\TaskInterface;

class GetSameTask implements TaskInterface
{

    function run(int $taskId, int $workerIndex)
    {








    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }
}
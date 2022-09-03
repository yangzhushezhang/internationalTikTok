<?php


namespace App\HttpController\Coroutines;


use App\HttpController\Model\ConfigModel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;

class Heartbeat extends AbstractProcess
{

    protected function run($arg)
    {
        go(function () {
   
            while (true) {
                DbManager::getInstance()->invoke(function ($client) {
                    $RES = ConfigModel::invoke($client)->all(['status' => 1]);
                    if ($RES) {
                        foreach ($RES as $RE) {
                            if (time()-$RE['update_at']>30){  #里上次心跳 时间有 30秒的 断链
                                ConfigModel::invoke($client)->where(['id' => $RE['id']])->update(['status' => 0]);
                            }

                        }
                    }
                });

                \co::sleep(3);   # 没三秒检查一次是否有新的用户进来!
            }

        });
    }


}
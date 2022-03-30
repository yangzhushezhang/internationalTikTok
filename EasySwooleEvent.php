<?php


namespace EasySwoole\EasySwoole;


use App\HttpController\Admin\Login;
use App\Log\LogHandel;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\Exception\Exception;
use EasySwoole\RedisPool\RedisPoolException;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        \EasySwoole\EasySwoole\Logger::getInstance(new \App\Log\LogHandel());  //日志初始化
        // 实现 onRequest 事件
        \EasySwoole\Component\Di::getInstance()->set(\EasySwoole\EasySwoole\SysConst::HTTP_GLOBAL_ON_REQUEST, function (\EasySwoole\Http\Request $request, \EasySwoole\Http\Response $response): bool {

            ###### 处理请求的跨域问题 ######
            $response->withHeader('Access-Control-Allow-Origin', '*');
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');
            $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            if ($request->getMethod() === 'OPTIONS') {
                $response->withStatus(\EasySwoole\Http\Message\Status::CODE_OK);
                return false;
            }
            return true;
        });
    }

    public static function mainServerCreate(EventRegister $register)
    {
        #注册进程
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('Heartbeat');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Coroutines\Heartbeat($processConfig))->getProcess());


        #注册mysql连接池
        $mysql_config = new Config(\EasySwoole\EasySwoole\Config::getInstance()->getConf('MYSQL'));
        DbManager::getInstance()->addConnection(new Connection($mysql_config));

        //    #注册redis连接池
        $redis_config = new \EasySwoole\Redis\Config\RedisConfig(\EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS'));
        try {
            \EasySwoole\RedisPool\RedisPool::getInstance()->register($redis_config, 'redis');
        } catch (Exception $e) {
        } catch (RedisPoolException $e) {
        }


//        #注册进程
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('GetGroupTestA');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Telegram\GetGroupTestA($processConfig))->getProcess());
//
//
//        #注册进程
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('GetGroupTestB');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Telegram\GetGroupTestB($processConfig))->getProcess());


        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess1($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess2($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess3($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess4($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess5($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess6($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess7($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess8($processConfig))->getProcess());
//        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('Shopping1'); 
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ShoppingProcess9($processConfig))->getProcess());
//        #注册进程 1

        /**
         * CwAddressProcess.php
         */
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('CwAddressProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\CwAddressProcess($processConfig))->getProcess());
//
//
//        //        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('GetSameProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\GetSameProcess($processConfig))->getProcess());
//
////        #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('RecentlySoldProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\RecentlySoldProcess($processConfig))->getProcess());
//                #注册进程 1
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('SearchProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\SearchProcess($processConfig))->getProcess());




//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('GetUidProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\GetUidProcess($processConfig))->getProcess());
//
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('SetUidTProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\SetUidTProcess($processConfig))->getProcess());

//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('AutomaticVideoCaptureProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\AutomaticVideoCaptureProcess($processConfig))->getProcess());
//
//




        ///////////


//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('AutomaticFanCollectionProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\AutomaticFanCollectionProcess($processConfig))->getProcess());
//        //AutomaticGetVideoIdProcess
//         $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('AutomaticGetVideoIdProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\AutomaticGetVideoIdProcess($processConfig))->getProcess());
//        //FaceRecognitionProcess
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('FaceRecognitionProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\FaceRecognitionProcess($processConfig))->getProcess());
//        //AutomaticVideoUrlIsNullProcess
//        $processConfig = new \EasySwoole\Component\Process\Config();
//        $processConfig->setProcessName('AutomaticVideoUrlIsNullProcess');
//        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\AutomaticVideoUrlIsNullProcess($processConfig))->getProcess());

    }
}
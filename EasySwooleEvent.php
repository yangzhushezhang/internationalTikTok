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

        /***
         * Init.php (主服务器进程 只允许 主服务器开----)  ###
         */
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('Init');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\Init($processConfig))->getProcess());

        /**
         * FaceRecognitionProcess  (人脸是被进程--  只允许 主服务器开----) ###
         */
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('FaceRecognitionProcess');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\FaceRecognitionProcess($processConfig))->getProcess());


        /**
         *  最关键进程  采集粉丝    #多个服务器开启
         */
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('AutomaticFanCollectionProcess');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\AutomaticFanCollectionProcess($processConfig))->getProcess());


        /**
         * AutomaticGetVideoIdProcess  (查漏进程 补充视频id)  #多个服务器开
         */
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('AutomaticGetVideoIdProcess');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\AutomaticGetVideoIdProcess($processConfig))->getProcess());

        /**
         * AutomaticVideoUrlIsNullProcess (获取视频的发布时间--暂时这样 )     #多个服务器开
         */
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('AutomaticVideoUrlIsNullProcess');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\AutomaticVideoUrlIsNullProcess($processConfig))->getProcess());
        /***
         * GetFansNumsProcess (粉丝详情补充进程)  #多个服务器开
         */
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('GetFansNumsProcess');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\GetFansNumsProcess($processConfig))->getProcess());


        /**
         * ClearFansProcess  (粉丝清除功能 ---) ###主服务器开
         */
        $processConfig = new \EasySwoole\Component\Process\Config();
        $processConfig->setProcessName('ClearFansProcess');
        ServerManager::getInstance()->getSwooleServer()->addProcess((new \App\HttpController\Process\ClearFansProcess($processConfig))->getProcess());
    }
}
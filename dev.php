<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'reload_async' => true,
            'max_wait_time'=>3,
            'document_root'=>EASYSWOOLE_ROOT.'/Static/',
            'enable_static_handler' => true,
        ],
        'TASK'=>[
            'workerNum'=>4,
            'maxRunningNum'=>128,
            'timeout'=>15
        ]
    ],

    'MYSQL' => [
        'host'          => '127.0.0.1',
        'port'          => '3306',
        'user'          => 'test_tool',
        'timeout'       => '5',
        'charset'       => 'utf8',
        'password'      => 'nSRLSf6zdzehfXiL',
        'database'      => 'test_tool',
        'fetch_mode'    => false,//开启fetch模式, 可与pdo一样使用fetch/fetchAll逐行或获取全部结果集(4.0版本以上)
        'POOL_MAX_NUM'  => '20',
        'POOL_TIME_OUT' => '0.1',
    ],
    #redis配置
    'REDIS' => [
        //数据库配置
        'host'                 => '127.0.0.1',//redis地址
        'port'                 => 6379,//redis端口
        'auth'                 => 'wy011620.',//数据库密码
        'db'                   => null,//哪个数据库
        'timeout'              => 5,//超时时间
        'reconnectTimes'       => 5,//重连次数
    ],

    'TEMP_DIR' => null,
    'LOG_DIR' => null
];

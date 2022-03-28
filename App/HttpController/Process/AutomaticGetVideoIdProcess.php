<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitorVideoModel;
use App\Log\LogHandel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;

/***
 * Class AutomaticGetVideoIdProcess
 * @package App\HttpController\Process
 *   获取链接 视频 id
 */
class AutomaticGetVideoIdProcess extends AbstractProcess
{


    protected function run($arg)
    {
        go(function () {
            while (true) {
                DbManager::getInstance()->invoke(function ($client) {
                    $res = MonitorVideoModel::invoke($client)->where('vID', NULL, 'IS')->all();
                    if ($res) {
                        foreach ($res as $re) {
                            $vID = $this->GetUidFormURL($re['url']);
                            if ($vID) {
                                //视频id 暂时别写
                                //查看这个视频 url 是否存在了
//                                $add['vID'] = $content;;
                                //判断这个 id 是否存在
                                $one = MonitorVideoModel::invoke($client)->get(['vID' => $vID]);
                                if ($one) {
                                    MonitorVideoModel::invoke($client)->destroy(['id' => $re['id']]);
                                } else {
                                    MonitorVideoModel::invoke($client)->where(['id' => $re['id']])->update(['vID' => $vID]);
                                }
                            }
                            \co::sleep(4);  //4 秒一个号 分钟进行一次采集
                        }
                    }

                });

                \co::sleep(60);  //10 分钟进行一次采集

            }

        });
    }


    function GetUidFormURL($url)
    {

        $log = new LogHandel();
        try {

//            var_dump($url);
            if (!$url || $url == "" || empty($url)) {
                return false;
            }
            $url = trim($url);
            #new 一个对象
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            #禁止重定向
            $client->enableFollowLocation(0);
            #正则 id
            $pattern = "/\d{19}/";
            $response = $client->get();
            if (!$response) {
                var_dump($response);
                $log->log('获取抖音的url id 失败 $response 返回为false');
                return false;
            }


            $data = $response->getBody();
            if (!$data) {
                var_dump($data);
                $log->log('获取抖音的url id 失败 $data 返回为false');
                return false;
            }
            preg_match($pattern, $data, $match);
            if (!isset($match[0])) {
                $log->log('获取抖音的url id 失败');
                return false;
            }
            return $match[0];
        } catch (\Throwable $exception) {
            $log->log('获取抖音的url id 异常' . $exception);
            var_dump($exception);
            return false;
        }


    }
}
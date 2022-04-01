<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitoriktokupnameModel;
use App\HttpController\Model\MonitorVideoModel;
use App\HttpController\Task\GetVideoReleaseTimeTask;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Bridge\DefaultCommand\Process;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use TencentCloud\Ocr\V20181119\Models\FormulaOCRRequest;

/**
 * Class AutomaticVideoCaptureProcess
 * @package App\HttpController\Process
 *
 *
 * 获取  up 主的 视频 然后 对最大的获取日期   ==(废弃)
 */
class AutomaticVideoCaptureProcess extends AbstractProcess
{


    protected function run($arg)
    {


        go(function () {

            try {
                $res = MonitoriktokupnameModel::create()->all(['status' => 1]);
                if ($res) {
                    foreach ($res as $re) {
                        $array_headers = [
                            'authority' => 'www.tiktok.com',
                            'cache-control' => 'max-age=0',
                            'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
                            'sec-ch-ua-mobile' => '?0',
                            'sec-ch-ua-platform' => '"macOS"',
                            'upgrade-insecure-requests' => '1',
                            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.74 Safari/537.36',
                            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                            'sec-fetch-site' => 'none',
                            'sec-fetch-mode' => 'navigate',
                            'sec-fetch-user' => '?1',
                            'sec-fetch-dest' => 'document',
                            'accept-language' => 'zh-CN,zh;q=0.9',
                        ];
                        $c = new \EasySwoole\HttpClient\HttpClient("https://www.tiktok.com/" . $re['username']);
                        $c->setHeaders($array_headers);
                        $c->setTimeout(5);
                        $c->setConnectTimeout(10);
                        $data = $c->get();
                        $content = $data->getBody();
                        //正则 uid    "id":"(\d{19})"
                        //   preg_match_all('/{"users":{"\S+":{"id":"(\d+)"/', $content, $uid);   //正则视频 id
                        //  var_dump($uid);
                        if (empty($re['uid'])) {
                            $isMatched = preg_match('/{"users":{"\S+":{"id":"(\d+)"/', $content, $matches);
                            if ($isMatched > 0) {
                                $uid = $matches[1];
                                MonitoriktokupnameModel::create()->where(['id' => $re['id']])->update(['uid' => $uid]);
                            }
                        }
                        preg_match_all('/{"list":([\s\S])*?]/', $content, $pat_array);   //正则视频 id
                        $MAX_Vid = 0;
                        if (isset($pat_array[0][0]) && !empty($pat_array[0][0])) {
                            preg_match_all('/(\d{19})/', $pat_array[0][0], $pp);
                            if (isset($pp[0])) {
                                foreach ($pp[0] as $item) {
                                    //先进行入库操作
//                                    $one = MonitorVideoModel::create()->get(['vID' => $item, 'up_id' => $re['id']]);
//                                    if (!$one) {
//                                        MonitorVideoModel::create()->data([
//                                            'up_id' => $re['id'],
//                                            'vID' => $item,
//                                            'created' => time()
//                                        ])->save();
//                                    }
                                    if ($item > $MAX_Vid) {
                                        $MAX_Vid = $item;
                                    }
//                                    var_dump($item);
                                }
                                if ($MAX_Vid != 0) {
                                    //推入到  异步任务进行操作
                                    //判断库里面是否存在
                                    $one = MonitorVideoModel::create()->get(['vID' => $MAX_Vid, 'up_id' => $re['id']]);
                                    if (!$one) {
                                        MonitorVideoModel::create()->data([
                                            'up_id' => $re['id'],
                                            'vID' => $MAX_Vid,
                                            'created' => time(),
                                            'status' => 3
                                        ])->save();
                                    }

//                                    $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();
//                                    // 投递异步任务
//                                    $task->async(new GetVideoReleaseTimeTask(['up_id' => $re['id'], 'vid' => $MAX_Vid, 'username' => $re['username']]));
                                }
                                var_dump("max is " . $MAX_Vid);
                            }

                        }

                    }
                }


            } catch (\Throwable $e) {
                var_dump($e->getMessage());
            }
        });

    }


}
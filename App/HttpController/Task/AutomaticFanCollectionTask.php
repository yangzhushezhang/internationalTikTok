<?php


namespace App\HttpController\Task;


use App\HttpController\Model\CookiesModel;
use App\HttpController\Model\JournalModel;
use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitorVideoModel;
use App\Log\LogHandel;
use EasySwoole\Log\Logger;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Task\AbstractInterface\TaskInterface;
use TencentCloud\Cdb\V20170320\Models\VerifyRootAccountRequest;


/***
 * Class AutomaticFanCollectionTask
 * @package App\HttpController\Task
 * 采集粉丝的  任务
 */
class AutomaticFanCollectionTask implements TaskInterface
{
    protected $data;

    public function __construct($data)
    {
        // 保存投递过来的数据
        $this->data = $data;
    }

    function run(int $taskId, int $workerIndex)
    {
        try {
            #修改 视频链接的状态
            MonitorVideoModel::create()->where(['id' => $this->data['id']])->update(['status' => 6]);# 正在使用中
            (new JournalModel())->Add(DEVICE, 1, "获取了视频链接", '', $this->data['vID']);
            $two = CookiesModel::create()->order('updated', 'ASC')->get(['status' => 1]);
            $redis = RedisPool::defer("redis");
            var_dump("准备采集:" . $this->data['vID']);
            if ($two) {
                CookiesModel::create()->where(['id' => $two['id']])->update(['updated' => time()]); //更新时间
                #更改 视频的的状态
                $headers = [
                    'authority' => 'www.tiktok.com',
                    # 'sec-ch-ua': '" Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
                    'sec-ch-ua-mobile' => '?0',
                    'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.74 Safari/537.36',
                    'sec-ch-ua-platform' => '"macOS"',
                    'accept' => '*/*',
                    'sec-fetch-site' => 'same-origin',
                    'sec-fetch-mode' => 'cors',
                    'sec-fetch-dest' => 'empty',
                    'referer' => 'https://www.tiktok.com/' . $this->data['up_name'] . '/video/' . $this->data['vID'],
                    'accept-language' => 'zh-CN,zh;q=0.9',
                    'cookie' => $two['cookies']
                ];
                $start = 0;
                $total = 0;
                for ($i = 0; $i < 2000; $i++) {  #10000/50
                    $url = "https://www.tiktok.com/api/comment/list/?aid=1988&app_language=ja-JP&app_name=tiktok_web&aweme_id=" . $this->data['vID'] . "&battery_info=1&browser_language=zh-CN&browser_name=Mozilla&browser_online=true&browser_platform=MacIntel&browser_version=5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_15_7%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F99.0.4844.74%20Safari%2F537.36&channel=tiktok_web&cookie_enabled=true&count=50&current_region=JP&cursor=" . $start . "&device_id=7077097017748997678&device_platform=web_pc&focus_state=false&fromWeb=1&from_page=video&history_len=1&is_fullscreen=false&is_page_visible=true&os=mac&priority_region=&referer=https%3A%2F%2Fwww.tiktok.com%2F%40petro_s&region=SG&root_referer=https%3A%2F%2Fwww.tiktok.com%2F%40petro_s&screen_height=1440&screen_width=2560&tz_name=Asia%2FShanghai&webcast_language=zh-Hant-TW&msToken=267gYAEscp85gdlXHOD-e3_hVs84jr5U0CmLpVt6feDTxZabFu08Pv6Q-Mnc5nKCJOileI4H3cVxFF42JCrUcggXAX4ocTeStFKk2OHHDJoJUOglCY-pn3lZ3EMM7WgqcamDErwQr1IEPtysFw==&X-Bogus=DFSzswVu9c2ANyTfSRo8p5KMtake&_signature=_02B4Z6wo00001fcFofAAAIDACUndw9ejtNH3BaVAAB.-d9";
                    $c = new \EasySwoole\HttpClient\HttpClient($url);
                    $c->setHeaders($headers);
                    $c->setTimeout(5);
                    $c->setConnectTimeout(10);
                    $data = $c->get();
                    $content = $data->getBody();
                    if ($json_data = json_decode($content, true)) {
                        #获取  总评论数
                        if (isset($json_data['total'])) {
                            $total = $json_data['total'];
                        }
                        if ($json_data['comments'] && count($json_data['comments']) > 0) {
                            foreach ($json_data['comments'] as $comment) {
                                var_dump("视频id:" . $this->data['vID'] . " 采集到粉丝:" . $comment['user']['uid']);
                                //插入粉丝
                                $add = [
                                    'unique_id' => '@' . $comment['user']['unique_id'],
                                    'uid' => $comment['user']['uid'],
                                    'send_text' => $comment['text'],
                                    'image_url' => $comment['user']['avatar_thumb']['url_list'][0],
                                    'created' => time(),
                                    'video_id' => $this->data['id'],
                                    'comment_time' => $comment['create_time'],
                                    'country' => $this->data['country']
                                ];
                                if ($redis->get("Fans_" . $comment['user']['uid'])) {  #粉丝重复
                                    continue;
                                }
                                $redis->set("Fans_" . $comment['user']['uid'], "status");
                                $one = MonitorFansModel::create()->get(['uid' => $comment['user']['uid']]);
                                if (!$one) {
                                    $res = MonitorFansModel::create()->data($add)->save();
                                    (new JournalModel())->Add(DEVICE, 2, "采集粉丝入库", $uid = $add['uid']);
//                                    $redis->rPush("GetFansNumsProcess", json_encode($add));  #  对粉丝补充详情
//                                    $redis->rPush("FaceRecognitionProcess", json_encode($add));  #对粉丝人脸识别
                                }
                            }
                        } else { #已经没有粉丝了 跳出循环
                            var_dump("视频 id:" . $this->data['vID'] . "采集完毕");
                            break;
                        }
                    }
                    \co::sleep(10);  //10秒采集一次
                    $start = $start + 50;
                }
                MonitorVideoModel::create()->where(['id' => $this->data['id']])->update(['status' => 4, 'updated' => time(), 'end' => $start, 'commit_num' => $total]);#   返还链接
                (new JournalModel())->Add(DEVICE, 1, "链接使用完毕", '', $vid = $this->data['vID']);
            } else { #没有获取到 cookie  把链接 返还
                $redis->rPush("AutomaticFanCollectionProcess", json_encode($this->data));
                MonitorVideoModel::create()->where(['id' => $this->data['id']])->update(['status' => 3]);#   返还链接
                (new JournalModel())->Add(DEVICE, 1, "链接返还,没有获取到cookie", '', $vid = $this->data['vID']);
            }
        } catch (\Throwable $exception) {
//            var_dump($exception->getMessage());
            //异常 修改
            (new JournalModel())->Add(DEVICE, 1, "异常:".$exception->getMessage(), '', $vid = $this->data['vID']);
            MonitorVideoModel::create()->where(['id' => $this->data['id']])->update(['status' => 3]);#   使用完毕
        }
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }
}
<?php


namespace App\HttpController\Process;


use App\HttpController\Model\CookiesModel;
use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitorVideoModel;
use EasySwoole\Component\Process\AbstractProcess;

/**
 * Class AutomaticFanCollectionProcess
 * @package App\HttpController\Process
 * 自动采集粉丝 进程
 */
class AutomaticFanCollectionProcess extends AbstractProcess
{

    protected function run($arg)
    {
        go(function () {
            while (true) {
                //获取可以采集的视频id
                $res = MonitorVideoModel::create()->all(['status' => 3]);
                if ($res) {
                    foreach ($res as $re) {  #  遍历每个 视频 id
                        //随机获取 cookie
                        $two = CookiesModel::create()->order('updated', 'ASC')->get(['status' => 1]);
                        if ($two) {
                            CookiesModel::create()->where(['id' => $two['id']])->update(['updated' => time()]); //更新时间
                            $start = 0;
                            for ($i = 0; $i < 2000; $i++) {  #10000/50
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
                                    'referer' => 'https://www.tiktok.com/' . $re['username'] . '/video/' . $re['vID'],
                                    'accept-language' => 'zh-CN,zh;q=0.9',
                                    # Requests sorts cookies= alphabetically
                                    'cookie' => $two['cookies']
                                    # 'cookie' => 'tt_csrf_token=nbn6kVkJCvS_24VdNeThsxqN; csrf_session_id=5a07e75b39433498d186561f2c3511c4; cookie-consent={%22ga%22:true%2C%22af%22:true%2C%22fbp%22:true%2C%22lip%22:true%2C%22bing%22:true%2C%22version%22:%22v5%22}; __tea_cache_tokens_1988={%22_type_%22:%22default%22}; bm_sz=B983CCEF70CD23B7B2B2E991A18DAB04~YAAQF2o+F+v5Rbx/AQAAMODpvg/RvnYBhHePO6OhcZg1ONIFqPVNg5l48kr1fkcKZOiP9188MYJ7xzMfNbyWgTtkLmZgLTSKI0I4vPlqSDy9uVxR3b11BPON8Pp5RKgxtgGZxSn8TulCYE9+Prb/PxYSVa64HVXMWBwmU6GqjxPAAOK+CNQWmMufQ1Kia9PRp5tbmNfabzzFShPbEDYoKvnaRUufBpqy5oP4oTlpgQYRD4eqdOtPvwtcc/MaISei5NUmBlPhr5p3KZFgzxxUiWw0mMPEcgC0F6/YCIaWLaF+Ohc=~3356741~3556404; ak_bmsc=283D2292A940686BA9AE104BC7FC6BDD~000000000000000000000000000000~YAAQF0dYaBEsTpp/AQAAD25bvw/gSJGQkxcGyptWqDfK3jk37/tzS5PSOycG8Sy4D5mM8mHOHeL731ZZsxwwHR4s8oeVTOJvmLx7kkHd89JSVa7c6Pvtq4p4Q+rQeFZjB8PgApMzdiQpZW1gtu7VzWUkwM7lHMVMT8yJNCmnu6AUQyc9sit1PJxmRvJZS+xR4JLOeqI0qRjqvOj1axxhv8PYQJ7MAF2PJPCYZj05FcYm5Cd4nqtnVg6UCTp57c3n4xg5KsMEm2gdVW9BIi0adr02JHWNB9Pom9TbsXLlBv1sAy6wh2T5ypHrIyLkE5RDZ4cUQqkF4ptBy85k/2ZfSn/oqyYr4H4o0wx1UpilDk2EMQ2j+9Rt4aW8Y69S+RggUto30Y/TiI502Q==; passport_csrf_token=cdfb0811382dd2bac5532cb7dc4b9931; passport_csrf_token_default=cdfb0811382dd2bac5532cb7dc4b9931; cmpl_token=AgQQAPO8F-RO0rID388Zuh07-Fd_Dnh4_6w3YMC_Yg; passport_auth_status=8f0778b78d3b0ca7e4a9bf2833362630%2C; passport_auth_status_ss=8f0778b78d3b0ca7e4a9bf2833362630%2C; sid_guard=4abb0169fcb57da3b634f75c259fb83f%7C1648183119%7C5184000%7CTue%2C+24-May-2022+04%3A38%3A39+GMT; uid_tt=2174a6432932d966f31e4a1bafc44d02dde63e1fcc54a25896fad5ae505f9076; uid_tt_ss=2174a6432932d966f31e4a1bafc44d02dde63e1fcc54a25896fad5ae505f9076; sid_tt=4abb0169fcb57da3b634f75c259fb83f; sessionid=4abb0169fcb57da3b634f75c259fb83f; sessionid_ss=4abb0169fcb57da3b634f75c259fb83f; sid_ucp_v1=1.0.0-KDgwMTY5MWM3OWMyODg4YmM2NDY3M2MwODZlNWVkMTA5YjFmNWE2MmEKHwiCiKOMia3MnmIQz471kQYYswsgDDDOjvWRBjgIQBIQARoDc2cxIiA0YWJiMDE2OWZjYjU3ZGEzYjYzNGY3NWMyNTlmYjgzZg; ssid_ucp_v1=1.0.0-KDgwMTY5MWM3OWMyODg4YmM2NDY3M2MwODZlNWVkMTA5YjFmNWE2MmEKHwiCiKOMia3MnmIQz471kQYYswsgDDDOjvWRBjgIQBIQARoDc2cxIiA0YWJiMDE2OWZjYjU3ZGEzYjYzNGY3NWMyNTlmYjgzZg; store-country-code=sg; tt-target-idc=alisg; _abck=610234D4A02DC9771BA6110073B69735~-1~YAAQHkdYaJvwy5x/AQAAHo5evwe6rubtpDmhJU6n9T0MouHkdGILtZpZHIKhiJ9/V9Hbdahywj7KVFB3K9dn2rwfmbpt6w8JnBuyP9g0L2uO0fDxTGQzJ3olKdNFA72iEt0Mw8WfEMSw6EcWWemQTB9V+eeyLAia++sIyC6flhWq1gVkgoyhhgna3XRDX8i8/ehrzVxBlXxvgCFWiVCqrQySu9TH5AHTiEN2zaqhvojfz9oqVFoGan6BPc4+DG7teuaFbHhyrtMBj4J6Sok/Z9EZ1Ihh/oQj3HbGrq4sAIcJUBJ2Ex0/nMMzeeZRrAGTdKzi9jPcVXxCHK7KTvSiTcLDVs8Urkvu9Qqg4ecwaUM7zIEH2KMXhKuv+qe+5IbXU2mjZixlTDzu0Q==~-1~-1~-1; store-idc=alisg; passport_fe_beating_status=true; bm_mi=F2A8A05A1253C2755920644DE1DB9D39~wCt2Nf30ECukmMASVHdAQaWgOk/GHZLo/dy3RHFatFBtFwWWrJZEObNwnLzfRj6MEAxGVKxsKjKsAMs7UBTb/lrInCw7oiIaX8qGTMzZA3ymLetPTXJxjRGk+fTWSchtCvBKyEmzWnGeRkPtDwESlHQY+tfFFsSUMgSbWmp4/m7c/us6Lq+mHW45vwBsJfK7S+HeFTrJD8D+nV7rhcLfuHjCFHUCRY/rzU1GQOyE/vvtW5Jh+6Z4PTgyyEs52wSq7v5lN1PLiCFkxHgGcJ/ibg==; odin_tt=3d41b8d002f0cfec08d97b7d196fd86e292af2fc6426f5b86416a18374318c959727a2183742cc04df7435edcacfaf6a719e5426d842314517937cfb2cb16c5b503b7d9a7662b9d4fbc55b67902f952c; ttwid=1%7CzW57WULSR0LEefk6MpEg6V7T7D6S5eryxLAmeQy-ttM%7C1648183379%7Cddc97e1e95d0c6888a126f3481d04a8c464950bda3c19d5af9ca86fbc5b9ed77; msToken=GdlpRQ4eRDQyoVk3E4pSAhCpdSW3VbDek2K6wGHeO6hv43kqcqaHs27_fSrrScIswMNFW6pwCDjrbEeWDaWLXxb_0iDkjQCDNvz6g2vLqxwErq5EhpXge3iLqscaUQ5ZY1ddSgykz-o2Kvk3CQ==; msToken=267gYAEscp85gdlXHOD-e3_hVs84jr5U0CmLpVt6feDTxZabFu08Pv6Q-Mnc5nKCJOileI4H3cVxFF42JCrUcggXAX4ocTeStFKk2OHHDJoJUOglCY-pn3lZ3EMM7WgqcamDErwQr1IEPtysFw==; bm_sv=202264FE3F0BD5C1B2130A47FACC622C~WeJT46gzyS4E94+X/vXFnQvdFZisCvU8XpjxUpXtNRIua4LHxBE05JoOJHeUP0956UXaZkumZ1hF0nRZJM1b/zqEP8Qhp0Lnw9k8Nv/BOGXgJjkk2+Lh+zBmkhOdbbUHlXYj2LbloKQ2Qwe6xUG79xP6dMNO1GBxTegMuuoP8Yk=',
                                ];
                                $url = "https://www.tiktok.com/api/comment/list/?aid=1988&app_language=ja-JP&app_name=tiktok_web&aweme_id=" . $re['vID'] . "&battery_info=1&browser_language=zh-CN&browser_name=Mozilla&browser_online=true&browser_platform=MacIntel&browser_version=5.0%20%28Macintosh%3B%20Intel%20Mac%20OS%20X%2010_15_7%29%20AppleWebKit%2F537.36%20%28KHTML%2C%20like%20Gecko%29%20Chrome%2F99.0.4844.74%20Safari%2F537.36&channel=tiktok_web&cookie_enabled=true&count=50&current_region=JP&cursor=" . $start . "&device_id=7077097017748997678&device_platform=web_pc&focus_state=false&fromWeb=1&from_page=video&history_len=1&is_fullscreen=false&is_page_visible=true&os=mac&priority_region=&referer=https%3A%2F%2Fwww.tiktok.com%2F%40petro_s&region=SG&root_referer=https%3A%2F%2Fwww.tiktok.com%2F%40petro_s&screen_height=1440&screen_width=2560&tz_name=Asia%2FShanghai&webcast_language=zh-Hant-TW&msToken=267gYAEscp85gdlXHOD-e3_hVs84jr5U0CmLpVt6feDTxZabFu08Pv6Q-Mnc5nKCJOileI4H3cVxFF42JCrUcggXAX4ocTeStFKk2OHHDJoJUOglCY-pn3lZ3EMM7WgqcamDErwQr1IEPtysFw==&X-Bogus=DFSzswVu9c2ANyTfSRo8p5KMtake&_signature=_02B4Z6wo00001fcFofAAAIDACUndw9ejtNH3BaVAAB.-d9";
                                $c = new \EasySwoole\HttpClient\HttpClient($url);
                                $c->setHeaders($headers);
                                $c->setTimeout(5);
                                $c->setConnectTimeout(10);
                                $data = $c->get();
                                $content = $data->getBody();
                                if ($json_data = json_decode($content, true)) {
                                    if ($json_data['comments'] && count($json_data['comments']) > 0) {
                                        foreach ($json_data['comments'] as $comment) {
                                            try {
                                                var_dump($comment['user']['uid']);  //7078881136253059886  //7078722429883187973  //7078722369800110854  //7005399972766925830
                                                //插入粉丝
                                                $add = [
                                                    'unique_id' => '@' . $comment['user']['unique_id'],
                                                    'uid' => $comment['user']['uid'],
                                                    'send_text' => $comment['text'],
                                                    'image_url' => $comment['user']['avatar_thumb']['url_list'][0],
                                                    'created' => time(),
                                                    'video_id' => $re['id']
                                                ];

                                                $one = MonitorFansModel::create()->get(['uid' => $comment['user']['uid']]);
                                                if (!$one) {
                                                    $res = MonitorFansModel::create()->data($add)->save();
                                                }
                                            } catch (\Throwable $exception) {
                                                var_dump($exception->getMessage());
                                            }
                                        }
                                    } else {  # 评论结束  记录下评论的start 位置
                                        var_dump("结束");   //修改 视频的 状态
                                        MonitorVideoModel::create()->where(['id' => $re['id']])->update(['updated' => time(), 'status' => 4, 'end' => $start]);
                                        break;
                                    }
                                } else {
                                    var_dump("????");
                                }
                                \co::sleep(20);  //20秒采集一次
                                $start = $start + 50;
                            }
                            MonitorVideoModel::create()->where(['id' => $re['id']])->update(['updated' => time(), 'status' => 4, 'end' => $start]);
                            //每个视频之间  停留 一分钟
                            \co::sleep(60);
                        } else {
                            //这里需要 通知 管理员员 cookie  已经 用玩了
                            var_dump("cookie 已经用完了");

                        }
                    }

                }
                \co::sleep(60);  //10 分钟进行一次采集
            }
        });
    }



}
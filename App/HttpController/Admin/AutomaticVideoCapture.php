<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\CookiesModel;
use App\HttpController\Model\Dy_url;
use App\HttpController\Model\JournalModel;
use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitoriktokupnameModel;
use App\HttpController\Model\MonitorVideoModel;
use App\HttpController\Model\UidTModel;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\RedisPool\RedisPool;
use TencentCloud\Tcaplusdb\V20190823\Models\IdlFileInfo;

class AutomaticVideoCapture extends Base
{


    /***
     * @return bool
     */
    function AutomaticVideoCapture()
    {
        try {


            var_dump("--");

            $this->writeJson(200, [], []);

            return false;

            $array_headers = [
                'authority' => 'www.tiktok.com',
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
                'cookie' => '_abck=E15852EC43400E94D4E6A819965A3837~-1~YAAQPXpAF7/skbp/AQAAUXeAvwf92w1UrG6snWz6NWLuxtXAV0fLvqvrDud6oTHjdlALfjDwnW8NHxg6yCzdY67ZrJu79Uo0szpaj0FfftTSwc2fF+IWFMDz4gt0Eibgxu1tvTlTEHorkCvT0Xh+ZWswfF3vTvD/k6J/Qr5nrwGyJItY7aJiqWMUiRqX5u6EyeAMa73nA7NjDxM6Qh26t6v0qNSRfjmA457mXe0UM7oPXDkZp/AKiizVn5mSNkzlRnF0ZwnUjtH52NqI8Gl6vwR+4dh6+Gt2Kq7pUTWT8Zgg47VGjpvmAxx85rB6g+B9loeFnBa6m84EakzYMNbqxFhX4l8XdVcylKUnXjSI4nBvbaILuxtxQrlc3SA=~-1~-1~-1; bm_sz=B5DAC8D18ECF26C95BAB269917ED395A~YAAQPXpAF8Hskbp/AQAAUXeAvw/rg8rGVhQUSlSiXMeR/wKo1eiUb1mGcy0KLk61xgrf8X4yz0JgOqGx0S0k9TzBLQiJK4b90YObXePpJCgNIMUIM/W9cwgIz0ihqOLboeYtPEo8iFvy8QiJjR8RdLBAKRCxh9hJl/Gxxf7bom38m+aIhwPLWyEXWVmK8pf+7XCSGeAn8ADVCZLCVrH7tE8NOCb47OZmedzt38rFYDUWR1fljrqyVzPFxkucsgUbyFkub7myyyDDTToaRXKBlu+JoKmV91N9YIDS2WSzoTdUjdA=~3684422~4338227; ak_bmsc=6E461967E83A9AC4626FE5DBD793F469~000000000000000000000000000000~YAAQPXpAF5Ltkbp/AQAAtYuAvw+dLlXRvjLAM6aveYXZw3Jc2kZnA97rZGthbkmR4hN8SOX+0ITbn8osqYjINRpRiUFAgN6rpr1uVORlwSsODFjju9AFRF892AGEaZIF6zn9/QFquNHuHbPYLXg/aUZZHAOS5KQsqq7HMzfGbGw5tJA0ANV0xbsCisNc7PjSd3NT4O3GspXL3dmlaNdJbojFK/S0Wk68QgMD2kstHMtw7waCzigdfP/zH0N5AvOSWq7DlnuXwEycwEETH9E1+QeQGOK9AbrQGTwrfwXsWvzRS+OHELygmomLQ7I/OamY8i0rHi/8eUTAjPeLBU5/RwVAAVVUYm0HS4DhSAgwkezmIrzuQhkq4klldGojCiw+95woccp+7pE/NBUvnWmllZ8VSRzyrnbS08w=; tt_csrf_token=liqtGb4xS9vlqd4hEpcv_D3F; msToken=sMlV4XQqOtyXVo1rclwtv4ffIA31v0XZZ0AoHuxBPb0NDcpZLnjt-DwrvcCkWtYQHcknEM01nVkK9fraNePmMelBUM8fLMjz4Ch9RFlihAUJ83Ot5H0RdoOBm6EWr-sH9RTGxAQen95i; bm_mi=327A30697E8CAC4DA27C76A8B2C2DA7F~Fnrqy3izVW8GxvBbFQ8aOfe5l7B0+Vs3FL+LZNe9XQxfxCQUVlFHfHEGLcU6TgoLWINmfxbodvTdnHBdpDQWkbHHuKMFK1Gk+YmvO/v/iCD9eSzO2z0eBWqBoeMbrVQfNn4lr2FyrMV8AO+Ee1pnEyD232hDXqcZC+0ob7o+M58MYuZAxMieALuckwWX8WrsxIMIC2DIAgCgCcSxf8vZxMK1ZcI7z+G734WykuYbPuGSkYLFLkFksuVDPgdfuk6shYVhvwDGjnJHXcoc8Sh2Vg==; ttwid=1%7CI670qGhRthRfIqfPMwc4EAMPcyrG8fWtC56qFM9zo5M%7C1648187296%7C2c3ce5763a8f8ab4c3afa613593bcfefaf36b7e27cdf3b14a765faa5f2041690; msToken=A4-Qoi-rIYquZEniQ29yaHK7s5qHdMtXHykhm87ejk-dNoDRrhXyz4LoimQ84do0Q7TSl06-IHhrZoLP-SYelN4XqmIFaKMN2LHacjI_R2-3UcC2PytHCarqmvcgxfV0ES2DIJUYVBU9; bm_sv=465A63A953C951DC7C6DB5BD95AB53FE~jkFSh4sm9/pjTMA99kqflRFC999/ZF7cyyDxToMSzFdLIjzT8qFOLPoLlaPBtiW7DPUrtbFccJ8dwWszKRuooCYBFt5MyItUo5YsAY00UMfBRGvhmHu6A3lZ+7SOEVbIJIfGqiAW5X8OfOetBVOkmIVA8IrKi7I1jknFRdzKHsE='
            ];
            $c = new \EasySwoole\HttpClient\HttpClient("https://www.tiktok.com/@petro_s/video/7077262444418141445");
            $c->setHeaders($array_headers);
            $c->setTimeout(5);
            $c->setConnectTimeout(10);
            $data = $c->get();
            $content = $data->getBody();
            $isMatched = preg_match('/<span> · <\/span>(.*)<\/a><\/div>/', $content, $matches);
            var_dump($matches);
            $this->response()->write($content);
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }

    }


    /***
     * @return bool|void
     *
     * 添加  需要监听的 up 主
     */
    function setmonitortiktokupname()
    {

        try {

            $action = $this->request()->getQueryParam('action');
            //添加
            if ($action == "ADD") {
                $username = $this->request()->getParsedBody('username');
                $username_array = explode("\n", $username);
                foreach ($username_array as $value) {
                    $one = MonitoriktokupnameModel::create()->get(['username' => $value]);
                    if (!$one) {
                        MonitoriktokupnameModel::create()->data([
                            'username' => $value,
                            'created' => time(),
                            'status' => 1
                        ])->save();
                    }
                }
                $this->writeJson(200, [], "添加成功");
                return;
            }
            //获取
            if ($action == "GET") {
                $page = $this->request()->getRequestParam('page');         // 当前页码
                $limit = $this->request()->getRequestParam('limit');        // 每页多少条数据
                $status = $this->request()->getRequestParam('status');
                $model = MonitoriktokupnameModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created', 'ASC');
                $list = $model->all();
                $result = $model->lastQueryResult();
                // 总条数
                $total = $result->getTotalCount();
                $return_data = [
                    "code" => 0,
                    "msg" => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data));
                return true;
            }
            //修改
            if ($action == "UPDATE") {
                $ID = $this->request()->getRequestParam('action');
                $status = $this->request()->getRequestParam('status');
                $username = $this->request()->getRequestParam('username');
                $ONE = MonitoriktokupnameModel::create()->get(['id' => $ID]);
                if (!$ONE) {
                    $this->writeJson(-101, [], "修改失败");
                    return;
                }
                $two = MonitoriktokupnameModel::create()->where(['id' => $ID])->update(['status' => $status, 'username' => $username]);
                if (!$two) {
                    $this->writeJson(-101, [], "修改失败");

                    return;
                }
                $this->writeJson(-101, [], "修改成功");
                return;
            }

            if ($action == "DEL") {
                $id = $this->request()->getQueryParam('id');
                $res = MonitoriktokupnameModel::create()->get(['id' => $id]);
                if (!$res) {
                    $this->writeJson(-101, [], "非法删除");
                    return false;
                }
                $res = MonitoriktokupnameModel::create()->destroy(['id' => $id]);
                if (!$res) {
                    $this->writeJson(-101, [], "删除失败");
                    return false;
                }
                $this->writeJson(200, [], "删除成功");
                return false;
            }


        } catch (\Throwable $exception) {
            $this->writeJson(-1, "", $exception->getMessage());
        }
    }


    /**
     * cookies 设置
     *
     */

    function SetCookiesForCollectionVideoFans()
    {

        try {
            $action = $this->request()->getRequestParam('action');
            if ($action == "GET") {
                $page = $this->request()->getRequestParam('page');         // 当前页码
                $limit = $this->request()->getRequestParam('limit');        // 每页多少条数据
                $status = $this->request()->getRequestParam('status');
                $model = CookiesModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created', 'ASC');
                $list = $model->all();  //1 是可以使用的cookie  2 cookies 失效
                $result = $model->lastQueryResult();
                // 总条数
                $total = $result->getTotalCount();
                $return_data = [
                    "code" => 0,
                    "msg" => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data));
                return true;
            }
            if ($action == "ADD") {
                $password = $this->request()->getParsedBody('password');
                $username = $this->request()->getParsedBody('username');
                $cookies = $this->request()->getParsedBody("cookies");
                $mail = $this->request()->getParsedBody('mail');
                //查找是否存在 这个账号
                $res = CookiesModel::create()->get(['username' => $username]);
                if ($res) {
                    $this->writeJson(-101, [], "不能重复添加");
                    return false;
                }
                $one = CookiesModel::create()->data(['username' => $username, 'password' => $password, 'status' => 1, 'cookies' => $cookies, 'mail' => $mail, 'created' => time()])->save();
                if (!$one) {
                    $this->writeJson(-101, [], "添加失败");
                    return false;
                }
                $this->writeJson(200, [], "添加成功");
                return false;
            }
            if ($action == "UPDATE") {
                $password = $this->request()->getParsedBody('password');
                $username = $this->request()->getParsedBody('username');
                $cookies = $this->request()->getParsedBody("cookies");
                $mail = $this->request()->getParsedBody("mail");
                $id = $this->request()->getParsedBody("id");
                $res = CookiesModel::create()->get(['id' => $id]);
                if (!$res) {
                    $this->writeJson(-101, [], "非法修改");
                    return false;
                }
                $update = [

                ];
                if (isset($username) && !empty($username)) {
                    $update['username'] = $username;
                }
                if (isset($password) && !empty($password)) {
                    $update['password'] = $password;
                }
                if (isset($cookies) && !empty($cookies)) {
                    $update['cookies'] = $cookies;
                }

                if (isset($mail) && !empty($mail)) {
                    $update['mail'] = $mail;
                }
                $res = CookiesModel::create()->where(['id' => $id])->update($update);
                if (!$res) {
                    $this->writeJson(-101, [], "修改失败");
                    return false;
                }
                $this->writeJson(200, [], "修改成功");

            }
            if ($action == "DEL") {
                $id = $this->request()->getQueryParam('id');
                $res = CookiesModel::create()->get(['id' => $id]);
                if (!$res) {
                    $this->writeJson(-101, [], "非法删除");
                    return false;
                }
                $res = CookiesModel::create()->destroy(['id' => $id]);
                if (!$res) {
                    $this->writeJson(-101, [], "删除失败");
                    return false;
                }
                $this->writeJson(200, [], "删除成功");
                return false;
            }
            if ($action == "Check") {  # 测试cookie 是否 有效
                $id = $this->request()->getQueryParam('id');
                $res = CookiesModel::create()->get(['id' => $id]);
                if (!$res) {
                    $this->writeJson(-101, [], "非法请求");
                    return false;
                }
                $headers = [
                    'authority' => 'www.tiktok.com',
                    'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="98", "Google Chrome";v="98"',
                    'sec-ch-ua-mobile' => '?0',
                    'sec-ch-ua-platform' => '"macOS"',
                    'upgrade-insecure-requests' => '1',
                    'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.109 Safari/537.36',
                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'sec-fetch-site' => 'none',
                    'sec-fetch-mode' => 'navigate',
                    'sec-fetch-user' => '?1',
                    'sec-fetch-dest' => 'document',
                    'accept-language' => 'zh-CN,zh;q=0.9',
                    'cookie' => $res['cookies'],
                ];
                $client1 = new \EasySwoole\HttpClient\HttpClient();
                $client1->setTimeout(10);
                $client1->setConnectTimeout(10);
                $client1->setHeaders($headers);
                $client1->setUrl("https://www.tiktok.com/@easy_going177");
                $result = $client1->get();
                $html = $result->getBody();
                preg_match("/u=(\d+)/", $html, $matches);
                if ($matches[1]) {
                    //更新入库
                    $this->writeJson(200, [], $matches[1]);
                } else {
                    CookiesModel::create()->where(['id' => $id])->update(["status" => 2]);
                    $this->writeJson(-101, [], "已经失效了");
                }
                return false;


            }
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], $exception->getMessage());
        }

    }


    /**
     * 获取粉丝
     */

    function GetFasFormVideo()
    {
        try {
            $action = $this->request()->getQueryParam('action');
            if ($action == "GET") {
                $page = $this->request()->getRequestParam('page');         // 当前页码
                $limit = $this->request()->getRequestParam('limit');        // 每页多少条数据
                $status = $this->request()->getRequestParam('status');
                $sex = $this->request()->getQueryParam('sex');
                $country = $this->request()->getQueryParam('country');
                $Vid = $this->request()->getQueryParam('Vid');
                $updated = $this->request()->getQueryParam('updated');

                $nickname = $this->request()->getQueryParam('nickname');
                $model = MonitorFansModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('updated_at', 'ASC');
                if (isset($Vid) && !empty($Vid)) {
                    $one = MonitorVideoModel::create()->get(['vID' => $Vid]);
                    if (!$one) {
                        $this->writeJson(-101, [], "查询的来源地址不存在!");
                        return false;
                    }
                    $model = $model->where(['video_id' => $one['id']]);
                }


                if (isset($sex) && !empty($sex)) {
                    $model = $model->where(['sex' => $sex]);
                }

                if (isset($country) && !empty($country)) {
                    $model = $model->where(['country' => $country]);
                }

                if (isset($updated) && !empty($updated)) {
                    $model = $model->where(['updated' => $updated]);

                }

                if (isset($nickname) && !empty($nickname)) {
                    $model = $model->where(['nickname' => $nickname]);

                }


                $list = $model->all(['status' => $status]);
                foreach ($list as $k => $value) {
                    //查询视频id
                    $one = MonitorVideoModel::create()->get(['id' => $value['video_id']]);
                    if ($one) {
                        $list[$k]['video_id'] = $one['vID'];
                    }
                }
                $result = $model->lastQueryResult();
                // 总条数
                $total = $result->getTotalCount();
                $return_data = [
                    "code" => 0,
                    "msg" => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data));
                return true;
            }
            if ($action == "phone") {
                $country = $this->request()->getQueryParam('country');
                $sex = $this->request()->getQueryParam('sex');
                $nickname = $this->request()->getQueryParam('nickname');
                $commit_time = $this->request()->getQueryParam('commitTime');
                $face_possibility = $this->request()->getQueryParam('face_possibility');
                $attention_nums = $this->request()->getQueryParam('attention_nums');
                $fans_nums = $this->request()->getQueryParam('fans_nums');
                $privacy = $this->request()->getQueryParam('privacy');


                $mode = MonitorFansModel::create();
                if (isset($country) && !empty($country)) {
                    $mode = $mode->where(['country' => $country]);
                }

                if (isset($commit_time) && $commit_time != -1) {
                    $mode = $mode->where('comment_time', $commit_time, '>');
                }


                if (isset($face_possibility) && $face_possibility != -1) {  #脸的识别度 大于等于  0.8
                    $mode = $mode->where('face_possibility', $face_possibility, '>=');
                }
                if (isset($attention_nums) && $attention_nums != -1) {  #关注数量
                    $mode = $mode->where('attention_nums', $attention_nums, '>=');
                }
                if (isset($fans_nums) && $fans_nums != -1) {  #关注数量  最小 和最大

                    $fansNums = explode('-', $fans_nums);
                    if (count($fans_nums) == 2) {
                        $mode = $mode->where('fans_nums', $fansNums[0], '>=');
                        $mode = $mode->where('fans_nums', $fansNums[1], '=<');
                    }


                }
                if (isset($privacy) && $privacy != -1) {  #是否是私密账号
                    $mode = $mode->where('privacy', $privacy, '=');
                }

                if (isset($sex) && !empty($sex)) {
                    $mode = $mode->where(['sex' => $sex]);
                }
                $one = $mode->get(['status' => 0]);
                if (!$one) {
                    $this->writeJson(-101, [], "粉丝用完了..");
                    return false;
                }

                $redis = RedisPool::defer('redis');
                if ($redis->get($one['uid'])) {  #删除这个 链接
                    MonitorFansModel::create()->destroy(['id' => $one['id']]);
                    $this->writeJson(-101, [], "获取失败,重复获取");
                    return false;
                }

                $redis->set($one['uid'], '11');
                //更新链接 状态
                MonitorFansModel::create()->where(['id' => $one['id']])->update(['status' => 1, 'nickname' => $nickname, 'updated' => date("Y-m-d", time()), 'updated_at' => time()]);
                $this->writeJson(200, [], $one['uid']);
                return false;
            }
        } catch (\Throwable $exception) {
            $this->writeJson(-1, "", $exception->getMessage());

        }
    }


    /***
     *  获取视频链接
     */

    function GetVideoUrl()
    {
        try {
            $action = $this->request()->getRequestParam('action');
            if ($action == "GET") {
                $page = $this->request()->getRequestParam('page');         // 当前页码
                $limit = $this->request()->getRequestParam('limit');        // 每页多少条数据
                $status = $this->request()->getRequestParam('status');
                $kinds = $this->request()->getRequestParam('kinds');
                $country = $this->request()->getQueryParam('country');
                $model = MonitorVideoModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created', 'ASC');
                if (isset($country) && !empty($country)) {
                    $model = $model->where(['country' => $country]);
                }
                $list = $model->all(['kinds' => $kinds, 'status' => $status]);  //1 是可以使用的cookie  2 cookies 失效
                $result = $model->lastQueryResult();
                // 总条数
                $total = $result->getTotalCount();
                $return_data = [
                    "code" => 0,
                    "msg" => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data));
                return true;
            }
            if ($action == "phone") {  //手机上传链接
                $content = $this->request()->getBody()->getContents();
                //    $data = json_decode($this->request()->getBody()->getContents(),true);
                $data = json_decode($content, true);
                $nickname = $data['nickname'];
                $county = $data['county'];
                $url = $data['url'];

                $one = MonitorVideoModel::create()->get(['url' => $url]);
                if ($one) {
                    $this->writeJson(200, [], "不要重复添加");
                    return false;
                }

                //class="tiktok-1y2yo26-StrongText e1bs7gq22">(\d+)  正则 匹配 视频的评论数

                //获取 链接的 视频id
                $content = $this->GetUidFormURL($url);
                $add = [
                    'url' => $url,
                    'kinds' => 2,
                    'status' => 5,
                    'country' => $county,
                    'nickname' => $nickname,
                    'created' => time(),
                    'commit_num' => $data['commit']
                ];


                if ($content && count($content) == 3) {
                    //视频id 暂时别写
                    //查看这个视频 url 是否存在了
                    $add['vID'] = $content[2];
                    $add['up_name'] = $content[1];

                    //添加之前
                    $one = MonitorVideoModel::create()->get(['vID' => $add['vID']]);
                    if (!$one) {
                        MonitorVideoModel::create()->data($add)->save();  //不存在
                    }
                } else {
                    MonitorVideoModel::create()->data($add)->save();
                }
                $this->writeJson(200, [], "");

            }
            if ($action == "check") {  //审核  国家
                $id = $this->request()->getQueryParam('id');
                $status = $this->request()->getQueryParam('status');
                //判断 数据是否存在
                $one = MonitorVideoModel::create()->get(['id' => $id]);
                if (!$one) {
                    $this->writeJson(-101, [], "数据不存在");
                    return false;
                }
                $two = MonitorVideoModel::create()->where(['id' => $id])->update(['status' => $status]);


                if ($status == 3) {  #审核-未使用
                    $redis = RedisPool::defer('redis');
                    $redis->rPush("AutomaticFanCollectionProcess", json_encode($one));
                }
                if (!$two) {
                    $this->writeJson(-101, [], "修改失败");
                    return false;
                }
                $this->writeJson(200, [], "修改成功");
                return false;
            }
            if ($action == "UPDATE") {  //更新数据  一般是 修改国际
                $id = $this->request()->getQueryParam('id');
                $country = $this->request()->getQueryParam('country');
                //判断 数据是否存在
                $one = MonitorVideoModel::create()->get(['id' => $id]);
                if (!$one) {
                    $this->writeJson(-101, [], "数据不存在");
                    return false;
                }
                $two = MonitorVideoModel::create()->where(['id' => $id])->update(['country' => $country]);
                if (!$two) {
                    $this->writeJson(-101, [], "修改失败");
                    return false;
                }
                $this->writeJson(200, [], "修改成功");
                return false;

            }
            if ($action == "DEL") {
                $id = $this->request()->getQueryParam('id');
                //判断 数据是否存在
                $one = MonitorVideoModel::create()->get(['id' => $id]);
                if (!$one) {
                    $this->writeJson(-101, [], "数据不存在");
                    return false;
                }
                $two = MonitorVideoModel::create()->destroy(['id' => $id]);
                if (!$two) {
                    $this->writeJson(-101, [], "删除失败");
                    return false;
                }
                $this->writeJson(200, [], "删除成功");
                return false;
            }


            if ($action == "oneCheck") {
                $country = $this->request()->getQueryParam('country');

                $model = MonitorVideoModel::create()->limit(50);
                if (isset($country) && !empty($country)) {
                    $model = $model->where(['country' => $country]);
                }
                $res = $model->all(['status' => 5]);
                if ($res) {
                    foreach ($res as $re) {
                        MonitorVideoModel::create()->where(['id' => $re['id']])->update(['status' => 3]);
                        $redis = RedisPool::defer('redis');
                        $redis->rPush("AutomaticFanCollectionProcess", json_encode($re->toRawArray()));
                    }
                }
                $this->writeJson(200, [], "审核完毕");
                return false;
            }


        } catch (\Throwable $exception) {
            var_dump($exception->getMessage());
            $this->writeJson(-1, [], $exception->getMessage());
        }
    }


    /**
     *   人脸识别 设置
     */
    function Config()
    {
        $action = $this->request()->getQueryParam('action');
        $redis = RedisPool::defer('redis');

        if ($action == "GET") {
            if (!$redis->hExists("CONFIG_FACE", "switch")) {  #1 关  2 开  开关
                $redis->hMSet("CONFIG_FACE", ["switch" => 1, "face_accuracy" => "0.9"]);
            }
            $this->writeJson(200, $redis->hGetAll("CONFIG_FACE"));
            return false;
        }


        if ($action == "SET") {
            $switch = $this->request()->getQueryParam('switch');
            $face_accuracy = $this->request()->getQueryParam('face_accuracy');
            if (isset($face_accuracy) && !empty($face_accuracy)) {
                $redis->hSet("CONFIG_FACE", "face_accuracy", $face_accuracy);
                $this->writeJson(200, "设置精度成功");
                return false;
            }
            if (isset($switch) && !empty($switch)) {
                $redis->hSet("CONFIG_FACE", "switch", $switch);
                $this->writeJson(200, "设置成功");
                return false;
            }
            return false;
        }


    }


    /**
     *  一键重置
     */
    function OneKey()
    {
        try {
            $status = $this->request()->getQueryParam('status');
            $res = MonitorVideoModel::create()->all(['status' => $status]);  #  已审核已使用
            $redis = RedisPool::defer('redis');
            foreach ($res as $re) {
                MonitorVideoModel::create()->where(['id' => $re['id']])->update(['status' => 3]);
                $redis->rPush("AutomaticFanCollectionProcess", json_encode($re));
            }
            $this->writeJson(200, [], "重置成功");
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], $exception->getMessage());
            return false;
        }
    }


    /**
     * @return bool
     * 日志
     */
    function Journal()
    {

        try {
            $action = $this->request()->getRequestParam('action');
            if ($action == "GET") {
                $page = $this->request()->getRequestParam('page');         // 当前页码
                $limit = $this->request()->getRequestParam('limit');        // 每页多少条数据
                $kinds = $this->request()->getRequestParam('kinds');
                $vid = $this->request()->getRequestParam('vid');
                $uid = $this->request()->getRequestParam('uid');
                $model = JournalModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created', 'ASC');
                if (isset($vid) && !empty($vid)) {
                    $model = $model->where(['vid' => $vid]);
                }
                if (isset($uid) && !empty($uid)) {
                    $model = $model->where(['uid' => $uid]);
                }
                $list = $model->all(['kinds' => $kinds]);  //1 是可以使用的cookie  2 cookies 失效
                $result = $model->lastQueryResult();
                // 总条数
                $total = $result->getTotalCount();
                $return_data = [
                    "code" => 0,
                    "msg" => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data));
                return true;
            }
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], $exception->getMessage());
        }

    }


}
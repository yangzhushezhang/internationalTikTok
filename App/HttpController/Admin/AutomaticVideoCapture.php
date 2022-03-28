<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\CookiesModel;
use App\HttpController\Model\Dy_url;
use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitoriktokupnameModel;
use App\HttpController\Model\MonitorVideoModel;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\Exception\Exception;

class AutomaticVideoCapture extends Base
{


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
                $model = MonitorFansModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created', 'ASC');
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


                $list = $model->all();
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

                //获取 链接的 视频id
                $content = $this->GetUidFormURL($url);

                var_dump($content);

                $add = [
                    'url' => $url,
                    'kinds' => 2,
                    'status' => 5,
                    'country' => $county,
                    'nickname' => $nickname,
                    'created' => time()
                ];
                if ($content) {
                    //视频id 暂时别写
                    //查看这个视频 url 是否存在了
                    $add['vID'] = $content;;

                    //添加之前
                    $one = MonitorVideoModel::create()->get(['vID' => $content]);
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


        } catch (\Throwable $exception) {
            var_dump($exception->getMessage());
            $this->writeJson(-1, [], $exception->getMessage());
        }
    }


}
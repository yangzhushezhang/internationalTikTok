<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\PhoneCodeModel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\AbstractInterface\REST;
use EasySwoole\RedisPool\RedisPool;

class PhoneCode extends Controller
{

    /**
     * @return bool
     *  获取验证码
     */
    function getPhoneCode()
    {
        try {

            $url = "http://api.quanqiusms.com/api/sms/mtsend";
            $client = new \EasySwoole\HttpClient\HttpClient($url);//实例化Htpp客户端
            $phone = $this->request()->getQueryParam("phone");
            if (!isset($phone) || empty($phone)) {
                $this->writeJson(-101, [], "非法请求");
                return false;
            }
            //随机生成字符串
            $code_id = $this->GetRandStr(30);
            //随机生成数字
            $code = $this->GetRandStrNum(4);
            $redis = RedisPool::defer("redis");
            $redis->Set($code_id . "_" . $phone, $code, 300);
            $post_data = [
                'appkey' => appkey,
                'secretkey' => secretkey,
                'phone' => $phone,
                'content' => urlencode("您的订单编码：" . $code . "。如需帮助请联系客服。"),

            ];


            $res = $client->post($post_data);
            $res_data = $res->getBody();
            if (json_decode($res_data)) {
                if ($res_data["code"] == 0) {
                    $this->writeJson(200, $code_id, "success ");
                    return true;
                }
            }

            $this->writeJson(-101, $res_data, "fail ");
            return false;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], $exception->getMessage());
            return false;
        }
    }


    /***
     * 提交
     */
    function checkPhoneCode()
    {
        try {
            $code = $this->request()->getQueryParam("code");
            $code_id = $this->request()->getQueryParam("code_id");
            $phone = $this->request()->getQueryParam("phone");


            $action = $this->request()->getQueryParam("action");
            if (!isset($code) || !isset($code_id) || !isset($phone)) {
                $this->writeJson(-101, [], "非法请求");
                return false;
            }
            $redis = RedisPool::defer("redis");
            $one = $redis->get($code_id . "_" . $phone);

            if (isset($action) && $action == "login") {
                if ($one == $code) {
                    $this->writeJson(200, [], "登录成功");

                    return false;

                }
                $this->writeJson(-101, [], "验证码错误");
                return false;
            }


            if ($one == $code) {


                // 判断这个手机 是不是拿过验证码
                $two = PhoneCodeModel::create()->get(['phone' => $phone]);
                if ($two) {
                    $this->writeJson(200, ["active_code" => $two['active_code']], "OK");
                    return false;
                }
                $one = PhoneCodeModel::create()->get(['status' => 1]);
                if (!$one) {
                    $this->writeJson(-101, [], "没有库存了!");
                    return false;
                }

                if ($one['phone'] != "") {
                    $this->writeJson(-101, [], "获取失败");
                    return false;
                }
                //更新数据
                $redis->del($code_id . "_" . $phone);
                PhoneCodeModel::create()->where(['id' => $one['id']])->update(['status' => 2, 'phone' => $phone, 'updated' => time()]);
                $this->writeJson(200, ["active_code" => $one['active_code']], "OK");
                return true;

            }
            $this->writeJson(-101, [], "验证码错误");
            return false;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], $exception->getMessage());
            return false;
        }


    }


    //导入 激活码
    function importActivationCode()
    {
        try {
            $action = $this->request()->getQueryParam("action");
            if ($action == "GET") {
                $page = $this->request()->getRequestParam('page');         // 当前页码
                $limit = $this->request()->getRequestParam('limit');        // 每页多少条数据
                $status = $this->request()->getRequestParam('status');
                $model = PhoneCodeModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created', 'ASC');
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
                return false;
            }
            $data = $this->request()->getParsedBody("data");
            if (!isset($data)) {
                $this->writeJson(-101, [], "导入失败");
                return false;
            }
            $data_array = explode("@", $data);
            foreach ($data_array as $value) {
                $one = PhoneCodeModel::create()->get(['active_code' => $value]);
                if (!$one) {
                    PhoneCodeModel::create()->data(['active_code' => $value, 'created' => time()])->save();
                }
            }
            $this->writeJson(200, [], "OK");
            return false;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], $exception->getMessage());
            return false;
        }
    }


    /**
     * @param $length
     * @return string  生成随机字符串
     */
    function GetRandStr($length)
    {
        //字符组合
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }


    function GetRandStrNum($length)
    {
        //字符组合
        $str = '0123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }

}
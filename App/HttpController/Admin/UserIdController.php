<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\UserIdModel;
use EasySwoole\Mysqli\Exception\Exception;
use EasySwoole\ORM\DbManager;

class UserIdController extends Base
{


    //插入数据库
    function addUserId()
    {
        $data = $this->request()->getParsedBody();
        if (!isset($data['user_id']) || !isset($data['location'])) {
            $this->writeJson(-101, [], "非法请求");
            return;
        }
        $data_array = explode("\n", $data['user_id']);
        if (count($data_array) == 0) {
            $this->writeJson(-101, [], "上传的参数不可以为空");
            return;
        }
        foreach ($data_array as $re) {
            if ($re != "") {
                try {
                    $one = UserIdModel::create()->get(['user_id' => $re]);
                    if (!$one) {
                        //插入

                        $one = UserIdModel::create()->get(['user_id' => $re]);
                        if (!$one) {
                            $data_one = [
                                'user_id' => $re,
                                'status' => 1,
                                'updated_at' => time(),
                                'location' => $data['location']

                            ];
//                        var_dump($data_one);
                            UserIdModel::create()->data($data_one)->save();
                        }

                    }
                } catch (\Throwable $e) {
                    var_dump("异常添加" . $e->getMessage());

                }
            }
        }
        $this->writeJson(200, [], "添加成功");
        return;
    }


    //获取 一个粉丝
    function getOne()
    {

        try {
            $location = $this->request()->getRequestParam('location');
            $nickname = $this->request()->getRequestParam('nickname'); //使用的手机

            if (!isset($location) || !isset($nickname)) {
                $this->writeJson(-1, [], "参数非法");
                return;
            }

            $res = UserIdModel::create()->get(['status' => 1, 'location' => $location]);
            if (!$res) {
                $this->writeJson(-1, [], "库存为空");
                return;
            }
            UserIdModel::create()->where(['id' => $res['id']])->update(['status' => 2, 'nickname' => $nickname]);
            $this->writeJson(1, [], $res['user_id']);
            return;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], "获取异常");
            return;
        }


    }





}
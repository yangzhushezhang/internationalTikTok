<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\AdminModel;
use App\HttpController\Model\FansTModel;
use App\HttpController\Model\FollowModel;
use App\HttpController\Model\UidTModel;
use App\HttpController\Model\UserIdModel;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Exception\Exception;

class UidController extends Base
{


    //http://47.242.44.105:7777/set_uid?action=GET&nickname=haha
    /**
     * @return bool
     *
     *  脚本 获取  uid  和 管理员 插入uid
     */
    function set_uid()
    {

        try {

            $action = $this->request()->getQueryParam("action");
            if ($action == "GET2") {
                $status = $this->request()->getQueryParam('status');
                $nickname = $this->request()->getQueryParam('nickname');
                $limit = $this->request()->getQueryParam('limit');
                $page = $this->request()->getQueryParam('page');
                $model = UidTModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created_at', 'DESC');;
                // 列表数据
//                if ($date == "123" && $nickname = "123") {
//                    $list = $model->all();
//                } else {
//                    if ($nickname != "") {
//                        $list = $model->all(['nickname' => $nickname]);
//                    } else {
//                        $list = $model->all(["FROM_UNIXTIME(created_at,'%Y-%m-%d')" => $date]);
//                    }
//                }
                if (isset($nickname)) {
                    $model = $model->where(['nickname' => $nickname]);

                }
                if (isset($status)) {
                    $model = $model->where(['status' => $status]);

                }

                $list = $model->all();
                $result = $model->lastQueryResult();
                $total = $result->getTotalCount();
                $return_data = [
                    'code' => 0,
                    'msg' => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data, true));
                return true;
            }


            if ($action == "GET") {   #脚本获取
                $nickname = $this->request()->getQueryParam("nickname");
                $res = UidTModel::create()->where("uid", 0, "!=")->get(["status" => 1]);
                if ($res) {
                    $this->writeJson(0, $res["uid"], "获取成功");
                    UidTModel::create()->where(['id' => $res['id']])->update(['status' => 2, 'nickname' => $nickname]);
                    return false;
                }
                $this->writeJson(-101, "", "没有库存了!");
                return false;
            }

            if ($action == "SET") {
                $uids = $this->request()->getParsedBody("uids");
                $uisd_array = explode("\n", $uids);
                foreach ($uisd_array as $uid) {
                    $one = UidTModel::create()->get(['uid' => $uid]);
                    if (!$one) {
                        UidTModel::create()->data(["status" => 1, "created_at" => time(), "uid" => $uid])->save();
                    }

                }
                $this->writeJson(200, "", "插入成功");
                return false;
            }

            if ($action == "SET3") {
                $uids = $this->request()->getParsedBody("username");
                $uisd_array = explode("\n", $uids);
                foreach ($uisd_array as $uid) {
                    if ($uid != "") {
                        $one = UidTModel::create()->get(['username' => $uid]);
                        if (!$one) {
                            UidTModel::create()->data(["status" => 1, "created_at" => time(), "uid" => 0, 'username' => $uid])->save();
                        }
                    }
                }
                $this->writeJson(200, "", "插入成功");
                return false;
            }
        } catch (\Throwable $exception) {
            var_dump($exception->getMessage());
            $this->writeJson(-1, "", $exception->getMessage());
            return false;
        }

    }


    /***
     * http://47.242.44.105:7777/set_fans?nickname=aaa&username=bbb&date=2019
     * @throws \EasySwoole\ORM\Exception\Exception
     */
    function set_fans()
    {
        try {

            $action = $this->request()->getQueryParam('action');
            if (isset($action) && $action == "GET") {

                $status = $this->request()->getQueryParam('status');
                $nickname = $this->request()->getQueryParam('nickname');
                $limit = $this->request()->getQueryParam('limit');
                $page = $this->request()->getQueryParam('page');
                $model = FansTModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created_at', 'DESC');;
                $list = $model->all();
                $result = $model->lastQueryResult();
                $total = $result->getTotalCount();
                $return_data = [
                    'code' => 0,
                    'msg' => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data, true));
                return true;
            }

            $nickname = $this->request()->getQueryParam("nickname");
            $username = $this->request()->getQueryParam('username');
            $date = $this->request()->getQueryParam('date');

            //判断 数据库是否已经有了  username
            $res = FansTModel::create()->get(['username' => $username]);
            if ($res) {
                $this->writeJson(-101, "", "不要重复添加");
                return false;
            }


            $last_time_of_vido = $this->dateOfTreatment($date);

            FansTModel::create()->data(['username' => $username, 'created_at' => time(), 'nickname' => $nickname, 'date' => $date, 'last_time_of_vido' => $last_time_of_vido])->save();
            $this->writeJson(200, "", "插入成功");

        } catch (\Throwable $exception) {
            $this->writeJson(-1, "", $exception->getMessage());
            return false;
        }


    }


    function SetLastTimeOfVido()
    {
        try {
            $res = FansTModel::create()->all(['last_time_of_vido' => 0]);
            foreach ($res as $re) {
                $last_time_of_vido = $this->dateOfTreatment($re['date']);
                var_dump($last_time_of_vido);
                FansTModel::create()->where(['id' => $re['id']])->update(['last_time_of_vido' => $last_time_of_vido]);
            }
            $this->writeJson(200, [], "执行成功");
            return false;
        } catch (\Throwable $e) {
            $this->writeJson(-1, [], $e->getMessage());
        }
    }


    function dateOfTreatment($date)
    {
        $date = trim(str_replace("· ", "", $date));
        if (strstr($date, "ago")) {
            if (strstr($date, "m")) {  //分钟
                $date = trim(str_replace("m ago", "", $date));
                return time() - $date * 60;
            } elseif (strstr($date, "h")) {  //小时
                $date = trim(str_replace("h ago", "", $date));
                return time() - $date * 60 * 60;
            } else if (strstr($date, "d")) {  //天
                $date = trim(str_replace("d ago", "", $date));
                return time() - $date * 60 * 60 * 24;
            } else if (strstr($date, "w")) { //一星期
                $date = trim(str_replace("w ago", "", $date));
                return time() - $date * 60 * 60 * 24 * 7;
            }
        } else {

            if (strlen($date) == 10) {

                return strtotime($date);

            } else if (strlen($date) == 5) {
                $y = Date("Y", time());

                return strtotime($y . "-" . $date);
            }

        }


        return 0;
    }


    function setCookies()
    {

        $action = $this->request()->getParsedBody("action");
        if (isset($action) && $action == "GET") {
            $res = AdminModel::create()->get(['id' => 1]);
            $this->writeJson(200, $res, "获取成功");
            return;
        }
        $cookies = $this->request()->getParsedBody('cookies');
        $res = AdminModel::create()->where(['id' => 1])->update(['cookies' => $cookies]);
        $this->writeJson(200, [], "插入成功");

    }


}
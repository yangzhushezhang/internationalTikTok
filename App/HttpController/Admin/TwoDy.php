<?php
/**
 * @Author WangYi
 * @Date 2021/5/28 23:09
 * @Version 1.0
 */


namespace App\HttpController\Admin;


use App\HttpController\Model\DyName;
use App\HttpController\Model\TwoDouyinModel;
use EasySwoole\Mysqli\Exception\Exception;

class TwoDy extends Base
{


    /**
     * @return bool
     * 采集 粉丝 id
     */
    function two_add_id()
    {

        try {


            $id = $this->request()->getQueryParam('douyi_id');
            $nickname = $this->request()->getQueryParam('nickname');  //收集者
            if (!isset($id) || $id == "") {
                $this->writeJson(-101, [], 'id 不可以为空');
                return false;
            }


            $res = TwoDouyinModel::create()->get(['douyi_id' => $id]);
            if ($res) {
                $this->writeJson(-101, [], '不要重复添加');
                return false;
            }

            $inert_data = [
                'douyi_id' => $id,
                'updated_at' => time(),
                'created_at' => time(),
                'nickname' => $nickname
            ];

            $res = TwoDouyinModel::create()->data($inert_data)->save();
            if (!$res) {
                $this->writeJson(-101, [], '插入数据库失败');
                return false;
            }


            $this->writeJson(200, [], '添加成功');
            return false;

        } catch (\Throwable $e) {
            $this->writeJson(-1, [], "获取异常:" . $e->getMessage());
            var_dump($e->getMessage());
            return false;
        }
    }


    function get_two_dy_id()
    {
        try {
            $username = $this->request()->getQueryParam('username');

            if (!$username) {
                $this->writeJson(-101, [], "username 不可以为空");
                return false;
            }

            $res = TwoDouyinModel::create()->get(['status' => 0]);
            if (!$res) {
                $this->writeJson(-101, [], "获取id 失败");
                return false;
            }


            TwoDouyinModel::create()->where(['douyi_id' => $res['douyi_id']])->update(['username' => $username, 'updated_at' => time(), 'status' => 1]); //更新数据库

            $array = explode("：", $res['douyi_id']);
            if (count($array) == 2) {
                $this->writeJson(200, [], $array[1]);
            } else {
                $this->writeJson(-101, [], "获取id 失败");
            }


            return false;

        } catch (\Throwable $e) {
            $this->writeJson(-1, [], "异常:" . $e->getMessage());
            return false;
        }


    }


    function get_list()
    {
        try {


            $id = $this->request()->getQueryParam('id');

            if (!isset($id)) {
                $id = 0;
            }
            $res = TwoDouyinModel::create()->field(['douyi_id','id'])->where('(id >'.$id.' )')->all();
            $this->writeJson(200,$res,'获取成功');
            return false;

        } catch (\Throwable $e) {
            $this->writeJson(-1, [], "异常:" . $e->getMessage());
            return false;
        }
    }


}
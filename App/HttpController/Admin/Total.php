<?php
/**
 * @Author WangYi
 * @Date 2021/6/3 18:38
 * @Version 1.0
 */


namespace App\HttpController\Admin;


use App\HttpController\Model\FollowModel;
use App\HttpController\Model\TotalModel;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\RedisPool\RedisPool;

class Total extends Base
{

    //定时任务 记录新的一天的 数据
    function Add_total()
    {
        try {
            $redis = RedisPool::defer('redis');
            //获取今日的数据
            $today_time = date("Y-m-d", time());
//            $data = $redis->hGetAll($today_time);
//            if (!$data) {
//                $this->writeJson(-101, [], "添加失败,redis 里面没有可用数据");
//                return false;
//            }
            $all_following = FollowModel::create()->where(['date' => $today_time])->sum('add_following');
            $all_sixin = FollowModel::create()->where(['date' => $today_time])->sum('sixin');
            $all_real_following = FollowModel::create()->where(['date' => $today_time])->sum('real_concerns');
            $insert = [
                'all_following' => $all_following,
                'all_sixin' => $all_sixin,
                'date' => $today_time,
                'all_real_following' => $all_real_following
            ];

            $res = TotalModel::create()->get(['date' => $today_time]);
            if ($res) {
                $this->writeJson(-101, [], "不要重复添加执行定时任务");
                return false;
            }
            $res = TotalModel::create()->data($insert)->save();
            if (!$res) {
                $this->writeJson(-101, [], "添加失败");
                return false;
            }

            $this->writeJson(200, [], "添加成功");
            return false;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], '异常:' . $exception->getMessage());
            return false;
        }
    }

    // 获取 今日的统计数据
    function get_total_all()
    {
        try {
            //计算获取
            $redis = RedisPool::defer('redis');
            $today_time = date("Y-m-d", time());
//          $data = TotalModel::create()->get(['date' => $today_time]);
            $all_following = FollowModel::create()->where(['date' => $today_time])->sum('add_following');
            $all_sixin = FollowModel::create()->where(['date' => $today_time])->sum('sixin');
            $all_real_following = FollowModel::create()->where(['date' => $today_time])->sum('real_concerns');
            $data = [
                'all_following' => $all_following,
                'all_sixin' => $all_sixin,
                'all_real_following' => $all_real_following
            ];
            $this->writeJson(200, $data, "获取成功");
            return false;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], '异常:' . $exception->getMessage());
            return false;
        }

    }

    /**
     * @return bool
     * 获取所以列表
     */
    function get_total_list()
    {
        $limit = $this->request()->getQueryParam('limit');
        $page = $this->request()->getQueryParam('page');
        try {
            $model = TotalModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('date', 'DESC');
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
        } catch (\Throwable $e) {
            $this->writeJson(-1, [], '获取异常:' . $e->getMessage());
            return false;
        };


    }


}
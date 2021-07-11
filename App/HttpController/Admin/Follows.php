<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\ConfigModel;
use App\HttpController\Model\FollowModel;
use App\Log\LogHandel;
use Cassandra\Varint;
use EasySwoole\Http\AbstractInterface\REST;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\Pool\Exception\PoolEmpty;

class Follows extends Base
{


    /***
     * @return bool
     *   获取粉丝
     */
    function followers()
    {
        try {
            $date = $this->request()->getQueryParam('date');
            $nickname = $this->request()->getQueryParam('nickname');
            $limit = $this->request()->getQueryParam('limit');
            $page = $this->request()->getQueryParam('page');
            $model = FollowModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created_at', 'DESC');;
            // 列表数据
            if ($date == "123" && $nickname = "123") {
                $list = $model->all();
            } else {
                if ($nickname != "") {
                    $list = $model->all(['nickname' => $nickname]);
                } else {
                    $list = $model->all(["FROM_UNIXTIME(created_at,'%Y-%m-%d')" => $date]);
                }
            }
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
            $this->writeJson(-1, 'OK', $e->getMessage());
            return false;
        }
    }


    //add 新的数据
    //add 新的数据
    function addFollow_one()
    {
        try {
            $get_data = $this->request()->getQueryParams();
            $created_at = FollowModel::create()->where(['nickname' => $get_data['nickname']])->max('created_at');
            if ($created_at) {
                $today = date("y-m-d", $created_at);
                $now = date("y-m-d", time());

                if ($today == $now) {
                    FollowModel::create()->where(['nickname' => $get_data['nickname']])->update(['following' => $get_data['following'], 'followers' => $get_data['followers'], 'created_at' => time()]);
                    $this->writeJson(1, 'OK', '更新成功');
                    return true;

                }
                #获取前一天数据
                $one = FollowModel::create()->get(['created_at' => $created_at]);
                if (!$one) {
                    $this->response()->write(json_encode(['code' => 0, 'msg' => '更新失败'], true));
                    return false;
                }
                $one = $one->toArray();
                $insert_data = [
                    'following' => $get_data['following'],
                    'followers' => $get_data['followers'],
                    'nickname' => $get_data['nickname'],
                    'created_at' => time(),
                    'add_following' => $get_data['following'] - $one['following'],
                    'add_followers' => $get_data['followers'] - $one['followers'],
                    // 'status' => $get_data['status']
                ];

                $one = FollowModel::create()->data($insert_data)->save();
                if (!$one) {
                    $this->response()->write(json_encode(['code' => 0, 'msg' => '更新失败'], true));
                    return false;
                }
                // 今天已经获取了!
                $this->response()->write(json_encode(['code' => 1, 'msg' => '更新成功'], true));
                return true;
            } else {
                //直接插入


                $insert_data = [
                    'following' => $get_data['following'],
                    'followers' => $get_data['followers'],
                    'nickname' => $get_data['nickname'],
                    'created_at' => time(),
                ];


                $one = FollowModel::create()->data($insert_data)->save();
                if (!$one) {
                    $this->response()->write(json_encode(['code' => 0, 'msg' => '更新失败'], true));
                    return false;
                }
                // 今天已经获取了!
                $this->response()->write(json_encode(['code' => 1, 'msg' => '更新成功'], true));
                return true;


            }


        } catch (\Exception $exception) {
        } catch (\Throwable $e) {
            var_dump($e);
            return false;
        }

    }


    /**
     * 抖音  封禁
     */
    function change_status()
    {
        $nickname = $this->request()->getQueryParam('nickname');
//        $status = $this->request()->getQueryParam('status');
        $action = $this->request()->getQueryParam('action');
        try {

            if (isset($action) && $action == "update_if_banned") {
                $status = $this->request()->getQueryParam('status');
                $one = ConfigModel::create()->where(['nickname' => $nickname])->update(['update_at' => time(), 'if_banned' => $status]);
                if (!$one) {
                    $this->writeJson(-101, 'OK', "修改失败");
                    return false;
                }
                $this->writeJson(200, 'OK', "修改成功");
                return false;
            }


            if (isset($action) && $action == "update_false_fan") {
                $status = $this->request()->getQueryParam('status');
                $one = ConfigModel::create()->where(['nickname' => $nickname])->update(['update_at' => time(), 'false_fan' => $status]);
                if (!$one) {
                    $this->writeJson(-101, 'OK', "修改失败");
                    return false;
                }
                $this->writeJson(200, 'OK', "修改成功");
                return false;
            }


            $res = ConfigModel::create()->get(['nickname' => $nickname]);
            if (!$res) {
                //如果这个用户名不存在  就加入到数据
                $inert = [
                    'nickname' => $nickname,
                    'created_at' => time(),
                    'update_at' => time(),
                    'status' => 1
                ];
                ConfigModel::create()->data($inert)->save();
                $this->writeJson(-101, 'OK', "修改失败");
                return false;
            }
            $one = ConfigModel::create()->where(['nickname' => $nickname])->update(['update_at' => time(), 'status' => 1]);
            if (!$one) {
                $this->writeJson(-101, 'OK', "修改失败");
                return false;
            }
            $this->writeJson(200, 'OK', "修改成功");
            return false;

        } catch (\Throwable $e) {

            $this->writeJson(-1, 'OK', '异常:' . $e->getMessage());
            return false;
        }


    }



    //更改所有配置的  if_get_follow

    /**
     * @return bool
     * @throws \Throwable
     * 这个是 获取 粉丝数 需要调用的.
     */
    function update_follow()
    {

        try {


            $ids = $this->request()->getQueryParam('id');
            $id = explode('@', $ids);

            // 获取post 参
            foreach ($id as $value) {
                if ($value != "") {
                    $res_one = ConfigModel::create()->get(['id' => $value]);
                    if (!$res_one) {
                        break;
                    }
                    $data = $res_one->toArray();

                    $res = ConfigModel::create()->where(['id' => $value])->update(['if_get_follow' => 0]);


                }
            }

            $this->response()->write(json_encode(['code' => 1, 'msg' => '执行成功'], true));
            return true;


        } catch (\Exception $exception) {


            var_dump('异常');
            return false;
        }


    }


    /**
     * @return bool
     * @throws \Throwable
     * 发送 信息修改 最新的时间
     */
    function update_for_message_status()
    {
        try {
            $uuid = $this->request()->getQueryParam('uuid');

            $res = ConfigModel::create()->where(['uuid' => md5($uuid)])->update(['update_at' => time()]);

            if (!$res) {
                $this->response()->write(json_encode(['code' => 0, 'msg' => '修改失败!'], true));
                return false;
            }
            $this->response()->write(json_encode(['code' => 1, 'msg' => '修改成功!'], true));
            return false;
        } catch (\Exception $exception) {


            var_dump('异常');
            return false;
        }


    }


    /**
     * @return bool
     *
     * 删除抖音链接
     */

    function delete_follows()
    {

        $id = $this->request()->getQueryParam('id');
        try {
            FollowModel::create()->where(['id' => $id])->destroy();
            $this->writeJson(200, 'OK', '删除成功');
        } catch (Exception $e) {
        } catch (\Throwable $e) {
            $this->writeJson(-1, 'OK', '删除失败');
        }


    }


    #更新 或者添加新的粉丝 详情
    function addFollow()
    {

        $log = new LogHandel();
        try {

            return DbManager::getInstance()->invoke(function ($client) {
                $nickname = $this->request()->getQueryParam('nickname');
                $following = $this->request()->getQueryParam('following');
                $followers = $this->request()->getQueryParam('followers');
                $real_concerns = $this->request()->getQueryParam('real_concerns');  //获取手机实际关注的数量

                $sixin = $this->request()->getQueryParam('sixin');
                if (isset($sixin) && $sixin != "") {
                    $today_time = date("Y-m-d", time());  #获取当前时间'
                    $three = FollowModel::invoke($client)->where(['date' => $today_time, 'nickname' => $nickname])->update(['sixin' => $sixin]);
                    $this->response()->write(json_encode(['code' => 1, 'msg' => '更新成功'], true));
                    return false;
                }


//                $real_concerns = 100;
                if ($nickname == "" || $following == "" || $followers == "") {
                    $this->response()->write(json_encode(['code' => 0, 'msg' => '参数不可以为空值'], true));
                    return false;
                }

                $today_time = date("Y-m-d", time());  #获取当前时间'
                $res = FollowModel::invoke($client)->get(['nickname' => $nickname, 'date' => $today_time]);
                if ($res) {
                    #今天的数据以及存在了  更新就好了
                    $yesterday = date("Y-m-d", time() - 86400);
                    #1. 查询昨天的数据
                    $one = FollowModel::invoke($client)->get(['date' => $yesterday, 'nickname' => $nickname]);
                    if ($one) {
                        $update_data = [
                            'following' => $following,
                            'followers' => $followers,
                            'nickname' => $nickname,
                            'updated_at' => time(),
                            'add_following' => $following - $one['following'],
                            'add_followers' => $followers - $one['followers'],
                            'real_concerns' => $real_concerns

                        ];
                        $three = FollowModel::invoke($client)->where(['id' => $res['id']])->update($update_data);
                        if (!$three) {
                            $this->response()->write(json_encode(['code' => 0, 'msg' => '更新失败'], true));
                            return false;

                        }
                        $this->response()->write(json_encode(['code' => 1, 'msg' => '更新成功'], true));
                        return false;
                    } else {
                        #不存在昨天的数据    //更新今天的数据
                        if ($res['following'] == $following) {
                            //上传的 粉丝数量和 上一次的一样 可能存在假粉
                            ConfigModel::create()->where(['nickname' => $nickname])->update(['false_fan' => 1, 'updated_at' => time()]);
                        }
                        $update_data = [
                            'following' => $following,
                            'followers' => $followers,
                            'nickname' => $nickname,
                            'updated_at' => time(),
                            'real_concerns' => $real_concerns
                        ];
                        $three = FollowModel::invoke($client)->where(['id' => $res['id']])->update($update_data);
                        if (!$three) {
                            $this->response()->write(json_encode(['code' => 0, 'msg' => '更新失败'], true));
                            return false;

                        }
                        $this->response()->write(json_encode(['code' => 1, 'msg' => '更新成功'], true));
                        return false;

                    }


                } else {
                    # 还没有今天的数据 插入
                    $yesterday = date("Y-m-d", time() - 86400);

                    #1. 查询昨天的数据
                    $one = FollowModel::invoke($client)->get(['date' => $yesterday, 'nickname' => $nickname]);
                    if (!$one) {
                        #说明 昨天没有数据
                        $insert_data = [
                            'following' => $following,
                            'followers' => $followers,
                            'created_at' => time(),
                            'nickname' => $nickname,
                            'updated_at' => time(),
                            'date' => $today_time,
                            'real_concerns' => $real_concerns
                        ];
                    } else {
                        $insert_data = [
                            'following' => $following,
                            'followers' => $followers,
                            'created_at' => time(),
                            'nickname' => $nickname,
                            'updated_at' => time(),
                            'date' => $today_time,
                            'add_following' => $following - $one['following'],
                            'add_followers' => $followers - $one['followers'],
                            'real_concerns' => $real_concerns
                        ];

                    }

                    $two = FollowModel::invoke($client)->data($insert_data)->save();
                    if (!$two) {
                        $this->response()->write(json_encode(['code' => 0, 'msg' => '新增失败'], true));
                        return false;

                    }
                    $this->response()->write(json_encode(['code' => 1, 'msg' => '新增成功'], true));
                    return false;

                }


            });

        } catch (\Throwable $e) {
            $log->log('添加 粉丝详情异常:' . $e->getMessage());
            $this->response()->write(json_encode(['code' => 0, 'msg' => '添加粉丝异常' . $e->getMessage()], true));
            return false;
        }


    }


}
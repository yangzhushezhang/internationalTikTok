<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\ConfigModel;
use App\HttpController\Model\DyName;
use App\HttpController\Model\FollowModel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Mysqli\Exception\Exception;
use EasySwoole\ORM\DbManager;
use EasySwoole\Pool\Exception\PoolEmpty;
use EasySwoole\Redis\Exception\RedisException;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Validate\Validate;

class Config extends Base
{


    /**
     * @return bool
     *  脚本 初始化
     */
    function get_config()
    {
        try {
            $data = $this->request()->getQueryParams();
            $uuid = md5($data['uuid']);

            $result = ConfigModel::create()->get(['uuid' => $uuid]);

            //创建redis
            $redis = $redis = \EasySwoole\RedisPool\RedisPool::defer('redis');
            $redis->set($uuid, 1, 30);

            if (!$result) {
                //这个设备在数据库不存在!
                $insert_into = [
                    'nickname' => $data['nickname'],
                    'uuid' => md5($data['uuid']),
                    'created_at' => time(),
                    'update_at' => time(),
                    'type' => $data['type'],
                ];
                // 插入数据库操作
                ConfigModel::create()->data($insert_into)->save();
                $this->writeJson(0, '', '设备尚未激活......');
                return false;
            }


            //设备没有激活
            if ($result['impower'] == 0) {
                $this->writeJson(0, '', '设备尚未激活..请联系管理员授权');
                return false;
            }


            if ($result['status'] == 1) {
                var_dump("离线!!");
                //离线
                ConfigModel::create()->where(['uuid' => $uuid])->update(['status' => 2]);
                $this->writeJson(0, '', '脚本初始化完毕,等待中控指令....');
                return false;
            }


            // 暂停 ..等待中控指令
            if ($result['status'] == 2) {
                $this->writeJson(2, '', '脚本初始化完毕,等待中控指令....');
                return false;
            }


            if ($result['status'] == 3) {
                var_dump("关注点赞!");
                if ($result['if_get_follow'] == 0) {
                    //这里是 获取粉丝数
                    $this->writeJson(4, '', $result);   //要去发信息
                    return false;
                }

                //给脚本运行的 命令
                $time_one = time() - $result['update_at']; //时间差
                $time_two = 60 * 60 * $result['sendMessage_time'];
                var_dump($time_one);
                var_dump($time_two);
                // $time_two=60;
                if ($time_one > $time_two) {  //要去发信息了
                    //更新数据
                    // ConfigModel::create()->where(['uuid'=>$uuid])->update(['update'=>time()]);
                    $this->writeJson(3, '', $result);   //要去发信息
                } else {

                    $this->writeJson(1, '', $result);
                }

                return false;
            }


        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {
            var_dump($e);
            return false;
        }


    }


    //获取列表
    function get_users()
    {
        try {

            $limit = $this->request()->getQueryParam('limit');
            $page = $this->request()->getQueryParam('page');
            $status = $this->request()->getQueryParam('status');

            $model = ConfigModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount();

            // 列表数据
            $list = $model->all(['status' => $status]);

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


        } catch (\Exception $exception) {
            var_dump('异常!');
            return false;

        }


    }


    //设备获取授权
    function get_impower()
    {

        try {

            $id = $this->request()->getQueryParam('id');


            $res = ConfigModel::create()->get(['id' => $id]);

            if (!$res) {
                $this->response()->write(json_encode(['code' => 0, 'msg' => '授权失败!'], true));
                return false;
            }


            $res = ConfigModel::create()->where(['id' => $id])->update(['impower' => 1]);
            if (!$res) {

                $this->response()->write(json_encode(['code' => 0, 'msg' => '授权失败!'], true));
                return false;

            }


            $this->response()->write(json_encode(['code' => 1, 'msg' => '授权成功!'], true));


            return true;


        } catch (\Exception $exception) {
            var_dump('异常!');
            return false;

        }

    }


    //修改配置
    function update_data()
    {

        try {
            $post = $this->request()->getParsedBody();
            // 获取post 参
            $update = [
                'running_model' => $post['running_model']
            ];
            if (isset($post['action']) && $post['action'] == "all") {
                $res = ConfigModel::create()->where(['status' => 0])->update($update);
                if (!$res) {
                    $this->response()->write(json_encode(['code' => 0, 'msg' => '修改失败'], true));
                    return false;
                }
            } else {
                $res = ConfigModel::create()->get(['id' => $post['id']]);
                if (!$res) {
                    $this->response()->write(json_encode(['code' => 0, 'msg' => '修改失败'], true));
                    return false;
                }
                $res = ConfigModel::create()->where(['id' => $post['id']])->update($update);
                if (!$res) {
                    $this->response()->write(json_encode(['code' => 0, 'msg' => '修改失败'], true));
                    return false;
                }
            }

            $this->response()->write(json_encode(['code' => 1, 'msg' => '修改成功'], true));
            return true;


        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {

            var_dump('异常');

            return false;
        }

    }


    //批量修改配置 update_data_All
    function update_data_All()
    {

        try {


            $ids = $this->request()->getQueryParam('id');
            $id = explode('@', $ids);

            // 获取post 参
            $post = $this->request()->getParsedBody();
            $update = [
                'api_key' => $post['api_key'],
                'secret_key' => $post['secret_key'],
                'attention_num' => $post['attention_num'],
                'sendMessage_time' => $post['sendMessage_time']
            ];
            foreach ($id as $value) {
                if ($value != "") {
                    $res = ConfigModel::create()->where(['id' => $value])->update($update);
                }
            }


            $this->response()->write(json_encode(['code' => 1, 'msg' => '修改成功'], true));
            return true;


        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {

            var_dump('异常');

            return false;
        }

    }


    //批量授权 impowerAll
    function impowerAll()
    {

        try {


            $ids = $this->request()->getQueryParam('id');
            $id = explode('@', $ids);

            // 获取post 参

            foreach ($id as $value) {
                if ($value != "") {
                    $res = ConfigModel::create()->where(['id' => $value])->update(['impower' => 1]);
                }
            }

            $this->response()->write(json_encode(['code' => 1, 'msg' => '修改成功'], true));
            return true;


        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {

            var_dump('异常');

            return false;
        }

    }


    //批量启动

    function runAll()
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
                    if ($data['status'] != 1) {

                        $update = [
                            'status' => 3,
                            'update_at' => time()

                        ];
                        $res = ConfigModel::create()->where(['id' => $value])->update($update);
                    }

                }
            }

            $this->response()->write(json_encode(['code' => 1, 'msg' => '启动成功'], true));
            return true;


        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {

            var_dump('异常');

            return false;
        }

    }


    //stopAll 批量停止
    function stopAll()
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
                    if ($data['status'] != 1) {
                        $res = ConfigModel::create()->where(['id' => $value])->update(['status' => 2]);
                    }

                }
            }

            $this->response()->write(json_encode(['code' => 1, 'msg' => '停止成功'], true));
            return true;


        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {

            var_dump('异常');

            return false;
        }

    }


    //单个 run_one

    function run_one()
    {
        try {

            $data = $this->request()->getQueryParams();

            if ($data['id'] == "" || $data['status'] == "") {
                $this->response()->write(json_encode(['code' => 0, 'msg' => '非法操作'], true));
                return false;
            }


            $res = ConfigModel::create()->get(['id' => $data['id']]);
            if (!$res) {
                $this->response()->write(json_encode(['code' => 0, 'msg' => '非法操作'], true));
                return false;
            }


            if ($data['status'] == 2) {

                $update = [
                    'status' => 3,
                    'update_at' => time()

                ];

                $res = ConfigModel::create()->where(['id' => $data['id']])->update($update);
                $this->response()->write(json_encode(['code' => 1, 'msg' => '操作成功'], true));
                return false;
            }

            if ($data['status'] == 3) {
                $res = ConfigModel::create()->where(['id' => $data['id']])->update(['status' => 2]);
                $this->response()->write(json_encode(['code' => 2, 'msg' => '操作成功'], true));
                return false;
            }


        } catch (\Exception $exception) {
            var_dump($exception);
            return;
        }


    }


    //删除
    function del()
    {

        try {

            $id = $this->request()->getQueryParam('id');


            $res = ConfigModel::create()->destroy(['id' => $id]);


            FollowModel::create()->destroy(['phone_id' => $id]);


            if (!$res) {
                $this->response()->write(json_encode(['code' => 0, 'msg' => '删除失败'], true));
                return false;
            }


            $this->response()->write(json_encode(['code' => 1, 'msg' => '删除成功'], true));
            return false;


        } catch (\Exception $exception) {
            var_dump($exception);
            return;
        }

    }


    //set_name
    function set_name()
    {
        try {
            $name = $this->request()->getQueryParam('name');
            //var_dump("name:" . $name);
            $res = DyName::create()->get(['name' => $name]);
            if (!$res) {
                //不存在
                DyName::create()->data(['name' => $name])->save();
                $this->writeJson('1', '', '');
                return false;
            }
            $this->writeJson('0', '', '名字重复了!');
            return false;
        } catch (\Throwable $e) {
            $this->writeJson('0', '', '异常:' . $e->getMessage());
            return false;
        }
    }


    /**
     * @user 脚本
     * @ 添加新的 设备哦
     */
    function set_new_phone()
    {
        try {
            $nickname = $this->request()->getQueryParam('nickname');
//            $api_key = $this->request()->getQueryParam('api_key');
//            $secret_key = $this->request()->getQueryParam('secret_key');
//
//            if ($nickname == "" || $api_key == "" || $secret_key == "") {
//                $this->writeJson(-101, 'OK', '必要参数不可以为空');
//
//                return false;
//            }


            DbManager::getInstance()->invoke(function ($client) use ($nickname) {
                $res = ConfigModel::invoke($client)->get(['nickname' => $nickname]);

                if ($res) {
                    $this->writeJson(-101, 'OK', '不要重复添加');
                    return false;
                }


                $insert_data = [
                    'nickname' => $nickname,
                    'created_at' => time(),
                    'update_at' => time()
                ];


                $res = ConfigModel::invoke($client)->data($insert_data)->save();

                if (!$res) {
                    $this->writeJson(-101, 'OK', '添加失败');
                    return false;
                }
                $this->writeJson(200, 'OK', '添加成功');
                return true;

            });

        } catch (\Throwable $e) {
            $this->writeJson(-1, 'OK', $e->getMessage());
            return false;
        }


    }


    /**
     * @return bool
     * 获取 设备是否  可以私聊
     */
    function get_phone_permissions_for_message()
    {
        try {
            $nickname = $this->request()->getQueryParam('nickname');

            if ($nickname == "") {
                $this->writeJson(-101, 'OK', 'nickname 不可以为空');
                return false;
            }


            $res = ConfigModel::create()->get(['nickname' => $nickname]);

            if (!$res) {
                $this->writeJson(200, 'OK', 1);
//                $this->writeJson(-101, 'OK', '数据库没有这个数据');
                return false;
            }

            $res = $res->toArray();

//            $this->writeJson(200, 'OK', $res['permissions_for_message']);
            $this->writeJson(200, 'OK', 1);
            return false;

        } catch (\Throwable $e) {
            $this->writeJson(-1, 'OK', '异常:' . $e->getMessage());
            return false;
        }


    }


    function change__permissions_for_message()
    {
        $id = $this->request()->getQueryParam('id');
        $permissions_for_message = $this->request()->getQueryParam('permissions_for_message');


        try {
            ConfigModel::create()->where(['id' => $id])->update(['permissions_for_message' => $permissions_for_message]);


            $this->writeJson(200, 'OK', '修改成功!');
            return false;
        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {
            $this->writeJson(-1, 'OK', '异常!' . $e->getMessage());
            return false;

        }


    }


    /**
     * @param $client_id
     * @param $client_secret
     * @return string
     *  access_token 有效期为 30天!  记得 没三十天重启服务器
     */
    function get_Access_Token($client_id, $client_secret): string
    {
        try {
            $url = "https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=" . $client_id . "&client_secret=" . $client_secret;
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            $res = $client->get()->getBody();
            if (!$res) {
                return "";
            }
            $accessToken = json_decode($res);
            if (!$accessToken) {
                return "";
            }
            if (!$accessToken->access_token) {
                return "";
            }
            return $accessToken->access_token;
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
            return "";
        }
    }


    /***
     * @param $access_token
     * @param $image
     * @return string
     *  返回性别
     */
    function get_Sex($access_token, $image)
    {
        try {
            $url = "https://aip.baidubce.com/rest/2.0/face/v3/detect?access_token=" . $access_token;
            $client = new \EasySwoole\HttpClient\HttpClient($url);



            $post_data = [
                'image' => $image,
                "image_type" => "BASE64",
                "face_field" => "age,gender",
            ];
            $res = $client->post($post_data)->getBody();

            $client->getErrCode();


            if (!$res) {
                return '';
            }

            $data = json_decode($res, true);
            if (!isset($data['result']['face_list'][0]['gender']['type'])) {
                return '';
            }


            return $data['result']['face_list'][0]['gender']['type'];
        } catch (InvalidUrl $e) {
            return "";
        }
    }


    function get_sex_form_my_server()
    {

        //ZL5wxWPUOp9Yzn1Yc7pjLeZ0
        //FGONKQ4YcRWeX7nIBMPgWukBcDXlG2ao
        try {
            // Access_Token 每一个 月更新一次
            $redis = RedisPool::defer("redis");
            $access_token = $redis->get("Access_Token");
            if (!$access_token) {
//                $access_token = $this->get_Access_Token("N5RYZegzg9yzaEX0pGy75yZ8", "jnAGQI0vxMdS24QXf9QKjaPowNvCfdQm");
                $access_token = $this->get_Access_Token("ZL5wxWPUOp9Yzn1Yc7pjLeZ0", "FGONKQ4YcRWeX7nIBMPgWukBcDXlG2ao");

                if ($access_token) {
                    $redis->set("Access_Token", $access_token, 3600 * 24 * 30);
                }
            }
            $image = $this->request()->getParsedBody();



            $sex = $this->get_Sex($access_token, $image['image']);
            $this->writeJson(200, $sex, "获取成功");
            var_dump($sex);
            return true;
        } catch (RedisException $e) {
            $this->writeJson(-1, [], "获取异常:" . $e->getMessage());
            var_dump($e->getMessage());
            return false;
        }
    }


    /**
     *  设备最后一次的关注时间
     */
    function attentionTheLastTime()
    {
        try {
            $nickname = $this->request()->getQueryParam('nickname');
            if ($nickname == "") {
                $this->writeJson(-101, 'OK', 'nickname 不可以为空');
                return false;
            }
            $res = ConfigModel::create()->get(['nickname' => $nickname]);
            if (!$res) {
                $this->writeJson(-101, 'OK', '数据库没有这个数据,遇到这种情况请联系作者!');
                return false;
            }


            $one = ConfigModel::create()->where(['nickname' => $nickname])->update(['attentionTheLastTime' => time()]);
            if (!$one) {
                $this->writeJson(101, 'OK', '数据更新失败');
                return true;
            }
            $this->writeJson(200, 'OK', '数据成功');
            return true;
        } catch (\Throwable $e) {
            $this->writeJson(-1, [], "获取异常:" . $e->getMessage());
            return false;

        }
    }


    /**
     * 获取 手机的 运行模式
     */


    function get_running_model()
    {
        try {
            DbManager::getInstance()->invoke(function ($client) {

                $username = $this->request()->getRequestParam("username");
                $res = ConfigModel::invoke($client)->get(['nickname' => $username]);
                if ($res) {
                    $this->writeJson(1, $res['running_model'], "获取成功");
                    return;
                }
                $this->writeJson(1, 1, "获取成功");
            });

        } catch (\Throwable $e) {
            $this->writeJson(1, 1, "获取异常:" . $e->getMessage());
            return;
        }
    }


}
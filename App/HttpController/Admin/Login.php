<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\AdminModel;
use App\Log\LogHandel;
use Cassandra\Varint;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\AbstractInterface\REST;
use EasySwoole\Mysqli\Exception\Exception;
use EasySwoole\ORM\DbManager;
use EasySwoole\Pool\Exception\PoolEmpty;

class Login extends Controller
{
    function login()
    {
        try {
            $data = $this->request()->getQueryParams();
            if ($data['username'] == "" || $data['password'] == "") {
                $return_data = [
                    'code' => 0,
                    'msg' => '非法参数'
                ];
                $this->response()->write(json_encode($return_data, true));
                return false;
            }
            $one = AdminModel::create()->get(['username' => $data['username']]);
            if (!$one) {
                $return_data = [
                    'code' => 0,
                    'msg' => '密码错误'
                ];
                $this->response()->write(json_encode($return_data, true));
                return false;
            }
            $data_array = $one->toArray();
            if ($data['password'] != $data_array['password']) {
                $return_data = [
                    'code' => 0,
                    'msg' => '密码错误'
                ];
                $this->response()->write(json_encode($return_data, true));
                return false;
            };
            $return_data = [
                'code' => 1,
                'msg' => '登录成功',
                'count' => 25,
                'data' => []
            ];
            $this->response()->write(json_encode($return_data, true));
            return true;
        } catch (\Throwable $e) {
            $this->writeJson(-1, 'OK', '异常:' . $e->getMessage());
            return false;
        }
    }


    #设置是否发送信息
    function set_send_message()
    {

        $status = $this->request()->getQueryParam('status');
        try {
            if ($status == 1) {

                AdminModel::create()->where(['id' => 1])->update(['send_message' => 1]);


                $this->writeJson(200, 'OK', '发送信息权限已经打开!');
                return true;

            } else {
                AdminModel::create()->where(['id' => 1])->update(['send_message' => 2]);

                $this->writeJson(200, 'OK', '发送信息权限已经关闭!');
                return true;

            }


        } catch (\Throwable $e) {

            var_dump($e->getMessage());
            $this->writeJson(200, 'OK', '修改失败!异常联系!');
            return true;

        }


    }


    function get_send_if()
    {

        try {
            $res = AdminModel::create()->get(['id' => 1]);
            if ($res) {
                $res = $res->toArray();
                $this->writeJson(200, 'OK', $res['send_message']);
                return false;
            }

            $this->writeJson(200, 'OK', 2);
            return false;

        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {
        }

    }


    /**
     * @return bool|mixed|void
     * 获取 总系统版本
     */
    function script_update()
    {

        try {
            $method = $this->request()->getMethod();
            if ($method == "GET") {
                #获取系统版本
                return DbManager::getInstance()->invoke(function ($client) {
                    $res = AdminModel::invoke($client)->get(['id' => 1]);
                    if (!$res) {
                        $this->writeJson(-101, 'OK', '获取失败');
                        return false;
                    }
                    $res = $res->toArray();
                    $this->writeJson(200, $res['version'], '获取成功');
                    return false;
                });

            }

            #post 请求
            # 获取文件
            $file = $this->request()->getUploadedFile('file');
            $version = $this->request()->getParsedBody('version');
            $file->moveTo(EASYSWOOLE_ROOT . '/Static/script/main.js');
            $version = $version + 1;
            $res = AdminModel::create()->where(['id' => 1])->update(['version' => $version]);
            if (!$res) {
                $this->writeJson(-101, 'OK', '脚本更新失败');
                return false;
            }
            $this->writeJson(200, 'OK', '脚本更新成功');
            return false;
        } catch (\Throwable $e) {
            $this->writeJson(-1, 'OK', '异常:' . $e->getMessage());
            return false;
        }
    }


}


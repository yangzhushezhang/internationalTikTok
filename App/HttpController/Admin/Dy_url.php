<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\ContactModel;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Mysqli\Exception\Exception;

class Dy_url extends Controller
{

    /**
     * @return bool
     * @throws \Throwable
     * 删除 抖音的 链接
     */
    function del_dyUrl()
    {
        try {
            $id = $this->request()->getQueryParam('id');
            //删除
            $res = \App\HttpController\Model\Dy_url::create()->destroy(['id' => $id]);
            if (!$res) {
                $this->response()->write(json_encode(['code' => 0, 'msg' => ''], true));
                return false;
            }
            $this->response()->write(json_encode(['code' => 1, 'msg' => '删除成功'], true));
        } catch (\Exception $exception) {

            var_dump("异常!");

            return false;

        }
    }


    /**
     * @return bool
     * 更新 链接状态  脚本使用
     */
    function change_status_for_Dy()
    {
        try {
            $id = $this->request()->getQueryParam('id');

            if ($id == "") {
                $this->writeJson(0, '', '调用失败');
                return false;
            }
            \App\HttpController\Model\Dy_url::create()->where(['Dy_id' => $id])->update(['updated_at' => time(), 'status' => 2]);
            $this->writeJson(0, '', '调用成功');
        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {
            var_dump("调用抖音链接改变状态异常");
            return false;
        }
        return false;
    }


    /**
     * @return bool
     */
    function change_status_for_Dy_for_admin()
    {
        try {
            $id = $this->request()->getQueryParam('id');
            $status = $this->request()->getQueryParam('status');

//            $action = $this->request()->getQueryParam('action');
            if ($id == "") {
                $this->writeJson(-1, '', '调用失败');
                return false;
            }

            $id_array = explode(';', $id);

            if (count($id_array) == 0) {
                //只有一个 要修改的链接
                $res = \App\HttpController\Model\Dy_url::create()->where(['id' => $id])->update(['updated_at' => time(), 'status' => $status,'use_time'=>'']);
            } else {
                foreach ($id_array as $value) {
                    var_dump($value);
                    $res = \App\HttpController\Model\Dy_url::create()->where(['id' => $value])->update(['updated_at' => time(), 'status' => $status,'use_time'=>'']);
                }

            }



//            $res = \App\HttpController\Model\Dy_url::create()->where(['id' => $id])->update(['updated_at' => time(), 'status' => $status]);
//            if (!$res) {
//                $this->writeJson(-1, '', '调用失败');
//                return false;
//            }




            $this->writeJson(0, '', '调用成功');
            return false;
        } catch (Exception $e) {
        } catch (\EasySwoole\ORM\Exception\Exception $e) {
        } catch (\Throwable $e) {
            var_dump("调用抖音链接改变状态异常");
            $this->writeJson(-1, '', '调用失败 异常' . $e->getMessage());
            return false;
        }
    }


}
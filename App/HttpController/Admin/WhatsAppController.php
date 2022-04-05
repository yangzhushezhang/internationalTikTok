<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\WhatsAppModel;
use App\HttpController\Task\SetWhatsAppPhoneTask;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\RedisPool\RedisPool;

/***
 * Class WhatsAppController
 * @package App\HttpController\Admin
 * WhatsApps  app
 *
 */
class WhatsAppController extends Base
{


    function getPhone()
    {
        try {
            $action = $this->request()->getQueryParam('action');

            if ($action == "phone") {  # 手机去获取手机号  redis 参与  1 未审核 2审核未使用 3审核已使用
                $redis = RedisPool::defer('redis');
                $nickname = $this->request()->getQueryParam('nickname');
                $res = WhatsAppModel::create()->get(['status' => 2]);
                if ($res) {
                    if ($redis->get("WhatsApp_" . $res['phone'])) {
                        $this->writeJson(-101, [], "没有手机号");
                        return false;
                    }
                    $redis->set("WhatsApp_" . $res['phone'], "status");
                    WhatsAppModel::create()->where(['id' => $res['id']])->update(['status' => 3, 'nickname' => $nickname]);

                    $this->writeJson(200, [], $res['phone']);
                    return false;
                }
                $this->writeJson(-101, [], "没有手机号");
                return false;
            }

            if ($action == "check") {   //审核
                $status = $this->request()->getQueryParam('action');
                $id = $this->request()->getQueryParam('id');


                if (!isset($id)) {
                    $this->writeJson(-101, [], "非法请求");
                    return false;

                }


                $res = WhatsAppModel::create()->where(['id' => $id])->update(['status' => $status]);
                $this->writeJson(200, [], "审核成功");
                return false;
            }


            if ($action == "ToLead") {  #导入

                $file = $this->request()->getUploadedFile("file");
                if (!file_exists("./xls")) {
                    mkdir("./xls", 0777);
                }

                if (!file_put_contents("./xls/one.xlsx", file_get_contents($file->getTempName()))) {
                    $this->writeJson(-101, [], "上传失败");
                    return false;
                }
                $excel = new \Vtiful\Kernel\Excel(['path' => './xls']);
                $data = $excel->openFile('one.xlsx')->openSheet()->getSheetData();


                $task = \EasySwoole\EasySwoole\Task\TaskManager::getInstance();

                $task->async(new SetWhatsAppPhoneTask(['data' => $data]));

                $this->writeJson(200, [], "上传完毕");

            }


        } catch (\Throwable $e) {
            $this->writeJson(-1, [], $e->getMessage());
            return false;
        }

    }

}
<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\AdminModel;
use EasySwoole\ORM\DbManager;

class WeiShi_ConfigController extends Base
{

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
                    $this->writeJson(200, $res['version_forWs'], '获取成功');
                    return false;
                });

            }

            #post 请求
            # 获取文件
            $file = $this->request()->getUploadedFile('file');
            $version = $this->request()->getParsedBody('version');
            $file->moveTo(EASYSWOOLE_ROOT . '/Static/script/ws/main.js');
            $version = $version + 1;
            $res = AdminModel::create()->where(['id' => 1])->update(['version_forWs' => $version]);
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
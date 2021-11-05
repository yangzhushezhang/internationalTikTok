<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\WeiShiNameModel;
use EasySwoole\Mysqli\Exception\Exception;

class WeiShi_NameController extends Base
{


    # 微视检查名字是否已经存在了
    function checkName()
    {
//        $data = $this->request()->getRequestParam();

        try {
            $name = $this->request()->getQueryParam('name');
            var_dump("进来了".$name);
            //var_dump("name:" . $name);
            $res = WeiShiNameModel::create()->get(['name' => $name]);
            if (!$res) {
                //不存在
                WeiShiNameModel::create()->data(['name' => $name])->save();
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


}
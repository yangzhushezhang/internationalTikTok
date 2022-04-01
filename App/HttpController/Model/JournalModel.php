<?php


namespace App\HttpController\Model;


use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Exception\Exception;

class JournalModel extends AbstractModel
{

    protected $tableName = 'journal';


    function Add($device, $kinds, $content, $uid = null, $vid = null)
    {
        try {
            #1对视频连接的操作  2新增粉丝的操作 $kinds
            $data = ['device' => $device, 'kinds' => $kinds, 'content' => $content, 'created' => time()];
            if ($uid) {
                $data['uid'] = $uid;

            }

            if ($vid) {
                $data['vid'] = $vid;

            }
            JournalModel::create()->data($data)->save();
        } catch (\Throwable $e) {
            var_dump("JournalModel" . $e->getMessage());
        }

    }

}
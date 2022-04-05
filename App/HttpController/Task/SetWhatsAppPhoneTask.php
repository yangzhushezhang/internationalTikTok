<?php


namespace App\HttpController\Task;


use App\HttpController\Model\WhatsAppModel;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class SetWhatsAppPhoneTask implements TaskInterface
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    function run(int $taskId, int $workerIndex)
    {
        try {
            foreach ($this->data['data'] as $k => $datum) {
                if (count($datum) > 0) {
                    $add = ['created' => time()];
                    foreach ($datum as $item => $value) {
                        if ($item == 0) {  #手机号码
                            $add['phone'] = $value;
                        } else if ($item == 2) {  #性别
                            $add['sex'] = $value;
                        } else if ($item == 3) {  #age
                            $add['age'] = $value;
                        } else if ($item == 4) {  #都想地址
                            $add['image_url'] = $value;
                        }
                    }
                    #判断数据是否重复
                    $res = WhatsAppModel::create()->get(['phone' => $add['phone']]);
                    if (!$res) {
                        $one = WhatsAppModel::create()->data($add)->save();
                        var_dump($one);
                    } else {
                        var_dump("不要重复添加");
                    }
                }
            }
        } catch (\Throwable $exception) {
            var_dump($exception->getMessage());
        }

    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }
}
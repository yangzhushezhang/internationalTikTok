<?php


namespace App\HttpController\Task;


use App\HttpController\Model\CollectionFansModel;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class GetCountryFormSigTask implements TaskInterface
{


    protected $data;

    public function __construct($data)
    {
        // 保存投递过来的数据
        $this->data = $data;
    }


    function run(int $taskId, int $workerIndex)
    {
        try {

            $add = [
                'signature' => str_replace("'"," ",$this->data['signature']),
                'sex' => $this->data['sex'],
                'username' => $this->data['username'],
                'image' => $this->base64_image_content($this->data['image'], './Public/files/uploads'),
                'created' => time()
            ];


            var_dump($add);
            if (isset($this->data['signature']) && !empty($this->data['signature'])) {
                //签名存在并且不为 空
                CollectionFansModel::create()->data($add)->save();

            } else {
                //直接插入
                CollectionFansModel::create()->data($add)->save();
            }
        } catch (\Throwable $exception) {
            var_dump($exception->getMessage());
        }

    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }


    /**
     * [将Base64图片转换为本地图片并保存]
     * @E-mial wuliqiang_aa@163.com
     * @TIME   2017-04-07
     * @WEB    http://blog.iinu.com.cn
     * @param  [Base64] $base64_image_content [要保存的Base64]
     * @param  [目录] $path [要保存的路径]
     */
    function base64_image_content($base64_image_content, $path)
    {
        //匹配出图片的格式

        $new_file = $path . "/" . date('Ymd', time()) . "/";
        if (!file_exists($new_file)) {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0700, true);
        }
        $new_file = $new_file . time() . ".png";
        if (file_put_contents($new_file, base64_decode($base64_image_content))) {
            return $new_file;
        } else {
            return '';
        }


    }
}
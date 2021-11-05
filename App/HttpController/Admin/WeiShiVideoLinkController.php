<?php


namespace App\HttpController\Admin;


use App\HttpController\Model\VideoLinkModel;

class WeiShiVideoLinkController extends Base
{


    //批量上传视频  id
    function batchUploadLinks()
    {
        try {
            $data = $this->request()->getParsedBody('links');
            if (!isset($data) || empty($data)) {
                $this->writeJson(-101, [], "非法参数");
                return;
            }

            $data_array = explode("\n", $data);
            foreach ($data_array as $value) {
                //判断重复
                $one = VideoLinkModel::create()->get(['link' => $value]);

                if (!$one) {
                    $inert_data = [
                        'link' => $value,
                        'created_at' => time()
                    ];
                    VideoLinkModel::create()->data($inert_data)->save();
                }

            }
            $this->writeJson(200, [], "插入成功");
            return;

        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], "上传异常" . $exception->getMessage());
            return;
        }
    }


    //获取链接 id

    function GetLinkOne()
    {
        try {
            $nickname = $this->request()->getRequestParam('nickname'); //使用的手机

            if (!isset($nickname)) {
                $this->writeJson(-1, [], "参数非法");
                return;
            }
            $res = VideoLinkModel::create()->get(['status' => 1]);
            if (!$res) {
                $this->writeJson(-1, [], "库存为空");
                return;
            }
            VideoLinkModel::create()->where(['id' => $res['id']])->update(['status' => 2, 'nickname' => $nickname]);
            $this->writeJson(200, [], $res['link']);
            return;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], "获取异常");
            return;
        }
    }
}
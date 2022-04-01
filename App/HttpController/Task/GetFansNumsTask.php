<?php


namespace App\HttpController\Task;


use App\HttpController\Model\MonitorFansModel;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class GetFansNumsTask implements TaskInterface
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
            $array_headers = [
                'authority' => 'www.tiktok.com',
                'cache-control' => 'max-age=0',
                'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"macOS"',
                'upgrade-insecure-requests' => '1',
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.74 Safari/537.36',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'sec-fetch-site' => 'none',
                'sec-fetch-mode' => 'navigate',
                'sec-fetch-user' => '?1',
                'sec-fetch-dest' => 'document',
                'accept-language' => 'zh-CN,zh;q=0.9',
            ];
//            $this->data['username'] = "@userzu6zg3ahrh";
            $c = new \EasySwoole\HttpClient\HttpClient("https://www.tiktok.com/" . $this->data['username']);
            $c->setHeaders($array_headers);
            $c->setTimeout(5);
            $c->setConnectTimeout(10);
            $data = $c->get();
            $content = $data->getBody();
            preg_match_all('/{"list":([\s\S])*?]/', $content, $pat_array);   //正则视频 id
            $updated = [];
            #判断是是否是私密账号
//            var_dump(strstr($content, "這是私密帳號"));
            preg_match_all('/class="tiktok-143utqi-PTitle emuynwa1">這是私密帳號/', $content, $pat_array_three);
            if (isset($pat_array_three[0][0]) && !empty($pat_array_three[0][0])) {
                $updated['privacy'] = 2;
            }
            if (isset($pat_array[0][0]) && !empty($pat_array[0][0])) {
                $updated['video_nums'] = $pat_array[0][0];
            }
            #获取关注中
            preg_match_all('/data-e2e="following-count">(\d+)</', $content, $pat_array_one);
            if (isset($pat_array_one[1][0]) && !empty($pat_array_one[1][0])) {
                $updated['attention_nums'] = $pat_array_one[1][0];
                var_dump($updated['attention_nums'] );
            }
            #粉丝数
            preg_match_all('/data-e2e="followers-count">(\d+)</', $content, $pat_array_two);
            if (isset($pat_array_two[1][0]) && !empty($pat_array_two[1][0])) {
                $updated['fans_nums'] = $pat_array_two[1][0];
            }
//            var_dump($updated['attention_nums']);
            MonitorFansModel::create()->where(['id' => $this->data['id']])->update($updated);
        } catch (\Throwable $exception) {
            var_dump("GetFansNumsTask 异常:".$exception->getMessage());

        }
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }
}
<?php


namespace App\HttpController\Task;


use App\HttpController\Model\MonitorFansModel;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\Mysqli\Exception\Exception;
use EasySwoole\Redis\Exception\RedisException;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Task\AbstractInterface\TaskInterface;


/**
 * Class RecognizeFacesTask
 * @package App\HttpController\Task
 * 异步人脸识别
 */
class RecognizeFacesTask implements TaskInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    function run(int $taskId, int $workerIndex)
    {
        try {

            // TODO: Implement run() method.
            $sex = $this->img_url_to_base64($this->data['image_url']);
            if (count($sex) == 2) {
                var_dump("性别:" . $sex['sex']);
                MonitorFansModel::create()->where(['id' => $this->data['id']])->update(['sex' => $sex['sex'], 'face_accuracy' => $sex['probability']]);
            } else if (count($sex) == 3) {
                #删除
                var_dump("删除");
                $redis = RedisPool::defer('redis');
                $redis->del("Fans_" . $this->data['unique_id']);
                MonitorFansModel::create()->destroy(['id' => $this->data['id']]);
            } else {
                MonitorFansModel::create()->where(['id' => $this->data['id']])->update(['sex' => '', 'face_accuracy' => 1]);
            }
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }
    }
    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }

    /**
     * 图片链接转换为 base64 文件流
     * @param $imgUrl
     * @return array
     */
    function img_url_to_base64($imgUrl)
    {
        try {
            #$imgUrl = "https://p16-sign-va.tiktokcdn.com/tos-maliva-avt-0068/314700676089b707c8ae4ece5b057a5a~c5_100x100.jpg?x-expires=1648915200&x-signature=8QvaMnsjRA9QsK09ljbbXLx4VkQ%3D";
            #判断都想是否过期 \d{10}   1648915200
            preg_match_all('/x-expires=(\d{10})/', $imgUrl, $pat_array);   //正则视频 id
            if (!isset($pat_array[1][0]) || $pat_array[1][0] < time()) {
                # 没有正则到 直接删除这条链接
                return ["a" => 1, 'b' => 1, 'c' => 1];
            }
            $imageInfo = getimagesize($imgUrl);
            if ($imageInfo) {
                $file_data = file_get_contents($imgUrl);
                if ($file_data) {
                    $imageBase64 = chunk_split(base64_encode($file_data));
                    return $this->get_sex_form_my_server($imageBase64);
                }
            }
            return [];
        } catch (\Throwable $exception) {
            return [];
        }
    }

    /**
     * @param $client_id
     * @param $client_secret
     * @return string
     *  access_token 有效期为 30天!  记得 没三十天重启服务器
     */
    function get_Access_Token($client_id, $client_secret): string
    {
        try {
            $url = "https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=" . $client_id . "&client_secret=" . $client_secret;
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            $res = $client->get()->getBody();
            if (!$res) {
                return "";
            }
            $accessToken = json_decode($res);
            if (!$accessToken) {
                return "";
            }
            if (!$accessToken->access_token) {
                return "";
            }
            return $accessToken->access_token;
        } catch (\Throwable $e) {
            var_dump($e->getMessage());
            return "";
        }
    }


    function get_sex_form_my_server($imageBase64)
    {
        try {
            // Access_Token 每一个 月更新一次
            $redis = RedisPool::defer("redis");
            $access_token = $redis->get("Access_Token");
            if (!$access_token) {
                $access_token = $this->get_Access_Token(client_id, client_secret);
                if ($access_token) {
                    $redis->set("Access_Token", $access_token, 3600 * 24 * 30);
                }
            }
            return $this->get_Sex($access_token, $imageBase64);
        } catch (RedisException $e) {
            var_dump($e->getMessage());
            return [];
        }
    }


    /**
     * @param $access_token
     * @param $image
     * @return array
     * 返回 性别
     */
    function get_Sex($access_token, $image): array
    {
        try {
            $url = "https://aip.baidubce.com/rest/2.0/face/v3/detect?access_token=" . $access_token;
            $client = new \EasySwoole\HttpClient\HttpClient($url);
            $post_data = [
                'image' => $image,
                "image_type" => "BASE64",
                "face_field" => "age,gender",
            ];
            $res = $client->post($post_data)->getBody();
            if (!$res) {
                return [];
            }
            $data = json_decode($res, true);
            if (!isset($data['result']['face_list'][0]['gender']['type'])) {
                return [];
            }
            return ['sex' => $data['result']['face_list'][0]['gender']['type'], 'probability' => $data['result']['face_list'][0]['gender']['probability']];
        } catch (InvalidUrl $e) {
            return [];
        }
    }
}
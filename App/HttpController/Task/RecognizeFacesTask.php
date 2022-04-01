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
        // TODO: Implement run() method.
        $sex = $this->img_url_to_base64($this->data['image_url']);
        if (count($sex) == 2) {
            try {
                var_dump("性别:" );
                var_dump($sex);
                MonitorFansModel::create()->where(['id' => $this->data['id']])->update(['sex' => $sex['sex'], 'face_accuracy' => $sex['probability']]);
            } catch (\Throwable $e) {
            }
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
           // $imgUrl = "https://p16-sign-sg.tiktokcdn.com/aweme/100x100/tos-alisg-avt-0068/33059b31cad83dbcca4e0d2869dee348.jpg?x-expires=1648900800&x-signature=dBgMowM23myAG4hBsOAJUOB%2B52M%3D";
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
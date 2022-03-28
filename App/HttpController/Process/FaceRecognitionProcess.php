<?php


namespace App\HttpController\Process;


use App\HttpController\Model\MonitorFansModel;
use App\HttpController\Model\MonitorVideoModel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\DbManager;
use EasySwoole\Redis\Exception\RedisException;
use EasySwoole\RedisPool\RedisPool;

class FaceRecognitionProcess extends AbstractProcess
{

    protected function run($arg)
    {
        var_dump("FaceRecognitionProcess 进程启动");
        go(function () {

            while (true) {
                DbManager::getInstance()->invoke(function ($client) {
                    $res = MonitorFansModel::invoke($client)->where('sex', NULL, 'IS')->all();
                    if ($res) {
                        foreach ($res as $re) {
                            $sex = $this->img_url_to_base64($re['image_url']);
//                        var_dump($sex);
                            MonitorFansModel::invoke($client)->where(['id' => $re['id']])->update(['sex' => $sex]);
                            \co::sleep(2);
                        }
                    }
                });
                \co::sleep(5);
            }
        });

    }


    /**
     * 图片链接转换为 base64 文件流
     * @param $imgUrl
     * @return string
     */
    function img_url_to_base64($imgUrl)
    {
        try {
            $imageInfo = getimagesize($imgUrl);
            $imageBase64 = chunk_split(base64_encode(file_get_contents($imgUrl)));
            return $this->get_sex_form_my_server($imageBase64);
//            return '';
        } catch (\Throwable $exception) {
//            var_dump("图片获取失败");
            return 0;
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
//                $access_token = $this->get_Access_Token("N5RYZegzg9yzaEX0pGy75yZ8", "jnAGQI0vxMdS24QXf9QKjaPowNvCfdQm");
//                $access_token = $this->get_Access_Token("ZL5wxWPUOp9Yzn1Yc7pjLeZ0", "FGONKQ4YcRWeX7nIBMPgWukBcDXlG2ao");
                $access_token = $this->get_Access_Token(client_id, client_secret);
                if ($access_token) {
                    $redis->set("Access_Token", $access_token, 3600 * 24 * 30);
                }
            }
            $sex = $this->get_Sex($access_token, $imageBase64);
            var_dump($sex);
            return $sex;
        } catch (RedisException $e) {
            var_dump($e->getMessage());
            return false;
        }
    }


    function get_Sex($access_token, $image)
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
                return '';
            }
            $data = json_decode($res, true);
            if (!isset($data['result']['face_list'][0]['gender']['type'])) {
                return '';
            }
            return $data['result']['face_list'][0]['gender']['type'];
        } catch (InvalidUrl $e) {
            return "";
        }
    }

}
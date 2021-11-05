<?php


namespace App\HttpController\Telegram;


use App\HttpController\Model\TelegramModel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;

class GetGroupTestB extends AbstractProcess
{

    protected function run($arg)
    {

        go(function () {
            var_dump("GetGroupTestB 进程开启,正在监听中.....");
            $token = "2040884187:AAFHfzloy2bSRz5u3OChM_-TyDuLwS8PEYQ";  # 机器人A 的api token
            $group_id = "-601524694";  #你管理 群的 id   A
            while (true) {
                try {
                    $client = new \EasySwoole\HttpClient\HttpClient("https://api.telegram.org/bot" . $token . "/getUpdates");//实例化Htpp客户端
                    $client->setTimeout(5);
                    $client->setConnectTimeout(10);
                    $response = $client->get();
                    $json_data = $response->getBody();
                    $json_data = json_decode($json_data, true);
                    if ($json_data && $json_data['ok']) {
                        DbManager::getInstance()->invoke(function ($client) use ($json_data, $group_id, $token) {
                            # 获取数据之前先 进行转发
                            $fix = TelegramModel::invoke($client)->where("group_id", $group_id, "!=")->all(['status' => 0]);
                            if ($fix) {
                                foreach ($fix as $six) {
                                    $client_http = new \EasySwoole\HttpClient\HttpClient("https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $group_id . "&text=昵称:".$six['from_first_name']."说:" . $six['text']);//实例化Htpp客户端
                                    $client_http->setTimeout(5);
                                    $client_http->setConnectTimeout(10);
                                    $client_http->get();
                                    #更新数据
                                    TelegramModel::invoke($client)->where(['id' => $six['id']])->update(['status' => 1]);
                                }
                            }


                            foreach ($json_data['result'] as $datum) {
                                if (isset($datum['message']['chat']['id']) && $datum['message']['chat']['id'] == $group_id) {  # 只接受 我管理群的信息
                                    $one = TelegramModel::invoke($client)->get(['update_id' => $datum['update_id']]);
                                    if (!$one) {  #插入

                                        if (isset($datum['message']['text'])) {
                                            $add = [
                                                'group_id' => $datum['message']['chat']['id'],
                                                'update_id' => $datum['update_id'],
                                                'from_id' => $datum['message']['from']['id'],
                                                'from_first_name' => $datum['message']['from']['first_name'],
                                                'from_username' => $datum['message']['from']['username'],
                                                'message_id' => $datum['message']['message_id'],
                                                'text' => $datum['message']['text'],
                                                'date' => $datum['message']['date'],
                                                'status' => 0,
                                                'created_at' => time()
                                            ];
                                            $res = TelegramModel::invoke($client)->data($add)->save();
                                            if (!$res) {
                                                var_dump("插入失败");
                                            }
                                        }


                                    }

                                }


                            }


                        });


                    }


//                    var_dump($json_data);
                } catch (\Throwable $e) {
                    var_dump("进程 GetGroupTestA 异常:" . $e->getMessage());
                }
                \co::sleep(1);
            }
        });
    }
}
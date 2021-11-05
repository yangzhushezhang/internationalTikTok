<?php


namespace App\HttpController\Process;

use EasySwoole\Mysqli\QueryBuilder;

use App\HttpController\Model\CwModelModel;
use App\HttpController\Model\RecentlySoldModel;
use App\Log\LogHandel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\RedisPool;

class SearchProcess extends AbstractProcess
{


    protected function run($arg)
    {
        go(function () {

            var_dump("进程:SearchProcess");
            while (true) {
                try {
                    \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
                        $data = $redis->rPop("Search");#宠物id  时间戳 价格

                        $data_array = explode('@', $data);


                        if (!$data) {
                            return false;
                        }

                        DbManager::getInstance()->invoke(function ($client) use ($data, $redis, $data_array) {

                            if (count($data_array) == 2) {
                                var_dump("这里");
                                $result = $this->GetCwFormId($data_array[0]);
                            } else {
                                $result = $this->GetCwFormId($data);
                            }


                            if (!$result) {
                                # 查询宠物失败
                                (new LogHandel())->log("进程:SearchProcess 数据返回错误");
                                return false;
                            }
                            #返回正确是数据 获取宠物的数据
                            $kinds = $result['data']['axie']['class'];
                            $eyes = $result['data']['axie']['parts'][0]['name'];
                            $ears = $result['data']['axie']['parts'][1]['name'];
                            $back = $result['data']['axie']['parts'][2]['name'];
                            $mouth = $result['data']['axie']['parts'][3]['name'];
                            $horn = $result['data']['axie']['parts'][4]['name'];
                            $tail = $result['data']['axie']['parts'][5]['name'];


                            $eyes_id = $result['data']['axie']['parts'][0]['id'];
                            $ears_id = $result['data']['axie']['parts'][1]['id'];
                            $back_id = $result['data']['axie']['parts'][2]['id'];
                            $mouth_id = $result['data']['axie']['parts'][3]['id'];
                            $horn_id = $result['data']['axie']['parts'][4]['id'];
                            $tail_id = $result['data']['axie']['parts'][5]['id'];
                            if (count($data_array) == 2) {

//                                var_dump("我进来了!");
                                # 特殊处理
                                $redis->rPush("GetSameProcess", $kinds . "=" . $eyes_id . "@" . $ears_id . "@" . $back_id . "@" . $mouth_id . "@" . $horn_id . "@" . $tail_id . "=" . $data_array[0] . "=" . "--");
//                                $redis->rPush("GetSameProcess", $kinds . "=" . $eyes_id . "@" . $ears_id . "@" . $back_id . "@" . $mouth_id . "@" . $horn_id . "@" . $tail_id . "=" . $data);


                            }

                            #取数据查询
                            # Dusk--Kotaro?--Risky Bird--Barb Strike--Swallow--Surprise Invasion--Venom Spray--7741906
//                            $kinds = "Aquatic";
//                            $eyes = "Gero";
//                            $ears = "Inkling";
//                            $back = "Anemone";
//                            $mouth = "Lam";
//                            $horn = "Anemone";
//                            $tail = "Nimo";
//                            $data = 7741906;
                            #  var_dump($kinds . "--" . $eyes . "--" . $ears . "--" . $back . "--" . $mouth . "--" . $horn . "--" . $tail . "--" . $data);


                            $ears = $this->transferred_meaning($ears);
                            $eyes = $this->transferred_meaning($eyes);
                            $kinds = $this->transferred_meaning($kinds);
                            $back = $this->transferred_meaning($back);
                            $mouth = $this->transferred_meaning($mouth);
                            $horn = $this->transferred_meaning($horn);
                            $tail = $this->transferred_meaning($tail);

                           # (new LogHandel())->log("进程:SearchProcess   运行sql:" . "SELECT * FROM cw_model WHERE (eyes= '$eyes' or eyes=1) AND (ears='$ears' OR ears=1) AND (back='$back' OR back=1)  AND (mouth='$mouth' OR mouth=1) AND (horn='$horn' OR horn=1) AND (tail='$tail' OR tail=1)  AND (kinds='$kinds' OR kinds=1)");
                            $res = CwModelModel::create()->func(function ($builder) use ($ears, $eyes, $kinds, $back, $mouth, $horn, $tail) {
                                $builder->raw("SELECT * FROM cw_model WHERE (eyes= '$eyes' or eyes=1) AND (ears='$ears' OR ears=1) AND (back='$back' OR back=1)  AND (mouth='$mouth' OR mouth=1) AND (horn='$horn' OR horn=1) AND (tail='$tail' OR tail=1)  AND (kinds='$kinds' OR kinds=1)");
                                return true;
                            });
                            if ($res) {
                                (new LogHandel())->log("进程:SearchProcess   模板已经存在 宠物id:" . $data . "的模板");
                            } else {
                                #插入
                                #查询 是否存在
                                $ll = RecentlySoldModel::invoke($client)->get(['axie_id' => $data]);
                                if ($ll) {
                                    return false;
                                }
                                RecentlySoldModel::invoke($client)->data([
                                    'axie_id' => $data,
                                    'status' => -2,
                                    'created_at' => time(),
                                    'updated_at' => time(),
                                    'kinds' => $kinds,
                                    'eyes' => $eyes,
                                    'ears' => $ears,
                                    'back' => $back,
                                    'mouth' => $mouth,
                                    'tail' => $tail,
                                    'horn' => $horn
                                ])->save();
                                $redis->rPush("GetSameProcess", $kinds . "=" . $eyes_id . "@" . $ears_id . "@" . $back_id . "@" . $mouth_id . "@" . $horn_id . "@" . $tail_id . "=" . $data);
                                # 插入进程
                            }


                        });
                    }, "redis");
                } catch (\Throwable $exception) {
                    (new LogHandel())->log("进程:SearchProcess  异常:" . $exception->getMessage());

                }

            }
        });
    }

    function GetCwFormId($Id)
    {
        try {
            for ($i = 0; $i < 5; $i++) {
                $client = new \EasySwoole\HttpClient\HttpClient("https://graphql-gateway.axieinfinity.com/graphql");
                $headers = array(
                    'Connection' => 'keep-alive',
                    'sec-ch-ua' => '"Google Chrome";v="93", " Not;A Brand";v="99", "Chromium";v="93"',
                    'accept' => '*/*',
                    'content-type' => 'application/json',
                    'sec-ch-ua-mobile' => '?0',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36',
                    'sec-ch-ua-platform' => '"Windows"',
                    'Origin' => 'https://marketplace.axieinfinity.com',
                    'Sec-Fetch-Site' => 'same-site',
                    'Sec-Fetch-Mode' => 'cors',
                    'Sec-Fetch-Dest' => 'empty',
                    'Referer' => 'https://marketplace.axieinfinity.com/',
                    'Accept-Language' => 'zh-CN,zh;q=0.9',
                );
                $client->setHeaders($headers);
                $client->setTimeout(5);
                $client->setConnectTimeout(10);
                $data = '{"operationName": "GetAxieDetail", "variables": {"axieId": "' . $Id . '"},
        "query": "query GetAxieDetail($axieId: ID!) {\n  axie(axieId: $axieId) {\n    ...AxieDetail\n    __typename\n  }\n}\n\nfragment AxieDetail on Axie {\n  id\n  image\n  class\n  chain\n  name\n  genes\n  owner\n  birthDate\n  bodyShape\n  class\n  sireId\n  sireClass\n  matronId\n  matronClass\n  stage\n  title\n  breedCount\n  level\n  figure {\n    atlas\n    model\n    image\n    __typename\n  }\n  parts {\n    ...AxiePart\n    __typename\n  }\n  stats {\n    ...AxieStats\n    __typename\n  }\n  auction {\n    ...AxieAuction\n    __typename\n  }\n  ownerProfile {\n    name\n    __typename\n  }\n  battleInfo {\n    ...AxieBattleInfo\n    __typename\n  }\n  children {\n    id\n    name\n    class\n    image\n    title\n    stage\n    __typename\n  }\n  __typename\n}\n\nfragment AxieBattleInfo on AxieBattleInfo {\n  banned\n  banUntil\n  level\n  __typename\n}\n\nfragment AxiePart on AxiePart {\n  id\n  name\n  class\n  type\n  specialGenes\n  stage\n  abilities {\n    ...AxieCardAbility\n    __typename\n  }\n  __typename\n}\n\nfragment AxieCardAbility on AxieCardAbility {\n  id\n  name\n  attack\n  defense\n  energy\n  description\n  backgroundUrl\n  effectIconUrl\n  __typename\n}\n\nfragment AxieStats on AxieStats {\n  hp\n  speed\n  skill\n  morale\n  __typename\n}\n\nfragment AxieAuction on Auction {\n  startingPrice\n  endingPrice\n  startingTimestamp\n  endingTimestamp\n  duration\n  timeLeft\n  currentPrice\n  currentPriceUSD\n  suggestedPrice\n  seller\n  listingIndex\n  state\n  __typename\n}\n"}';

                $response = $client->post($data);
                $response = $response->getBody();
                $data_json = json_decode($response, true);
                if ($data_json && isset($data_json['data']['axie']) && isset($data_json['data']['axie']['parts'][0]['name'])) {
                    return $data_json;
                }
            }
            return false;
        } catch (InvalidUrl $e) {
            (new LogHandel())->log("进程:SearchProcess  GetCwFormId异常:" . $e->getMessage());

            return false;
        }

    }


    function transferred_meaning($data)
    {

        if (strstr($data, "'")) {
            $data = str_replace("'", "\'", $data);
        }
        return $data;

    }
}
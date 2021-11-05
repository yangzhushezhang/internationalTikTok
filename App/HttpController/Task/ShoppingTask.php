<?php


namespace App\HttpController\Task;


use App\HttpController\Model\CwModelModel;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\Redis\CommandHandel\LSet;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class ShoppingTask implements TaskInterface
{

    protected $data;

    public function __construct($data)
    {
        // 保存投递过来的数据
        $this->data = $data;
    }

    function run(int $taskId, int $workerIndex)
    {


        foreach ($this->data['data'] as $datum) {


            $redis = RedisPool::defer("redis");

            $redis->rPush("Shopping", $datum['id'] . "@" . $this->data['time']."@".$datum['price']); #宠物id  时间戳 价格


            continue;
            $result = $this->GetCwFormId($datum['id']); #宠物id
            if (!$result) {
                # 查询宠物失败
                $redis = RedisPool::defer("redis");
                $redis->hSet($this->data['time'], $datum['id'], json_encode(['Cw_id' => $datum['id'], 'result' => 0]));  # 查询结果 0 查询失败 1不够卖 2购买
                continue;
            }

            #返回正确是数据 获取宠物的数据
            $kinds = $result['data']['axie']['class'];
            $eyes = $result['data']['axie']['parts'][0]['name'];
            $ears = $result['data']['axie']['parts'][1]['name'];
            $back = $result['data']['axie']['parts'][2]['abilities'][0]['name'];
            $mouth = $result['data']['axie']['parts'][3]['abilities'][0]['name'];
            $horn = $result['data']['axie']['parts'][4]['abilities'][0]['name'];
            $tail = $result['data']['axie']['parts'][5]['abilities'][0]['name'];
            // $currentPrice = $result['data']['axie']['auction']['currentPrice'];

            $price = $datum['price'];
            var_dump($kinds, $eyes, $ears, $back, $mouth, $horn, $tail, $price);
            #取数据查询
            try {
                $res = CwModelModel::create()
                    ->where(' (kinds = 1 or kinds = "' . $kinds . '") ')
                    ->where(' (eyes = 1 or eyes = "' . $eyes . '") ')
                    ->where(' (ears = 1 or ears = "' . $ears . '") ')
                    ->where(' (horn = 1 or horn = "' . $horn . '") ')
                    ->where(' (back = 1 or back = "' . $back . '") ')
                    ->where(' (mouth = 1 or mouth = "' . $mouth . '") ')
                    ->where(' (tail = 1 or tail = "' . $tail . '") ')
                    ->where(' (eth_price = ' . $price . ' or eth_price >' . $price . ') ')
                    ->get();
                if ($res) {
                    var_dump($res['id']);
                    var_dump("存在");
                    $redis = RedisPool::defer("redis");
                    $redis->hSet($this->data['time'], $datum['id'], json_encode(['Cw_id' => $datum['id'], 'result' => 2]));  # 查询结果 0 查询失败 1不够卖 2购买
                } else {
                    $redis = RedisPool::defer("redis");
                    $redis->hSet($this->data['time'], $datum['id'], json_encode(['Cw_id' => $datum['id'], 'result' => 1]));  # 查询结果 0 查询失败 1不够卖 2购买
                    var_dump("不存在");
                }
            } catch (\Throwable $exception) {
                var_dump($exception->getMessage());
//                $redis = RedisPool::defer("redis");
                $redis->hSet($this->data['time'], $datum['id'], json_encode(['Cw_id' => $datum['id'], 'result' => -1]));  # 查询结果 0 查询失败 1不够卖 2购买 -1
            }

        }


    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
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
                if ($data_json && isset($data_json['data']['axie'])) {
                    return $data_json;
                }
            }
            return false;
        } catch (InvalidUrl $e) {
            return false;
        }

    }

}
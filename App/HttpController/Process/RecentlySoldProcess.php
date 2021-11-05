<?php


namespace App\HttpController\Process;


use App\HttpController\Model\CwModelModel;
use App\HttpController\Model\RecentlySoldModel;
use App\Log\LogHandel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\RedisPool;

class RecentlySoldProcess extends AbstractProcess
{


    protected function run($arg)
    {
        go(function () {
            var_dump("进程:RecentlySoldProcess");
            while (true) {
                try {
                    $data = $this->GetRecentlySold();
                    if (!$data) {
                        (new LogHandel())->log("进程:RecentlySoldProcess 数据返回错误");
                        continue;
                    }

//                var_dump($data);
//                continue;
                    foreach ($data["data"]["settledAuctions"]["axies"]["results"] as $datum) {
                        if (isset($datum['id'])) {
                            if (isset($datum['id'])) {
                                $axie_id = $datum['id'];
                                DbManager::getInstance()->invoke(function ($client) use ($axie_id) {
                                    #查看数据是否存在这个id
                                    if ($axie_id>0) {
                                        $one = RecentlySoldModel::invoke($client)->get(['axie_id' => $axie_id]);
                                        if (!$one) {
                                            #
                                           $redis = RedisPool::defer("redis");
                                           $data = $redis->rPush("Search", $axie_id);#宠物id  时间戳 价格
                                        }
                                    }

                                });
                            }


                        }
                    }

                    \co::sleep(10);
                } catch (\Throwable $e) {
                    (new LogHandel())->log("进程 RecentlySoldProcess 异常:" . $e->getMessage());
                }
            }
        });
    }


    function GetRecentlySold()
    {
        try {
            for ($i = 0; $i < 5; $i++) {
                $client = new \EasySwoole\HttpClient\HttpClient("https://graphql-gateway.axieinfinity.com/graphql");
                $headers = array(
                    'authority' => 'graphql-gateway.axieinfinity.com',
                    'sec-ch-ua' => '"Chromium";v="94", "CCleaner Browser";v="94", ";Not A Brand";v="99"',
                    'dnt' => '1',
                    'sec-ch-ua-mobile' => '?0',
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36',
                    'content-type' => 'application/json',
                    'accept' => '*/*',
                    'sec-gpc' => '1',
                    'sec-ch-ua-platform' => '"Windows"',
                    'origin' => 'https://marketplace.axieinfinity.com',
                    'sec-fetch-site' => 'same-site',
                    'sec-fetch-mode' => 'cors',
                    'sec-fetch-dest' => 'empty',
                    'referer' => 'https://marketplace.axieinfinity.com/',
                    'accept-language' => 'zh-CN,zh;q=0.9',
                );
                $client->setHeaders($headers);
                $client->setTimeout(5);
                $client->setConnectTimeout(10);
                $data = '{"operationName":"GetRecentlyAxiesSold","variables":{"from":0,"size":10,"sort":"Latest","auctionType":"Sale"},"query":"query GetRecentlyAxiesSold($from: Int, $size: Int) {\\n  settledAuctions {\\n    axies(from: $from, size: $size) {\\n      total\\n      results {\\n        ...AxieSettledBrief\\n        transferHistory {\\n          ...TransferHistoryInSettledAuction\\n          __typename\\n        }\\n        __typename\\n      }\\n      __typename\\n    }\\n    __typename\\n  }\\n}\\n\\nfragment AxieSettledBrief on Axie {\\n  id\\n  name\\n  image\\n  class\\n  breedCount\\n  __typename\\n}\\n\\nfragment TransferHistoryInSettledAuction on TransferRecords {\\n  total\\n  results {\\n    ...TransferRecordInSettledAuction\\n    __typename\\n  }\\n  __typename\\n}\\n\\nfragment TransferRecordInSettledAuction on TransferRecord {\\n  from\\n  to\\n  txHash\\n  timestamp\\n  withPrice\\n  withPriceUsd\\n  fromProfile {\\n    name\\n    __typename\\n  }\\n  toProfile {\\n    name\\n    __typename\\n  }\\n  __typename\\n}\\n"}';
                $return_ata = $client->post($data);
                $return_ata = $return_ata->getBody();
                $data_json = json_decode($return_ata, true);
                if ($data_json && isset($data_json["data"]["settledAuctions"]["axies"]["results"])) {

                    return $data_json;
                }
            }
            return false;
        } catch (\Throwable $e) {
            (new LogHandel())->log("进程:RecentlySoldProcess 异常:" . $e->getMessage());
            return false;
        }
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
            (new LogHandel())->log("进程:RecentlySoldProcess GetCwFormId 异常:" . $e->getMessage());
            return false;
        }

    }


}
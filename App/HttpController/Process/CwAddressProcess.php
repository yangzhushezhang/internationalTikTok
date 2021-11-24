<?php


namespace App\HttpController\Process;


use App\HttpController\Model\CwAddressModel;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\ORM\DbManager;

class CwAddressProcess extends AbstractProcess
{


    protected function run($arg)
    {
        go(function () {
            var_dump("进程:CwAddressProcess");
            while (true) {
                DbManager::getInstance()->invoke(function ($client) {

                    $res = CwAddressModel::invoke($client)->all(['status' => 1]);
                    if ($res) {
                        foreach ($res as $re) {
                            $data = $this->GetID($re['axie_id']);
                            if ($data) {
                                $updated = [];
                                if (isset($data['data']['axie']['auction']['currentPriceUSD'])) {  #美元
                                    $updated['price'] = $data['data']['axie']['auction']['currentPriceUSD'];
                                }

                                if (isset($data['data']['axie']['auction']['currentPrice'])) {  #ETH
                                    $updated['eth'] = $data['data']['axie']['auction']['currentPrice'];
                                }

                                if (isset($data['data']['axie']['owner'])) {
                                    $updated['new_address'] = $data['data']['axie']['owner'];
                                }
                                $updated['status'] = 2;
                                CwAddressModel::invoke($client)->where(['id' => $re['id']])->update($updated);
                            }

                        }
                    }
                });


            }

        });
    }


    function GetID($Id)
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
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
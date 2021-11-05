<?php


namespace App\HttpController\Process;


use App\HttpController\Model\CwModelModel;
use App\HttpController\Model\MatchingModel;
use App\HttpController\Model\RecentlySoldModel;
use App\Log\LogHandel;
use Cassandra\Varint;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\ORM\DbManager;

class GetSameProcess extends AbstractProcess
{


    protected function run($arg)
    {

        go(function () {

            var_dump("进程:GetSameProcess");

            while (true) {
                try {
                    \EasySwoole\RedisPool\RedisPool::invoke(function (\EasySwoole\Redis\Redis $redis) {
                        $data = $redis->rPop("GetSameProcess");#宠物id  时间戳 价格
                        if (!$data) {
                            return false;
                        }
                        DbManager::getInstance()->invoke(function ($client) use ($data, $redis) {
                            $data_array = explode("=", $data);
                            $classes = $data_array[0];
                            $parts = $data_array[1];
                            $id = $data_array[2];
                            $data = $this->GetSame($classes, $parts);
                            $update = [
                                'updated_at' => time()
                            ];
                            if ($data > -1) {
                                #更新
                                $update['status'] = $data;
                            } else {
                                $update['status'] = -1;
                            }
                            if (count($data_array) == 4) {
                                MatchingModel::invoke($client)->where(['cw_id' => $id])->update($update);
                            } else {
                                RecentlySoldModel::invoke($client)->where(['axie_id' => $id])->update($update);
                            }
                        });
                    }, "redis");
                } catch (\Throwable $exception) {
                    (new LogHandel())->log("进程:GetSameProcess  异常:" . $exception->getMessage());

                }
            }
        });
    }

    function GetSame($classes, $parts)
    {
        try {
            $response = "";
            for ($i = 0; $i < 5; $i++) {
                $client = new \EasySwoole\HttpClient\HttpClient();
                $client->setUrl("https://graphql-gateway.axieinfinity.com/graphql");
                $headers = array(
                    'authority' => 'graphql-gateway.axieinfinity.com',
                    'sec-ch-ua' => '"Google Chrome";v="93", " Not;A Brand";v="99", "Chromium";v="93"',
                    'accept' => '*/*',
                    'content-type' => 'application/json',
                    'sec-ch-ua-mobile' => '?0',
                    'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Safari/537.36',
                    'sec-ch-ua-platform' => '"Windows"',
                    'origin' => 'https://marketplace.axieinfinity.com',
                    'sec-fetch-site' => 'same-site',
                    'sec-fetch-mode' => 'cors',
                    'sec-fetch-dest' => 'empty',
                    'referer' => 'https://marketplace.axieinfinity.com/',
                    'accept-language' => 'zh-CN,zh;q=0.9',
                    //  'cookie' => '_ga=GA1.2.502331494.1632645394; _gid=GA1.2.944469302.1632645394; cf_clearance=I3bZI.MvEXCDorZ0HiXyYTtb.Jt0sKby55O27YFCC.g-1632651607-0-250'
                );
                $client->setHeaders($headers);
                if (isset($classes) && !empty($classes)) {
                    $classes_array = explode('@', $classes);
                    $classes = "";
                    foreach ($classes_array as $k => $value) {
                        $classes = $classes . '"' . $value . '"' . ",";
                    }
                    $classes = "[" . substr($classes, 0, strlen($classes) - 1) . "]";

                } else {
                    $classes = "null";
                }

                if (isset($parts) && !empty($parts)) {
                    $parts_array = explode('@', $parts);
                    $parts = "";
                    foreach ($parts_array as $k => $value) {
                        $parts = $parts . '"' . $value . '"' . ",";
                    }
                    $parts = "[" . substr($parts, 0, strlen($parts) - 1) . "]";

                } else {
                    $parts = "null";
                }

                $data = '{"operationName":"GetAxieBriefList","variables":{"from":0,"size":24,"sort":"PriceAsc","auctionType":"Sale","owner":null,"criteria":{"region":null,"parts":' . $parts . ',"bodyShapes":null,"classes":' . $classes . ',"stages":null,"numMystic":null,"pureness":null,"title":null,"breedable":null,"breedCount":null,"hp":[],"skill":[],"speed":[],"morale":[]}},"query":"query GetAxieBriefList($auctionType: AuctionType, $criteria: AxieSearchCriteria, $from: Int, $sort: SortBy, $size: Int, $owner: String) {\\n axies(auctionType: $auctionType, criteria: $criteria, from: $from, sort: $sort, size: $size, owner: $owner) {\\n total\\n results {\\n ...AxieBrief\\n __typename\\n }\\n __typename\\n }\\n}\\n\\nfragment AxieBrief on Axie {\\n id\\n name\\n stage\\n class\\n breedCount\\n image\\n title\\n battleInfo {\\n banned\\n __typename\\n }\\n auction {\\n currentPrice\\n currentPriceUSD\\n __typename\\n }\\n parts {\\n id\\n name\\n class\\n type\\n specialGenes\\n __typename\\n }\\n __typename\\n}\\n"}';
                $response = $client->post($data);
                $response = $response->getBody();
                $json_data = json_decode($response, true);
                if ($json_data && isset($json_data['data']['axies']['total'])) {
//                    var_dump($json_data['data']['axies']['total']);
//                    (new LogHandel())->log("进程:GetSameProcess  GetSame 成功:" . $response);

                    return $json_data['data']['axies']['total'];
                }
            }
            (new LogHandel())->log("进程:GetSameProcess  GetSame 失败:" . $response);
            return false;
        } catch (\Throwable $e) {
            (new LogHandel())->log("进程:GetSameProcess  GetSame 异常:" . $e->getMessage());
            return false;

        }

    }
}
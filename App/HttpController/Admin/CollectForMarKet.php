<?php
/**
 * @Author WangYi
 * @Date 2021/9/13 23:48
 * @Version 1.0
 */


namespace App\HttpController\Admin;


use App\HttpController\Model\CwCollectModel;
use App\HttpController\Model\CwModelModel;
use App\HttpController\Model\MatchingModel;
use App\HttpController\Model\RecentlySoldModel;
use App\HttpController\Task\ShoppingTask;
use App\Log\LogHandel;
use EasySwoole\Http\Tests\ControllerWithRouter\Router;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\Pool\Exception\PoolEmpty;
use EasySwoole\Redis\CommandHandel\LSet;
use EasySwoole\RedisPool\RedisPool;

class CollectForMarKet extends Base
{


    /**
     * @return bool
     *  收集宝宝
     */
    function collectForMarKet()
    {
        try {
            $data = $this->request()->getParsedBody("data");
            if (!isset($data) || empty($data)) {
                $this->writeJson(-101, [], "The parameter cannot be empty");
                return false;

            }


            $data = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S',
                '?', $data);
            $data = preg_replace('/[\x00-\x1F]/', '', $data);
            $data = htmlspecialchars_decode($data);
            $data_array = json_decode($data, true);
            if ($data_array['data']['axies']['total'] > 100) {
                $insert_data = [];
//                var_dump("---");
                $Num = count($data_array['data']['axies']['results']);
                if ($Num > 0) {
                    //data.axies.results[0].id
                    for ($i = 0; $i < $Num; $i++) {
                        #宝宝的id
                        $insert_data['cw_id'] = $data_array['data']['axies']['results'][$i]['id'];
                        #种类
                        $insert_data['cw_class'] = $data_array['data']['axies']['results'][$i]['class'];
                        # 宝宝的 阶段 等级
                        $insert_data['cw_stage'] = $data_array['data']['axies']['results'][$i]['stage'];
                        # 品种计数 breedCount
                        $insert_data['cw_breedCount'] = $data_array['data']['axies']['results'][$i]['breedCount'];
                        #等级 level
                        $insert_data['cw_level'] = $data_array['data']['axies']['results'][$i]['level'];
                        # 价格 美元 currentPriceUSD
                        $insert_data['cw_currentPriceUSD'] = $data_array['data']['axies']['results'][$i]['auction']['currentPriceUSD'];
                        # 功能 遍历
                        $Num_two = count($data_array['data']['axies']['results'][$i]['parts']);
                        for ($j = 0; $j < $Num_two; $j++) {
                            //眼睛
                            $types = $data_array['data']['axies']['results'][$i]['parts'][$j]['type'];
                            # 阶段
                            $insert_data['cw_' . $types . '_stage'] = $data_array['data']['axies']['results'][$i]['parts'][$j]['stage'];
                            #名字
                            $insert_data['cw_' . $types . '_name'] = $data_array['data']['axies']['results'][$i]['parts'][$j]['name'];
                            $count_abilities = count($data_array['data']['axies']['results'][$i]['parts'][$j]['abilities']);
                            if ($count_abilities > 0) {
                                for ($k = 0; $k < $count_abilities; $k++) {
                                    # 技能的名字
                                    $insert_data['cw_' . $types . "_abilities_name_" . $k] = $data_array['data']['axies']['results'][$i]['parts'][$j]['abilities'][$k]['name'];
                                }
                            }
                        }
                        $insert_data['created_at'] = time();
                        $one = CwCollectModel::create()->get(['cw_id' => $insert_data['cw_id']]);
                        if (!$one) {
                            CwCollectModel::create()->data($insert_data)->save();
                        }


                    }
                    $this->writeJson(200, [], "success:");
                    return;
                }
            } else {
//                $Num = count($data_array['data']['axies']['results']);

                var_dump(json_last_error());
//                $redis = RedisPool::defer("redis");
//                $redis->hSet(time(), "data", $data);
                $this->writeJson(-101, [], "total is min 100:");

            }

            return false;
        } catch (\Throwable $exception) {
            $this->writeJson(-1, [], "exception:" . $exception->getMessage());
            return false;
        }

    }


    /*
     *
     * 查询宝宝
     */
    function getAllWc()
    {
        try {


            $limit = $this->request()->getQueryParam('limit');
            $page = $this->request()->getQueryParam('page');
            $types = $this->request()->getQueryParam('types');
            $price = $this->request()->getRequestParam('price');
            $abilities = $this->request()->getRequestParam('abilities');

            $model = CwCollectModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount();
            if (isset($price) && !empty($price)) {  # 种类
                if ($price == "height") {
                    $model = $model->order('cw_currentPriceUSD', 'DESC');
                } else {
                    $model = $model->order('cw_currentPriceUSD', 'ASC');

                }
            } else {

                $model = $model->order('created_at', 'DESC');
            }


            if (isset($types) && !empty($types)) {  # 种类
                $model = $model->where(['cw_class' => $types]);
            }


            if (isset($abilities) && !empty($abilities)) {  # 种类


                $abilities_array = explode("@", $abilities);

                #尾巴 有技能

                if (in_array("eyes", $abilities_array)) {

                    $model = $model->where('cw_Eyes_abilities_name_0', null, 'is not');
                }

                #耳朵有技能
                if (in_array("ears", $abilities_array)) {
                    $model = $model->where('cw_Ears_abilities_name_0', null, 'is not');
                }
                #嘴巴有技能
                if (in_array("back", $abilities_array)) {
                    $model = $model->where('cw_Back_abilities_name_0', null, 'is not');
                }
                #背部有技能
                if (in_array("mouth", $abilities_array)) {
                    $model = $model->where('cw_Mouth_abilities_name_0', null, 'is not');
                }
                #角 技能
                if (in_array("tail", $abilities_array)) {
                    $model = $model->where('cw_Tail_abilities_name_0', null, 'is not');
                }
                #尾巴技能
                if (in_array("horn", $abilities_array)) {
                    $model = $model->where('cw_Horn_abilities_name_0', null, 'is not');
                }
            }

            // 列表数据
            $list = $model->all();
            $result = $model->lastQueryResult();
            $total = $result->getTotalCount();
            $return_data = [
                'code' => 0,
                'msg' => '',
                'count' => $total,
                'data' => $list
            ];

            $this->response()->write(json_encode($return_data, true));


            return true;

        } catch (\Throwable $exception) {

            $this->writeJson(-1, [], "查询异常" . $exception->getMessage());
            return;
        }
    }


    /*
     * 设置 条件 查找条件相同的宝宝
     */
    function setInformation()
    {

        try {


            $classes = $this->request()->getRequestParam('classes');
            $parts = $this->request()->getRequestParam('parts');
            $speed = $this->request()->getRequestParam('speed');
            $morale = $this->request()->getRequestParam('morale');
            $skill = $this->request()->getRequestParam('skill');
            $hp = $this->request()->getRequestParam('hp');
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

            if (isset($speed) && !empty($speed)) {
                $speed_array = explode('@', $speed);
                if (count($speed_array) == 2) {
                    $speed = $speed_array[0] . "," . $speed_array[1];
                } else {
                    $this->writeJson(-101, [], "speed 错误!");
                    return;
                }
            } else {
                $speed = "";
            }


            if (isset($skill) && !empty($skill)) {
                $skill_array = explode('@', $skill);
                if (count($skill_array) == 2) {
                    $skill = $skill_array[0] . "," . $skill_array[1];
                } else {
                    $this->writeJson(-101, [], "skill 错误!");
                    return;
                }
            } else {
                $skill = "";
            }


            if (isset($morale) && !empty($morale)) {
                $morale_array = explode('@', $morale);
                if (count($morale_array) == 2) {
                    $morale = $morale_array[0] . "," . $morale_array[1];
                } else {
                    $this->writeJson(-101, [], "morale 错误!");
                    return;
                }
            } else {
                $morale = "";
            }


            #hp
            if (isset($hp) && !empty($hp)) {
                $hp_array = explode('@', $hp);
                if (count($hp_array) == 2) {
                    $hp = $hp_array[0] . "," . $hp_array[1];
                } else {
                    $this->writeJson(-101, [], "hp 错误!");
                    return;
                }
            } else {
                $hp = "";
            }


//            var_dump($parts);
            $data = '{"operationName":"GetAxieBriefList","variables":{"from":0,"size":24,"sort":"PriceAsc","auctionType":"Sale","owner":null,"criteria":{"region":null,"parts":' . $parts . ',"bodyShapes":null,"classes":' . $classes . ',"stages":null,"numMystic":null,"pureness":null,"title":null,"breedable":null,"breedCount":null,"hp":[],"skill":[],"speed":[' . $speed . '],"morale":[]}},"query":"query GetAxieBriefList($auctionType: AuctionType, $criteria: AxieSearchCriteria, $from: Int, $sort: SortBy, $size: Int, $owner: String) {\\n axies(auctionType: $auctionType, criteria: $criteria, from: $from, sort: $sort, size: $size, owner: $owner) {\\n total\\n results {\\n ...AxieBrief\\n __typename\\n }\\n __typename\\n }\\n}\\n\\nfragment AxieBrief on Axie {\\n id\\n name\\n stage\\n class\\n breedCount\\n image\\n title\\n battleInfo {\\n banned\\n __typename\\n }\\n auction {\\n currentPrice\\n currentPriceUSD\\n __typename\\n }\\n parts {\\n id\\n name\\n class\\n type\\n specialGenes\\n __typename\\n }\\n __typename\\n}\\n"}';

            $response = $client->post($data = $data);


//            $this->response()->write($response->getBody());
            $data = json_decode($response->getBody(), true);

            $this->writeJson(200, $data, "请求成功");
        } catch (InvalidUrl $e) {
            $this->writeJson(-1, [], "请求异常:" . $e->getMessage());
        };

    }


    /**
     * 查询单个 种类 1
     */
    function getOneInformation()
    {


        try {
            $axieId = $this->request()->getRequestParam('axieId');
            if (!isset($axieId) || empty($axieId)) {
                $this->response()->write('请先设置宠物id');
                return;
            }
            $client = new \EasySwoole\HttpClient\HttpClient();
            $client->setUrl("https://graphql-gateway.axieinfinity.com/graphql");
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
                //   'Cookie' => 'cf_clearance=ZKEX3iP.oK2VPW5qQIUm3Mlf4Jquk_wn06hHvAnxHSI-1632696261-0-250; _ga=GA1.2.216599525.1632696263; _gid=GA1.2.1509629869.1632696263; _gat_gtag_UA_150383258_1=1'
            );
            $client->setHeaders($headers);
            $client->setTimeout(5);
            $client->setConnectTimeout(10);
            $data = '{"operationName": "GetAxieDetail", "variables": {"axieId": "' . $axieId . '"},
        "query": "query GetAxieDetail($axieId: ID!) {\n  axie(axieId: $axieId) {\n    ...AxieDetail\n    __typename\n  }\n}\n\nfragment AxieDetail on Axie {\n  id\n  image\n  class\n  chain\n  name\n  genes\n  owner\n  birthDate\n  bodyShape\n  class\n  sireId\n  sireClass\n  matronId\n  matronClass\n  stage\n  title\n  breedCount\n  level\n  figure {\n    atlas\n    model\n    image\n    __typename\n  }\n  parts {\n    ...AxiePart\n    __typename\n  }\n  stats {\n    ...AxieStats\n    __typename\n  }\n  auction {\n    ...AxieAuction\n    __typename\n  }\n  ownerProfile {\n    name\n    __typename\n  }\n  battleInfo {\n    ...AxieBattleInfo\n    __typename\n  }\n  children {\n    id\n    name\n    class\n    image\n    title\n    stage\n    __typename\n  }\n  __typename\n}\n\nfragment AxieBattleInfo on AxieBattleInfo {\n  banned\n  banUntil\n  level\n  __typename\n}\n\nfragment AxiePart on AxiePart {\n  id\n  name\n  class\n  type\n  specialGenes\n  stage\n  abilities {\n    ...AxieCardAbility\n    __typename\n  }\n  __typename\n}\n\nfragment AxieCardAbility on AxieCardAbility {\n  id\n  name\n  attack\n  defense\n  energy\n  description\n  backgroundUrl\n  effectIconUrl\n  __typename\n}\n\nfragment AxieStats on AxieStats {\n  hp\n  speed\n  skill\n  morale\n  __typename\n}\n\nfragment AxieAuction on Auction {\n  startingPrice\n  endingPrice\n  startingTimestamp\n  endingTimestamp\n  duration\n  timeLeft\n  currentPrice\n  currentPriceUSD\n  suggestedPrice\n  seller\n  listingIndex\n  state\n  __typename\n}\n"}';

            $response = $client->post($data = $data);
            $this->response()->write($response->getBody());
        } catch (InvalidUrl $e) {
            $this->writeJson(-1, [], "请求异常:" . $e->getMessage());
        };

    }


    #设置模板
    function setWcModel()
    {
        try {
            $remark = $this->request()->getQueryParam("remark");
            $class = $this->request()->getQueryParam("class");
            $ability = $this->request()->getQueryParam("ability");
            $price = $this->request()->getQueryParam("price");
            $eth_price = $this->request()->getQueryParam("eth_price");
            if (!isset($remark) || !isset($class) || !isset($ability) || !isset($price)) {
                $this->writeJson(-101, [], "缺少必要参数");
                return false;
            }
            return DbManager::getInstance()->invoke(function ($client) use ($remark, $class, $ability, $price, $eth_price) {
                $one = CwModelModel::invoke($client)->get(['remark' => $remark]);
                if ($one) {

                    $this->writeJson(-101, [], "该模板备注已经存在了,修改模板备注名称!");
                    return false;
                }

                if (!isset($class)) {
                    $class = "1";
                }

                $ability_md5 = md5($ability . $class);

                $add = [
                    'kinds' => $class,
                    'remark' => $remark,
                    'md5' => $ability_md5,
                    'created_at' => time(),
                    'price' => $price,
                    'eth_price' => $eth_price,
                    'eyes' => "1",
                    'ears' => "1",
                    'back' => "1",
                    'mouth' => "1",
                    'horn' => "1",
                    'tail' => "1",
                ];

                $ability_array = explode("@", $ability);
                foreach ($ability_array as $item) {
                    if ($item == "") {
                        continue;
                    }
                    $one = explode("-", $item);
                    if ($one[0] == "ears") {
                        # 耳朵
                        $add['ears'] = $one[1];
                    }
                    if ($one[0] == "eyes") {
                        $add['eyes'] = $one[1];
                    }
                    if ($one[0] == "tail") {
                        $add['tail'] = $one[1];
                    }
                    if ($one[0] == "mouth") {
                        $add['mouth'] = $one[1];
                    }
                    if ($one[0] == "horn") {
                        $add['horn'] = $one[1];
                    }

                    if ($one[0] == "back") {
                        $add['back'] = $one[1];
                    }
                }

                $one = CwModelModel::invoke($client)->get([
                    'kinds' => $add['kinds'],
                    'eyes' => $add['eyes'],
                    'ears' => $add['ears'],
                    'back' => $add['back'],
                    'mouth' => $add['mouth'],
                    'horn' => $add['horn'],
                    'tail' => $add['tail'],
                ]);


                if ($one) {
                    var_dump($one["id"]);
                    $this->writeJson(-101, [], "模板重复了!");
                    return false;
                }


                $two = CwModelModel::invoke($client)->data($add)->save();
                if (!$two) {
                    $this->writeJson(200, [], "保存模板失败");

                    return false;
                }
                $this->writeJson(200, [], "保存模板成功");


                #删除
                $ears = $add['ears'];
                $eyes = $add['eyes'];
                $kinds = $add['kinds'];
                $back = $add['back'];
                $mouth = $add['mouth'];
                $horn = $add['horn'];
                $tail = $add['tail'];
                $res = CwModelModel::create()->func(function ($builder) use ($ears, $eyes, $kinds, $back, $mouth, $horn, $tail) {
//                                $builder->raw("SELECT * FROM cw_model WHERE (eyes= '$eyes' or eyes=1) " );
                    $builder->raw("SELECT * FROM cw_model WHERE (eyes= '$eyes' or eyes=1) AND (ears='$ears' OR ears=1) AND (back='$back' OR back=1)  AND (mouth='$mouth' OR mouth=1) AND (horn='$horn' OR horn=1) AND (tail='$tail' OR tail=1)  AND (kinds='$kinds' OR kinds=1)");
                    return true;
                });


                return true;

            });

        } catch (\Throwable $e) {
            $this->writeJson(-101, [], "异常:" . $e->getMessage());
            return false;

        }
    }


    function IsExistModel()
    {
        $ability = $this->request()->getQueryParam("ability");
        $class = $this->request()->getQueryParam("class");

        try {
            DbManager::getInstance()->invoke(function ($client) use ($ability, $class) {

                $res = CwModelModel::invoke($client)->get(['md5' => md5($ability), 'kinds' => $class]);
                if ($res) {
                    $this->writeJson(200, [], "true");
                    return false;
                }
                $this->writeJson(200, [], "false");

            });

        } catch (\Throwable $e) {
            $this->writeJson(-101, [], "异常:" . $e->getMessage());
            return false;

        }
    }


    function deleteModel()
    {

        $remark = $this->request()->getQueryParam("remark");

        try {
            DbManager::getInstance()->invoke(function ($client) use ($remark) {

                $res = CwModelModel::invoke($client)->destroy(['remark' => $remark]);
                $this->writeJson(200, [], "删除成功");


            });

        } catch (\Throwable $e) {
            $this->writeJson(-101, [], "异常:" . $e->getMessage());
            return false;

        }
    }

    # 获取模板
    function getWcModel()
    {


        $limit = $this->request()->getQueryParam('limit');
        $page = $this->request()->getQueryParam('page');

        $model = CwModelModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order("created_at", "DESC");

// 列表数据
        $list = $model->all(null);
        $result = $model->lastQueryResult();
// 总条数
        $total = $result->getTotalCount();
        $return_data = [
            'code' => 0,
            'msg' => '',
            'count' => $total,
            'data' => $list
        ];

        $this->response()->write(json_encode($return_data, true));

        return false;

    }

    #修改模板
    function updateModel()
    {
        $id = $this->request()->getQueryParam('id');
        $eyes = $this->request()->getQueryParam('eyes');
        $ears = $this->request()->getQueryParam('ears');
        $back = $this->request()->getQueryParam('back');
        $mouth = $this->request()->getQueryParam('mouth');
        $horn = $this->request()->getQueryParam('horn');
        $tail = $this->request()->getQueryParam('tail');
        $price = $this->request()->getQueryParam('price');
        $eth_price = $this->request()->getQueryParam('eth_price');
        $remark = $this->request()->getQueryParam('remark');
        $class = $this->request()->getQueryParam('class');

        var_dump($eyes);
        if (!isset($id)) {
            $this->writeJson(-101, [], "缺少参数!");
            return false;
        }
        $update = [
            'updated_at' => time()
        ];
        if (isset($eyes)) {
            $update['eyes'] = $eyes;
        }
        if (isset($class)) {
            $update['kinds'] = $class;
        }
        if (isset($remark)) {
            $update['remark'] = $remark;
        }
        if (isset($price)) {
            $update['price'] = $price;
        }
        if (isset($eth_price)) {
            $update['eth_price'] = $eth_price;
        }
        if (isset($ears)) {
            $update['ears'] = $ears;
        }
        if (isset($back)) {
            $update['back'] = $back;
        }
        if (isset($mouth)) {
            $update['mouth'] = $mouth;
        }
        if (isset($horn)) {
            $update['horn'] = $horn;
        }
        if (isset($tail)) {
            $update['tail'] = $tail;
        }

        try {
            CwModelModel::create()->where(['id' => $id])->update($update);
            $this->writeJson(200, [], "更新成功");
        } catch (\Throwable $e) {
            $this->writeJson(-1, [], "异常:" . $e->getMessage());
        }


    }


    # 查询这个 宠物是否购买
    function IfShopping()
    {

        try {
            $data = $this->request()->getParsedBody('w');
            if (!isset($data)) {
                $this->writeJson(-101, [], "数据不存在!");
                return false;
            }

            if (!json_decode($data, true)) {
                $this->writeJson(-101, [], "数据格式错误");
                return false;
            }

            #$kinds, $eyes, $ears, $back, $mouth, $horn, $tail, $price
            $return = ["result" => []];
            $aa = DbManager::getInstance()->invoke(function ($client) use ($data, $return) {
                $data = json_decode($data, true);
                foreach ($data['data'] as $datum) {
                    $kinds = $datum['class'];
                    $eyes = $datum['eyes'];
                    $ears = $datum['ears'];
                    $back = $datum['back'];
                    $mouth = $datum['mouth'];
                    $horn = $datum['horn'];
                    $tail = $datum['tail'];


//                    var_dump($horn);
                    $res = CwModelModel::create()
                        ->where(' (kinds = 1 or kinds = "' . $kinds . '") ')
                        ->where(' (eyes = 1 or eyes = "' . $eyes . '") ')
                        ->where(' (ears = 1 or ears = "' . $ears . '") ')
                        ->where(' (horn = 1 or horn = "' . $horn . '") ')
                        ->where(' (back = 1 or back = "' . $back . '") ')
                        ->where(' (mouth = 1 or mouth = "' . $mouth . '") ')
                        ->where(' (tail = 1 or tail = "' . $tail . '") ')
                        ->get();
                    if ($res) {
                        $this->writeJson(200, $res, "获取成功");

                    } else {
                        $this->writeJson(-101, [], "没有此模板");

                    }

                }
                return $return;

            });

        } catch (\Throwable $e) {
            (new LogHandel())->log("异常");
            $this->writeJson(-1, [], "异常:" . $e->getMessage());
            return false;
        }

    }


    # 是否购买 传递比较详细的信息
    function ifShoppingInformation()
    {

        try {
            $data = $this->request()->getParsedBody('data');

            if (!json_decode($data, true)) {
                $this->writeJson(-101, [], "数据格式错误");
                return false;
            }

            #$kinds, $eyes, $ears, $back, $mouth, $horn, $tail, $price
            $return = ["result" => []];
            $aa = DbManager::getInstance()->invoke(function ($client) use ($data, $return) {
                $data = json_decode($data, true);
                foreach ($data['data'] as $datum) {
                    if (isset($datum['class'])) {
                        $kinds = $datum['class'];
                    }

                    if (isset($datum['eyes'])) {
                        $eyes = $datum['eyes'];
                    }
                    if (isset($datum['ears'])) {
                        $ears = $datum['ears'];
                    }
                    if (isset($datum['back'])) {
                        $back = $datum['back'];
                    }
                    if (isset($datum['mouth'])) {
                        $mouth = $datum['mouth'];
                    }

                    if (isset($datum['horn'])) {
                        $horn = $datum['horn'];
                    }

                    if (isset($datum['tail'])) {
                        $tail = $datum['tail'];
                    }

                    if (isset($datum['price'])) {
                        $price = $datum['price'];
                    }


                    $id = $datum['id'];

                    if (!isset($kinds) || !isset($eyes) || !isset($ears) || !isset($back) || !isset($mouth) || !isset($horn) || !isset($tail) || !isset($price) || !isset($id)) {
                        $this->writeJson(-101, [], "缺少参数!");
                        return false;
                    }
                    if (empty($kinds) || empty($eyes) || empty($ears) || empty($back) || empty($mouth) || empty($horn) || empty($tail) || empty($price) || empty($id)) {
                        $this->writeJson(-101, [], "参数不可以为空!");
                        return false;
                    }


                    // var_dump($kinds . "--" . $eyes . "--" . $ears . "--" . $back . "--" . $mouth . "--" . $horn . "--" . $tail . "--" . $price);


                    if (strstr($eyes, "?")) {
                        $eyes = str_replace("?", "", $eyes);
                    }


                    $res = CwModelModel::create()
                        ->where(' (kinds = 1 or kinds = "' . $kinds . '") ')
                        ->where(' (eyes = 1 or eyes = "' . $eyes . '") ')
                        ->where(' (ears = 1 or ears = "' . $ears . '") ')
                        ->where(' (horn = 1 or horn = "' . $horn . '") ')
                        ->where(' (back = 1 or back = "' . $back . '") ')
                        ->where(' (mouth = 1 or mouth = "' . $mouth . '") ')
                        ->where(' (tail = 1 or tail = "' . $tail . '") ')
                        ->where(' (price = ' . $price . ' or price >' . $price . ') ')
                        ->get();


                    if ($res) {
                        #查询 是否 购买权限是否开启

                        if ($res['switch'] == 1) {
                            array_push($return['result'], [
                                'id' => $id,
                                'result' => true
                            ]);
                        } else {
                            array_push($return['result'], [
                                'id' => $id,
                                'result' => false
                            ]);
                        }

                        CwModelModel::create()->where(['id' => $res['id']])->update(['times' => QueryBuilder::inc(1)]);
                        (new LogHandel())->log("宠物id:" . $id . "匹配成功");
                        $one = MatchingModel::create()->data(['cw_id' => $id, 'created_at' => time(), 'price' => $price, 'mode_price' => $res['price'], 'status' => -2, 'model_id' => $res['id']])->save();
                        if ($one) {
                            (new LogHandel())->log("宠物id:" . $id . "插入MatchingModel 成功!  插入的值 price: " . $price . "mod_price 的价格:" . $res['price']);
                            $redis = RedisPool::defer("redis");
                            $data = $redis->rPush("Search", $id . "@" . "--");#宠物id  时间戳 价格

                        }

                    } else {
                        array_push($return['result'], [
                            'id' => $id,
                            'result' => false
                        ]);
                        (new LogHandel())->log("宠物id:" . $id . "匹配失败");
                    }
                }
                // return $return;
                $this->writeJson(200, $return, "获取成功");
            });


        } catch (\Throwable $e) {
            (new LogHandel())->log("ifShoppingInformation 异常");
            $this->writeJson(-1, [], "ifShoppingInformation 异常:" . $e->getMessage());
            return false;
        }

    }


    #回调函数获取
    function GetTaskResult()
    {
        $taskId = $this->request()->getQueryParam('taskId');
        if (!isset($taskId)) {
            $this->writeJson(-101, [], "缺少参数!");
            return false;
        }


        $redis = RedisPool::defer("redis");
        $data = $redis->hGetAll($taskId);

        if (!$data) {
            $this->writeJson(-101, [], "改任务还没有执行,或者改任务id不存在!");
            return false;
        }

        $re = [
            'count' => 0
        ];
        $count = 0;
        foreach ($data as $k => $val) {
            $re[$k] = json_decode($val, true);
            $count++;
            $re['count'] = $count;
        }

        $this->writeJson(200, $re, "获取成功");
        return false;
    }


    #模板导入
    function importModel()
    {

        $file = $this->request()->getUploadedFile('file');
        $content = file_get_contents($file->getTempName());
        if (!json_decode($content, true)) {
            $this->writeJson(-101, [], "数据格式错误!");
            return false;
        }
        $json_data = json_decode($content, true);

        try {
            DbManager::getInstance()->invoke(function ($client) use ($json_data) {
                foreach ($json_data["data"] as $datum) {
                    $res = CwModelModel::invoke($client)->get(['md5' => $datum['md5']]);
                    if (!$res) {
                        $add = [
                            'remark' => $datum['remark'],
                            'md5' => $datum['md5'],
                            'created_at' => time(),
                            'price' => $datum["price"],
                            'eth_price' => $datum["eth_price"],
                        ];
                        $add['kinds'] = $datum['kinds'];
                        $add['eyes'] = $datum['eyes'];
                        $add['ears'] = $datum['ears'];
                        $add['back'] = $datum['back'];
                        $add['mouth'] = $datum['mouth'];
                        $add['horn'] = $datum['horn'];
                        $add['tail'] = $datum['tail'];

                        #导入之前 看是否有重复的!
                        $one = CwModelModel::invoke($client)->get([
                            'kinds' => $add['kinds'],
                            'eyes' => $add['eyes'],
                            'ears' => $add['ears'],
                            'back' => $add['back'],
                            'mouth' => $add['mouth'],
                            'horn' => $add['horn'],
                            'tail' => $add['tail'],
                        ]);
                        if (!$one) {
                            CwModelModel::invoke($client)->data($add)->save();
                        }

                    }
                }

            });

            $this->writeJson(200, [], "导入成功");


        } catch (\Throwable $e) {
            $this->writeJson(-1, [], "异常:" . $e->getMessage());
            return false;
        }


    }


    #传宠物id  返回 宠物模板

    function backModel()
    {

        try {
            DbManager::getInstance()->invoke(function ($client) {
                $id = $this->request()->getQueryParam("id");
                $result = $this->GetCwFormId($id);
                if (!$result) {
                    $this->writeJson(-101, [], "获取数据失败,请稍后再试!");
                    return false;
                }


                $kinds = $result['data']['axie']['class'];
                $eyes = $result['data']['axie']['parts'][0]['name'];
                $ears = $result['data']['axie']['parts'][1]['name'];
                $back = $result['data']['axie']['parts'][2]['name'];
                $mouth = $result['data']['axie']['parts'][3]['name'];
                $horn = $result['data']['axie']['parts'][4]['name'];
                $tail = $result['data']['axie']['parts'][5]['name'];


                var_dump($kinds . "--" . $eyes . "--" . $ears . "--" . $back . "--" . $mouth . "--" . $horn . "--" . $tail);

                $limit = $this->request()->getQueryParam('limit');
                $page = $this->request()->getQueryParam('page');
                $model = CwModelModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created_at', 'DESC');
                $model = $model->where(' (kinds = 1 or kinds = "' . $kinds . '") ')
                    ->where(' (eyes = 1 or eyes = "' . $eyes . '") ')
                    ->where(' (ears = 1 or ears = "' . $ears . '") ')
                    ->where(' (horn = 1 or horn = "' . $horn . '") ')
                    ->where(' (back = 1 or back = "' . $back . '") ')
                    ->where(' (mouth = 1 or mouth = "' . $mouth . '") ')
                    ->where(' (tail = 1 or tail = "' . $tail . '") ');;

                // 列表数据
                $list = $model->all();
                $result = $model->lastQueryResult();
                $total = $result->getTotalCount();
                $return_data = [
                    'code' => 0,
                    'msg' => '',
                    'count' => $total,
                    'data' => $list
                ];
                $this->response()->write(json_encode($return_data, true));
                return true;
            });
        } catch (\Throwable $e) {
            $this->writeJson(-1, [], "异常:" . $e->getMessage());
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
            return false;
        }

    }


    function selecting()
    {
        $one = MatchingModel::create()->order("created_at", "DESC")
            ->all();


        foreach ($one as $k => $value) {

            $two = CwModelModel::create()->get(['id' => $value['model_id']]);
            if ($two) {
                $one[$k]['cw_mode'] = $two->toArray();
            } else {
                $one[$k]['cw_mode'] = [];
            }


        }

        $this->writeJson(200, [], $one);
    }


    function GetRec()
    {
        try {
            $limit = $this->request()->getQueryParam('limit');
            $page = $this->request()->getQueryParam('page');


            $model = RecentlySoldModel::create()->limit($limit * ($page - 1), $limit)->withTotalCount()->order('created_at', 'DESC');

            // 列表数据
            $list = $model->all();
            $result = $model->lastQueryResult();
            $total = $result->getTotalCount();
            $return_data = [
                'code' => 0,
                'msg' => '',
                'count' => $total,
                'data' => $list
            ];
            $this->response()->write(json_encode($return_data, true));
        } catch (Exception $e) {
            $this->writeJson(-1, [], "异常:" . $e->getMessage());

        };
    }


    function delRec()
    {
        $id = $this->request()->getQueryParam('id');
        RecentlySoldModel::create()->destroy(['id' => $id]);
        $this->writeJson(200, [], "删除成功");
    }


    function delAll()
    {
        $res = RecentlySoldModel::create()->func(function ($builder) {
            $builder->raw("truncate table recently_sold");

            return true;
        });
        $this->writeJson(200, [], "清除成功");

    }


    function changeSwitch()
    {


        $ids = $this->request()->getQueryParam('id');

        $ids_array = explode("@", $ids);
        $switch = $this->request()->getQueryParam('switch');

        foreach ($ids_array as $value) {

            CwModelModel::create()->where(['id' => $value])->update(['switch' => $switch]);

        }
        $this->writeJson(200, [], "修改成功");
        return false;
    }

    function get_cw_modelOne()
    {
        $ids = $this->request()->getQueryParam('id');

        $res = CwModelModel::create()->get(['id' => $ids]);
        $this->writeJson(200, $res, "获取成功");
    }


    function transferred_meaning($data)
    {

        if (strstr($data, "'")) {
            $data = str_replace("'", "\'", $data);
        }
        return $data;

    }

}
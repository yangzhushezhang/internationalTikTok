<?php
require_once __DIR__ . '/vendor/autoload.php';

go (function () {
   $client = new \EasySwoole\HttpClient\HttpClient('https://graphql-gateway.axieinfinity.com/graphql');
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
        'Cookie' => 'cf_clearance=ZKEX3iP.oK2VPW5qQIUm3Mlf4Jquk_wn06hHvAnxHSI-1632696261-0-250; _ga=GA1.2.216599525.1632696263; _gid=GA1.2.1509629869.1632696263; _gat_gtag_UA_150383258_1=1'
    );
   $client->setHeaders($headers, false, false);
   $ret = $client->postJson('{"operationName":"GetAxieDetail","variables":{"axieId":"3786815"},"query":"query GetAxieDetail($axieId: ID!) {\n  axie(axieId: $axieId) {\n    ...AxieDetail\n    __typename\n  }\n}\n\nfragment AxieDetail on Axie {\n  id\n  image\n  class\n  chain\n  name\n  genes\n  owner\n  birthDate\n  bodyShape\n  class\n  sireId\n  sireClass\n  matronId\n  matronClass\n  stage\n  title\n  breedCount\n  level\n  figure {\n    atlas\n    model\n    image\n    __typename\n  }\n  parts {\n    ...AxiePart\n    __typename\n  }\n  stats {\n    ...AxieStats\n    __typename\n  }\n  auction {\n    ...AxieAuction\n    __typename\n  }\n  ownerProfile {\n    name\n    __typename\n  }\n  battleInfo {\n    ...AxieBattleInfo\n    __typename\n  }\n  children {\n    id\n    name\n    class\n    image\n    title\n    stage\n    __typename\n  }\n  __typename\n}\n\nfragment AxieBattleInfo on AxieBattleInfo {\n  banned\n  banUntil\n  level\n  __typename\n}\n\nfragment AxiePart on AxiePart {\n  id\n  name\n  class\n  type\n  specialGenes\n  stage\n  abilities {\n    ...AxieCardAbility\n    __typename\n  }\n  __typename\n}\n\nfragment AxieCardAbility on AxieCardAbility {\n  id\n  name\n  attack\n  defense\n  energy\n  description\n  backgroundUrl\n  effectIconUrl\n  __typename\n}\n\nfragment AxieStats on AxieStats {\n  hp\n  speed\n  skill\n  morale\n  __typename\n}\n\nfragment AxieAuction on Auction {\n  startingPrice\n  endingPrice\n  startingTimestamp\n  endingTimestamp\n  duration\n  timeLeft\n  currentPrice\n  currentPriceUSD\n  suggestedPrice\n  seller\n  listingIndex\n  state\n  __typename\n}\n"}');
   $jsonStr = $ret->getBody();
   echo json_encode(json_decode($jsonStr, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
   var_dump($ret->getStatusCode());
});
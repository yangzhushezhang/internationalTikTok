<?php


namespace App\HttpController\Task;


use App\HttpController\Model\MonitorVideoModel;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\Task\AbstractInterface\TaskInterface;

class GetVideoReleaseTimeTask implements TaskInterface
{

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    function run(int $taskId, int $workerIndex)
    {
        //<span> · </span>(.*)</a></div>    获取   假设 时间 符合需求 就对他的 粉丝进行采集
        //将其状态 改成 3  可以进行采集
        try {
            $array_headers = [
                'authority' => 'www.tiktok.com',
                'sec-ch-ua' => '" Not A;Brand";v="99", "Chromium";v="99", "Google Chrome";v="99"',
                'sec-ch-ua-mobile' => '?0',
                'sec-ch-ua-platform' => '"macOS"',
                'upgrade-insecure-requests' => '1',
                'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.74 Safari/537.36',
                'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                'sec-fetch-site' => 'none',
                'sec-fetch-mode' => 'navigate',
                'sec-fetch-user' => '?1',
                'sec-fetch-dest' => 'document',
                'accept-language' => 'zh-CN,zh;q=0.9',
                'cookie' => '_abck=E15852EC43400E94D4E6A819965A3837~-1~YAAQPXpAF7/skbp/AQAAUXeAvwf92w1UrG6snWz6NWLuxtXAV0fLvqvrDud6oTHjdlALfjDwnW8NHxg6yCzdY67ZrJu79Uo0szpaj0FfftTSwc2fF+IWFMDz4gt0Eibgxu1tvTlTEHorkCvT0Xh+ZWswfF3vTvD/k6J/Qr5nrwGyJItY7aJiqWMUiRqX5u6EyeAMa73nA7NjDxM6Qh26t6v0qNSRfjmA457mXe0UM7oPXDkZp/AKiizVn5mSNkzlRnF0ZwnUjtH52NqI8Gl6vwR+4dh6+Gt2Kq7pUTWT8Zgg47VGjpvmAxx85rB6g+B9loeFnBa6m84EakzYMNbqxFhX4l8XdVcylKUnXjSI4nBvbaILuxtxQrlc3SA=~-1~-1~-1; bm_sz=B5DAC8D18ECF26C95BAB269917ED395A~YAAQPXpAF8Hskbp/AQAAUXeAvw/rg8rGVhQUSlSiXMeR/wKo1eiUb1mGcy0KLk61xgrf8X4yz0JgOqGx0S0k9TzBLQiJK4b90YObXePpJCgNIMUIM/W9cwgIz0ihqOLboeYtPEo8iFvy8QiJjR8RdLBAKRCxh9hJl/Gxxf7bom38m+aIhwPLWyEXWVmK8pf+7XCSGeAn8ADVCZLCVrH7tE8NOCb47OZmedzt38rFYDUWR1fljrqyVzPFxkucsgUbyFkub7myyyDDTToaRXKBlu+JoKmV91N9YIDS2WSzoTdUjdA=~3684422~4338227; ak_bmsc=6E461967E83A9AC4626FE5DBD793F469~000000000000000000000000000000~YAAQPXpAF5Ltkbp/AQAAtYuAvw+dLlXRvjLAM6aveYXZw3Jc2kZnA97rZGthbkmR4hN8SOX+0ITbn8osqYjINRpRiUFAgN6rpr1uVORlwSsODFjju9AFRF892AGEaZIF6zn9/QFquNHuHbPYLXg/aUZZHAOS5KQsqq7HMzfGbGw5tJA0ANV0xbsCisNc7PjSd3NT4O3GspXL3dmlaNdJbojFK/S0Wk68QgMD2kstHMtw7waCzigdfP/zH0N5AvOSWq7DlnuXwEycwEETH9E1+QeQGOK9AbrQGTwrfwXsWvzRS+OHELygmomLQ7I/OamY8i0rHi/8eUTAjPeLBU5/RwVAAVVUYm0HS4DhSAgwkezmIrzuQhkq4klldGojCiw+95woccp+7pE/NBUvnWmllZ8VSRzyrnbS08w=; tt_csrf_token=liqtGb4xS9vlqd4hEpcv_D3F; msToken=sMlV4XQqOtyXVo1rclwtv4ffIA31v0XZZ0AoHuxBPb0NDcpZLnjt-DwrvcCkWtYQHcknEM01nVkK9fraNePmMelBUM8fLMjz4Ch9RFlihAUJ83Ot5H0RdoOBm6EWr-sH9RTGxAQen95i; bm_mi=327A30697E8CAC4DA27C76A8B2C2DA7F~Fnrqy3izVW8GxvBbFQ8aOfe5l7B0+Vs3FL+LZNe9XQxfxCQUVlFHfHEGLcU6TgoLWINmfxbodvTdnHBdpDQWkbHHuKMFK1Gk+YmvO/v/iCD9eSzO2z0eBWqBoeMbrVQfNn4lr2FyrMV8AO+Ee1pnEyD232hDXqcZC+0ob7o+M58MYuZAxMieALuckwWX8WrsxIMIC2DIAgCgCcSxf8vZxMK1ZcI7z+G734WykuYbPuGSkYLFLkFksuVDPgdfuk6shYVhvwDGjnJHXcoc8Sh2Vg==; ttwid=1%7CI670qGhRthRfIqfPMwc4EAMPcyrG8fWtC56qFM9zo5M%7C1648187296%7C2c3ce5763a8f8ab4c3afa613593bcfefaf36b7e27cdf3b14a765faa5f2041690; msToken=A4-Qoi-rIYquZEniQ29yaHK7s5qHdMtXHykhm87ejk-dNoDRrhXyz4LoimQ84do0Q7TSl06-IHhrZoLP-SYelN4XqmIFaKMN2LHacjI_R2-3UcC2PytHCarqmvcgxfV0ES2DIJUYVBU9; bm_sv=465A63A953C951DC7C6DB5BD95AB53FE~jkFSh4sm9/pjTMA99kqflRFC999/ZF7cyyDxToMSzFdLIjzT8qFOLPoLlaPBtiW7DPUrtbFccJ8dwWszKRuooCYBFt5MyItUo5YsAY00UMfBRGvhmHu6A3lZ+7SOEVbIJIfGqiAW5X8OfOetBVOkmIVA8IrKi7I1jknFRdzKHsE='
            ];
            $c = new \EasySwoole\HttpClient\HttpClient("https://www.tiktok.com/" . $this->data['username'] . "/video/" . $this->data['vid']);
            $c->setHeaders($array_headers);
            $c->setTimeout(5);
            $c->setConnectTimeout(10);
            $data = $c->get();
            $content = $data->getBody();
            $isMatched = preg_match('/<span> · <\/span>(.*)<\/a><\/div>/', $content, $matches);
            if ($isMatched > 0) {
                var_dump($matches[1]);  #时间以后再做判断
            }

            MonitorVideoModel::create()->where(['up_id' => $this->data['up_id'], 'vID' => $this->data['vid']])->update(['status' => 3]);

        } catch (\Throwable $e) {
            var_dump($e->getMessage());
        }
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex)
    {
        // TODO: Implement onException() method.
    }
}
<?php


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.tiktok.com/@petro_s/video/7077594795992042758');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'authority' => 'www.tiktok.com',
    'cache-control' => 'max-age=0',
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
    'Accept-Encoding' => 'gzip',
]);
curl_setopt($ch, CURLOPT_COOKIE, 'tt_csrf_token=nbn6kVkJCvS_24VdNeThsxqN; csrf_session_id=5a07e75b39433498d186561f2c3511c4; cookie-consent={%22ga%22:true%2C%22af%22:true%2C%22fbp%22:true%2C%22lip%22:true%2C%22bing%22:true%2C%22version%22:%22v5%22}; __tea_cache_tokens_1988={%22_type_%22:%22default%22}; _abck=610234D4A02DC9771BA6110073B69735~-1~YAAQF2o+F+r5Rbx/AQAAMODpvgd4sysqjBpbqCVa/n4YXDvU1R7/EHdIpR57oCFYYO/6Ah3vhOIAYFf3PiQlN2sFGPw3PxIY+YGwi96MZRVLh6OGNJ5zGfvJ+/iPtc23P1Kbw3FrsWHJuqPFuJyMF8XPrbOoaX2jQt7SXuIhjj6PXKQyQ+2wIv06d8+OeicCXjUTMJzL7wR/hVtSV5ixYXZ3K+vxwsTyvitQOVhHZZto8AVsOVjWZ7RZKEX5S1sjpfOwGSidrZyD4ASeBVguYx3qmlnEvYtzPIFB1gYtXWc7utrp+eS5T9TCadybe7UNE5M7nSIQ2QanSpRYfAGiLMDgI3I2SZwhF4FEJptHkK7z/I9aYvTWGFjBmg+4nOfGXrHBvnhZRuZmww==~-1~-1~-1; bm_sz=B983CCEF70CD23B7B2B2E991A18DAB04~YAAQF2o+F+v5Rbx/AQAAMODpvg/RvnYBhHePO6OhcZg1ONIFqPVNg5l48kr1fkcKZOiP9188MYJ7xzMfNbyWgTtkLmZgLTSKI0I4vPlqSDy9uVxR3b11BPON8Pp5RKgxtgGZxSn8TulCYE9+Prb/PxYSVa64HVXMWBwmU6GqjxPAAOK+CNQWmMufQ1Kia9PRp5tbmNfabzzFShPbEDYoKvnaRUufBpqy5oP4oTlpgQYRD4eqdOtPvwtcc/MaISei5NUmBlPhr5p3KZFgzxxUiWw0mMPEcgC0F6/YCIaWLaF+Ohc=~3356741~3556404; ak_bmsc=D378334224BDF481C7820A38B689EC29~000000000000000000000000000000~YAAQF2o+F+z5Rbx/AQAA7uXpvg9xE9XRAQDkxMZO4tqP49bUMe7MZ3QL4q3DpLy1SemTOtMm7/5uOY0+6qNhPCCCTWTFLQbwb6B2VFHJJN0fJTdXHCs1t8BWP54wQmHfqcWpjSZstkon+Jld7GBsxUPaNObZK9GUeRQB+BkjtNX+F+HTUU8iS39SMo6ZK6iXhB2nvIRUvc1+TgM/gX/Md+ZhLjLao84XdHzG5tf3NK0vDGgMmHX40GDxW4u+qj06pE56apjLtD7cwXMX3+tuxsT3mCj/qWuJp2J9sL9iE6j1PavfzA5RxLoA2KGHf9/xyjfm+/ZWbQkrmTOb0dOLVrWuvOdN1ahtvpihkM0Dbp8zQmYpiDMaRQTWedR/xTLdRYI4o+oaiW0PzQ==; bm_mi=2F1570CEEE7C52F27E6E75570B795AF8~RLa2XF8xgF2UteDW+goNnaVzCAehYZ4jkfGSmX1qou6zK3ql/ot8ia4iBOoKtLhZcoeqVstReWATnEp8/YA/W1TxhyIIaCvGUtSnrUKj0MzL8f2TfZfVb1NgJBnK11S22jv2En9sCQ7OZzqaD/xQgAyDyRyZpbJ8oAP8Defhbkz+cBc09k7cqcRQaLWp/AiW6BQCDifhQ398MSObL8PY/fkaI7KFohiSPSfIOPUO60Z0fXhgh1HoLxOnVy3xafMNbI5KUJa7SaEbGo9hxhBH0Q==; ttwid=1%7CzW57WULSR0LEefk6MpEg6V7T7D6S5eryxLAmeQy-ttM%7C1648181445%7C3a9c6d7a7c50c7dd32564591959939bcd3db7a20425ccd82d4c36ebc98607dee; msToken=fWh4BwGC_Kzsz3Zr9UTi7KSYCV03rNErL7ygZp_kReM2N0LMDSdJsw8QF56iCsYM9mtde4SwmK_pVfMwDBBQYrv1HdOUBu0R9Skj_wsMx86FrWUF7t8_I5rjhd4TMxACQ0KfPCGPu4poCW1Qig==; bm_sv=A5272DBFB65DA6D6A0CC563B005CFD70~S96wk+eJwc4h53wtJ8O4U6xRZQC0GBhd1wLJoFpzrwt5s1lmeG2OZY6omJBrVBzcwilDklCl1aaVRt43zR7reGzWsJUcDOVGUuwWa9PNT5yBzvkjieho2hkh97oWZ+c2EGfqQ0hIncjwYt6qzKNy1hjh9SzCFgVpcItTB28+Z18=; msToken=99BYdcpZjeT_1wQgNHpM5uJXJxwm376rT2YPCAzUrDUb40qHPYUn9unjIjYChMBIhha8hUlYoWjiItrsy2ZShtJsw7arKpM89r4SJv5bzBSAjMX25wDMXmCZ4Y-ugHJEtFn39RiFFMrbxJ1vVA==');

$response = curl_exec($ch);


var_dump($response);

curl_close($ch);
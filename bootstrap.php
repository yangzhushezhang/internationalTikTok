<?php
//全局bootstrap事件
//date_default_timezone_set('Asia/Shanghai');   //中国时区

//date_default_timezone_set('Asia/Phnom_Penh');//必写：柬埔寨时区

date_default_timezone_set("America/New_York");

//$access_token = $this->get_Access_Token("ZL5wxWPUOp9Yzn1Yc7pjLeZ0", "FGONKQ4YcRWeX7nIBMPgWukBcDXlG2ao");


define("client_id", "ZL5wxWPUOp9Yzn1Yc7pjLeZ0");
define("client_secret", "FGONKQ4YcRWeX7nIBMPgWukBcDXlG2ao");
define("DEVICE", 1);
define("AutomaticFanCollectionProcessNum", 5);   #采集粉丝的进程数量
define("FaceRecognitionProcessNum", 2);#  人脸识别的并发
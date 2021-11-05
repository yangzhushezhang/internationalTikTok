
/**
 * 设置脚本日志
 * 
 * @user 刘哥
 * 国际抖音版本   v18.1.3
 */


// console.setGlobalLogConfig({
//     "file": "/sdcard/1.txt"
// });

//域名 = "172.81.248.168:9501"

/**
 * 修改系统 toast 方法!
 */
var _toast_ = toast;
toast = function (message) {
    _toast_(message);
    sleep(2000);
}

// 请求超时! 10秒
http.__okhttp__.setTimeout(10000);

//获取 百度的SK AK






/**
 * @method 脚本初始化 获取SK AK
 *  全局变量
 */
var AK = ""
var SK = ""
var nickname = ""
var 域名 = ""
var 全局抖音ID = ""
path = "/sdcard/Pictures/nickname.txt";

if (files.exists(path)) {
    nickname = files.read(path);
    toast("昵称:" + nickname)
} else {
    toast("昵称不存在,请写好配置文件!!!在运行")
    exit();
}

path = "/sdcard/Pictures/ip.txt";
if (files.exists(path)) {
    var 域名 = files.read(path);
    toast("使用域名:" + 域名)
} else {
    toast("域名不存在,请写好配置文件!!!在运行")
    exit();

}





//设置手机 信息
function set_new_phone() {
    try {
        var res = http.get("http://" + 域名 + "/set_new_phone?secret_key=" + SK + "&api_key=" + AK + "&nickname=" + nickname);
    } catch (error) {
        console.log("set_new_phone 异常:" + error);
    }

}



function 脚本初始化() {
    path = "/sdcard/Pictures/api_key.txt";
    if (files.exists(path)) {
        AK = files.read(path);
    } else {
        toast("百度api_key不存在,请写好配置文件!!!在运行")
        exit();

    }

    path = "/sdcard/Pictures/secret_key.txt";
    if (files.exists(path)) {
        SK = files.read(path);
    } else {
        toast("百度secret_key不存在,请写好配置文件!!!在运行")
        exit();
    }
    // var res = http.get("http://" + 域名 + "/set_new_phone?secret_key=" + SK + "&api_key=" + AK + "&nickname=" + nickname);
    set_new_phone()
}


//脚本初始化()


/**
 * 上传手机 信息 
 */
function 上传手机脚本手机信息() {
    try {
        var res = http.get("http://" + 域名 + "/set_new_phone");
    } catch (error) {
        console.log("set_new_phone 异常:" + error);
    }
}









/**
 * @method 截取头像
 * @returns 
 */
var can_ScreenCapture = false;
function screen_douyin_png() {
    //请求截图
    if (!can_ScreenCapture) {
        if (!requestScreenCapture()) {
            toast("请求截图失败,重新请求");
        }
        can_ScreenCapture = true
    }
    var img = captureScreen();
    images.saveImage(img, "/sdcard/douyi.png");
    //对图片进行  剪切 
    sleep(1000)
    var src = images.read("/sdcard/douyi.png");
    sleep(1000) //延迟
    var clip = images.clip(src, 408, 242, 290, 270);
    images.save(clip, "/sdcard/clip_douyin.png");
    console.log("截取完毕")
    toast("截屏完毕")
    return true;
}




/**
 * @method 获取百度的 access_token
 * @param {*} SK 
 * @param {*} AK 
 * @returns 
 */
function get_baidu_Access_Token(SK, AK) {
    host = 'https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=' + AK + '&client_secret=' + SK;

    console.log(host)
    for (var i = 0; i < 10000000; i++) {
        try {
            var res = http.get(host);
            if (res.statusCode != 200) {
                toast("请求失败: " + res.statusCode + " " + res.statusMessage);
            } else {
                var weather = res.body.json();
                if (weather != "") {

                    //获取 百度的access_token
                    return weather.access_token;
                }
            }
            sleep(2000)
        } catch (error) {
            toast("method get_baidu_Access_Token 获取...异常" + error)
            console.log("get_baidu_Access_Token 异常:" + error)
        }
    }

}



/**
 *   吧本地图片变成 base64 数据
 * @param path 
 */
function get_image_base64(path) {
    var img = images.read(path);
    if (img) {
        return images.toBase64(img)
    }
    return ''
}





/**
 * 
 * @param {*} access_token  获取性别
 * 
 * 百度 的接口
 */
function get_age_male(access_token) {
    path = "/sdcard/clip_douyin.png"
    var image_base = get_image_base64(path)
    var male = "";
    if (image_base != "") {
        var url = "https://aip.baidubce.com/rest/2.0/face/v3/detect?access_token=" + access_token
        for (var i = 0; i < 10000000; i++) {
            try {
                var res = http.postJson(url, {
                    "image": image_base,
                    "image_type": "BASE64",
                    "face_field": "age,gender"
                });
                if (res.statusCode != 200) {
                    toast("请求失败: " + res.statusCode + " " + res.statusMessage);
                } else {
                    var result = res.body.string();
                    result = JSON.parse(result)
                    //获取性别
                    console.log("结果:" + result)
                    if (result.error_code == 0) {
                        male = result.result.face_list[0].gender.type
                    } else {
                        console.log("不是人脸")
                        male = ""
                    }
                    toast("性别:" + male)
                    return male;
                }
                sleep(2000)
            } catch (error) {
                toast("method get_age_male() 异常退出");
                console.log("get_age_male 异常:" + error)
            }
        }
    } else {
        toast("程序错误,没有获取到图片")
        return male;
    }
}




/**
 * 从 自己的服务器获取 性别
 */
function get_sex_form_my_server() {
    path = "/sdcard/clip_douyin.png"
    var image_base = get_image_base64(path)
    var male = "";

    if (image_base != "") {
        var url = "http://" + 域名 + "/get_sex_form_my_server"
        for (var i = 0; i < 10000000; i++) {
            try {
                var res = http.post(url, {
                    "image": image_base,
                });

                if (res.statusCode != 200) {
                    toast("请求失败: " + res.statusCode + " " + res.statusMessage);
                } else {
                    var result = res.body.json();
                    //获取性别

                    if (result.code == 200) {
                        male = result.result
                    } else {
                        console.log("不是人脸")
                        male = ""
                    }
                    toast("性别:" + male)
                    return male;
                }
                sleep(2000)
            } catch (error) {
                toast("method get_age_male() 异常退出");
                console.log("get_age_male 异常:" + error)
            }
        }
    } else {
        toast("程序错误,没有获取到图片")
        return male;
    }

}



/**
 * @method 获取抖音的@id
 * @returns 
 */
function get_id_name() {
    if (id('dtx').exists()) {
        for (var i = 0; i < 1000000; i++) {
            try {
                var name = id('dtx').findOne().text()
                console.log("http://" + 域名 + "/set_name?name=" + name)
                var result = http.get("http://" + 域名 + "/set_name?name=" + name);
                if (result.statusCode != 200) {
                    toast("上传 id请求失败: " + result.statusCode + " " + result.statusMessage);
                } else {
                    var data = result.body.string();
                    //返回 1   不存在 或者存在 
                    var v = JSON.parse(data);
                    if (v['code'] == "1") {
                        return true;
                    } else {
                        console.log("失败")
                        return false;
                    }
                }
                sleep(1000)
            } catch (error) {
                toast("method get_id_name() 跑出异常!!" + error)
                console.log("method get_id_name() 跑出异常!!" + error)
            }
        }
    } else {
        toast("获取控件 dot 失败,没有办法上传id")
        return false;
    }
    return false;
}




function 最后一次关注的时间() {
    try {
        var res = http.get("http://" + 域名 + "/attentionTheLastTime?nickname=" + nickname);
    } catch (error) {
        console.log(" 心跳检测异常抛出:" + error)
    }
}




/**
 *@method 是否去点击粉丝
 * @returns 
 */
function if_follow_click() {
    //判断是否存在 关注这个控件
    var if_click = false
    for (var i = 0; i < 6; i++) {
        var back = false
        if (id("c9b").exists()) {   //个人信息这个界面存在
            for (var i = 0; i < 5; i++) {
                toast("等待加载....")   //这里考虑网络延迟问题 给10 秒的加载时间
            }
            var if_re = get_id_name();
            if (!if_re) {
                toast("名字重复了!")
                click(60, 110)
                break;
            }
            screen_douyin_png()  //这里我先不做处理  截屏这里  可能会截取屏幕失败
            // var sex = get_age_male(get_baidu_Access_Token(SK, AK))
            var sex = get_sex_form_my_server()
            if (sex == "male") {
                //更新 最后一次 更新 粉丝关注时间
                最后一次关注的时间()
                //需要点击关注
                toast("点击返回")
                if (id("c9a").exists()) {
                    id("c9a").click()  //点击关注
                }
                sleep(1000)  //点完关注  等待时间
                if (id('l1').exists()) {  //如果控件存在 就点击 控件返回
                    id("l1").click()
                } else {
                    click(60, 110)
                }
                if_click = true
                break;
            } else {
                //点击返回
                toast("点击返回")
                if (id('l1').exists()) {  //如果控件存在 就点击 控件返回
                    id("l1").click()
                } else {
                    click(60, 110)
                }
                break;
            }
        } else {
            //点击生效了 或者
            toast("等待个人信息界面......")
            sleep(2000)

        }

    }
    return if_click;
}



/**
 * @method  获取头像坐标
 * @returns 
 */
function get_head_image_bounds() {
    for (var j = 0; j < 7; j++) {
        if (id('ans').exists()) {
            ////头像存在 哦
            var uc = id('com.zhiliaoapp.musically:id/ans').find();
            console.log("头像有:" + uc.length)
            if (uc.length == 0) {
                toast("暂无评论...,切换下一个视频")
            }
            for (var i = 0; i < uc.length; i++) {
                var tv = uc[i];
                var Y = tv.bounds().centerY()
                var X = tv.bounds().centerX()
                if (X > 0 && Y > 0) {
                    if (Y < 622 || Y > 1595) {
                        continue;  //这个坐标容易点到下面的聊天  //
                    }
                    toast("点击头像坐标:X" + X + "," + Y)
                    click(X, Y)
                    sleep(1000)
                    // 检查性别 
                    if (if_follow_click()) {  //男性
                        //点击点赞
                        toast("点赞坐标:Y:" + Y)
                        //这里是点赞功能   
                        // click(1000, Y)  
                        var 今日关注数量 = 获取今日已经关注的数量()
                        toast("今日关注数量:" + 今日关注数量)
                        if (今日关注数量 >= 249) {
                            toast("今日关注已经达到上限......准备关闭脚本")
                            sleep(5000)
                            //  send_message()
                            exit()
                        }
                    }
                } else {
                    toast("头像坐标点为负值,框架不稳定....")
                    sleep(1000)
                }
            }
            return true;
        } else {
            //这个控件没有找到 可能原因:1网络问题 2没有评论
            toast("提示:头像控件没有找到....")
            if (j == 6) {
                if (id('aq0').exists()) {  // 红色++存在
                    //说明在首页 
                    click_commit()
                    return true;
                } else {
                    return false;
                }

            }
        }
        sleep(1000)
    }
}







/**
 *  从服务器 获取链接并且跳转 
 */
function 从服务器获取视频链接() {
    host = 'http://' + 域名 + '/get_Dy_link_id?nickname=' + nickname
    for (var i = 0; i < 10000000; i++) {
        try {
            console.log(host)
            var res = http.get(host);
            if (res.statusCode != 200) {
                toast("请求失败: " + res.statusCode + " " + res.statusMessage);
            } else {
                var weather = res.body.json()
                if (weather) {
                    if (weather.code == 1) {
                        toast("获取到id:" + weather.msg);
                        全局抖音ID = weather.msg
                        app.startActivity({
                            data: "snssdk1233://aweme/detail/" + weather.msg,
                        });

                        //这里新增判断 是否进入了  首页
                        var 进入评论超时时间 = timest()
                        while (true) {
                            if (timest() - 进入评论超时时间 > 300) {
                                toast("进入评论首页超时了.....准备切换下一条链接...")
                                break;
                            } else {
                                if (id('a1r').exists()) {
                                    break;
                                } else if (text("Privacy Policy").exists()) {
                                    if (id("c77").exists()) {
                                        id("c77").findOne().click()
                                    } else {
                                        toast("没有找到OK id")
                                    }

                                } else {
                                    toast("等待进入评论首页......")
                                }
                            }
                        }
                        break;
                    } else {
                        toast(weather.msg)
                    }
                }
            }
            sleep(2000)
        } catch (error) {
            toast("method 从服务器获取视频链接 异常" + error)
            console.log("method 从服务器获取视频链接 异常" + error)

        }
    }

}





/**
 * 改变抖音的使用 状态
 */
function 改变抖音链接已使用的状态() {
    try {
        var host = "http://" + 域名 + "/change_status_for_Dy?id=" + 全局抖音ID
        var res = http.get(host);
    } catch (error) {

        console.log("改变抖音链接已使用的状态 异常:" + error)

    }


}



//获取 评论最后一个名字
function get_name_for_last_for_commit() {
    if (id('com.zhiliaoapp.musically:id/title').exists()) {
        var uc = id('com.zhiliaoapp.musically:id/title').find();
        if (uc.length == 0) {
            return '';
        }
        return uc[uc.length - 1].text()
    } else {
        toast("发送信息-没有过去到最后一个名字")
        return '';
    }
}




/**
 * 向上滑动
 * 头像 ...
 */

function up_swipe() {
    if (id('ans').exists()) {
        // swipe(84, 1513, 84, 505, 1200)
        swipe(84, 1513, 84, 350, 1200)
        sleep(2000)
    } else {
        toast("头像控件不可以滑动.....")

    }
}






/**
 * @method 点击评论 直到 成分为止
 */


function click_commit() {
    var click_commit_times = 0
    var is_click_commit_button = false;
    for (var i = 0; i < 5; i++) {
        if (is_click_commit_button) {
            if (id('b9p').exists()) {  //笑脸图标存在 说明 已经打开 评论了...
                toast("评论已经打开了.....")
                break;
            } else {
                for (var j = 0; j < 5; j++) {
                    toast("等待评论打开中.......")
                }
                is_click_commit_button = false
            }
        } else {

            if (id('a1r').exists()) {
                toast("点击评论")
                click(1000, 1200)
                is_click_commit_button = true
            } else {
                toast("没有找到评论按钮....等待.....")
            }
        }

    }

}



function set_comments() {
    if (id('a1i').exists()) {
        var comments = id('a1i').findOne().text()
        try {
            var res = http.get("http://" + 域名 + "/set_comments?Dy_id=" + 全局抖音ID + "&comments=" + comments);
            toast("http://" + 域名 + "/set_comments?Dy_id=" + 全局抖音ID + "&comments=" + comments)
        } catch (error) {
            console.log(" 心跳检测异常抛出:" + error)
        }

    }


}



/**
 * @returns 获取时间戳
 */
function timest() {
    var tmp = Date.parse(new Date()).toString();
    tmp = tmp.substr(0, 10);
    return tmp;
}




/**
 * @method 去评论
 */
function main_one() {
    // 脚本初始化()
    //上传手机脚本手机信息()
    var hint = ""
    var swipe_times = 0
    var 点击搜索按钮 = false
    var 是否点击home = false
    var 可以切换下一个视频 = false
    var the_last_name = ""
    var 现在的时间戳 = timest()
    var hint_one = ""
    var 检查假粉次数 = 0
    while (true) {
        if (timest() - 现在的时间戳 > 7200) {
            //如果时间 大于两个小时 就去获取评论
            if (hint_one == "") {
                if (text("Me").exists()) {
                    toast("成功打开国际版抖音....main_one")
                    hint_one = "获取评论"
                } else {
                    toast("打开抖音失败....,准备重启..main_one")
                    关闭应用("com.zhiliaoapp.musically")
                    sleep(3000)
                    launchApp("TikTok");
                    sleep(6000)
                }
            } else if (hint_one == "获取评论") {
                if (是否是主界面() || id('cg0').exists()) {
                    click(960, 1800)
                    toast("点击me")
                } else if (id('aqp').exists()) {
                    get_follows()
                    现在的时间戳 = timest()
                    hint_one = ""
                    hint = ""  //检查完毕后 重新去获取连接
                } else if (text("Refresh").exists()) {
                    toast("断网了.....,点击刷新")
                    text("Refresh").findOne().click()

                } else {
                    if (检查假粉次数 > 10) {
                        hint_one = ""
                    } else {
                        检查假粉次数++
                        toast("检查假粉...未知界面.....")
                    }
                }
            }

        } else {
            var 断网判断 = 1
            if (hint == "") {
                if (可以切换下一个视频) {
                    从服务器获取视频链接()
                    可以切换下一个视频 = false
                }
                if (id('a1r').exists()) {

                    set_comments()
                    click_commit()
                    hint = "进入评论"
                } else if (text("Video isn't available").exists()) { //这种情况一般是断网 导致的    暂时提示  吧不进行操作 或者是短视频失效导致的.
                    if(断网判断>30){
                        断网判断=1
                        可以切换下一个视频=true
                    }else{
                        toast("网络断链,请管理员检查网络.......联系作者,对这里限制进行修改.....")
                        断网判断++
                    }
                } else {
                    从服务器获取视频链接()
                    console.log("ID---" + 全局抖音ID)

                }
            } else if (hint == "进入评论") {

                if (get_head_image_bounds()) {

                    hint = "滑动"
                } else {
                    hint = ""
                }

            } else if (hint == "滑动") {
                if (swipe_times > 10000) {
                    //back()
                    // 改变抖音链接已使用的状态()
                    swipe_times = 0
                    hint = ""
                    可以切换下一个视频 = true
                } else {
                    the_last_name = get_name_for_last_for_commit();
                    up_swipe()
                    if (the_last_name == get_name_for_last_for_commit()) {

                        swipe_times = 0
                        hint = ""
                        可以切换下一个视频 = true
                    } else {
                        swipe_times = swipe_times + 1
                        hint = "进入评论"
                    }
                }

            }
            toast("脚本正在运行中.........")
            sleep(2000)
        }
    }

}







/*******************************************************************************分割线********发送 消息******************************************************************************************** */


function 是否是主界面() {
    if (id('zy').exists()) {
        return true;
    }
    return false;
}

function 点击Home() {
    click(90, 1700);
}

/**
 * @method 获取粉丝上传
 * @returns 
 */
function get_follows() {
    for (var i = 0; i < 100000; i++) {
        try {
            if (id('aqq').exists()) {
                var following = id('aqp').findOne().text()
                var followers = id('aqi').findOne().text()
                var res = http.get("http://" + 域名 + "/addFollow?nickname=" + nickname + "&following=" + following + "&followers=" + followers + "&real_concerns=" + 上传今日关注数());
                toast("http://" + 域名 + "/addFollow?nickname=" + nickname + "&following=" + following + "&followers=" + followers)
                console.log("http://" + 域名 + "/addFollow?nickname=" + nickname + "&following=" + following + "&followers=" + followers)
                if (res.statusCode != 200) {
                    toast("请求失败: " + res.statusCode + " " + res.statusMessage);
                } else {
                    if (res != "") {


                        var weather = res.body.json()
                        if (weather.code == 1) {  //等于1  的时候就是运行 脚本了..
                            // 点击Home()
                            sleep(2000)
                            return true;
                        } else {
                            toast(weather.msg)
                            return false
                        }
                    }
                }
            } else {
                toast("following 控件不存在!")
            }
        } catch (error) {
            toast("get_follows 上传粉丝异常!!" + error)
            console.log("get_follows 异常:" + error)

        }
        sleep(2000)
    }
}




/**
 * @method 私信个数上传
 * @returns 
 */
function 上传私信数量() {
    for (var i = 0; i < 100000; i++) {
        try {
            var res = http.get("http://" + 域名 + "/addFollow?nickname=" + nickname + "&sixin=" + 上传今日私聊的个数());
            // console.log("http://" + 域名 + "/addFollow?nickname=" + nickname + "&following=" + following + "&followers=" + followers)
            if (res.statusCode != 200) {
                toast("请求失败: " + res.statusCode + " " + res.statusMessage);
            } else {
                if (res != "") {
                    var weather = res.body.json()
                    if (weather.code == 1) {  //等于1  的时候就是运行 脚本了..
                        // 点击Home()
                        sleep(2000)
                        return true;
                    } else {
                        toast(weather.msg)
                        return false
                    }
                }
            }
        } catch (error) {
            toast("get_follows 上传粉丝异常!!" + error)
            console.log("get_follows 异常:" + error)

        }
        sleep(2000)
    }
}









/**
 * @method 随机函数
 * @param {} minNum 
 * @param {*} maxNum 
 * @returns 
 */
function randomNum(minNum, maxNum) {
    switch (arguments.length) {
        case 1:
            return parseInt(Math.random() * minNum + 1, 10);
            break;
        case 2:
            return parseInt(Math.random() * (maxNum - minNum + 1) + minNum, 10);
            //或者 Math.floor(Math.random()*( maxNum - minNum + 1 ) + minNum );
            break;
        default:
            return 0;
            break;
    }
}

/**
 * @method 获取联系方式
 * @returns 
 */
function 获取联系方式() {
    for (var i = 0; i < 10000000; i++) {
        try {
            url = "http://" + 域名 + "/get_one"
            var res = http.get(url);
            if (res.statusCode != 200) {
                toast("请求失败: " + res.statusCode + " " + res.statusMessage);
            } else {
                if (res != "") {
                    var weather = res.body.json();
                    //获取 百度的access_token
                    if (weather.code == 1) {  //等于1  的时候就是运行 脚本了..
                        // AK = weather.msg.api_key
                        //   SK = weather.msg.secret_key
                        //toast("开始运行!!")
                        return weather.msg;
                    }
                }
            }
        } catch (error) {
            toast("获取联系方式 异常:" + error)
            console.log("获取联系方式:" + error)
        }
        sleep(2000)
    }
}

/**
 * @method 是否是发送消息界面
 * @returns 
 */
function 是否是发信息界面() {
    if (id("ag9").exists()) {
        return true;
    }
    return false;
}

/**
 * @method 判断抖音是否被封
 * @returns 
 */
function 判断抖音是否被封了() {
    try {
        if (id('cxm').exists()) {
            if (id('com.zhiliaoapp.musically:id/bzu').exists()) {

                var uc = id('com.zhiliaoapp.musically:id/bzu').find();
                if (uc.length == 0) {
                    return false;
                }
                var fenjin = uc[uc.length - 1].text()
                if (fenjin.indexOf("This message violated our Community Guidelines") != -1 || fenjin.indexOf("Due to multiple Community Guideline violations") != -1) {

                    console.log("=============================================================检测出封号了!!!")
                    return true;
                }

                return false;

            } else {
                return false;
            }
        } else {
            return false;
        }
    } catch (error) {
        return false;
    }

}
/**
 * 
 * @returns @method 是否已经发送过消息
 */
function 是否已经发过消息() {
    var uc = id('com.zhiliaoapp.musically:id/l4').find();
    if (uc.length == 0) {
        return false;
    }
    for (var i = 0; i < uc.length; i++) {
        var tv = uc[i];
        var X = tv.bounds().centerX()
        if (X > 300) {
            return true;
        }
    }
    return false;
}

/**
 * @method 要发送的消息
 * @param {} text_value 
 */
function 设置要发的信息(text_value) {
    if (id("buv").exists()) {
        var bqu = id("buv").findOne()
        if (bqu) {
            bqu.setText(text_value)
        }
    } else {
        toast("设置要发的信息不存在")

    }

}

/**
 * @method 点击发送
 */
function 点击发送() {
    if (id("com.zhiliaoapp.musically:id/cog").exists()) {
        var weight = id('com.zhiliaoapp.musically:id/cog').findOne();
        if (weight) {
            var X = weight.bounds().centerX();
            var Y = weight.bounds().centerY();
            click(X, Y)
            sleep(1000)
        }
    } else {
        toast("点击发送按钮不存在.......")
    }
}


/**
 * 发送消息
 */
function 发送信息() {
    var text_value_0 = "I'm sorry to bother you. My company will soon arranged for me to work in the your there. I want to ask you something like about house lease, traffic rules, life in the your place.Go to a new city alone or want to make some close friends intimac LLOLL.";
    var 联系方式 = '';
    var text_value_1 = "If you want to know please add my whatsapp:" + 联系方式;
    var 特殊符号 = Array("꒦ິ^꒦ິ", "ॱଳॱ", "๐˙Ⱉ˙๐", "☾", "ʕ•͡-•ʔ");
    var 是否发送信息 = false
    var 联系方式 = 获取联系方式();
    var 获取特殊符号 = 特殊符号[randomNum(0, 特殊符号.length - 1)]
    var index = randomNum(0, 4);
    //var text_value_0 = "Hello, I am a girl from Malaysia " + 获取特殊符号 + "I see your video content and your photos, " + 获取特殊符号 + "I think you are very handsome, I want to make friends with you, can you add me? I'm looking for a boyfriend.." + 获取特殊符号;
    var text_value_0 = "ĐĂNG KÍ TẶNG NGAY 66K KHÔNG YÊU CẦU NẠP THẮNG CÓ THỂ TRỰC TIẾP RÚT.";

    // if (index == 0) {
    //     //var text_value_0 = "Hello, I am a girl from Malaysia "+获取特殊符号+"I see your video content and your photos, "+获取特殊符号+"I think you are very handsome, I want to make friends with you, can you add me? I'm looking for a boyfriend..";
    //    // var text_value_1 = "Add " + 获取特殊符号 + " me  WhatsApp" + 获取特殊符号 + "+"+                         "+"+ 联系方式;
    // } else if (index == 1) {
    //   //  var text_value_1 = "Add " + 获取特殊符号 + " me  WhatsApp" + 获取特殊符号 + "+"+                         "+"+ 联系方式;
    // } else if (index == 2) {
    //    // var text_value_1 = "Add " + 获取特殊符号 + " me  WhatsApp" + 获取特殊符号 + "+"+                         "+"+ 联系方式;
    // } else if (index == 3) {
    //    // var text_value_1 = "Add " + 获取特殊符号 + " me  WhatsApp" + 获取特殊符号 + "+"+                         "+"+ 联系方式;
    // } else if (index == 4) {

    //     //var text_value_1 = "Add " + 获取特殊符号 + " me  WhatsApp" + 获取特殊符号 + "+"+                         "+"+ 联系方式;
    // }


    var text_value_1 = "Địa chỉ của chúng tôi: " + 联系方式;


    var data_array = Array(text_value_0, text_value_1)
    for (var i = 0; i < 10; i++) {
        if (是否是发信息界面()) {
            if (判断抖音是否被封了()) {
                //toast("已经被封了")
                try {
                    var res = http.get("http://" + 域名 + "/change_status?nickname=" + nickname + "&status=1&action=update_if_banned");
                } catch (error) {
                    console.log(" 被封设备:" + error)
                }
            }

            var j = 0;
            toast("准备发送信息......")
            设置要发的信息(data_array[j]);
            sleep(1000)
            点击发送();
            j = j + 1;
            sleep(1000)
            设置要发的信息(data_array[j]);
            sleep(2000)
            点击发送()
            toast("发消息完毕......返回..")
            id('bja').click()  //点击返回
            sleep(2000)
            //这里统计 私聊个数
            获取今日私聊的个数()
            break;

            // if (是否已经发过消息()) {
            //     //已经发过信息了!  //点击返回
            //     toast("已经发过信息了......返回..")
            //     id('bja').findOne().click()  //点击返回
            //     // sleep(2000)
            //     break;
            // } else {
            //     var j = 0;
            //     toast("准备发送信息......")
            //     设置要发的信息(data_array[j]);
            //     sleep(1000)
            //     点击发送();
            //     j = j + 1;
            //     sleep(1000)
            //     设置要发的信息(data_array[j]);
            //     sleep(2000)
            //     点击发送()
            //     toast("发消息完毕......返回..")
            //     id('bja').click()  //点击返回
            //     sleep(2000)
            //     //这里统计 私聊个数
            //     获取今日私聊的个数()
            //     break;
            // }

        } else {
            toast("等待发送消息界面...")
        }
    }
}







/**
 * @method 遍历所有message
 */
function 遍历所有Message() {
    if (id('bzz').exists()) {
        var uc = id('com.zhiliaoapp.musically:id/bzz').find();
        toast("Message的按钮有:" + uc.length + "个");
        if (uc.length == 0) {
            toast("暂无可以发的信息,继续回去关注......")
            点击Home()
        } else {
            for (var i = 0; i < uc.length; i++) {
                var tv = uc[i];
                if (tv.text() != "Watch") {
                    var Y = tv.bounds().centerY()
                    var X = tv.bounds().centerX()
                    if (X > 0 && Y > 0) {
                        if (Y < 1637) {  //
                            toast("准备点击坐标:" + X + "," + Y)
                            click(X, Y)
                            //sleep(2000)
                            发送信息()
                        }
                    }
                }
            }
        }
    } else {
        toast("没有找到message按钮....框架不稳定?")
    }

}


/**
 * 获取评论的 最后一个名字
 * @returns 
 */
function get_name_for_last() {
    if (id('com.zhiliaoapp.musically:id/c0u').exists()) {
        var uc = id('com.zhiliaoapp.musically:id/c0u').find();
        if (uc.length == 0) {
            return '';
        }
        return uc[uc.length - 1].text()
    } else {
        toast("发送信息-没有过去到最后一个名字")
        return '';

    }
}



/**
 * message 滑动
 */
function 滑动Message() {
    toast("准备滑动........")
    swipe(80, 1537, 80, 292, 1200)

}

/**
 *  获取 用户 私聊权限是否开启
 * @returns 
 */
function 获取是否发信息() {
    host = 'http://' + 域名 + '/get_phone_permissions_for_message?nickname=' + nickname
    for (var i = 0; i < 10000000; i++) {
        try {
            var res = http.get(host);
            if (res.statusCode != 200) {
                toast("请求失败: " + res.statusCode + " " + res.statusMessage);
            } else {
                var weather = res.body.json()
                if (weather) {
                    if (weather.code == 200) {
                        if (weather.msg == 0) {
                            //不发送信息
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        toast(weather.msg)
                    }
                }
            }
            sleep(2000)
        } catch (error) {
            toast("获取是否发信息 异常退出!!!!" + error);
            console.log("获取是否发信息:" + error)
            return false;
        }
    }

}


/**
 * 
 * @param  packageName 
 * 关闭抖音
 */
function 关闭应用(packageName) {
    var 关闭应用现在的时间戳 = timest()
    var name = getPackageName(packageName);
    if (!name) {
        if (getAppName(packageName)) {
            name = packageName;
        } else {
            return false;
        }
    }
    app.openAppSetting(name);
    text(app.getAppName(name)).waitFor();
    while (true) {
        if (timest() - 关闭应用现在的时间戳 > 30) {
            //等待超时大于30 秒
            var name = getPackageName(packageName);
            if (!name) {
                if (getAppName(packageName)) {
                    name = packageName;
                } else {
                    return false;
                }
            }
            app.openAppSetting(name);
            text(app.getAppName(name)).waitFor();
            var 关闭应用现在的时间戳 = timest()
        } else {
            if (id("right_button").exists()) {
                text("FORCE STOP").click();
                sleep(2000)
                text("OK").click()
                break  //退出循环
            } else {
                toast("right_button 按钮不存在")
                back() //返回
            }
        }


        sleep(2000)
    }


}











/**
 * 发消息 进程
 */
function send_message() {
    var hint = ""
    关闭应用("com.zhiliaoapp.musically")
    sleep(3000)
    launchApp("TikTok");
    sleep(4000)
    if_get_follow_today = false;
    var 脚本的速度 = 1000;
    var hint_one = "";
    var the_last_name = "";
    var 是否点击了followers = false
    var 点击index的次数 = 0
    while (true) {
        if (hint_one == "") {
            if (text("Me").exists()) {
                toast("成功打开国际版抖音....send_message")
                hint_one = "获取评论"
            } else {
                toast("打开抖音失败....,准备重启..send_message")
                关闭应用("com.zhiliaoapp.musically")
                sleep(3000)
                launchApp("TikTok");
                sleep(6000)
            }
        } else if (hint_one == "获取评论") {
            if (是否是主界面() || id('cg0').exists()) {
                click(960, 1800)
                toast("点击me")
            } else if (id('c0h').exists()) {
                toast("点击 All activity")
                click(500, 120)
                sleep(2000)
                是否点击了followers = true
            } else if (id('ayh').exists()) {
                toast("点击 followers")
                click(500, 800)
                toast("等待加载......")
                sleep(2000)
                hint_one = "发消息"
            } else if (id('aqp').exists()) {
                get_follows()
                if (点击index的次数 < 10) {
                    click(750, 1800)
                    toast("点击index")
                } else {
                    点击index的次数 = 点击index的次数 + 1
                    sleep(1000)
                    break;
                }

            } else if (text("Refresh").exists()) {
                toast("断网了.....,点击刷新")
                text("Refresh").findOne().click()

            } else {

                toast("未知界面.....")
            }
        } else if (hint_one == "发消息") {
            遍历所有Message()
            hint_one = "滚动"
        } else if (hint_one == "滚动") {

            //获取最后 判断是否到底
            the_last_name = get_name_for_last();
            滑动Message()
            sleep(2000)
            // 改变抖音链接已使用的状态()
            // break; //跳出循环
            if (the_last_name == get_name_for_last()) {
                // 改变抖音链接已使用的状态()
                上传私信数量() //上传私信数量
                toast("好友已经到底了.........停止发消息.....休息下...准备继续点赞.. 关注..")
                break; //跳出循环
            }
            sleep(2000)
            hint_one = "发消息"
        } else {
            toast("脚本正在执行 发送消息...hint_one:" + hint_one)
        }

        sleep(脚本的速度)
    }


}


function 获取当前时间() {
    curTime = new Date();
    return curTime.getHours() + ":" + curTime.getMinutes();
}



function 单机模式总线程() {
    var 点赞是否启动 = false
    var 点赞线程
    var 回复进程
    var 关闭抖音 = false
    while (true) {
        if (获取当前时间() == "1000:0" || 获取当前时间() == "300:0" || 获取当前时间() == "1001:0") {  //刘的私聊时间
            // if (获取当前时间() == "1:0" || 获取当前时间() == "3:0" || 获取当前时间() == "5:0" || 获取当前时间() == "12:30") {  //啊撤的私聊时间
            // if (获取当前时间() == "15:45") {  //啊撤的私聊时间
            if (关闭抖音) {
                toast("子线程:发消息正在进行.....")
                sleep(3000)
            } else {
                console.log("准备去 评论")
                // 点赞线程.getEngine().forceStop()
                点赞线程.interrupt();
                关闭抖音 = true
                send_message()
                点赞是否启动 = false;
                关闭抖音 = false;
            }
        } else {
            if (点赞是否启动) {
                toast("子线程:关注正在进行.....")
                sleep(10000)
            } else {
                点赞是否启动 = true
                // var 点赞线程= engines.execScript("新环境",main_one().toString())
                点赞线程 = threads.start(function () {
                    //在新线程执行的代码
                    main_one()
                });
            }
        }
        sleep(2000)
        try {
            var res = http.get("http://" + 域名 + "/change_status?nickname=" + nickname + "&status=1");
        } catch (error) {
            console.log(" 心跳检测异常抛出:" + error)
        }


    }

}









// 单机模式总线程()
function 获取今日已经关注的数量() {
    var keep_path = "/sdcard/抖音/";
    var date = new Date();
    var 日期 = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate()
    if (files.exists("/sdcard/抖音/" + 日期 + "-nums" + ".txt")) {
        // 获取旧的
        var old = files.read("/sdcard/抖音/" + 日期 + "-nums" + ".txt")
        var k = parseInt(old) + 1
        files.write("/sdcard/抖音/" + 日期 + "-nums" + ".txt", k);
        return k
    } else {
        //创建路径
        files.ensureDir("/sdcard/抖音/" + 日期 + "-nums" + ".txt");//创建路径
        files.write("/sdcard/抖音/" + 日期 + "-nums" + ".txt", 0);
        files.ensureDir(keep_path);//创建路径
        return 0;
    }
}


function 上传今日关注数() {
    var keep_path = "/sdcard/抖音/";
    var date = new Date();
    var 日期 = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate()
    if (files.exists("/sdcard/抖音/" + 日期 + "-nums" + ".txt")) {
        // 获取旧的
        var old = files.read("/sdcard/抖音/" + 日期 + "-nums" + ".txt")
        return old
    } else {
        //创建路径
        files.ensureDir("/sdcard/抖音/" + 日期 + "-nums" + ".txt");//创建路径
        files.write("/sdcard/抖音/" + 日期 + "-nums" + ".txt", 0);
        files.ensureDir(keep_path);//创建路径
        return 0;
    }
}



function 获取今日私聊的个数() {
    var keep_path = "/sdcard/抖音/";
    var date = new Date();
    var 日期 = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate()
    if (files.exists("/sdcard/抖音/" + 日期 + "-message" + ".txt")) {
        // 获取旧的
        var old = files.read("/sdcard/抖音/" + 日期 + "-message" + ".txt")
        var k = parseInt(old) + 1
        files.write("/sdcard/抖音/" + 日期 + "-message" + ".txt", k);
        return k
    } else {
        //创建路径
        files.ensureDir("/sdcard/抖音/" + 日期 + "-message" + ".txt");//创建路径
        files.write("/sdcard/抖音/" + 日期 + "-message" + ".txt", 0);
        files.ensureDir(keep_path);//创建路径
        return 0;
    }
}


function 上传今日私聊的个数() {
    var keep_path = "/sdcard/抖音/";
    var date = new Date();
    var 日期 = date.getFullYear() + "-" + date.getMonth() + "-" + date.getDate()
    if (files.exists("/sdcard/抖音/" + 日期 + "-message" + ".txt")) {
        // 获取旧的
        var old = files.read("/sdcard/抖音/" + 日期 + "-message" + ".txt")
        return old
    } else {
        //创建路径
        files.ensureDir("/sdcard/抖音/" + 日期 + "-message" + ".txt");//创建路径
        files.write("/sdcard/抖音/" + 日期 + "-message" + ".txt", 0);
        files.ensureDir(keep_path);//创建路径
        return 0;
    }


}






单机模式总线程()













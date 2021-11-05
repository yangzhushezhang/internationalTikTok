


/**
 * @user wy
 * version v8.28.0.305 
 * 安卓 7.1.1 1+
 */


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






手机屏幕宽 = 1080
var type = 1  //1直播间采集的 链接 2短视频采集的链接
var nickname = ""
var 域名 = ""
var 全局抖音ID = ""
path = "/sdcard/Pictures/nickname.txt";
var 全局的最后一个加粉名字 = ""

if (files.exists(path)) {
    nickname = files.read(path);
    toast("昵称:" + nickname)
} else {
    toast("昵称不存在,请写好配置文件!!!在运行")
    exit();
}



path = "/sdcard/Pictures/ip.txt";
if (files.exists(path)) {
    域名 = files.read(path);
    toast("使用域名:" + 域名)
} else {
    toast("域名不存在,请写好配置文件!!!在运行")
    exit();

}


function timest() {
    var tmp = Date.parse(new Date()).toString();
    tmp = tmp.substr(0, 10);
    return tmp;
}

function 打开微视() {
    launch("com.tencent.weishi");
}

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


function 关闭微视() {
    关闭应用("com.tencent.weishi")
}


// 打开微视()




//中国的公厕高科技到什么程度？>>https://isee.weishi.qq.com/ws/app-pages/share/index.html?wxplay=1&id=7fqr6w2oz1M259EmG&spid=2151113968253298580&qua=v1_and_weishi_8.33.0_588_312027000_d&chid=100081014&pkg=&attach=cp_reserves3_1000370011




/**
 * 从服务器获取
 */
function 切换链接() {
    app.startActivity({
        data: "weishi://feed?feed_id=7fqr6w2oz1M259EmG",
    });
}



function 是否存在评论控件() {
    if (id("qfy").exists()) {
        return true;
    }
    return false;
}


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

function 判断性别() {
    var sex = "男"
    var 是否复制了连接 = true
    var 已经复制 = false
    //获取性别
    for (var i = 0; i < 10; i++) {
        if (id("sys").exists()) {
            //判断是否已经关注了
            if(text("已关注").exists() ){
                toast("这个粉丝已经关注了...点击返回")
                if (id("rh").exists()) {
                    id("rh").findOne().click()
                    break;
                }
            }

    
            if (是否复制了连接) {
                if (已经复制) {
                    var b = getClip()
                    toast("剪贴板内容为:" + b);
                    b = b.substr(-16)
                    if (get_personage_id(b)) {  //名字重复了
                        //判断名字是否重复 去服务器查看
                        toast("名字重复了....")
                        if (id("rh").exists()) {
                            id("rh").findOne().click()
                            break;
                        }
                    } else {
                        //关注
                        是否复制了连接 = false
                        已经复制 = false
                    }
                } else {
                    toast("点击更多按钮.....")
                    id("sys").findOne().click()
                }
            } else {
                if (text(sex).exists()) {
                    toast("性别:" + sex + "  点击关注.....")
                    click(900, 410)  //点击关注
                    var 今日关注数量 = 获取今日已经关注的数量()
                    toast("今日关注数量:" + 今日关注数量)
                    if (今日关注数量 >= 199) {
                        toast("今日关注已经达到上限......准备关闭脚本")
                        sleep(5000)
                        exit()
                    }
                    sleep(2000)
                    if (id("rh").exists()) {
                        id("rh").findOne().click()
                        break;
                    }
                } else {
                    toast("性别不存在...点击返回")
                    if (id("rh").exists()) {
                        id("rh").findOne().click()
                        break;
                    }
                }

            }
        } else if (text("选择分享方式").exists()) {
            if(已经复制){
                toast("复制完毕,等待判断性别....")

            }else{
                toast("准备复制连接....")
                click(120, 1700)
                已经复制 = true
            }
        
        } else if (id("wer").exists()) {
            toast("点击取消")
            click(500, 1850)
            sleep(2000)
            if (id("rh").exists()) {
                id("rh").findOne().click()
                break;
            }
        } else {
            toast("等待个人资料界面......")
        }
    }
}










function 滑动评论() {
    if (id('avatar').exists()) {
        // swipe(84, 1513, 84, 505, 1200)
        swipe(84, 1746, 84, 749, 1200)
        sleep(2000)
    } else {
        toast("头像控件不可以滑动.....")

    }
}




/**
 * @method 获取微视的微视号@id
 * @returns 
 */
function get_id_name() {
    if (id('sys').exists()) {
        for (var i = 0; i < 1000000; i++) {
            try {
                var a = className("android.widget.TextView").find()
                var 微视号 = a[0].text()
                if (微视号 == "关注") {
                    微视号 = 微视号 = a[1].text()
                } else {
                    微视号 = 微视号.slice(4)
                }
                console.log("http://" + 域名 + "/weishi/checkName?name=" + 微视号)
                var result = http.get("http://" + 域名 + "/weishi/checkName?name=" + 微视号);
            
                if (result.statusCode != 200) {
                    toast("上传 id请求失败: " + result.statusCode + " " + result.statusMessage);
                } else {
                    var data = result.body.string();
                    //返回 1   不存在 或者存在 
                    var v = JSON.parse(data);
                    log(v)
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
        toast("获取控件 sys 失败,没有办法上传id")
        return false;
    }
    return false;
}


/**
 * @method 获取个人主页的id
 * @returns 
 */
function get_personage_id(微视号) {
    for (var i = 0; i < 1000000; i++) {
        try {
            console.log("http://" + 域名 + "/weishi/checkName?name=" + 微视号)
            var result = http.get("http://" + 域名 + "/weishi/checkName?name=" + 微视号);
            log(result)
            if (result.statusCode != 200) {
                toast("上传 id请求失败: " + result.statusCode + " " + result.statusMessage);
            } else {
                var data = result.body.string();
                //返回 1   不存在 或者存在 
                var v = JSON.parse(data);
                log(v)
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
}








function 关注() {
    var 是否点赞 = true
    var 休息时间 = 5000 // 2000 =2秒
    if (id("avatar").exists()) {
        //遍历
        var uc = id('com.tencent.weishi:id/avatar').find();    //X=80 min_y=749  max_y=1746
        toast("头像个数有:" + uc.length + "个");
        if (uc.length == 0) {
            toast("没有头像个数")
            return;
        } else {
            for (var i = 0; i < uc.length; i++) {
                console.log(i);
                var tv = uc[i];
                var X = tv.bounds().centerX()
                var Y = tv.bounds().centerY()
                if (749 < Y && Y < 1746) {
                    toast("点击第:" + i + "个头像 坐标:" + X + "," + Y);
                    sleep(1000)
                    click(X, Y)
                    log(X, Y)
                    sleep(2000)
                    判断性别()
                    // if (是否点赞) {
                    //     if (X < 137) {
                    //         toast("点赞")
                    //         click(X, 780)
                    //     }
                    // }
                }
                sleep(休息时间)
            }
        }
    } else {
        toast("评论控件不存在....")
    }
}







function 主程序() {
    var hint = ""
    关闭微视()
    toast("关闭微视......")
    sleep(3000)
    打开微视()
    sleep(3000)
    toast("打开微视.....")
    var 打开微视时间 = timest()
    var 是否点击评论控件 = false
    var 进入微视关注时间 = timest()
    var 进入切换链接时间 = timest()
    while (true) {
        if (hint == "") {
            if (是否存在评论控件()) {
                toast("成功打开微视")
                hint = "关注"
            } else if (text("青少年保护功能提示").exists()) {
                toast("关闭青少年保护功能提示")
                if (id("pfy").exists()) {
                    id("pfy").findOne().click()
                }


            } else {
                if (timest() - 打开微视时间 > 60) {
                    toast("打开微视超时....")
                    关闭微视()
                    toast("关闭微视......")
                    sleep(3000)
                    打开微视()
                    sleep(3000)
                    toast("打开微视.....")
                    打开微视时间 = timest()
                    进入微视关注时间 = timest()
                    是否点击评论控件 = false
                } else {
                    toast("等待进入微视首页.....")
                    sleep(1000)
                }
            }
        } else if (hint == "关注") {
            if (是否存在评论控件()) {
                if (是否点击评论控件) {
                    toast("等待评论出现.....")
                } else {
                    toast("点击评论")
                    id("qfy").findOne().click()  //点击评论
                    是否点击评论控件 = true
                }
            } else if (id("avatar").exists()) {   //评论头像id 
                toast("准备去关注了...")
                关注()
                进入微视关注时间 = timest()
                hint = "切换链接"
                进入切换链接时间 = timest()

            } else {
                if (timest() - 进入微视关注时间 > 60) {
                    toast("hint=关注 超时,准备重启")
                    hint = ""
                    打开微视时间 = timest()  //重新赋值

                } else {
                    toast("hint=关注  位置界面......")
                }

            }
        } else if (hint == "切换链接") {
            if (timest() - 进入微视关注时间 > 60) {
                toast("hint=切换链接 超时,准备重启")
                hint = ""
                打开微视时间 = timest()  //重新赋值
            } else {
                toast("准备滑动........")
                滑动评论()
                hint = "关注"
            }

        }

        toast("主程序:关注正在运行......")
    }


}



主程序()













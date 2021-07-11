

var gdrequestloginAddressUrl = "";

$.getJSON("json/config.json", function(data) {


    gdrequestloginAddressUrl = data.global_requestloginAddress
    // console.log("后台登录："+gdrequestloginAddressUrl);

});

layui.use(['form', 'layer', 'jquery'], function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : top.layer
    $ = layui.jquery;


    //登录按钮
    form.on("submit(login)", function (data) {



        var username = $("#username").val();
        var password = $("#password").val();


        // console.log(username,password);
        //声明json格式的请求数据
        // var requestdata = {"admin_name": username, "admin_password": password};


        //模拟登录
        // window.location.href = "../../index.html";
        //



        $.ajax({
            url: gdrequestloginAddressUrl+"?username="+username+"&password="+password,
            type: "GET",
            dataType:"json",
            success: function (resultJson) {

                if (1 === resultJson.code) {
                    // alert("登录成功!");


                    layer.msg("登录成功");

                    setTimeout(function (){

                        var millisecond = new Date().getTime();
                        var expiresTime = new Date(millisecond + 10000000000000000000 * 1000 * 10000000000000000000);

                        localStorage.setItem("changeRefresh","true");
                        // console.log("设置localStorage"+localStorage.getItem("changeRefresh"));
                        var tokenCK  = "R42jjp6dbA8O7AUyDkitbArZmayCD47hS5by324324";
                        // var tokenCK  = resultJson.result.token;
                        //var date = new Date();
                        //date.setTime(date.getTime()+10*1000);//只能这么写，10表示10秒钟

                        //jquery 设置cookies方法
                        //$.cookie('token',resultJson.result.token,{expires: expiresTime});

                        //js设置cookies方法

                        JsSetCookie('tokenMyb',tokenCK);
                        //document.cookie = resultJson.result.token;
                        //document.cookie=tokenCK;


                        // console.log(document.cookie);
                        window.location.href = "../dyadminold/index.html";


                    },2000)


                }else{

                    layer.msg(resultJson.msg);
                    //alert(resultJson.code);

                }


            },


        });

        return false;
    })


    //设置CK方法
    function JsSetCookie(name,value)//两个参数，一个是cookie名称，一个是值
    {
        var millisecond = new Date().getTime();
        var expiresTime = new Date(millisecond + 60 * 1000 * 60 * 24 );
        // var expiresTime = new Date(millisecond + 60 * 1000 * 60);


        document.cookie = name + "="+ escape (value) + ";expires=" + expiresTime.toGMTString();
        // document.cookie = name + "="+ escape (value) + ";expires=" + expiresTime.toGMTString();
    }

    //表单输入效果
    $(".loginBody .input-item").click(function (e) {
        e.stopPropagation();
        $(this).addClass("layui-input-focus").find(".layui-input").focus();
    })

    $(".loginBody .layui-form-item .layui-input").focus(function () {
        $(this).parent().addClass("layui-input-focus");
    })
    $(".loginBody .layui-form-item .layui-input").blur(function () {
        $(this).parent().removeClass("layui-input-focus");
        if ($(this).val() != '') {
            $(this).parent().addClass("layui-input-active");
        } else {
            $(this).parent().removeClass("layui-input-active");
        }
    })
})

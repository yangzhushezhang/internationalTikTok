//
global_requestAddress = "";
//登录接口
global_requestloginAddress_js="/login";

//脚本上传更新接口
global_requestDyGetVersionUrl_js="/script_update";

//获取抖音链接接口(含未使用(status=0),已被使用(status=2),正在使用(status=1))
global_requestDywshUrl_js="/get_list_for_dy_url";

//添加抖音链接接口(含单个添加get和批量添加post)
global_requestDyAddUrl_js="/add_Dy_url";

//删除抖音链接接口
global_requestDyDelUrl_js="/del_dyUrl";

//链接状态改变接口
global_requestDyChangeUrl_js="/change_status_for_Dy_for_admin";

//获取手机设备的接口(含未运行(status=0),运行中(status=1),已禁用(status=2))
global_requestDyGetSjlbUrl_js="/get_users";

//改变手机设备的(封禁状态/是否假粉)
global_requestDychange_status_js="/change_status"

//更改手机设备配置
global_requestupdate_data="/update_data";


//开启私聊接口(目前这个接口废弃使用)
global_requestDychannelSjlbUrl_js="/change__permissions_for_message";

//获取粉丝查看接口
global_requestDyGetfsglbUrl_js="/get_follows";

//删除粉丝数据接口
global_requestDydelete_follows_js="/delete_follows";

//获取私聊管理联系人接口
global_requestDygetcontact_js="/get_contact";

//私聊管理添加联系人接口
global_requestDyGetaddchat_js="/add_chat";

//私聊管理删除联系人接口
global_requestDycontactdel_js="/contact_del"

//获取首页当天统计数据接口
global_requestDyget_total_all="/get_total_all";

//获取粉丝统计数据接口
global_requestDyget_total_list="/get_total_list";

//批量添加UID模板数据接口
global_requestDyget_dy_addUserId="/dy/addUserId";

//批量添加UID模板数据接口(新)
global_requestDyget_set_uid="/set_uid";

//获取活粉接口(新)
global_requestDyget_set_fans="/set_fans";

//获取Ck接口(新)
global_requestDyget_setCookies="/setCookies";

//监听up主(新)
global_requestDyget_setmonitortiktokupname="/setmonitortiktokupname";

//web_cookie(新)
global_requestDyget_SetCookiesForCollectionVideoFans="/SetCookiesForCollectionVideoFans";

//web获取粉丝
global_requestDyget_GetFasFormVideo="/GetFasFormVideo";

//获取视频
global_requestDyget_GetVideoUrl="/GetVideoUrl";

//获取配置
global_requestDyget_Config="/Config";

//一键重置
global_requestDyget_OneKey="/OneKey";

//获取日志
global_requestDyget_Journal="/Journal";

//whatapp获取手机接口
global_requestDyget_getPhone="/getPhone";

//激活码
global_requestDyget_importActivationCode="/importActivationCode";


var getRootPath_webStr = getRootPath_web();

//获取目录路径方法
function getRootPath_web() {

		//获取当前网址，如： http://localhost:8888/eeeeeee/aaaa/vvvv.html
		var curWwwPath = window.document.location.href;
		//获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
		var pathName = window.document.location.pathname;
		var pos = curWwwPath.indexOf(pathName);
		//获取主机地址，如： http://localhost:8888
		var localhostPaht = curWwwPath.substring(0, pos);
		//获取带"/"的项目名，如：/abcd
		var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1);

		// return (localhostPaht + projectName);


		// console.log("当前网址:"+curWwwPath);
		// console.log("主机地址后的目录:"+pos+"----"+pathName);
		// console.log("主机地址:"+localhostPaht);
		// console.log("项目名:"+projectName);


		return projectName;
}



//时间戳转日期时间型工具类
function formatDateTime(inputTime) {
	var date = new Date(inputTime);
	var y = date.getFullYear();
	var m = date.getMonth() + 1;
	m = m < 10 ? ('0' + m) : m;
	var d = date.getDate();
	d = d < 10 ? ('0' + d) : d;
	var h = date.getHours();
	h = h < 10 ? ('0' + h) : h;
	var minute = date.getMinutes();
	var second = date.getSeconds();
	minute = minute < 10 ? ('0' + minute) : minute;
	second = second < 10 ? ('0' + second) : second;
	return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;
}


//时间日期型转时间戳
function strToTime(strtime) {
	let date = strtime.substring(0,19);
	date = new Date(date.replace(/-/g, '/'));
	let time1 = date.getTime();
	let time2 = date.valueOf();
	let time3 = Date.parse(date)/1000;

	return time3
}
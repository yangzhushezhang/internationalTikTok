var requertUrl =""


/**
 * 已知前后文 取中间文本
 * @param str 全文
 * @param start 前文
 * @param end 后文
 * @returns 中间文本 || null
 */
function getStr(str, start, end) {
    let res = str.match(new RegExp(`${start}(.*?)${end}`))
    return res ? res[1] : null
}


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

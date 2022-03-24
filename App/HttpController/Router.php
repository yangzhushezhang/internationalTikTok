<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use FastRoute\RouteCollector;

class Router extends AbstractRouter
{
    function initialize(RouteCollector $routeCollector)
    {
        /*
          * eg path : /router/index.html  ; /router/ ;  /router
         */


        //登录功能
        $routeCollector->get('/login', '/Admin/Login/login');
        #script_update
        $routeCollector->get('/script_update', '/Admin/Login/script_update');
        $routeCollector->post('/script_update', '/Admin/Login/script_update');


        //抖音 和服务器通讯
        $routeCollector->get('/get_config', '/Admin/Config/get_config');
        //获取手机
        $routeCollector->get('/get_users', '/Admin/Config/get_users');
        $routeCollector->get('/get_impower', '/Admin/Config/get_impower');
        //修改 update_data
        $routeCollector->post('/update_data', '/Admin/Config/update_data');


        # 设备获取 运行类型
        $routeCollector->get('/get_running_model', '/Admin/Config/get_running_model');


        /**
         * WeiShi_ConfigController.php
         */
        $routeCollector->get('/wei_shi/script_update', '/Admin/WeiShi_ConfigController/script_update');
        $routeCollector->post('/wei_shi/script_update', '/Admin/WeiShi_ConfigController/script_update');


        # 测试Test
        $routeCollector->post('/get_sex_form_my_server', '/Admin/Config/get_sex_form_my_server');
        //一次修改配置 update_data_All
        $routeCollector->post('/update_data_All', '/Admin/Config/update_data_All');
        //批量授权 impowerAll
        $routeCollector->post('/impowerAll', '/Admin/Config/impowerAll');
        //runAll
        $routeCollector->post('/runAll', '/Admin/Config/runAll');
        //stopAll
        $routeCollector->post('/stopAll', '/Admin/Config/stopAll');
        #get_phone_permissions_for_message

        $routeCollector->get('/get_phone_permissions_for_message', '/Admin/Config/get_phone_permissions_for_message');

        #change__permissions_for_message
        $routeCollector->get('/change__permissions_for_message', '/Admin/Config/change__permissions_for_message');

        //run_one
        $routeCollector->get('/run_one', '/Admin/Config/run_one');


        //del
        $routeCollector->get('/del', '/Admin/Config/del');

        //插入抖音名字  set_name

        $routeCollector->get('/set_name', '/Admin/Config/set_name');

        #set_new_phone

        $routeCollector->get('/set_new_phone', '/Admin/Config/set_new_phone');


        //获取粉丝数followers
        $routeCollector->get('/get_follows', '/Admin/Follows/followers');


        //添加粉丝数据addFollow
        $routeCollector->get('/addFollow', '/Admin/Follows/addFollow');

        //修改所有配置的 if_get_follow
        $routeCollector->get('/update_follow', '/Admin/Follows/update_follow');


        // get_contact

        $routeCollector->get('/get_contact', '/Admin/Contact/get_contact');


        //contact_del

        $routeCollector->get('/contact_del', '/Admin/Contact/contact_del');


        #add_Dy_url
        $routeCollector->get('/add_Dy_url', '/Admin/Contact/add_Dy_url');
        $routeCollector->post('/add_Dy_url', '/Admin/Contact/add_Dy_url');

        #get_list_for_dy_url
        $routeCollector->get('/get_list_for_dy_url', '/Admin/Contact/get_list_for_dy_url');
        #change_status
        $routeCollector->get('/change_status', '/Admin/Follows/change_status');

        //update_for_message_status
        $routeCollector->get('/update_for_message_status', '/Admin/Follows/update_for_message_status');
        #get_Dy_link_id
        $routeCollector->get('/get_Dy_link_id', '/Admin/Contact/get_Dy_link_id');


        //add_chat
        $routeCollector->get('/add_chat', '/Admin/Contact/add_chat');
        // get_one
        $routeCollector->get('/get_one', '/Admin/Contact/get_one');

        #del_dyUrl

        $routeCollector->get('/del_dyUrl', '/Admin/Dy_url/del_dyUrl');
#change_status_for_Dy_for_admin
        $routeCollector->get('/change_status_for_Dy_for_admin', '/Admin/Dy_url/change_status_for_Dy_for_admin');


        #change_status_for_Dy
        $routeCollector->get('/change_status_for_Dy', '/Admin/Dy_url/change_status_for_Dy');


        #set_send_message
        $routeCollector->get('/set_send_message', '/Admin/Login/set_send_message');
        #get_send_if
        $routeCollector->get('/get_send_if', '/Admin/Login/get_send_if');

        # delete_follows
        $routeCollector->get('/delete_follows', '/Admin/Follows/delete_follows');

        #addFollow_one
        $routeCollector->get('/addFollow_one', '/Admin/Follows/addFollow_one');

        $routeCollector->get('/router', '/test');
        $routeCollector->post('/test', '/test');


        // 关注试试更
        $routeCollector->get('/attentionTheLastTime', '/Admin/Config/attentionTheLastTime');

        # 链接的评论人数上传 set_comments
        $routeCollector->get('/set_comments', '/Admin/Contact/set_comments');


        $routeCollector->get('/two_add_id', '/Admin/TwoDy/two_add_id');
        #get_two_dy_id
        $routeCollector->get('/get_two_dy_id', '/Admin/TwoDy/get_two_dy_id');

        #Add_total


        #get_list
        $routeCollector->get('/get_list', '/Admin/TwoDy/get_list');

        /**
         * Total.php
         */
        $routeCollector->get('/Add_total', '/Admin/Total/Add_total');
        #get_total_all
        $routeCollector->get('/get_total_all', '/Admin/Total/get_total_all');
        #get_total_list
        $routeCollector->get('/get_total_list', '/Admin/Total/get_total_list');
        # PyAddDyUrl python 添加
        $routeCollector->get('/PyAddDyUrl', '/Admin/Contact/PyAddDyUrl');


        /**
         * WeiShi_NameController.php
         */
        #微视检查微视号是否重复
        $routeCollector->get('/weishi/checkName', '/Admin/WeiShi_NameController/checkName');


        /**
         * App/HttpController/Admin/UserIdController.php
         */
        $routeCollector->post('/dy/addUserId', '/Admin/UserIdController/addUserId');
        $routeCollector->get('/dy/getOne', '/Admin/UserIdController/getOne');


        /**
         * WeiShiVideoLinkController.php
         */
        $routeCollector->post('/weishi/batchUploadLinks', '/Admin/WeiShiVideoLinkController/batchUploadLinks');
        #GetLinkOne
        $routeCollector->get('/weishi/GetLinkOne', '/Admin/WeiShiVideoLinkController/GetLinkOne');


        # CollectForMarKet 采集宝宝
        $routeCollector->post('/CollectForMarKet', '/Admin/CollectForMarKet/collectForMarKet');
        # getAllWc
        $routeCollector->get('/getAllWc', '/Admin/CollectForMarKet/getAllWc');
        #setInformation
        $routeCollector->get('/setInformation', '/Admin/CollectForMarKet/setInformation');
        #getOneInformation
        $routeCollector->get('/getOneInformation', '/Admin/CollectForMarKet/getOneInformation');
        # setWcModel
        $routeCollector->get('/setWcModel', '/Admin/CollectForMarKet/setWcModel');

        #getWcModel
        $routeCollector->get('/getWcModel', '/Admin/CollectForMarKet/getWcModel');

        #IsExistModel
        $routeCollector->get('/IsExistModel', '/Admin/CollectForMarKet/IsExistModel');
        #deleteModel
        $routeCollector->get('/deleteModel', '/Admin/CollectForMarKet/deleteModel');
        #IfShopping
        $routeCollector->post('/IfShopping', '/Admin/CollectForMarKet/IfShopping');
        #GetTaskResult
        $routeCollector->get('/GetTaskResult', '/Admin/CollectForMarKet/GetTaskResult');
        #updateModel
        $routeCollector->get('/updateModel', '/Admin/CollectForMarKet/updateModel');
        #ifShoppingInformation  购买接口


        $routeCollector->post('/ifShoppingInformation', '/Admin/CollectForMarKet/ifShoppingInformation');
        #importModel
        $routeCollector->post('/importModel', '/Admin/CollectForMarKet/importModel');
        # backModel
        $routeCollector->get('/backModel', '/Admin/CollectForMarKet/backModel');
        #selecting
        $routeCollector->get('/selecting', '/Admin/CollectForMarKet/selecting');
        #GetRec
        $routeCollector->get('/GetRec', '/Admin/CollectForMarKet/GetRec');
        #delRec
        $routeCollector->get('/delRec', '/Admin/CollectForMarKet/delRec');
        #delAll
        $routeCollector->get('/delAll', '/Admin/CollectForMarKet/delAll');
        #changeSwitch
        $routeCollector->get('/changeSwitch', '/Admin/CollectForMarKet/changeSwitch');
        #get_cw_modelOne
        $routeCollector->get('/get_cw_modelOne', '/Admin/CollectForMarKet/get_cw_modelOne');
        #add_cw_address
        $routeCollector->post('/add_cw_address', '/Admin/CollectForMarKet/add_cw_address');
        #select_cw_addres
        $routeCollector->post('/select_cw_address', '/Admin/CollectForMarKet/select_cw_address');
        #GetBaPrice TestController.php
        $routeCollector->get('/GetBaPrice', '/Admin/TestController/GetBaPrice');
        /**
         *
         * 采集视频 活粉 api
         */

        //set_uid UidController.php

        $routeCollector->get('/set_uid', '/Admin/UidController/set_uid');
        $routeCollector->post('/set_uid', '/Admin/UidController/set_uid');
        //set_fans
        $routeCollector->get('/set_fans', '/Admin/UidController/set_fans');
        #SetLastTimeOfVido
        $routeCollector->get('/SetLastTimeOfVido', '/Admin/UidController/SetLastTimeOfVido');
        #setCookies
        $routeCollector->post('/setCookies', '/Admin/UidController/setCookies');

        /*
         * eg path : /closure/index.html  ; /closure/ ;  /closure
         */
        $routeCollector->get('/closure', function (Request $request, Response $response) {
            $response->write('this is closure router');
            //不再进入控制器解析
            return false;
        });
    }
}
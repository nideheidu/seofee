<?php
//// +----------------------------------------------------------------------
//// | ThinkPHP [ WE CAN DO IT JUST THINK ]
//// +----------------------------------------------------------------------
//// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
//// +----------------------------------------------------------------------
//// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
//// +----------------------------------------------------------------------
//// | Author: liu21st <liu21st@gmail.com>
//// +----------------------------------------------------------------------
//
////Route::get('think', function () {
////    return 'hello,ThinkPHP5!';
////});
////
////Route::get('hello/:name', 'index/hello');
////
////return [
////
////];
use think\facade\Route;

//客户接口路由
Route::group('v1',function (){
    //登录
    Route::rule('user/login','api/Login/dologin','post')->ext('html');
    Route::rule('user/reg','api/User/doreg','post')->ext('html');
    Route::rule('admin/login','api/Login/adminLogin','post')->ext('html');
    Route::rule('test','test','post')->ext('html');
    Route::rule('verify','api/Verify/index','get')->ext('html');
    
    Route::rule('initView','api/User/getViewData','get');

    Route::rule('api/test','api/Simulation/getTask','get')->ext('html');

    Route::group([],function (){
        Route::rule('init','api/User/init','get');
        Route::rule('getrank','api/Task/get_rank_log','post');
        Route::rule('get_user_info','api/User/info','get');
        Route::rule('editPassword','api/User/editPassword','get'); 
        Route::rule('notice','api/User/notice','get');
        Route::rule('get_user_money_info','api/User/getUserMoneyInfo','post');
        Route::rule('menu/list','api/Meun/index','get');   //获取菜单列表
        Route::rule('task/list','api/Task/keywords_lists','get');   //关键字列表
        Route::rule('task/feedbacklist','api/Task/feedback_lists','get');   //关键字列表
        Route::rule('keyword/add','api/Task/add_keywords','post');   //添加关键字
        Route::rule('keyword/update','api/Task/update_keywords_rank','post');   //添加关键字
        Route::rule('keyword/addquick','api/Task/add_keywords_quick','post');   //提交审核
        Route::rule('keyword/edit','api/Task/edit_keywords','post');   //编辑关键字
        Route::rule('keyword/del','api/Task/del_keywords','post');   //删除关键字
        Route::rule('keyword/back','api/Task/back','post');   //删除关键字
        Route::rule('keyword/stopback','api/Task/stopback','post');   //停止审核撤回
        Route::rule('keyword/changeFee','api/Task/changeFee','post');   //删除关键字
        Route::rule('task/feedback','api/Task/feedback','post');   //非首页词反馈

        Route::rule('keywordrank/list','api/Task/keywordrank','get');   //客户关键字排名查询
        Route::rule('keywordrank/add','api/Task/add_keywordrank','post');   //客户关键字增加
        Route::rule('keyword/submit','api/Task/keyword_submit','post');   //客户关键字增加
        Route::rule('keyword/batchsubmit','api/Task/keyword_batch_submit','post');   //客户关键字增加
        Route::rule('keyword/delete','api/Task/keyword_delete','post');   //客户关键字删除
        Route::rule('keyword/stop','api/Task/keyword_stop','post');   //客户关键字增加
        Route::rule('keyword/changeFees','api/Task/keyword_changefee','post');   //客户关键字增加

        Route::rule('keyword/inquiry','api/Task/keyword_inquiry','post');   //关键词查询价格
        Route::rule('keyword/inquiryall','api/Task/inquiry_all','post');   //关键词查询价格所有搜索引擎
        Route::rule('keyword/submitnew','api/Task/keyword_submit_new','post');   //客户提交查询后的关键词


        Route::rule('order/list','api/Task/mingxi_lists','get');   //任务列表
        Route::rule('order/addlist','api/Task/mingxi_add_lists','get');   //任务列表

        Route::rule('order/add','api/Task/order_add','post');   //提交审核

        Route::rule('user/save_info','api/User/save_info','post');   //保存个人信息
        Route::rule('task/getcounts','api/Task/getcounts','post');   //获取统计信息

        Route::rule('user/recharge','api/User/recharge','get');

    })->middleware('CustomerCheck');
    /*Route::rule('keyword/add','admin/Task/addKeywrods','post')->middleware('CustomerCheck')->ext('html');
    Route::rule('keyword/del','admin/Task/delKeywords','post')->middleware('CustomerCheck')->ext('html');
    Route::rule('task/list/[:type]','admin/Task/getTaskList','get')->middleware('CustomerCheck')
       ->pattern(['type' => '\d+']);
    Route::rule('keywordrank/add','admin/KeywordsRank/add','post')->middleware('CustomerCheck');
    Route::rule('keywordrank/list','admin/KeywordsRank/getLists','get')->middleware('CustomerCheck');
    Route::rule('keywordrank/del','admin/KeywordsRank/del','post')->middleware('CustomerCheck');

    Route::rule('order/list','admin/Order/getTaskOrderList','get')->middleware('CustomerCheck');

    Route::rule('order/add','admin/Order/addTaskOrder','post')->middleware('CustomerCheck');*/
});

//代理商
Route::group('admin',function (){
    Route::rule('get_user_money_info','agent/Meun/getAdminUserMoneyInfo','post')->middleware('Check');
    Route::rule('getrank','api/Task/get_rank_log','post')->middleware('Check');
    Route::rule('login','agent/Login/login','post')->ext('html');
    Route::rule('agent/login','agent/Login/adminLogin','post')->ext('html');
    Route::rule('init','agent/SystemConfig/fnInit','get')->middleware('Check');
    Route::rule('get_user_info','agent/User/getUserInfo','get')->middleware('Check'); //获取用户详情
    Route::rule('editPassword','agent/User/editPassword','get')->middleware('Check'); //获取用户详情
    Route::rule('task/getcounts','agent/Task/getcounts','get')->middleware('Check');   //获取统计信息
    Route::rule('task/update','agent/Task/update_keywords_rank','post')->middleware('Check');   //手动更新排名
    Route::rule('task/settlement','agent/Task/settlement_keywords_rank','post')->middleware('Check');   //手动更结算
    Route::rule('task/stop','agent/Task/stop_keywords','post')->middleware('Check');   //暂停任务
    Route::rule('task/add','agent/Task/admin_add_keywords','post')->middleware('Check');   //代理商添加任务
    Route::rule('menu/list','agent/Meun/index','get')->middleware('Check');
    Route::rule('customer/list','agent/Customer/getCustomerList','get')->middleware('Check');
    Route::rule('customer/add','agent/Customer/addCustomer','post')->middleware('Check');
    Route::rule('customer/edit','agent/Customer/editCustomer','post')->middleware('Check');
    Route::rule('customer/editbalance','agent/Customer/editCustomerBalance','post')->middleware('Check');
    Route::rule('customer/del','agent/Customer/delCustomer','post')->middleware('Check');
    // Route::rule('web/url','agent/WebUrl/getWebUrlList','get')->middleware('Check');
    //关键字查询
    Route::rule('keywords/list','agent/Keywords/getLists','get')->middleware('Check');
    Route::rule('keywords/stoplist','agent/Keywords/getStopLists','get')->middleware('Check');
    Route::rule('keywords/submit','agent/Keywords/submit','post')->middleware('Check');
    //Route::rule('keywords/submitstop','agent/Keywords/submitstop','get')->middleware('Check');
    Route::rule('task/examine','agent/Keywords/examine','post')->middleware('Check');
    Route::rule('keywords/submitrefuse','agent/Keywords/submitrefuse','post')->middleware('Check');
    Route::rule('keywords/del','agent/Keywords/del','post')->middleware('CustomerCheck');
    Route::rule('mingxi/list','api/Task/admin_mingxi_lists','get')->middleware('Check');   //任务列表
    Route::rule('mingxi/rechargelist','api/Task/admin_recharge_lists','get')->middleware('Check');   //任务列表

    Route::rule('system/base','agent/SystemConfig/index','get')->middleware('Check');
    Route::rule('system/topmenu','agent/SystemConfig/topmenu','get')->middleware('Check');
    Route::rule('system/bottommenu','agent/SystemConfig/bottommenu','get')->middleware('Check');
    Route::rule('system/copyright','agent/SystemConfig/copyright','get')->middleware('Check');
    Route::rule('system/engines','agent/SystemConfig/engines','get')->middleware('Check');
    Route::rule('system/edit','agent/SystemConfig/editSystem','post')->middleware('Check');
    Route::rule('system/notice','agent/SystemConfig/notice','get')->middleware('Check');

    Route::rule('system/upload','agent/SystemConfig/upload','post')->middleware('Check');

    Route::rule('system/recharge','agent/SystemConfig/recharge','get')->middleware('Check');

    Route::rule('system/domain','agent/SystemConfig/domain','get')->middleware('Check');
    Route::rule('system/logo','agent/SystemConfig/logo','get')->middleware('Check');
    Route::rule('system/slider','agent/SystemConfig/slider','get')->middleware('Check');


    Route::rule('system/edittemplate','agent/SystemConfig/edittemplate','post')->middleware('Check');
    Route::rule('system/editrecharge','agent/SystemConfig/editrecharge','post')->middleware('Check');

    Route::rule('/groupadd','agent/Group/add','post')->middleware('Check');
    Route::rule('/feeadd','agent/Group/feeadd','post')->middleware('Check');//扣费增加
    Route::rule('/grouplist','agent/Group/getList','get')->middleware('Check');
    Route::rule('/feelist','agent/Group/feelist','get')->middleware('Check');//扣费列表
    Route::rule('/groupedit','agent/Group/edit','post')->middleware('Check');
    Route::rule('/coderefresh','agent/Group/coderefresh','post')->middleware('Check');
    Route::rule('/groupdel','agent/Group/del','post')->middleware('Check');
    Route::rule('/feedel','agent/Group/feedel','post')->middleware('Check');
    Route::rule('/feedit','agent/Group/feedit','post')->middleware('Check');
    Route::rule('/keywords/shenhe','agent/Keywords/getLists','get')->middleware('Check');
    Route::rule('/order/shenhe','agent/Order/shenheOrder','post')->middleware('Check');
    Route::rule('/order/list','agent/Keywords/getLists','get')->middleware('Check');
    Route::rule('/order/status','agent/Order/editStatus','post')->middleware('Check');
    Route::rule('/edit/ranking','agent/Order/editRanking','get');
    Route::rule('getselect','agent/User/getSelect','get')->middleware('Check');
    Route::rule('order/updatetask','agent/Order/updatetask','post')->middleware('Check');
});



//后台接口路由
Route::group('platform',function (){
    Route::rule('get_user_money_info','admin/Meun/getAdminUserMoneyInfo','post')->middleware('PlatformCheck');
    Route::rule('getrank','api/Task/get_rank_log','post')->middleware('PlatformCheck');
    Route::rule('login','admin/Login/login','post')->ext('html');
    Route::rule('init','admin/SystemConfig/fnInit','get')->middleware('PlatformCheck');
    Route::rule('get_user_info','admin/User/getUserInfo','get')->middleware('PlatformCheck'); //获取用户详情
    Route::rule('editPassword','admin/User/editPassword','get')->middleware('PlatformCheck'); //获取用户详情
    Route::rule('task/getcounts','admin/Task/getcounts','get')->middleware('PlatformCheck');   //获取统计信息
    Route::rule('task/update','admin/Task/update_keywords_rank','post')->middleware('PlatformCheck');   //手动更新排名
    Route::rule('task/feedbackupdate','admin/Task/update_feedback_rank','post')->middleware('PlatformCheck');   //手动更新反馈词排名
    Route::rule('task/settlement','admin/Task/settlement_keywords_rank','post')->middleware('PlatformCheck');   //手动更结算
    Route::rule('task/stop','admin/Task/stop_keywords','post')->middleware('PlatformCheck');   //暂停任务
    Route::rule('task/update_feedback_rank','admin/Task/process_feedback_rank','post')->middleware('PlatformCheck');   //反馈词排名处理
    Route::rule('task/update_feedback_status','admin/Task/process_feedback_status','post')->middleware('PlatformCheck');   //反馈词状态处理
    Route::rule('task/submitcloud','admin/Task/submmit_cloud','post')->middleware('PlatformCheck');   //将关键词提交到云平台
    Route::rule('task/deleteCloud','admin/Task/delete_cloud','post')->middleware('PlatformCheck');   //将关键词从云平台删除
    Route::rule('task/add','admin/Task/admin_add_keywords','post')->middleware('PlatformCheck');   //平台管理员添加任务
    Route::rule('task/edit','admin/Task/admin_edit_keywords','post')->middleware('PlatformCheck');   //平台管理员添加任务
    Route::rule('task/edit_feedback','admin/Task/admin_edit_feedback','post')->middleware('PlatformCheck');   //手动修改反馈词排名
    Route::rule('task/agentlist','admin/Task/get_agetn','post')->middleware('PlatformCheck');   //获取代理商列表
    Route::rule('task/userlist','admin/Task/get_user','post')->middleware('PlatformCheck');   //获取用户列表
    Route::rule('task/settlementdate','admin/Task/settlement_date_keywords_rank','post')->middleware('PlatformCheck');   //手动结算指定时间
    Route::rule('menu/list','admin/Meun/index','get')->middleware('PlatformCheck');
    Route::rule('customer/list','admin/Customer/getCustomerList','get')->middleware('PlatformCheck');
    Route::rule('agent/list','admin/Customer/getCustomerList','get')->middleware('PlatformCheck');
    Route::rule('reg/edit','admin/group/editReg','post')->middleware('PlatformCheck');
    Route::rule('group/list','admin/Group/getList','get')->middleware('PlatformCheck');
    Route::rule('group/getDefultGroup','admin/Group/getDefaultGroup','get')->middleware('PlatformCheck');
    Route::rule('customer/add','admin/Customer/addCustomer','post')->middleware('PlatformCheck');
    Route::rule('customer/edit','admin/Customer/editCustomer','post')->middleware('PlatformCheck');
    Route::rule('customer/del','admin/Customer/delCustomer','post')->middleware('PlatformCheck');
    Route::rule('customer/editbalance','admin/Customer/editCustomerBalance','post')->middleware('PlatformCheck');

    Route::rule('command/add','admin/Simulation/addCommand','post')->middleware('PlatformCheck');
    Route::rule('command/delete','admin/Simulation/deleteCommand','get')->middleware('PlatformCheck');
    Route::rule('command/list','admin/Simulation/commandLis','get')->middleware('PlatformCheck');

    Route::rule('template/list','admin/Simulation/templateLis','get')->middleware('PlatformCheck');
    Route::rule('template/add','admin/Simulation/addTemplate','post')->middleware('PlatformCheck');
    Route::rule('template/delete','admin/Simulation/deleteTemplate','get')->middleware('PlatformCheck');


    Route::rule('web/url','admin/WebUrl/getWebUrlList','get')->middleware('PlatformCheck');
    //关键字查询
    Route::rule('keywords/list','admin/Keywords/getLists','get')->middleware('PlatformCheck');
    Route::rule('keywords/stoplist','admin/Keywords/getStopLists','get')->middleware('PlatformCheck');
    Route::rule('keywords/submit','admin/Keywords/submit','post')->middleware('PlatformCheck');
    Route::rule('task/examine','admin/Keywords/examine','post')->middleware('PlatformCheck');
    Route::rule('keywords/submitstop','admin/Keywords/submitstop','get')->middleware('PlatformCheck');
    Route::rule('keywords/refusestop','admin/Keywords/refusestop','get')->middleware('PlatformCheck');
    Route::rule('keywords/submitrefuse','admin/Keywords/submitrefuse','post')->middleware('PlatformCheck');
    Route::rule('keywords/del','admin/Keywords/del','post')->middleware('PlatformCheck');
    Route::rule('keywords/domainmatch','admin/Keywords/domainmatch','post')->middleware('PlatformCheck');
    Route::rule('keywords/huifu','admin/Keywords/huifu','post')->middleware('PlatformCheck');   //恢复已删除账号
    Route::rule('mingxi/list','api/Task/admin_mingxi_lists','get')->middleware('PlatformCheck');   //任务列表
    Route::rule('mingxi/rechargelist','api/Task/admin_recharge_lists','get')->middleware('PlatformCheck');   //任务列表
    Route::rule('mingxi/balancelist','api/Task/admin_balance_lists','get')->middleware('PlatformCheck');   //任务列表

    Route::rule('/system/base','admin/SystemConfig/index','get')->middleware('PlatformCheck');

    Route::rule('/system/topmenu','admin/SystemConfig/topmenu','get')->middleware('PlatformCheck');
    Route::rule('/system/bottommenu','admin/SystemConfig/bottommenu','get')->middleware('PlatformCheck');
    Route::rule('/system/copyright','admin/SystemConfig/copyright','get')->middleware('PlatformCheck');
    Route::rule('/system/domain','admin/SystemConfig/domain','get')->middleware('PlatformCheck');
    Route::rule('/system/logo','admin/SystemConfig/logo','get')->middleware('PlatformCheck');
    Route::rule('/system/slider','admin/SystemConfig/slider','get')->middleware('PlatformCheck');
    Route::rule('/system/edittemplate','admin/SystemConfig/edittemplate','post')->middleware('PlatformCheck');

    Route::rule('/system/oemdomain','admin/SystemConfig/domainlist','get')->middleware('PlatformCheck');
    Route::rule('/system/auzdomain','admin/SystemConfig/auzdomain','get')->middleware('PlatformCheck');


    Route::rule('system/upload','admin/SystemConfig/upload','post')->middleware('PlatformCheck');

    Route::rule('/system/engines','admin/SystemConfig/engines','get')->middleware('PlatformCheck');
    Route::rule('/system/edit','admin/SystemConfig/editSystem','post')->middleware('PlatformCheck');
    Route::rule('/groupadd','admin/Group/add','post')->middleware('PlatformCheck');
    Route::rule('/feeadd','admin/Group/feeadd','post')->middleware('PlatformCheck');
    Route::rule('/add','admin/Group/add','post')->middleware('PlatformCheck');
    Route::rule('/grouplist','admin/Group/getList','get')->middleware('PlatformCheck');
    Route::rule('/feelist','admin/Group/feelist','get')->middleware('PlatformCheck');//扣费列表
    Route::rule('/groupedit','admin/Group/edit','post')->middleware('PlatformCheck');
    Route::rule('/groupdel','admin/Group/del','post')->middleware('PlatformCheck');
   
    Route::rule('/feedel','admin/Group/feedel','post')->middleware('PlatformCheck');
    Route::rule('/feedit','admin/Group/feedit','post')->middleware('PlatformCheck');
    Route::rule('/keywords/shenhe','admin/Keywords/getLists','get')->middleware('PlatformCheck');
    Route::rule('/keywords/feedback','admin/Keywords/getfeedbackLists','get')->middleware('PlatformCheck');
    Route::rule('/keywords/export','admin/Keywords/getLists','get')->middleware('PlatformCheck');
    Route::rule('/keywords/feedback_export','admin/Keywords/getfeedbackLists','get')->middleware('PlatformCheck');
    Route::rule('/order/shenhe','admin/Order/shenheOrder','post')->middleware('PlatformCheck');
    Route::rule('/order/list','admin/Keywords/getLists','get')->middleware('PlatformCheck');
    Route::rule('/order/status','admin/Order/editStatus','post')->middleware('PlatformCheck');
    Route::rule('/edit/ranking','admin/Order/editRanking','get');
    Route::rule('getselect','admin/User/getSelect','get')->middleware('PlatformCheck');
    Route::rule('order/updatetask','admin/Order/updatetask','post')->middleware('PlatformCheck');
    Route::rule('order/updatefeedback','admin/Order/updatefeedback_rank','post')->middleware('PlatformCheck');
    Route::rule('web/updateurl','admin/WebUrl/updatetask','post')->middleware('PlatformCheck');

    Route::rule('addnotice','admin/SystemConfig/addnotice','post')->middleware('PlatformCheck');
    Route::rule('delnotice','admin/SystemConfig/delnotices','get')->middleware('PlatformCheck');
    Route::rule('getnotice','admin/SystemConfig/getnotices','get')->middleware('PlatformCheck');
});
//
//


//销售工具接口路由
Route::group('salestool',function (){
    Route::rule('user/login','salestool/Login/doLogin','post');
    Route::rule('get_user_info','salestool/User/getInfo','get')->middleware('SalesCheck');
    Route::rule('menu/list','salestool/Meun/index','get')->middleware('SalesCheck');
    Route::rule('/keywords/shenhe','salestool/Keywords/getLists','get')->middleware('SalesCheck');
    Route::rule('/keywords/export_data','salestool/Keywords/exportData','get')->middleware('SalesCheck');
    Route::rule('balancelist','salestool/Balance/balanceList','get')->middleware('SalesCheck');   //任务列表
    Route::rule('init','salestool/SystemConfig/fnInit','get')->middleware('SalesCheck');
    Route::rule('keywords/delKeyword','salestool/Keywords/delKeyword','get')->middleware('SalesCheck');
});

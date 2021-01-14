layui.define(['table','form','setter'],function (exports) {
    var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,table = layui.table
        ,setter = layui.setter
        ,request = setter.request
        ,form = layui.form;

    //优化中的任务
    table.render({
        elem: '#LAY-admin-task-list'
        ,url: '/salestool/keywords/shenhe' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,where:{
        }
        ,cols: [[
            {field: 'keywords', title: '关键字', align:'center',minWidth: 120,templet: function(d){
                var url;
                switch (d.search_ngines) {
                    case "百度PC":
                        url = 'https://www.baidu.com/s?wd=';
                        break;
                    case "百度移动":
                        url = 'https://m.baidu.com/s?wd=';
                        break;
                    case "360PC":
                        url = 'https://www.so.com/s?ie=utf-8&q=';
                        break;
                    case "搜狗PC":
                        url = 'https://www.sogou.com/web?query=';
                        break;
                    case "百度移动":
                        url = 'https://m.baidu.com/s?word=';
                        break;
                    case "搜狗移动":
                        url = 'https://wap.sogou.com/web/searchList.jsp?keyword=';
                        break;
                    case "360移动":
                        url = 'https://m.so.com/s?q=';
                        break;
                    default:
                        url = 'https://www.baidu.com/s?wd=';
                        break;
                }
                return '<div><a href="'+url+d.keywords+'" target="_blank" class="layui-table-link">'+d.keywords+'</a></div>'
                }}
            ,{field: 'web_url', title: '链接',align:'center', minWidth: 150}
            ,{field: 'search_ngines', title: '搜索引擎' ,align:'center', minWidth: 100}
            ,{field:'current_ranking' ,title: '当前排名' ,align:'center',templet:function(d){return d.current_ranking==101 || d.current_ranking==0?">100":d.current_ranking}}
            ,{field:'delete_time' ,title: '初始闲置时间' ,align:'center', minWidth: 140}
            ,{field:'rank_time' ,title: '最新排名更新时间' ,align:'center', minWidth: 140}
        ]]
        ,parseData:function ( res ) {
            return {
                "code": res.code, //解析接口状态
                "msg": res.msg, //解析提示文本
                "count": res.data.total,  //解析数据长度
                "data": res.data.data.length>0? res.data.data:null //解析数据列表
            };
        }
        ,page: true
        ,limit: 30
        ,limits: [30, 50, 100, 200, 500]
        ,text: {none: '一条数据也没有^_^'}
    });

    table.on('tool(LAY-admin-task-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'orderinfo'){
            admin.popup({
                title: '详情'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-info'
                ,btn:['修改','关闭']
                ,success: function(layero, index){
                    
                    view(this.id).render('seo/task/orderinfo', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
                ,yes:function (index, layero) {
                    var _order = $('#orderNumber').val();
                    var new_ranking = $("input[name='current_ranking']").val();
                    var original_rank = $("input[name='original_rank']").val();
                    var new_cycle = $("input[name='cycle']").val();
                    var new_price = $("input[name='price']").val();
                    console.log(_order)
                    admin.req({
                        url: '/admin/order/updatetask'
                        ,type:"post"
                        ,data:{tid:data.id,current_ranking:new_ranking,original_rank:original_rank,cycle:new_cycle,price:new_price}
                        ,done: function( res ){
                            //登入成功的提示与跳转
                            layer.msg(res.msg, {
                                offset: '15px'
                                ,icon: 1
                                ,time: 1000
                            },function () {
                                layui.table.reload('LAY-admin-task-list'); //重载表格
                                layer.close(index); //执行关闭
                            });
                        }
                    });
                }
                ,btn2: function(index, layero){
                    layer.close(index); //执行关闭
                }
            });
        }else if(obj.event === 'update'){
            admin.req({
                url: '/admin/task/update' //
                ,type:'post'
                ,data: {tid:data.id}
                ,done: function( res ){
                    
                    
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 1
                        ,time: 1000
                    }, function(){
                        if(res.id)
                        {
                            layui.table.reload('LAY-app-query-ranking-list');
                        }
                    });
                    
                }
            });
        }else if(obj.event === 'settlement'){
            admin.req({
                url: '/admin/task/settlement' //
                ,type:'post'
                ,data: {tid:data.id}
                ,done: function( res ){
                    
                    
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 1
                        ,time: 1000
                    }, function(){
                        if(res.id)
                        {
                            layui.table.reload('LAY-admin-task-list');
                        }
                    });
                    
                }
            });
        }else if(obj.event === 'stop'){
            admin.req({
                url: '/admin/task/stop' //
                ,type:'post'
                ,data: {tid:data.id,type:'stop'}
                ,done: function( res ){
                    
                    
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 1
                        ,time: 1000
                    }, function(){
                        layui.table.reload('LAY-admin-task-list');
                    });
                    
                }
            });
        } else if(obj.event === 'ftpInfo'){
            admin.popup({
                title: 'FTP后台详情'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    console.log(data.web_id);
                    view(this.id).render('seo/task/ftpinfo', data.web_id).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
            });
        }else if(obj.event === 'rank'){
            window.open("http://"+window.location.host+"/system/#/seo/task/rank/id="+data.id)
        }
    });

//监听工具条
    table.on('tool(LAY-admin-task-shenhe)', function(obj){
        var data = obj.data;
        if(obj.event === 'orderinfo'){
            admin.popup({
                title: '详情'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-info'
                ,btn:['通过','不通过']
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('seo/task/orderinfo', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
                ,yes:function (index, layero) {
                    var _order = $('#orderNumber').val();
                    console.log(_order)
                    admin.req({
                        url: '/admin/order/shenhe'
                        ,type:"post"
                        ,data:{shenhe_status:1,order_number:_order}
                        ,done: function( res ){
                            //登入成功的提示与跳转
                            layer.msg(res.msg, {
                                offset: '15px'
                                ,icon: 1
                                ,time: 1000
                            },function () {
                                layui.table.reload('LAY-admin-task-shenhe'); //重载表格
                                layer.close(index); //执行关闭
                            });
                        }
                    });
                }
                ,btn2: function(index, layero){
                    var _order = $('#orderNumber').val();
                    admin.req({
                        url: '/admin/order/shenhe'
                        ,type:"post"
                        ,data:{shenhe_status:2,order_number:_order}
                        ,done: function( res ){
                            //登入成功的提示与跳转
                            layer.msg(res.msg, {
                                offset: '15px'
                                ,icon: 1
                                ,time: 1000
                            },function () {
                                layui.table.reload('LAY-admin-task-shenhe'); //重载表格
                                layer.close(index); //执行关闭
                            });
                        }
                    });
                }
            });
        } else if(obj.event === 'ftpInfo'){
            admin.popup({
                title: 'FTP后台详情'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    console.log(data.web_id);
                    view(this.id).render('seo/task/ftpinfo', data.web_id).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
            });
        }
    });
   
    exports('admintask', {});
})
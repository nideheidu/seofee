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
        ,url: '/platform/order/list'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,where:{
            status:1
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'order_number', width:'180', title: '订单号', align:'center', sort: true}
            ,{field: 'contacts', title: '客户',align:'center', minWidth: 100}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){
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
            ,{field: 'price', title: '单价',align:'center'}
            ,{field: 'url', title: '网址',align:'center', minWidth: 120}
            ,{field: 'search_ngines', title: '搜索引擎' ,align:'center', minWidth: 100}
            ,{field:'create_time' ,title: '添加时间' ,align:'center', minWidth: 200}
            ,{field:'original_rank' ,title: '初排' ,align:'center', minWidth: 80, sort: true,templet:function(d){return d.original_rank==101 || d.original_rank==0?">100":d.original_rank}}
            ,{field:'current_ranking' ,title: '新排' ,align:'center', minWidth: 80, sort: true,templet:function(d){return d.current_ranking==101 || d.current_ranking==0?">100":d.current_ranking}}
            ,{field:'update_time' ,title: '更新时间' ,align:'center', minWidth: 200}
            ,{field:'standard' ,title: '达标天' ,align:'center', minWidth: 100}
            ,{field:'day' ,title: '优化天数' ,align:'center', minWidth: 100}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}

            //,{title: '操作', minWidth: 250, align: 'center', fixed: 'right', toolbar: '#table-content-list'}
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




    //待优化的任务
    table.render({
        elem: '#LAY-admin-task-shenhe'
        ,url: '/platform/keywords/shenhe?status=1' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            // ,{field: 'id', width: 100, title: 'id', sort: true}
            ,{field: "company_name", title: '公司', minWidth: 100}
            ,{field: 'contacts', title: '联系人', minWidth: 100}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){
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
            
            ,{field: 'web_url', title: '网址'}
            ,{field: '', title: '后台FTP',toolbar:'#ftpHostTpl'}
            ,{field: 'search_ngines', title: '搜索引擎'}
            ,{field:'' ,title: '前N名',templet:function (d) {
                    return '10';
                }, sort:true}
            ,{field:'current_ranking' ,title: '现在排名', sort:true,templet:function(d){return d.current_ranking>100?">100":d.current_ranking;}}
            ,{field:'create_time' ,title: '添加时间', sort:true}
            ,{field:'price' ,title: '用户单价', sort:true}
            ,{field:'agent_price' ,title: '代理单价', sort:true}
            ,{field:'status' ,title: '状态',templet: '#buttonTpl'}
            ,{title: '操作', minWidth: 200, align: 'center', fixed: 'right', toolbar: '#table-content-list'}
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
                    var agent_price = $("input[name='agent_price']").val();

                    admin.req({
                        url: '/platform/keywords/submit'
                        ,type:"post"
                        ,data:{tid:data.id,current_ranking:new_ranking,original_rank:original_rank,cycle:new_cycle,price:new_price,agent_price:agent_price}
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
                        url: '/platform/keywords/submitrefuse'
                        ,type:"post"
                        ,data:{tid:data.id,order_number:_order}
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
        } else if(obj.event === 'update'){
            admin.req({
                url: '/platform/task/update' //
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
                            table.reload('LAY-admin-task-list');
                        }
                    });
                    
                }
            });
        }else if(obj.event === 'ftpInfo'){
            admin.popup({
                title: 'FTP后台详情'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    data.web_id.keywords=data.keywords;
                    view(this.id).render('seo/task/ftpinfo', data.web_id).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
            });
        }else if(obj.event === 'editxx'){
            admin.popup({
                title: '编辑关键词'
                ,area: ['800px', '600px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    view(this.id).render('seo/task/edit-form', data).done(function(){
                        form.render(null, 'layuiadmin-app-task-edit');
                    });
                }
            });
        }else if(obj.event === 'batchdelx'){
                layer.confirm('确定删除吗？', function(index) {

                    //执行 Ajax 后重载

                    admin.req({
                      url: '/platform/Keywords/del'
                        ,type:"post"
                        ,data: {ids:data.id}
                        ,done: function( res ){
                            //登入成功的提示与跳转
                            layer.msg('删除成功', {
                                offset: '15px'
                                ,icon: 1
                                ,time: 1000
                            }, function(){
                                table.reload('LAY-admin-task-shenhe');
                                //  location.hash = search.redirect ? decodeURIComponent(search.redirect) : '/';
                            });
                        }
                    });

                    table.reload('LAY-app-query-ranking-list');
                    layer.msg('已删除');
                });
            }
    });
    exports('admintask_check', {});
})
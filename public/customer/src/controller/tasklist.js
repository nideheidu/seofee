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
        elem: '#LAY-app-task-list'
        ,url: '/v1/order/list' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,where:{
            shenhe_status:1
            ,status:1
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'order_number', width: 150, title: '订单号', align: 'center'}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){var url = d.search_ngines="百度PC"?"https://www.baidu.com/s?wd=":"https://m.baidu.com/s?word=";return '<div><a href="'+url+d.keywords+'" target="_blank" class="layui-table-link">'+d.keywords+'</a></div>'}}
            ,{field: 'url', title: '网址', minWidth: 200, align: 'center'}
            ,{field: '', title: '后台FTP',templet:'#fptTpl', align: 'center'}
            ,{field: 'search_ngines', title: '搜索引擎', align: 'center'}
            ,{field:'create_time' ,title: '添加时间', align: 'center'}
            ,{field:'original_rank' ,title: '初排', align: 'center',templet:function (d) {
                    if(d.original_rank>100){
                        return ">100";
                    }
                    return d.original_rank;
                }}
            ,{field:'current_ranking' ,title: '新排', align: 'center',templet:function (d) {
                    if(d.current_ranking>100){
                        return ">100";
                    }
                    return d.current_ranking;
                }}
            ,{field:'update_time' ,title: '更新时间', align: 'center',templet:function (d) {
                    if(d.update_time===0){
                        return "-";
                    }
                    return d.update_time;
                }}
           
            ,{field:'update_time' ,title: '达标天', align: 'center'}
            ,{field: 'status', title: '发布状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
            ,{field:'update_time' ,title: '天数', align: 'center'}
            ,{title: '操作', minWidth: 150, align: 'center', fixed: 'right', toolbar: '#table-content-list'}
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
        ,text: {none: '目前还没有优化任务'}
    });
    table.on('tool(LAY-app-task-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'ftpHost'){
            admin.popup({
                title: 'FTP后台管理'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('customers/tasks/ftp-form', data).done(function(){
                        form.render(null, 'layuiadmin-ftp-form-list');
                        //监听提交
                        form.on('submit(layuiadmin-ftp-form-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            //$.ajax({});
                            layui.table.reload('LAY-app-task-list'); //重载表格
                            layer.close(index); //执行关闭
                        });
                    });
                }
            });
        } else if(obj.event === 'edit'){
            admin.popup({
                title: '编辑文章'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('app/content/listform', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');

                        //监听提交
                        form.on('submit(layuiadmin-app-form-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            //$.ajax({});
                            layui.table.reload('LAY-app-content-list'); //重载表格
                            layer.close(index); //执行关闭
                        });
                    });
                }
            });
        }
    });
    //待优化的任务
    table.render({
        elem: '#LAY-app-task-shenhe'
        ,url: '/v1/order/list' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || ''),
        }
        ,where:{
            shenhe_status:0
            ,status:0
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){var url = d.search_ngines="百度PC"?"https://www.baidu.com/s?wd=":"https://m.baidu.com/s?word=";return '<div><a href="'+url+d.keywords+'" target="_blank" class="layui-table-link">'+d.keywords+'</a></div>'}}
            ,{field: 'url', title: '网址', align: 'center'}
            ,{field: '', title: '后台FTP',templet:'#fptTpl', align: 'center'}
            ,{field: 'search_ngines', title: '搜索引擎', align: 'center'}
            ,{field:'create_time' ,title: '添加时间', align: 'center'}
            ,{field:'' ,title: '前N名',templet:function (d) {
                    return '10';
                }, align: 'center'}
            ,{field:'current_ranking' ,title: '现在排名',templet:function (d) {
                    if(d.current_ranking>100){
                        return ">100";
                    }
                    return d.current_ranking;
                }, align: 'center'}
            ,{field:'cycle' ,title: '合作周期',templet:function (d) {
                    return d.cycle + "天";
                }, align: 'center'}
            ,{field:'shenhe_status' ,title: '状态',templet:'#buttonTpl', align: 'center'}
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
    table.on('tool(LAY-app-task-shenhe)', function(obj){
        var data = obj.data;
        if(obj.event === 'ftpHost'){
            admin.popup({
                title: 'FTP后台管理'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('customers/tasks/ftp-form', data).done(function(){
                        form.render(null, 'layuiadmin-ftp-form-list');
                        //监听提交
                        form.on('submit(layuiadmin-ftp-form-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            //$.ajax({});
                            layui.table.reload('LAY-app-content-list'); //重载表格
                            layer.close(index); //执行关闭
                        });
                    });
                }
            });
        } else if(obj.event === 'edit'){
            admin.popup({
                title: '编辑文章'
                ,area: ['550px', '550px']
                ,id: 'LAY-popup-content-edit'
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('app/content/listform', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');

                        //监听提交
                        form.on('submit(layuiadmin-app-form-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            //$.ajax({});
                            layui.table.reload('LAY-app-content-list'); //重载表格
                            layer.close(index); //执行关闭
                        });
                    });
                }
            });
        }
    });
    exports('tasklist', {});
})
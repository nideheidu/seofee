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
            status:1
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'order_number', width: 100, title: '订单号', sort: true}
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
            ,{field: '后台FTP', title: 'ftp_host'}
            ,{field: 'search_ngines', title: '搜索引擎'}
            ,{field:'create_time' ,title: '添加时间'}
            ,{field:'' ,title: '前N名'}
            ,{field:'create_time' ,title: '初排'}
            ,{field:'create_time' ,title: '新排',}
            ,{field:'update_time' ,title: '更新时间'}
            ,{field:'update_time' ,title: '达标天'}
            ,{field: 'status', title: '发布状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
            ,{field:'update_time' ,title: '类型'}
            ,{field:'update_time' ,title: '天数'}
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
        ,text: {none: '一条数据也没有^_^'}
    });

    //待优化的任务
    table.render({
        elem: '#LAY-app-task-shenhe'
        ,url: '/v1/order/list' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'order_number', width: 100, title: '任务编号', sort: true}
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
            ,{field: '', title: '后台FTP',templet:'#fptTpl'}
            ,{field: 'search_ngines', title: '搜索引擎'}
            ,{field:'create_time' ,title: '添加时间'}
            ,{field:'' ,title: '前N名',templet:function (d) {
                    return '10';
                }}
            ,{field:'yspm' ,title: '现在排名',templet:function (d) {
                    if(d.yspm>100){
                        return ">100";
                    }
                    return d.yspm;
                }}
            ,{field:'cycle' ,title: '合作周期',templet:function (d) {
                    return d.cycle + "天";
                }}
            ,{field:'status' ,title: '状态',templet: '#buttonTpl'}
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
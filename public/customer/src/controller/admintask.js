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
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){var url = d.search_ngines="百度PC"?"https://www.baidu.com/s?wd=":"https://m.baidu.com/s?word=";return '<div><a href="'+url+d.keywords+'" target="_blank" class="layui-table-link">'+d.keywords+'</a></div>'}}
            ,{field: 'web_url', title: '网址'}
            ,{field: '后台FTP', title: 'ftp_host'}
            ,{field: 'search_ngines', title: '搜索引擎'}
            ,{field:'create_time' ,title: '添加时间'}
            ,{field:'' ,title: '前N名'}
            ,{field:'create_time' ,title: '初排'}
            ,{field:'create_time' ,title: '新排'}
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
        elem: '#LAY-admin-task-shenhe'
        ,url: '/v1/order/list' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'order_number', width: 100, title: '任务编号', sort: true}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){var url = d.search_ngines="百度PC"?"https://www.baidu.com/s?wd=":"https://m.baidu.com/s?word=";return '<div><a href="'+url+d.keywords+'" target="_blank" class="layui-table-link">'+d.keywords+'</a></div>'}}
            ,{field: 'web_url', title: '网址'}
            ,{field: 'ftp_host', title: '后台FTP'}
            ,{field: 'search_ngines', title: '搜索引擎'}
            ,{field:'create_time' ,title: '添加时间'}
            ,{field:'' ,title: '前N名',templet:function (d) {
                    return '10';
                }}
            ,{field:'current_ranking' ,title: '现在排名', sort:true,templet:function(d){var rank = d.current_ranking>100?">100":d.current_ranking;return rank;}}
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

    exports('admintask', {});
})
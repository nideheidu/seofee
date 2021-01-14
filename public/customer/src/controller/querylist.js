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
        elem: '#LAY-app-query-ranking-list'
        ,url: '/v1/keywordrank/list.html'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {field:'id',type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 40, title: 'id',align:'center'}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){var url = d.search_ngines="百度PC"?"https://www.baidu.com/s?wd=":"https://m.baidu.com/s?word=";return '<div><a href="'+url+d.keywords+'" target="_blank" class="layui-table-link">'+d.keywords+'</a></div>'}}
            ,{field: 'web_url', title: '网址', width:200,align:'center'}
            ,{field: 'search_ngines', title: '搜索引擎',width: 100,}
            ,{field:'rank_time' ,title: '查询时间',align:'center'}
            ,{field:'current_ranking' ,title: '排名',templet:'#rankTpl',align:'center',width: 60,}
            ,{field:'complete_time' ,title: '完成时间',align:'center',width: 100,}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', Width: 60, align: 'center',fixed: 'right',}
            ,{field: 'status', title: '操作', templet: '#button-content', minWidth: 80, align: 'center',fixed: 'right',}
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

    exports('querylist', {});
});
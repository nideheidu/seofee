layui.define(['table','form','util','setter'],function (exports) {
    var $ = layui.$
        ,admin = layui.admin
        ,util = layui.util
        ,view = layui.view
        ,table = layui.table
        ,setter = layui.setter
        ,request = setter.request
        ,form = layui.form;

    //资金明细
    table.render({
        elem: '#LAY-app-free-list'
        ,url: '/v1/order/list' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,where:{
            status:1
        }
        ,cols: [[
            {field: 'type', minWidth : 100, title: '资金变动类型',templet: '#buttonTpl',align:'center'}
            ,{field: 'keywords', title: '关键词',align:'center'}
            ,{field: 'url', title: '关键词链接',align:'center'}
            ,{field: 'search_ngines', title: '搜索引擎',align:'center'}
            ,{field: 'free', title: '变动金额(元)', minWidth: 100,align:'center'}
            ,{field:'create_time' ,title: '变动日期',templet: function(d) {return util.toDateString(d.create_time*1000).slice(0,10);},align:'center'},
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

    exports('detailedlist', {});
})
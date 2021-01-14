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
        ,url: '/v1/order/addlist' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,where:{
            status:1
        }
        ,cols: [[
            {field: 'type', minwidth: 100, title: '充值类型',templet: '#buttonTpl',align:'center'}
            ,{field: 'free', title: '充值金额(元)', minWidth: 100,align:'center'}
            ,{field:'create_time' ,title: '充值时间',templet: function(d) {return util.toDateString(d.create_time*1000);},align:'center'}
            ,{field: 'balance', title: '当前账号余额(元)',align:'center'}
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

    exports('adddetailedlist', {});
})
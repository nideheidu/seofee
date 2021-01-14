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
        ,url: '/platform/mingxi/rechargelist' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,where:{
            status:1
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'type',  title: '类型',templet: '#buttonTpl',}
            ,{field: 'uid', title: '用户'}
            ,{field: 'before_free', title: '交易前（元）'}
            ,{field: 'free', title: '交易金额（元）', minWidth: 100}
            ,{field: 'time', title: '交易时间'}
            ,{field: 'after_free', title: '交易后（元）'}
            ,{field:'remarks' ,title: '备注'}
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

    exports('rechargelist', {});
})
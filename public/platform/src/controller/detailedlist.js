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
        ,url: '/platform/mingxi/list' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,where:{
            status:1,
            type:1
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'uid', title: '用户'}
            ,{field: 'type',title: '类型',templet: '#buttonTpl',}
            ,{field: 'free', title: '变动', minWidth: 100}
            ,{field: 'url', title: 'url'}
            ,{field: 'keywords', title: '关键词'}
            ,{field:'create_time' ,title: '发生时间',templet: function(d) {return util.toDateString(d.create_time*1000);}}
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

    exports('detailedlist', {});
})
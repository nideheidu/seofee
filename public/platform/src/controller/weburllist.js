/**
 * 客户
 */


layui.define(['table', 'form','setter'], function(exports){
    var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,table = layui.table
        ,setter = layui.setter
        ,request = setter.request
        ,router = layui.router()
        ,form = layui.form;
    uid = router.search.uid;
    //客户列表
    table.render({
        elem: '#LAY-app-weburl-list'
        ,url: '/platform/web/url'
        ,where: {
            uid: uid 
        }
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left', align: 'center'}
            ,{field: 'contacts', title: '客户', minWidth: 100, align: 'center'}
            ,{field: 'url', title: 'url', minWidth: 100, align: 'center'}
            ,{field: 'keywords_count', title: '关键字数量', align: 'center',toolbar:"#keywordTpl"}
            ,{field: '', title: 'FTP后台', align: 'center',toolbar:"#FTPHostTpl"}
            ,{field: 'create_time', title: '添加时间', sort: true, minWidth: 200, align: 'center'}
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
    table.on('tool(LAY-app-weburl-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'ftpInfo'){
            admin.popup({
                title: 'FTP后台详情'
                ,area: ['700px', '600px']
                ,id: 'LAY-popup-customer-edit'
                ,btn:['修改','关闭']
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('seo/web/info', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
                ,yes:function (index, layero) {
                    var _order = $('#orderNumber').val();
                    var xiongzhang = $("input[name='xiongzhang']").val();
                    
                    admin.req({
                        url: '/platform/web/updateurl'
                        ,type:"post"
                        ,data:{tid:data.id,xiongzhang:xiongzhang}
                        ,done: function( res ){
                            //登入成功的提示与跳转
                            layer.msg(res.msg, {
                                offset: '15px'
                                ,icon: 1
                                ,time: 1000
                            },function () {
                                layui.table.reload('LAY-app-weburl-list'); //重载表格
                                layer.close(index); //执行关闭
                            });
                        }
                    });
                }
                ,btn2: function(index, layero){
                    layer.close(index); //执行关闭
                }
            });
        }else if(obj.event === 'keywordInfo'){
            admin.popup({
                title: '关键字查看'
                ,area: ['700px', '600px']
                ,id: 'LAY-popup-customer-edit'
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('seo/web/keywordsinfo', data.keywordslists).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
            });
        }
    });


    exports('weburllist', {})
});
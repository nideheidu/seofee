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
        ,form = layui.form;
    var router = layui.router()
                ,path = router.path
                ,pathURL = admin.correctRouter(router.path.join('/'));
    
    //客户列表
    table.render({
        elem: '#LAY-app-customer-list'
        ,url: '/admin/customer/list'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left', align: 'center'}
            ,{field: 'username', title: '客户账号', minWidth: 100, align: 'center'}
            ,{field: 'contacts', title: '联系人', minWidth: 100, align: 'center'}
            ,{field: 'qq_number', title: 'QQ', align: 'center'}
            ,{field: 'email', title: '邮箱', align: 'center'}
            ,{field: 'company_name', title: '公司名称',minWidth: 100, align: 'center'}
            ,{field: 'order_count', title: '任务数量',sort: true, align: 'center'}
            ,{field: 'compliance_rate', title: '达标率',sort: true, align: 'center'}
            ,{field: 'balance', title: '余额', sort: true, align: 'center'}
            ,{field: 'lev', title: '会员等级'}
            ,{field: 'last_login_time', title: '最近登录',minWidth: 200, align: 'center'}
            ,{field: 'create_time', title: '注册时间', sort: true, minWidth: 200, align: 'center'}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', minWidth: 80, align: 'center'}
            ,{title: '操作', minWidth: 250, align: 'center', fixed: 'right', toolbar: '#table-content-list', align: 'center'}
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
    table.on('tool(LAY-app-customer-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'editBalance'){
            var params ={}
            var flag = true;
            var html = '<div class="layui-form-item">\n' +
                '        <label class="layui-form-label">修改金额</label>\n' +
                '        <div class="layui-input-inline">\n' +
                '                <input type="number" name="balance" id="balanceInput" value="0" lay-verify="required" autocomplete="off" class="layui-input">\n' +
                '        </div>\n' +
                '        <div class="layui-form-mid layui-word-aux">\n' +
                '            <span style="color: red">* 正数为增加，负数为减少</span>'+
                '        </div>\n' +
                '    </div>'
            var baindex = layer.open({
                type: 1
                ,title:'修改余额'
                ,id: 'edit-balance' //防止重复弹出
                ,content: '<div class="layui-form" style="padding: 50px 20px 0px 20px;">'+ html +'</div>'
                ,btn: ['提交','取消']
                ,btnAlign: 'c' //按钮居中
                ,shade: 0 //不显示遮罩
                ,yes: function(){
                    params.uid = data.uid;
                    params.balance = $("#balanceInput").val();
                    //alert(params.balance);return;
                    if(params.balance ==='0'){
                        layer.close(baindex);
                        return;
                    }
                    if(!flag){
                        return false;
                    }
                    flag = false;
                    admin.req({
                            url: '/admin/customer/editbalance'
                            ,type:"post"
                            ,data:params
                            ,success:function () {
                            }
                            ,done: function( res ){
                                layer.msg(res.msg, {
                                    offset: '15px'
                                    ,icon: 1
                                    ,time: 1000
                                },function () {
                                    layui.table.reload('LAY-app-customer-list'); //重载表格
                                    layer.close(baindex); //执行关闭
                                });
                            }
                        });

                }
            });
        } else if(obj.event === 'edit'){
            admin.popup({
                title: '编辑客户'
                ,area: ['700px', '600px']
                ,id: 'LAY-popup-customer-edit'
                ,success: function(layero, index){
                    view(this.id).render('seo/customer/edit', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');

                        //监听提交
                        form.on('submit(layuiadmin-seo-customer-edit-submit)', function(data){
                            var field = data.field; //获取提交的字段
                            if(field.status === "on"){
                                field.status = 1;
                            }else {
                                field.status = 0;
                            }
                            admin.req({
                                url: '/admin/customer/edit'
                                ,type:"post"
                                ,data:field
                                ,done: function( res ){
                                    //登入成功的提示与跳转
                                    layer.msg(res.msg, {
                                        offset: '15px'
                                        ,icon: 1
                                        ,time: 1000
                                    },function () {
                                        layui.table.reload('LAY-app-customer-list'); //重载表格
                                        //layer.close(index); //执行关闭
                                    });
                                }
                            });
                        });
                    });
                }
            });
        }
    });


    exports('customerlist', {})
});
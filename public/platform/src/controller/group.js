/**

 @Name：layuiAdmin 用户管理 管理员管理 角色管理
 @Author：star1029
 @Site：http://www.layui.com/platform/
 @License：LPPL

 */


layui.define(['table', 'form','setter'], function(exports){
    var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,setter = layui.setter
        ,request = setter.request
        ,table = layui.table
        ,form = layui.form;

    //角色管理
    table.render({
        elem: '#LAY-user-back-role'
        ,url: '/platform/grouplist'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {field: 'id', width: 80, title: 'ID', sort: true}
            ,{field: 'group_name', title: '代理商组名'}
            ,{field: 'keyword_free', title: '首页达标扣费'}
            ,{field: 'keyword_free2', title: '次页达标扣费'}
            ,{field: 'descr', title: '具体描述'}
            ,{title: '操作', width: 150, align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
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
    table.on('tool(LAY-user-back-role)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此会员组？', function(index){
                admin.req({
                    url:'/platform/groupdel'
                    ,type:'post'
                    ,data:data
                    ,done:function (res) {
                        layer.msg(res.msg, {
                            offset: '15px'
                            ,icon: 1
                            ,time: 1000
                        },function () {
                            obj.del();
                            layer.close(index);
                        });
                    }
                });
                obj.del();
                layer.close(index);
            });
        }else if(obj.event === 'edit'){
            admin.popup({
                title: '编辑会员组'
                ,area: ['500px', '480px']
                ,id: 'LAY-popup-user-add'
                ,success: function(layero, index){
                    view(this.id).render('set/group/goupform', data).done(function(){
                        form.render(null, 'layuiadmin-form-role');

                        //监听提交
                        form.on('submit(LAY-user-role-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            admin.req({
                                url:'/platform/groupedit'
                                ,type:'post'
                                ,data:field
                                ,done:function (res) {
                                    layer.msg(res.msg, {
                                        offset: '15px'
                                        ,icon: 1
                                        ,time: 1000
                                    },function () {
                                        layui.table.reload('LAY-user-back-role'); //重载表格
                                        layer.close(index); //执行关闭
                                    });
                                }
                            });
                        });
                    });
                }
            });
        }
    });

    exports('group', {})
});
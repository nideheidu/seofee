/**

 @Name：layuiAdmin 用户管理 管理员管理 角色管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */


layui.define(['table', 'form','setter','treetable'], function(exports){
    var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,setter = layui.setter
        ,request = setter.request
        ,table = layui.table
        ,form = layui.form;
        var treetable = layui.treetable;
    //角色管理
    treetable.render({
        treeColIndex: 1,
        treeSpid: -1,
        treeIdName: 'd_id',
        treePidName: 'd_pid',
        elem: '#LAY-user-back-role',
        url: '/admin/feelist',
        headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        },
        page: false,
        cols: [[
            {type: 'numbers'},
            {field: 'group_name', title: '会员组'},
            {field: 'minnum', title: '最低指数'}
            ,{field: 'maxnum',title: '最高指数'}
             ,{field: 'fee', title: '首页扣费金额'}
            ,{field: 'fee2', title: '次页扣费金额'}
            ,{title: '操作',  align: 'center', fixed: 'right', toolbar: '#table-useradmin-admin'}
        ]],
        done: function () {
            layer.closeAll('loading');
            treetable.foldAll('#LAY-user-back-role');
        }
    });




    //监听工具条
    table.on('tool(LAY-user-back-role)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.confirm('确定删除此扣费标准？', function(index){
                admin.req({
                    url:'/admin/feedel'
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
                title: '编辑扣费标准'
                ,area: ['500px', '480px']
                ,id: 'LAY-popup-user-add'
                ,success: function(layero, index){
                    view(this.id).render('set/seo/seoform', data).done(function(){
                        form.render(null, 'layuiadmin-form-role');

                        //监听提交
                        form.on('submit(LAY-user-role-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            admin.req({
                                url:'/admin/feedit'
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
        }else if(obj.event === 'add'){
            var group_id = obj.data.id;
            $('#member_level_id').val(group_id);
            admin.popup({
                title: '添加指数收费标准'
                ,area: ['500px', '480px']
                ,id: 'LAY-popup-user-add'
                ,success: function(layero, index){
                    view(this.id).render('set/seo/seoform',data).done(function(){
                        

                        form.render('select', 'layuiadmin-form-role');

                        //监听提交
                        form.on('submit(LAY-user-role-submit)', function(data){
                            var field = data.field; //获取提交的字段

                            //提交 Ajax 成功后，关闭当前弹层并重载表格
                            admin.req({
                                url:'/admin/feeadd'
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
    
    exports('seo', {})
});
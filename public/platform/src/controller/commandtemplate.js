layui.define(['table','form','util','setter'],function (exports) {
    var $ = layui.$
        ,admin = layui.admin
        ,util = layui.util
        ,view = layui.view
        ,table = layui.table
        ,setter = layui.setter
        ,request = setter.request
        ,form = layui.form;

    //列表
    table.render({
        elem: '#LAY-app-command-list'
        ,url: '/platform/template/list'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left', align: 'center'}
            ,{field: 'name', title: '命令名', minWidth: 100, align: 'left'}
            ,{field: 'content', title: '内容', width:560, align: 'left',templet: function(d){
                var str = '';
                var resStr = '';
                var data = JSON.parse(d.content);
                data = JSON.parse(data);
                for (var j = 0; j < data.length; j++) {
                    str = '';
                    for (var i = 0; i < data[j].length ; i++) {

                        if( i > 0 )
                        {
                            str += ',';
                        }
                        str += '"'+data[j][i]['key']+'":"'+data[j][i]['value']+'"';
                    }
                    resStr += '{'+str+'}'
                }
                return resStr;
                }}
            ,{field: 'create_time', title: '添加时间', sort: true, minWidth: 160, align: 'center'}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', width:100, align: 'center',templet: function(d){return d.status===0?'正常':'禁止'; }}
            ,{title: '操作', minWidth: 240, align: 'center', fixed: 'right', toolbar: '#table-content-list', align: 'center',toolbar: '#commandEditTpl'}
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

    table.on('tool(LAY-app-command-list)', function(obj){
        var data = obj.data;
        if(obj.event === 'orderinfo'){

            admin.popup({
                title: '编辑动作'
                ,area: ['1200px', '710px']
                ,id: 'LAY-popup-command-edit'
                ,btn:['修改','关闭']
                ,success: function(layero, index){
                    
                    view(this.id).render('simulation/template/add',data).done(function () {
                            vm.commands = JSON.parse(JSON.parse(data.content));
                            vm.id = data.id;
                            vm.title = data.name;
                            admin.req({
                                url: '/platform/command/list'
                                ,type:"get"
                                ,data:{"page":1,"limit":9999}
                                ,done: function( res ){
                                    vm.allcommand = res.data.data;
                                }
                            });  
                            //form.render(null, 'layuiadmin-app-form-add');
                        });
                }
                ,yes:function (index, layero) {
                    vm.commands.sort(function(a,b){
                            return a.sort - b.sort
                        })    //按顺序排序
                        // //监听提交
                        if(!vm.title)
                        {
                            layer.msg("请填写模板名称");
                            return false;
                        }
                        if(vm.commands.length <= 0)
                        {
                            layer.msg("请设置动作！");
                            return false;
                        }

                        admin.req({
                            url: '/platform/template/add'
                            ,type:"post"
                            ,data:{"id":vm.id,"data":JSON.stringify(vm.commands),'title':vm.title}
                            ,done: function( res ){
                                layer.msg(res.msg, {
                                    offset: '15px'
                                    ,icon: 1
                                    ,time: 1000
                                },function () {
                                    layui.table.reload('LAY-app-command-list'); //重载表格
                                    layer.close(index); //执行关闭
                                });
                            }
                        });
                }
                ,btn2: function(index, layero){
                    layer.close(index); //执行关闭
                }
            });
        }else if(obj.event === 'delete'){
            
            layer.confirm('确定要删除这条动作吗？', function(index){
                admin.req({
                    url:'/platform/template/delete'
                    ,type:'get'
                    ,data:{"id":data.id}
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
        }
    });

    exports('commandtemplate', {});
})
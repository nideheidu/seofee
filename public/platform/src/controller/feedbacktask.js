layui.define(['table','form','setter'],function (exports) {
    var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,table = layui.table
        ,setter = layui.setter
        ,request = setter.request
        ,router = layui.router()
        ,form = layui.form;
        var uid = router.search.uid;

    //客户反馈词
    table.render({
        elem: '#LAY-admin-task-feedbacklist'
        ,url: '/platform/keywords/feedback' //模拟接口
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[{
            field: 'keywords', title: '关键词', align:'center',templet: function(d){
                    var url;
                    switch (d.search_ngines) {
                        case "百度PC":
                            url = 'https://www.baidu.com/s?wd=';
                            break;
                        case "百度移动":
                            url = 'https://m.baidu.com/s?wd=';
                            break;
                        case "360PC":
                            url = 'https://www.so.com/s?ie=utf-8&q=';
                            break;
                        case "搜狗PC":
                            url = 'https://www.sogou.com/web?query=';
                            break;
                        case "搜狗移动":
                            url = 'https://wap.sogou.com/web/searchList.jsp?keyword=';
                            break;
                        case "360移动":
                            url = 'https://m.so.com/s?q=';
                            break;
                        default:
                            url = 'https://www.baidu.com/s?wd=';
                            break;
                    }
                    return '<div><a href="'+url+d.keywords+'" target="_blank" class="layui-table-link">'+d.keywords+'</a></div>'
                }}
            ,{field: 'web_url', title: '关键词链接',align:'center'}
            ,{field: 'search_ngines', title: '搜索引擎' ,width:85,align:'center'}
            ,{field:'old_rank' ,title: '反馈时排名', width:82,align:'center'}
            ,{field:'new_rank' ,title: '纠正后排名', width:82,align:'center'}
            ,{field:'create_time' ,title: '反馈时间' ,align:'center',minWidth: 145, sort: true}
            ,{field:'rank_time' ,title: '处理时间' ,align:'center',minWidth: 145, sort: true}
            ,{field:'refresh_time' ,title: '更新排名时间' ,align:'center',minWidth: 145, sort: true}
            ,{field: 'status', title: '处理状态', templet: '#buttonTpl', Width: 60, align: 'center',fixed: 'right'}
            ,{title: '操作', minWidth:120, align: 'center', fixed: 'right', toolbar: '#ftpHostTpl'}
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
    table.on('tool(LAY-admin-task-feedbacklist)', function(obj){
        var data = obj.data;
        if(obj.event === 'orderinfo'){
            admin.popup({
                title: '详情'
                ,area: ['800px', '600px']
                ,id: 'LAY-popup-content-info'
                ,btn:['修改','关闭']
                ,success: function(layero, index){

                    view(this.id).render('seo/task/editfeedback-form', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');
                    });
                }
                ,yes:function (index, layero) {
                    var new_rank = $("input[name='new_rank']").val();
                    var id = $("input[name='id']").val();
                    var status = $("#status").val();
                    admin.req({
                        url: '/platform/order/updatefeedback'
                        ,type:"post"
                        ,data:{id:id,new_rank:new_rank,status:status}
                        ,done: function( res ){
                            //登入成功的提示与跳转
                            layer.msg(res.msg, {
                                offset: '15px'
                                ,icon: 1
                                ,time: 1000
                            },function () {
                                layui.table.reload('LAY-admin-task-feedbacklist'); //重载表格
                                layer.close(index); //执行关闭
                            });
                        }
                    });
                }
                ,btn2: function(index, layero){
                    layer.close(index); //执行关闭
                }
            });
        }else if(obj.event === 'update'){
            admin.req({
                url: '/platform/task/feedbackupdate' //
                ,type:'post'
                ,data: {tid:data.id}
                ,done: function( res ){
                    //登入成功的提示与跳转
                    layer.msg(res.msg, {
                        offset: '15px'
                        ,icon: 1
                        ,time: 1000
                    },function () {
                        layui.table.reload('LAY-admin-task-feedbacklist'); //重载表格
                        layer.close(index); //执行关闭
                    });
                }
            });
        }
    });

    //对外暴露的接口
    exports('feedbacktask', {});
})
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
    var cookie = {
    set:function(key,val,time){//设置cookie方法
        var date=new Date(); //获取当前时间
        var expiresDays=time;  //将date设置为n天以后的时间
        date.setTime(date.getTime()+expiresDays*24*3600*1000); //格式化为cookie识别的时间
        document.cookie=key + "=" + val +";expires="+date.toGMTString();  //设置cookie
    },
    get:function(key){//获取cookie方法
        /*获取cookie参数*/
        var getCookie = document.cookie.replace(/[ ]/g,"");  //获取cookie，并且将获得的cookie格式化，去掉空格字符
        var arrCookie = getCookie.split(";")  //将获得的cookie以"分号"为标识 将cookie保存到arrCookie的数组中
        var tips;  //声明变量tips
        for(var i=0;i<arrCookie.length;i++){   //使用for循环查找cookie中的tips变量
            var arr=arrCookie[i].split("=");   //将单条cookie用"等号"为标识，将单条cookie保存为arr数组
            if(key==arr[0]){  //匹配变量名称，其中arr[0]是指的cookie名称，如果该条变量为tips则执行判断语句中的赋值操作
                tips=arr[1];   //将cookie的值赋给变量tips
                return tips;
                break;   //终止for循环遍历
            }
        }
      }
    };
    //客户列表
    table.render({
        elem: '#LAY-app-agent-list'
        ,url: '/platform/agent/list?action=agent'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left', align: 'center'}
            ,{field: 'username', title: '客户账号', minWidth: 100, align: 'center'}
            ,{field: 'contacts', title: '联系人', width:120, align: 'center'}
            ,{field: 'company_name', title: '公司名称',minWidth: 100, align: 'center'}
            ,{field: 'customer_count', title: '客户数量',width:100, sort: true, align: 'center',templet: '#linkTpl'}
            //,{field: 'compliance_rate', title: '达标率',width:100,sort: true, align: 'center'}
            ,{field: 'total_sum', title: '余额', width:100, sort: true, align: 'center'}
            ,{field: 'todaysum', title: '今日消费', width:100,sort: true, align: 'center'}
            ,{field: 'member_level', title: '会员等级'}
            ,{field: 'last_login_time', title: '最近登录',minWidth: 160, align: 'center'}
            ,{field: 'create_time', title: '注册时间', sort: true, minWidth: 160, align: 'center'}
            ,{field: 'status', title: '状态', templet: '#buttonTpl', width:100, align: 'center'}
            ,{title: '操作', minWidth: 240, align: 'center', fixed: 'right', toolbar: '#table-content-list', align: 'center'}
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
    table.on('tool(LAY-app-agent-list)', function(obj){
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
                    params.uid = data.id;
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
                            url: '/platform/customer/editbalance'
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
                                    layui.table.reload('LAY-app-agent-list'); //重载表格
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
                ,id: 'LAY-popup-agent-edit'
                ,success: function(layero, index){
                    console.log(data);
                    view(this.id).render('seo/agent/edit', data).done(function(){
                        form.render(null, 'layuiadmin-app-form-list');

                        //监听提交
                        form.on('submit(layuiadmin-seo-agent-edit-submit)', function(data){
                            var field = data.field; //获取提交的字段
                            if(field.status === "on"){
                                field.status = 1;
                            }else {
                                field.status = 0;
                            }
                            admin.req({
                                url: '/platform/customer/edit'
                                ,type:"post"
                                ,data:field
                                ,done: function( res ){
                                    //登入成功的提示与跳转
                                    layer.msg(res.msg, {
                                        offset: '15px'
                                        ,icon: 1
                                        ,time: 1000
                                    },function () {
                                        layui.table.reload('LAY-app-agent-list'); //重载表格
                                        //layer.close(index); //执行关闭
                                    });
                                }
                            });
                        });
                    });
                }
            });
        }else if(obj.event === 'adminlogin'){
            cookie.set("agentid",data.id,1);
            localStorage.setItem("agentid",data.id);
            window.open("http://"+window.location.host+"/system/#/user/adminlogin")
        }else if(obj.event === 'customerlist'){
            
            window.open("http://"+window.location.host+"/platform/#/seo/customer/index/agentid="+data.id)
           
        }else if(obj.event === 'delete'){
            
            layer.confirm('确定删除此代理商，同时会删除该代理商下属会员及相关数据？', function(index){
                admin.req({
                    url:'/platform/customer/del'
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
        }
    });


    exports('agentlist', {})
});
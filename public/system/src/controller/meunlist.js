
layui.define(['treeGrid', 'form'],function (exports) {
        var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,treeGrid = layui.treeGrid
        ,form = layui.form;
    //菜单管理
    treeGrid.render({
        id:'LAY-app-meun-list'
        ,elem: '#LAY-app-meun-list'
        ,idField:'id'
        ,url:'./json/data2.json'
        ,method:'get'
        ,treeId:'id'   //树形id字段名称
        ,treeUpId:'pId'   //树形父id字段名称
        ,treeShowName:'name'   //以树形式显示的字段
        ,cols: [[
            {type:'checkbox'}
            ,{field:'name',width:300, title: '菜单名称'}
            ,{field:'id',width:100, edit:'text', title: '排序'}
            ,{field:'pId', title: '状态'}
            ,{width:200,title: '操作', align:'center',fixed:'right'/*toolbar: '#barDemo'*/
                ,templet: function(d){
                    var html='';
                    var addBtn='<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="add">添加子菜单</a>';
                    var delBtn='<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>';
                    return addBtn+delBtn;
                }
            }

        ]]
        ,page:true
    });

    treeGrid.on('tool(LAY-app-meun-list)',function (obj) {
        if(obj.event === 'del'){   //删除行
            console.log(obj);
            del(obj);
        }else if(obj.event==="add"){    //添加行
            add(obj.data);
        }
    });
    function del(obj) {
        layer.confirm("你确定删除数据吗？如果存在下级节点则一并删除，此操作不能撤销！", {icon: 3, title:'提示'},
            function(index){//确定回调
                obj.del();
                layer.close(index);
            },function (index) {//取消回调
                layer.close(index);
            }
        );
    }


    var i=1000000;
    //添加
    function add(pObj) {
        var pdata=pObj?pObj.data:null;
        var param={};
        param.name='水果'+Math.random();
        param.id=++i;
        param.pId=pdata?pdata.id:null;
        treeGrid.addRow('LAY-app-meun-list',pdata?pdata[treeGrid.config.indexName]+1:0,param);
    }

    function print() {
        console.log(treeGrid.cache[tableId]);
        var loadIndex=layer.msg("对象已打印，按F12，在控制台查看！", {
            time:3000
            ,offset: 'auto'//顶部
            ,shade: 0
        });
    }

    function openorclose() {
        var map=treeGrid.getDataMap('LAY-app-meun-list');
        var o= map['102'];
        treeGrid.treeNodeOpen('LAY-app-meun-list',o,!o[treeGrid.config.cols.isOpen]);
    }


    function openAll() {
        var treedata=treeGrid.getDataTreeList('LAY-app-meun-list');
        treeGrid.treeOpenAll('LAY-app-meun-list',!treedata[0][treeGrid.config.cols.isOpen]);
    }

    function getCheckData() {
        var checkStatus = treeGrid.checkStatus('LAY-app-meun-list')
            ,data = checkStatus.data;
        layer.alert(JSON.stringify(data));
    }
    function radioStatus() {
        var data = treeGrid.radioStatus('LAY-app-meun-list')
        layer.alert(JSON.stringify(data));
    }
    function getCheckLength() {
        var checkStatus = treeGrid.checkStatus('LAY-app-meun-list')
            ,data = checkStatus.data;
        layer.msg('选中了：'+ data.length + ' 个');
    }

    function reload() {
        treeGrid.reload('LAY-app-meun-list',{
            page:{
                curr:1
            }
        });
    }
    function query() {
        treeGrid.query('LAY-app-meun-list',{
            where:{
                name:'sdfsdfsdf'
            }
        });
    }

    function test() {
        console.log(treeGrid.cache['LAY-app-meun-list'],treeGrid.getClass('LAY-app-meun-list'));


        /*var map=treeGrid.getDataMap(tableId);
        var o= map['102'];
        o.name="更新";
        treeGrid.updateRow(tableId,o);*/
    }
    exports('meunlist', {})
})
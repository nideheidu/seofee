layui.define(['table','form','setter'],function (exports) {
    var $ = layui.$
        ,admin = layui.admin
        ,view = layui.view
        ,table = layui.table
        ,setter = layui.setter
        ,request = setter.request
        ,form = layui.form;

    //优化中的任务
    table.render({
        elem: '#LAY-app-query-ranking-list'
        ,url: '/platform/keywords/list.html'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            // ,{field: 'id', width: 100, title: 'id', sort: true}
            ,{field: "company_name", title: '公司', minWidth: 100}
            ,{field: 'contacts', title: '联系人', minWidth: 100}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 100,templet: function(d){
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
                    case "百度移动":
                        url = 'https://m.baidu.com/s?word=';
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
            ,{field: 'web_url', title: '网址'}
            ,{field: '', title: '后台FTP',toolbar:'#ftpHostTpl'}
            ,{field: 'search_ngines', title: '搜索引擎'}
            ,{field:'original_rank' ,title: '初排', sort: true,templet:function(d){return d.original_rank==101 || d.original_rank==0?">100":d.original_rank}}
            ,{field:'current_ranking' ,title: '新排', sort: true,templet:function(d){return d.current_ranking==101 || d.current_ranking==0?">100":d.current_ranking}}
            ,{field:'price' ,title: '价格'}
            ,{field:'create_time' ,title: '开始时间'}
            ,{field:'status' ,title: '状态',templet: '#buttonTpl'}
            ,{title: '操作', minWidth: 150, align: 'center', fixed: 'right', toolbar: '#button-content'}
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

    exports('querylist', {});
});
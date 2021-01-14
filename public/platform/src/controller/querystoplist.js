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
        ,url: '/platform/keywords/stoplist.html'
        ,headers:{
            'access-token':(layui.data(setter.tableName)[request.tokenName] || '')
        }
        ,cols: [[
            {type: 'checkbox', fixed: 'left'}
            ,{field: 'id', width: 100, title: 'id', sort: true, align: 'center'}
            ,{field: "company_name", title: '公司', minWidth: 100, align: 'center'}
            ,{field: 'contacts', title: '联系人', minWidth: 100, align: 'center'}
            ,{field: 'keywords', title: '关键字', align:'center',minWidth: 200,templet: function(d){
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
            ,{field: 'web_url', title: '网址', align: 'center'}
            ,{field: '', title: '后台FTP',toolbar:'#ftpHostTpl', align: 'center'}
            ,{field: 'search_ngines', title: '搜索引擎', align: 'center'}
            ,{field:'original_rank' ,title: '初排', align: 'center', sort: true,templet:function(d){return d.original_rank==101 || d.original_rank==0?">100":d.original_rank}}
            ,{field:'current_ranking' ,title: '新排', align: 'center', sort: true,templet:function(d){return d.current_ranking==101 || d.current_ranking==0?">100":d.current_ranking}}
            ,{field:'create_time' ,title: '开始时间', align: 'center',minWidth: 180}
            ,{field:'status' ,title: '状态',templet: '#buttonTpl', align: 'center'}
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

    exports('querystoplist', {});
});
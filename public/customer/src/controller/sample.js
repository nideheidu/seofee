/**

 @Name：layuiAdmin 主页示例
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：GPL-2
    
 */


layui.define(function(exports){
  var admin = layui.admin;

  //区块轮播切换
  layui.use(['admin', 'carousel'], function(){
    var $ = layui.$
    ,admin = layui.admin
    ,carousel = layui.carousel
    ,element = layui.element
    ,device = layui.device();

    //轮播切换
    $('.layadmin-carousel').each(function(){
      var othis = $(this);
      carousel.render({
        elem: this
        ,width: '100%'
        ,arrow: 'none'
        ,interval: othis.data('interval')
        ,autoplay: othis.data('autoplay') === true
        ,trigger: (device.ios || device.android) ? 'click' : 'hover'
        ,anim: othis.data('anim')
      });
    });

    element.render('progress');

  });

  //八卦新闻
  layui.use(['carousel', 'echarts'], function(){
    var $ = layui.$
    ,carousel = layui.carousel
    ,echarts = layui.echarts;

    var echartsApp = [], options = [
      {
        title : {
          subtext: '完全实况球员数据',
          textStyle: {
            fontSize: 14
          }
        },
        tooltip : {
          trigger: 'axis'
        },
        legend: {
          x : 'left',
          data:['罗纳尔多','舍普琴科']
        },
        polar : [
          {
            indicator : [
              {text : '进攻', max  : 100},
              {text : '防守', max  : 100},
              {text : '体能', max  : 100},
              {text : '速度', max  : 100},
              {text : '力量', max  : 100},
              {text : '技巧', max  : 100}
            ],
            radius : 130
          }
        ],
        series : [
          {
            type: 'radar',
            center : ['50%', '50%'],
            itemStyle: {
              normal: {
                areaStyle: {
                  type: 'default'
                }
              }
            },
            data:[
              {value : [97, 42, 88, 94, 90, 86], name : '舍普琴科'},
              {value : [97, 32, 74, 95, 88, 92], name : '罗纳尔多'}
            ]
          }
        ]
      }
    ]
    ,elemDataView = $('#LAY-index-pageone').children('div')
    ,renderDataView = function(index){
      echartsApp[index] = echarts.init(elemDataView[index], layui.echartsTheme);
      echartsApp[index].setOption(options[index]);
      window.onresize = echartsApp[index].resize;
    };
    //没找到DOM，终止执行
    if(!elemDataView[0]) return;

    renderDataView(0);
  });

  //统计
  layui.use(['carousel', 'echarts'], function(){
    var $ = layui.$
    ,carousel = layui.carousel
    ,echarts = layui.echarts
    ,nextmonth = []
    ,thismonth = [];
    admin.req({
          url: '/v1/task/getcounts'
          ,type:"post"
          ,success:function (res) {
              
          }
          ,done: function( res ){
                nextmonth=res.nextmonth;
                thismonth=res.thismonth;
              
              //堆积折线图
              var echheapline = [], heapline = [
                  {
                      tooltip : {
                          trigger: 'axis'
                      },
                      legend: {data:['本月达标','上月达标']},
                      calculable : true,
                      xAxis : [
                          {
                              type : 'category',
                              boundaryGap : false,
                              data : ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31']
                          }
                      ],
                      yAxis : [
                          {
                              type : 'value'
                          }
                      ],
                      series : [
                          {
                              name:'本月达标',
                              type:'line',
                              stack: '总量',
                              data:thismonth
                          },
                          {
                              name:'上月达标',
                              type:'line',
                              stack: '总量',
                              data:nextmonth
                          }
                      ]
                  }
              ]
                  ,elemheapline = $('#LAY-index-taskline').children('div')
                  ,renderheapline = function(index){
                  echheapline[index] = echarts.init(elemheapline[index], layui.echartsTheme);
                  echheapline[index].setOption(heapline[index]);
                  window.onresize = echheapline[index].resize;
              };
              if(!elemheapline[0]) return;
                renderheapline(0);
          }
      });
      //提交审核
    // $.ajax({
    //   url: '/v1/task/getcounts' 
    //   ,type: 'get'
    //   ,dataType: 'html'
    //   ,success: function(res){
    //     res=$.parseJSON(res);
    //     nextmonth=res.nextmonth;
    //     thismonth=res.thismonth;
      
    //   //堆积折线图
    //   var echheapline = [], heapline = [
    //       {
    //           tooltip : {
    //               trigger: 'axis'
    //           },
    //           legend: {data:['本月达标','上月达标']},
    //           calculable : true,
    //           xAxis : [
    //               {
    //                   type : 'category',
    //                   boundaryGap : false,
    //                   data : ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31']
    //               }
    //           ],
    //           yAxis : [
    //               {
    //                   type : 'value'
    //               }
    //           ],
    //           series : [
    //               {
    //                   name:'本月达标',
    //                   type:'line',
    //                   stack: '总量',
    //                   data:thismonth
    //               },
    //               {
    //                   name:'上月达标',
    //                   type:'line',
    //                   stack: '总量',
    //                   data:nextmonth
    //               }
    //           ]
    //       }
    //   ]
    //       ,elemheapline = $('#LAY-index-taskline').children('div')
    //       ,renderheapline = function(index){
    //       echheapline[index] = echarts.init(elemheapline[index], layui.echartsTheme);
    //       echheapline[index].setOption(heapline[index]);
    //       window.onresize = echheapline[index].resize;
    //   };
    //   if(!elemheapline[0]) return;
    //     renderheapline(0);
    //   }
    //   ,error: function(e){
    //   }
    // });
      
    
  });

  //回复留言
  admin.events.replyNote = function(othis){
    var nid = othis.data('id');
    layer.prompt({
      title: '回复留言 ID:'+ nid
      ,formType: 2
    }, function(value, index){
      //这里可以请求 Ajax
      //…
      layer.msg('得到：'+ value);
      layer.close(index);
    });
  };

  exports('sample', {})
});
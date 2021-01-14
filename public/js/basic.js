/**
 * Created by star-x on 2017/7/31.
 */
var basic = {
    // 滚动聊天页面
    scrollPage:function(){
        //$(".pc-talk>div").last().get(0).scrollIntoView();
        if(navigator.userAgent.indexOf('Safari')>=0){
            document.querySelector(".sevice").scrollTop = document.querySelector(".pc-talk").offsetHeight;
        }else{
            var elems = document.querySelector(".pc-talk").children;
            elems[elems.length-1].scrollIntoView();
        }

    },
    // 滚动机器人页面
    scrollPage_robot:function(){
        var elems = document.querySelector(".pc-robot").children;
        elems[elems.length-1].scrollIntoView();
        // $(".pc-robot>div").last().get(0).scrollIntoView();
    },
    // 中间弹出提醒文字
    toastOut:function(txt){
        $(".content-left .toasts").find(".toastsText").text(txt).end().show();
        setTimeout(function() {
            $(".content-left .toasts").hide();
        }, 1500);
    },
    // 关闭窗口
    close_window:function(){
        window.opener=null;
        window.open('','_self');
        window.close();
    },
    beforeRender:function(str){
        if(str.length>16){
            str = str.substr(0,10)+'...'+str.substr(-6);
        };
        return str;
    }
};
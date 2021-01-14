/**
 * Created by M on 2017/6/19.
 */
(function ($) {
    var wqdVideoBackground = {};
    wqdVideoBackground.init = function () {
        if (!$(".sectionV2-video").length)return;
        var self = this;
        wqdVideoBackground.scroll();
        $(window).on("scroll", self.scroll);
    };

    wqdVideoBackground.scroll=function() {

        var $elements = $(".sectionV2-video");
        var $visibleElement = $elements.filter(function () {
            var $e = $(this),
                $w = $(window),
                wt = $w.scrollTop(),
                et = $e.offset().top,
                wb = wt + $w.height(),
                eb = et + $e.height();

            return et >= wt  && $e.is(":visible");
        });


        $visibleElement.each(function (i, _) {

            var isPlaying = $(this).find("video")[0].currentTime > 0 && !$(this).find("video")[0].paused && !$(this).find("video")[0].ended && $(this).find("video")[0].readyState > 2;

            if (isPlaying || $(this).find("video")[0].currentTime > 0 || $(this).data("end")=="1")return; //????????????????????????  ?????????????????????  ??????????????????????????????

            var $video = $(this).find("video");

            var $dom=$(this).next(".sectionV2");

            if (!$dom.attr("vsrc"))return;

            if(!$video.find("source").length){

                $('<source src=' + $dom.attr("vsrc") + ' type="video/mp4">').appendTo($video);

                new MediaEdit($(_));//??????????????????????????????

            }

            $(this).find("video").trigger("click");

        });

    };

    $(document).on('touchstart', wqdVideoBackground.scroll);

    function MediaEdit(dom){
        if (typeof(dom) != "object")return;

        this.dom=dom;

        this.$video = dom.find("video");

        this.section=this.dom.next(".sectionV2");

        this.init();
    }

    $.extend(MediaEdit.prototype,{

        init:function() {

            this.initialize();

            this.bindEvent();
        },

        initialize:function() {

            var dataStyle = this.section.attr("data-style") || "";

            this.$video[0].volume =(this.getAttribute(dataStyle,"initVolume")||0)/100;

            var speed=this.getAttribute(dataStyle,"speed")|| '1x';

            this.$video[0].playbackRate = speed.substr(0,speed.length-1); //?????????????????????

            if (this.dom.parents(".wqdIphoneView").length)this.$video[0].muted=true;

            this.loop=this.getAttribute(dataStyle,"loop");

        },
        bindEvent:function() {

            var self=this;

            self.$video.on('loadedmetadata', function () { //loadedmetadata before canplay

                if (self.dom.data("end")=="1")self.dom.data("end", "0");

            });

            self.$video.on('ended', function() {

                self.dom.removeClass("on");

                if(self.loop=="on" && self.$video.trigger("click"))return; //autoplay

                if (self.dom.parents(".wqdIphoneView").length)self.dom.data("end", "1");

            });

            self.dom.off("click.play").on("click.play", "video", function(e) {

                e.stopPropagation();
                e.preventDefault();

                var isPlaying = self.$video[0].currentTime > 0 && !self.$video[0].paused && !self.$video[0].ended && self.$video[0].readyState > 2;

                if(self.identifyBrowser()){
                    if (!isPlaying)self.$video[0].play();
                    return;
                }

                if (!isPlaying) {
                    self.$video[0].play();
                    self.exchangeBtnStatus("play",  true);
                }

            });
        },

        exchangeBtnStatus: function (element, bool) { //playing states

            if (element == "play" && bool) this.dom.addClass("on");

        },


        getAttribute: function (objString, name) { //????????????data style???????????????

            if (!objString || !name) return "";
            var paramVal = "",
                paramObj = {};
            $.each(objString.split(";"), function (i, _) {
                if (!_) return true;
                var key = _.split(":");
                paramObj[key[0]] = key[1];
            });
            paramVal = paramObj[name] || "";

            return paramVal;

        },

        identifyBrowser:function() {//Mobile && uc && qq

            return((navigator.userAgent.indexOf('UCBrowser') > -1 || navigator.userAgent.indexOf('QQBrowser') > -1) && $(".wqdIphoneView").length);

        }

    });

    wqdVideoBackground.init();

})($);
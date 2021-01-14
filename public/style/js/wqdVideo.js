/**
 * Created by M on 2017/5/11.
 */
(function ($) {
    var wqdVideo = {};
    wqdVideo.init = function () {
        this.bindEvent();
        wqdVideo.scroll()
    };
    wqdVideo.bindEvent = function (e) {
        var self = this;

        $(document).on("click",".wqdVideo .play,.wqdVideo .screenage",function() {

            var $parents=$(this).parents(".wqdelementEdit.wqdVideo");

            var $video = $parents.find("video");

            if (!$video.attr("vsrc"))return;

            $('<source src=' + $video.attr("vsrc") + ' type="video/mp4">').appendTo($video);

            new MediaEdit($parents);

            $parents.find(".play").trigger("click");

        });

        $(window).on("scroll", self.scroll);

    };

    wqdVideo.scroll=function() {

        var $elements = $(".wqdVideo[autoplay=autoplay]");
        var $visibleElement = $elements.filter(function () {
            var $e = $(this),
                $w = $(window),
                wt = $w.scrollTop(),
                et = $e.offset().top,
                wb = wt + $w.height(),
                eb = et + $e.height();

            return et >= wt && $e.is(":visible");
        });

        $visibleElement.each(function (i, _) {

            var isPlaying = $(this).find("video")[0].currentTime > 0 && !$(this).find("video")[0].paused && !$(this).find("video")[0].ended && $(this).find("video")[0].readyState > 2;

            if (isPlaying || $(this).find("video")[0].currentTime > 0 || $(this).data("end")=="1")return; //????????????????????????  ?????????????????????  ??????????????????????????????

            var $video = $(this).find("video");

            if (!$video.attr("vsrc"))return;

            if(!$video.find("source").length){
                $('<source src=' + $video.attr("vsrc") + ' type="video/mp4">').appendTo($video);

                new MediaEdit($(_)); //autoplay

            }

            $(this).find(".play").trigger("click");

        });

    };

    $(document).on('touchstart', wqdVideo.scroll);//Mobile autoplay

    function MediaEdit(dom){
        if (typeof(dom) != "object")return;

        this.dom=dom;
        this.elementId = dom.attr("elementid");
        this.$video = dom.find("video");
        this.$screenage = dom.find(".screenage");
        this.$poster = dom.find(".poster");
        this.$layer = dom.find(".layer");
        this.$play = dom.find(".play");
        this.$totalTime = dom.find(".remain");
        this.$currentTime = dom.find(".current");
        this.$process = dom.find(".process");
        this.$loading = dom.find(".loading");
        this.$point = dom.find(".point");
        this.$volume = dom.find(".volume");
        this.$volumeWrap = dom.find(".volume-wrap");
        this.$volumePoint = dom.find(".volume-point");
        this.$curVolume = dom.find(".volume-progress");
        this.$rePlay = dom.find(".re-play");
        this.$fullScreen = dom.find(".full-screen");
        this.$controls= dom.find(".controls");

        this.ratio = (this.$point.width() / 2) / this.$process.width();//video progressBall  ratio

        this.volumeRatio=(this.$volumePoint.width() / 2) / this.$volumeWrap.width();//volume progressBall  ratio

        this.origWidth = dom.width();  this.origHeight = dom.height(); this.origTop = dom.position().top; this.origLeft = dom.position().left;

        this.init();
    }

    $.extend(MediaEdit.prototype,{

        init:function() {

            this.initialize();

            this.bindEvent();
        },

        initialize:function() {

            this.autoplay=this.dom.attr("autoplay");

            this.loop=this.dom.attr("loop");

            var dataStyle = this.dom.attr("data-style") || "";

            var initVolume=(this.getAttribute(dataStyle,"initVolume")||50)/100;

            this.$video[0].volume = initVolume;//initialize volume

            this.$volume.data("volume", initVolume);

            this.$curVolume.css('width', 100*initVolume + '%');

            this.$volumePoint.css('left',100*(initVolume -  this.volumeRatio) + '%');


            var speed=this.getAttribute(dataStyle,"speed")|| '1x';

            this.$video[0].playbackRate = speed.substr(0,speed.length-1); //initialize Rate


            if(this.identifyBrowser())this.$controls.css("bottom","-50px");




        },
        bindEvent:function() {

            var self=this;


            self.$video.on('loadedmetadata', function () { //loadedmetadata before canplay

                if (self.dom.data("end")=="1")self.dom.data("end", "0");

                self.$screenage.addClass("on");


                var duration=self.getFormatTime(this.duration);

                self.$totalTime.text(duration);

                //phone && uc && qq add time
                if ($(this).parents(".wqdIphoneView").length && this.duration) $("<span class='duration'>"+duration+"</span>").appendTo(self.$layer);

            });


            self.$video.on('timeupdate', function () {


                self.$currentTime.text(self.getFormatTime(this.currentTime));

                self.$loading.css('width', 100 * this.currentTime / this.duration + '%');

                self.$point.css('left', (self.$loading.width() - 6) + "px");

            });

            self.$video.on('playing', function() {//phone pause

                !self.$screenage.hasClass("hide") && self.$screenage.addClass("hide");
                if(!self.identifyBrowser() && self.dom.parents(".wqdIphoneView").length)self.dom.find(".duration").remove();//phone no uc qq remove time

            });

            self.$video.on('pause', function() { //phone pause

                self.$screenage.hasClass("hide") && self.$screenage.removeClass("hide");

            });

            self.$video.on('ended', function() {

                self.$loading.css('width', 0 );

                self.$point.css('left', -(self.$point.width() / 2));

                self.$currentTime.text( self.getFormatTime() );

                self.$video[0].currentTime = 0;

                self.$play.removeClass("on");
                self.$video.removeClass("on");
				self.$layer.removeClass("on");

                if(self.loop && self.$screenage.trigger("click"))return;

                self.$screenage.removeClass("hide");

                if (self.dom.parents(".wqdIphoneView").length)self.dom.data("end", "1");

            });

            self.$volume.on("click", function (e) {

                var curVolume=parseFloat($(this).data("volume"));

                if(!curVolume)return;

                $(this).toggleClass("on");

                var flag = $(this).hasClass("on") ? true : false;

                //$video[0].muted = !flag;//??????
                self.$video[0].volume = flag ? 0 : curVolume ;

                self.$curVolume.css('width', flag ? 0 : 100 * curVolume + '%');

                self.$volumePoint.css('left', flag ? -(self.$volumePoint.width() / 2) : 100 * (curVolume -  self.volumeRatio) + '%');

                e.stopPropagation();
                return false;

            });

            self.$volumeWrap.on('click', function (e) {
                e = e || window.event;

                self.jumpingV(e);

                e.stopPropagation();
                e.preventDefault();
                return false;
            });

            self.$process.on('click', function (e) {

                e = e || window.event;

                self.jumpingP(e);

            });

            self.$point.on("mousedown.point", function (e) {

                e.stopPropagation();
                e.preventDefault();

                $(document).off("mousemove.point").on("mousemove.point", function (e) {

                    self.jumpingP(e);

                }).off("mouseup.point").on("mouseup.point", function () {

                    $(document).off("mousemove.point").off("mouseup.point");
                })

            });

            self.$volumePoint.on("mousedown.volume", function (e) {

                e.stopPropagation();
                e.preventDefault();

                $(document).off("mousemove.volume").on("mousemove.volume", function (e) {

                    self.jumpingV(e);

                }).off("mouseup.volume").on("mouseup.volume", function () {

                    $(document).off("mousemove.volume").off("mouseup.volume");

                })

            });


            self.$rePlay.on('click', function (e) {//replay

                if (! self.$screenage.hasClass("on hide"))return;

                self.$loading.css('width', 0 );

                self.$point.css('left', -(self.$point.width() / 2));

                self.$currentTime.text( self.getFormatTime() );

                self.$video[0].currentTime = 0;

            });

            self.dom.off("click.play").on("click.play", ".play,.screenage.on,video,.layer", function(e) { //????????????

                e.stopPropagation();
                e.preventDefault();
                e = e || window.event;
                e.target = e.target || e.srcElement;

                if (self.dom.parents(".wqdIphoneView").length && e.target.tagName.toLowerCase() === "video")return;

                if (e.target.tagName.toLowerCase() === "video" && !$(".screenage.on").hasClass("hide"))return;

                var isPlaying = self.$video[0].currentTime > 0 && !self.$video[0].paused && !self.$video[0].ended && self.$video[0].readyState > 2;

                if(self.identifyBrowser()){
                    if (!isPlaying)self.$video[0].play();
                    return;
                }

                if (!isPlaying) {
                    self.$video[0].play();
                    self.exchangeBtnStatus("play",  true);
                }else{
                    self.$video[0].pause();
                    self.exchangeBtnStatus("play",  false);
                }

            });

            ["fullscreenchange", "webkitfullscreenchange", "mozfullscreenchange", "MSFullscreenChange"].forEach(function (eventType) {

                document.addEventListener(eventType, function (event) {

                    if($("body").data("fullElement")!= self.elementId ) return;

                    if (self.isFullScreen()) { // go full-screen
                        var cltHeight = window.screen.height, cltWidth = window.screen.width;

                        self.dom.css({  width: cltWidth, height: cltHeight, top: 0, left: 0 });

                        self.exchangeBtnStatus("fullscreen", true);

                    } else { // exit full-screen

                        self.dom.css({  width: self.origWidth, height: self.origHeight, top: self.origTop, left: self.origLeft });

                        self.exchangeBtnStatus("fullscreen", false);

                    }

                    self.$point.css('left', (self.$loading.width() - 6) + "px");

                })
            });

            self.$fullScreen.on("click", function () {
                if (!self.isFullScreen()) {

                    $("body").data("fullElement", self.elementId);

                    // go full-screen
                    if (self.dom[0].requestFullscreen) {
                        self.dom[0].requestFullscreen();
                    } else if (self.dom[0].webkitRequestFullscreen) {
                        self.dom[0].webkitRequestFullscreen();
                    } else if (self.dom[0].mozRequestFullScreen) {
                        self.dom[0].mozRequestFullScreen();
                    } else if (self.dom[0].msRequestFullscreen) {
                        self.dom[0].msRequestFullscreen();
                    }
                } else {
                    // exit full-screen
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                }
            });

        },

        exchangeBtnStatus: function (element, bool) { // switch state

            if (element == "play") {//switch playing or pause
                if (bool) {
                    this.$poster.addClass("on");
                    this.$play.addClass("on");

                    if (this.identifyBrowser())return;

                    this.$video.addClass("on");
                    this.$screenage.addClass("hide");
                    this.$layer.addClass("on");

                } else {
                    if(this.identifyBrowser())return;

                    this.$layer.removeClass("on");
                    this.$play.removeClass("on");
                    this.$screenage.removeClass("hide");
                }
            }
            if (element == "fullscreen") { //switch fullscreen

                this.$controls.toggleClass("fullControll", bool);

                this.$fullScreen.toggleClass("on", bool);

            }

        },

        isFullScreen: function () {
            return document.fullscreenElement ||
                document.webkitFullscreenElement ||
                document.mozFullScreenElement ||
                document.msFullscreenElement;
        },

        getFormatTime: function (time) {

            var time = time || 0;

            var h = parseInt(time / 3600),
                m = parseInt(time / 60),
                s = parseInt(time % 60);
            h = h < 10 ? "0" + h : h;
            //m = m < 10 ? "0" + m : m;
            s = s < 10 ? "0" + s : s;

            return  m + ":" + s;
        },

        jumpingP:function(e) {

            var self = this;

            var positions = Math.max(0,Math.min(self.$process.width(),e.pageX -  self.$process.offset().left)); //Click pos

            var percentage = 100 * positions /  self.$process.width();

            self.$loading.css('width', percentage + '%');

            self.$point.css('left', (self.$loading.width() - 6) + "px");

            var curTime=percentage/100 *  self.$video[0].duration;

            self.$currentTime.text( self.getFormatTime(curTime) );

            self.$video[0].currentTime = curTime;

        },

        jumpingV:function(e) {

            e.cancelable=false;

            var self = this;

            var positions = Math.max(0, Math.min(self.$volumeWrap.width(),e.pageX - self.$volumeWrap.offset().left));

            var volumeScale = positions / self.$volumeWrap.width();

            self.$curVolume.css('width', 100 * volumeScale + '%');

            self.$volumePoint.css('left', 100 * (volumeScale -  self.volumeRatio) + '%');

            self.$video[0].volume = volumeScale;
            self.$volume.data("volume", volumeScale);

            self.$volume.toggleClass("on", volumeScale <= 0);

        },

        getAttribute: function (objString, name) { //split data-style

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

    wqdVideo.init();

})($);
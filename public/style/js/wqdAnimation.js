(function($) {
	var utility = {};
	/* ?????????????????????????????? */
	utility.transitionEndName = function(){
		var el = document.createElement('wqdtransitionEnd');
	    var transEndEventNames = {
	      WebkitTransition : 'webkitTransitionEnd',
	      MozTransition    : 'transitionend',
	      OTransition      : 'oTransitionEnd otransitionend',
	      transition       : 'transitionend'
	    };
	    for (var name in transEndEventNames) {
	      if (el.style[name] !== undefined) {
	        return { end: transEndEventNames[name] };
	      }
	    }
	    return false;
	};
	utility.animatEndName = function(){
		var el = document.createElement('wqdAnimatEnd');
	    var transEndEventNames = {
	      WebkitTransition : 'webkitAnimationEnd animationend',
	      MozTransition    : 'mozAnimationEnd',
	      OTransition      : 'oanimationend',
	      transition       : 'MSAnimationEnd animationend'
	    };
	    for (var name in transEndEventNames) {
	      if (el.style[name] !== undefined) {
	        return { end: transEndEventNames[name] };
	      }
	    }
	    return false;
	};
	$.fn.wqdTransiEnd = function (duration) {
	    var called = false;
	    var $el = this;
	    $(this).one("wqdTransitionEnd", function () { called = true });
	    var callback = function () { if (!called) $($el).trigger($.support.wqdTransition.end) };
	    setTimeout(callback, duration*1000);
	    return this;
	};
	$.fn.wqdAnimatEnd = function (duration) {
	    var called = false;
	    var $el = this;
	    $(this).one("wqdAnimationEnd", function () { called = true });
	    var callback = function () { if (!called) $($el).trigger($.support.Animation.end) };
	    setTimeout(callback, duration*1000);
	    return this;
	};

    $.support.wqdTransition = utility.transitionEndName();
    $.support.Animation = utility.animatEndName();

    if (!$.support.wqdTransition) return;

    $.event.special.wqdTransitionEnd = {
	    bindType: $.support.wqdTransition.end,
	    delegateType: $.support.wqdTransition.end,
	    handle: function (e) {
	       if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments);
	    }
    };
    $.event.special.wqdAnimationEnd = {
	    bindType: $.support.Animation.end,
	    delegateType: $.support.Animation.end,
	    handle: function (e) {
	       if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments);
	    }
    };

	$.fn.extend({
		wqdAnimate: function (indexArr) {
			$(this).trigger("wqdAnimationEnd");
			var $this    = $(this),$box = $this.find(".wqdelementEditBox"),
				animate  = $this.attr("data-animate") && $this.attr("data-animate").split(",") || [],//????????????
				duration = $this.attr("data-animation-duration") && $this.attr("data-animation-duration").split(",") || [],//????????????????????????
				delay    = $this.attr("data-animation-delay") && $this.attr("data-animation-delay").split(",") || [],//????????????
				iterationCount = $this.attr("data-animation-iteration-count") && $this.attr("data-animation-iteration-count").split(",") || [],//???????????????????????????
				action   = $this.attr("triggeraction") && $this.attr("triggeraction").split(",") || [],//????????????
				i = 0,
				j = 0,animateCss,animateStyle;
			indexArr = $.merge([],indexArr||[]);
			var addAnimate = function (i) {
				animateCss         = {};
				duration[i]        = duration[i] || 0;
				delay[i]           = delay[i] || 0;
				// ??????????????????????????????????????????????????????????????????????????????style???1???
				// iterationCount[i]  = iterationCount[i] ==="infinite" && i !== indexArr.length-1 ? 1 : iterationCount[i] ? iterationCount[i] : 0;
				iterationCount[i]  = iterationCount[i] ==="infinite" && indexArr.length > 1 ? 1 : iterationCount[i] ? iterationCount[i] : 0;
				// animateCss["animation-name"] = animate[i];
				// animateCss["animation-duration"]        = duration[i]+"s";
				// animateCss["animation-delay"]           = delay[i]+"s";
				// animateCss["animation-iteration-count"] = iterationCount[i];

				var isHide = action[i] == "hide" && iterationCount[i] != "infinite",
					iCount = iterationCount[i] == "infinite" ? 1 : iterationCount[i];
				//??????????????????????????????????????????????????????????????????????????????
				setTimeout(function () {
					isHide ? $this.addClass("aniHide") : $this.removeClass("aniHide");
				},(+(!isHide ? 0.1 : +duration[i] * iCount) + (+delay[i]) || 0)*1000 );

				// action[i] == "show" && addClass('aniHide');
				action[i] && action[i] != "hide" && !duration[i] && $this.removeClass('aniHide');
				animateCss = {
					"animation-duration":duration[i]+"s",
					"animation-delay":delay[i]+"s",
					"animation-iteration-count":iterationCount[i]
				};
				if(animate[i] == "rotateAlways") animateCss["animation-timing-function"] = "linear";
				$box.css(animateCss).addClass("wqdAnimatedos aning").css("animation-name",animate[i]);

				// // 3.8
				// // ???????????????????????????????????????????????????????????????
				// action[i] == "show" ? $this.addClass("aniHide") : $this.removeClass("aniHide");
				// // setTimeout??????????????????????????????????????????????????????????????????
				// setTimeout(function() {
				// 	//????????????????????????????????????????????????????????????????????????
				// 	setTimeout(function () {
				// 		action[i] == "show" && $this.removeClass("aniHide");
				// 	},( +(j == 0 && !isHide ? 0.1 : +duration[i] * iCount) + (+delay[i]) || 0)*1000);
				// 	$box.css(animateCss).addClass("wqdAnimatedos aning");
				// },50);


				// ??????????????????????????????????????????
				$box.one("wqdAnimationEnd",function (e) {
					// ???????????????????????????
					animateStyle = $box.attr("style").replace(/(-|\w|\s|)*animation(-|\w|\s|:)+;/g,"");
					// iterationCount[i] != "infinite" && $box.removeClass(animate[i]).attr("style",animateStyle);
					iterationCount[i] != "infinite" ? $box.removeClass(animate[i]).attr("style",animateStyle) : $box.off(".wqdAnimate");
					$box.removeClass("wqdAnimatedos aning");
					// ??????????????????????????????
					indexArr.length && indexArr.shift();
					j++;
					if(indexArr.length) {
						addAnimate(indexArr[0]);
					}else {
						$box.trigger("allAnimationEnd");
					}
				});
				if(j != indexArr.length-1 || iterationCount[i] !="infinite"){
					$box.wqdAnimatEnd((duration[i]-0)*iterationCount[i]+(delay[i]-0));
				}
			};

			if(animate.length && indexArr.length) {
				if($box.hasClass("aning")) {
					if($box.css("animation-iteration-count") == "infinite") {
						var tDuration = parseFloat($box.css("animation-duration")) || 1,
							tDelay    = parseFloat($box.css("animation-delay")) || 0;
						$box.wqdAnimatEnd(+tDuration+(+tDelay));
					}
					$box.one("wqdAnimationEnd",function (e) {
						addAnimate(indexArr[0] || i);
					})
				}else {
					addAnimate(indexArr[0] || i);
				}
			}
			return $this;
		}
	});
	$(document).on("wqdAnimateEventOff",function (e) {
		$(this).off(".wqdAnimate");
		// wqdUnveil.init();
	});
	var wqdUnveil = {};
	function bindAnimate (selector) {
		selector = selector || "[data-animate]";
		$(selector).off(".wqdAnimate").each(function(i,_) {
			var	$this = $(this),
				thisId = $this.attr("id"),
				triggerType = $this.attr("triggertype") || "unveil.wqdAnimate";
				triggerType = triggerType.split(","),
				triggerElem = $this.attr("triggerElem") || "";
			// $this.off("unveil.wqdAnimate").off("click.wqdAnimate").off("mouseenter.wqdAnimate");

			var triggerElemNum = {};
			if(triggerElem) {
				$.each(triggerElem.split(","),function (i,val) {
					val = val || thisId;
					triggerElemNum[val] ? triggerElemNum[val].push(i) : triggerElemNum[val] = [i];
				});
			} else {//????????????????????????????????????
				triggerElemNum[thisId] = $.map(triggerType,function (_,i) {
					return i;
				});
			}

			var bindTrigger = function ($node,elemId,tList) {
				var triggerClick = [],
					triggerMouse = [],
					triggerunveil = [];
				for(var i=0; i < triggerType.length; i++){
					tList[i] = tList[i] || {};
					var ev = tList[i].ev || "";
					if(ev.indexOf("click") != -1){
						triggerClick.push(i);
					}else if(ev.indexOf("mouseenter") != -1){
						triggerMouse.push(i);
					}else if(ev.indexOf("unveil") != -1){
						triggerunveil.push(i);
					}
				}

				if(triggerClick.length){
					$node.on("click.wqdAnimate",function(e){
						$("#"+elemId).wqdAnimate(triggerClick);
					}).addClass('aniTriOther');
				}
				if(triggerMouse.length){
					$node.on("mouseenter.wqdAnimate",function(e){
						$("#"+elemId).wqdAnimate(triggerMouse);
					}).addClass('aniTriOther');
				}
				if(triggerunveil.length){
					$node.one("unveil.wqdAnimate",function(e){
						$("#"+elemId).wqdAnimate(triggerunveil);
					}).addClass('aniTriOther');
				}
			};

			var bindTriggers = [];
			$.each(triggerElemNum,function (i,val) {
				var triggerList = [];
				$.each(val,function (j,__) {
					triggerList[__] = {};
					triggerList[__].ev = triggerType[__];
				});
				bindTrigger($("#"+i),thisId,triggerList);
			})
		});
	}
	wqdUnveil.init = function(){
		$("[elementandgroup='true']").off(".wqdAnimate");
		bindAnimate();
        $(document).on('slid.Wqdcarousel','.wqdCarousel',function () {
			wqdUnveil.unveil();
        	// var elements =  $(this).find(".carousel-inner .item.active .bannerContainer").find("[data-animate]");
        	// elements.each(function(){
        	// 	var triggertypes = $(this).attr("triggertype") || "unveil.wqdAnimate";
        	// 		triggertypes = triggertypes.split(",");
        	// 	for(var k=0; k < triggertypes.length; k++){
        	// 		if(triggertypes[k] == "unveil.wqdAnimate"){
        	// 			$(this).wqdAnimate();
        	// 			break;
        	// 		}
        	// 	}
        	// });
        });
	};

	wqdUnveil.unveil = function(){
		var element = $("[data-animate],.aniunveil,.progunveil");
		if($("#HTMLDATA .wqdIphoneView").length){
			var inview = element.filter(function() {
	            var that = $(this),
	            	elemTop = that.offset().top,
	            	elemTh = elemTop + that.height(),
	            	boxTop = $("#wqdIphoneContainer").offset().top,
	            	boxHeight = $(window).height() > $("#wqdIphoneContainer").height()+boxTop ? $("#wqdIphoneContainer").height()+boxTop : $(window).height();
	            return elemTop >= boxTop && elemTh <= boxHeight;
	        });
	    }else{
	    	var inview = element.filter(function() {

	            var $e = $(this), $w = $(window), wt = $w.scrollTop(), wb = wt + $w.height(), et = $e.offset().top, eb = et + $e.height();

	            return et >= wt && eb <= wb && $e.is(":visible");
	        });
	    }
        inview.trigger("unveil.wqdAnimate");
	};

	wqdUnveil.bindEvent = function(){
		if($("#HTMLDATA .wqdIphoneView").length){
			$("#wqdIphoneContainer .nano-content").scroll(function(){
				wqdUnveil.unveil();
			});
		}
		$(document).bind("scroll", function() {
            wqdUnveil.unveil();
        });
        $(document).on("domInit", function() {
        	wqdUnveil.init();
            wqdUnveil.unveil();
        });
        setTimeout(function () {
        	wqdUnveil.unveil();
        },400)
	};
	wqdUnveil.init();
	wqdUnveil.bindEvent();

	return wqdUnveil;
})(jQuery);


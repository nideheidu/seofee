﻿$(function() {
	var navTemplate = {};

	navTemplate.init = function(){
		$.fn.movebg=function(options){
			var defaults={
				width:120,/*??????????????????*/
				extra:40,/*???????????????*/
				speed:300,/*??????????????????*/
				rebound_speed:200/*??????????????????*/
			};
			var defaultser=$.extend(defaults,options);
			return this.each(function(){
				var _this=$(this);
				var _item=_this.children("ul").children("li");/*?????????????????????????????????	*/
				var origin=_this.children("ul").children("li.active").index();/*???????????????????????????*/
				var _mover=_this.find(".template1_mask");/*????????????*/
				//var hidden;/*?????????????????????html???????????????cur?????????????????????????????????*/
				if (origin==-1){origin=0;_mover.hide()} else{_mover.show()};/*??????????????????cur,?????????????????????????????????*/
				var cur=prev=origin;/*??????????????????????????????????????????????????????;*/
				var originItem = _this.children("ul").children("li").eq(origin);
				var nowLeft = originItem.position().left;
				var extra=defaultser.extra;/*?????????????????????????????????????????????*/
				_mover.css({left:nowLeft,width:originItem.outerWidth()});/*?????????????????????????????????*/
				
				//????????????????????????
				_item.each(function(index,it){
					$(it).off("mouseenter.navTemplate1").on("mouseenter.navTemplate1",function(){
						_mover.css("width",$(this).outerWidth());
						nowLeft = $(this).position().left;
						cur=index;/*??????????????????????????????*/
						move();
						prev=cur;/*??????????????????????????????????????????*/
					});
				});
				_this.off("mouseleave.navTemplate1").on("mouseleave.navTemplate1",function(){
					originItem = _this.children("ul").children("li.active").length ? _this.children("ul").children("li.active") : _this.children("ul").children("li:first-child");
					_mover.css("width",originItem.outerWidth());
					nowLeft = originItem.position().left;
					cur=origin;/*?????????????????????????????????????????????????????????*/
					move();
					!_this.children("ul").children("li.active").length && _mover.stop().fadeOut();/*???html???????????????cur?????????????????????????????????*/
				});
				
				//????????????
				function move(){
					_mover.clearQueue();
					if(cur<prev){extra=-Math.abs(defaultser.extra);} /*???????????????????????????????????????????????????????????????*/
					else{extra=Math.abs(defaultser.extra)};/*?????????????????????????????????????????????????????????*/
					_mover.queue(
						function(){
							$(this).show().stop(true,true).animate({left:nowLeft+extra},defaultser.speed),
							function(){$(this).dequeue()}
						}
					);
					_mover.queue(
						function(){
							$(this).stop(true,true).animate({left:nowLeft},defaultser.rebound_speed),
							function(){$(this).dequeue()}
						}
					);
				};
			})
		}
		$(".navTemplateWrap1").movebg();
	};

	navTemplate.init();

});	

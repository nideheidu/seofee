/*
Powered by ly200.com		http://www.ly200.com
广州联雅网络科技有限公司		020-83226791
*/
//loading加载效果
$.fn.loading=function(e){
	e=$.extend({opacity:.5,size:"big"},e);
	$(this).each(function(){
		if($(this).hasClass("masked")) return;
		var obj=$(this);
		var l=$('<div class="loading"></div>').css("opacity", 0);
		obj.addClass("masked").append(l);
		var lb=$('<div class="loading_msg loading_big"></div>').appendTo(obj);
		lb.css({
			top: obj.height() / 2 - (lb.height() + parseInt(lb.css("padding-top")) + parseInt(lb.css("padding-bottom"))) / 2,
			left: obj.width() / 2 - (lb.width() + parseInt(lb.css("padding-left")) + parseInt(lb.css("padding-right"))) / 2
		});
	});
	return this;
}
//取消loading加载效果
$.fn.unloading=function(){
	$(this).each(function(){
		$(this).find(".loading_msg, .loading").remove();
		$(this).removeClass("masked");
	});
}

//滚动插件
$.fn.carousel=function(e){
	e=$.extend({itemsPerMove:2,duration:1e3,vertical:!1,specification:"",width:0,height:0,step:1,preCtrEntity:"pre_arrow",nextCtrEntity:"next_arrow"},e);
	var t=this,
		n=t.find(".viewport"),
		r=n.find(".list"),
		i,s,o,u,a,f=!1,
		l={
			init:function(){
				var oFirst=r.children(":first"),
					oLast=r.children(":last"),
					l,c,list_len=r.children().length;
				
				if(e.vertical){	//判断滚动方式
					l=Math.max(oFirst.outerHeight(!0), oLast.outerHeight(!0));
					i=l*e.itemsPerMove;
					c=oFirst.outerHeight(!0)-oFirst.outerHeight();
					t.addClass("vertical").css({height:e.height||i-c, width:e.width||oFirst.outerWidth(!0)});
					r.height(l*list_len);
					if(l*list_len>(e.height || i-c)){
						s={scrollTop:"-="+i};
						o={scrollTop:i};
						u={scrollTop:"-="+i*e.step};
						a={scrollTop:i*e.step};
						this.bind_event();
					}
				}else{
					l=Math.max(oFirst.outerWidth(!0), oLast.outerWidth(!0));
					i=l*e.itemsPerMove;
					c=oFirst.outerWidth(!0)-oFirst.outerWidth();
					t.addClass("horizontal").css({height:e.height||oFirst.outerHeight(!0), width:e.width||i-c});
					r.width(l*list_len);
					if(l*list_len>(e.width || i-c)){
						s={scrollLeft:"-="+i};
						o={scrollLeft:"+="+i};
						u={scrollLeft:"-="+i*e.step};
						a={scrollLeft:i*e.step};
						this.bind_event();
					}
				}
			},
			step_prev:function(t){
				if(f) return;f=!0;
				for(var o=0;o<e.itemsPerMove;o++)r.prepend(r.children(":last"));
				n[e.vertical?"scrollTop":"scrollLeft"](i).stop().animate(s,{
					duration:e.duration,
					complete:function(){
						t-=1;
						f=!1;
						t>0 && l.step_prev(t);
					}
				});
			},
			step_next:function(t){
				if(f) return;
				f=!0;
				n.stop().animate(o, {
					duration:e.duration,
					complete:function(){
						l.repeatRun(function(){
							r.children(":last").after(r.children(":first"))
						}, e.itemsPerMove);
						e.vertical?n.scrollTop(0):n.scrollLeft(0);
						t-=1;
						f=!1;
						t>0 && l.step_next(t);
					}
				})
			},
			moveSlide:function(t){
				t==="next"?this.step_next(e.step):this.step_prev(e.step)
			},
			repeatRun:function(e,t){
				for(var n=0; n<t; n++) e()
			},
			bind_event:function(){
				t.find(".btn").on("click", function(e){
					l.moveSlide($(this).hasClass("prev")?"prev":"next")
				});
			}
		}
	l.init();
}
$(function(){
	$('#pdetail .description').length && showthis('#pdetail .hd span','#pdetail .bd .desc_txt',0,'cur');
	$('#pdetail .description .hd span').click(function(){
		showthis('#pdetail .hd span','#pdetail .bd .desc_txt',$(this).index(),'cur');
	});
});
$(document).ready(function(){
	nav("#sitenav .w","#sitenav .blk");
	$('#header form.search').submit(function(){if(global_obj.check_form($(this).find('*[notnull]'), $(this).find('*[format]'))){return false;}});
});



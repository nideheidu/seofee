﻿// ?????????????????????????????????????????????????????????????????????
$(function(){
	$.fn.colorSetting = function(boole,element,category) {
		$(".wqdBkEditos").each(function(){
			var hover_color = $(this).attr("wqdHoverColoros") || "",
				HoverTextcoloros =  $(this).attr("wqdhovertextcoloros") || "";
			if(hover_color || HoverTextcoloros){
				$(this).find(".wqdElHoveros").off("mouseenter").off("mouseleave");
				$(this).find(".wqdElHoveros").on("mouseenter",function(){
					var that = $(this);
					if(hover_color){
						if(that.hasClass("wqdChangeBgcoloros")) that.css("background-color",hover_color);
						if(that.hasClass("wqdChangeColoros")) that.css("color",hover_color);
						if(that.find(".wqdChildBgcoloros").length!=0) that.find(".wqdChildBgcoloros").css("background-color",hover_color);
						if(that.find(".wqdChildColoros").length!=0) that.find(".wqdChildColoros").css("color",hover_color);
					}
					if(HoverTextcoloros){
						if(that.hasClass("wqdChangeTextcoloros")) that.css("color",HoverTextcoloros);
					}

				}).on("mouseleave",function(){
					var that = $(this);
					if(hover_color){
						if(that.hasClass("wqdChangeBgcoloros")) that.css("background-color",that.attr("wqdBeforeBgcoloros"));
						if(that.hasClass("wqdChangeColoros")) that.css("color",that.attr("wqdBeforeColoros"));
						if(that.find(".wqdChildBgcoloros").length!=0) that.find(".wqdChildBgcoloros").css("background-color",that.find(".wqdChildBgcoloros").attr("wqdChildBgcoloros"));
						if(that.find(".wqdChildColoros").length!=0) that.find(".wqdChildColoros").css("color",that.find(".wqdChildColoros").attr("wqdChildColoros"));
					}
					if(HoverTextcoloros){
						if(that.hasClass("wqdChangeTextcoloros")) that.css("color",that.attr("wqdBeforeTextcoloros"));
					}

				});
			}
		});
		return this;
	};
	$(document).on("domInit",function(){
		$.fn.colorSetting(false);
	});
	$.fn.colorSetting(false);
	//????????????????????????????????????
	$.fn.unveil = function(callback1,callback2) {
        var $w = $(window), elements = this;
        elements.one("unveil", function(e,boole) {
            if (typeof callback1 === "function") {
                callback1.call(this);
            }
            /*
            else if(typeof callback2 === "function" && $(this).attr("status")=="on" && !boole){
            	callback2.call(this);
            }*/
        });
        function unveil() {
            var inview = elements.filter(function() {
                var $e = $(this), wt = $w.scrollTop(), wb = wt + $w.height(), et = $e.offset().top, eb = et + $e.height();
                return eb >= wt && et <= wb
            });
            inview.trigger("unveil",true);
            //var outview = elements.not(inview);
        	//outview.trigger("unveil",false);
        }
        $w.bind("scroll", function() {
            unveil();
        });
        $w.bind("resize", function() {
            unveil();
        });
        $(document).ready(function(){
        	 unveil();
        });
        return this;
    };
});

$(function(){

	//??????cookie
	function setCookie(keyOfCookie, value, expireDays){
		var date=new Date();
		if (!expireDays) {
			//expireDays = 24*60*60*1000;
			expireDays = 5*60*1000;
			//expireDays = 5*1000;
		}
		date.setTime(date.getTime()+expireDays);
		document.cookie = keyOfCookie + "=" + escape(value)+";expires="+date.toGMTString()+";path=/";
	}

	//??????cookie
	function getCookie(name){
		var strCookie=document.cookie;
		var arrCookie=strCookie.split("; ");
		for(var i=0;i<arrCookie.length;i++){
			var arr=arrCookie[i].split("=");
			if(arr[0]==name) {
				return arr[1];
			}
		}
		return "";
	}



	if( !$('HTMLDATA').length ){

		//?????????????????? ???????????? select??????
		$('.wqdDropDownMenuos li').click(function(){
			var node = $(this).parents('div.dropdown').find('button').find('span').eq(0);
			var beforevalue = node.text();
			node.attr('beforevalue',beforevalue).text( $(this).find('a').text() );
		});


		// ?????????????????????????????????
		$('.wqdFromSubmitos').click(function(){
			var that = $(this);
			if( window.location.host.match(/p.wqdian/gi) ){
				return false;
			}
			if($(this).find('span').text()=='????????????'){
				return false;
			}

			$(this).parents('.yzmoveContent').find('.checkbox_radio[from_submited]').removeAttr('from_submited');

			$(this).parent().find('div.wqdfromnullos').remove();

			var obj = $(this).parents('div.yzmoveContent').find('.wqdFormAreaos');
			var name =  $(this).parents('div.yzmoveContent').find('.wqdFromTitleos').text().substr(0,32) || '??????';
			var sectionId = $(this).parents('div.yzmoveContent').find('div').eq(0).attr('partid');

			var a = name.split('') ,b=0;
			for(var i=0;i<a.length;i++){
			  (a[i].charCodeAt() > 255) ? (b = b+2) : ++b;
			}


		var bigMap = {} ,notnull = [];

			obj.find('.wqdAppFromos').each(function(index){
				var map = {};
				if($(this).get(0).tagName.toLowerCase()=='input' || $(this).get(0).tagName.toLowerCase()=='textarea'){
					var key =  $(this).attr('placeholder').replace(/\*\s+/g,'');
					var val = $(this).val() || '';

					if(val=='' && $(this).attr('fsubmit')=='need'){
						notnull.push($(this));
					}

						map[key] = val.replace(/\n/g,'<br/>');
						bigMap['form'+index] = map;
				}

				if( $(this).attr('type')=='select' ){
					var selectnode = $(this).find('button.dropdown-toggle').find('span:not(.caret)');
					var key = selectnode.attr('beforevalue') || selectnode.text();
					var val = selectnode.text() || '';
					key = key.replace(/\*\s+/g,'');

					if($(this).attr('fsubmit')=='need'){
						if(key == val.replace(/\*\s+/g,'') ){
							notnull.push($(this));
						}
					}

					if(key==val){
						val = '';
					}
					map[key] = val.replace(/\n/g,'<br/>');
					bigMap['form'+index] = map;
				}


				if( $(this).find('div.checkbox').length || $(this).find('div.radio').length ){
					var cr = $(this).parents('.checkbox_radio');

					if( cr.attr('from_submited')== void 0 ){
						cr.attr('from_submited','from_submited');
						var key = cr.find('div.wqdCheckboxRadioDetailos').text();
						var val = [];
						cr.find('input:checked').each(function(){
							val.push( ($(this).next('span').text() || '').replace(/\n/g,'<br/>') );
						});

						if(!val.length && $(this).parents('.checkbox_radio').attr('fsubmit')=='need'){
							notnull.push($(this));
						}

						map[key] = val;
						bigMap['form'+index] = map;
					}
				}

			})

			var parames =  JSON.stringify(bigMap);

			if(notnull.length){
				$(this).after($('<div class="wqdfromnullos">??????????????????????????????</div>'));
				return false;
			}

			if(getCookie(sectionId) == 'yes'){
				$(this).after($('<div class="wqdfromnullos">??????????????????????????????5?????????????????????</div>'));
				return false;
			}

			$('.checkbox_radio').removeAttr('from_submited');

			$.ajax({
				url: '/pcdesign/fixed/form/submitForm',
				dateType:'json',
				type:'post',
				data : {'name':name,'sectionId':sectionId,'value':parames},
				success:function(data, status) {
					if(data.status==200){
						var text = that.text();
						that.text('????????????');
						setCookie(sectionId,'yes');
						setTimeout(function(){   that.text(text)  },1000);
					}
					bigMap = {};
				},
				error : function(){
					bigMap = {};
				}
			});
		})
	}
});

$(function(){
	if(!$('#HTMLDATA').length){
		$('.wqdtableTouchos').each(function(){
			$(this).width($(window).width());
		});
	}

	//????????????????????????
	var devicesWidth = $(".wqdIphoneView").length ? true : false;
	if(devicesWidth){
		if($('#HTMLDATA').length){
			$(document).on("domInit",function(){
				navAutoFit();
			});
		}else{
			navAutoFit();
		}
	}
	function navAutoFit(){
		$(".wqdLogoBoxos").each(function(){
			var obj = $(this).parents(".wqdBkEditos");
			if(obj.find(".wqdLogoLeftos").length) return true;
			obj.find(".navbar").removeClass("wqdNavCenteros wqdNavBottomos").addClass("wqdNavRightos").parent().prepend('<div class="wqdLogoLeftos"></div>');
			obj.find(".wqdLogoLeftos").append($(this).clone());
			obj.find(".navbar-nav li.wqdLogoCenteros").addClass("hide").html("");
			obj.find(".wqdLogoTopos").remove();
		});
	}
	//??????????????????
	$(window).on("orientationchange",function() {
		location.reload();
	});
});

//?????????????????? begin
$(function(){
	var url="";
	var isEditPage = $('#HTMLDATA').length>0;/* ???????????????????????????  */
	if(isEditPage){
		url = URLPATH+'pcdesign/design/cs/read?siteId='+USERSITEID;
	}else{
		url="/pcdesign/fixed/design/cs/read";
	}
	$.ajax({ //????????????????????? get/post ?????????
		type: "GET",
		url: url,
		dataType: "json",
		success:function(data){
			if(data.activeStatus == "on"){//????????????
				if(isEditPage){//???????????????
					dataShow(data,true);//????????????
					//????????????
					if($("#wqdIphoneContainer").length) {
						changeQQPos();
					}
				}
				if(!isEditPage){//?????????
					serverChange(data);
				}
			}
			if(data.activeStatus == "off" && isEditPage){
				dataShow(data,false);//????????????
			}
		},
		error:function(e) {
		}
	});
	function dataShow(data,boole){
		//??????????????? 1.???????????????2.?????????3.????????????
		var colorArr=[];
		var code1= data.code.split(";");
		var conten1=data.remark.replace(/<br>/g,'\n');
		colorArr.push(data.defaultBg);
		colorArr.push(data.defaultIcon);
		colorArr.push(data.openBg);
		colorArr.push(data.openIcon);
		colorArr.push(data.openTitle);
		colorArr.push(data.openContent);
		colorChange(data);
		//??????????????????????????????
		colorSetChange(data);
		if(boole){
			$("#wqdpServerD .ser-select span").addClass('on');
			$('#wqdpServerD textarea').attr("disabled",false).css({"background":"#fff"});//?????????
			$('#wqdpServerD input.promptTitle').attr("disabled",false).css({"background":"#fff"});//?????????
			// $("#wqdShowQQD .serQQ").animate({'right':"0px"},350);
			$("#wqdpServerD .ul-select").removeClass("close");//??????????????????
			$("#wqdpServerD .select-box").removeClass("bee");//?????????????????????????????????
		}else{
		}
		if (data.code.match(/uin=[\d]*/g) != null) {
			$("#wqdpServerD textarea.edit[name='code']").val(data.code.match(/uin=[\d]*/g).join(";").replace(/uin=/g, ""));
			$("#wqdpServerD input[name='qqTitle']").val(data.code.match(/alt="[^"]*/)[0].substr(5));
		};

		$("#wqdpServerD textarea.edit[name='detail']").val(conten1);
		$("#wqdpServerD .ul-select input").each(function(index){
			$(this).attr("value", colorArr[index]);
		})
		$("#serverNumber").empty();
		for(i in code1){
			var str='';
			var str='<li>'+code1[i]+'</li>';
			$("#serverNumber").append(str);
		}
		$("#wqdShowQQD .shuoming").html(data.remark);
	}
	function serverChange(data){//????????????

		var html = "";
		var code1= data.code.split(";");
		var str='';
		var conten=data.remark.replace(/\\\\n/g,"<br>");
		for(i in code1){
			str +='<li>'+code1[i]+'</li>';
		}
		//????????????????????????????????????????????????
		var yxdd = getURLStr("model");
		var sss = "right:0px;";
		if(yxdd == "pad") {
			sss = "right:97px";
		} else if(yxdd == "phone") {
			sss = "right:27px";
		}
		html += '<div id="wqdShowQQD"><div class="serQQ color1" style="'+sss+'" name="defBg">';
		html += '<div class="fa fa-phone color1" name="defIcon"></div>';
		html += '</div>';
		html += '<div class="serShow color1" name="openBg">';
		html += '<div class="header">';
		html += '<div class="fa fa-phone color1" name="openIcon"></div>';
		html += '<span class="color1" name="openTitle">????????????</span>';
		html += '</div>';
		html += '<div class="down">';
		html += '<ul id="serverNumber">';
		html += str;
		html += '</ul>';
		html += '<div class="shuoming color1" name="openContent">';
		html += conten;
		html += '</div>';
		html += '</div>';
		html += '</div></div>';
		$('body').prepend(html);
		colorChange(data);
		$("#wqdShowQQD .serQQ").hover(function(){
			$("#wqdShowQQD .serShow").removeClass('noshow').addClass('ifshow');
			if($(window).width() < 991) {
				$("body").addClass("wqdzzServer");
				//??????????????????--??????????????????(ipad???iPhone)
				$(".wqdzzServer").on("click", function() {
					if($(this).hasClass("on")) {
						$("#wqdShowQQD .serShow").removeClass('ifshow').addClass('noshow');
						$(".wqdzzServer").off("click");
						$("body").removeClass("wqdzzServer on");
					} else {
						$(this).addClass("on");
					}
				});
			}
		}, function(){
		})
		$("#wqdShowQQD .serShow").hover(function(){
		}, function(){
			$("#wqdShowQQD .serShow").removeClass('ifshow').addClass('noshow');
		})
	}
	function colorChange(data){
		$("#wqdShowQQD .color1[name=defBg]").css("background-color",data.defaultBg);
		$("#wqdShowQQD .color1[name=defIcon]").css("color",data.defaultIcon);
		$("#wqdShowQQD .color1[name=openBg]").css("background-color",data.openBg);
		$("#wqdShowQQD .color1[name=openIcon]").css("color",data.openIcon);
		$("#wqdShowQQD .color1[name=openTitle]").css("color",data.openTitle);
		$("#wqdShowQQD .color1[name=openContent]").css("color",data.openContent);
	}
	function colorSetChange(data) {
		$("#wqdpServerD input[name=defBg]").css("background",data.defaultBg);
		$("#wqdpServerD input[name=defIcon]").css("background",data.defaultIcon);
		$("#wqdpServerD input[name=openBg]").css("background",data.openBg);
		$("#wqdpServerD input[name=openIcon]").css("background",data.openIcon);
		$("#wqdpServerD input[name=openTitle]").css("background",data.openTitle);
		$("#wqdpServerD input[name=openContent]").css("background",data.openContent);
	}
	//??????????????????qq??????????????????????????????
	function changeQQPos() {
		var obj = $("#wqdpServerD").find("#wqdShowQQD");
		var str = obj.html();
		$("#wqdIphoneContainer").append('<div id="wqdShowQQD" style="background: transparent;z-index: 98;position: absolute;width: 1px;right: 0px;top: 0;bottom: 0;">'+str+'</div>');
		obj.remove();
	}
	//??????url???????????????
	function getURLStr(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)","i");
		var r = window.parent.location.search || window.location.search;//.substr(1).match(reg);
		if (r.substr(1).match(reg)!=null) {
			return r.substr(1).match(reg)[2];
		} else {
			return null;
		}
	}
})
//?????????????????? end

//??????????????????
$(function(){
	if($("#HTMLDATA").length){
		$(document).on("domInit",function(){
			var pageid = $(".menu-list-page>li.on").attr("data-pageid"),
				navSelect = false; //???????????????????????????????????????
			$(".wqdMoreNavos>.nav-li>a").each(function(){
				if($(this).attr("pageid") == pageid){
					navSelect = true;
					if(!$(this).parents("li").hasClass("active")){
						if($(this).parents('.wqdBkEditos').find('.wqdElHoveros').length){
							var hovercolor = $(this).parents('.wqdBkEditos').attr('wqdHoverColoros') || 'transparent';
							$(this).css({'background-color':hovercolor,'color':$(this).parents('.wqdBkEditos').attr('wqdhovertextcoloros')}).removeAttr('wqdBeforeBgcoloros').removeAttr('wqdbeforetextcoloros').parent('li').addClass('active').siblings('li.active').removeClass('active').find('a').eq(0).attr('wqdBeforeBgcoloros','transparent').attr('wqdbeforetextcoloros',$(this).parents('.wqdBkEditos').attr('wqdtextcoloros')).css({'background-color':'transparent','color':$(this).parents('.wqdBkEditos').attr('wqdtextcoloros')});
						}else{
							$(this).parent('li').addClass('active').siblings().removeClass('active');
						}
					}
				}
			});
			if(!navSelect && $(".wqdMoreNavos>.nav-li.active").length){
				var selectNav = $(".wqdMoreNavos>.nav-li.active").find("a");
				if(selectNav.parents('.wqdBkEditos').find('.wqdElHoveros').length){
					selectNav.attr('wqdBeforeBgcoloros','transparent').attr('wqdbeforetextcoloros',selectNav.parents('.wqdBkEditos').attr('wqdtextcoloros')).css({'background-color':'transparent','color':selectNav.parents('.wqdBkEditos').attr('wqdtextcoloros')}).parent('li').removeClass('active');
				}else{
					selectNav.parent('li').removeClass('active');
				}
			}
		});
	}else{
		var pathName = location.pathname.replace("\/",""),
			elmenDom = null;
		if(!pathName){		//??????
			$(".wqdMoreNavos>.nav-li>a").each(function(){
				if($(this).attr("homepage")=="true"){
					elmenDom = $(this);
					return false
				}
			});
		}else{
			$(".wqdMoreNavos>.nav-li>a").each(function(){
				var hrefurl = $(this).attr("href") ? $(this).attr("href").replace("\/","") : "";
				if(hrefurl == pathName){
					elmenDom = $(this);
					return false
				}
			});
		}
		if(elmenDom){
			if(elmenDom.parents('.wqdBkEditos').find('.wqdElHoveros').length){
				var hovercolor = elmenDom.parents('.wqdBkEditos').attr('wqdHoverColoros') || 'transparent';
				elmenDom.css({'background-color':hovercolor,'color':elmenDom.parents('.wqdBkEditos').attr('wqdhovertextcoloros')}).removeAttr('wqdBeforeBgcoloros').removeAttr('wqdbeforetextcoloros').parent('li').addClass('active').siblings('li.active').removeClass('active').find('a').eq(0).attr('wqdBeforeBgcoloros','transparent').attr('wqdbeforetextcoloros',elmenDom.parents('.wqdBkEditos').attr('wqdtextcoloros')).css({'background-color':'transparent','color':elmenDom.parents('.wqdBkEditos').attr('wqdtextcoloros')});
			}else{
				elmenDom.parent('li').addClass('active').siblings().removeClass('active');
			}
		}else{
			var deleDom = $(".wqdMoreNavos>.nav-li.active");
			if(deleDom.length){
				deleDom.removeClass('active').find('a').eq(0).attr('wqdBeforeBgcoloros','transparent').attr('wqdbeforetextcoloros',deleDom.parents('.wqdBkEditos').attr('wqdtextcoloros')).css({'background-color':'transparent','color':deleDom.parents('.wqdBkEditos').attr('wqdtextcoloros')});
			}
		}
		$(".wqdMoreNavos .nav-li a").each(function(){
			if($(this).attr("href")=="") $(this).attr("href","javascript:void(0);");
		});
	}
});

//??????????????????js
(function($){
	var dragging, placeholders = $();
	$.fn.sortable = function(options) {
		options = options || {};
		return this.each(function() {

			var index, items = $(this).children(), connectWith = false;
			var placeholder = $('<' + items[0].tagName + ' class="sortable-placeholder">');

			placeholders = placeholders.add(placeholder);

			items.attr('draggable', 'true')
			$(this).off();
			$(this).on('dragstart.h5s','li', function(e) {
				var dt = e.originalEvent.dataTransfer;
				dt.effectAllowed = 'move';
				dt.setData('Text', 'dummy');
				dragging = $(this).addClass('sortable-dragging');
				index = dragging.index();
			}).on('dragend.h5s','li', function() {
				if(dragging){
					dragging.removeClass('sortable-dragging').fadeIn();
					placeholders.detach();
					if (index != dragging.index()) {
						window.before_sort_index = index;
						window.after_sort_index = dragging.index();
						items.parent().trigger('sortupdate');
					}
					dragging = null;
				}

			}).on('selectstart.h5s','li', function() {
				this.dragDrop && this.dragDrop();
				return false;
			})

			items.add([this, placeholder]);
			$(this).on('dragover.h5s dragenter.h5s drop.h5s', 'li',function(e) {
				if (!items.is(dragging) && connectWith !== $(dragging).parent().data('connectWith')) {
					return true;
				}
				if (e.type == 'drop') {
					e.stopPropagation();
					placeholders.filter(':visible').after(dragging);
					return false;
				}
				e.preventDefault();
				e.originalEvent.dataTransfer.dropEffect = 'move';
				if (items.is(this)) {
					dragging.hide();
					$(this)[placeholder.index() < $(this).index() ? 'after' : 'before'](placeholder);
					placeholders.not(placeholder).detach();
				}
				return false;
			});
		});
	};
})(jQuery)

//????????????26?????????
$(function(){
	$("body").on("click",".wqdTabBar .wqdTabBarOption",function(){
		if($(this).hasClass("active")) return false;
		$(this).addClass("active").siblings(".wqdTabBarOption.active").removeClass("active").parents(".wqdBkEditos").find(".wqdTabBox > .wqdTabBox_list").eq($(this).index()).addClass("active").siblings().removeClass("active");
	});
});


/*//???????????? ????????????
$(function(){
	var size = $('.wqdSectiondiv').outerHeight() / $('.wqdSectiondiv').outerWidth();
	$('.wqdSectiondiv').children().height(size*$(document).width());

	$(window).resize(function(){
		$('.wqdSectiondiv').children().height(size*$(document).width());
	})
})*/

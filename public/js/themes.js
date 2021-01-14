// JavaScript Document
//前端显示方式(0:自适应 1:窄屏 2:宽屏)
$.fn.webDisplay=function(type){
	if(type==0){
		if($(window).width()>=1250){$('body').addClass('w_1200');}
		$(window).resize(function(){
			if($(window).width()>=1250){
				$('body').addClass('w_1200');
			}else{
				$('body').removeClass('w_1200');
			}
		});
	}else if(type==2){
		$('body').addClass('w_1200');
	}
}
function nav(father,son,bor,del){	//判断导航栏，如果太长自动隐藏  参数:父元素,子元素,border的大小,是否延迟
	var delay = del ? del : 0;
	var func = function(){
		var chd = $(son);
		var nav = $(father).width();
		var chd_width = 0;
		var border = bor ? bor : 0;
		chd.each(function(){
			var w = $(this).width();
			var pl = $(this).css("padding-left");
			var	pr = $(this).css("padding-right");
			var ml = $(this).css("margin-left");
			var mr = $(this).css("margin-right");
			var width = parseInt(w) + parseInt(pl) + parseInt(pr) + parseInt(ml) + parseInt(mr) + border;
			chd_width +=width;
			if(chd_width>nav){
				$(this).hide();
			}else{
				$(this).show();
			}
		});
	}
	func();
	$(window).resize(function(){
		if(delay){
			setTimeout(func, 210);
		}else{
			func();
		}
	});
}
function showthis(o1,o2,i,c){
	$(o1).eq(i).addClass(c).siblings(o1).removeClass(c);
	$(o2).eq(i).show().siblings(o2).hide();
}
function SetEditorContents(ContentsId){		//PC版编辑器设置
	var _this = $(ContentsId);
	var _img = _this.find("img");
	_img.each(function(){
		var _float = $(this).css("float");
		if(_float=='left'){
			$(this).css("margin-right","20px");	
		}else if(_float=='right'){
			$(this).css("margin-left","20px");	
		}	
    });	
	_this.find('td').css('word-break', 'normal');
}
$(function(){SetEditorContents("#global_editor_contents");})

function product_gallery(){// 产品详细页放大镜/static/static/inc/products/gallery.php使用的函数
	$('#small_img .small_img_list .bd').on('click', 'span', function(){
		var img=$(this).attr('pic');
		var big_img=$(this).attr('big');
		$('#bigimg_src').attr('src', img).parent().attr('href', big_img);
		$(this).addClass('on').siblings('span').removeClass('on');
		$('#zoom').css('width', 'auto');
		var_j(document).a('domready', MagicZoom.refresh);
		var_j(document).a('mousemove', MagicZoom.z1);
	}); 
}

function case_gallery(){// 案例详细页/static/static/inc/case/gallery.php使用的函数
	$('#small_img .small_img_list .bd').delegate('span', 'click', function(){
		var img=$(this).attr('pic');
		var big_img=$(this).attr('big');
		$('#bigimg_src').attr('src', img).parent().attr('href', big_img);
		$(this).addClass('on').siblings('span').removeClass('on');
	}); 
}

$(function(){
	$('.dark').css({opacity:"0.5"});//透明公共样式 
	$('#newsletter').submit(function(){//订阅ok
		if(global_obj.check_form($(this).find('*[notnull]'), $(this).find('*[format]'))){return false;}
		$(this).find('input[type=submit]').attr('disabled', 'disabled');
		$.post('?do_action=action.newsletter', $(this).serialize(), function(data){
			if(data.status==1){
				global_obj.win_alert(lang_obj.global.add_newsletter, function(){$('#newsletter input[name=Email]').val('');});
			}else{
				global_obj.win_alert('"'+data.msg+'" '+lang_obj.global.email_exists);
			}
		}, 'json');
		$(this).find('input[type=submit]').removeAttr('disabled');
		return false;
	});
	$("#footer_feedback").animate({bottom:-270},function(){
		$("#footer_feedback").animate({bottom:-290},function(){
			$("#footer_feedback").animate({bottom:-270});													
		});															
	});
	$("#footer_feedback .title .close").click(function(){$("#footer_feedback").remove();});
	
	$('#footer_feedback .title').click(function(){//底部留言js 
		if($(this).parent('#footer_feedback').attr('class')=='up'){
			$(this).parent('#footer_feedback').stop(false,true).animate({bottom:"0px"},'fast');
			$(this).parent('#footer_feedback').removeClass('up');
		}else{
			$(this).parent('#footer_feedback').stop(false,true).animate({bottom:"-270px"},'fast');
			$(this).parent('#footer_feedback').addClass('up');
		}
	});
	$('#lib_feedback_form form[name=feedback]').submit(function(){//在线留言提交处理ok
		if(global_obj.check_form($(this).find('*[notnull]'), $(this).find('*[format]'))){return false;}
		var e=$(this).find('input[name=Email]');
		var float=$(this).find('input[name=float]');
		e.removeAttr('style');
		if(e.length!=0){
			if(e.val()!='' && (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(e.val())===false)){
				e.css('border', '1px solid red');
				e.focus();
				global_obj.win_alert(lang_obj.format.email);
				return false;
			}
		}
		$(this).find('input:submit').attr('disabled', 'disabled');
		
		$.post('?do_action=action.submit_feedback', $(this).serialize(), function(data){
			$('#lib_feedback_form form[name=feedback] input:submit').removeAttr('disabled');
			global_obj.win_alert(data.msg);
			if(data.status==1){
				$('#lib_feedback_form input[type=text]').val('');
				$('#lib_feedback_form textarea').val('');
				$('#lib_feedback_form .rows span img').click();
			}else if(data.status==-1){
				$('#lib_feedback_form input[name=VCode]').css('border', '1px solid red').val('').focus().siblings('img').click();
			}else if(data.status==-2){
				
			}
		},'json');
		return false;
	});
	
	$('#lib_down_list a').click(function(){
		var id = $(this).attr('l');
		if ($(this).hasClass('pwd')){
			global_obj.win_alert('', function (){
				var pwd = $('.win_alert input[name=Password]').val();
				$.get('/', {do_action:'action.download_pwd', DId:id, pwd:pwd}, function (data){
					if (data.ret==1){
						$('.win_alert .error_tips').hide(0).html('');
						if (data.msg.url==0){//内链
							$('.win_alert').remove();
							global_obj.div_mask(1);
							window.location.href = '/?do_action=action.download'+'&DId='+id+'&pwd='+pwd;
						}else{//外链
							window.location.href = data.msg.url;
						}
					}else{
						$('.win_alert .error_tips').show(0).html(data.msg[0]);
					}
				}, 'json');
			}, 'password');
		}else{
			if(!$(this).is('[target]')){
				window.location = '/?do_action=action.download'+'&DId='+id;
			}
		}
	});//下载
	//产品附件下载
	$('.pro_right .down_list a').click(function(){
		var proid = $(this).attr('ProId');
		var path = $(this).attr('path');
		if ($(this).hasClass('pwd')){
			global_obj.win_alert('', function (){
				var pwd = $('.win_alert input[name=Password]').val();
				$.get('/', {do_action:'action.products_download_pwd', Path:path, ProId:proid, pwd:pwd}, function (data){
					if (data.ret==1){
						$('.win_alert .error_tips').hide(0).html('');
						$('.win_alert').remove();
						global_obj.div_mask(1);
						window.location.href = '/?do_action=action.products_download&Path='+path+'&ProId='+proid+'&pwd='+pwd;
					}else{
						$('.win_alert .error_tips').show(0).html(data.msg[0]);
					}
				}, 'json');
				
			}, 'password');
		}else{
			window.location = '/?do_action=action.products_download&Path='+path+'&ProId='+proid;
		}
	});//产品附件下载 end
	$('#lib_feedback_form form[name=feedback_other]').submit(function(){//在线留言提交处理
		if(global_obj.check_form($(this).find('*[notnull]'))){return false;}
		var e=$(this).find('input[name=Email]');
		var float=$(this).find('input[name=float]');
		if(e.length!=0){
			if(e.size()){
				e.removeAttr('style');
				if(e.val()!='' && (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(e.val())===false)){
					e.css('border', '1px solid red');
					e.focus();
					global_obj.win_alert(lang_obj.format.email);
					return false;
				}
			}
		}
		var phone = $(this).find('input[name=Phone]');
		var mobile = $(this).find('input[name=Mobile]');
		if	(phone.length && phone.val()!=''){
			var temp = phone.val();
			phone.removeAttr('style');
			if(!/^[0-9]\d*$/.test(temp)){
				phone.css('border', '1px solid red');
				phone.focus();
				global_obj.win_alert(lang_obj.format.telephone);
				return false;
			}
		}
		if (mobile.length && mobile.val()!=''){
			var temp = mobile.val();
			mobile.removeAttr('style');
			if(!/^[0-9]\d*$/.test(temp)){
				mobile.css('border', '1px solid red');
				mobile.focus();
				global_obj.win_alert(lang_obj.format.mobilephone);
				return false;
			}
		}
		
		$(this).find('input:submit').attr('disabled', 'disabled');
		
		$.post('?do_action=action.submit_article_feedback',$(this).serialize(), function(data){
			$('#lib_feedback_form form[name=feedback_other] input:submit').removeAttr('disabled');
			global_obj.win_alert(data.msg);
			if(data.status==1){
				$('#lib_feedback_form input[type=text]').val('');
				$('#lib_feedback_form textarea').val('');
				$('#lib_feedback_form .rows span img').click();
			}else if(data.status==-1){
				$('#lib_feedback_form input[name=VCode]').css('border', '1px solid red').val('').focus().siblings('img').click();
			}else if(data.status==-2){
				
			}
		},'json');
		return false;
	});	
	
	$(".prod_info_pdf").click(function(){$("#export_pdf").attr("src", "//pdfmyurl.com?url="+window.location.href.replace(/^http[s]?:\/\//, ""));});//PDF打印
	$('#add_to_inquiry , .add_to_inquiry').click(function(){	//加入询盘篮
		$.post('?do_action=action.add_inquiry', 'ProId='+$(this).attr('data'), function(data){
			if(data.inq_type){
				window.location.href="/inquiry.html";
			}else{
				if(data.is_there==-1){
					tips = lang_obj.global.already;
				}else{
					tips = lang_obj.global.add;
				}
				global_obj.div_mask();
				$('body').prepend('<div id="global_win_alert"><div id="alert_img"></div><div id="alert_tips">'+tips+'</div>'+'<div class="clear"></div><div id="alert_bottom"><a href="javascript:void(0);" id="alert_continue">'+lang_obj.global.continues+'</a><a href="/inquiry.html" id="alert_inquery">'+lang_obj.global.inquery+'</a></div></div>');
				$('#global_win_alert').css({
					position:'fixed',
					left:$(window).width()/2-240,
					top:'30%',
					background:'#fff',
					border:'1px solid #ccc',
					opacity:0.95,
					width:580,
					height:218,
					'z-index':100000,
					'border-radius':'8px'
				});
				$('#alert_img').css({width:'60px',height:'45px',float:'left',margin:'35px 0 0 115px'});
				$('#alert_tips').css({fontSize:'16px',width:'285px',margin:'56px 0 0 9px',float:'left'});
				$('#alert_bottom').css({background:'url(../images/ico/sh_line.png) no-repeat left 30px',overflow:'hidden',width:'492px',margin:'0 auto',paddingTop:'30px'});
				$('#alert_continue').css({marginTop:'30px',float:'left',width:'210px',height:'43px',lineHeight:'43px',textAlign:'center',color:'#ffffff','border-radius':'5px',background:'#575757',fontSize:'18px'});
				$('#alert_inquery').css({marginTop:'30px',float:'right',width:'210px',height:'43px',lineHeight:'43px',textAlign:'center',color:'#ffffff','border-radius':'5px',background:'#a4a4a4',fontSize:'18px'});
				$('#alert_continue').click(function(){
					$('#global_win_alert').remove();	
					$('#div_mask').remove();	
				});		
			}
		},'json');
	});
	$('#lib_inquire_list>ul>li .info .remove a').click(function(){//删除询盘篮
		var obj=$(this).parent().parent().parent();
		$.post('?do_action=action.del_inquiry', 'ProId='+$(this).attr('data'), function(data){
			if(data.status==1){
				global_obj.win_alert('Successful!', function(){
					// obj.remove();
					window.location.reload();
				});
			}else if(data.status==-1){
				global_obj.win_alert('Error!');
			}
		},'json');
	});
	$('#lib_inquire_list form[name=inquiry]').submit(function(){//产品询盘提交处理
		if(global_obj.check_form($(this).find('*[notnull]'))){return false;}
		var e=$(this).find('input[name=Email]');
		if(e.size()){
			e.removeAttr('style');
			if(e.val()!='' && (/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(e.val())===false)){
				e.css('border', '1px solid red');
				e.focus();
				global_obj.win_alert(lang_obj.format.email);
				return false;
			}
		}
		$(this).find('input:submit').attr('disabled', 'disabled');
		
		$.post('?do_action=action.submit_inquiry', $(this).serialize(), function(data){
			if(data.status==1){
				global_obj.win_alert(data.msg, function(){window.location.href='/';});
			}else{
				global_obj.win_alert(data.msg);
			}
		},'json');
		
		$(this).find('input:submit').removeAttr('disabled');
		return false;
	});
	$('#lib_review_form form[name=review]').submit(function(){//评论
		if(global_obj.check_form($(this).find('*[notnull]'), $(this).find('*[format]'))){return false;}
		$(this).find('input:submit').removeAttr('disabled');
		$.post('?do_action=action.submit_review', $(this).serialize(), function(data){
			if(data.status==1){
				global_obj.win_alert(data.msg, function(){window.location.href='/';});
			}else{
				global_obj.win_alert(data.msg);
			}
		},'json');
		return false;
	});
});

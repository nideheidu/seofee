/*
Powered by ly200.com		http://www.ly200.com
广州联雅网络科技有限公司		020-83226791
*/
var global_obj={
	check_form:function(notnull_obj, format_obj, type){
		var flag=false;
		if(notnull_obj){
			notnull_obj.each(function(){
				if($.trim($(this).val())==''){
					$(this).css('border', '1px solid red');
					flag==false && ($(this).focus());
					flag=true;
				}else{
					$(this).removeAttr('style');
				}
			});
			if(flag){return flag;};
		}
		if(format_obj){
			var reg={
				'MobilePhone':/^\d*$/,
				'Telephone':/^\d*-?\d*$/,
				'Fax':/^\d*-?\d*$/,
				'Email':/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/,
				'Length':/^.*/
			};
			var tips={
				'MobilePhone':lang_obj.format.mobilephone,
				'Telephone':lang_obj.format.telephone,
				'Fax':lang_obj.format.fax,
				'Email':lang_obj.format.email,
				'Length':lang_obj.format.length
			};
			if(type==1){
				format_obj.each(function(){
					var o=$(this);
					if(o.val()!=''){
						if(reg[o.attr('format')].test(o.val())===false){
							o.css('border', '1px solid red');
							o.focus();
							flag=true;
						}
					}
				});
			}else{
				format_obj.each(function(){
					var o=$(this);
					var s=o.attr('format').split('|');
					if((s[0]=='Length' && o.val().length!=parseInt(s[1])) || (s[0]!='Length' && reg[s[0]].test(o.val())===false)){
						global_obj.win_alert(tips[s[0]]);
						o.css('border', '1px solid red');
						o.focus();
						flag=true;
						return false;
					}
				});
			}
		}
		return flag;
	},
	
	win_alert:function(tips, callback, type){
		var type=(typeof(arguments[2])=='undefined')?'alert':arguments[2], html;
		global_obj.div_mask();
		html='<div class="win_alert">';
			html+='<div class="win_close"><button class="close">X</button></div>';
			if(type=='password'){
				html+='<div class="win_pwd clean"><div class="pwd_name fl">'+lang_obj.signIn.password+':</div><div class="fl pwd_r"><input name="Password" value="" type="password" autocomplete="off" class="pwd_text"><div class="error_tips"></div></div></div>';
			}else{
				html+='<div class="win_tips">'+tips+'</div>';
			}
			html+='<div class="win_btns"><button class="btn btn_sure">'+lang_obj.global.confirm+'</button>';
			if(type=='confirm' || type=='password') html+='<button class="btn btn_cancel">'+lang_obj.global.cancel+'</button>';
			html+='</div>';
		html+='</div>';
		$('body').prepend(html);
		$('.win_alert').css({left:$(window).width()/2-200});
		if(type=='confirm'){
			$('.win_alert').on('click', '.close, .btn_cancel', function(){
				$('.win_alert').remove();
				global_obj.div_mask(1);
			}).on('click', '.btn_sure', function(){
				$.isFunction(callback) && callback();
				$('.win_alert .close').click();
			});
			/*$(document).keyup(function(event){	//Esc、Space取消提示，空格、Enter确定提示
				if(event.keyCode==27 || event.keyCode==8){
					$('.win_alert .close').click();
				}else if(event.keyCode==32 || event.keyCode==13){
					$.isFunction(callback) && callback();
					$('.win_alert .close').click();
				}
			});*/
		}else if(type=='password'){
			$('.win_alert').on('click', '.close, .btn_cancel', function(){
				$('.win_alert').remove();
				global_obj.div_mask(1);
			}).on('click', '.btn_sure', function(){
				$.isFunction(callback) && callback();
				//$('.win_alert .close').click();
			});
		}else{
			$('.win_alert').on('click', '.close, .btn_sure', function(){
				$.isFunction(callback) && callback();
				$('.win_alert').remove();
				global_obj.div_mask(1);
			});
			/*$(document).keyup(function(event){	//Esc、Enter、Space、空格取消提示
				if(event.keyCode==13 || event.keyCode==8 || event.keyCode==27 || event.keyCode==32) {
					$('.win_alert .close').click();
				}
			});*/
		}
		return false;
	},
	
	div_mask:function(remove){
		if(remove==1){
			$('#div_mask').remove();
		}else{
			$('body').prepend('<div id="div_mask"></div>');
			$('#div_mask').css({width:'100%', height:$(document).height(), overflow:'hidden', position:'fixed', top:0, left:0, background:'#000', opacity:0.6, 'z-index':10000});
		}
	},
	
	data_posting:function(display, tips){
		if(display){
			$('body').prepend('<div id="data_posting"><img src="../images/ico/data_posting.gif" width="16" height="16" align="absmiddle" />&nbsp;&nbsp;'+tips+'</div>');
			$('#data_posting').css({
				width:'188px',
				height:'24px',
				'line-height':'24px',
				padding:'0 8px',
				overflow:'hidden',
				border:'1px solid #bbb',
				background:'#ddd',
				position:'fixed',
				top:'40%',
				left:'48%',
				'z-index':10001
			});
		}else{
			setTimeout('$("#data_posting").remove();', 500);
		}
	},
	
	urlencode:function(str) { 
		str = (str + '').toString();   
		return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/%20/g, '+');  
	},
	
	in_array:function(needle, arr){
		for(var i=0; i<arr.length && arr[i]!=needle; i++); 
		return !(i==arr.length);
	},
	
	is_array:function(data){
		if(Object.prototype.toString.call(data) == '[object Array]'){
			return true;
		}else{
			return false;
		}
	}
}

//Object => string
$.toJSON = typeof JSON == "object" && JSON.stringify ? JSON.stringify: function(e) {
	if (e === null) return "null";
	var t, n, r, i, s = $.type(e);
	if (s === "undefined") return undefined;
	if (s === "number" || s === "boolean") return String(e);
	if (s === "string") return $.quoteString(e);
	if (typeof e.toJSON == "function") return $.toJSON(e.toJSON());
	if (s === "date") {
		var o = e.getUTCMonth() + 1,
		u = e.getUTCDate(),
		a = e.getUTCFullYear(),
		f = e.getUTCHours(),
		l = e.getUTCMinutes(),
		c = e.getUTCSeconds(),
		h = e.getUTCMilliseconds();
		o < 10 && (o = "0" + o);
		u < 10 && (u = "0" + u);
		f < 10 && (f = "0" + f);
		l < 10 && (l = "0" + l);
		c < 10 && (c = "0" + c);
		h < 100 && (h = "0" + h);
		h < 10 && (h = "0" + h);
		return '"' + a + "-" + o + "-" + u + "T" + f + ":" + l + ":" + c + "." + h + 'Z"'
	}
	t = [];
	if ($.isArray(e)) {
		for (n = 0; n < e.length; n++) t.push($.toJSON(e[n]) || "null");
		return "[" + t.join(",") + "]"
	}
	if (typeof e == "object") {
		for (n in e) if (hasOwn.call(e, n)) {
			s = typeof n;
			if (s === "number") r = '"' + n + '"';
			else {
				if (s !== "string") continue;
				r = $.quoteString(n)
			}
			s = typeof e[n];
			if (s !== "function" && s !== "undefined") {
				i = $.toJSON(e[n]);
				t.push(r + ":" + i)
			}
		}
		return "{" + t.join(",") + "}"
	}
};

//string => Object
$.evalJSON = typeof JSON == "object" && JSON.parse ? JSON.parse: function(str) {
	return eval("(" + str + ")")
};
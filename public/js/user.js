/*
Powered by ly200.com		http://www.ly200.com
广州联雅网络科技有限公司		020-83226791
*/

var user_obj={
	/******************* 登录或注册 Start *******************/
	user_login_reg:function(){
		var frm_login=$('#lib_user_login form.login');
		frm_login.submit(function(){return false;});
		frm_login.find('button:submit').click(function(){
			if(global_obj.check_form(frm_login.find('*[notnull]'))){return false;};
			$(this).attr('disabled', true);
			
			$.post('/account/', frm_login.serialize(), function(data){
				frm_login.find('button:submit').attr('disabled', false);
				if(data.ret!=1){
					$('#error_login_box').html(data.msg[0]).show();
				}else{
					//alert(data.msg[0]);
					window.location=data.msg[0];
				}
			}, 'json');
		});
		
		var frm_register=$('#lib_user_login form.register');
		var status=0;
		frm_register.submit(function(){return false;});
		frm_register.find('button:submit').click(function(){
			if(global_obj.check_form(frm_register.find('*[notnull]'), frm_register.find('*[format]'))){
				status=1;
			}else status=0;
			
			if(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test($('#Email').val())==false){
				$('#Email').next().show();
				status=1;
			}else{
				$('#Email').next().hide();
				status=0;
			}
			
			if($('#Password').val()!=$('#Password2').val()){
				$('#Password2').next().show();
				status=1;
			}else{
				$('#Password2').next().hide();
				status=0;
			}
			if(status) return false;
			
			$(this).attr('disabled', true);
			$.post('/account/', frm_register.serialize(), function(data){
				frm_register.find('button:submit').attr('disabled', false);
				if(data.ret!=1){
					$('#error_register_box').html(data.msg[0]).show();
				}else{
					//window.location=data.msg[0];
				}
			}, 'json');
		});
	},
	
	user_login_binding:function(){
		var frm_binding=$('#lib_user_binding form.login');
		frm_binding.submit(function(){return false;});
		frm_binding.find('button:submit').click(function(){
			if(global_obj.check_form(frm_binding.find('*[notnull]'))){return false;};
			$(this).attr('disabled', true);
			
			$.post('/account/', frm_binding.serialize()+'&do_action=user_oauth_binding', function(data){
				frm_binding.find('button:submit').attr('disabled', false);
				if(data.ret!=1){
					$('#error_login_box').html(data.msg[0]).show();
				}else{
					window.location=data.msg;
				}
			}, 'json');
		});
	},
	/******************* 登录或注册 End *******************/
	
	user_address:function(){
		$('.chzn-container-single .chzn-search').css('height', $('.chzn-container-single .chzn-search input').height());
		user_obj.set_default_address(0);
		
		$('a.chzn-single').click(function(){
			$(this).parent().next('p.errorInfo').text('');
			if($(this).hasClass('chzn-single-with-drop')){
				$(this).blur().removeClass('chzn-single-with-drop').next().css({'left':'-9000px'}).parent().removeClass('chzn-container-active').css('z-index', '0').find('li.result-selected').removeClass('highlighted');
			}else{
				$(this).blur().addClass('chzn-single-with-drop').next().css({'left':'0', 'top':'27px'}).parent().addClass('chzn-container-active').css('z-index', '10').find('li.result-selected').addClass('highlighted');
			}
		});
		
		$('.chzn-results li.group-option').live('mouseover', function(){
			$(this).parent().find('li').removeClass('highlighted');
			$(this).addClass('highlighted');
		}).live('mouseout', function(){
			$(this).removeClass('highlighted');
		});
		
		$('#country_chzn li.group-option').click(function(){	//Select Country
			var obj=$('#country_chzn li.group-option').removeClass('result-selected').index($(this));
			var s_cid=$('select[name=country_id]').val();
			$(this).addClass('result-selected').parent().parent().css({'left':'-9000px'}).parent().removeClass('chzn-container-active').children('a').removeClass('chzn-single-with-drop').find('span').text($(this).text()).parent().parent().prev().find('option').eq(obj+1).attr('selected', 'selected');
			
			var cid=$('select[name=country_id]').val();
			(s_cid!=cid) && user_obj.get_state_from_country(cid);	//change country
		});
		
		$('#zoneId li.group-option').live('click', function(){
			var obj=$('#zoneId li.group-option').removeClass('result-selected').index($(this));
			$(this).addClass('result-selected').parent().parent().css({'left':'-9000px'}).parent().removeClass('chzn-container-active').children('a').removeClass('chzn-single-with-drop').find('span').text($(this).text()).parent().parent().prev().find('option').eq(obj+1).attr('selected', 'selected');
		});
			
		$(document).click(function(e){ 
			e = window.event || e; // 兼容IE7
			obj = $(e.srcElement || e.target);
			if (!$(obj).is("#country_chzn, #country_chzn *")) { 
				$('#country_chzn').removeClass('chzn-container-active').css('z-index', '0').children('a').blur().removeClass('chzn-single-with-drop').end().children('.chzn-drop').css({'left':'-9000px'}).find('input').val('').parent().next().find('.group-option').addClass('active-result');
			} 
			if (!$(obj).is("#zoneId .chzn-container, #zoneId .chzn-container *")) { 
				$('#zoneId .chzn-container').removeClass('chzn-container-active').css('z-index', '0').children('a').blur().removeClass('chzn-single-with-drop').end().children('.chzn-drop').css({'left':'-9000px'}).find('input').val('').parent().next().find('.group-option').addClass('active-result');
			} 
		});
		
		//JS search result from tagert tags
		jQuery.expr[':'].Contains = function(a,i,m){
			return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
		};
		function filterList(input, list) { 
			$(input)
			.change( function () {
				var filter = $(this).val();
				if(filter) {
					$matches = $(list).find('li:Contains(' + filter + ')');
					$('li', list).not($matches).removeClass('active-result');
					$matches.addClass('active-result');
				}else {
					$(list).find("li").addClass('active-result');
				}
				return false;
			})
			.keyup( function () {
				$(this).change();
			});
		}
		filterList("#country_chzn .chzn-search input", $("#country_chzn .chzn-results"));
		filterList("#zoneId .chzn-search input", $("#zoneId .chzn-results"));
		


		/*$('button[id=useAddress]').click(function(){	//会员处理地址信息
		});*/
		$('.editAddr').delegate('button[id=useAddress]', 'click', function(){
			if(!check_form_address()){return false;}
			$(this).attr('disabled', 'disabled');
			var obj=$('.editAddr form');
			var typeAddr=parseInt(obj.find('input[name=typeAddr]').val())==1?1:0;
			
			if(typeAddr==1){
				cart_obj.checkout_no_login();
			}else{
				$.post('/account/', obj.serialize()+'&do_action=addressbook_mod', function(data){
					if(data.status==1){
						window.top.location.reload();
					}
				}, 'json');
				
			}
			$(this).removeAttr('disabled');
			return false;
		});
		
		$('input[name=Email], input[name=FirstName], input[name=LastName], input[name=PhoneNumber]').focus(function(){$(this).next().next('p.errorInfo').text('');});
		$('input[name=AddressLine1], input[name=City], input[name=ZipCode]').focus(function(){$(this).next('p.errorInfo').text('');});
		$('select[name=tax_code_type]').change(function(){
			maxlen=$(this).val()==1?11:($(this).val()==2?14:12);
			$(this).next('input[name=tax_code_value]').attr('maxlength', maxlen).val('');
		});
		
		function check_form_address(){
			typeAddr=$('.editAddr form input[name=typeAddr]').val();
			firstname=$('.editAddr form input[name=FirstName]');
			lastname=$('.editAddr form input[name=LastName]');
			address=$('.editAddr form input[name=AddressLine1]');
			city=$('.editAddr form input[name=City]');
			state=$('.editAddr form select[name=Province]');
			country=$('.editAddr form select[name=country_id]');
			taxcode=$('.editAddr form #taxCodeValue');
			tariff=$('.editAddr form #tariffCodeValue');
			zipcode=$('.editAddr form input[name=ZipCode]');
			phone=$('.editAddr form input[name=PhoneNumber]');
			firstnameTips=lastnameTips=addressTips=cityTips=stateTips=countryTips=taxTips=tariffTips=zipTips=phoneTips='';
			
			if(typeAddr==1){
				email=$('.editAddr form input[name=Email]');
				emailTips='';
			
				if(email.val()=='')
					emailTips='Please enter your Email.'
				else if(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test(email.val())===false)
					emailTips='Email entered doesn\'t match with confirm Email value.'
					
				email!='' && email.next('p.errorInfo').text(emailTips);
			}
			
			if(firstname.val()=='')
				firstnameTips='Please enter your first name.'
			else if(firstname.val().length<2)
				firstnameTips='Your First name must contain a minimum of 2 characters.'

			if(lastname.val()=='')
				lastnameTips='Please enter your last name.';
			else if(lastname.val().length<2)
				lastnameTips='Your First name must contain a minimum of 2 characters.';

			if(address.val()=='')
				addressTips='Please enter shipping address.'
			else if(address.val().length<5)
				addressTips='Your shipping address should be at least 5 characters long.'

			if(city.val()=='')
				cityTips='Please enter your city.'
			else if(city.val().length<3)
				cityTips='Your city should be at least 3 characters long.'

			if(country.val()==-1)
				countryTips='Please select your destination country/region.'
			
			if(typeof(state.attr("disabled"))=="undefined" && state.val()==-1)	//typeof($("#aid").attr("rel"))=="undefined"
				stateTips='Please select your state/province/region.'
			
			if(typeof(taxcode.attr("disabled"))=="undefined"){
				str=taxcode.prev().val()==1?'CPF':'CNPJ';
				taxlen=taxcode.attr('maxlength');
				if(taxcode.val()=='')
					taxTips='Sorry, your ' + str + ' is required.'
				else if(taxcode.val().length<taxlen)
					taxTips='Your ' + str + ' must contain a minimum of ' + taxlen + ' numbers.'
			}
			
			if(typeof(tariff.attr("disabled"))=="undefined"){
				str=tariff.prev().val()==3?'Personal ID':'VAT ID';
				if(tariff.val()=='')
					tariffTips='Sorry, your ' + str + ' number is required.'
				else if(tariff.val().length<12)
					tariffTips='Your ' + str + ' number must contain a minimum of 12 numbers.'
			}

			if(zipcode.val()=='')
				zipTips='Please enter a ZIP / postal code.'
			else if(zipcode.val().length<4)
				zipTips='Your ZIP / postal code should be at least 4 digits long.'

			if(phone.val()=='')
				phoneTips='Please enter your phone number.'
			else if(!(/^[0-9\-]+$/g.test(phone.val())))//phone.val()==''
				phoneTips='Please enter a valid phone number.';
			else if(phone.val().replace('-', '').length<7)
				phoneTips='Your phone number must be at least 7 digits.'
			
			
			firstname!='' && firstname.next().next('p.errorInfo').text(firstnameTips);
			lastname!='' && lastname.next().next('p.errorInfo').text(lastnameTips);
			address!='' && address.next('p.errorInfo').text(addressTips);
			city!='' && city.next('p.errorInfo').text(cityTips);
			state!='' && state.next().next('p.errorInfo').text(stateTips);
			country!='' && country.next().next('p.errorInfo').text(countryTips);
			taxcode!='' && taxcode.next().next('p.errorInfo').text(taxTips);
			tariff!='' && tariff.next().next().next('p.errorInfo').text(tariffTips);
			zipcode!='' && zipcode.next('p.errorInfo').text(zipTips);
			phone!='' && phone.next().next('p.errorInfo').text(phoneTips);
			
			if(firstnameTips==''&&lastnameTips==''&&addressTips==''&&cityTips==''&&stateTips==''&&countryTips==''&&taxTips==''&&tariffTips==''&&zipTips==''&&phoneTips=='')
				return true;
			else
				return false;
		}
	},
	
	get_state_from_country:function(cid){
		$.ajax({
			url:"/account/",
			async:false,
			type:"POST",
			data:{"CId": cid, do_action:'select_country'},
			dataType:"json",
			success: function(data){
				if(data.status==1){
					d=data.contents;
					if(d==-1){
						$('#zoneId').css({'display':'none'}).find('select').attr('disabled', 'disabled');
						$('#state').css({'display':'table-row'}).find('input').removeAttr('disabled');
					}else{
						$('#zoneId').css({'display':'table-row'}).find('select').removeAttr('disabled');
						$('#state').css({'display':'none'}).find('input').attr('disabled', 'disabled');
						str='';
						var vselect='<option value="-1"></option>';
						var vli='';
						for(i=0;i<d.length;i++){
							vselect+='<option value="'+d[i]['SId']+'">'+d[i]['States']+'</option>';
							vli+='<li class="group-option active-result">'+d[i]['States']+'</li>';
						}
						$('#zoneId select').html(vselect);
						$('#zoneId ul').html(vli);
						$('#zoneId .chzn-container a span').text('Please select---');
					}
					$('#countryCode').val('+'+data.code);
					$('#phoneSample span').text(data.code);
					if(data.cid==30){
						$('#taxCode').css({'display':'table-row'}).find('select, input').removeAttr('disabled');
						$('#tariffCode').css({'display':'none'}).find('select, input').attr('disabled', 'disabled').parent().find('p.errorInfo').text('');
					}else if(data.cid==211){
						$('#tariffCode').css({'display':'table-row'}).find('select, input').removeAttr('disabled');
						$('#taxCode').css({'display':'none'}).find('select, input').attr('disabled', 'disabled').parent().find('p.errorInfo').text('');
					}else{
						$('#taxCode').css({'display':'none'}).find('select, input').attr('disabled', 'disabled').parent().find('p.errorInfo').text('');
						$('#tariffCode').css({'display':'none'}).find('select, input').attr('disabled', 'disabled').parent().find('p.errorInfo').text('');
					}
				}
			}
		});
	},
	
	set_default_address:function(AId){
		$.ajax({
			url:"/account/",
			async:false,
			type:'post',
			data:{'do_action':'get_addressbook', 'AId':AId},
			dataType:'json',
			success:function(data){
				if(data.status==1){
					$('input[name=edit_address_id]').val(data.address.AId);
					$('input[name=FirstName]').val(data.address.FirstName).parent().next().find('input[name=LastName]').val(data.address.LastName)
					$('input[name=AddressLine1]').val(data.address.AddressLine1);
					$('input[name=AddressLine2]').val(data.address.AddressLine2);
					$('input[name=City]').val(data.address.City);
					
					var index=$('select[name=country_id]').find('option[value='+data.address.CId+']').eq(0).attr('selected', 'selected').index();
					$('#country_chzn a span').text(data.country.Country);
					$('#country_chzn ul.chzn-results li.group-option').eq(index).addClass('result-selected');
					user_obj.get_state_from_country(data.address.CId);
					if(data.address.CId==30||data.address.CId==211){
						$('select[name=tax_code_type]').find('option[value='+data.address.CodeOption+']').attr('selected', 'selected');
						$('input[name=tax_code_value]').attr('maxlength', (data.address.CodeOption==1?11:(data.address.CodeOption==2?14:12))).val(data.address.TaxCode);
					}
					
					if(data.country.HasState==1){
						$('#zoneId div a span').text(data.address.StateName);
						var sindex=$('select[name=Province]').find('option[value='+data.address.SId+']').attr('selected', 'selected').index();
						$('#zoneId ul.chzn-results li.group-option').eq(sindex-1).addClass('result-selected');
					}else{
						$('input[name=State]').val(data.address.State);
					}
					
					$('input[name=ZipCode]').val(data.address.ZipCode);
					$('input[name=CountryCode]').val('+'+data.address.CountryCode).next().find('input[name=PhoneNumber]').val(data.address.PhoneNumber);
					
				}else if(data.status==2){
					$('input[name=edit_address_id]').val('');
					$('.editAddr input[name=FirstName]').val('').parent().next().find('input[name=LastName]').val('');
					$('input[name=AddressLine1]').val('');
					$('input[name=AddressLine2]').val('');
					$('input[name=City]').val('');
					$('input[name=tax_code_value]').val('');
					$('input[name=State]').val('');
					$('input[name=ZipCode]').val('');
					$('input[name=CountryCode]').val('').next().find('input[name=PhoneNumber]').val('');

					var index=$('select[name=country_id]').find('option[value='+data.country.CId+']').eq(0).attr('selected', 'selected').index();
					$('#country_chzn a span').text(data.country.Country);
					$('#country_chzn ul.chzn-results li.group-option').eq(index).addClass('result-selected');
					user_obj.get_state_from_country(data.country.CId);
				}else{
					global_obj.win_alert(data.error);
				}
			}
		});
		/*$.post('/account/', "&do_action=get_addressbook&AId="+AId, function(data){
		}, 'json');*/
	},
	
	address_init:function(){
		$('#edit_billing_addr').click(function(){
			var addrId=$(this).attr('addrid');
			$('#addressForm .errorInfo').html('');
			user_obj.set_default_address(addrId);
			$('#addressForm').slideUp('fast', function(){
				$('.billing_addr, .shipping_addr').slideUp('fast', function(){
					$('#addressForm').children('.big').text($('.billing_addr').children('.big').text());
					$('#addressForm').slideDown('fast');
				});
			});
			return false;
		});
		
		$('#addAddress').click(function(){
			$('#addressForm .errorInfo').html('');
			user_obj.set_default_address(0);
			$('#addressForm').slideUp('fast', function(){
				$('.billing_addr, .shipping_addr').slideUp('fast', function(){
					$('#addressForm').children('.big').text($('.shipping_addr').children('.big').text());
					$('#addressForm').slideDown('fast');
				});
			});
			return false;
		});
		
		$('.edit_shipping_addr').click(function(){
			var addrId=$(this).attr('addrid');
			$('#addressForm').slideUp('fast', function(){
				$('#addressForm .errorInfo').html('');
				user_obj.set_default_address(addrId);
				$('.billing_addr, .shipping_addr').slideUp('fast', function(){
					$('#addressForm').children('.big').text($('.shipping_addr').children('.big').text());
					$('#addressForm').slideDown('fast');
				});
			});
			return false;
		});
		
		$('#cancelAddr').click(function(){
			$('#addressForm').slideUp('fast', function(){
				$('.billing_addr, .shipping_addr').slideDown('fast');
			});
			return false;
		});
		
		$('.del_shipping_addr').click(function() {
			return window.confirm('Are you sure want to delete this shipping address?');
		});
	}
};

var account_obj={
	sign_in_init:function(){
		//点击登录链接，显示登录框
		$('body').on('click', '.SignInButton', function(){account_obj.set_form_sign_in();});

		//关闭登录
		$('body').on('click', '#signin_close, #div_mask', function(){
			if($('#signin_module').length){
				$('#signin_module').remove();
				global_obj.div_mask(1);
			}
		});
		
		//会员登录
		$('body').delegate('#signin_module form[name=signin_form]', 'submit', function(){
			if(global_obj.check_form($(this).find('*[notnull]'))){return false;};
			var Email=$(this).find('input[name=Email]').val();
			var r=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
			if(!r.test(Email)){
				alert(lang_obj.format.email);
				return false;
			}
			$(this).find('button:submit').attr('disabled', true);
			$.post('?do_action=user.login', $(this).serialize(), function(data){
				$('#signin_module form[name=signin_form] button:submit').removeAttr('disabled');
				if(data.ret!=1){
					$('#error_login_box').html(data.msg[0]).show();
				}else{
					window.location='/account/';
				}
			}, 'json');
			
			return false;
		});
	},
	
	set_form_sign_in:function(){//生成登录框
		var signin_html='<div id="signin_module">';
			signin_html=signin_html+'<div class="box_bg"></div><a class="noCtrTrack" id="signin_close">×</a>';
			signin_html=signin_html+'<div id="lb-wrapper"><form name="signin_form" class="login" method="POST">';
				signin_html=signin_html+'<h3>'+lang_obj.signIn.title+'</h3>';
				signin_html=signin_html+'<div id="error_login_box" class="error_note_box">'+lang_obj.signIn.error_note+'</div>';
				signin_html=signin_html+'<div class="row"><label for="Email">'+lang_obj.signIn.email+'</label><input name="Email" class="lib_txt" type="text" maxlength="100" size="43" format="Email" notnull /></div>';
				signin_html=signin_html+'<div class="row"><label for="Password">'+lang_obj.signIn.password+'</label><input name="Password" class="lib_txt" type="password" size="43" notnull /></div>';
				signin_html=signin_html+'<div class="row">'+lang_obj.signIn.forgot+'</div>';
				signin_html=signin_html+'<div class="row protect"><input class="ckb" type="checkbox" name="IsStay" value="1" checked="checked" /> '+lang_obj.signIn.stay_note+'</div>';
				signin_html=signin_html+'<div class="row"><button class="signbtn signin FontBgColor" type="submit">'+lang_obj.signIn.sign_in+'</button><a href="/account/sign-up.html" class="signbtn signup">'+lang_obj.signIn.join_fee+'</a></div>';					
			signin_html=signin_html+'</form></div>';
		signin_html=signin_html+'</div>';
		
		$('#signin_module').length && $('#signin_module').remove();
		$('body').prepend(signin_html);
		$('#signin_module').css({left:$(window).width()/2-220});
		global_obj.div_mask();
	},
	
	sign_up_init:function(){
		var frm_register=$('#signup form.register');
		var status=0;
		$('input[name=Birthday]', frm_register).daterangepicker({
			showDropdowns:true,
			singleDatePicker:true,
			timePicker:false,
			format:'MM/DD/YYYY'
		});
		frm_register.submit(function(){return false;});
		frm_register.find('button:submit').click(function(){
			status=0;
			if(global_obj.check_form(frm_register.find('*[notnull]'), frm_register.find('*[format]'))){
				status=1;
			}

			if(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test($('#Email').val())==false){
				$('#Email').next().show();
				status=1;
			}else{
				$('#Email').next().hide();
			}
			//密码长度大于6位
			if($('#Password').val().length<6){
				$('#Password').css('border', '1px solid red');
				$('#Password').next().show();
				status=1;
			}else{
				$('#Password').next().hide();
				$('#Password').removeAttr('style');
			}
			
			if($('#Password').val()!=$('#Password2').val()){
				$('#Password2').next().show();
				status=1;
			}else{
				$('#Password2').next().hide();
			}
			if(status==1) return false;
			$(this).attr('disabled', true);
			
			$.post('?do_action=user.register', frm_register.serialize(), function(data){
				frm_register.find('button:submit').attr('disabled', false);
				if(data.ret!=1){
					$('#error_register_box').html(data.msg[0]).show();
				}else{
					window.location='/account/';
				}
			}, 'json');
		});//已完成    
		
		$('.amount').keydown(function(e){
			var value=$(this).val();
			var key=window.event?e.keyCode:e.which;
			if((key>95 && key<106) || (key>47 && key<60) || (key==109 && value.indexOf("-")<0) || (key==110 && value.indexOf(".")<0) || (key==190 && value.indexOf(".")<0)){
			}else if(key!=8){
				if(window.event){//IE
					e.returnValue=false;
				}else{//Firefox
					e.preventDefault();
				}
				return false;
			}
		})
	}, 
	
	user_login_binding:function(){
		var frm_binding=$('#lib_user_binding form.login');
		frm_binding.submit(function(){return false;});
		frm_binding.find('button:submit').click(function(){
			if(global_obj.check_form(frm_binding.find('*[notnull]'))){return false;};
			$(this).attr('disabled', true);
			
			$.post('/account/', frm_binding.serialize()+'&do_action=user_oauth_binding', function(data){
				frm_binding.find('button:submit').attr('disabled', false);
				if(data.ret!=1){
					$('#error_login_box').html(data.msg[0]).show();
				}else{
					window.location=data.msg;
				}
			}, 'json');
		});
	},
	
	forgot_init:function (){
		var frm_register=$('#signup form.register');
		frm_register.submit(function(){return false;});
		frm_register.find('.fotgotbtn').click(function(){//发送忘记密码邮件
			if(global_obj.check_form(frm_register.find('*[notnull]'), frm_register.find('*[format]'))){
				status=1;
			}else status=0;
			
			if(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/.test($('#Email').val())==false){
				$('#Email').next().show();
				status=1;
			}else{
				$('#Email').next().hide();
				status=0;
			}
			
			if(status==1) return false;
			$(this).attr('disabled', true);
			$.post('?do_action=user.forgot', frm_register.serialize(), function(data){
				frm_register.find('.fotgotbtn').attr('disabled', false);
				if(data.ret!=1){
					$('#error_register_box').html(data.msg[0]).show();
				}else{
					window.location=data.msg[0];
				}
			}, 'json');
		});
		
		frm_register.find('.resetbtn').click(function(){//发送忘记密码邮件
			if(global_obj.check_form(frm_register.find('*[notnull]'), frm_register.find('*[format]'))){
				status=1;
			}else status=0;
			
			if($('#Password').val()!=$('#Password2').val()){
				$('#Password2').next().show();
				status=1;
			}else{
				$('#Password2').next().hide();
				status=0;
			}
			
			if(status==1) return false;
			$(this).attr('disabled', true);
			
			$.post('?do_action=user.reset_password', frm_register.serialize(), function(data){
				frm_register.find('.resetbtn').attr('disabled', false);
				if(data.ret!=1){
					$('#error_register_box').html(data.msg[0]).show();
				}else{
					window.location=data.msg[0];
				}
			}, 'json');
		});
		
	}
};
	$(function(){
	if($("html").html().indexOf('colorbox-min')==-1){
		$("html").append('<script type="text/javascript" src="https://libs.wqdian.net/colorbox/jquery.colorbox-min.js"></script>');
	}
	//?????????????????????
	var obj=$("[data-elementtype=form]");
	if(!obj.length)return;
	obj.attr("eleid",obj.parent().parent().attr("id"));
	obj.children().css("overflow",'visible').children().css("overflow",'visible');
	obj.each(function(n,ele){
		//obj.parents(".yzmoveContent ").append(obj.eq(n).css({"zIndex":450,'top':obj.eq(n).offset().top,'left':obj.eq(n).offset().left}));
	});

    //???????????????????????????
    $("[data-elementtype=formTextarea]").find('textarea').removeAttr('readonly disabled').val("");
    $("[data-elementtype=formInput], [data-elementtype=formDate]").find('input').removeAttr('readonly disabled');
	$("[data-elementtype=formSelect]").each(function(n,ele){
		$("[data-elementtype=formSelect]").eq(n).attr("zindexn",$("[data-elementtype=formSelect]").eq(n).css("zIndex"));
	});
	$("[data-elementtype=formDate]").each(function(n,ele){
		$("[data-elementtype=formDate]").eq(n).attr("zindexn",$("[data-elementtype=formDate]").eq(n).css("zIndex"));
	});
	$("[data-elementtype=formButton]").css("cursor",'pointer');
	//????????????
	var oDate=new Date();
	$(".wqd-form-element.formDate.wqdControlFormEl6 input[type=text]").val(oDate.getFullYear()+"-"+(oDate.getMonth()+1)+"-"+oDate.getDate());
	//?????????????????????
	var obj=$(".wqd-form-element.formSelect.wqdControlFormEl5 p span");
	if(obj.html()=="?????????"){
		obj.html(obj.parent().next().find("li span").eq(0).html());
	}
	//??????
	$(".wqd-form.wqdControlForm .wqdControlFormEl3").on("click",function(){ 
		if($(this).find("i").hasClass("on")) return;
		var name=$(this).find("input").attr("name");
		$("[name="+name+"]").removeAttr("checked").prev().removeClass("on");
		$(this).find("i").addClass("on").next("input").attr("checked","checked");
	});
	//??????
	$(".wqd-form.wqdControlForm .wqdControlFormEl4").on("click",function(){
		if($(this).find("i").hasClass("on")) $(this).find("i").removeClass("on").next("input").removeAttr("checked");
		else $(this).find("i").addClass("on").next("input").attr("checked","checked");
	});
	//???????????????
	$(".wqd-form-element.formSelect.wqdControlFormEl5 p").on("click",function(){

		if($(this).hasClass("on")) {
			$(this).removeClass("on").parent().removeClass("on");
			$(this).parents('section.sectionV2').css({'zIndex':5,'overflow':'hidden'});
		}
		else {
			$(this).addClass("on").parent().addClass("on");
			$(this).parents('section.sectionV2').css({'zIndex':6,'overflow':'visible'});
		}
		$(this).parents("[data-elementtype=formSelect]").css("zIndex",403);
	});
	$(".wqd-form-element.formSelect.wqdControlFormEl5 ul li").on("mousedown",function(){
		$(this).parents('section.sectionV2').css({'zIndex':5,'overflow':'visible'});
		if(!$(this).hasClass("on")) $(this).addClass("on").siblings().removeClass("on");
		$(this).parent().siblings("p").children("span").html($(this).children("span").html());
		$(this).parent().siblings("p").removeClass("on").parent().removeClass("on");
		$(this).parents("[data-elementtype=formSelect]").css("zIndex",$(this).parents("[data-elementtype=formSelect]").attr("zindexn"));
	});
    $('.wqdNavLi').hover(function(){
        $('section.sectionV2').css({'zIndex':5});
    },function(){
        $('section.sectionV2').css({'zIndex':5});
    });
	$("body").not(".wqd-form-element.formSelect.wqdControlFormEl5").on("mouseup",function(){
		if($(".wqd-form-element.formSelect.wqdControlFormEl5").hasClass("on")){
            $(this).parents('section.sectionV2').css({'zIndex':5,'overflow':'visible'});
			$(".wqd-form-element.formSelect.wqdControlFormEl5").removeClass("on").children("p").removeClass("on");
			//$("[data-elementtype=formSelect]").css("zIndex",400);
			$("[data-elementtype=formSelect]").each(function(n,ele){
				$("[data-elementtype=formSelect]").eq(n).css("zIndex",$("[data-elementtype=formSelect]").eq(n).attr("zindexn"));
			});
		}
	});
	function isAttr(obj,title){
		obj.each(function(n,ele){
			obj.eq(n).attr("wqdformtitle")?obj.eq(n).attr("wqdformtitle"):obj.eq(n).attr("wqdformtitle",title);
		});
	};
	//??????????????????
	isAttr($("[data-elementtype=formInput]"),"??????");//?????????
	isAttr($("[data-elementtype=formTextarea]"),"??????");//?????????
	isAttr($("[data-elementtype=formRadio]"),"??????");//?????????
	isAttr($("[data-elementtype=formCheckbox]"),"??????");//?????????
	isAttr($("[data-elementtype=formSelect]"),"??????");//?????????
	isAttr($("[data-elementtype=formDate]"),"??????");//??????
	
	
	//????????????
	var json={};
	function setCookie(cookieName,elementid){//??????cookie
		var gettime=new Date();
		//cookie????????????
		var nDate=new Date();
		nDate.setTime(nDate.getTime()+1000*60*60*24);
		document.cookie=(cookieName+elementid)+"="+gettime.getTime()+";expires="+nDate.toGMTString();
		document.cookie=('clickn'+elementid)+'='+1+";expires="+nDate.toGMTString();
	}
	function getCookie(cookieName,elementid){//??????cookie
		var allCookie=document.cookie;
		if(allCookie.indexOf(cookieName+elementid)==-1){
			return true;
		}else{
			return false;
		}
	}
	var bool=true;
	function colorbox(html){
		$.colorbox({
            transition: "none",
            opacity: 0.5,
            html:html,
            fixed: true,
            closeButton: false,
            onOpen: function() {
                window.scroll_top = $(document).scrollTop();
            },
            onComplete: function() {
            	setTimeout(function(){
            		bool=true;
            		$("#colorbox,#cboxOverlay").remove();
				},1000);
            },
            onClosed: function() {
                window.scrollTo(0, window.scroll_top);
            }
        });
	};
	var json={},arr=[];
	function formclass(obj,fn){fn(obj.length)};
    //checked??????name
    $("[data-elementtype=formRadio],[data-elementtype=formCheckbox]").each(function(n,ele){
        if(!$(this).find('input').attr('name')){
            $(this).find('input').attr('name',$(this).attr('elementid'));
        }
    });
	$(".wqdControlFormEl7 a").on("click",function(){
		//???????????????????????? return
		if(window.parent != window.self) return;

		var html,elementid=$(this).parents(".wqdelementEdit.elementsContainer").attr("elementid");
		if(getCookie("getCookie",elementid)){
			html="<div class='colbox'><i class='tag success'></i><div class='point success'><h2>???????????????</h2></div></div>";
			if(bool){
				bool=false;
				colorbox(html);
			}
			setCookie("getCookie",elementid);
		}else{
			if(parseInt(document.cookie.split(('clickn'+elementid)+'=')[1].split(';')[0])<10){
				document.cookie=('clickn'+elementid)+'='+(parseInt(document.cookie.split('clickn'+elementid+'=')[1].split(';')[0])+1);
				html="<div class='colbox'><i class='tag success'></i><div class='point success'><h2>???????????????</h2></div></div>";
				if(bool){
					bool=false;
					colorbox(html);
				}
			}else{
				html="<div class='colbox'><i class='tag error'></i><div class='point error'><h2>???????????????</h2><p>??????????????????10???</p></div></div>";
				if(bool){
					bool=false;
					colorbox(html);
				}
                window.location.reload();
				return;
			}
		}
		var obj=$(this);
		var form=obj.parents('[data-elementtype=formButton]').parent();
		if(obj.children().html()=='????????????') return false;
        
		formclass(form.find("[data-elementtype=formInput]"),//?????????
			function(n){
				for(var i=0;i<n;i++){
					(function(m){
						var a={};
						var ele=form.find("[data-elementtype=formInput]").eq(m);
						var titie=ele.attr("wqdformtitle").replace('"','???').replace("'","???");
						a[titie]=ele.find("input").val().replace('"','???').replace("'","???");
                        var id=ele.attr('id');
                        //arr.push(a);
						arr.push([a,id]);
					})(i);
				}
			}
		);
		formclass(form.find("[data-elementtype=formTextarea]"),//?????????
			function(n){
				for(var i=0;i<n;i++){
					(function(m){
						var a={};
						var ele=form.find("[data-elementtype=formTextarea]").eq(m);
						var titie=ele.attr("wqdformtitle").replace('"','???').replace("'","???");
						a[titie]=ele.find("textarea").val().replace('"','???').replace("'","???");
                        var id=ele.attr('id');
                        //arr.push(a);
						arr.push([a,id]);
					})(i);
				}
			}
		);
		formclass(form.find("[data-elementtype=formSelect]"),//?????????
			function(n){
				for(var i=0;i<n;i++){
					(function(m){
						var a={};
						var ele=form.find("[data-elementtype=formSelect]").eq(m);
						var titie=ele.attr("wqdformtitle").replace('"','???').replace("'","???");
						a[titie]=ele.find("ul").find("li.on span").html().replace('"','???').replace("'","???");
                        var id=ele.attr('id');
                        //arr.push(a);
						arr.push([a,id]);
					})(i);
				}
			}
		);
		formclass(form.find("[data-elementtype=formDate]"),//??????
			function(n){
				for(var i=0;i<n;i++){
					(function(m){
						var a={};
						var ele=form.find("[data-elementtype=formDate]").eq(m);
						var titie=ele.attr("wqdformtitle").replace('"','???').replace("'","???");
						a[titie]=ele.find("input").val().replace('"','???').replace("'","???");
                        var id=ele.attr('id');
                        //arr.push(a);
						arr.push([a,id]);
					})(i);
				}
			}
		);
		formclass(form.find("[data-elementtype=formRadio]"),//?????????
			function(n){
				for(var i=0;i<n;i++){
					(function(m){
						var radio=form.find("[data-elementtype=formRadio]").eq(m).find("i");
						if(radio.hasClass("on")) {
							var a={};
							var ele=form.find("[data-elementtype=formRadio]").eq(m);
							var titie=ele.attr("wqdformtitle").replace('"','???').replace("'","???");
							a[titie]=ele.find("label").html().replace('"','???').replace("'","???");
                           var id=ele.find('input').attr('name') || ele.attr("id");
							//arr.push(a);
                            arr.push([a,id]);
						}
					})(i);
				}
			}
		);
		/////////////////////////////////////////////////?????????
		var formCheckbox=form.find("[data-elementtype=formCheckbox]");
		var checkboxlength=formCheckbox.length;
		var checkJson={};
		var checkarr=[];
		for(var i=0;i<checkboxlength;i++){
			(function(n){
				//checkJson[formCheckbox.eq(n).attr("id")]=1;
               checkJson[formCheckbox.eq(n).find('input').attr("name")]=1;
			})(i);
		}
        //console.log(checkboxlength);
		function getJsonLength(json){
			var n = 0;
			for(var item in json){
				checkarr.push(item);
				n++;
			}
			return n;
		}
		getJsonLength(checkJson);
		var checkArrLength=checkarr.length;
        formclass(form.find("[data-elementtype=formCheckbox]"), 
            function(n){
                for(var i=0;i<checkarr.length;i++){
                    (function(m){
                        var html=[];
                        form.find("[name="+checkarr[m]+"]").each(function(index,ele){
                            if($(this).prev("i").hasClass("on")) html.push($(this).siblings("label").html().replace('"','???').replace("'","???"));
                        });
                        var a={};
                        var elea=form.find("[name="+checkarr[m]+"]").parents('[data-elementtype=formCheckbox]');
                        var titie=elea.attr("wqdformtitle").replace('"','???').replace("'","???");
                        var id=elea.attr('id');
                        a[titie]=html.join(",");
                        //arr.push(a);
                        arr.push([a,id]);
                        //html="";
                    })(i);    
                }  
            }
        );
		//????????????
        var newArrsort = [], newArrString = [];
        form.children().each(function(){
        	var elemId = $(this).attr("id");
        	for(var k=0; k<arr.length; k++){
        		if($(this).attr("data-elementtype")=="formRadio"||$(this).attr("data-elementtype")=="formCheckbox"){
        			if($(this).find("input").attr("name") == form.find("#"+arr[k][1]).find("input").attr("name")){
        				var exist = true;
	        			for(var p=0; p<newArrsort.length; p++){
	        				if(arr[k][1] == newArrsort[p][1]){
	        					exist = false;
	        					break;
	        				}
	        			}
	        			exist && newArrsort.push(arr[k]);
        			}
        		}else{ 
        			if(arr[k][1] == elemId){
        				newArrsort.push(arr[k]);
        			}
        		}
        	}
        });
        var sortId = [];
		for(var i=0;i<newArrsort.length;i++){
			var valString = '"'+newArrsort[i][1]+'":'+JSON.stringify(newArrsort[i][0]);
			newArrString.push(valString);
			sortId.push(newArrsort[i][1]);
		}
		var name = obj.parents('[data-elementtype=formButton]').parents("[data-elementtype=form]").attr("wqdFormName") || '??????',
			//sectionId = $(this).parents("section").parent().attr("id"),
			sectionId =$(this).parents(".wqdelementEdit.elementsContainer").attr("elementid"),
			value = "{" + newArrString.join(",") + "}";
		$.ajax({
			url: '/fixed/form/submitForm',
			dateType:'json',
			type:'post', 
			data : {'name':name,'sectionId':sectionId,'value':value,'fieldOrder':sortId.join(",")},
			success:function(data, status) { 
				if(data.status==200){ 
					var text = obj.children().html();
					obj.children().html('????????????');
					setTimeout(function(){
						obj.children().html(text);
                       window.location.reload();
					},1000);
					arr.length=0;
				}
			},
			error : function(){
			}
		});
	});
	$("body").on("click",".date_selector .selectable_day",function(){
		$(this).parents(".date_selector").hide();
	});
	$("body").on("click",".wqd-form-element.wqdControlFormEl1 input",function(e){
		e.stopPropagation();
	})
});
//})(jQuery);
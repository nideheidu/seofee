$(function() {
	var commodityList = {};

	commodityList.init = function(){
		this.commonInit();
		this.outsideEvent();
	};
	commodityList.commonInit = function(){
		$(document).on("mouseenter",".categoryFirst > li > a",function(){
			$(this).parent().siblings("li").find(".categorySecond").hide();
			$(this).siblings(".categorySecond").show();
		}).on("mouseleave",".categoryFirst > li",function(){
			$(this).find(".categorySecond").hide();
		}).on("mouseenter",".categorySecond > li > a",function(){
			if(!$(this).siblings(".categoryThird").length) return;
			var li = $(this).parent(),
				third = $(this).siblings(".categoryThird").css("left","100%"),
				left = -(third.width()||0);
			li.offset().left + li.outerWidth() + third.outerWidth() > $(window).width() && third.css("left",left);
			//li.index() != 0 && third.find("li").length > li.index() && third.css("top",-li.position().top);
			li.siblings("li").find(".categoryThird").hide();
			third.show();
		}).on("mouseleave",".categorySecond > li",function(){
			$(this).find(".categoryThird").hide();
		});

		this.updataModity();	//????????????
	}
	//?????????
	commodityList.outsideEvent = function(){
		//??????????????????
		$(document).on("click",".modityCategory a",function(e){
			//if($(this).hasClass("active")) return;
			var contactId = $(this).parents(".wqdcommodityCategory").attr("wqdmark") || "ssss",
				modityDom = $(".wqdcommodityList[wqdmark="+contactId+"]"),
				stype = modityDom.attr("sorttype") || "time",
				USERID = modityDom.attr("userid") || "",
				categoryid = $(this).attr("data-category") || (modityDom.attr("data-categoryids") ? modityDom.attr("data-categoryids") : "");
			commodityList.requestDate("/fixed/item/getItems",{userId:USERID,categoryId:categoryid,sortType:stype},function(data){
				modityDom.data("cacheData",data.data);	//????????????
				data.status == 200 && commodityList.dataInit.call(modityDom,data.data);
			});
			$(this).parents(".wqdcommodityCategory").find(".modityCategory a").removeClass("active");
			$(this).addClass("active");
			$(this).parents(".categoryThird").siblings("a").addClass("active");
			$(this).parents(".categorySecond").siblings("a").addClass("active");
			/* ??????????????????????????????  ?????????  ??????????????????????????? ?????????  ??????  loadSearch*()  ??????*/
			if($(this).parents('.categorySecond').length) {
				// ??????????????????????????????????????????
				var wqdcommodityAdvanceSearch = $(this).parents('.wqdcommodityCategory').siblings('.wqdcommodityAdvanceSearch[wqdmark="'+contactId+'"]');
				/* ??????????????????????????? ???????????? */	
				if(!$(this).parents(".wqdcommodityCategory").hasClass('advance-search')) {
					return false;
				}
				// ?????????????????????
				var dataJSON = {};
				dataJSON.firstId = $(this).parents('.categorySecond').prev().data('category');
				dataJSON.firstVal = $(this).parents('.categorySecond').prev().text();
				dataJSON.secondId = $(this).data('category');
				dataJSON.secondVal =  $(this).text();
				advSearch.init(wqdcommodityAdvanceSearch, dataJSON);
				/**
				 * @param  id  ?????????????????????????????????id  
				 * @param  obj  ????????????????????????
				 * @param  string  ????????????????????????mark??????
				 * */
				advSearch.loadSearch($(this).data('category'), wqdcommodityAdvanceSearch, contactId);
			}
		}).on("click",".wqdcommodityList .admireMark svg",function(event){		//??????
			return;  //??????????????????????????????????????????
			event.stopPropagation();
			event.preventDefault();
			if($(this).parent().hasClass("active")) return;
			var that = $(this),
				modityId = that.parents(".commodityWrap").parent().attr("data-modity") || "!!!!!!";
			$.ajax({
	            type: "post",
	            url: "/fixed/item/review",
	            data : {modityId:modityId},
	            dataType: "json",
	            success:function(data){
	            	if(data.status == 200){
	            		that.parent().addClass("active").end().siblings("span").text(that.siblings("span").text()-0+1);
	            		commodityList.setCookie(modityId,"admire");
	            	}else{
	            		alert(data.msg)
	            	}
	            }
	        });
		}).on("click",".pagingWrap .pagingBtn, .pagingWrap .pagingList li",function(){	//??????
			if($(this).hasClass("active")) return;
			var parent = $(this).parents(".pagingWrap"),
				pagenum = null,
				index = parent.find(".pagingList li.active").text()-1,
				maxVal = parent.find(".pagingList li:last-child").text()-0,
				modityDom = $(this).parents(".wqdcommodityList");
			if($(this).hasClass("firstPage")){	//??????
				if(index == 0) return;
				pagenum = 0;
				commodityList.caeatePaging.call(parent,1,1,maxVal);
			}else if($(this).hasClass("prevPage")){		//?????????
				if(index == 0) return;
				pagenum = index - 1;
				if(parent.find(".pagingList li:first-child").hasClass("active")){
					commodityList.caeatePaging.call(parent,index,index,maxVal);
				}else{
					parent.find(".pagingList li.active").prev().addClass("active").siblings().removeClass("active");
				}
			}else if($(this).hasClass("nextPage")){		//?????????
				if(parent.find(".pagingList li:last-child").hasClass("active")) return;
				pagenum = index + 1;
				if(parent.find(".pagingList li.active").next().text()=="..."){
					var star = maxVal-pagenum>6 ? pagenum+1 : maxVal-6;
					commodityList.caeatePaging.call(parent,star,pagenum+1,maxVal);
				}else{
					parent.find(".pagingList li.active").next().addClass("active").siblings().removeClass("active");
				}
			}else if($(this).hasClass("lastPage")){		//??????
				if(parent.find(".pagingList li:last-child").hasClass("active")) return;
				pagenum = parent.find(".pagingList li:last-child").text()-1;
				var star = maxVal>6 ? maxVal-6 : 1;
				commodityList.caeatePaging.call(parent,star,maxVal,maxVal);
			}else{
				if(parent.find(".pagingList li:last-child").text()-parent.find(".pagingList li:first-child").text()>6){
					var html = "";
					if($(this).index() == 5){
						pagenum = $(this).prev().text() - 0;
						var star = maxVal-$(this).prev().text()>6 ? pagenum + 1 : maxVal-6;
						commodityList.caeatePaging.call(parent,star,pagenum+1,maxVal);
					}else if($(this).index() == 6){
						pagenum = maxVal - 1;
						commodityList.caeatePaging.call(parent,maxVal-6,maxVal,maxVal);
					}else{
						pagenum = $(this).text() - 1;
						$(this).addClass("active").siblings().removeClass("active");
					}
				}else{
					pagenum = $(this).text() - 1;
					$(this).addClass("active").siblings().removeClass("active");
				}
				
			}
			var cacheData = modityDom.data("cacheData") || [];
				dataStyle = modityDom.attr("data-style") || "",
				column = commodityList.getParam(dataStyle,"modity-column") || 4,
				row = commodityList.getParam(dataStyle,"modity-row") || 1,
				star = row*column*pagenum,
				dataArr = cacheData.slice(star);
			commodityList.dataInit.call(modityDom,dataArr,true);
		});	
	};
	//????????????
	commodityList.caeatePaging = function(star,active,maxVal){
		var html = "";
		for(var k=star; k<star+7 && k<=maxVal; k++){
			if(k == active){
				html += '<li class="hoverBtn active">'+k+'</li>';
			}else if(k == star+5 && maxVal-star > 6){
				html += '<li class="hoverBtn">...</li>';
			}else if(k == star+6 && maxVal-star > 6){
				html += '<li class="hoverBtn">'+maxVal+'</li>';
			}else{
				html += '<li class="hoverBtn">'+k+'</li>';
			}
		}
		$(this).find(".pagingList").html(html);
	};
	commodityList.requestDate = function(url,data,callback) {
		$.ajax({
            type: "GET",
            url: url,
            data : data,
            dataType: "json",
            success:callback
        });
	};
	//????????????
	commodityList.updataModity = function(){
		$(".wqdcommodityCategory").each(function(){
			var that = $(this),
				contactId = $(this).attr("wqdmark") || "ssss",
				USERID = $(".wqdcommodityList[wqdmark="+contactId+"]").attr("userid") || "",
				categoryIds = $(".wqdcommodityList[wqdmark="+contactId+"]").attr("data-categoryIds") || "";
			commodityList.requestDate("/fixed/item/getAllCategory",{userId:USERID},function(data){
				//commodityList.categoryData = data.data;		//????????????
				data.status == 200 && commodityList.categoryInit.call(that,commodityList.getCatetoryDate(data.data, categoryIds));
			});
		});
		$(".wqdcommodityList").each(function(){
			var that = $(this),
				USERID = that.attr("userid") || "",
				stype = $(this).attr("sorttype") || "time",
				categoryIds = that.attr("data-categoryIds") || "";
			commodityList.requestDate("/fixed/item/getItems",{userId:USERID,categoryId:categoryIds,sortType:stype},function(data){
				that.data("cacheData",data.data);	//????????????
				data.status == 200 && commodityList.dataInit.call(that,data.data);
			});
		});
	};
	//????????????
	commodityList.categoryInit = function(data){
		var html = "";
		if(data && data.length && data[0]){
			html += '<div class="categoryAll"><a href="javascript:void(0);" class="active">????????????</a></div><ul class="categoryFirst">';
			for(var i=0; i<data.length; i++){
				html += '<li><a href="javascript:void(0);" data-category='+data[i].categoryId+'>'+data[i].name+'</a>';
				if(data[i].child && data[i].child.length){
					html += '<ul class="categorySecond">';
					for(var j=0; j<data[i].child.length; j++){
						html += '<li><a href="javascript:void(0);" data-category='+data[i].child[j].categoryId+'>'+data[i].child[j].name+'</a>';
						if(data[i].child[j].child && data[i].child[j].child.length){
							html += '<ol class="categoryThird">';
							for(var k=0; k<data[i].child[j].child.length; k++){
								html += '<li><a href="javascript:void(0);" data-category='+data[i].child[j].child[k].categoryId+'>'+data[i].child[j].child[k].name+'</a></li>';
							}
							html += '</ol>';
						}
						html += '</li>';
					}
					html += '</ul>';
				}
				html += '</li>';
			}
			html += '</ul>';
		}
		$(this).find(".modityCategory").html(html);
	};
	//??????????????????
	commodityList.dataInit = function(data,bool){
		var pageNum = 0,
			USERID = $(this).attr("userid") || "",
			pageId = $(this).attr("data-pageid") || "",
			modityStr = "",
			pagingStr = "",
			dataStyle = $(this).attr("data-style") || "",
			column = parseInt(commodityList.getParam(dataStyle,"modity-column") || 4),
			row = parseInt(commodityList.getParam(dataStyle,"modity-row") || 1);
		if(data && data.length && data[0]){
			pageNum = parseInt(data.length/(column*row));
			pageNum = data.length % (column*row) == 0 ? pageNum : pageNum+1;
			for(var i=0; i<data.length && i<column*row; i++){
				i % column == 0 && i != 0 && (modityStr += '</ol></li>');
				var parentStr = i % column == 0 ? '<li><ol class="list_row">' : "",
					modityCookie = commodityList.getCookie(data[i].id),
					act_class = modityCookie ? "" : "",	//???????????????????????????????????????????????????
					_blank = data[i].widowsType=="news" ? ' target="_blank"' : "",
					hrefUrl = pageId ? "pageItem_"+pageId+"_"+data[i].id+".html?catch="+USERID : "javascript:;";
					hrefUrl =  data[i].detailsType=="external" ? data[i].itemLink : hrefUrl;
				modityStr += parentStr+'<li data-modity="'+data[i].id+'"><a'+_blank+' href="'+hrefUrl+'" class="commodityWrap">';
				modityStr += '<div class="modityImg"><img src="' + CSSURLPATH+data[i].picPath.split(",")[0] +'"></div>';
				modityStr += '<div class="textMessage"><p class="moditDescribe">'+data[i].name+'</p>';
				modityStr += '<div class="modityMark clearfix"><div class="priceMark">';
				modityStr += '<div class="nowPrice commomPrice">??<span>'+data[i].currentPrice+'</span></div>';
				modityStr += '<div class="originalPrice commomPrice">??<span>'+data[i].originalPrice+'</span></div></div>';
				modityStr += '<div class="countMark"><div class="admireMark'+act_class+'"><svg class="admireSvg" viewBox="-250 -250 2392 2392" xmlns="http://www.w3.org/2000/svg"><path fill="#999" d="M1664 596q0-81-21.5-143t-55-98.5-81.5-59.5-94-31-98-8-112 25.5-110.5 64-86.5 72-60 61.5q-18 22-49 22t-49-22q-24-28-60-61.5t-86.5-72-110.5-64-112-25.5-98 8-94 31-81.5 59.5-55 98.5-21.5 143q0 168 187 355l581 560 580-559q188-188 188-356zm128 0q0 221-229 450l-623 600q-18 18-44 18t-44-18l-624-602q-10-8-27.5-26t-55.5-65.5-68-97.5-53.5-121-23.5-138q0-220 127-344t351-124q62 0 126.5 21.5t120 58 95.5 68.5 76 68q36-36 76-68t95.5-68.5 120-58 126.5-21.5q224 0 351 124t127 344z"></path></svg>';
				modityStr += '<span>'+data[i].favorable+'</span></div><div class="salesMark">?????????<span>'+data[i].salesVolume+'</span>???</div></div></div></div></a></li>';
			}
			modityStr += '</ol></li>';
			if(!bool){
				//??????
				pagingStr += '<a class="pagingSize hoverBtn pagingBtn firstPage" href="javascript:void(0);">??????<svg class="pageSvg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 74 60" enable-background="new 0 0 74 60" xml:space="preserve"><path fill="#666666" d="M40.6,26.1c-1.1,1.1-1.7,2.5-1.7,3.9c0,1.4,0.6,2.9,1.7,3.9c0,0,20.4,21.4,22.5,23.5c2.1,2,5.8,2.2,8.1,0c2.2-2.2,2.4-5.2,0-7.9L52.4,30l18.7-19.5c2.4-2.7,2.2-5.7,0-7.9c-2.2-2.2-6-2-8.1,0C61,4.6,40.6,26.1,40.6,26.1z M1.1,30c0,1.4,0.6,2.9,1.7,3.9c0,0,20.4,21.4,22.5,23.5c2.1,2,5.8,2.2,8.1,0c2.2-2.2,2.4-5.2,0-7.9L14.6,30l18.7-19.5c2.4-2.7,2.2-5.7,0-7.9c-2.2-2.2-6-2-8.1,0c-2.1,2-22.5,23.5-22.5,23.5C1.7,27.1,1.1,28.6,1.1,30z"/></svg></a>';
				pagingStr += '<a class="pagingSize hoverBtn pagingBtn prevPage" href="javascript:void(0);">?????????<svg class="pageSvg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 36 60" enable-background="new 0 0 36 60" xml:space="preserve"><path fill="#666666" d="M25.2,2.6c-2.1,2-22.5,23.5-22.5,23.5C1.6,27.1,1,28.6,1,30c0,1.4,0.6,2.9,1.7,3.9c0,0,20.4,21.4,22.5,23.5c2.1,2,5.8,2.2,8.1,0c2.2-2.2,2.4-5.2,0-7.9L14.5,30l18.7-19.5c2.4-2.7,2.2-5.7,0-7.9C31,0.4,27.3,0.5,25.2,2.6z"/></svg></a>';
				pagingStr += '<ol class="pagingSize pagingList" pagenum="'+pageNum+'">';
				for(var k=1; k<=pageNum && k<=7; k++){
					var num = k;
					if(k==6){
						num = pageNum > 7 ? "..." : 6; 
					}else if(k==7){
						num = pageNum;
					}
					pagingStr += '<li class="hoverBtn">'+num+'</li>';
				}
				pagingStr += '</ol><a class="pagingSize hoverBtn pagingBtn nextPage" href="javascript:void(0);">?????????<svg class="pageSvg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 34 58" enable-background="new 0 0 34 58" xml:space="preserve"><path fill="#666666" d="M10.2,54.8c2-1.9,21.3-22.2,21.3-22.2c1-0.9,1.6-2.4,1.6-3.7s-0.6-2.7-1.6-3.7c0,0-19.3-20.2-21.3-22.2c-2-1.9-5.5-2.1-7.7,0c-2.1,2.1-2.3,4.9,0,7.5L20.3,29L2.7,47.4c-2.3,2.5-2.1,5.4,0,7.5S8.2,56.8,10.2,54.8z"/></svg></a>';
				pagingStr += '<a class="pagingSize hoverBtn pagingBtn lastPage" href="javascript:void(0);">??????<svg class="pageSvg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 74 60" enable-background="new 0 0 74 60" xml:space="preserve"><path fill="#666666" d="M10.9,2.6c-2.1-2-5.8-2.2-8.1,0c-2.2,2.2-2.4,5.2,0,7.9L21.6,30L2.9,49.5c-2.4,2.7-2.2,5.7,0,7.9c2.2,2.2,6,2,8.1,0c2.1-2,22.5-23.5,22.5-23.5c1.1-1.1,1.7-2.5,1.7-3.9c0-1.4-0.6-2.9-1.7-3.9C33.4,26.1,13,4.6,10.9,2.6zM71.2,26.1c0,0-20.4-21.4-22.5-23.5c-2.1-2-5.8-2.2-8.1,0c-2.2,2.2-2.4,5.2,0,7.9L59.4,30L40.6,49.5c-2.4,2.7-2.2,5.7,0,7.9c2.2,2.2,6,2,8.1,0c2.1-2,22.5-23.5,22.5-23.5c1.1-1.1,1.7-2.5,1.7-3.9C72.9,28.6,72.3,27.1,71.2,26.1z"/></svg></a>';
			}
				
		}else{
			modityStr = '<p class="nomodityHint"><strong>??????????????????????????????</strong></p>';
		}
		$(this).find(".commodityList").html(modityStr);
		!bool && $(this).find(".pagingWrap").html(pagingStr).find(".pagingList li:first-child").addClass("active");
		
		commodityList.updateboxHeight($(this));
	};
	//????????????
	commodityList.getParam = function (objString,name) {
		if(!objString || !name) return "";
		var paramVal = "",
			paramObj = {};
        $.each(objString.split(";"),function (i,_) {
            if(!_) return true;
            var key = _.split(":");
            paramObj[key[0]] = key[1];
        });
        paramVal = paramObj[name] || "";
        return paramVal;
    };
    //????????????????????????
    commodityList.updateboxHeight = function (dom) {
    	var scale = 3/4, 
    		width = dom.find(".commodityWrap .modityImg").width(),
    		height = width*scale;
    	dom.find(".commodityWrap .modityImg").height(height);
    };
    //????????????id?????????????????????
    commodityList.getCatetoryDate = function(dataArr,categoryIds){
    	var newArr = [];
    	if(categoryIds){
			categoryIds = categoryIds.split(",");
			// $.map(dataArr,function(_i){
			// 	for(var k=0; k<categoryIds.length; k++){
			// 		if(_i.categoryId==categoryIds[k]){
			// 			newArr.push(_i);
			// 			break;
			// 		}
			// 	}	
			// });
			/* ??????????????????????????????????????? */
            for(var k=0; k<categoryIds.length; k++) {
                $.map(dataArr, function (_i) {
                    if(categoryIds[k] == _i.categoryId) {
                        newArr.push(_i);
                        // return false;
                    }
                });
            }
			return newArr;
		}else{
			return dataArr;
		}
    };
    //??????cookie
    commodityList.setCookie = function(key, value, expireDays){
        var date=new Date();
        expireDays = expireDays || 7;
        date.setTime(date.getTime()+expireDays*24*3600*1000);
        document.cookie = key + "=" + escape(value)+";expires="+date.toGMTString()+";path=/";
    }
    //??????cookie
    commodityList.getCookie = function(name){
        var strCookie=document.cookie,
            arrCookie=strCookie.split(";");

        for(var i=0;i<arrCookie.length;i++){
            var arr=arrCookie[i].split("=");
            arr[0] = arr[0].replace(/\s/,"");
            if(arr[0]==name) {
                return arr[1];
            }
        }
        return "";
    }
	commodityList.init();
	/* ?????? ???????????? auth:lx */
	var advSearch = {};

	advSearch.commonInit = function ($obj) {
		// ?????????????????????????????? ????????????
		$(document).off('click.close-this').on('click.close-this', function (e) {
			if($(e.target).parents('.last-nav').length) {
				return;
			}
			if($(e.target).parents('.switch').length) {
				return;
			}
			if(!$(e.target).parents('.wqdcommodityAdvanceSearch').length) {
				$obj.hide();
				$(document).off('click.close-this');
			}
		});
	};
	
	/**
	 * @author Lx
	 * ??????????????????
	 * @param {[obj]} ????????????????????????
	 * @param {[Obeject]} data ???????????????????????????
	 */
	advSearch.init = function ($obj,data) {
		advSearch.commonInit($obj)
		// ????????????  ????????????????????????
		// ????????????????????????
		$obj.css({
			'left':'0',
			'top':'0'
		})
		$obj.find('.pc-container-box .pc-nav-bar .first').attr('data-categoryid',data.firstId);
		$obj.find('.pc-container-box .pc-nav-bar .first').text(data.firstVal)
		$obj.find('.pc-container-box .pc-nav-bar .second').attr('data-categoryid',data.secondId);
		$obj.find('.pc-container-box .pc-nav-bar .second').text(data.secondVal)
		$obj.find('.pc-container-box .need2show').hide();
		$obj.find('.pc-container-box .last-nav').length && $obj.find('.pc-container-box .last-nav').remove();
	}
	/* ???????????? */
	/**
	 * @param {[string]}  categoryId  ????????????????????????????????????id???
	 * @param {[Object]}  $_obj  ??????????????????
	 * @param {[string]}  markId ??????????????????markId???
	 */
	advSearch.loadSearch = function (categoryId, $_obj, markId) {
		var self = this;
		var markId = markId;
		var str='';
		/* ajax?????? */
 		$.ajax({
			url : '/fixed/item/getItemSearchCriteria',
			type: 'GET',
			data: {
				userId:  $('.wqdcommodityList[wqdmark="'+markId+'"]').attr('userid'),
				categoryId: categoryId
			},
			beforeSend: function (xhr) {
				xhr.withCredentials = true;
				xhr.setRequestHeader("Content-Type", "application/json");
			},
			success : function (data, status) {
				if(status == 'success') {
					var str = '';
					/* ????????????????????????  ????????? ????????? */
					if(!data.data.itemProperty.length) {
						return false;
					}
					$.each(data.data.itemProperty, function (i, _) {
						str += ['<div class="type-item" data-flag="'+i+'">',
							'<div class="type-name">'+_.name+'</div>',
							'<div class="type-content">',
							getStr(_.values),
							'</div>',
							'<div class="operation">??????',
								'<i class="switch"></i>',
								'<svg class="icon" viewBox=" 0 0 1026 1024 " style="width:10px;height:10px;">',
									'<path d="M1018.701913 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0L516.563478 726.416696 45.924174 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0s-8.704 22.77287 0 31.47687l486.4 486.4c4.34087 4.34087 10.039652 6.522435 15.738435 6.522435s11.397565-2.181565 15.738435-6.522435l486.4-486.4C1027.383652 278.550261 1027.383652 264.45913 1018.701913 255.75513z "></path>',
								'</svg>',
							'</div>',
						'</div>',].join('');
					});
					/* ??????????????? */
					function getStr(arr) {
						var strHtml = '';
						for(var i = 0; i< arr.length; i++) {
							strHtml += '<span data-valid="'+arr[i].id+'">'+arr[i].name+'</span>'
						}
						return strHtml;
					}
					// ??????????????? ????????? ????????????
					$_obj.find('.type-box').empty().append(str);
					// ???????????????????????????
					$_obj.find('.pc-container-box').css('position','relative')
					$_obj.find('.pc-container-box').find('.more-choose').length && $_obj.find('.pc-container-box').find('.more-choose').remove();
					$_obj.find('.type-box-group').after('<div class="more-choose">??????<i class="switch"> <svg class="icon" viewBox="0 0 1026 1024" style="width:12px;height:12px;"> <path d="M1018.701913 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0L516.563478 726.416696 45.924174 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0s-8.704 22.77287 0 31.47687l486.4 486.4c4.34087 4.34087 10.039652 6.522435 15.738435 6.522435s11.397565-2.181565 15.738435-6.522435l486.4-486.4C1027.383652 278.550261 1027.383652 264.45913 1018.701913 255.75513z" /> </svg> </i> </div></div>');
					// ??????????????????????????????????????? ?????????????????? ???????????????
					$_obj.css({
						'top': $_obj.siblings('.wqdcommodityCategory[wqdmark="'+markId+'"]').offset().top - $_obj.parent().offset().top + $_obj.siblings('.wqdcommodityCategory[wqdmark="'+markId+'"]').height() + 'px',
						'left': $_obj.siblings('.wqdcommodityCategory[wqdmark="'+markId+'"]').css('left'),
						'height':4*41 + $_obj.find('.more-choose').height() + 35 + $_obj.find('.type-item').length*1 +6+'px',
					});
					// $_obj.parents('.wqdelementEditBox, .wqdelementEdit .wqdelementContent')
					$_obj.find('.type-item:last').css('border-bottom-style','solid');
					$_obj.show();
					$_obj.find('.type-box-group').show();
					$_obj.find('.nano').nanoScroller({alwaysVisible:true});
					// ???????????????????????????????????????
					self.bindEvent($_obj, data.data.categoryId, markId);
				}
			}
		});
	}

	/**
	 * ???????????????????????????????????????
	 * @param {[Object]} $obj ??????????????????????????????  ?????? 
	 * @param {[string]} categoryId ?????????????????????????????????id
	 * @param {[string]}  markId ??????????????????markId???
	 */
	advSearch.bindEvent = function ($obj, categoryId, markId) {
		/* ?????? ?????? ?????? */
		$obj.off('click.operation').on('click.operation', '.operation', function () {
			if($(this).hasClass('on')) {
				$(this).removeClass('on')
				$(this).prev().css('overflow','hidden'); 
				$(this).siblings('.type-name').css('height','41px');
				// ????????????
				$(this).find('.icon').css({
					'transform':'rotate(0deg)',
					'-ms-transform':'rotate(0deg)', 	/* IE 9 */
					'-moz-transform':'rotate(0deg)', 	/* Firefox */
					'-webkit-transform':'rotate(0deg)', /* Safari ??? Chrome */
					'-o-transform':'rotate(0deg)', 	/* Opera */
				});
			} else {
				$(this).addClass('on')
				$(this).prev().css('overflow','visible'); 
				$(this).siblings('.type-name').css('height',Math.floor($(this).parents('.type-item').height() / 40)*41 + 'px');
				// ????????????
				$(this).find('.icon').css({
					'transform':'rotate(180deg)',
					'-ms-transform':'rotate(180deg)', 	/* IE 9 */
					'-moz-transform':'rotate(180deg)', 	/* Firefox */
					'-webkit-transform':'rotate(180deg)', /* Safari ??? Chrome */
					'-o-transform':'rotate(180deg)', 	/* Opera */
				})
			}
		});
		/* ??????????????????  ?????????????????? */
		$obj.off('click.closeAll').on('click.closeAll', '.pc-nav-bar .closeAll', function () {
			$obj.hide();
		});
		/* ??????????????????  */
		$obj.off('click.more-choose').on('click.more-choose', '.pc-container-box .more-choose', function () {
			if($(this).hasClass('on')) {
				$(this).removeClass('on')
				// ??????????????????type-box-group??????????????????  ????????????  ???????????????
				$obj.find('.type-box-group').show();
				$(this).html('??????<i class="switch"> <svg class="icon" viewBox="0 0 1026 1024" style="width:12px;height:12px;"> <path d="M1018.701913 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0L516.563478 726.416696 45.924174 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0s-8.704 22.77287 0 31.47687l486.4 486.4c4.34087 4.34087 10.039652 6.522435 15.738435 6.522435s11.397565-2.181565 15.738435-6.522435l486.4-486.4C1027.383652 278.550261 1027.383652 264.45913 1018.701913 255.75513z"></path> </svg> </i>');
				// ????????????
				$(this).find('.switch').css({
					'transform':'rotate(180deg)',
					'-ms-transform':'rotate(180deg)', 	/* IE 9 */
					'-moz-transform':'rotate(180deg)', 	/* Firefox */
					'-webkit-transform':'rotate(180deg)', /* Safari ??? Chrome */
					'-o-transform':'rotate(180deg)', 	/* Opera */
				});
			} else {
				$(this).addClass('on');
				$obj.find('.type-box-group').hide();
				$(this).html('??????<i class="switch"> <svg class="icon" viewBox="0 0 1026 1024" style="width:12px;height:12px;"> <path d="M1018.701913 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0L516.563478 726.416696 45.924174 255.75513c-8.704-8.704-22.77287-8.704-31.47687 0s-8.704 22.77287 0 31.47687l486.4 486.4c4.34087 4.34087 10.039652 6.522435 15.738435 6.522435s11.397565-2.181565 15.738435-6.522435l486.4-486.4C1027.383652 278.550261 1027.383652 264.45913 1018.701913 255.75513z"></path> </svg> </i>');
				// ????????????
				$(this).find('.switch').css({
					'transform':'rotate(0deg)',
					'-ms-transform':'rotate(0deg)', 	/* IE 9 */
					'-moz-transform':'rotate(0deg)', 	/* Firefox */
					'-webkit-transform':'rotate(0deg)', /* Safari ??? Chrome */
					'-o-transform':'rotate(0deg)', 	/* Opera */
				});
			}
		});
		/* ?????? ????????????????????? */
		$obj.off('click.choice').on('click.choice', '.type-content span', function () {
			// ???????????????/???  ???  ?????????/???
			var attr = $(this).parent().siblings('.type-name').text();
			var val = $(this).text();
			// ????????????
			$obj.find('.pc-nav-bar').append('<span data-id="'+$(this).data('valid')+'" data-flag="'+$(this).parents('.type-item').data('flag')+'" class="last-nav">'+attr + '/' + val+'<i class="icon-close"> <svg class="icon" viewBox="0 0 1024 1024" style="width:12px;height:12px;" fill="#e31436"> <path d="M971.292568 77.731362 945.967786 52.40658 511.698125 486.676242 77.429487 52.40658 52.104705 77.731362 486.373343 512.001023 52.104705 946.269661 77.429487 971.594443 511.698125 537.325805 945.967786 971.594443 971.292568 946.269661 537.022906 512.001023Z"></path> </svg> </i> </span>')
			$obj.find('.pc-nav-bar .need2show').show();
			// advSearch.renderShopList($(this).data('categoryId'))
			// ????????????????????????????????????????????????
			$(this).parents('.type-item').hide();
			// ???????????????????????????id
			var idArr = [];
			$.each($obj.find('.pc-nav-bar .last-nav'), function (i, v) {
				idArr.push($(v).data('id'));
			});
			// ??????????????????
			advSearch.renderShopList(categoryId, idArr, markId);
			resetStyle($obj);
		});
		/* ??????????????????????????????????????????????????? */
		$obj.off('click.close').on('click.close', '.pc-nav-bar .last-nav svg', function () {
			// ???????????????
			$(this).parents('.last-nav').remove();
			// ???????????????????????????
			$obj.find('.type-box .type-item[data-flag="'+$(this).parents('.last-nav').data('flag')+'"]').show();
			// ???????????????????????????id
			var idArr = [];
			$.each($obj.find('.pc-nav-bar .last-nav'), function (i, v) {
				idArr.push($(v).data('id'));
			});
			/* ????????????????????? ?????????????????? */
			if($obj.find('.pc-container-box .more-choose.on').length) $obj.find('.pc-container-box .more-choose').click();
			// ????????????/*  */
			advSearch.renderShopList(categoryId, idArr, markId);
			resetStyle($obj);
		});
		function resetStyle ($_obj) {
			// ???????????????????????? ???????????????????????????  ??????????????????
			$_obj.css({
				'top': $_obj.siblings('.wqdcommodityCategory[wqdmark="'+markId+'"]').offset().top - $_obj.parent().offset().top + $_obj.siblings('.wqdcommodityCategory[wqdmark="'+markId+'"]').height() + 'px',
				'left': $_obj.siblings('.wqdcommodityCategory[wqdmark="'+markId+'"]').css('left'),
				'height':4*41 + 6+ $_obj.find('.more-choose').height() +  $_obj.find('.pc-nav-bar').height()+'px',
			});
			// ?????? ????????????????????????????????????nav ???????????????????????????
			if(!$_obj.find('.type-box .type-item:visible').length) {
				$_obj.find('.type-box').css('border-top-color','transparent');
				$_obj.find('.pc-nav-bar').css({
					'border-bottom-right-radius': '3px',
					'border-bottom-left-radius': '3px',
					'border-bottom-color':'rgb(220, 220, 220)'
				})
			} else {
				$_obj.find('.type-box').css('border-top-color','rgba(220, 220, 220, 1)');
				$_obj.find('.pc-nav-bar').css({
					'border-bottom-right-radius': '0px',
					'border-bottom-left-radius': '0px',
					'border-bottom-color':'transparent'
				})
			}
			$_obj.find('.type-item:last').css('border-bottom-style','solid');
		}
	}

	/**
	 * ?????????????????? 
	 * @param {[Object]} categoryId ?????????????????????????????????id
	 * @param {[Array]} idArr ??????????????? ??????????????????id??????
	 * @param {[string]}  markId ??????????????????markId???
	 */
	advSearch.renderShopList = function (categoryId, idArr, markId) {
		// ????????????
		// ?????? ??????
		var contactId = markId,
		modityDom = $(".wqdcommodityList[wqdmark="+contactId+"]"),
		stype = modityDom.attr("sorttype") || "time",
		USERID = modityDom.attr("userid") || "";
		// ??????commodityList.requestDate()  ????????????ajax ??????  ??????????????????
		// categoryid = $(this).attr("data-category") || (modityDom.attr("data-categoryids") ? modityDom.attr("data-categoryids") : "");
		commodityList.requestDate("/fixed/item/getItems",{userId:USERID,categoryId:categoryId,sortType:stype,properyId:idArr.join(',')},function(data){
			// modityDom.data("cacheData",data.data);	//????????????
			data.status == 200 && commodityList.dataInit.call(modityDom, data.data);
		});
	}
});	

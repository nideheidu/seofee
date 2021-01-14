/**
 * ?????????????????????
 */

$(function() {
	var _object = function () {

		this.init();
	};

	var pageNo = 1;
	var flagAJAX = 1;

	/* ????????? ?????? */
	var detailpageId = '';
	var textStr = '';

	_object.prototype = {
		/* ??????????????? */
		init: function () {
			var self = this;
			// ???????????????????????????
			$('body').on('click', '.shopSearch svg', function () {
				pageNo = 1;			
				// ??????????????? ??????????????????????????? ??????
				$('.wqdCenterView').html('<div class="search-list-zone"></div>');
				// ?????? ?????? siteid  
				var userId  = $(this).parents('.search-box').data('siteid');
				detailpageId = $(this).parents('.search-box').data('pageid')
				// ???????????????????????????  ????????????????????????????????????
				var json = pageNo +'?text=' + textStr  + '&siteId='+ userId;
				// ????????????
				self.strHtml(json);
				// ???????????????????????????????????????????????????????????? ????????????
				$(document).scroll(function (e) {
					var H = $(document).height();
					var oH = $(document).scrollTop();
					var Hr = document.documentElement.clientHeight;
					var footH = $('.wqdFooterView') && $('.wqdFooterView').height();
					if(H - footH <= oH + Hr ) {
						json = pageNo +'?text=' + textStr + '&siteId=' + userId;
						flagAJAX && self.strHtml(json);
					} 
				})
			})
			// ?????????????????????????????????????????????
			$('body').off('focus.search').on('focus.search', '.search-box .search-input', function (e) {
				this.onkeyup = function(event) {
					var event = event || window.event;
					event.keyCode == '13' && $('body .shopSearch svg').click();
				}
			});
			$('body').off('input.search propertychange.search').on('input.search propertychange.search', '.search-input', function (e) {
				textStr = $(this).val();
			});
			
		},
		strHtml: function (json) {
			var self = this;
			flagAJAX = 0;
			$.ajax({
				type: "GET",
				url: SAAS_SHOP+'/shop/api/goods/search/'+json,  // ?????? pageNo?text=""&siteId=""
				timeout: 20000, //?????????????????????????????????
				data: '',
				async: true,
				contentType: 'application/json, charset=utf-8 ',
				dataType: "jsonp", 
				success: function (data, status) {
					if(status == 'success') {
						var str = '';
						if(data.data.length) {
							$.each(data.data, function (i, val) {
								str += self.getHtml(val);
							})
							pageNo ++ ;
							flagAJAX = 1;
							$('.wqdCenterView .search-list-zone').append(str);
							// history.pushState('abc', '', '#'+new Date().getTime());
							// window.location.reload();
							$(document).scrollTop(0);
						} else {
							$('.wqdCenterView .search-list-zone img.no-list').length && $('.wqdCenterView .search-list-zone img.no-list').remove();
							$('.wqdCenterView .search-list-zone').append('<img class="no-list" style="width:142px;" src="http://ohuih434n.bkt.clouddn.com/qngroup001/u4366488/0/pc-blank.png">')
							$(document).scrollTop(0);
						}
						if(!data.hasNext && pageNo >1) {
							flagAJAX = 0;
							$(document).off('scroll');
							$('.wqdCenterView .search-list-zone .no-more').length && $('.wqdCenterView .search-list-zone .no-more').remove();
							$('.wqdCenterView .search-list-zone').append('<p class="no-more">???????????????<p>');
						}
					}
				}
			})
			
		},
		/**
		 * 
		 */
		getHtml: function (data) {
			return [
				'<div class="list-cell">',
				'<div class="shop-searsh-result-cell">',
					'<a '+(data.itemLink ? ('href="'+data.itemLink+'"'+(data.windowsType == 'news'?' target="_Blank"':'')):('href="pageItem_'+detailpageId+'_'+data.id+'.html?catch='+data.userId+'"'))+'>',
						'<div class="shop-img ">',
							'<img src="'+CSSURLPATH+data.picPath.split(',')[0]+'">',
						'</div>',
						'<div class="shop-brief">',
							data.name,
						'</div>',
						'<div class="show-foot" style="display:'+(/sharp-display\.cn/gi.test(window.location.href)?'none;':'block')+'">',
							'<span class="cur-money"><em>???</em>'+data.currentPrice+'</span>',
							'<span class="fav"><svg class="icon-fav" width="22" height="16" viewBox="-250 -250 2392 2392" xmlns="http://www.w3.org/2000/svg"><path fill="#999" d="M1664 596q0-81-21.5-143t-55-98.5-81.5-59.5-94-31-98-8-112 25.5-110.5 64-86.5 72-60 61.5q-18 22-49 22t-49-22q-24-28-60-61.5t-86.5-72-110.5-64-112-25.5-98 8-94 31-81.5 59.5-55 98.5-21.5 143q0 168 187 355l581 560 580-559q188-188 188-356zm128 0q0 221-229 450l-623 600q-18 18-44 18t-44-18l-624-602q-10-8-27.5-26t-55.5-65.5-68-97.5-53.5-121-23.5-138q0-220 127-344t351-124q62 0 126.5 21.5t120 58 95.5 68.5 76 68q36-36 76-68t95.5-68.5 120-58 126.5-21.5q224 0 351 124t127 344z"></path></svg>'+data.favorable+'</span>',
							'<span class="or-money">'+(data.originalPrice?('???'+data.originalPrice):'')+'</span>',
							'<span class="sales">?????????<em>'+data.salesVolume+'</em>???</span>',
						'</div>',
					'</a>',
				'</div>',
			'</div>'
			].join('');
		},
	};
	new _object();
});	

/*
Powered by ly200.com		http://www.ly200.com
广州联雅网络科技有限公司		020-83226791
*/
$(function(){
	var li=$('#igallery .bd li');
	jQuery(window).on('load resize', function(){
		if($('body').hasClass('w_1200')){
			$('#igallery .bd .tempWrap').css('width', '1118px');
			if(li.length>=6){
				$('#igallery .bd li').css('width', '172px');
				var w=$('#igallery .bd .tempWrap ul').width();
				$('#igallery .bd .tempWrap ul').css('width', (li.length*3*192)+'px')
			}
		}else{
			if(li.length>=6){
				$('#igallery .bd li').css('width', '162px');
				$('#igallery .bd .tempWrap ul').css('width', (li.length*3*172)+'px')
			}
			$('#igallery .bd .tempWrap').css('width', '914px');
		}
	});
	
	$("#igallery").slide({ mainCell:".bd ul", effect:"leftMarquee", vis:6, autoPlay:true, interTime:30});
});
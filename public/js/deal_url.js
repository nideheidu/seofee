jQuery(function(){
	var f=k=o='';
	var para=(window.location.search).replace('?','');
	if(para!=''){
		var ary=para.split('&');
		for(i=0; i<ary.length; i++){
			var v=ary[i].split('=');
			if(v[0]=='f' && v[1]!=''){
				f='&'+v[0]+'='+v[1];
			}else if(v[0]=='k' && v[1]!=''){
				k='&'+v[0]+'='+v[1];
			}else if(v[0]=='o' && v[1]!=''){
				o='&'+v[0]+'='+v[1];
			}
		}
		
		if(f!='' && k!=''){
			jQuery('a').each(function(){
				var url=jQuery(this).attr('href');
				if((/^\/(.*)$/g.test(url)) || (/^http(.*)$/g.test(url))){
					if((/^(.*)\?(.*)$/g.test(url))){
						url=url+f+k+o;
					}else{
						url=url+'?'+f+k+o;
					}
					jQuery(this).attr('href', url);
				}
			});
		}
	}
	jQuery('#global_qq_service a').click(function(){
		var referer=window.location.href;
		var query_string='qq='+jQuery(this).attr('d')+'&v='+referer;
		jQuery.get('/inc/lib/global/online_service.php?no_jump=1&'+query_string);
		jQuery.post('/action/web.php','do_action=online_service&qq='+jQuery(this).attr('d'));
	});
});
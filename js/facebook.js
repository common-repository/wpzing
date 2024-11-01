var ajaxurl;
var nonce;

Facebook=function(){
  
	return{
		init: function(ajax,security){
			ajaxurl=ajax;
			nonce=security;
		},
    
		facebookLike: function(id,nonce,unlike){
			data={
				action:'facebookLike',
				security:nonce,
				id:id,
				like:unlike
			}
			jQuery.post(ajaxurl, data ,function(response){
				if(response=='Liked') {
					jQuery('#'+id+'_like').text('Unlike')
					jQuery('#'+id+'_like').attr('onclick', 'App.facebookLike("'+id+'","'+nonce+'","unlike")');
				} else if(response=='Unliked'){
					jQuery('#'+id+'_like').text('Like')
					jQuery('#'+id+'_like').attr('onclick', 'App.facebookLike("'+id+'","'+nonce+'","like")');
				}
			})
		},

		facebookComment: function(id){
			jQuery('#'+id+'_comment').focus();
		},

		facebookCommentKey: function(e,comment_id){
			message=jQuery('#'+comment_id+'_comment').val();
			if(e.keyCode==13 || e.which==13){
				data={
					action:'facebookComment',
					comment_id:comment_id,
					message:message,
					security:nonce
				}
				jQuery.post(ajaxurl,data,function(response){
					if (response!=-1){
						jQuery('#zWallFb_comments'+comment_id).append(response);
						jQuery('#'+comment_id+'_comment').val('');
					}
				});
			}
		},

		load : function(){
			data={
				action:'getFacebookFeed',
				security:nonce
			}
			jQuery.post(ajaxurl,data,function(response){
				jQuery('#zWallFb').html(response);
			});
		}
	}
}();

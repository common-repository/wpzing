
Twitter = function(){
    
	return{
		init: function(ajax,security){
			ajaxurl=ajax;
			nonce=security;
		},
        
		hover_tweet:function(tweet){
		},
    
		expand : function(id,link,photo){
			jQuery('#'+id).removeClass('zTweetHover')
			jQuery('#'+id).find('.zTweetExpandContent').slideDown('slow');
			jQuery(link).attr('onClick','Twitter.collapse(\''+id+'\',this,'+photo+')');
			jQuery(link).css({
				color:'#1982D1'
			})
			jQuery(link).text('Collapse');
			jQuery('#'+id).find('.zTweetExtraActions').addClass('visibleFixed');
		},
    
		collapse:function(id,link,photo){
			jQuery('#'+id).addClass('zTweetHover')
			jQuery('#'+id).find('.zTweetExpandContent').slideUp('slow');
			jQuery(link).attr('onClick','Twitter.expand(\''+id+'\',this,'+photo+')');
			if(!photo)
				jQuery(link).text('Expand');
			else
				jQuery(link).text('View Photo');
			jQuery(link).css({
				color:'#AAAAAA'
			})
			jQuery('#'+id).find('.zTweetExtraActions').removeClass('visibleFixed');
		},
    
		favorite : function(id,favorite){
			data={
				action:'twitterFavorite',
				id:id
			}
			jQuery.post(ajaxurl,data,function(response){
            
				if(response){
					if(jQuery('#'+id).find('.rtf').hasClass('rtf')){
						jQuery('#'+id).find('.rtf').removeClass('rtf').addClass('favorited');
					} else {
						jQuery('#'+id).find('.retweeted').removeClass('retweeted').addClass('refavorited')
					}
					jQuery('#'+id).find('.zWallTweetFavorite').removeClass('zWallTweetFavorite').addClass('zWallTweetFavorited');
					jQuery('#'+id).find('.favoriteLink').attr('onClick','Twitter.destroyFavorite("'+id+'")');
					jQuery('#'+id).find('.favoriteLink').removeClass('favoriteLink').addClass('zWallTweetActiveFavorite');

				}
			})
		},
		destroyFavorite : function(id){
			data={
				action:'twitterDestroyFavorite',
				id:id
			}
			jQuery.post(ajaxurl,data,function(response){

				if(response){
					if(jQuery('#'+id).find('.refavorited').hasClass('refavorited')){
						jQuery('#'+id).find('.refavorited').removeClass('refavorited').addClass('retweeted');
					} else {
						jQuery('#'+id).find('.favorited').removeClass('favorited').addClass('rtf')
					}
					jQuery('#'+id).find('.zWallTweetFavorited').removeClass('zWallTweetFavorited').addClass('zWallTweetFavorite');
					jQuery('#'+id).find('.zWallTweetActiveFavorite').attr('onClick','Twitter.favorite("'+id+'")');
					jQuery('#'+id).find('.zWallTweetActiveFavorite').removeClass('zWallTweetActiveFavorite').addClass('favoriteLink');
				}
			});
		},

		retweet: function(id){
			data={
				action:'twitterRetweet',
				id:id
			};
			jQuery.post(ajaxurl,data,function(response){
            
				if(response){
					if(jQuery('#'+id).find('.rtf').hasClass('rtf')){
						jQuery('#'+id).find('.rtf').removeClass('rtf').addClass('retweeted');
					} else {
						jQuery('#'+id).find('.favorited').removeClass('favorited').addClass('refavorited')
					}
					jQuery('#'+id).find('.zWallTweetRetweet').removeClass('zWallTweetRetweet').addClass('zWallTweetRetweeted');
					jQuery('#'+id).find('.retweetLink').attr('onClick','Twitter.destroyRetweet("'+id+'")');
					jQuery('#'+id).find('.retweetLink').removeClass('retweetLink').addClass('zWallTweetActiveRetweet');
				}
			});
		},

		destroyRetweet: function(id){
			data={
				action:'destroyRetweet',
				id:id
			}
			jQuery.post(ajaxurl,data,function(response){
            
				if(response){
					if(jQuery('#'+id).find('.refavorited').hasClass('refavorited')){
						jQuery('#'+id).find('.refavorited').removeClass('refavorited').addClass('favorited');
					} else {
						jQuery('#'+id).find('.retweeted').removeClass('retweeted').addClass('rtf')
					}
					jQuery('#'+id).find('.zWallTweetRetweeted').removeClass('zWallTweetRetweeted').addClass('zWallTweetRetweet');
					jQuery('#'+id).find('.zWallTweetActiveRetweet').attr('onClick','Twitter.retweet("'+id+'")');
					jQuery('#'+id).find('.zWallTweetActiveRetweet').removeClass('zWallTweetActiveRetweet').addClass('favoriteRetweet');
				}
			});
		},
		destroyTweet:function(id){
			data={
				action:'destroyTweet',
				id:id
			}
        
			jQuery.post(ajaxurl,data,function(response){
				if(response){
					jQuery('#'+id).remove();
				}
			});
		},

		focusText : function(id,screen_name){
			text=jQuery.trim(jQuery('#reply-to-'+id).val());
			if(text=='Reply to @'+screen_name || text==''){
				jQuery('#reply-to-'+id).animate({
					height:'80px'
				},0);
				jQuery('#zTweetButton'+id).show();
				jQuery('#zTwitterCounter'+id).show();
				jQuery('#reply-to-'+id).addClass('active');
				jQuery('#reply-to-'+id).css('color','#333333');
				jQuery('#reply-to-'+id).val('@'+screen_name);
				var l=text.length;
				jQuery('#zTwitterCounter'+id).text(140-l);
				jQuery('#reply-to-'+id).focusout(function(){
					if(jQuery.trim(jQuery('#reply-to-'+id).val())=='@'+screen_name || jQuery.trim(jQuery('#reply-to-'+id).val())==''){
						jQuery('#reply-to-'+id).animate({
							height:'24px'
						},0);
						jQuery('#reply-to-'+id).removeClass('active');
						jQuery('#reply-to-'+id).val('Reply to @'+screen_name)
						jQuery('#reply-to-'+id).css('color','#AAAAAA');
						jQuery('#zTweetButton'+id).hide();
						jQuery('#zTwitterCounter'+id).hide();
					}
				});
			}
		},
		counter : function(id,screen_name){
			var l=jQuery('#reply-to-'+id).val().length;
			jQuery('#zTwitterCounter'+id).text(140-l);
			text=jQuery.trim(jQuery('#reply-to-'+id).val())

			if (text=='@'+screen_name || text==''){
				jQuery('#zTweetButton'+id).prop('disabled', true);
				jQuery('#zTweetButton'+id).removeClass('zWallTweetButtonActive');
			} else {
				jQuery('#zTweetButton'+id).prop('disabled',false);
				jQuery('#zTweetButton'+id).addClass('zWallTweetButtonActive');
			}
		},
		replyLink: function(id,replyLink,photo){
			expanded=jQuery('#'+id).find('.zTweetExpandContent');
			if(! jQuery(expanded).is(':visible')){
				link=jQuery(replyLink).parent().parent().parent().find('.zWallTweetExpand');
				Twitter.expand(id,link,photo);
			}
			jQuery('#reply-to-'+id).focus();
		},
        
		reply: function(id,screen_name){
			text=jQuery('#reply-to-'+id).val()
			if(text!='Reply to @'+screen_name && text!=''){
				data={
					action:'twitterReply',
					id:id,
					status:text
				}
				jQuery.post(ajaxurl,data,function(response){
					if(response){
						jQuery('#reply-to-'+id).animate({
							height:'24px'
						},0);
						jQuery('#reply-to-'+id).removeClass('active');
						jQuery('#reply-to-'+id).val('Reply to @'+screen_name)
						jQuery('#reply-to-'+id).css('color','#AAAAAA');
						jQuery('#zTweetButton'+id).hide();
						jQuery('#zTwitterCounter'+id).hide();
					}
				});
			}
		},
    
		modal: function(id){
			jQuery('#zRetweet'+id).dialog({
				resizable: false,
				width: 500,
				modal: true,
				buttons: {
					'Cancel': function() {
						jQuery( this ).dialog( "close" );
					},
					'Retweet': function() {
						jQuery( this ).dialog( "close" );
						Twitter.retweet(id);
					}
				
				}
			})
		},

		load : function(){
			data={
				action:'twitterGetTimeline',
				security:nonce
			}
			jQuery.post(ajaxurl,data,function(response){
				jQuery('#zWallTwitter').html(response);
			});
      
		}
	}

}();


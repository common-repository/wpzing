
Wall = function(){

	return {
		init: function(){
			
		},

		post_status: function(nonce){

			if(jQuery('#wall_post_text').val()){
				jQuery('#zWall_post_submit').css({
					"display":"none"
				});
				jQuery('#zWall_post_ajax').css({
					"display":"inline"
				});

				var data = Wall.form_data('zWall_post', {
					'action':'insert_wall_post',
					'nonce':nonce
				});

				jQuery.post(ajaxurl, data, function(response){
					if (response.success == true) {
						document.getElementById('zWall_post').reset();
						jQuery('#zWallCounter').text('0');
						jQuery('#zWallPosts').prepend(response.post);
						Embed.close();
					}
					jQuery('#zWall_post_ajax').css({
						"display":"none"
					});
					jQuery('#zWall_post_submit').css({
						"display":"inline"
					});
					
				}, 'json');
			}
			return false;
		},

		postComment: function(id, nonce){
			if(jQuery("#comment_author"+id).val()==""){
				author=false;
				jQuery("#comment_author"+id).css({
					"border":"1px solid red"
				});
			}	else {
				jQuery("#comment_author"+id).css({
					"border":"1px solid #CCCCCC"
				});
				author=true;
			}
			if(jQuery("#comment_email"+id).val()==""){
				email=false;
				jQuery("#comment_email"+id).css({
					"border":"1px solid red"
				});
			}	else {
				jQuery("#comment_email"+id).css({
					"border":"1px solid #CCCCCC"
				});
				email=true;
			}
			if (jQuery("#comment"+id).val()=="") {
				comment=false;
				jQuery("#comment"+id).css({
					"border":"1px solid red"
				});
			} else {
				jQuery("#comment"+id).css({
					"border":"1px solid #CCCCCC"
				});
				comment=true;
			}
			if(author && email && comment) {
				/* */
				jQuery('#post_comment_submit'+id).parent().css({
					"display":"none"
				});
				jQuery('#post_comment_ajax'+id).css({
					"display":"inline"
				});

				var data = Wall.form_data('post_comment'+id, {
					'action':'insert_wall_comment',
					'nonce':nonce
				});
				jQuery.get(ajaxurl, data, function(response){
					jQuery('#comment_box'+id).append(response);
					jQuery("#comment"+id).val('');

					jQuery('#post_comment_ajax'+id).css({
						"display":"none"
					});
					jQuery('#post_comment_submit'+id).parent().css({
						"display":"inline"
					});

				});

			}
			return false;
		},

		moreComments: function(post_id, number){
			jQuery('#more_comments'+post_id).remove();
      
			jQuery('#more_comments_ajax'+post_id).show();
			jQuery.get(ajaxurl, {
				'action':'more_comments',
				'post_id':post_id,
				'number':number
			}, function(response){
				jQuery('#more_comments_ajax'+post_id).remove();
				jQuery('#comment_box'+post_id).prepend(response);
			});
		},

		commentUser: function(data){
			if(data){
				user = data;
				jQuery('form[name=post_comment] input[name=comment_author]').each(function(){
					jQuery(this).val(user.author);
					jQuery(this).attr('readonly', 'readonly');
				});
				jQuery('form[name=post_comment] input[name=comment_author_email]').each(function(){
					jQuery(this).val(user.email);
					jQuery(this).attr('readonly', 'readonly');
				});
				jQuery('form[name=post_comment] input[name=comment_author_url]').each(function(){
					jQuery(this).val(user.website);
					jQuery(this).attr('readonly', 'readonly');
				});
				jQuery('form[name=post_comment] input[name=comment_zsuserid]').each(function(){
					jQuery(this).val(user.zsuserid);
				});
			}
		},

		form_data: function(id, addendum){
			var array = jQuery('#'+id).serializeArray();
			var data = {};
			for(var key in array){
				data[array[key].name] = array[key].value;
			}
			if(addendum){
				for(key in addendum){
					data[key] = addendum[key];
				}
			}
			return data;
		},
    collapseComments:function(id,span){
      
      jQuery('#zWallComments'+id).slideToggle('slow');
      if(jQuery(span).hasClass('expand')){jQuery(span).removeClass('expand').addClass('collapse')}
        else{jQuery(span).removeClass('collapse').addClass('expand')}
      
    },
    showCommentForm:function(id,span){
      
      if(!jQuery('#zWallComments'+id).is(':visible')){Wall.collapseComments(id, jQuery(span).parent().find('.expand') )}
      
      
      jQuery('html, body').animate({
         scrollTop: jQuery('#comment'+id).offset().top-300
     }, 500);
     jQuery('#zWallComments'+id).find('#comment'+id).focus();
    },
    toTop: function(){
      
     jQuery("html,body").animate({ scrollTop: 0 }, 'slow');
      
    }
	}
}();

Embed=function(){
	var url=null;
	var regex = /^http[s]?\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(\/\S*)?$/
	return{
        
		init: function(ajax,security){
			ajaxurl=ajax;
			nonce=security;
		},
		checkUrl:function(textarea){
			setTimeout(function(){
				var text=textarea.val().split(' ');
				url=null;
				if(text){
					Embed.getUrl(text);
				}	else {
					if(regex.test(jQuery.trim(textarea.val())))
						url=jQuery.trim(textarea.val());
				}
				if(url){
					textarea.css('border-bottom','none');
					textarea.attr('disabled','disabled')
					jQuery('.zWallEmbedHolder').show()
                                
					data={
						action:'embed',
						url:url,
						nonce:nonce
					}
					jQuery.post(ajaxurl,data,function(response){
						if(response.indexOf('{"message"')>-1){
							response=response.substring(response.indexOf('{"message"'), response.length);
							response=jQuery.parseJSON(response)
							if(response.message=='ok'){
								jQuery('.zWallEmbedHolder').append(response.data);
								jQuery('.embedLoader').hide();
								jQuery('#zWallEmbededContent').val(1);
								jQuery('.zWallEmbedList').find('li:first').show();
							} else {
								jQuery('.zWallEmbedHolder').append('<div class="zWallEmbed" id="zWallEmbed"><p>Not Found</p><span class="zWallCloseEmbed" onclick="Embed.close()">x</span></div>');
								jQuery('.embedLoader').hide();
								jQuery('#zWallEmbededContent').val(1);
							}
						} else {
							jQuery('.zWallEmbedHolder').append('<div class="zWallEmbed" id="zWallEmbed"><p>Not Found</p><span class="zWallCloseEmbed" onclick="Embed.close()">x</span></div>');
							jQuery('.embedLoader').hide();
							jQuery('#zWallEmbededContent').val(1);
						}
						textarea.removeAttr('disabled');
					});
				}
			},100)
		},
        
		getUrl:function(text){
			jQuery.each(text, function(index,value) {
				if(regex.test(value)) {
					url=value;
					return;
				}
			});
		},
		close:function(){
			jQuery('.zWallEmbedHolder').css({
				display:'none'
			});
			jQuery('.zWallEmbedHolder .embedLoader').css({
				display:'inline'
			});
			jQuery(document).find('#zWallEmbed').remove();
			jQuery(".zWallTextarea").css('border-bottom','1px solid #C8C8C8');
			url=null;
			jQuery('#zWallEmbededContent').val('');
		},
		video:function(e,url){
      jQuery(e).parent().parent().find('p').css({'padding-left':'5px'});
			jQuery(e).parent('div').html('<iframe width="531" height="350" src="'+url+'&autoplay=1" frameborder="0" allowfullscreen ></iframe>');
      
      
		},
		navigate:function(e){
			var li=jQuery('.zWallEmbedList').find(':visible')
			var index=jQuery(li).index();
			var length=jQuery('.zWallEmbedList li').length

			if(jQuery(e).attr('class')=='zWallNavLeft'){
				index=index-1
			} else{
				index=index+1
			}
			if(length==index){
				jQuery('.zWallEmbedList li').hide().eq(0).show();
			} else {
				jQuery('.zWallEmbedList li').hide().eq(index).show();
			}
			jQuery('#zWallEmbededContentImage').val("<img src='"+jQuery('.zWallEmbedList').find(':visible').find('img').attr('src')+"' class='zWallEmbedImage' />");
		}
    
  
	}
    
}();

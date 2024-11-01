App = function(param) {

	var text='';
	var user={
		autor:'',
		email:'',
		website:'',
		zsuserid:''
	};
	var regex = /^http[s]?\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(\/\S*)?$/
	return{
		init: function(ajax,security){
			ajaxurl=ajax;
			nonce=security;
		},

		enableWallPost: function(element,id){
			if (jQuery(element).val()!="") jQuery('#wall_post'+id).removeAttr("disabled")
		},
	  isEnterKey: function(e,id){
			if (e.keyCode==13 || e.which==13){
				e.preventDefault();
				App.postComment(id);
			}
		},

		deleteStatusUpdate: function(id,e,nonce){
      jQuery(e).text('');
      jQuery(e).parent().find('img').show();
			data={
				action:'deleteStatusUpdate',
				security:nonce,
				id:id
			};
			jQuery.post(ajaxurl,data,function(response){
				if(response!=-1){
					setTimeout(function(){
						jQuery('#zWallStatusUpdate'+id).remove()
					},500);
				}
			});
		},

		repostWall: function(id){
      
			data={
				action:'repostWall',
				security:nonce,
				id:id,
				comment:jQuery('#comment'+id).val()
			};

			jQuery.post(ajaxurl,data,function(response){
				window.location.reload();
			});
		},

		deleteComment: function(id,post_id,nonce){
            
      jQuery('#zWallComment'+id).hide();
      jQuery('#zWallComment'+id).parent().find('.embedLoader').show();
			data={
				action:'deleteComment',
				id:id,
				post_id:post_id,
				security:nonce
			}
			jQuery.post(
				ajaxurl,data,function(response){
					if(response!=-1) {
						jQuery('#zWallComment'+id).parent().remove();
						jQuery('#NewsFeed'+id).parent().remove()
					}
				}
				);
		},

		toggleUploadForm: function(){
			if (jQuery('#upload_form').is(':visible')){
				jQuery('#upload_form').hide();
				jQuery('#upload_form_title').find('a').text('Upload your RSS file');
			} else{
				jQuery('#upload_form').show();
				jQuery('#upload_form_title').find('a').text('Close');
			}
		},

		toggleTabs: function(element,id, tab){
			jQuery('#zWallTabs .zWallTab').removeClass('active');
			jQuery(element).addClass('active');
			if(id==2){
				jQuery('#zWallSubtabs').show();
				App.toggleSubTabs(jQuery('#zWallSubtabs').find('span:first'),0);
			} else {
				jQuery('#zWallSubtabs').hide();
			}
			jQuery('#Zingram .zingram_div').hide().eq(id).show();
			jQuery('#'+tab).show();
			return false;
		},

		toggleSubTabs: function(element,id, subtab){
			jQuery('#zWallSubtabs .zWallSubtab').removeClass('active');
			jQuery(element).addClass('active');
			jQuery('#Zingram .zingram_div').hide().eq(id+2).show();
			jQuery('.zingram_div').hide();
			jQuery('#'+subtab).show();
			return false;
		},
		adminTabs: function(element) {
			// Set active
			jQuery('#zWall-tabs .nav-tab').removeClass('nav-tab-active');
			jQuery(element).addClass('nav-tab-active');
			// Get index
			var $tabIndex = jQuery('#zWall-tabs span').index(element);
			jQuery('.zWallPage').hide().eq($tabIndex).show();
			// Change action for tab return
			$form = jQuery(document).find('form[name=zing_form]');
			if($form.length) $form.attr('action', App.action($tabIndex, 1));
		},
         
		adminSubTabs: function(element, id) {
			// Set active
			jQuery(element).parent().children().removeClass('nav-tab-active');
			jQuery(element).addClass('nav-tab-active');
			// Get index
			var $tabIndex = jQuery(element).parent().children('span').index(element);
			jQuery('.zWallPage:visible .zWallSubPage').hide().eq($tabIndex).show();
			// Change action for tab return
			$form = jQuery(document).find('form[name=zing_form]');
			if($form.length) $form.attr('action', App.action($tabIndex, 2));
		},
         
		editFeed: function(e,id){
			text=jQuery(e).parent().parent().find('.link').text();
			jQuery(e).parent().parent().find('.link').html('<input type="text" id="link'+id+'">');
			jQuery(e).parent().parent().find('.save').show();
			jQuery(e).parent().parent().find('.cancel').show();
			jQuery(e).parent().parent().find('.edit').hide();
			jQuery(e).parent().parent().find('.delete').hide();
		},
		cancelEditFeed: function(e,id){
			jQuery(e).parent().parent().find('.link').text(id);
			jQuery(e).parent().parent().find('.save').hide();
			jQuery(e).parent().parent().find('.cancel').hide();
			jQuery(e).parent().parent().find('.edit').show();
			jQuery(e).parent().parent().find('.delete').show();
		},
         
		saveFeed: function(e,id,nonce){
			var link;
			link=jQuery('#link'+id).val();
			if(!regex.test(link)) {
				jQuery('#link'+id).css('border','1px solid red');
			} else{
				jQuery(e).parent().parent().find('.loader').show();
				data={
					action : 'saveFeed',
					'id' : id,
					'link' : link,
					security:nonce
				}
				jQuery.post(
					ajaxurl,data,function(response){
						if(response=='valid'){
							jQuery(e).parent().parent().find('.save').hide();
							jQuery(e).parent().parent().find('.cancel').hide();
							jQuery(e).parent().parent().find('.delete').show();
							jQuery(e).parent().parent().find('.valid').html('');
							jQuery(e).parent().parent().find('.link').html(link);
						}
						else{
							jQuery(e).parent().parent().find('.save').hide();
							jQuery(e).parent().parent().find('.cancel').hide();
							jQuery(e).parent().parent().find('.delete').show();
							jQuery(e).parent().parent().find('.edit').show();
							jQuery(e).parent().parent().find('.link').html(link);
						}
					}
					);
				jQuery(e).parent().parent().find('.loader').hide();
			}
		},
         
		deleteFeed: function(id,nonce){
			data={
				action: 'deleteFeed',
				'id' : id,
				security:nonce
			}
			jQuery.post(
				ajaxurl,data,function(response){
					if(response=='ok')
						jQuery('#row'+id).remove()
				}
				);
		},
         
		addFeed: function(nonce){
			if(regex.test(jQuery('#custom_url').val())) {
				jQuery('#custom_url').css('border','1px solid white');
				setTimeout(function(){
					jQuery('#custom_url').css('border','1px solid red')
				},100)
				setTimeout(function(){
					jQuery('#custom_url').css('border','1px solid white')
				},200)
				setTimeout(function(){
					jQuery('#custom_url').css('border','1px solid red')
				},300)
			}
			else{
				jQuery('#add_feed').attr('disabled','disabled');
				jQuery('#loader').show();

				jQuery.post(ajaxurl, {
					action:'addFeed',
					security:nonce,
					link:jQuery('#custom_url').val()
				}, function(response){
					if(response!='-1'){
						jQuery('#loader').hide();
						jQuery('#feeds_table').append(response);
						jQuery('#add_feed').removeAttr('disabled');
					}
					else
						jQuery('#loader').hide();
				});
			}
		},
         
		subTabs: function(tabIndex, subTabIndex) {
			// Set tab
			if( ! tabIndex) tabIndex = 0;
			var $tabElement = jQuery('#zWall-tabs span:eq(' + tabIndex + ')');
			App.adminTabs($tabElement);
			// Set subtab
			if( ! subTabIndex) subTabIndex = 0;
			if(jQuery('.zWallPage:visible .nav-tab-wrapper').length) {
				var $subTabElement = jQuery('.zWallPage:visible .nav-tab-wrapper:first span:eq(' + subTabIndex + ')');
				App.adminSubTabs($subTabElement);
			}
		},
         
		action: function(tab,level){
			action=jQuery(document).find('form[name=zing_form]').attr('action');
			if (level==1){
				pos=action.lastIndexOf('zwall');
				action=action.substring(0,pos+5);
				return action+'&subtab='+tab;
			}
			else{
				pos=action.indexOf('subtab');
				action=action.substring(0,pos+8);
				return action+'&subsubtab='+tab;
			}
		},
            
		search: function(nonce){
			keyword=jQuery('#zs_keyword').val();
      
      if(keyword=='') {return false}
			data={
				action:'search',
				keyword:keyword,
				security:nonce
			}
			
					data.followingarticles = 1;
			
					data.followingwallposts = 1;

					data.myarticles = 1;

          data.mywallposts = 1;


			jQuery('#zWallSubtabs').hide();
           
			jQuery('body').append('<div id="zWall-loader" style="z-index:9999;position:fixed;left:0;top:0;height:'+jQuery(window).height()+'px;width:'+jQuery(window).width()+'px;background:#FFFFFF;opacity:0.6;filter:alpha(opacity=60);"><div style="position:absolute;top:40%;left:50%;opacity:1;filter:alpha(opacity=100);height:100px;width:100px;" class="zWallSearchLoader"></div></div>')

			jQuery.get(ajaxurl,data,function(response){
				if(response!='-1'){
					jQuery('#zWallTabs').find(':hidden').show();
					jQuery('body').find('#zWall-loader').remove();
                
                
					App.toggleTabs(jQuery('#zWallSearchTab'),1, 'zSearch');
                
					jQuery('#zSearch').html(response);
				}
				else{
					jQuery('body').find('#zWall-loader').remove();
				}
			});
			return false;
		},
       
		updateZWall: function(nonce) {
			setInterval("App.zWallUpdate('"+nonce+"','"+ajaxurl+"')",120000);
		},
       
		zWallUpdate: function(nonce) {
			jQuery.get(ajaxurl,{
				action:'updateZWall',
				security:nonce
			},function(response) {
				if (response != '-1' && response !== '') {
					jQuery('#zWallPosts').prepend('<div id="zWallUpdates"></div>');
					jQuery('#zWallUpdates').prepend(response).hide().slideDown('slow');
				}
			});
		},
		closeSearch: function(){
           
			jQuery('#zWallTabs').find('.zWallSearch').show();
			jQuery('#zWallTabs').find('.search').hide();
			jQuery('#zSearch').html('');
			jQuery('#zs_keyword').val('');
			setTimeout(function(){
				App.toggleTabs(jQuery('#zWallTab'),0)
			},50);
		},
		showMore: function(limit){
			data={
				action:'showMore',
				page:limit
			}
			jQuery.get(ajaxurl,data,function(response){

          resp=jQuery.parseJSON(response);
						jQuery('#zWallPosts').append('<div id="zWall_'+limit+'" style="display:none">'+resp.html+'</div>');
						jQuery('#zWall_'+limit).hide().slideDown('slow');
				if(resp.next){
						jQuery('#zWall').find('.show_more a').attr('onClick','App.showMore('+(limit+1)+')');
					} else {
						jQuery('#zWallPosts').append('<p class="zWallGrayed">There is no more posts</p>');
						jQuery('#zWall').find('.show_more').html('');
					}
				
			})
		},
		getMore: function(limit,next_articles,next_feeds){
			data={
				action:'getMore',
				limit:limit,
				articles:next_articles,
				feeds:next_feeds
			}
			jQuery.get(ajaxurl,data,function(response){
				resp=jQuery.parseJSON(response);
				if(response!='-1'){
					jQuery('#zWallNewsFeedPosts').append('<div id="zWallNewsFeed_'+limit+'" style="display:none">'+resp.html+'</div>');
					jQuery('#zWallNewsFeed_'+limit).hide().slideDown('slow');
					if(resp.next_feeds!=0 || resp.next_articles!=0){
						jQuery('#zWallNewsFeed').find('.get_more a').attr('onClick','App.getMore('+(limit+10)+','+resp.next_articles+','+resp.next_feeds+')');
					}
					else {
						jQuery('#zWallNewsFeed').append('<p class="zWallGrayed">There is no more posts</p>');
						jQuery('#zWallNewsFeed').find('.get_more').html('');
					}
				}
			});
		},
	
		sendLog: function(nonce){
			data={
				action:'sendLog',
				security:nonce
			}
			jQuery('#send_loader').show();
			jQuery.post(ajaxurl,data,function(response){
				if(response!=-1)
					jQuery('#zHelp').find(' .message').text('Log was successfully sent');
				jQuery('#zHelp').find(' .message').show();
				jQuery('#send_loader').hide();
			})
		},
       
		feedsSettingsTabs: function(id){
			jQuery('#zWallFeeds-settings_content').find('div').filter(':visible:first').hide();
			jQuery('#zWallFeeds-settings_tabs').find(' .active_tab').css('');
			jQuery('#zWallFeeds-settings_tabs').find(' .active_tab').css({
				'margin': '0',
				'padding': '10px',
				'cursor': 'pointer',
				'border':'none'
			});
			jQuery('#zWallFeeds-settings_tabs').find(' .active_tab').removeClass('active_tab');
          
			jQuery('#'+id).addClass('active_tab');
			jQuery('#'+id).css({
				'border': '1px solid',
				'border-right': '1px solid #FFF',
				'margin': '0;',
				'padding': '10px',
				'margin-right':' -1px',
				'cursor': 'pointer'
			});
			jQuery('#'+id+'_content').show();
		},
    
		zWallCounter: function(){
			count=jQuery('#wall_post_text').val().length;
			if(count<141) jQuery('#zWallCounter').text(count);
			else
			if(count<500)
				jQuery('#zWallCounter').text('140+');
			else jQuery('#zWallCounter').text('500');
		},

		closeTodo: function(){
			jQuery('#zWallTodo').hide();
		},

		closeTodoItem: function(id,nonce){
			data={
				action:'closeTodoItem',
				security:nonce,
				item:id
			}
			jQuery.post(ajaxurl, data,function(){
				jQuery('#todo_'+id).remove();
				if(jQuery('#zWallTodoList').children('li').length==0)
					jQuery('#zWallTodo').remove();
			})
		},
    
		uploadOPML:function(){
			if(jQuery('input[name=rss]').val()==''){
				jQuery('input[name=rss]').css({
					'border':'1px solid white'
				})
				setTimeout(function(){
					jQuery('input[name=rss]').css({
						'border':'1px solid red'
					})
				},100);
				setTimeout(function(){
					jQuery('input[name=rss]').css({
						'border':'1px solid white'
					})
				},200);
				setTimeout(function(){
					jQuery('input[name=rss]').css({
						'border':'1px solid red'
					})
				},300);
			} else{
				jQuery('#zWallUploadRss').trigger('click');
				jQuery('#zWait').show();
			}
		},

		followZsBlog:function(){
		}
	}
}();

jQuery(function( $ ) {
	// Toggle zFeedback
	if($('#zWallFeedback').length) $('#zWallFeedback').css('display', 'none');
	$('#zWallFeedbackButton').click(function(event) {
		event.preventDefault();
		$('#zWallFeedback').fadeIn();
	});
	$('#zWallFeedbackClose').click(function(event) {
		event.preventDefault();
		$('#zWallFeedback').fadeOut();
	});

	jQuery('#zWallFeeds-settings_tabs').find('li').first().css({
		'border': '1px solid',
		'border-right': '1px solid #FFF',
		'margin': '0;',
		'padding': '10px',
		'margin-right':' -1px',
		'cursor': 'pointer'
	});
	jQuery('#zWallFeeds-settings_tabs').find('li').first().addClass('active_tab');
	jQuery('#zWallFeeds-settings_content').find('div').first().show();
});


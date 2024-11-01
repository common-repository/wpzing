Tumblr=function(){
  return{
    
     init: function(ajax,security){
            ajaxurl=ajax;
            nonce=security;
        },
     like: function(post_id,reblog_key){
       
       data={post_id:post_id,reblog_key:reblog_key,action:'tumblrLike',security:nonce}
       
        jQuery.post(ajaxurl,data,function(response){
                    
                   if(response == 'ok'){
                     jQuery('#tumblrLike'+post_id).attr('class','zTumblrUnlike')
                     jQuery('#tumblrLike'+post_id).attr('title','Unlike');
                     jQuery('#tumblrLike'+post_id).attr('onclick','Tumblr.unlike(\''+post_id+'\',\''+reblog_key+'\')');
                     jQuery('#tumblrLike'+post_id).attr('id','tumblrUnlike'+post_id);
                   }                     
                                    
        })
       
     },
     unlike: function(post_id,reblog_key){
       
       data={post_id:post_id,reblog_key:reblog_key,action:'tumblrUnlike',security:nonce}
       
        jQuery.post(ajaxurl,data,function(response){
                    
                   if(response == 'ok'){
                     jQuery('#tumblrUnlike'+post_id).attr('class','zTumblrLike')
                     jQuery('#tumblrUnlike'+post_id).attr('title','Like');
                     jQuery('#tumblrUnlike'+post_id).attr('onclick','Tumblr.like(\''+post_id+'\',\''+reblog_key+'\')');
                     jQuery('#tumblrUnlike'+post_id).attr('id','tumblrLike'+post_id);
                   }                     
                                    
        })
       
     },
     deletePost: function(post_id,blog){
       
       data={post_id:post_id,blog:blog,action:'tumblrDeletePost',security:nonce}
       
       jQuery.post(ajaxurl,data,function(response){
                    
                   if(response == 'ok'){
                     jQuery('#'+post_id).remove();
                   }                     
                                    
        })
       
     },
     unfollow: function(blog){
       
       data={blog:blog,action:'tumblrUnfollow',security:nonce}
       
       jQuery.post(ajaxurl,data,function(response){
                    
                   if(response == 'ok'){
                     jQuery('#zWallTumblr .'+blog).remove();
                     jQuery('.zTumblrPostWrapper .unfollow').hide();
                     jQuery('#unfollow_overlay').hide();
                   }                     
                                    
        })
       
     },
     load : function(){
    
      data={action:'getDashboard',security:nonce}
      
      jQuery.post(ajaxurl,data,function(response){
        
        jQuery('#zWallTumblr').html(response);
        
      })
    }
  
  }
  
}();


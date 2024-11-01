<div class="zWallPostForm">
   <form name="zWall_post" action="" method="post" id="zWall_post" class="zWallForm" onSubmit="return Wall.post_status('<?php $this->nonce?>')">
       <img src="<?php print plugins_url('images/avatar-bg.png',ZING_PLUGIN_FILE); ?>" class="userAvatar avatarBg" />
       <img src="<?php print $blogInfo['avatar']? $blogInfo['avatar']:plugins_url('images/default_avatar.png',ZING_PLUGIN_FILE)  ?>" class="userAvatar" />
        <textarea class="zWallTextarea" name="wall_post_text" id="wall_post_text" placeholder="Say it..." maxlength="500" onKeyPress="App.enableWallPost()" onKeyUp="App.zWallCounter()"></textarea>
            <div class="zWallEmbedHolder">
                 <div style="margin:0 auto">
                      <img src="<?php print plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE) ?>" style="margin:0 auto" class="embedLoader"/>
                 </div>
            </div>
            <input type="hidden" id="zWallEmbededContent" value="0" />
   <div style="clear:both"></div>
   <div class="zWallCounter"><span style="float:right" id="zWallCounter" class="zWallGrayed">0</span></div>
        <div class="zWallFormFooter">
          <div class="zWallPublishOption">
              <input type="checkbox" name="zWall_blog_post" <?php print $this->plugin->get_option('zBlogPost') ? 'checked="checked"' : '';?> style="display: none"/>
              <label for="zWall_blog_post" class="zWallTransform <?php print $this->plugin->get_option('zBlogPost') ? 'zWallChecked' : 'zWallCheckbox';?> "></label>
              <span class="zWallGrayed">Publish this zWall status as blog post</span>
          </div>
              <div class="zWallFormSubmitButton">
                <input type="submit" value="" name="zWall_post" id="zWall_post_submit" class="zWallPublishButton"/>
                <img src="<?php print plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE)?>" style="margin:12px auto 0; display:none" id="zWall_post_ajax" class="embedLoader" alt="ajax"/>
              </div>  
      </div>
        
  </form>
    <script>jQuery(document).ready(function(){

      $=jQuery.noConflict();
      
      jQuery(".commentInput").autosize();



      jQuery(".zWallFormHolder").click(function(){
        jQuery(".zWallFormHolder").removeClass("zWallFormHolder");

      })

      jQuery(".zWallTextarea").keydown(function(e) {
        if (e.keyCode == 32 && jQuery("#zWallEmbededContent").val()==0) {
          Embed.checkUrl(jQuery(this));
        }
      });
      jQuery(".zWallTextarea").bind("paste", function() {
        if (jQuery("#zWallEmbededContent").val()==0) {
          Embed.checkUrl(jQuery(this));
        }
      });
      jQuery("#zs_keyword").keypress(function(e){
        if (e.keyCode==13 || e.which==13){
          e.preventDefault();
          App.search(nonce);
        }


      })
      
         jQuery(".zWallTransform").bind("click",function(){
           
           var name=jQuery(this).attr("for");

           if(jQuery("[name=\'"+name+"\']").is(":checked")){
             
             jQuery("[name=\'"+name+"\']").removeAttr("checked");
             jQuery(this).removeClass("zWallChecked");
             jQuery(this).addClass("zWallCheckbox");
             
           }
           else{
             
             jQuery("[name=\'"+name+"\']").attr("checked","checked");
             jQuery(this).addClass("zWallChecked");
             jQuery(this).removeClass("zWallCheckbox");
           }
           
         })
      
    })
    
  </script>
</div>
<div class="zWallClear"></div>
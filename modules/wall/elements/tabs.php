<?php

/*
 * WPzing: Integrate Zingsphere's services into your WordPress blog
 * Copyright (c) 2012 Zingsphere Ltd.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
*/

?>
<div id="zWallNavigation">
<a href="/">Home</a>  
<a href="#" class="active">zWall</a>
<div class="zWallSearch">
  <form action="" method="post" id="zWall_search_form" onSubmit="return App.search('<?php echo wp_create_nonce('wpzing-security-nonce')?>')">
      <input type="text" name="zs_keyword" id="zs_keyword" value="<?php print htmlentities($_POST['zs_keyword'])?>" placeholder="Search..."  />
		</form>
</div>
</div> 
<div class="zWallClear"></div>
<div id="zWallTabs">
  <div class="zWallTabsFrame"></div>
	<span onClick="return App.toggleTabs(this,0, 'zWall')" class="zWallTab active" id="zWallTab"><?php echo $page->post_title ?></span>
	<?php if($this->plugin->get_option('newsFeeds_active') && $this->canUserManageWall()): ?>
	<span onClick="return App.toggleTabs(this,2, 'zWallNewsFeed')" class="zWallTab subtabs" id="zWallNewsFeedTab">Feed</span>
	<?php endif ?>
	<span onClick="return App.toggleTabs(this,1, 'zSearch')" class="zWallTab search" style="display:none" id="zWallSearchTab">Search</span>

<!--	<div class="zWallPowered">Powered by <a href="<?php echo ZING_WWW_BASE?>" target="_blank">Zingsphere</a></div>-->
	<div class="zWallClear"></div>
</div>
 
<!--If curent user is admin create subtabs for social news feed-->
<?php if($this->canUserManageWall() && $this->plugin->get_option('newsFeeds_active')): ?>
<div id="zWallSubtabs" style="display:none">
  <div class="zWallTabsFrame"></div>
	<span onClick="return App.toggleSubTabs(this,0, 'zWallNewsFeed')" class="zWallSubtab active following" title="Following"></span>
		<?php if($this->plugin->get_option('publish_feeds_from_facebook') || !$this->plugin->modules['wall']->getFbConnected()): ?>
	<span onClick="return App.toggleSubTabs(this,1, 'zWallFb')" class="zWallSubtab facebook" title="Facebook"></span>
		<?php else: ?>
	<span title="To enable facebook feeds go to the plugin settings->zFeeds->social feeds and enable displaying facebook news feed on zWall" title="Facebook" class="inactive facebook"></span>
		<?php endif;
		if($this->plugin->get_option('publish_feeds_from_twitter') || !$this->plugin->modules['wall']->getTwitterConnected()): ?>
	<span onClick="return App.toggleSubTabs(this,2, 'zWallTwitter')" class="zWallSubtab twitter" title="Twitter"></span>
		<?php else: ?>
	<span title="To enable twitter feeds go to the plugin settings->zFeeds->social feeds and enable displaying twitter news feed on zWall" class="inactive twitter" title="Twitter"></span>
		<?php endif;?>
	<span onClick="return App.toggleSubTabs(this,3, 'zWallNewsPosts')" class="zWallSubtab last walls">Friend Walls</span>
</div>
<?php endif; ?>
<div class="zWallClear"></div>

<script>
									
                jQuery(".zFollowButton, .zUnfollowButton").bind("click",function(){
                
                var button=jQuery(this);
                var id=jQuery(button).parent().attr("id").replace("follow_","");
                
                jQuery(button).hide()
                
                jQuery(button).parent().parent().append("<img src=\''. plugins_url('images/embed_loader.gif',ZING_PLUGIN_FILE).'\' class=\'zWallLoader\' />");
                             
               
                
                data={action:"followZsBlog",id:id};
               
                jQuery.post("'.  admin_url('admin-ajax.php') .'" , data , function(response){
                  alert(response)
                          if(response=="followed"){
                            
                            jQuery(this).find("zFollowButton").removeClass("zFollowButton").addClass("zUnfollowButton");
                            jQuery(this).attr("title","Unfollow blog");
                            }
                           if(response=="unfollowed"){
                              alert(response)
                              jQuery(this).attr("class","zFollowButton");
//                              jQuery(this).title("Follow Blog");
                            }

                });
                
                

                    })
                </script>




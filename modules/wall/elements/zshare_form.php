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

<link  href="<?php echo plugins_url('/css/zshare.css',ZING_PLUGIN_FILE)?>"  type="text/css" rel="stylesheet" media="screen"/>
<script src="<?php echo includes_url('/js/jquery/jquery.js'); ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/js/app.js',ZING_PLUGIN_FILE) ?>" type="text/javascript"></script>
<script src="<?php echo plugins_url('/js/zwall.js',ZING_PLUGIN_FILE) ?>" type="text/javascript"/></script>
<script src="<?php echo plugins_url('/js/jquery.autosize-min.js',ZING_PLUGIN_FILE) ?>" type="text/javascript"/></script>
<script src="<?php echo plugins_url('/js/zwall.js',ZING_PLUGIN_FILE) ?>" type="text/javascript"/></script>
<script src="<?php echo plugins_url('/js/jquery.autosize-min.js',ZING_PLUGIN_FILE) ?>" type="text/javascript"/></script>
<div class="zHeader">
  <img src="http://zingsphere.com/img/logo.png" />
</div>
<div class="zShareFormHolder">
  <b>Share this with your zWall</b>
  <form name="zWall_post" action="" method="post" id="zWall_post" class="zWallForm" onSubmit="setTimeout(function(){window.close()},2000);return Wall.post_status('<?php $this->nonce?>');">
    <textarea class="zWallTextarea" style="height:35px" name="wall_post_text" id="wall_post_text" placeholder="Write something..." maxlength="500" onKeyUp="App.zWallCounter('')"><?php echo $_GET['u'] ?></textarea>
		  <div class="zWallEmbedHolder">
       <div style="margin:0 auto">
				<img src="<?php echo plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE) ?>" style="margin:0 auto" class="embedLoader"/>
			</div>
		</div>
		<div style="clear:both"></div>
		<p class="zWallFormFooter">
			<input type="checkbox" name="zWall_blog_post" <?php print $this->plugin->get_option('zBlogPost') ? 'checked' : '';?>/><span class="zWallGrayed">Make this zWall post a blog post</span>
			<span style="float:right" id="zWallCounter" class="zWallGrayed">0</span>
		</p>
		<p class="zWallFormFooter"><input type="submit" value="Share" name="zWall_post" id="zWall_post_submit" class="comment_button"/>
		<img src="<?php print plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE) ?>" style="margin:12px auto 0; display:none" id="zWall_post_ajax" class="embedLoader" alt="ajax"/>
		</p>
   </form>
</div>
<script type="text/javascript">
	jQuery(document).ready(function(){
    Embed.init('<?php print admin_url('admin-ajax.php') ?>', '<?php print wp_create_nonce('wpzing-security-nonce') ?>');
		Embed.checkUrl(jQuery('#wall_post_text'));

<?php if($_GET['s']): ?>
			setTimeout(function(){jQuery('textarea').val('<?php echo $_GET['s']; ?>') },200);
<?php endif; ?>    

			setTimeout(function(){App.zWallCounter('')},300);
			
		})
</script>
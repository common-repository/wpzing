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

<!-- zWall General settings -->
<div id="zWallGeneral" class="zWallPage">
  <p>View <a href="<?php echo get_permalink($this->get_zwall_page('ID')) ?>" target="_blank" >zWall</a> page</p>
  <h3 class="page-title">zShare<a href="http://support.zingsphere.com/zingsphere/topics/what_is_zshare?rfm=1"  title="What is zShare?" target="_blank" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE); ?>" /></a></h3>
	<a onclick="return false" class="zShareButton"
		 href="javascript:var
		 doc=document,
		 w=window,
		 e=w.getSelection,
		 k=doc.getSelection,
		 x=doc.selection,
		 selected=(e?e():(k)?k():(x?x.createRange().text:0)),
		 f='<?php echo admin_url('admin-ajax.php') ?>',
		 loc=doc.location,
		 e=encodeURIComponent,
		 u=f+'?action=zShare&u='+e(loc.href)+'&s='+e(selected);
		 zShare=function(){
		 if(!w.open(u,'zShare','toolbar=0,resizable=1,scrollbars=1,status=1,width=720,height=570')) {loc.href=u;w.document.title = 'zShare';}
		 };
		 if (/Firefox/.test(navigator.userAgent)) setTimeout(zShare, 0);
		 else zShare();void(0)">zShare</a>
	<h3 class="page-title">General settings</h3>
	<p>Rename zWall page </p>
	<p><input type="text" name="zwall_name" maxlength="15" value="<?php echo $this->get_zwall_page('post_title')?>" /></p>
	<input type="submit"  name="renamezWall" value="Submit"  />
	<h3 class="page-title">zPost</h3>
	<div class="p5L">
    <p<?php if($disabled) echo ' class="disabled"';?>>
			<span>
				<input type="checkbox" name="zBlogPost" <?php echo $this->plugin->get_option('zBlogPost') ? 'checked=""': '';
if($disabled) echo ' disabled'; ?> />
				Publish zWall statuses as blog posts
			</span>
    </p>
    <p><input type="submit" name="zBlogPostSubmit" value="Submit"/></p>
	</div>
	<h3 class="page-title">Social settings</h3>
	<div class="p5L">

		<!-- Facebook settings -->
		<p>
			<b>Facebook</b>
			<a href="http://support.zingsphere.com/zingsphere/topics/why_should_i_connect_zwall_with_my_facebook-2asa9"  target="_blank" title="Why should I connect zWall with my Facebook?" class="m5L">Why should I connect my zWall with my Facebook account?</a>
		</p>
		<div class="p5L">
<?php if($this->getFbConnected()): ?>
			<p<?php if($disabled) echo ' class="disabled"';?>>
				<span title="With this option, everything you write on your zWall will be published directly to your Facebook timeline, without even leaving your blog!">
					<input type="checkbox" name="social[publish_from_facebook]"<?php if($this->plugin->get_option('publish_from_facebook')) echo ' checked = "checked"';
	if($disabled) echo ' disabled'; ?> />
					Publish my Facebook posts on my zWall
					<a href="http://support.zingsphere.com/zingsphere/topics/what_is_this_publish_my_zwall_posts_to_my_facebook_account-166vri"  title="What is Publish my zWall posts to my Facebook account?" target="_blank" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE); ?>" /></a>
				</span>
			</p>
			<p<?php if($disabled) echo ' class="disabled"';?>>
				<span title="This option publishes only your Facebook status updates on your zWall. In that way, visitors of your blog can be aware of your social presence.">
					<input type="checkbox" name="social[publish_to_facebook]"<?php if($this->plugin->get_option('publish_to_facebook')) echo ' checked = "checked"';
	if($disabled) echo ' disabled'; ?> />
					Publish my zWall posts to my Facebook account
					<a href="http://support.zingsphere.com/zingsphere/topics/what_is_this_publish_my_facebook_posts_on_my_zwall-1inry6"  title="What is Publish my Facebook posts on my zWall? " target="_blank" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE); ?>" /></a>
				</span>
			</p>

			<?php else: ?>
				<?php
	$this->plugin->set_option('publish_from_facebook', 0);
				$this->plugin->set_option('publish_to_facebook', 0, true);
	?>
			<p<?php if($disabled) echo ' class="disabled"';?>>To enable publishing your Facebook posts on zWall please <a href="<?php echo $disabled ? '' : ZING_WWW_BASE . '/account'; ?>" target="_blank">connect</a> your Zingsphere account with your Facebook account.</p>
<?php endif; ?>
		</div>

		<!-- Twitter settings -->
		<p>
			<b>Twitter</b>
			<a href="http://support.zingsphere.com/zingsphere/topics/why_should_i_connect_zwall_with_my_twitter-cn5qd" target="_blank" title="Why should I connect zWall with my Twitter? " class="m5L">Why should I connect my zWall with my Twitter account?</a>
		</p>
		<div class="p5L">
<?php if($this->getTwitterConnected()): ?>
			<p<?php if($disabled) echo ' class="disabled"';?>>
				<span title="Anything you tweet from your phone or while you’re signed in on your Twitter account, will appear on your zWall. All your visitors can catch up with your thoughts in .zip style!">
					<input type="checkbox" name="social[publish_from_twitter]" <?php if($this->plugin->get_option('publish_from_twitter')) echo ' checked="checked"';
	if($disabled) echo ' disabled'; ?> />
					Publish my Tweets on my zWall
					<a href="http://support.zingsphere.com/zingsphere/topics/what_is_this_publish_my_tweets_on_my_zwall-t41s3"  target="_blank" title="What is Publish my Tweets on my zWall?" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE) ?>"/></a>
				</span>
			</p>
			<p<?php if($disabled) echo ' class="disabled"';?>>
				<span title="Activating this option will allow you to tweet instantly any idea that you may come up with while you’re blogging. Be sure to make it interesting in 140 characters or less!">
					<input type="checkbox" name="social[publish_to_twitter]" <?php echo $disabled ? 'disabled' : '';
				if($this->plugin->get_option('publish_to_twitter'))echo "checked='checked'" ?>/>
					Publish my zWall posts to my Twitter account
					<a href="http://support.zingsphere.com/zingsphere/topics/what_is_this_publish_my_zwall_posts_to_my_twitter_account-1n9b9g"  target="_blank" title="What is Publish my zWall posts to my Twitter account?" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE) ?>"/></a>
				</span>
			</p>
<?php else: ?>
				<?php
	$this->plugin->set_option('publish_from_twitter', 0);
	$this->plugin->set_option('publish_to_twitter', 0, true);
	?>
			<p<?php if($disabled) echo ' class="disabled"';?>>To enable publishing your Twitter timeline on zWall please <a href="<?php echo $disabled ? '' : ZING_WWW_BASE . '/account'?>" target="_blank">connect</a> your Zingsphere account with your Twitter account.</p>
<?php endif; ?>
		</div>
		<p><input type="submit" name="zWallSocialSettings" value="Save"/></p>
	</div>
	<div class="page-buttons"><input type="submit" name="deactivatezWall" value="Deactivate zWall"/></div>
</div>

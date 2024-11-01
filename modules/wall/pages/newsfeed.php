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

<!-- zWall news feeds -->
<div id="zWallNewsFeed" class="zWallPage" style="display: none;">

	<?php if($zingsphere_options['newsFeeds_active']): ?>

	<h3 id="zWallFeeds_subtabs" class="nav-tab-wrapper">
		<span id="feeds-tab" <?php if (!$disabled) echo 'class="nav-tab nav-tab-active" onClick="App.adminSubTabs(this);"'; else echo 'class="nav-tab"'; ?>>Customize</span>
		<span id="zs_feeds-tab" class="nav-tab"<?php if (!$disabled) {?>  onClick="App.adminSubTabs(this);" <?php } ?>>ZS feeds</span>
	</h3>

	<div id="zWallCustomize" class="zWallSubPage">

		<h3 class="page-title">Social feeds</h3>

		<div class="p5L">

			<!-- Facebook -->
				<?php if($this->plugin->modules['wall']->getFbConnected()): ?>
			<p>
				<span title="This option will show your Facebook news feed on zWall, but only to you and only while you’re signed in on your website.">
					<input type="checkbox" name="socialFeeds[publish_feeds_from_facebook]"<?php if($zingsphere_options['publish_feeds_from_facebook']) echo ' checked="checked"'; ?> />
					Show me my Facebook news feed on zWall
					<a href="http://support.zingsphere.com/zingsphere/topics/what_is_this_show_me_my_facebook_news_feed_on_my_zwall-bzkk7"  target="_blank" title="What is Show me my Facebook news feed on my zWall? " class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE); ?>" /></a>
				</span>
			</p>
				<?php else: ?>
			<p>To enable publishing your Facebook news feed on zWall please connect your Zingsphere <a href="<?php echo ZING_WWW_BASE . '/account'?>" target="_blank">account</a> with your Facebook account.</p>
				<?php endif; ?>


			<!-- Twitter -->
				<?php if($this->plugin->modules['wall']->getTwitterConnected()): ?>
			<p>
				<span title="This option will show your Twitter Timeline on zWall, but only to you and only while you’re signed in on your website.">
					<input type="checkbox" name="socialFeeds[publish_feeds_from_twitter]"<?php if($zingsphere_options['publish_feeds_from_twitter']) echo ' checked="checked"'; ?> />
					Show me my Twitter timeline on zWall
					<a href="http://support.zingsphere.com/zingsphere/topics/what_is_this_show_me_my_twitter_timeline_on_my_zwall-1ibqod" target="_blank" title="What is Show me my Twitter timeline on my zWall?" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE); ?>" /></a>
				</span>
			</p>
				<?php else: ?>
			<p>To enable publishing your Twitter news feed on zWall please connect your Zingsphere <a href="<?php echo ZING_WWW_BASE . '/account'?>" target="_blank">account</a> with your Twitter account.</p>
				<?php endif; ?>

			<p><input type="submit" name="zWallSocialFeeds" value="Save" /></p>

		</div>

	

		<div class="p5L">
			<hr/>
			<p><input type="submit" value="Deactivate zFeeds" name="deactivateNewsFeeds" /></p>

		</div>

	</div>

	<div id="zWall_ZS_feeds" class="zWallSubPage" style="display:none">

		<h3 class="page-title">Choose what you want to be published on your blog's wall<a href="http://support.zingsphere.com/zingsphere/topics/how_to_add_or_remove_zingsphere_feeds_on_your_zwall" target="_blank"  title="How to add or remove Zingsphere Feeds on your zWall?" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE); ?>"/></a></h3>

			<?php
			$settings = $this->plugin->modules['wall']->getWallSettings();
			if( ! empty($settings)):
				?>
		<div>
			<div style="float: left; border-right: 1px solid; margin-right: 10px; height: 100%;" id="zWallFeeds-settings_tabs">
				<ul>
							<?php foreach($settings AS $key => $val): ?>
					<li style="margin: 0; padding: 10px; cursor: pointer;" id="<?php echo 'zWall_' . str_replace(' ','', $key); ?>" onClick="App.feedsSettingsTabs('<?php echo 'zWall_' . str_replace(' ','', $key); ?>')"><?php echo $key; ?></li>
							<?php endforeach; ?>
				</ul>
			</div>
			<div id="zWallFeeds-settings_content" style="width: 60%; float: left;" >
						<?php foreach($settings AS $key => $val): ?>
				<div id="<?php echo 'zWall_' .  str_replace(' ','', $key) . '_content'; ?>" style="display:none;">
								<?php if(!empty($val)): ?>
					<table>
										<?php foreach($val AS $record): ?>
						<tr >
							<td width="1%" style="padding: 0 4px 0 10px;"><input type="checkbox" id="<?php echo $record['type'].$record['id']; ?>" name="settings[<?php echo $record['type'].']['.$record['id']; ?>]" value=1 <?php echo ( $record['publish'] == 1 ?  'checked="checked"' :'');
												echo ($disabled ? ' disabled="disabled"' : '')?>/></td>
					<?php if(isset($record['entanglement'])): ?>
							<td style="background:url(http://zingsphere.com/img/entanglement.png) no-repeat scroll 0px 6px transparent;width:50px !important;padding:0"><span style="padding: 12px 0px 14px 0px;display: block; font-size: 16px; font-weight: bold; text-align: center; color: #ffffff; background: url(http://zingsphere.com/img/entanglement.png) no-repeat -50px bottom;font-family: 'Helvetica Neue',Arial,Helvetica,'Nimbus Sans L',sans-serif;"><?php echo $record['entanglement']; ?></span></td>
					<?php endif; ?>
							<td width="1%" style="padding: 5px 5px;"><a href="<?php echo $record['url'] ?>" target="_blank"><img src="<?php echo $record['thumb'] ?>"  onerror="" width="60px" height="45px" style="border:1px solid"/></a></td>
							<td style="padding-left: 5px;"><a href="<?php echo $record['url'] ?>" style="font-weight: bold;text-decoration: none" target="_blank" ><?php echo $record['title'] ?></a><?php if (isset($record['user'])) { ?><br/>By: <a href="<?php echo ZING_WWW_BASE . '/account/'.$record['user_id'] ?>"><?php echo $record['user'];
											}?></a></td>
						</tr>
									<?php endforeach; ?>
					</table>
								<?php else: switch ($key) {
										case 'My Blogs': $text='Here you will see feeds from all <a href="'.ZING_WWW_BASE.'/user/blogs" target="_blank">your blogs</a> submitted to Zingsphere.  It won’t hurt to have them all at your hand.';
											break;
										case 'Following Blogs': $text='You are not following any blog yet. <a href="'.ZING_WWW_BASE.'/blogs" target="_blank">Search</a> through our blog base on Zingsphere, find the ones you like and don’t miss any post they publish.';
											break;
										case 'Blogs that follow me': $text='Being a member of Zingsphere doesn’t make your blog instantly recognisable. Defend what you like or criticise what you don’t like, draw other people’s attention and it might help you get your blog noticed.';
											break;
										case 'My Groups': $text='<a href="'.ZING_WWW_BASE.'/user/groups/create" target="_blank">Create</a>  your own group and invite your fellow bloggers to join them. Use groups to get the word-of-mouth for your blog!';
											break;
										case 'Member Groups': $text='You are not a member of any group yet. Don’t wait an invitation, but rather go and <a href="'.ZING_WWW_BASE.'/groups" target="_blank"> find groups</a> on Zingsphere that are interesting to you and ask for membership approval.';
											break;
										case 'Following Groups': $text='<a href="'.ZING_WWW_BASE.'/groups" target="_blank">Find and follow</a> groups on Zingsphere. Introduce yourself to the world and meet some new people!';
											break;
										case 'Following Users': $text='When you <a href="'.ZING_WWW_BASE.'" target="_blank">follow a user</a>, you’ll get feeds for all his posts that he wrote on all of his blogs. One stone, but many birds.';
											break;
										case 'Users that follow me': $text='Simply following other users on Zingsphere is not enough, you have to build relationships and give other people reason why they should follow you back. ';
											break;
										case 'Groups owned by users following me ': $text='You can choose to get feeds from <a href="'.ZING_WWW_BASE.'/groups/following/users" target="_blank"> groups</a> owned by users that are following you. Get a sneak peek over the fence and see where’s the party.';
											break;
										default:
						break;
									} ?>
					<p><?php echo $text?></p>
							<?php endif; ?>
				</div>
		<?php endforeach; ?>
			</div>
		</div>
		<div style="clear:both;"></div>

		<div>
			<p<?php if($disabled) echo ' class="disabled"';?>>
				<span title="With this option you can easily stay up to date with all the people that you’re following on Zingsphere. You will have to activate News feed feature in the first place in order to see what’s new on Zingsphere.">
					<input type="checkbox" name="follow_auto_publish"<?php if($zingsphere_options['follow_auto_publish']) echo ' checked="checked"';
		if($disabled) echo ' disabled'; ?> />
					When I follow some blog, automatically publish it on my News feed
				</span>
			</p>
		</div>
		<p><input type="submit" name="zFeeds" value="Submit"/></p>
	<?php else: ?>
		<p>No blogs returned. Please reload page to see list of avaliable blogs.</p>
	<?php endif; ?>

	</div>

<?php else: ?>

	<p><input type="submit" name="activateNewsFeeds" value="Activate zFeeds"/></p>

<?php endif; ?>  

</div>  

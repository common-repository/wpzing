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

<script type="text/javascript" src="<?php echo plugins_url('js/tinyscrollbar.js',ZING_PLUGIN_FILE)?>"></script>
<?php if($settings['theme']=='light'):?>
<link href="<?php print plugins_url('css/sticker-light.css',ZING_PLUGIN_FILE)?>" rel="stylesheet" type="text/css" />
<link href="<?php print plugins_url('css/scrollbar-light.css',ZING_PLUGIN_FILE)?>" rel="stylesheet" type="text/css" />
<?php else: ?>
<link href="<?php print plugins_url('css/sticker-dark.css',ZING_PLUGIN_FILE)?>" rel="stylesheet" type="text/css" />
<link href="<?php print plugins_url('css/scrollbar.css',ZING_PLUGIN_FILE)?>" rel="stylesheet" type="text/css" />
<?php endif;?>

<!--<div id="zWallStickerShadow"></div>-->
<div id="zWallStickerHeader"><div id="zWallStickerTitle">zWall sticker </div><a href="#" class="zStickerHeaderArrowDown"></a></div>

<div id="zWallStickerContent">
	<div id="zWallStickerSearch" style="display:none">
		<form id="zWallStickerSearchForm" action="" method="post" onSubmit="return App.search('<?php echo wp_create_nonce('wpzing-security-nonce')?>')" >
			<input type="text" name="search" id="zWallStickerSearchField" value="" placeholder="Search"/>
		</form>
	</div>

	<div id="zWallStickerWidgets">

		<?php if($settings[$privacy]['followers_widget']): ?>
		<div class="zWallStickerWidgetHeader"><a href="#"><img src="<?php echo plugins_url('images/sticker/arrow-right.png',ZING_PLUGIN_FILE)?>" /></a>Following</div>
		<div class="zWallStickerWidget <?php  if(!empty($followings)) print "zWallStickerWidgetScroll" ?>">
      <div class="zWallStickerWidgetFlash"></div>
			<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
			<div class="viewport">
				<div class="overview"><?php  if(!empty($followings)): ?>
						<?php foreach($followings AS $blog): ?>
					<div class="blog">
						<div class="avatar"><img src="<?php print $blog['thumb'] ?>" alt=""/></div>
						<div class="title">
							<a href="<?php print $blog['profile'] ?>" target="_blank"> <?php print strlen($blog['title']) < 20 ? $blog['title'] : substr($blog['title'], 0,20).'...' ?></a>
						</div>
						<?php /* <div class="follow"><a href="Javascript:void(0)"><img src="<?php print plugins_url('images/sticker/follow.png',ZING_PLUGIN_FILE) ?>"/></a></div> */ ?>
						<div class="link"><a href="<?php print $blog['url'] ?>" target="_blank">Go to blog</a></div>
					</div>
          <div class="divide"></div>
						<?php endforeach; ?>
					<?php else: ?>
					<p class="zWallStickerEmpty">This blog is not following any blog</p>
					<?php endif?>
				</div>
			</div>

		</div>

		<div class="zWallStickerWidgetHeader"><a href="#"><img src="<?php echo plugins_url('images/sticker/arrow-right.png',ZING_PLUGIN_FILE)?>" /></a>Followers</div>
		<div class="zWallStickerWidget <?php  if(!empty($followers)) print "zWallStickerWidgetScroll"?>">
      <div class="zWallStickerWidgetFlash"></div>
			<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
			<div class="viewport">
				<div class="overview">
					<?php  if(!empty($followers)): ?>
						<?php foreach($followers AS $blog): ?>
					<div class="blog">
						<div class="avatar"><img src="<?php print $blog['thumb'] ?>" alt=""/></div>
						<div class="title"><a href="<?php print $blog['profile'] ?>" target="_blank"> <?php print strlen($blog['title']) < 20 ? $blog['title'] : substr($blog['title'], 0,20).'...' ?></a></div>
						<?php if($this->canUserManageWall() && (!$followings || is_array($followings) && !array_key_exists($blog['id'], $followings))): ?>
						<div id="follow<?php print $blog['id'] ?>" class="follow"><a href="Javascript:void(0)" onclick="zSticker.follow(<?php print $blog['id'] ?>)"><img src="<?php print plugins_url('images/sticker/follow.png',ZING_PLUGIN_FILE) ?>"/></a></div>
						<?php endif; ?>
						<div class="link"><a href="<?php print $blog['url'] ?>" target="_blank">Go to blog</a></div>
						<div class="zWallClear"></div>
					</div>
          <div class="divide"></div>
						<?php endforeach; ?>
					<?php else: ?>
					<p class="zWallStickerEmpty">This blog has no followers</p>
					<?php endif?>
				</div>
			</div>

		</div>
    <?php endif ?>
    <?php  if($this->canUserManageWall() &&  $settings['admin']['similar_blogs']):?>		<!--		</div>-->
    <div class="zWallStickerWidgetHeader"><a href="#"><img src="<?php echo plugins_url('images/sticker/arrow-right.png',ZING_PLUGIN_FILE)?>" /></a>Similar Blogs </div>
		<div class="zWallStickerWidget <?php  if(!empty($similar)) print "zWallStickerWidgetScroll" ?>">
      <div class="zWallStickerWidgetFlash"></div>
			<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
			<div class="viewport">
				<div class="overview">
					<?php  if(!empty($similar)): ?>
						<?php foreach($similars AS $blog): ?>
					<div class="blog">
						<div class="avatar"><img src="<?php print $blog['thumb'] ?>" alt=""/></div>
						<div class="title"><a href="<?php print $blog['profile'] ?>" target="_blank"> <?php print strlen($blog['title']) < 20 ? $blog['title'] : substr($blog['title'], 0,20).'...' ?></a></div>
						<div class="link"><a href="<?php print $blog['url'] ?>" target="_blank">Go to blog</a></div>
						<div class="follow"></div>
						<div class="zWallClear"></div>
					</div>
          <div class="divide"></div>
						<?php endforeach; ?>
					<?php else: ?>
					<p class="zWallStickerEmpty">No simmilar blogs at the moment</p>
					<?php endif?>
				</div>
			</div>

		</div>
    <?php endif?>
		<div id="zWallStickerFooter">
			<ul>
        <?php if($this->canUserManageWall()): ?>
				<li class="zWallStickerFooterDivide admin"><a href="<?php print ZING_WWW_BASE.'/blog/'.$this->plugin->get_option('blog_mangle') ?>" title="See blog profile on Zingsphere"><img src="<?php echo plugins_url('images/sticker/home.png',ZING_PLUGIN_FILE)?>" alt=""></a></li>
				<li class="zWallStickerFooterDivide admin"><a href="<?php print ZING_WWW_BASE.'/blogs' ?>"  target="_blank" title="Find blogs on Zingsphere"><img src="<?php echo plugins_url('images/sticker/search.png',ZING_PLUGIN_FILE)?>" alt=""></a></li>
				<li class="admin"><a href="/wp-admin/admin.php?page=wpzing&tab=zwall" title="Go to zWall settings"><img src="<?php echo plugins_url('images/sticker/settings.png',ZING_PLUGIN_FILE)?>" alt=""></a></li>
				<?php else: ?>
        <li class="zWallStickerFooterDivide"><a href="<?php print ZING_WWW_BASE.'/blog/'.$this->plugin->get_option('blog_mangle') ?>" title="See blog profile on Zingsphere"><img src="<?php echo plugins_url('images/sticker/home.png',ZING_PLUGIN_FILE)?>" alt=""></a></li>
				<li><a href="<?php print ZING_WWW_BASE.'/blogs' ?>"  target="_blank" title="Find blogs on Zingsphere"><img src="<?php echo plugins_url('images/sticker/search.png',ZING_PLUGIN_FILE)?>" alt=""></a></li>
        <?php endif; ?>
			</ul>
		</div>
	</div>
	<script type="text/javascript" src="<?php print plugins_url('js/sticker.js', ZING_PLUGIN_FILE)?>"></script>
	<script type="text/javascript">
		var api = '<?php print ZING_API_BASE ?>';
		if(jQuery('.zWallStickerWidgetScroll').length){
			jQuery('.zWallStickerWidgetScroll').tinyscrollbar();
			jQuery('.zWallStickerWidgetScroll').tinyscrollbar_update();
		}
		<?php
		$options = $this->plugin->get_options();
		$minimized = isset($options['zstickerSettings']) && isset($options['zstickerSettings']['general']) && isset($options['zstickerSettings']['general']['minimized']) && $options['zstickerSettings']['general']['minimized'];
		$autotoggle = isset($options['zstickerSettings']) && isset($options['zstickerSettings']['general']) && isset($options['zstickerSettings']['general']['autotoggle']) && $options['zstickerSettings']['general']['autotoggle'];
		?>
		zSticker.init(<?php print $minimized ? 'true' : 'false' ?>, <?php print $autotoggle ? 'true' : 'false' ?>);
	</script>

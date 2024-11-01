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

<!-- zWall sticker settings -->
<div class="zWallPage" style="display: none;" id="zSticker-settings">
  <p>zSticker is a handy sidebar tool for monitoring activity on your zWall and for discovering new, interesting blogs on Zingsphere effortless. It can also help you promote your blog and attract more followers. Customize it in a way that suits you the best! Choose which options will be displayed publicly and which ones will be for your eyes only.</p>
	<h3 class="page-title">General settings</h3>
	<div class="p5L">
		<p><input type="checkbox" name="zsticker_enable" id="zsticker_enable" <?php print $zingsphere_options['zsticker_enable'] ? 'checked="checked"' : ''?>/><span title="Enabling this feature will make your widgets visible on your zWall as well. Check for additional options bellow to customize its appearance."> Enable zSticker</p>
		<div class="p5L">
			<p class="zStickerSub"><span title=" Use this option if you think that displaying widgets on your zWall might distract your visitors from following your own social footprint."><input type="checkbox" name="zstickerSettings[general][minimized]" id="zsticker_minimized" <?php print $zingsphere_options['zstickerSettings']['general']['minimized'] ? 'checked="checked"' : ''?> <?php print $zingsphere_options['zsticker_enable'] ? '' : 'disabled="disabled"'?>/> Display minimized by default</span></p>
			<p class="zStickerSub"><span title="Enabling this option will prevent zSticker from overlapping your blog theme and its content. This is a great option if you care about tablet and laptop users!"><input type="checkbox" name="zstickerSettings[general][autotoggle]" id="zsticker_show" <?php print $zingsphere_options['zstickerSettings']['general']['autotoggle'] ? 'checked="checked"' : ''?> <?php print $zingsphere_options['zsticker_enable'] ? '' : 'disabled="disabled"'?>/> Auto toggle by default</span></p>
		</div>
	</div>
	<h3 class="page-title">Choose zSticker theme</h3>
	<p class="zStickerSub"> <input type="radio" name="zstickerSettings[theme]" value="light" <?php print $zingsphere_options['zstickerSettings']['theme']=='light' ? 'checked' : '' ?>  <?php print $zingsphere_options['zsticker_enable'] ? '' : 'disabled="disabled"'?>/><img src="<?php print plugins_url('images/sticker/light_theme.png',ZING_PLUGIN_FILE) ?>" style="margin: 0 5px -5px 5px;"/>  Light theme </p>
	<p class="zStickerSub"><input type="radio" name="zstickerSettings[theme]" value="dark"   <?php print $zingsphere_options['zstickerSettings']['theme']=='dark' ? 'checked' : '' ?>     <?php print $zingsphere_options['zsticker_enable'] ? '' : 'disabled="disabled"'?>/><img src="<?php print plugins_url('images/sticker/dark_theme.png',ZING_PLUGIN_FILE) ?>" style="margin: 0 5px -5px 5px;"/>  Dark theme </p>
	<h3 class="page-title">User settings</h3>
	<p>Choose what option will appear to you as a user:</p>
<!--    <p class="zStickerSub"><input type="checkbox" name="zstickerSettings[admin][recent_comments]"  <?php print $zingsphere_options['zstickerSettings']['admin']['recent_comments'] ? 'checked' : '' ?>   <?php print ($zingsphere_options['zsticker_enable'] ) ? '' : 'disabled="disabled"'?>/> Display Recent Comments from zWall</p>-->
	<p class="zStickerSub"><input type="checkbox" name="zstickerSettings[admin][followers_widget]" <?php print $zingsphere_options['zstickerSettings']['admin']['followers_widget'] ? 'checked' : '' ?>  <?php print $zingsphere_options['zsticker_enable'] ? '' : 'disabled="disabled"'?>/> Display Followers/Following widget</p>
	<p class="zStickerSub"><input type="checkbox" name="zstickerSettings[admin][similar_blogs]"    <?php print $zingsphere_options['zstickerSettings']['admin']['similar_blogs'] ? 'checked' : '' ?>     <?php print $zingsphere_options['zsticker_enable'] ? '' : 'disabled="disabled"'?>/> Display Similar blogs widget</p>
	<h3 class="page-title">Public settings</h3>
	<p>Enable public zSticker</p>
	<p class="zStickerSub"><input type="checkbox" name="zstickerSettings[public][enable]" id="public_enable"  <?php print $zingsphere_options['zstickerSettings']['public']['enable'] ? 'checked' : '' ?>           <?php print $zingsphere_options['zsticker_enable'] ? '' : 'disabled="disabled"'?>/> Enable public zSticker</p>
	<div class="p5L">
<!--    <p class="zStickerSub"><input type="checkbox" name="zstickerSettings[public][recent_comments]"            <?php print $zingsphere_options['zstickerSettings']['public']['recent_comments'] ? 'checked' : '' ?>  <?php print (!$zingsphere_options['zsticker_enable'] || !$zingsphere_options['zstickerSettings']['public']['enable']) ? 'disabled="disabled"' : ''?>/> Display Recent Comments from zWall</p>-->
    <p class="zStickerSub"><input type="checkbox" name="zstickerSettings[public][followers_widget]"           <?php print $zingsphere_options['zstickerSettings']['public']['followers_widget'] ? 'checked' : '' ?> <?php print (!$zingsphere_options['zsticker_enable'] || !$zingsphere_options['zstickerSettings']['public']['enable']) ? 'disabled="disabled"' : ''?>/> Display Followers/Following widget</p>
	</div>
	<div class="page-buttons"><input type="submit" name="zStickerSubmit" value="Save" /></div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function( $ ) {
<?php  print $zingsphere_options['zsticker_enable'] ? '$(".zStickerSub").css({color:"#333333"});' : '$(".zStickerSub").css({color:"#AAAAAA"});'?>
				jQuery("#zsticker_enable").click(function(){
					if(!$("#zsticker_enable").is(":checked")){
            $("#zSticker-settings input[name^=zstickerSettings]").attr('disabled', 'disabled');
            $(".zStickerSub").css({color:'#AAAAAA'});
					} else {
            $("#zSticker-settings input[name^=zstickerSettings]").removeAttr('disabled');
            $(".zStickerSub").css({color:'#333333'});
            if(!$('#public_enable').is(':checked')){
							$('input[name="zstickerSettings[public][recent_comments]"]').attr('disabled', 'disabled');
							$('input[name="zstickerSettings[public][recent_comments]"]').parent().css({color:'#AAAAAA'});
							$('input[name="zstickerSettings[public][followers_widget]"]').attr('disabled', 'disabled');
							$('input[name="zstickerSettings[public][followers_widget]"]').parent().css({color:'#AAAAAA'});
            }
					}
				});

				jQuery('#public_enable').click(function(){

          if(!$('#public_enable').is(':checked')){
            $('input[name="zstickerSettings[public][recent_comments]"]').attr('disabled', 'disabled');
            $('input[name="zstickerSettings[public][recent_comments]"]').parent().css({color:'#AAAAAA'});
            $('input[name="zstickerSettings[public][followers_widget]"]').attr('disabled', 'disabled');
            $('input[name="zstickerSettings[public][followers_widget]"]').parent().css({color:'#AAAAAA'});
          } else{
            $('input[name="zstickerSettings[public][recent_comments]"]').removeAttr('disabled');
            $('input[name="zstickerSettings[public][recent_comments]"]').parent().css({color:'#333333'});
            $('input[name="zstickerSettings[public][followers_widget]"]').removeAttr('disabled');
            $('input[name="zstickerSettings[public][followers_widget]"]').parent().css({color:'#333333'});
          }
				});
			});
</script>
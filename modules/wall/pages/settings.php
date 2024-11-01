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

<!-- zWall settings -->
<div id="zWallSettings" class="zWallPage" style="display: none;">

	<h3 id="zWallSettings_subtabs" class="nav-tab-wrapper">
		<span id="zWallMenuChoose-tab" class="nav-tab nav-tab-active" onClick="App.adminSubTabs(this);">WP Settings</span>
		<span id="zWallStyle-tab" class="nav-tab"<?php if ( ! $disabled): ?> onClick="App.adminSubTabs(this);"<?php endif; ?>>zWall Styling</span>
	</h3>

	<div id="zWallWpSettings" class="zWallSubPage">
		<h3 class="page-title">Managing permissions</h3>
		<p>
			<span title="If you have a certain number of content creators with different roles on your blog/website, define which roles will have permission to manage your zWall.">
				<b>Select which users can manage wall</b>
				<a href="http://support.zingsphere.com/zingsphere/topics/ce871ehok1xlu"  target="_blank" title="User roles – who can manage my zWall" class="m5L"><img src="<?php echo plugins_url('images/question-mark.png', ZING_PLUGIN_FILE); ?>" /></a>
			</span>
		</p>
		<p><input type="checkbox" name="user[editor]"<?php if(in_array('editor', $zingsphere_options['user_roles'])): ?> checked="checked"<?php endif; ?> /> Editors</p>
		<p><input type="checkbox" name="user[author]"<?php if(in_array('author', $zingsphere_options['user_roles'])): ?> checked="checked"<?php endif; ?> /> Authors</p>
		<p><input type="checkbox" name="user[contributor]"<?php if(in_array('contributor', $zingsphere_options['user_roles'])): ?> checked="checked"<?php endif; ?> /> Contributors</p>
		<p><input type="submit" value="Save" name="zWallUserRoles"/></p>

		<?php if(!empty($menus)): ?>
		<h3 class="page-title">Menu selection</h3>
		<p><b>Select in which menu you want to put zWall link</b></p>
			<?php foreach($menus as $menu): ?>
		<p><input type="radio" name="zWall_menu_select" value="<?php echo $menu->term_id; ?>" <?php if($zingsphere_options['menu_choosen'] && $zingsphere_options['menu_choosen'] == $menu->term_id) echo 'checked'?> /> <?php echo $menu->name ?></p>
			<?php endforeach; ?>
		<p><input type="radio" name="zWall_menu_select" value="none" <?php if($zingsphere_options['menu_choosen'] && $zingsphere_options['menu_choosen'] == 'none') echo 'checked'; ?> /> None</p>
		<p><input type="submit" name="zWallMenuSelect" value="Save"/></p>
		<?php endif; ?>

	</div>

	<div id="zWallStyle" class="zWallSubPage" style="display: none">
		<h3 class="page-title">Styling</h3>
		<textarea rows="80" cols="180" name="zWallCss" style="font-family: Courier;">
			<?php echo $this->plugin->modules['wall']->getWallCss(); ?>
		</textarea>
		<p><input type="submit" name="zWallSaveCss" value="Save" /></p>
	</div>

</div>

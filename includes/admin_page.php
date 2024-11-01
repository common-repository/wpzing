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
<style type="text/css">
#zing_logo{background: url('<?php echo plugins_url('images/zing_logo.png', ZING_PLUGIN_FILE); ?>') no-repeat top left;float: left;height: 34px;margin: 7px 8px 0 0;width: 32px;}  
</style>
<div class="wrap">
	<form name="zing_form" method="post" action="<?php echo admin_url('admin.php?page=wpzing&tab='.$this->modules[$this->admin_current_tab]->admin_tab['name'])?>" enctype="multipart/form-data">
		<h2>Zingsphere Plugin Settings</h2>
		<?php
		if(!empty($this->admin_tabs)) {
			echo '<div id="zing_logo"></div>';
			echo '<h2 class="nav-tab-wrapper">';
			foreach($this->admin_tabs AS $tab) {
				$module = $this->modules[$tab];
				if($module->is_active()) {
					$class = $module->name == $this->admin_current_tab ? ' nav-tab-active' : '';
					echo "<a class='nav-tab$class' href='".admin_url('admin.php?page=wpzing&tab='.$module->admin_tab['name'])."'>".$module->admin_tab['title']."</a>";
				} else {
					echo "<a class='nav-tab' style='color:#C1C1C1'>".$module->admin_tab['title']."</a>";
				}
			}
			echo '</h2>';

			//$module = $this->modules[$this->admin_current_tab];
			$this->modules[$this->admin_current_tab]->admin();
		}


		?>
	</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){setTimeout(function(){jQuery('#zingMessage').slideUp(2000,function(){})},5000)})
</script>

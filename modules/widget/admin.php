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

class Available_Widget_Table extends WP_List_Table {

	// Vars
	var $plugin = null;

	// Constructor
	function __construct(&$plugin) {
		parent::__construct(array('singular' => 'widget', 'plural' => 'widgets', 'ajax' => false));
		$this->plugin = $plugin;
	}

	// Name column
	function column_name($item) {
		return sprintf('<a href="%s/blog/%s/widget/add/%s" class="row-title" target="_blank">%s</a>', ZING_API_URL, $this->plugin->get_option('blog_api_token'), $item['id'], $item['name']);
	}

	function column_learn($item) {
		return sprintf('<a href="javascript:void(0)" onclick="learn(%s)"><span class="zslearn" id="zslearn%s">Learn more</span></a></td></tr><tr id="zshelp%s" class="zshelp" style="display:none"><td colspan="2"><img src="%s%s" style="float:left;padding-right:10px">%s', $item['id'], $item['id'], $item['id'], ZING_WWW_BASE, $item['image'], $item['text']);
	}
	// Get columns
	function get_columns() {
		return $columns = array('name' => 'Available widgets', 'learn' => 'About widget');
	}

	// Prepare items
	function prepare_items($items = array()) {
		// Prep columns
		$columns = $this->get_columns();
		$this->_column_headers = array($columns, array());
		// Fetch items
		$this->items = $items;
	}

} // Available_Widget_Table

class Widget_Table extends WP_List_Table {

	// Vars
	var $plugin = null;

	// Constructor
	function __construct(&$plugin) {
		parent::__construct(array('singular' => 'widget', 'plural' => 'widgets', 'ajax' => false));
		$this->plugin = $plugin;
	}

	// Name column
	function column_name($item) {
		if($item['status']) {
			$actions = array(
							'edit' => sprintf('<a href="%s/blog/%s/widget/%s/edit" target="_blank">Edit</a>', ZING_API_URL, $this->plugin->get_option('blog_api_token'), $item['id']),
							'delete' => sprintf('<a href="%s/blog/%s/widget/%s/delete" target="_blank">Delete</a>', ZING_API_URL, $this->plugin->get_option('blog_api_token'), $item['id'])
			);
		} else {
			$actions = array(
							'install' => sprintf('<a href="%s">Install</a>', get_admin_url() . 'widgets.php')
			);
		}
		return sprintf('<a href="%s/blog/%s/widget/%s/edit" class="row-title" target="_blank">%s</a>%s', ZING_API_URL, $this->plugin->get_option('blog_api_token'), $item['id'], $item['name'], $this->row_actions($actions));
	}

	// Status column
	function column_status($item) {
		return $item['status'] ? 'Installed' : 'Not installed <a href="http://support.zingsphere.com/zingsphere/topics/my_widget_status_is_not_installed_what_should_i_do?rfm=1" target="_blank"><img src="' . plugins_url('images/question-mark.png', ZING_PLUGIN_FILE) . '" /></a>';
	}

	// Get columns
	function get_columns() {
		return $columns = array('name' => 'Created widgets', 'status' => 'Status');
	}

	// Get sortable columns
	public function get_sortable_columns() {
		return array();
	}

	// Prepare items
	function prepare_items($items = array()) {
		// Fetch items
		$this->items = $items;
		// Prep columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
	}

} // Widget_Table

// If account is active
if($this->is_active()):

// Prep table handler
	$wTable = new Widget_Table($this->plugin);
	$awTable = new Available_Widget_Table($this->plugin);
	$result = $this->plugin->zingsphere_api_call('listWidgets');
	if( is_wp_error($result) ): ?>
<script type="text/javascript">window.location = '<?php echo get_admin_url().'admin.php?page=wpzing&tab=account&reconnect'; ?>';</script>
	<?php else: ?>
<div id="col-container">
			<?php
			if(isset($result['widgetTypes'])) {
				$awTable->prepare_items($result['widgetTypes']);
				?><div id="col-right"><div class="col-wrap"><?php $awTable->display(); ?></div></div>
	<script type="text/javascript">
		function learn(id){
			if(jQuery('#col-right #zshelp'+id).css('display') == 'none') {
				jQuery('#col-right .zslearn').html('Learn more');
				jQuery('#col-right .zshelp').css({'display':'none'});
				jQuery('#col-right #zslearn'+id).html('Close');
				jQuery('#col-right #zshelp'+id).css({'display':'table-row'});
			} else {
				jQuery('#col-right .zslearn').html('Learn more');
				jQuery('#col-right .zshelp').css({'display':'none'});
			}
		}
	</script>
				<?php
			}
			if(isset($result['listWidgets'])) {
				$wTable->prepare_items($result['listWidgets']);
				?><div id="col-left"><div class="col-wrap"><?php $wTable->display(); ?></div>

    <div class="alignleft actions">
			<a href="<?php echo ZING_API_URL.'/blog/'.$this->plugin->get_option('blog_api_token').'/widget/add' ?>" target="_blank" class="alignleft button-secondary">Create new widget</a>
    </div>

	</div><?php
			}
			?>
	<div class="tablenav bottom">
    <div class="alignleft actions">
			<a href="" class="alignleft button-secondary">Refresh</a>
    </div>
	</div>
</div>
	<?php endif; ?>
<?php else: ?>
<h3 style="font-weight: normal;">To use the plug-in, you must first connect to your Zingsphere account.</h3>
<script type="text/javascript">window.location = '<?php echo get_admin_url().'admin.php?page=wpzing&tab=account&reconnect'; ?>';</script>
<?php endif; ?>
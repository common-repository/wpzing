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

/**
 * Zingsphere Widget Class
 */
class ZingsphereWidget extends WP_Widget {

	/**
	 * Method declares the ZingsphereWidget class
	 */
	function ZingsphereWidget($widget_id=null) {
		$widget_ops = array('classname' => 'zingsphere_widget', 'description' => __( "Zingsphere widgets for your blog") );
		$control_ops = array('width' => 200, 'height' => 300);
		$name = 'Zingsphere';
		if($widget_id !== null) {
			$widgets = $zingsphere_object->zingsphere_api_call('listWidgets');
			if(!is_wp_error($widgets) && isset($widgets['listWidgets'])) {
				foreach ($widgets['listWidgets'] as $widget) {
					if(isset($widget['id']) && $widget['id'] == $widget_id) {
						//$name .= ' - '.$widget['name'];
					}
				}
			}
			$control_ops['blog_widget_id'] = $widget_id;
		}
		$this->WP_Widget('zingsphere', __($name), $widget_ops, $control_ops);
	} // ZingsphereWidget

	/**
	 * Method displays the Widget
	 */
	function widget($args, $instance) {
		extract($args);

		if(!empty($instance['blog_widget_html'])) {
			// Before the widget
			echo $before_widget;

			// The title
			if ( $title )
				echo $before_title . $title . $after_title;

			echo $instance['blog_widget_html'];

			// After the widget
			echo $after_widget;
		} // if
	} // widget

	/**
	 * Method saves the widgets settings
	 */
	function update($new_instance, $old_instance) {
		global $zingsphere_object;
		$instance = $old_instance;

		if(!empty($new_instance['blog_widget_id'])) {
			$instance['blog_widget_id'] = $new_instance['blog_widget_id'];
			$result = $zingsphere_object->zingsphere_api_call('getWidgetHtml', array('widget_id' => $new_instance['blog_widget_id']));
			if(is_wp_error($result))
				$instance['error'] = $result->get_error_message();
			else if(isset($result['widgetHtml'])) {
				$instance['blog_widget_html'] = $result['widgetHtml'];
				unset($instance['error']);
				$zingsphere_object->zingsphere_api_call('scanBlog');
			} else
				$instance['error'] = 'An unknown error occurred.';
		} // if

		return $instance;
	}

	function save_settings($settings) {
		global $zingsphere_object;
		parent::save_settings($settings);
		if($zingsphere_object)
			$zingsphere_object->zingsphere_api_call('scanBlog');
	}

	static function getWidgetHtml($blog_widget_id) {
		global $zingsphere_object;
		$result = $zingsphere_object->zingsphere_api_call('getWidgetHtml', array('widget_id' => $blog_widget_id));
		if(!is_wp_error($result) && isset($result['widgetHtml']))
			return $result['widgetHtml'];
		return '';
	}

	/**
	 * Method creates the edit form for the widget.
	 */
	function form($instance) {
		global $zingsphere_object;

		if(!$zingsphere_object->get_option('blog_api_token')) {
			print '<p style="background-color: #f2f2f2; color: #555; padding: 2px 4px; border: 1px solid red;">Click <a href="'.admin_url('admin.php?page=wpzing').'">here</a> to configure Zingsphere plugin</p>';
		} else {
			if(!empty($instance['error']))
				print '<p style="background-color: #f2f2f2; color: #555; padding: 2px 4px; border: 1px solid red;">' . $instance['error'] . '</p>';
			$widgets = $zingsphere_object->zingsphere_api_call('listWidgets');
			if(is_wp_error($widgets)) {
				print '<p style="background-color: #f2f2f2; color: #555; padding: 2px 4px; border: 1px solid red;">' . $widgets['error_str'] . '</p>';
			}
			else if(isset($widgets['listWidgets'])) {
				if(!empty($widgets['listWidgets'])) {
					print '<label for="blogApiToken" style="display: block; margin: 0; padding: 5px 0 2px 0; color: #444444;">Select existing widget:</label>';
					print '<select id="' . $this->get_field_id('id') . '-widgets" name="' . $this->get_field_name('blog_widget_id') . '" style="width: 100%">';
					$title = '';
					foreach ($widgets['listWidgets'] as $widget) {
						if(!empty($widget['id'])) {
							if($instance['blog_widget_id'] == $widget['id']) {
								$title = $widget['name'];
								print '<option value="' . $widget['id'] . '" selected="selected">' . $widget['name'] . '</option>';
							} else {
								print '<option value="' . $widget['id'] . '">' . $widget['name'] . '</option>';
							} // if
						} // if
					} // foreach
					print '</select>';
					print '<input type="hidden" id="'.$this->get_field_id('id').'-title" value="'.$title.'" />';
				} else {
					print '<p style="background-color: #f2f2f2; color: #555; padding: 2px 4px; border: 1px solid red;">Click <a href="'.ZING_API_URL.'/blog/'.$zingsphere_object->get_option('blog_api_token').'/widget/add/6" target="_blank">here</a> to create widgets for your blog.</p>';
				} // if
				if(isset($widgets['widgetTypes']) && !empty($widgets['widgetTypes'])) {
					print '
<script type="text/javascript">
if(typeof zscolapse == "undefined"){
  function zscolapse(id){
    if(jQuery("#zswidgettypes-"+id).css("display") == "none")
		  jQuery("#zswidgettypes-"+id).show();
		else
		  jQuery("#zswidgettypes-"+id).hide();
  }
}
</script>
';
					print '<p style="margin: 0; padding: 10px 0 5px 0; color: #444444;"><a href="javascript:void(0)" onclick="zscolapse(\''.$this->get_field_id('id').'\')">Create new widget</a></p>';
					print '<div class="hide-if-js" id="zswidgettypes-'.$this->get_field_id('id').'">';
					foreach($widgets['widgetTypes'] AS $w)
						print '<p style="margin: 0; padding: 7px 0; border-bottom: 1px solid #ffffff; border-top: 1px solid #d6d6d6;"><a href="'.ZING_API_URL.'/blog/'.$zingsphere_object->get_option('blog_api_token').'/widget/add/'.$w['id'].'" target="_blank" style="text-decoration: none;">'.$w['name'].'</a></p>';
					print '</div>';
				}
			} // if
		} // if
	} // form

} // ZingsphereWidget

?>

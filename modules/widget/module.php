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

require_once('widget.php');

/**
 * Widget Module
 */
class WidgetModule extends ZingsphereModule {


	/**
	 * Constructor
	 * @param <ZingspherePlugin> $plugin
	 */
	public function  __construct($plugin) {
		$this->plugin = $plugin;
		$this->name = 'widget';
		$this->dependecies = array('zing');
		$this->admin_tab = array('name' => 'widget', 'title' => 'Widgets', 'weight' => 20);
		$zingsphere_options = get_option('zingsphere_options');
	}

	/**
	 * Method initializes plugin module
	 */
	public function init() {
		if($this->is_active()) {
			add_action('widgets_init', array(&$this, 'widget_init'));
			add_action('wp_footer', array(&$this, 'display_invisable_widget'));
		}
	}

	/**
	 * Method checks if module is active
	 * @return <boolean>
	 */
	public function is_active() {
		parent::is_active();
		$zingsphere_options = get_option('zingsphere_options');
		$this->active &= $zingsphere_options && isset($zingsphere_options['blog_api_token']) && $zingsphere_options['blog_api_token'];
		return $this->active;
	}

	/**
	 * Method called when plugin is connected
	 * @return <string>
	 */
	public function zingsphere_connect() {
		$widgets = $this->plugin->zingsphere_api_call('listWidgets');
		if(is_wp_error($widgets))
			return $widgets->get_error_message();
		else if(isset($widgets['listWidgets'])) {
			echo '<script type="text/javascript">window.location="'.get_admin_url().'admin.php?page=wpzing&tab=widget'.'"</script>';
		}
		return null;
	}

	/**
	 * Method called when plugin is disconnected
	 */
	public function zingsphere_disconnect() {
		WidgetModule::uninstall_widgets();
	}

	/**
	 * Method called on plugin deactivation
	 */
	public function zingsphere_deactivate() {
		WidgetModule::uninstall_widgets();
	}
	/**
	 * Method registers widget
	 */
	function widget_init() {
		register_widget('ZingsphereWidget');
	}

	/**
	 * Method installs specified zingshere widget
	 * @global <array> $wp_registered_widgets
	 * @staticvar <int> $number
	 * @param <int> $widget_id
	 * @param <string> $sidebar
	 */
	public static function install_widget($widget_id, $sidebar='') {
		global $wp_registered_widgets;
		static $number = 1;
		if($number == 1) {
			foreach($wp_registered_widgets as $w_id => $w) {
				if(preg_match('/zingsphere-([0-9]+)$/', $w_id, $matches))
					$number = max($number, $matches[1]);
			}
		}
		$number++;
		$settings = get_option('widget_zingsphere');
		$settings['_multiwidget'] = 1;
		$settings[$number] = array('blog_widget_id' => $widget_id, 'blog_widget_html' => ZingsphereWidget::getWidgetHtml($widget_id));
		update_option('widget_zingsphere', $settings);
		$sidebars = get_option('sidebars_widgets');
		if($sidebar) {
			$sidebars[$sidebar][] = 'zingsphere-'.$number;
			update_option('sidebars_widgets', $sidebars);
		} else {
			foreach($sidebars AS $key => $val) {
				if(!preg_match('/inactive/', $key)) {
					$sidebars[$key][] = 'zingsphere-'.$number;
					update_option('sidebars_widgets', $sidebars);
					break;
				}
			}
		}
	}

	/**
	 * Method checks if specified widget is installed
	 * @param <int> $id
	 * @param <string> $sidebar
	 * @return <boolean>
	 */
	public static function widget_installed($id, $sidebar='') {
		$widgets = get_option('widget_zingsphere');
		$sidebar_widget_id = 0;
		foreach($widgets AS $w_id => $widget) {
			if($widget['blog_widget_id'] == $id) {
				$sidebar_widget_id = $w_id;
				break;
			}
		}
		if($sidebar_widget_id) {
			$sidebars = wp_get_sidebars_widgets();
			foreach($sidebars AS $k => $s) {
				if((!$sidebar || $sidebar == $k) && in_array('zingsphere-'.$sidebar_widget_id, $s)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Method uninstalls all widgets
	 */
	public static function uninstall_widgets() {
		$sidebars = wp_get_sidebars_widgets();
		$widget_zingsphere = get_option('widget_zingsphere');
		foreach($sidebars AS $skey => $widgets) {
			if(is_array($widgets) && !empty($widgets)) {
				foreach($widgets AS $wkey => $widget) {
					if(substr($widget, 0, 10) == 'zingsphere') {
						$id = substr($widget, 11);
						unset($widget_zingsphere[$id]);
						unset($sidebars[$skey][$wkey]);
					}
				}
			}
		}
		update_option('widget_zingsphere', $widget_zingsphere);
		wp_set_sidebars_widgets($sidebars);
		delete_option("widget_zingsphere");
	}

	/**
	 * Method displays invisible widget in page footer
	 */
	public function display_invisable_widget() {
		$widget = $this->plugin->zingsphere_api_call('getInvisibleWidget');
		if(isset($widget['getInvisibleWidget'])) {
			echo $widget['getInvisibleWidget'];
		}
	}

}


?>
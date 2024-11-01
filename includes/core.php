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
 * Core plugin class
 */
class ZingspherePlugin {

	var $modules;
	var $admin_tabs;
	var $admin_current_tab;
	var $options;

	/**
	 * Method creates the global zingsphere object
	 * @global <ZingspherePlugin> $zingsphere_object
	 * @return <ZingspherePlugin>
	 */
	public static function zingsphere_create_object() {
		global $zingsphere_object;
		if(!$zingsphere_object) {
			$zingsphere_object = new ZingspherePlugin;
			$zingsphere_object->initialize();
		}
		return $zingsphere_object;
	}

	/**
	 * Method performs plugin initializion
	 */
	public function initialize() {
		add_filter('plugin_action_links', array(&$this, 'zingsphere_settings_link'), 9, 2);
		add_action('admin_menu', array(&$this, 'zingsphere_admin_actions'));
		wp_enqueue_script('app(\'' . admin_url('admin-ajax.php') . '\')', plugins_url('js/app.js', ZING_PLUGIN_FILE),array('jquery'));
		wp_enqueue_script('jquery-ui-sortable');

		$this->get_options();
		$this->load_modules();
	}

	/**
	 * Method loads available modules
	 */
	public function load_modules() {
		$this->modules = array();
		if($this->options) {
			$handler = opendir(ZING_MODULES);
			while($moduleName = readdir($handler)) {
				if($moduleName != '.' && $moduleName != '..'
								&& is_dir(ZING_MODULES . DIRECTORY_SEPARATOR . $moduleName)
								&& file_exists(ZING_MODULES . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'module.php')) {
					require_once(ZING_MODULES . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'module.php');
					$moduleClass = ucfirst($moduleName).'Module';
					$this->modules[$moduleName] = new $moduleClass($this);
				}
			}
			closedir($handler);
		}
		$this->call_modules('init');
	}

	/**
	 * Method calls a specified method in all active modules
	 * @param <string> $method
	 * @return <array>
	 */
	public function call_modules($method) {
		$results = array();
		foreach($this->modules AS $module) {
			if(method_exists($module, $method)) {
				$results[$module->name] = $module->$method();
			}
		}
		return $results;
	}

	/**
	 * Method displays the plugin admin settings for all available modules
	 */
	function zingsphere_admin() {
		$this->admin_tabs = array();
		foreach($this->modules AS $module) {
			if($module->admin_tab) {
				$this->admin_tabs[$module->admin_tab['weight']] = $module->name;
				if(isset($_GET['tab']) && $_GET['tab'] == $module->admin_tab['name'] && $module->is_active())
					$this->admin_current_tab = $module->name;
			}
		}
		ksort($this->admin_tabs);

		if(!$this->admin_current_tab) {
			foreach($this->admin_tabs AS $tab) {
				if($this->modules[$tab['name']] && $this->modules[$tab['name']]->is_active()) {
					$this->admin_current_tab = reset($tab['name']);
					break;
				}
			}
		}
		if(!$this->admin_current_tab) {
			$this->admin_current_tab = reset($this->admin_tabs);
		}
		if($_POST) {
			$this->error = $this->modules[$this->admin_current_tab]->admin_submit();
		}
		include('admin_page.php');
	}

	/**
	 * Method calls procedures when admin is logged
	 */
	public function zingsphere_admin_actions() {
		if(ZING_UPDATE) {
			add_filter('plugins_api_args', array(&$this, 'plugins_api_args'), 10, 2);
			add_filter('plugins_api', array(&$this, 'plugins_api'), 10, 3);
			add_filter('plugins_api_result', array(&$this, 'plugins_api_result'), 10, 3);
			$this->check_updates();
		}
		add_menu_page("Zingsphere", "Zingsphere", "manage_options", "wpzing", array(&$this, "zingsphere_admin"), plugins_url('images/zingicon.png', ZING_PLUGIN_FILE) );
	}

	/**
	 * Method returns the value of the specified plugin option
	 * @param <string> $option Name of the option requested
	 * @return <string>
	 */
	public function get_option($option) {
		$this->get_options();
		if(!isset($this->options[$option])) return null;
		return $this->options[$option];
	}

	/**
	 * Method returns all plugin options
	 * @param <boolean> $force Forces the options reload from database
	 * @return <array>
	 */
	public function get_options($force=false) {
		if($force || !isset($this->options)) {
			$this->options = get_option('zingsphere_options');
			if(!$this->options) {
				$this->options = array('active'=>false);
				add_option("zingsphere_options", $this->options);
			}
		}
		return $this->options;
	}
	/**
	 * Method sets the value of an option
	 * @param <string> $option Name of the option
	 * @param <string> $value Value of the option
	 * @param <boolean> $save Forces the option to be saved in database
	 */
	public function set_option($option, $value, $save=false) {
		$this->get_options();
		$this->options[$option] = $value;
		if($save) $this->save_options();
	}

	/**
	 * Method sets the value of all options
	 * @param <array> $options Options to be set
	 * @param <boolean> $save Forces the options to be saved in database
	 */
	public function set_options($options, $save=true) {
		$this->get_options();
		$this->options = $options;
		if($save) $this->save_options();
	}

	/**
	 * Method saves option values
	 */
	public function save_options() {
		update_option('zingsphere_options', $this->options);
	}

	/**
	 * Method calls zingsphere api
	 * @param <string> $command
	 * @param <array> $params
	 * @param <boolean> $token
	 * @return WP_Error
	 */
	public function zingsphere_api_call($command, $params=array(), $token=true) {
		$body = $params;
		$body['command'] = $command;
		$body['version'] = ZING_PLUGIN_VERSION;
		if($token)
			$body['token'] = $this->get_option('blog_api_token');

		$result = wp_remote_post(ZING_API_URL, array('timeout' => 40, 'body' => $body));
		$this->log('debug', 'Zingsphere API call to '.$command.' API method', $result);
		if(is_wp_error($result)) {
			return $result;
		} else {
			$result['body'] = json_decode($result['body'], true);
			if($result['body'] === false) {
				$this->log('error', 'An unknown error occurred while calling Zingsphere '.$command.' API method', $result);
				return new WP_Error('zingsphere_api_call', __('An unknown error occurred.'), $result['body']);
			} else if(isset($result['body']['error'])) {
				$this->log('ERROR',$result['body']['error_str'].' while calling Zingsphere '.$command.' API method','Zingsphere API');
				return new WP_Error('zingsphere_api_call', __($result['body']['error_str']));
			} else {
				return $result['body'];

			}
		}
		return array();
	}

	/**
	 * Method displays the plugin settings link in admin panel
	 * @param <string> $links
	 * @param <string> $file
	 * @return <string>
	 */
	public function zingsphere_settings_link( $links, $file ) {
		if( $file == preg_replace('/^.*\//', '', ZING_ROOT) . '/wpzing.php' && function_exists( "admin_url" ) ) {
			$settings_link = '<a href="' . admin_url('admin.php?page=wpzing') . '">' . __('Settings') . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Method checks for plugin updates
	 * @return <WP_Error>
	 */
	public function check_updates() {
		$plugin_name = 'wpzing/wpzing.php';
		$option = get_site_transient("update_plugins");
		$request = wp_remote_post(ZING_API_BASE . '/plugins/wpzing/updates', array('timeout' => 15, 'body' => array('version' => ZING_PLUGIN_VERSION, 'slug' => 'wpzing')));
		if(is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.'), $request->get_error_message() );
		} else {
			$res = unserialize(wp_remote_retrieve_body($request));
			if (false === $res)
				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred.'), wp_remote_retrieve_body($request));
			else {
				$option->response[$plugin_name] = $res;
				set_site_transient('update_plugins', $option);
			}
		}
		return $res;
	}

	/**
	 * Method is a wp api helper
	 * @param <array> $args
	 * @param <string> $action
	 * @return <WP_Error>
	 */
	public function plugins_api_args($args, $action) {
		return $args;
	}

	/**
	 * Method is a wp api helper
	 * @param <array> $arg
	 * @param <string> $action
	 * @param <array> $args
	 * @return <WP_Error>
	 */
	public function plugins_api($arg, $action, $args) {
		if(!is_object($args) || $args->slug != 'wpzing') return false;
		$request = wp_remote_post(ZING_API_BASE . '/plugins/wpzing/info', array( 'timeout' => 15, 'body' => array('action' => $action, 'request' => serialize($args))) );
		if(is_wp_error($request)) {
			$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.'), $request->get_error_message() );
		} else {
			$res = unserialize(wp_remote_retrieve_body($request));
			if (false === $res)
				$res = new WP_Error('plugins_api_failed', __('An unknown error occurred.'), wp_remote_retrieve_body($request ));
		}
		return $res;
	}

	/**
	 * Method is a wp api helper
	 * @param <type> $res
	 * @param <type> $action
	 * @param <type> $args
	 * @return <type>
	 */
	public function plugins_api_result($res, $action, $args) {
		return $res;
	}

	/**
	 * Method is called to activate plugin
	 */
	public static function zingsphere_plugin_activate() {
		global $wpdb;
		ZingspherePlugin::zingsphere_create_object()->set_option('active', true, true);
		$table_name=$wpdb->prefix.'zing_log';
		//Creating table in database for storing status messages from social networks
		$sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `severity` enum('debug','info','warning','error','fatal') COLLATE utf8_unicode_ci DEFAULT NULL,
                        `message` varchar(256) DEFAULT NULL,
                        `component` varchar(256) DEFAULT NULL,
			                  `data` text DEFAULT NULL,
                        `date` datetime DEFAULT NULL,
                        PRIMARY KEY (`id`)
                        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public static function zingsphere_plugin_deactivate() {
		global $zingsphere_object, $wpdb;
		if($zingsphere_object)
			$zingsphere_object->call_modules('zingsphere_deactivate');

		$table_name=$wpdb->prefix.'zing_log';
		$wpdb->query("DROP TABLE " . $table_name);
		delete_option('zingsphere_options');
	}

	/**
	 * Method logs plugin
	 * @param <string> $severity
	 * @param <string> $message
	 * @param <string> $component
	 */
	public function log($severity, $message, $data=array(), $component='core') {
		global $wpdb;
		if(!is_wp_error($data))
			$data['version'] = ZING_PLUGIN_VERSION;
		$wpdb->insert($wpdb->prefix.'zing_log',array('severity'=>$severity,'message'=>$message,'component'=>$component, 'data'=>maybe_serialize($data),'date'=>date('Y-m-d H:i:s')));
		return;
	}

	/**
	 * Checking ajax referer
	 */
	public function check_ajax($component, $field='security') {
		if(check_ajax_referer( 'wpzing-security-nonce', $field ));
		else {
			$this->log('warning','Illegal ajax referer',array(),$component);
		}
	}

	public function sendlog($user_request=false) {
		global $wpdb;

		$data = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zing_log ORDER BY date DESC LIMIT 0,150", ARRAY_A);
		$request = $this->zingsphere_api_call('getBlogLog', array('dump'=>$data , 'blog' => get_bloginfo('url'),'user_request'=>$user_request));
		if(!is_wp_error($request) && $request['getBlogLog']) {
			$wpdb->query("DELETE FROM " . $wpdb->prefix.'zing_log');
			$this->set_option('sendlog', false, true);
		} else {
			$this->set_option('sendlog', true, true);
		}
	}

	/**
	 * Method returns browser type and version
	 * @return <type>
	 */
	private function getBrowser() {

		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";

		//First get the platform?
		if (preg_match('/linux/i', $u_agent)) {
			$platform = 'linux';
		}
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
			$platform = 'mac';
		}
		elseif (preg_match('/windows|win32/i', $u_agent)) {
			$platform = 'windows';
		}

		// Next get the name of the useragent, yes seperately and for good reason
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
			$bname = 'Internet Explorer';
			$ub = "MSIE";
		}
		elseif(preg_match('/Firefox/i',$u_agent)) {
			$bname = 'Mozilla Firefox';
			$ub = "Firefox";
		}
		elseif(preg_match('/Chrome/i',$u_agent)) {
			$bname = 'Google Chrome';
			$ub = "Chrome";
		}
		elseif(preg_match('/Safari/i',$u_agent)) {
			$bname = 'Apple Safari';
			$ub = "Safari";
		}
		elseif(preg_match('/Opera/i',$u_agent)) {
			$bname = 'Opera';
			$ub = "Opera";
		}
		elseif(preg_match('/Netscape/i',$u_agent)) {
			$bname = 'Netscape';
			$ub = "Netscape";
		}

		// finally get the correct version number
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
						')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}

		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			//we will have two since we are not using 'other' argument yet
			//see if version is before or after the name
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)) {
				$version= $matches['version'][0];
			}
			else {
				$version= $matches['version'][1];
			}
		}
		else {
			$version= $matches['version'][0];
		}

		// check if we have a number
		if ($version==null || $version=="") {
			$version="?";
		}

		return array(
						'userAgent' => $u_agent,
						'name'      => $bname,
						'version'   => $version,
						'platform'  => $platform,
						'pattern'    => $pattern
		);
	}

}

?>
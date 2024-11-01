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
 * Zing Module
 */
class ZingModule extends ZingsphereModule {

	/**
	 * Constructor
	 * @param <ZingspherePlugin> $plugin
	 */
	public function  __construct($plugin) {
		$this->plugin = $plugin;
		$this->name = 'zing';
		$this->dependecies = array();
		$this->active = true;
		$this->admin_tab = array('name' => 'account', 'title' => 'Account', 'weight' => 40);
		$this->login_check = true;
	}

	/**
	 * Method initializes plugin module
	 */
	public function init() {
		if($this->is_active()) {
			add_action('wp_ajax_zingsphere_check', array(&$this, 'ajax_login_check'));
		}
	}

	/**
	 * Method called on plugin disconnect
	 */
	public function zingsphere_disconnect() {
		if($this->plugin->get_option('blog_api_token')) {
			$this->plugin->zingsphere_api_call('signout');
		}
		$this->plugin->set_options(array('active' => true));
	}

	/**
	 * Method called on plugin deactivation
	 */
	public function zingsphere_deactivate() {
		delete_option("zingsphere_options");
	}

	public function admin() {
		if(isset($_GET['reconnect'])) {
			$this->plugin->call_modules('zingsphere_disconnect');
		}
		if(!$this->plugin->get_option('blog_id')){
			$request = $this->plugin->zingsphere_api_call('getBlogInfo');
			if(!is_wp_error($request) && isset($request['getBlogInfo']['blog_id'])) {
				$this->plugin->set_option('blog_id', $request['getBlogInfo']['blog_id'], true);
			}
      if(!is_wp_error($request) && isset($request['getBlogInfo']['mangle'])) {
				$this->plugin->set_option('blog_mangle', $request['getBlogInfo']['mangle'], true);
			}
      
		}
		$request = $this->plugin->zingsphere_api_call('blogLanguage');
		if(!is_wp_error($request)) {
			$this->language = $request['blog_language'];
			$this->country = $request['blog_country'];
		}
		parent::admin();
	}

	/**
	 * Method displays admin settings for plugin module
	 * @return <type>
	 */
	public function admin_submit() {
		if(isset($_POST['Disconnect'])) {
			$this->plugin->call_modules('zingsphere_disconnect');
			//$this->login_check = 'social';
			return;
		}
		if(isset($_POST['tos']) && isset($_POST['tou']) && isset($_POST['touafb']) && isset($_POST['pp'])){
			$this->plugin->set_option('tos', true, true);
			return;
		}

		global $current_user;
		get_currentuserinfo();

		$blog_api_token = $this->plugin->get_option('blog_api_token');
		if(empty($blog_api_token)) {

			if(isset($_POST['confirm_code']) && isset($_POST['email'])) {
				$data = array('name' => get_bloginfo('name'),
								'url' => get_bloginfo('url'),
								'feed_url' => get_bloginfo('rss2_url'),
								'description' => get_bloginfo('description'),
								'email' => $_POST['email'],
								'token' => $_POST['confirm_code']
				);
				$request = $this->plugin->zingsphere_api_call('activateAccount', array('arguments' => $data), false);
				if(is_wp_error($request)) {
					return $request->get_error_message();
				} else if(isset($request['blog_api_token'])) {
					$this->plugin->set_option('blog_api_token', $request['blog_api_token']);
					$this->plugin->set_option('blog_api_user', $_POST['email'], true);
					add_user_meta($current_user->ID, 'zsuserid', $request['user_api_token'], true);
					add_user_meta($current_user->ID, 'zsdisplayname', $request['user_display_name'], true);
					$results = $this->plugin->call_modules('zingsphere_connect');
					$this->plugin->zingsphere_api_call('scanBlog');
					if(isset($results['widget'])) {
						return $results['widget'];
					}
				} else {
					return 'Token has expired.';
				}
			}

			$action = 'login';
			if(isset($_POST['Login']))
				$action = 'login';
			else if(isset($_POST['zinguser']) && isset($_POST['zingpass']) &&
							!empty($_POST['zinguser']) && !empty($_POST['zingpass']))
				$action = 'login';
			else if(isset($_POST['Signup']))
				$action = 'signup';
			else if(isset($_POST['zinguser_signup']) && isset($_POST['zingpass_signup']) && isset($_POST['zingpass_retype'])
							&& !empty($_POST['zinguser_signup']) && !empty($_POST['zingpass_signup']) && !empty($_POST['zingpass_retype']))
				$action = 'signup';

			if($action == 'login') {
				if(isset($_POST['zinguser']) && isset($_POST['zingpass']) && !empty($_POST['zinguser']) && !empty($_POST['zingpass'])) {
					$data = array('name' => get_bloginfo('name'),
									'url' => get_bloginfo('url'),
									'feed_url' => get_bloginfo('rss2_url'),
									'description' => get_bloginfo('description'),
									'email' => $_POST['zinguser'],
									'password' => $_POST['zingpass'],
									'ip' => $_SERVER['REMOTE_ADDR']);
					$request = $this->plugin->zingsphere_api_call('signin', array('arguments' => $data), false);
					if(is_wp_error($request)) {
						$this->login_check = 'zingsphere';
						return $request->get_error_message();
					} else if(isset($request['blog_api_token'])) {
						if($request['blog_api_token'] == false) {
							$this->user_exists = $request['user'];
							return '';
						} else {
							$this->plugin->set_option('blog_api_token', $request['blog_api_token']);
							$this->plugin->set_option('blog_language', $request['blog_language']);
							$this->plugin->set_option('blog_api_user', $_POST['zinguser'], true);
							add_user_meta($current_user->ID, 'zsdisplayname', $request['user_display_name'], true);
							add_user_meta($current_user->ID, 'zsuserid', $request['user_api_token'], true);
							$this->plugin->call_modules('zingsphere_connect');
							$this->plugin->zingsphere_api_call('scanBlog');
						}
					} else {
						$this->login_check = 'zingsphere';
						return 'An unknown error occurred.';
					}
				} else {
					$this->login_check = 'zingsphere';
					return 'Please specify zingsphere username and password';
				}
			}
			if($action == 'signup') {
				if(isset($_POST['zinguser_signup']) && isset($_POST['zingpass_signup']) && isset($_POST['zingpass_retype'])
								&& !empty($_POST['zinguser_signup']) && !empty($_POST['zingpass_signup']) && !empty($_POST['zingpass_retype'])) {
					if($_POST['zingpass_signup'] === $_POST['zingpass_retype']) {
						$data = array('name' => get_bloginfo('name'),
										'url' => get_bloginfo('url'),
										'feed_url' => get_bloginfo('rss2_url'),
										'description' => get_bloginfo('description'),
										'email' => $_POST['zinguser_signup'],
										'password' => $_POST['zingpass_signup'],
										'password_retype' => $_POST['zingpass_retype'],
										'ip' => $_SERVER['REMOTE_ADDR']);
						if(defined('ZING_AFFILIATE_CODE') && ZING_AFFILIATE_CODE != '')
							$data['code'] = ZING_AFFILIATE_CODE;

						$request = $this->plugin->zingsphere_api_call('signin', array('arguments' => $data), false);
						if(is_wp_error($request)) {
							$this->login_check = 'zingsphere';
							return $request->get_error_message();
						} else if(isset($request['blog_api_token'])) {
							if($request['blog_api_token'] == false) {
								$this->user_exists = $request['user'];
								return '';
							} else {
								$this->plugin->set_option('blog_api_token', $request['blog_api_token']);
								$this->plugin->set_option('blog_api_user', $_POST['zinguser_signup'], true);
								add_user_meta($current_user->ID, 'zsuserid', $request['user_api_token'], true);
								add_user_meta($current_user->ID, 'zsdisplayname', $request['user_display_name'], true);
								$results = $this->plugin->call_modules('zingsphere_connect');
								$this->plugin->zingsphere_api_call('scanBlog');
								if(isset($results['widget'])) {
									return $results['widget'];
								}
							}
						} else {
							$this->login_check = 'zingsphere';
							return 'An unknown error occurred.';
						}
					} else {
						$this->login_check = 'zingsphere';
						return 'Signup passwords do not match';
					}
				} else {
					$this->login_check = 'zingsphere';
					return 'Please specify valid email address and password to signup on Zingpshere';
				}
			}
		}
	}

	/**
	 * Method for ajax login check
	 */
	public function ajax_login_check() {
		$user_api_token = $_POST['user_api_token'];
		if(!$user_api_token) $user_api_token = $_COOKIE['zsuserid'];
		if($user_api_token) {
			$data = array('name' => get_bloginfo('name'),
							'url' => get_bloginfo('url'),
							'feed_url' => get_bloginfo('rss2_url'),
							'description' => get_bloginfo('description'),
							'user_api_token' => $user_api_token
			);
			$request = $this->plugin->zingsphere_api_call('signin', array('arguments' => $data), false);
			if(!is_wp_error($request) && isset($request['blog_api_token'])) {
				$this->plugin->set_option('blog_api_token', $request['blog_api_token']);
				$this->plugin->set_option('blog_api_user', $request['user'], true);
				$this->plugin->call_modules('zingsphere_connect');
				$this->plugin->zingsphere_api_call('scanBlog');
			}
		}
		die;
	}

}
?>
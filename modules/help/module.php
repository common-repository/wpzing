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
 * Help module
 */
class HelpModule extends ZingsphereModule {

	/**
	 * Constructor
	 * @param <ZingspherePlugin> $plugin
	 */
	public function  __construct($plugin) {
		$this->plugin = $plugin;
		$this->name = 'help';
		$this->dependecies = array('zing');
		$this->admin_tab = array('name' => 'help', 'title' => 'Help', 'weight' => 50);
	}

	/**
	 * Method initializes plugin module
	 */
	public function init() {
		add_action( 'wp_ajax_sendLog',array( &$this,'sendLog') );
	}

	public function is_active() {
		$zingsphere_options = get_option('zingsphere_options');
		//Returning zWall settings
		return $zingsphere_options && $zingsphere_options['blog_api_token'];
	}

	public function sendLog() {
		//Check ajax referer
		$this->plugin->check_ajax('wall');
		//Select logs from database
		$this->plugin->sendLog(true);
		echo 1;
		die;
	}

	public function admin_submit() {
		if($_POST['send_feedback']) {
			$this->sendFeedback();
		}
	}

	/**
	 * Send feedback
	 *
	 * @return string
	 */
	public function sendFeedback() {
		// Set log
		$this->plugin->log('debug', 'User is sending feedback', array(), 'help');
		// Set fields
		$subject = strip_tags($_POST['feedback_subject']);
		$text = strip_tags($_POST['feedback_text']);
		// Send to API
		$response = $this->plugin->zingsphere_api_call('sendZendesk', array('subject' => $subject, 'text' => $text));
		// Check response
		if( ! is_wp_error($response)) {
			$this->plugin->message='<p id="zingMessage" style="padding: 5px; background-color: #F2F5F2; color: #333333; border: 1px solid #CED1CE; font-weight: bold;">Thank you for sending us feedback.</p>';
			return;
		} else return 'An unknown error occured. Please try again later.';
	}

}

?>
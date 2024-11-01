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

require_once ZING_ROOT.'/modules/wall/module.php';

class NewsFeeds extends WallModule {

	public function  __construct($plugin) {

		$this->plugin=$plugin;
		$this->nonce=  wp_create_nonce('wpzing-security-nonce');
	}

	/**
	 * Activating news feeds
	 */
	public function activateNewsFeeds() {
		try {
			$this->log('debug','User activating news feed');
			//Updating zingsphere options
      $this->plugin->set_option('newsFeeds_active',1);
      $this->plugin->set_option('follow_auto_publish',1);
      $this->plugin->set_option('todo_activate_news_feeds',0);
      $this->plugin->save_options();

		} catch (Exception $e) {
			$this->log('error',$e->getMessage());
		}

	}

	/**
	 * Deactivating News feeds
	 */
	public function deactivateNewsFeeds() {
		try {
      //Updating zingsphere options
			$this->log('debug', 'User deactivating News Feed tab');
			$this->plugin->set_option('newsFeeds_active',0,true);
			
		}
		catch(Exception $e) {
			$this->log('error',$e->getMessage());
		}
	}

}

?>

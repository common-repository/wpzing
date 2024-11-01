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
 * Blog Module
 */
class BlogModule extends ZingsphereModule {

	/**
	 * Constructor
	 * @param <ZingspherePlugin> $plugin
	 */
	public function  __construct($plugin) {
		$this->plugin = $plugin;
		$this->name = 'blog';
		$this->dependecies = array('zing');
	}

	/**
	 * Method checks if module is active
	 * @return <boolean>
	 */
	public function is_active() {
		parent::is_active();
		return $this->active;
	}

	/**
	 * Method initializes plugin module
	 */
	public function init() {
		add_action('publish_post', array(&$this, 'scan_article'), 10, 1);
		add_action('delete_post', array(&$this, 'delete_article'), 10, 1);
	}

	/**
	 * Method informs zingsphere about published article
	 * @param <integer> $postid
	 */
	public function scan_article($postid) {
		$post = get_post($postid, ARRAY_A);
		if($post['post_type'] == 'post') {
			$tags = wp_get_post_tags($postid);
			$permalink = get_permalink($postid);
			$category = get_the_category($postid);
			$meta = get_post_meta($postid);
			$data = array('permalink' => $permalink, 'post' => $post, 'meta' => $meta, 'tags' => $tags, 'category' => $category);
			$this->plugin->zingsphere_api_call('scanArticle', array('arguments' => $data));
		}
	}

	/**
	 * Method informs zingsphere about deleted article
	 * @param <type> $postid 
	 */
	function delete_article($postid) {
		$post = get_post($postid, ARRAY_A);
		if($post['post_type'] == 'post') {
			$permalink = get_permalink($postid);
			$this->plugin->zingsphere_api_call('deleteArticle', array('arguments' => array('permalink' => $permalink)));
		}
	}
	
}

?>

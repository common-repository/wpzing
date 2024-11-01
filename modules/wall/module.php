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


/*
 * Include classes
*/

require_once ZING_ROOT . '/modules/wall/classes/facebook/facebook.php';
require_once ZING_ROOT . '/modules/wall/classes/twitter/twitter.php';
require_once ZING_ROOT . '/modules/wall/classes/embed.php';
require_once ZING_ROOT . '/modules/wall/classes/news_feeds.php';

/**
 * Wall Module
 */
class WallModule extends ZingsphereModule {

	/**
	 * Construct
	 * @param <ZingspherePlugin> $plugin
	 */
	public function __construct($plugin) {
		$this->plugin = $plugin;
		$this->name = 'wall';
		$this->dependecies = array('zing', 'widget');
		$this->admin_tab = array('name' => 'zwall', 'title' => 'zWall', 'weight' => 30);
		$this->nonce = wp_create_nonce('wpzing-security-nonce');
		$this->zwall_page == null;

		$this->NewsFeeds  =  new NewsFeeds($plugin);
		$this->Facebook   =  new zFacebook($plugin);
		$this->Twitter    =  new zTwitter($plugin);
		$this->Embed      =  new Embed($plugin);
	}

	/**
	 * Is zWall activated??
	 */
	public function is_active() {
		return $this->active = parent::is_active() && $this->plugin->get_option('blog_api_token');
	}

	/**
	 * Method initializes plugin module
	 */
	public function init() {
		if ($this->is_active()) {

			/* Add Actions */
			
			add_action('template_redirect', array(&$this, 'template'));

			add_action('wp_ajax_zwall', array(&$this, 'ajax_zwall'));
			add_action('wp_ajax_nopriv_zwall', array(&$this, 'ajax_zwall'));
			add_action('wp_ajax_zwall_posts', array(&$this, 'ajax_zwall_posts'));
      add_action('wp_ajax_zwall_show_more', array(&$this, 'ajax_zwall_show_more'));
      add_action('wp_ajax_nopriv_zwall_show_more', array(&$this, 'ajax_zwall_show_more'));
			add_action('wp_ajax_nopriv_zwall_posts', array(&$this, 'ajax_zwall_posts'));
			add_action('wp_ajax_following_feed', array(&$this, 'ajax_following_feed'));
			add_action('wp_ajax_nopriv_following_feed', array(&$this, 'ajax_following_feed'));
			add_action('wp_ajax_facebook_feed', array(&$this, 'ajax_facebook_feed'));
			add_action('wp_ajax_nopriv_facebook_feed', array(&$this, 'ajax_facebook_feed'));
			add_action('wp_ajax_twitter_feed', array(&$this, 'ajax_twitter_feed'));
			add_action('wp_ajax_nopriv_twitter_feed', array(&$this, 'ajax_twitter_feed'));
			add_action('wp_ajax_zwall_feed', array(&$this, 'ajax_zwall_feed'));
			add_action('wp_ajax_nopriv_zwall_feed', array(&$this, 'ajax_zwall_feed'));

			add_action('wp_ajax_insert_wall_post', array(&$this, 'ajax_insert_wall_post'));
			add_action('wp_ajax_insert_wall_comment', array(&$this, 'ajax_insert_wall_comment'));
			add_action('wp_ajax_nopriv_insert_wall_comment', array(&$this, 'ajax_insert_wall_comment'));

			add_action('wp_ajax_more_comments', array(&$this, 'ajax_get_more_comments'));
			add_action('wp_ajax_nopriv_more_comments', array(&$this, 'ajax_get_more_comments'));

			add_action('wp_ajax_zwall_sticker', array(&$this, 'ajax_zwall_sticker'));
			add_action('wp_ajax_nopriv_zwall_sticker', array(&$this, 'ajax_zwall_sticker'));

			add_action('wp_ajax_receiveWallComment', array(&$this, 'ajax_receiveWallComment'));
			add_action('wp_ajax_nopriv_receiveWallComment', array(&$this, 'ajax_receiveWallComment'));

			add_action('wp_ajax_deleteComment', array(&$this, 'deleteComment'));
			add_action('wp_ajax_saveFeed', array($this->NewsFeeds, 'saveFeed'));
			add_action('wp_ajax_addFeed', array($this->NewsFeeds, 'addFeed'));
			add_action('wp_ajax_deleteFeed', array(&$this, 'deleteFeed'));
			add_action('wp_ajax_scanFeed', array($this->NewsFeeds, 'scanFeed'));
			add_action('wp_ajax_rescanFeed', array($this->NewsFeeds, 'rescanFeed'));
			add_action('wp_ajax_nopriv_rescanFeed', array($this->NewsFeeds, 'rescanFeed'));
			add_action('wp_ajax_updateZWall', array(&$this, 'getWallUpdate'));
			add_action('wp_ajax_nopriv_updateZWall', array(&$this, 'getWallUpdate'));
			add_action('wp_ajax_search', array(&$this, 'search'));
			add_action('wp_ajax_nopriv_search', array(&$this, 'search'));
			add_action('wp_ajax_showMore', array(&$this, 'showMore'));
			add_action('wp_ajax_nopriv_showMore', array(&$this, 'showMore'));
			add_action('wp_ajax_getMore', array($this->NewsFeeds, 'getMore'));
			add_action('wp_ajax_nopriv_getMore', array($this->NewsFeeds, 'getMore'));
			add_action('wp_ajax_closeTodoItem', array(&$this, 'todo'));
			add_action('wp_ajax_nopriv_closeTodoItem', array(&$this, 'todo'));
			add_action('wp_ajax_deleteStatusUpdate', array(&$this, 'deleteStatusUpdate'));
			add_action('wp_ajax_nopriv_deleteStatusUpdate', array(&$this, 'deleteStatusUpdate'));
			add_action('wp_ajax_zShare', array($this->Embed,'zShare'));
			add_action('wp_ajax_nopriv_zShare', array($this->Embed,'zShare'));
			add_action('wp_ajax_embed', array($this->Embed, 'embed'));
			add_action('wp_ajax_followZsBlog', array(&$this, 'followZsBlog'));
			add_action('wp_ajax_resetzFollowers', array(&$this, 'resetzFollowers'));
			add_action('wp_ajax_repostWall', array(&$this, 'ajax_repostWall'));
			add_action('wp_ajax_newFollower', array(&$this, 'newFollower'));
			add_action('wp_ajax_nopriv_newFollower', array(&$this, 'newFollower'));
			add_action('wp_ajax_connectSocial', array(&$this, 'connectSocial'));
			add_action('wp_ajax_nopriv_connectSocial', array(&$this, 'connectSocial'));
			add_action('wp_ajax_nopriv_disconnectSocial', array(&$this, 'disconnectSocial'));
			add_action('wp_ajax_disconnectSocial', array(&$this, 'disconnectSocial'));
      
      
		}

		if ($_POST && isset($_POST['zWall_post']))
			header("Location: " . $_SERVER['REQUEST_URI']);
	}

	function wp_filter_timeout_time() {
		$time = 100;
		return $time;
	}

	/**
	 * Method adds javascript files
	 */
	public function getJsFiles() {

    $jsfiles=array(
        plugins_url('js/jquery-ui-1.10.1.custom.min.js', ZING_PLUGIN_FILE),
        plugins_url('js/app.js', ZING_PLUGIN_FILE),
        plugins_url('js/zwall.js', ZING_PLUGIN_FILE),
        plugins_url('js/lightbox.js', ZING_PLUGIN_FILE),
        plugins_url('js/jquery.cookie.js', ZING_PLUGIN_FILE),
        plugins_url('js/jquery.autosize-min.js', ZING_PLUGIN_FILE),
        plugins_url('js/twitter.js', ZING_PLUGIN_FILE),
        plugins_url('js/facebook.js', ZING_PLUGIN_FILE),
    );
    
    return $jsfiles;

	}
  public function getStyleFiles(){
    
    $stylefiles=array(
        plugins_url('css/style.css', ZING_PLUGIN_FILE),
        plugins_url('css/jquery-ui.css', ZING_PLUGIN_FILE),
        plugins_url('css/lightbox.css', ZING_PLUGIN_FILE),        
    );
    return $stylefiles;
  }

	function get_zwall_page($value='') {
		if(!$this->zwall_page) {
			global $wpdb;
			$page = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type='page'", 'zwall'));
			if($page) {
				$this->zwall_page = get_page($page);
			}
		}
		return $this->zwall_page ? $value ? $this->zwall_page->$value : $this->zwall_page : null;
	}

	/**
	 * Overriding page template for zwall
	 *
	 * @global object $wp_query
	 */
	function template() {
		global $wp_query;

		$zwall = $this->get_zwall_page();
		if($zwall && $wp_query->query_vars['pagename'] == 'zwall' ||
						isset($wp_query->query_vars['page_id']) && $zwall &&
						$wp_query->query_vars['page_id'] == $zwall->ID) {
			if(!$this->plugin->get_option('zWall_active'))
				wp_redirect(home_url());
			global $current_user;
			get_currentuserinfo();
			if($current_user->ID) {
				if(!isset($_COOKIE['zsuserid']) && get_user_meta($current_user->ID, 'zsuserid', true))
					setcookie('zsuserid', get_user_meta($current_user->ID, 'zsuserid', true));
			} else {
				setcookie('zsuserid', false);
			}
			include('pages/template.php');
			die;
		}
	}

	/**
	 * First content displayed on the wall page
	 * loads everything else
	 */
	function ajax_zwall() {
		print '<div id="error" style="width:100%;text-align:center;color:#FF0000;border:1px solid #FF0000;display:none"></div>';

		//Make Z wall and reader tabs
		print $this->makeTabs();

		//If user is logged in make form for posting status messages on wall
		print $this->wallPostForm();

		print '<div id="Zingram">';

		//Creating zWall HTML
		print '<div id="zWall" class="zingram_div"><div id="zWallPosts">';
		print $this->load_ajax_content('zwall_posts', 'zWallPosts');
		print '</div>';
    print '<div class="zWallGrayed show_more" id="zWallShowMore">';
    print $this->load_ajax_content('zwall_show_more', 'zWallShowMore');
    print '</div></div>';
		//Add the search div to display search results
		print $this->addSearch();
		if($this->canUserManageWall()) {
			//News feed tab HTML
			print '<div id="zWallNewsFeed" class="zingram_div" style="display:none;"><div id="zWallNewsFeedPosts">';
			print $this->load_ajax_content('following_feed', 'zWallNewsFeed');
			print '</div></div>';

			print '<div id="zWallFb" class="zingram_div" style="display:none;">';
			print $this->load_ajax_content('facebook_feed', 'zWallFb');
			print '</div>';

			print '<div id="zWallTwitter" class="zingram_div" style="display:none;">';
			print $this->load_ajax_content('twitter_feed', 'zWallTwitter');
			print '</div>';

			print '<div id="zWallNewsPosts" class="zingram_div" style="display:none">';
			print $this->load_ajax_content('zwall_feed', 'zWallNewsPosts');
			print '</div>';

		}

		//Add javascript code to frequently update zWall
		print $this->addWallUpdate();
    print '<div id="zWallScrollToTop" title="Back to top" onClick="Wall.toTop()">';
    print '<img src="'.plugins_url('images/scroll.png',ZING_PLUGIN_FILE).'" /> ';
    print '</div>';
    print '<script>jQuery("#zWallScrollToTop").css("top",document.body.offsetHeight-70+"px");jQuery(window).scroll(function(){if(jQuery(this).scrollTop()){jQuery("#zWallScrollToTop:hidden").stop(true, true).fadeIn();}else{jQuery("#zWallScrollToTop").stop(true, true).fadeOut();}});</script>';
		print '</div>'; // Zingram

		if($this->plugin->get_option('zsticker_enable')) {
			$settings=$this->plugin->get_option('zstickerSettings');
			if($settings['public']['enable'] || $this->canUserManageWall()) {
				print '<div id="zWallSticker">';
				print '<div style="padding-left:100px">'.$this->load_ajax_content('zwall_sticker', 'zWallSticker').'</div>';
				print '</div>';
			}
		}
		die;
	}

	public function load_ajax_content($action, $content_id) {
		$args = array(
						'action' => $action,
						'nonce' => $this->nonce,
		);
		$html = '
  <center><br/><img src="'.plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE).'" style="margin:0 auto" class="embedLoader" alt="ajax"/><br/><br/></center>
  <script type="text/javascript">
	(function() {
	jQuery.get("'.admin_url('admin-ajax.php').'", ';
		$html .= json_encode($args);
		$html .= ', function(response){
		jQuery("#'.$content_id.'").html(response);
	});
	}());
</script>';
		return $html;
	}

	public function ajax_zwall_posts() {
		//Generate zWall page content
		print $this->generateZwall_wallposts();
		die;
	}
  
  public function ajax_zwall_show_more() {
		//Generate Show more link
    $posts=$this->generateZwall_wallposts($page,10,'',true);
    if($posts['next'])
		print '<a href="Javascript:void(0)" onClick="App.showMore(2)" class="zWallGrayed">Show more</a>';
		die;
	}
  
	public function ajax_following_feed() {
		//Get News feed tab content
		print $this->news_feed_blogs();
		die;
	}

	public function ajax_facebook_feed() {
		//Get Facebook news feed tab content
		print $this->Facebook->getFacebook();
		die;
	}

	public function ajax_twitter_feed() {
		//Get Twitter news feed tab content
		print $this->Twitter->getTimeline();
		die;
	}


	public function ajax_zwall_feed() {
		print $this->news_feed_wall();
		die;
	}

	public function ajax_zwall_sticker() {
		$result = $this->plugin->zingsphere_api_call('sticker');
    
		if(!is_wp_error($result) && isset($result['sticker'])) {
			$followers = $result['sticker']['followers'];
			$followings = $result['sticker']['followings'];
			$similars = $result['sticker']['similars'];
			$settings = $this->plugin->get_option('zstickerSettings');
			$privacy = $this->canUserManageWall() ? 'admin' : 'public';
			include(ZING_ROOT.'/modules/wall/elements/sticker.php');
		}
		die;
	}

	/**
	 * Handling actions on admin submit
	 */
	public function admin_submit() {
		//Log submit
		$this->log('debug', 'Admin submit');
		try {
			//Activate zWall??
			if ($_POST['activatezWall']) {
				$this->activatezWall();
				return;
			}

			//Deactivate wall??
			if ($_POST['deactivatezWall']) {
				$this->deactivatezWall();
				return;
			}

			//Activate reader??
			if ($_POST['activateNewsFeeds']) {
				$this->NewsFeeds->activateNewsFeeds();
				return;
			}

			//Deactivate reader??
			if ($_POST['deactivateNewsFeeds']) {
				$this->NewsFeeds->deactivateNewsFeeds();
				return;
			}

			//Upload rss feed file??
			if ($_POST['zWallUploadRss']) {
				$this->NewsFeeds->uploadXml($_FILES['rss']);
				return;
			}
			//Renamed zWall page??
			if ($_POST['renamezWall']) {
				$change = $this->renamezWall($_POST);
				return $change;
			}

			//Social settings??
			if ($_POST['zWallSocialSettings']) {
				$this->socialSettings($_POST);
				return;
			}

			//Social feeds??
			if ($_POST['zWallSocialFeeds']) {
				$this->socialFeeds($_POST);
				return;
			}

			//Social settings??
			if ($_POST['zBlogPostSubmit']) {
				if (isset($_POST['zBlogPost']))
					$this->plugin->set_option('zBlogPost', true, true);
				else
					$this->plugin->set_option('zBlogPost', false, true);
				return;
			}

			//zSticker settings??
			if ($_POST['zStickerSubmit']) {
				$this->zStickerSettings($_POST);
				return;
			}

			//User roles?
			if ($_POST['zWallUserRoles']) {
				$this->userRoles($_POST);
				return;
			}

			//Changed css file??
			if (isset($_POST['zWallSaveCss'])) {
				$this->saveCss($_POST);
				return;
			}
			//ifttt??
			if (isset($_POST['zWallIfttt'])) {
				$this->iftttSettings($_POST);
				return;
			}

			//Chosen menu for wall page?
			if (isset($_POST['zWallMenuSelect'])) {
				$this->insertZwallInMenu($_POST);
				return;
			}

			//Submit new wall settings
			if (isset($_POST['zFeeds']) and $_POST['zFeeds'] == 'Submit') {
				$auto_publish = isset($_POST['follow_auto_publish']) ? true : false;
				$this->plugin->zingsphere_api_call('setAutoPublish', array('on' => $auto_publish));
				$this->plugin->set_option('follow_auto_publish', $auto_publish, true);
				$settings = $_POST['settings'];
				$this->plugin->zingsphere_api_call('setWallSettings', array('settings' => $settings));
			}
		}
		//Catch exception
		catch (Exception $e) {
			$this->log('error', $e->getMessage(), $_POST);
		}
	}

	/**
	 * Method checks if zwall page exists, generates one if not
	 * The page's slug should always be zwall and should not be changed
	 */
	public function create_zwall_page() {
		$page = $this->get_zwall_page();
		if (!$page) {
			//Setting values for zWall page
			$wall = array(
							'comment_status' => 'closed',
							'ping_status' => 'closed',
							'post_author' => 1,
							'post_content' => '',
							'post_name' => 'zwall',
							'post_status' => 'publish',
							'post_title' => 'zWall',
							'post_type' => 'page',
			);
			//Insert zWall page in database
			$id = wp_insert_post($wall);
			if ($id) {
				$this->plugin->set_option('zwall_page_id', $id, true);
				$this->log('debug', 'zWall page created');
			}	else {
				$this->log('error', 'Error while creating zWall page');
			}
		}
	}

	/**
	 * Method deletes zwall page for good if exists
	 */
	public function destroy_zwall_page() {
		$page = $this->get_zwall_page();
		if($page) {
			//Removing wall page from database
			if (wp_delete_post($page->ID, true))
				$this->log('debug', 'zWall page deleted from database');
			else
				$this->log('error', 'Error while deleting zWall page');
		}
	}

	/**
	 * Activating Z wall
	 */
	public function activatezWall() {
		try {
			//Log activating zWall
			$this->log('debug', 'User activating zWall');
			$this->create_zwall_page();
			//Updating zingsphere options
			$this->plugin->set_option('zWall_active', true);
			//Updating options on zingsphere
			$this->plugin->zingsphere_api_call('setAutoPublish', array('on' => TRUE));

			//Pre setting options for zWall
			$this->plugin->set_option('follow_auto_publish', true);
			$this->plugin->set_option('user_roles', array('administrator', 'editor', 'author', 'contributor'));
			$this->plugin->set_option('zfeedback_enable', true);
			$this->plugin->set_option('zsticker_enable', true);
			$this->plugin->set_option('zsticker_show', true);
			$this->plugin->set_option('zstickerSettings', array('general' => array('autotoggle' => 'on','minimized'=>'true'), 'admin' => array('followers_widget' => 'on', 'similar_blogs' => 'on'), 'theme'=>'light','public'=>array('enable'=>'on','followers_widget'=>'on')));
			$this->plugin->set_option('zsticker_widget_ids', array());
			$this->plugin->set_option('todo_connect_social_networks', 1,true);
			$this->plugin->set_option('todo_choose_blogs', 1,true);
			$this->plugin->set_option('todo_activate_news_feeds', 0,true);
			$this->plugin->set_option('newsFeeds_active', 1,true);

			$result = $this->plugin->zingsphere_api_call('listWidgets');
			if(!is_wp_error($result) && isset($result['listWidgets'])) {
				$w = array();
				foreach($result['listWidgets'] AS $widget) {
					$w[] = $widget['id'];
				}
				$this->plugin->set_option('zsticker_widget_ids', $w);
			}

			$this->NewsFeeds->activateNewsFeeds();
			//Send information for activating zWall
			$this->plugin->zingsphere_api_call('activateWall');
			//Saving options
			$this->plugin->save_options();
			//Check which social networks are already connected
			$response=$this->plugin->zingsphere_api_call('socialConnected');
			if(!is_wp_error($response) && isset($response['connected']))
				foreach ($response['connected'] as $network) {
					$this->plugin->set_option($network['network'],(object)$network['data'],true);
				}

			$this->log('debug', 'zWall activated successfully');
		} catch (Exception $e) {
			$this->log('exception', $e->getMessage());
		}
	}

	/**
	 * Deactivating zWall
	 */
	public function deactivatezWall() {
		try {
			//Log deactivating
			$this->log('debug', 'User deactivating zWall');
			$this->destroy_zwall_page();
			$this->plugin->set_option('zWall_active', false);
			$this->plugin->set_option('facebook', false);
			$this->plugin->set_option('twitter', false);
      $this->plugin->set_option('last_tw_post',false);
      global $wpdb;
      $wpdb->query('DELETE FROM '.$wpdb->posts.' WHERE post_type="nav_menu_item" AND post_name="zwall"');
      $wpdb->query('DELETE FROM '.$wpdb->posts.' WHERE post_type="facebook" OR post_type="twitter"');
			$this->plugin->set_option('menu_choosen', false);
			$this->plugin->set_option('newsFeeds_active', 0);
			//Saving options
      $this->plugin->save_options();
			$this->plugin->zingsphere_api_call('deactivateWall');
			$this->log('debug', 'Updating zingsphere options-setting all values to false');
			//Clearing settings for social networks
			//$this->socialSettings(array());
		} catch (Exception $e) {
			$this->log('error', $e->getMessage());
		}
	}

	/**
	 * Method called on plugin deactivation
	 */
	public function zingsphere_deactivate() {
		try {
			$this->deactivatezWall();
		} catch (Exception $e) {
			$this->log('error', $e->getMessage());
		}
	}

	/**
	 * Insert a wall status update
	 */
	public function ajax_insert_wall_post() {
		$text = strip_tags($_POST['wall_post_text']);
		$post = array(
						'comment_status' => 'closed',
						'ping_status' => 'closed',
						'post_content' => $text,
						'post_status' => 'publish',
						'post_title' => 'zWall post',
						'post_type' => isset($_POST['zWall_blog_post']) ? 'post' : 'zwall',
		);
		$id = wp_insert_post($post);
		if ($id) {
			if (isset($_POST['embeded']) && !empty($_POST['embeded'])) {
				add_post_meta($id, 'embeded', $_POST['embeded'], true);
			}
			$this->sendWallPost($id);
			$f=$this->Facebook->postStatusMessage($text,$id);
			if($f) add_post_meta($id, 'published_to_facebook', 1,true);

			$t=$this->Twitter->postStatusMessage($text,$id);
			if($t) add_post_meta($id, 'published_to_twitter', 1,true);
			$html = $this->generateZwall_wallpost($this->post2wall($id));

			$result = array('success' => true, 'id' => $id, 'post' => $html);
		} else {
			$result = array('success' => false, 'error' => 'error!');
		}
		echo json_encode($result);
		die;
	}

	public function filter_meta($a){
		return $a[0];
	}

	public function post2wall($post_id) {
		if(is_object($post_id)) $post = $post_id;
		else $post = get_post($post_id);

		$post_meta = array_map(array(&$this, 'filter_meta'), get_metadata('post', $post->ID));

		$wall = array(
						'id' => $post->ID,
						'type' => $post->post_type,
            'date'=>$post->post_date,
						'user_zs_profile' => $post_meta['user_zs_profile'],
						'user_display_name' => $post_meta['user_display_name'],
						'user_avatar' => $post_meta['user_avatar'],
						'default_avatar' => ZING_WWW_BASE.'/thumbs/avatars/default.png',
						'repost' => false,
						'repostable' => false,
						'commentable' => true,
						'erasable' => true,
						'content' => $post->post_content,
						'comments' => array()
		);
		if(isset($post_meta['repost']) && $post_meta['repost']) {
			$wall['repost'] = true;
			$wall['repost_user_zs_profile'] = $post_meta['repost_user_zs_profile'];
			$wall['repost_user_display_name'] = $post_meta['repost_user_display_name'];
		}
		if(isset($post_meta['embeded'])){
			$wall['embeded'] = maybe_unserialize($post_meta['embeded']);
		}

		$query = array('post_id' => $post->ID, 'status'=>'1', 'orderby'=>'comment_date', 'order'=>'ASC','number'=>5, 'offset'=>0, 'count'=>true);
		$comments_count = get_comments($query);
		$wall['comments_count'] = $comments_count;
		$query['count'] = false;
		if($comments_count > 5)
			$query['offset'] = $comments_count - 5;
		$post_comments = get_comments($query);
		foreach($post_comments AS $comment) {
			$wall['comments'][] = array('comment_id' => $comment->comment_ID, 'user_display_name' => $comment->comment_author, 'content' => $comment->comment_content,'comment_date'=>$comment->comment_date);
		}
		return $wall;
	}

	public function ajax_get_more_comments() {
		$post_id = $_GET['post_id'];
		$number = $_GET['number'];
		$offset = $_GET['offset'] ? $_GET['offset'] : 0;

		$query = array('post_id' => $post_id, 'status'=>'approve', 'orderby'=>'comment_date', 'order'=>'ASC','number'=>$number, 'offset'=>$offset);
		$post_comments = get_comments($query);
		$html = '';
		foreach($post_comments AS $comment) {
			$html .= '<div class="zWallComment"><p id="' . $comment->comment_ID . '"><b>' . $comment->comment_author . '</b>: '.$comment->comment_content.'<br/><span style="font-size:0.9em">'.$comment->comment_date.'</span>';
			if (current_user_can('manage_options'))
				$html.=' <a href="javascript:void(0);" class="zWallFormDeleteComment" onClick="App.deleteComment('.$comment->comment_ID.','.$post_id.',\''.wp_create_nonce('wpzing-security-nonce').'\');">Delete</a></p></div>';
			else $html.='</p></div>';
		}
		print $html;
		die;
	}

	public function sendWallPost($post_id) {
		$post = get_post($post_id, ARRAY_A);
		$post_meta = array_map(array(&$this, 'filter_meta'), get_metadata('post', $post_id));

		$result = $this->plugin->zingsphere_api_call('sendWallPost', array('post' => $post, 'post_meta' => $post_meta, 'zsuserid' => $_COOKIE['zsuserid']));

		if (!is_wp_error($result) && isset($result['success'])) {
			add_post_meta($post_id, 'zingsphere_timestamp', $result['sendWallPost']['timestamp'], true);
			add_post_meta($post_id, 'user_zs_profile', $result['sendWallPost']['user_zs_profile'], true);
			add_post_meta($post_id, 'user_display_name', $result['sendWallPost']['user_display_name'], true);
			add_post_meta($post_id, 'user_avatar', $result['sendWallPost']['user_avatar'], true);
		}
	}

	/**
	 * Generate zWall and News Feed tabs
	 */
	public function makeTabs() {
		//Html for tabs
		$page = get_page_by_path('zwall');
		include_once ZING_ROOT . '/modules/wall/elements/tabs.php';
	}

	/**
	 * Form for posting status updates
	 */
	public function wallPostForm() {

		//Is current user is admin create form for posting status updates
		if ($this->canUserManageWall()) {
			//Generate form
      $blogInfo = $this->plugin->zingsphere_api_call('getBlogInfo');

			include(ZING_ROOT.'/modules/wall/elements/post_form.php');
		}

	}

	public function news_feed_blogs() {
		$html = '';
		$result = $this->plugin->zingsphere_api_call('wallBlogFeed');
		//$html .= '<pre>'.print_r($result, true).'</pre>';
		if(!is_wp_error($result) && isset($result['wallBlogFeed']) && is_array($result['wallBlogFeed'])) {
			$posts = $result['wallBlogFeed'];
			//echo '<pre>'.print_r($posts, true).'</pre>';
			foreach($posts AS $post) {
				$html .= '
           <div class="zWallArticle">
              <div class="zWallArticleTitle">
              <img src="'.plugins_url('images/avatar-bg.png',ZING_PLUGIN_FILE).'" class="userAvatar avatarBg" />
              <img src="'.$post['thumb'].'" class="userAvatar"/>
              <a href="' . $post['permalink'] . '" target="_blank" class="zWallArticleTitle">' . $post['title'] . '</a></div>
             ' . strip_tags($post['excerpt']) . '
           </div><div style="clear:both"></div>';
			}
		}
		return $html;
	}

	public function news_feed_wall($limit=0) {
		$html = '';
		$result = $this->plugin->zingsphere_api_call('wallPostFeed');
		if(!is_wp_error($result) && isset($result['wallPostFeed']) && is_array($result['wallPostFeed'])) {
			$posts = $result['wallPostFeed'];
			foreach($posts AS $post) {
				if(isset($post['embeded'])) $post['embeded'] = maybe_unserialize($post['embeded']);
				$html .= $this->generateZwall_wallpost($post);
			}
		}
		return $html;
	}

	public function generateZwall_wallposts($page=1, $limit=10, $keyword='',$next=false) {
    
    $fb=$this->plugin->get_option('publish_from_facebook') ? array('facebook') : array();
    $tw=$this->plugin->get_option('publish_from_twitter') ? array('twitter') : array();
    $types=  array_merge($fb,$tw,array('zwall','post'));
		$search_query = array(
						'post_type' => $types,
						'posts_per_page' => $limit,
						'paged' => $page,
						'order' => 'DESC',
						'orderby' => 'post_date',
						'ignore_sticky_posts' => true,
		);

		if($keyword)
			$search_query['s'] = $keyword;

		$search = new WP_Query($search_query);
		$html = '';
		if($search->have_posts()) {
			do {
				$search->next_post();
				$post = $search->post;
				if($search->post->post_type == 'zwall') {
					$html .= $this->generateZwall_wallpost($this->post2wall($search->post));
				} else if($post->post_type == 'post') {
					$html .= '
                                    <div class="zWallPost"> 
                                     <img src="'.plugins_url('images/avatar-bg.png',ZING_PLUGIN_FILE).'" class="userAvatar avatarBg" />
                                    <div class="userAvatar">'.get_avatar($post->post_author, 88).'</div>
                                       <div class="zWallPostTitle"><a href="' . get_permalink($post->ID) . '" target="_blank">' . $post->post_title . '</a></div>
                                        ' . strip_tags($post->post_excerpt == '' ? substr($post->post_content, 0, 512) : $post->post_excerpt) . '
                                    </div>';

				}	else if($post->post_type=='facebook' && $this->plugin->get_option('publish_from_facebook')) {
					if(!get_post_meta($post->ID,'zs_post',true)=='1')
						$html.=$this->generateSocialWallPost ($post);
				}	else if($post->post_type=='twitter'  && $this->plugin->get_option('publish_from_twitter')) {
					$html.=$this->generateSocialWallPost ($post);
				}
			} while($search->have_posts());
		}
		if(!($current_user->ID && get_metadata('user', 'zsdisplayname', true) && $this->plugin->get_option('blog_api_user')))
			$html.='<script type="text/javascript" src="'.ZING_WWW_BASE.'/users/check_user?callback=Wall.commentUser"></script>';
    
    if($next) { return array('html'=>$html,'next'=>count($search->posts) < 10 ? false : true );}
    
		return $html;
	}

	public function generateZwall_wallpost($item) {
    
		$html = '
                <div class="zWallPost" id="zWallStatusUpdate' . $item['id'] . '">
                <div class="zWallPostWrapper">
                
                <a href="'.$item['user_zs_profile'].'" target="_blank">
                  <img src="'.plugins_url('images/avatar-bg.png',ZING_PLUGIN_FILE).'" class="userAvatar avatarBg" />
                  <img src="'.($item['user_avatar']? $item['user_avatar'] : plugins_url('images/default_avatar.png',ZING_PLUGIN_FILE)) .'" class="userAvatar"  />
                </a>';
		$html.='<div class="zWallStatusContent">
            <p class="zWallDisplayName">';
		if(isset($item['repost']) && $item['repost'])
			$html .= 'Reposted from: <a href="'.$item['repost_user_zs_profile'].'" target="_blank">'.$item['repost_user_display_name'].'</a>';
		else
			$html .='<a href="'.$item['user_zs_profile'].'" target="_blank">'.$item['user_display_name'].'</a>';
		$html .= '</p>';
		$html.= '<p class="zWallStatus">'.$item['content'].'</p>';
// insert embed meta
		if(isset($item['embeded']) && !empty($item['embeded']))
			$html.=$this->Embed->format_meta_content($item['embeded']);

		$html.='<div class="zWallPostStatus">';
		if($item['type'] == 'zwall')
			{}
		else
			$html .= $item['user_display_name'];
//If user can manage zWall add delete Icon
		if ($item['type'] == 'zwall'&& $this->canUserManageWall()) $html.='<span onClick="App.deleteStatusUpdate(' . $item['id'] . ',this,\''.$this->nonce.'\')" class="zWallStatusDelete" title="Delete status update">X</span> ';
		if ($item['repostable'] && $this->canUserManageWall()) $html.='<button onclick="App.repostWall('.$item['id'].',\''.$this->nonce.'\')" class="zWallRepostButton"></button>';
    $html.='<img src="'.plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE).'" style="display:none" id="repost'.$item['id'].'"/>';
		$html.='</div>';

		if(get_post_meta($item['id'],'published_to_twitter',true)) {
			//If this wall post is published to Twitter display Twitter icon
			$html .= '<span><img src="'.plugins_url('/images/twitter-icon.png',ZING_PLUGIN_FILE).'" title="Published to Twitter" class="zWallPostSocialIconTwitter"/></span>';
		}

		if(get_post_meta($item['id'],'published_to_facebook',true)) {
			//If this wall post is published to Facebook display Facebook icon
			$html .= '<span><img src="'.plugins_url('images/facebook-icon.png',ZING_PLUGIN_FILE).'" title="Published to Facebook" class="zWallPostSocialIcon"/></span>';
		}
    $html.='<div class="zWallStatusFooter">'.date('g:i a, F j, Y',  strtotime($item['date'])).
           '<div class="zWallStatusFooterActions">';
    if(wp_count_comments($item['id'])->approved)  {$html.=wp_count_comments($item['id'])->approved.' comment'; $html.=wp_count_comments($item['id'])->approved > 2 ? 's' : ''; }
    $html.='<span class="comment" title="Leave Comment" onClick="Wall.showCommentForm('.$item['id'].',this)"></span>
            
            <span class="expand" onClick="Wall.collapseComments('.$item['id'].',this)"></span>
    </div></div>';
		$html.='</div><div class="zWallClear"></div>';
    $html.='<div id="zWallComments'.$item['id'].'" class="zWallComments">';
    if(isset($item['commentable']) && $item['commentable'] &&isset($item['comments']) && !empty($item['comments']))
		$html .= '<div class="zWallPostSubtitle">Comments</div>';

		if(isset($item['comments']) && !empty($item['comments'])) {
			if($item['comments_count'] > 5) {
				$html .= '<a href="javascript:void(0)" id="more_comments'.$item['id'].'" style="font-size:10px;color:rgb(18, 144, 243)" onClick="Wall.moreComments('.$item['id'].','.($item['comments_count']-5).')">see '.($item['comments_count']-5).' more comments</a>';
				$html .= '<img src="'.plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE).'" style="margin:0 auto; display:none" id="more_comments_ajax'.$item['id'].'" class="embedLoader" alt="ajax"/>';
			}
		}
		$html.='<div id="comment_box' . $item['id'] . '" class="zWallCommentBox">';
		if(isset($item['comments']) && !empty($item['comments'])) {
			foreach($item['comments'] as $comment) {
				$html .= '<div class="zWallComment"><img src="'.plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE).'" style="display:none" class="embedLoader" alt="ajax"/><p id="zWallComment' . $comment['comment_id'] . '"><b>' . $comment['user_display_name'] . '</b>: '.$comment['content'].'<br/><span style="font-size:0.8em">'.date('g:i a, F j, Y',  strtotime($comment['comment_date'])).'</span>';
				if ($this->canUserManageWall())
					$html.=' <a href="javascript:void(0);" class="zWallFormDeleteComment" onClick="App.deleteComment('.$comment['comment_id'].','.$item['id'].',\''.wp_create_nonce('wpzing-security-nonce').'\');">Delete</a></p></div>';
				else $html.='</p></div>';
			}
		}

		$html .= '</div>';
		if(isset($item['commentable']) && $item['commentable']) {
			//Add form for posting comments on wall post
			$html .= $this->wall_comment_form($item['id'], $item['type']);
		}
		$html .= '</div></div></div>';
    
		return $html;
	}

	public function wall_comment_form($item_id, $type) {
		global $current_user;
		get_currentuserinfo();


		$html = '<div class="zWallClear"></div>';
		$html .= '<form action="" method="post" name="post_comment" id="post_comment' . $item_id . '" class="zWallCommentForm" onSubmit="return Wall.postComment('.$item_id.',\''.$this->nonce.'\')">';

		$author = $zsuserid = $email = '';

		if($current_user->ID == 1) {
			$author = get_metadata('user', 'zsdisplayname', true);
			$zsuserid = get_metadata('user', 'zsuserid', true);
			$email = $this->plugin->get_option('blog_api_user');
			if($author && $email)
				$html .= '<div style="display:none">';
			$html .= '<input type="text" name="comment_author" class="zWallCommentInput" id="comment_author' . $item_id . '" placeholder="Author..." maxlength="30" value="'.$author.'"/> *';
			$html .= '<input type="text" name="comment_author_email" class="zWallCommentInput" id="comment_email' . $item_id . '" placeholder="Email..." maxlength="30" value="'.$email.'"/> *';
			$html .= '<input type="text" name="comment_author_url" class="zWallCommentInput" id="comment_website' . $item_id . '" placeholder="Website..." maxlength="30"/>';
			$html .= '<input type="hidden" name="comment_zsuserid" id="comment_zsuserid' . $item_id . '" value="'.$zsuserid.'" />';
			if($author && $email)
				$html .= '</div>';
		} else {
			$html .= '<input type="text" name="comment_author" class="zWallCommentInput" id="comment_author' . $item_id . '" placeholder="Author..." maxlength="30"/> *';
			$html .= '<input type="text" name="comment_author_email" class="zWallCommentInput" id="comment_email' . $item_id . '" placeholder="Email..." maxlength="30"/> *';
			$html .= '<input type="text" name="comment_author_url" class="zWallCommentInput" id="comment_website' . $item_id . '" placeholder="Website..." maxlength="30"/>';
			$html .= '<input type="hidden" name="comment_zsuserid" id="comment_zsuserid' . $item_id . '" value="" />';

		}
		$html .= '<input type="text" name="comment_content" class="zWallCommentInput" id="comment' . $item_id. '" placeholder="Leave a comment..." maxlength="150"/> *';
		$html .= '<input type="hidden" value="' . $item_id . '" name="'.($type == 'zwall' ? 'comment' : 'zwall').'_post_ID"/>';

		$html .= '<p class="zWallGrayed" id="comment_button' . $item_id . '" style="text-align:left">';
		$html .= '<span><input type="submit" value="" id="post_comment_submit'.$item_id.'" class="comment_button"/> or hit Enter to post</span>';
		$html .= '<img src="'.plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE).'" style="margin:12px auto 0; display:none" id="post_comment_ajax'.$item_id.'" class="embedLoader" alt="ajax"/></p>';
		$html .= '</form>';
		$html .= '</div></div>';
		$html .= '<div class="zWallClear"></div>';
		$html .= '';
		return $html;
	}

	/**
	 * Function to call Facebook and Twitter api when page is loaded and get new content if exists (called from ajax function)
	 */
	public function getWallUpdate() {

		//Check ajax referer
		$this->plugin->check_ajax('wall');
		//If publishing from Facebook is turned on get Facebook statuses
		if ($this->plugin->get_option('publish_from_facebook'))
			$fb = $this->Facebook->getFbPosts();

		//If publishing from Facebook is turned on get Facebook statuses
		if ($this->plugin->get_option('publish_from_twitter'))
			$tw = $this->Twitter->getStatuses();


		//Array for storing social statuses
		$posts = array();
		//If there is new Facebook statuses
		if ($fb)
			foreach ($fb as $p) {
				$posts[] = $p;
			}
		//If there is new Twitter statuses
		if ($tw)
			foreach ($tw as $p) {
				$posts[] = $p;
			}

		//Sort array of objects by date
		usort($posts, array($this, 'sortreader_object'));
		//HTML for displaying new statuses
		$html = '';
		foreach ($posts as $post) {
			//For each status call function to generate HTML for displaying social statuses
			$html.=$this->generateSocialWallPost($post);

		}

		//Return generated HTML
		echo $html;
		//Exit the function
		die;
	}

	/**
	 * Get Twitter statuses that are not yet in database
	 */
	public function getTwitterStatuses() {
		$this->Twitter->getStatuses();
	}

	/**
	 * Get zWall settings
	 */
	public function getWallSettings() {
		//Calling Zingsphere api to get the list of Zingsphere articles that would be shown on zWall
		$response = $this->plugin->zingsphere_api_call('getWallSettings');
		if(!is_wp_error($response))
			return $response['settings'];
		else
			return array();
	}

	public function ajax_insert_wall_comment() {
		global $wpdb, $current_user;
		get_currentuserinfo();
		$this->plugin->check_ajax('wall', 'nonce');

		$comment = array(
						'comment_post_ID' => $post_id,
						'ping_status' => 'closed',
						'comment_type' => 'zwall_comment',
						'comment_content' => strip_tags($_GET['comment_content']),
		);
		$comment['comment_author'] = strip_tags($_GET['comment_author']);
		$comment['comment_author_email'] = strip_tags($_GET['comment_author_email']);
		$comment['comment_author_url'] = strip_tags($_GET['comment_author_url']);

		if(isset($_GET['comment_post_ID'])) {
			$comment['comment_post_ID'] = $_GET['comment_post_ID'];
			$id = wp_insert_comment($comment);
			if($id) {
				$comment = get_comment($id, ARRAY_A);
			}
		}	else if (isset($_GET['zwall_post_ID'])) {
			$comment['zwall_post_ID'] = $_GET['zwall_post_ID'];
			$id='zwall_comment';
		} else die;

		if($id) {
			$this->plugin->zingsphere_api_call('sendWallComment', array('comment' => $comment, 'zsuserid' => $_GET['comment_zsuserid']));

			$html = '<div class="zWallComment">
                  <img src="'.plugins_url('images/embed_loader.gif', ZING_PLUGIN_FILE).'" style="display:none" class="embedLoader" alt="ajax"/><p id="zWallComment' . $id . '"><b>' . $comment['comment_author'] . '</b>: ' . $comment['comment_content'].' <br/><span style="font-size:0.8em">'.date('g:i a, F j, Y',  strtotime($comment['comment_date'])).'</span>';
			if ($this->canUserManageWall())
				$html.=' <a href="javascript:void(0);" onClick="App.deleteComment(' . $id . ',' . $_GET['comment_post_ID'] . ' , \'' . $this->nonce . '\');" class="zWallFormDeleteComment">Delete</a>';
			$html.='</p></div>';
			echo $html;
		};
		die;
	}

	/**
	 * Delete comment on zWall status update
	 */
	public function deleteComment() {
		//Get id of comment that ajax call posted
		$id = $_POST['id'];
		//If current user is admin
		if ($this->canUserManageWall()) {
			//Delete comment with selected id
			if (!wp_delete_comment($id));
			echo 'Deleted';
			die;
		}
		return;
	}

	/**
	 * Check if current user have privileges to manage wall
	 */
	public function canUserManageWall() {
		//Set current user role
		if (current_user_can('administrator'))
			$user = 'administrator';
		else if (current_user_can('editor'))
			$user = 'editor';
		else if (current_user_can('contributor'))
			$user = 'contributor';
		//If current user is in array of users who have privileges to manage wall return true, else return false
		if (in_array($user, $this->plugin->get_option('user_roles')))
			return true;
		else
			return false;
	}

	/**
	 * Deleting a single feed
	 */
	function deleteFeed() {
		//Check ajax referer
		$this->plugin->check_ajax('wall');
		//Get id from ajax request
		$id = $_POST['id'];

		$response=$this->zingsphere_api_call('deleteFeed',array('id'=>$id));
		if(!is_wp_error($response))
			if($response['deleted']==true) {
				$feeds=$this->plugin->get_option('rssFeeds');
				unset($feeds[$id]);
				$this->plugin->set_option('rssFeeds',$feeds,true);

				echo 'ok';
				die;
			}

	}

	/**
	 * Function to determinate time ago from certain date
	 * @param datetime $datetime
	 * @param time $reference
	 */
	protected function ago($datetime, $reference = null) {
		//Convert to time passed datetime param
		$datetime = strtotime($datetime);
		//If reference time is null set reference time to now
		if ($reference == null) {
			$reference = time();
		}
		//Determine diference between passed date and reference time
		$diff = $reference - $datetime;
		//If diference is less than 60 sec
		if ($diff < 60) {
			$value = 'Few seconds ago';
		}
		//Otherwise if difference less than 2 min
		elseif ($diff < 120) {
			$value = 'A minute ago';
		}
		//Otherwise if difference less than an hour
		elseif ($diff < 3600) {
			$value = floor($diff / 60) . ' minutes ago';
		}
		//IF difference less than two hours
		elseif ($diff < 7200) {
			$value = 'An hour ago';
		}
		//If diference less than a day
		elseif ($diff < 86400) {
			//Determine how many hours passed
			if (date('j', $datetime) != date('j', $reference)) {
				$value = 'Yesterday';
			} else {
				$value = 'Few hours ago';
			}
		}
		//If difference less than two days
		elseif ($diff < 172800) {
			$value = 'Yesterday';
		}
		//Otherwise determine how many days are passed
		elseif ($diff < 2592000) {
			$value = floor($diff / 86400) . ' days ago';
		}
		//If difference is more than a month determine how many months are passed
		else {
			$value = floor($diff / 2629743);
			if ($value > 1)
				$value.= ' months ago';
			else
				$value.='month ago';
		} // if
		//Return obtained value
		return $value;
	}

	/**
	 * Sort array by date
	 * @param array $a
	 * @param array $b
	 */
	protected function sortreader($a, $b) {
		return strnatcmp($b['date'], $a['date']);
	}

	/**
	 * Sort array of objects by date
	 *
	 * @param array $a
	 * @param array $b
	 *
	 */
	protected function sortreader_object($a, $b) {
		return strnatcmp($b->date, $a->date);
	}

	/**
	 * Update Social settings
	 *
	 * @param array $settings
	 */
	public function socialSettings($settings=null) {
		if($settings === null) $setting = $_POST;
		if (!empty($settings))
			$this->log('debug', 'Updating zWall social settings');
		else
			$this->log('debug', 'Clearing zWall social settings');

		foreach(array('publish_from_facebook', 'publish_to_facebook', 'publish_from_twitter', 'publish_to_twitter') AS $key) {
			$this->plugin->set_option($key, isset($settings['social'][$key]));
		}

		//Update Zingsphere options
		$this->plugin->save_options();
		//Set message
		$this->plugin->message = 'Social settings updated';
	}

	/*
   * Update Social Feeds settings
   * @param array $_POST
	*/
	public function socialFeeds($_POST) {
		if (!empty($_POST))
			$this->log('debug', 'Updating zWall social settings');
		else
			$this->log('debug', 'Clearing zWall social settings');

		$zingsphere_options = get_option('zingsphere_options');
		//Reset socialFeeds settings
		$zingsphere_options['publish_feeds_from_facebook'] = 0;
		$zingsphere_options['publish_feeds_from_twitter'] = 0;
		//Turn on everything what is checked
		if (isset($_POST['socialFeeds']))
			foreach ($_POST['socialFeeds'] as $key => $checked) {
				$zingsphere_options[$key] = 1;
			}
		//Update Zingsphere options
		update_option('zingsphere_options', $zingsphere_options);
		//Set message
		$this->plugin->message = 'Social feeds settings updated';
	}

	/**
	 * Update zSticker settings
	 *
	 * @param array $_POST
	 */
	public function zStickerSettings($_POST) {
		if (!empty($_POST))
			$this->log('debug', 'Updating zWall zSticker settings');
		else
			$this->log('debug', 'Clearing zWall zSticker settings');

		
		// Update display options
		$this->plugin->set_option('zsticker_enable',isset($_POST['zsticker_enable']) ? true : false);

		if(isset($_POST['zstickerSettings']))
			$this->plugin->set_option('zstickerSettings', $_POST['zstickerSettings']);
		// Update options
		$this->plugin->save_options();
		// Set message
		$this->plugin->message = 'zSticker settings updated';
	}

	/**
	 * Defining who can manage zWall
	 * @param array $post
	 */
	protected function userRoles($post) {
		$zingsphere_options = get_option('zingsphere_options');
		//Adding administrator by default
		$zingsphere_options['user_roles'] = array('administrator');
		//If another user role is checked
		if ($post['user'])
		//Insert every user role that is checked into zingsphere options
			foreach ($post['user'] as $key => $user) {

				$zingsphere_options['user_roles'][] = $key;
			}
		$this->log('debug', 'User roles changed');
		//Save zingsphere options
		update_option('zingsphere_options', $zingsphere_options);
		return;
	}

	/*
   * Generate HTML for zWall social post
   * @param array $post
	*/

	protected function generateSocialWallPost($post) {

		$html = ' <div class="zWallPost ' . $post->post_type . ' social">
                        <div class="zWallPostWrapper">
                        <img src="'.plugins_url('images/avatar-bg.png',ZING_PLUGIN_FILE).'" class="userAvatar avatarBg" />
                        <div class="zWalluserImage ' . $post->post_type . 'UserImage">';


		if ($post->post_type == 'facebook') {
			$html.= '<a href="https://facebook.com/' . $this->plugin->get_option('facebook')->id . '" target="_blank">
                                        <img src="https://graph.facebook.com/' . $this->plugin->get_option('facebook')->id . '/picture" />
                                    </a>
                                    </div>';
      $html.='<p><a href="https://facebook.com/' . $this->plugin->get_option('facebook')->id . '" target="_blank" class="zWallFbName">'.$this->plugin->get_option('facebook')->username.'</a></p>';
			
      $html.='<p>'.$post->post_title.'</p>';
			$comments=get_comments('post_id='.$post->ID);
			if(!empty($comments)) {
        $html.='<div class="zWallFbComments">';
				foreach ($comments as $comment) {

					$html.= '<div class="zWallClear"></div>
            
                  <div class="zWallFbCommentWrapper">
                  <div class="zWallFbComment">
                            <a href="https://facebook.com/'.get_comment_meta($comment->comment_ID, 'comment_author_id', true).'" target="_blank"><img src="https://graph.facebook.com/' .get_comment_meta($comment->comment_ID, 'comment_author_id', true).'/picture" class="zWallFbCommentAvatar"/></a>
                            <div class="zWallFbCommentData">
                            <a href="https://facebook.com/'.  get_comment_meta($comment->comment_ID, 'comment_author_id', true).'" target="_blank">'.$comment->comment_author.'</a> '.$comment->comment_content.'<br/>';
					$html.='<span class="time">'. date('F j',  strtotime($comment->comment_date)) .' at '. date('g:i a',  strtotime($comment->comment_date)) . '</span>
                            </div></div></div>';
				}
        $html.='</div>';
			}
		} else {
			$html.='<a href="https://twitter.com/' . $this->plugin->get_option('twitter')->id . '" target="_blank">
                                    <img src="https://api.twitter.com/1/users/profile_image?screen_name=' . $this->plugin->get_option('twitter')->id . '&size=normal"  />
                                </a>
                                </div>';
      $html.='<p class="zWallTwitterName"><a href="https://twitter.com/'.$this->plugin->get_option('twitter')->id.'" target="_blank">'.$post->post_content.'</a></p>';
			$html.='<p class="zWallTweetText">'.$post->post_title.'</p>';
		}

		$html.='<div class="zWallPostStatus">
                                    ' . $this->ago($post->post_date) . '
                                    <img src="' .plugins_url('/images/'.$post->post_type.'-icon.png',ZING_PLUGIN_FILE). '" class="zWallPostIcon"  title="Published from ' . $post->post_type . '"/>
                                </div></div></div>
                                <div class="zWallClear"></div>';
		return $html;

	}

	/*
   * Add div in which to display search results
	*/
	protected function addSearch() {
		return '<div id="zSearch" class="zingram_div" style="display:none"></div>';
	}

	/**
	 * If publishing from social networks activated adding Javascript code to zWall page to check every minute for changes on social networks
	 */
	protected function addWallUpdate() {
		// If publishing from social networks activated
		if ($this->plugin->get_option('publish_from_facebook') || $this->plugin->get_option('publish_from_twitter'))
		//Return piece of Java Script code
			return '<script>
                           
                        jQuery(document).ready(function(){
                           
                        App.init("' . admin_url('admin-ajax.php') . '","' . $this->nonce . '");Embed.init("' . admin_url('admin-ajax.php') . '","' . $this->nonce . '");App.updateZWall("' . $this->nonce . '");App.zWallUpdate("' . $this->nonce . '");
                            
                            });
                        </script>';
		else
		//Otherwise return
			return '<script>
                                jQuery(document).ready(function(){App.init(\'' . admin_url('admin-ajax.php') . '\',"' . $this->nonce . '");Embed.init("' . admin_url('admin-ajax.php') . '","' . $this->nonce . '");});
                                </script>';
	}

	/**
	 * Check if user connected Zingsphere account with Facebook account
	 */
	public function getFbConnected() {
		return ($this->plugin->get_option('facebook')) ? true : false;
	}

	/**
	 * Check if user connected Zingsphere account with Twitter account
	 */
	public function getTwitterConnected() {
		return ($this->plugin->get_option('twitter')) ? true : false;
	}

	public function disconnectSocial() {
    
		$network=$_POST['network'];
    $this->plugin->set_option($network,false);
    $this->plugin->set_option('publish_feeds_from_'.$network,$network,0);
    $this->plugin->set_option('publish_to_'.$network,$network,0);
    $this->plugin->set_option('publish_from_'.$network,$network,0);

		$this->plugin->save_options();
		echo  'ok';
		die;
	}

	public function connectSocial() {
		$network  = $_POST['network'];
		$data =  $_POST['data'];
		$data=maybe_unserialize(stripslashes($data));
		$this->plugin->set_option($network,$data,true);
	}


	/**
	 * Search Zingsphere articles
	 */
	public function search() {
		//Check ajax referer
		$this->plugin->check_ajax('wall');
		//Get keyword param from ajax request
		$keyword = $_GET['keyword'];
		//Call Zingsphere api to get search results
		$followingarticles = $_GET['followingarticles'];
		$followingwallposts = $_GET['followingwallposts'];
		$myarticles = $_GET['myarticles'];
		$mywallposts = $_GET['mywallposts'];
    $logged=$this->canUserManageWall();
		$response = $this->plugin->zingsphere_api_call('search', array('search' => $keyword, $limit=>10, 'logged'=>$logged,
						'settings'=>array(
										'followingarticles' => $followingarticles,
										'followingwallposts' => $followingwallposts,
										'myarticles' => $myarticles,
										'mywallposts' => $mywallposts,
		)));

		//If there is no error in response
		if (!is_wp_error($response)) {
			//Get search results from response
			$wall = $response['search'];
			//if there is search results
			if (!empty($wall)) {
				//Echo results
				$html = '<div id="zWallAction">Showing search results, <span class="zWallLink" onclick="App.closeSearch();">dismiss</span></div>'
								.'<span class="zWallGrayed">Filters:</span>
                  <input type="checkbox" id="followingarticles" name="followingarticles" checked="checked" /> <label for="followingarticles" class="zWallTransform zWallChecked"></label><span class="zWallGrayed"> blog posts</span>'
								. '<input type="checkbox" id="mywallposts" name="mywallposts" checked="checked" /> <label for="mywallposts" class="zWallTransform zWallChecked"></label><span class="zWallGrayed"> wall posts</span>'
//								. '<input type="checkbox" id="myarticles" name="myarticles" '.($myarticles==1?'checked="checked"':'').' /> <label for="myarticles" class="'.($followingarticles==1 ? 'zWallTransform zWallChecked' : 'zWallTransform zWallCheckbox').'"></label><span class="zWallGrayed">ZS feeds</span>'
//								. '<input type="checkbox" id="followingwallposts" name="followingwallposts" '.($followingwallposts==1?'checked="checked"':'').' /> <label for="followingwallposts" class="'.($followingarticles==1 ? 'zWallTransform zWallChecked' : 'zWallTransform zWallCheckbox').'"></label><span class="zWallGrayed">wall feeds</span>'
								. '<div class="search_results">';
        $html.="<script>jQuery('#zSearch .zWallTransform').bind('click',function(){
          
           var name=jQuery(this).attr('for');
           
           if(jQuery(this).hasClass('zWallChecked')){
             jQuery('#'+name).removeAttr('checked');
             jQuery(this).removeClass('zWallChecked');
             jQuery(this).addClass('zWallCheckbox'); 
           }
           else{
             jQuery('#'+name).attr('checked','checked');
             jQuery(this).addClass('zWallChecked');
             jQuery(this).removeClass('zWallCheckbox');
           } 
         })
    </script>";
        
				foreach($wall AS $item) {
					if($item['type'] == 'wall_post'){
            $html .='<div class="zWallmywallposts">';
						$html .= $this->generateZwall_wallpost($item);
            $html .='</div>';
          }
					else if($item['type'] == 'article'){
            $html .='<div class="zWallfollowingarticles">';
						$html .= $item['html'];
            $html .='</div>';
      }   
				}

				$html .= '</div>'.
								'<script>
									
                jQuery("#zSearch .zWallTransform").bind("click",function(){
                  
                
                if(jQuery(this).hasClass("zWallChecked")){
                
                  jQuery("#zSearch .zWall"+jQuery(this).attr("for")).show();
                
                }
                else jQuery("#zSearch .zWall"+jQuery(this).attr("for")).hide();

                })

                 jQuery(".zFollowButton, .zUnfollowButton").bind("click",function(){
                
                var button=jQuery(this);
                var id=jQuery(button).parent().attr("id").replace("follow_","");
                
                jQuery(button).hide()
                
                jQuery(button).parent().parent().append("<img src=\''. plugins_url('images/embed_loader.gif',ZING_PLUGIN_FILE).'\' class=\'zWallLoader\' />");
                             
               
                
                data={action:"followZsBlog",id:id};
               
                jQuery.post("'.  admin_url('admin-ajax.php') .'" , data , function(response){
                          if(response=="followed"){
                            jQuery(button).parent().parent().find(".zWallLoader").remove();
                            jQuery(button).removeClass("zFollowButton").addClass("zUnfollowButton");
                            jQuery(button).attr("title","Unfollow blog");
                            jQuery(button).show();
                            }
                           if(response=="unfollowed"){
                              jQuery(button).parent().parent().find(".zWallLoader").remove();
                              jQuery(button).removeClass("zUnfollowButton").addClass("zFollowButton");
                              jQuery(button).attr("title","Follow Blog");
                              jQuery(button).show();
                            }

                });
                
                

                    })
                </script>';
				echo $html;
			} else {
				//If there is no search results display message on zWall
				echo '<p class="zWallGrayed" style="padding-top:30px;">There are no search results, <span class="zWallLink" onclick="App.closeSearch();">dismiss</span></p>';
			}
		}
		die;
	}

	/**
	 * Return css file
	 */
	public function getWallCss() {
		//Read content of style.css
		$css = file_get_contents(plugins_url('css/style.css', ZING_PLUGIN_FILE), FILE_USE_INCLUDE_PATH);
		if ($css)
		//Return content of style.css
			return $css;
		else
		//If content can't be retrieved.
			$this->log('WARNING', 'Can\t get content of css file');
	}

	/**
	 * Change css file
	 */
	public function saveCss($post) {
		$this->log('debug', 'User changing css file');
		//Open style.css
		$file = fopen(ZING_ROOT . '/css/style.css', 'w');
		//Wrtite data that user submitted
		fwrite($file, trim($post['zWallCss']));
		//Close style.css
		fclose($file);
		return;
	}

	/*
   * Insert zWall link in menu that user choosed
	*/

	public function insertZwallInMenu($post) {

		$this->log('debug', 'User choosing menu for Zwall');
		//Get menu ID from post
		$menu_id = $post['zWall_menu_select'];
    
		if($this->plugin->get_option('menu_choosen') !=$menu_id) {
      
			if($this->plugin->get_option('menu_choosen')!='none' && $this->plugin->get_option('menu_choosen')!=false ){
				global $wpdb;
        $wpdb->query('DELETE FROM '.$wpdb->posts.' WHERE post_type="nav_menu_item" AND post_name="zwall"');
                }
			//If menu_id is defined
      
			if ($menu_id!='none') {
				//Update options and save it in zingsphere_options array
				wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' => __('zWall'),
				'menu-item-url' => get_permalink($this->plugin->get_option('zwall_page_id')),
				'menu-item-status' => 'publish'));
        
				$this->plugin->set_option('menu_choosen',$menu_id,true);
        $this->plugin->message='zWall inserted in ';
				}
        else {
				//If there is no menu_id passed
				$this->plugin->set_option('menu_choosen','none',true);
        }
			}
		}
	

	public function log($severity, $message, $data = array()) {
		$this->plugin->log($severity, $message, $data, $this->name);
	}

	public function deleteMenuItem() {
		$zingsphere_options = get_option('zingsphere_options');
	}

	/*
   * zTodo
	*/

	public function todo() {
		$item = $_POST['item'];
		if ($item) {
			$zingsphere_options = get_option('zingsphere_options');
			$zingsphere_options['todo_' . $item] = 0;
			update_option('zingsphere_options', $zingsphere_options);
			echo 'updated';
			die;
		}
		echo -1;
		die;
	}

	/*
   * Delete status update
	*/
	public function deleteStatusUpdate() {
		//Check ajax referer
		$this->plugin->check_ajax('wall');
		if ($this->canUserManageWall()) {
			$post = get_post($_POST['id']);
			if($post) {
				wp_delete_post($post->ID, true);
			}
			echo 'Deleted';
			die;
		}
		echo -1;
		die;
	}

	/*
   * Rename zWall page
	*/

	public function renamezWall($post) {
		if ($post['zwall_name'] != '') {
			$page = $this->get_zwall_page();
			$page->post_title = $post['zwall_name'];
			wp_update_post($page);
			$this->plugin->message = 'zWall page renamed.';
		} else {
			return 'Page name must not be empty!';
		}
	}


	/* Facebook like action */
	public function facebookLike() {
		$this->Facebook->facebookLike();
	}

	/* Facebook Comment action */
	public function facebookComment() {
		$this->Facebook->facebookComment();
	}

	/* Favorite Tweet */
	public function favoriteTweet() {
		$this->Twitter->favoriteTweet();
	}

	/* Destroy Favorite */
	public function destroyFavorite() {
		$this->Twitter->deleteFavorite();
	}

	/* Reply */
	public function twitterReply() {
		$this->Twitter->reply();
	}

	/* Retweet */
	public function twitterRetweet() {
		$this->Twitter->retweet();
	}

	/* destroy Retweet */
	public function destroyRetweet() {
		$this->Twitter->deleteRetweet();
	}

	/* Destroy Tweet */
	public function destroyTweet() {
		$this->Twitter->deleteTweet();
	}

	public function ajax_receiveWallComment() {
		$blog_api_token = $_POST['blog_api_token'];
		if($blog_api_token && $this->plugin->get_option('blog_api_token') == $blog_api_token) {
			$post_comment = $_POST['comment'];
			if($post_comment && is_array($post_comment)) {
				$post = get_post($post_comment['post_ID']);
				if($post && $post->post_type == 'zwall') {
					$comment = array(
									'comment_post_ID' => $post->ID,
									'comment_author' => $post_comment['comment_author'],
									'comment_author_email' => $post_comment['comment_author_email'],
									'comment_author_url' => $post_comment['comment_author_url'],
									'comment_content' => $post_comment['comment_content'],
									'comment_approved' => $post_comment['comment_approved'],
									'ping_status' => 'closed',
									'comment_type' => 'zwall_comment',
					);
					wp_insert_comment($comment);
					echo 'done';
				} else echo 'wrong post';
			} else echo 'wrong args';
		} else echo 'wrong token';
		die;
	}

	public function ajax_repostWall() {
		$post_id = $_POST['id'];
		$response = $this->plugin->zingsphere_api_call('getWallPost', array('id' => $post_id));
		if(!is_wp_error($response)){
			$post = array(
						'comment_status' => 'closed',
						'ping_status' => 'closed',
						'post_content' => $response['getWallPost']['content'],
						'post_status' => 'publish',
						'post_title' => 'zWall post',
						'post_type' => 'zwall',
			);
		}
		$id = wp_insert_post($post);
		if($id) {
			add_post_meta($id, 'repost', $response['getWallPost']['id'], true);
			add_post_meta($id, 'repost_user_zs_profile', $response['getWallPost']['user_zs_profile'], true);
			add_post_meta($id, 'repost_user_display_name', $response['getWallPost']['user_display_name'], true);
			if (isset($response['getWallPost']['embeded']) && !empty($response['getWallPost']['embeded'])) {
				add_post_meta($id, 'embeded', $response['getWallPost']['embeded'], true);
			}
			$f=$this->Facebook->postStatusMessage($text,$id);
			if($f) add_post_meta($id, 'published_to_facebook', 1,true);

			$t=$this->Twitter->postStatusMessage($text,$id);
			if($t) add_post_meta($id, 'published_to_twitter', 1,true);
      
			$this->sendWallPost($id);

			$html = $this->generateZwall_wallpost($this->post2wall($id));
			$result = array('success' => true, 'id' => $id, 'post' => $html);
		} else {
			$result = array('success' => false, 'error' => 'error!');
		}
		echo json_encode($result);
		die;
	}

	/**
	 * Show more on zWall
	 */
	public function showMore() {
    $page=$_GET['page'];
    print json_encode($this->generateZwall_wallposts($page,10,'',true));
		die;
	}

	function followZsBlog() {
		//$this->plugin->check_ajax('wall');
		$id=$_POST['id'];
		if($id) {
			$response=$this->plugin->zingsphere_api_call('followZsBlog',array('id'=>$id));
			if(!is_wp_error($response)) {
				if($response['status']) {
					echo 'followed';
					die;
				} else {
					echo 'unfollowed';
					die;
				}
			}
		}
	}

	function newFollower() {
		$zingsphere_options=  get_option('zingsphere_options');
		$zingsphere_options['new_followers'][]=$_POST;
		update_option('zingsphere_options', $zingsphere_options);
	}

	function resetzFollowers() {
		$zingsphere_options=  get_option('zingsphere_options');
		$zingsphere_options['new_followers']=null;
		update_option('zingsphere_options', $zingsphere_options);
		echo 'reset';
		die;
	}

}

?>
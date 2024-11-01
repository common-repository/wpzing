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

if (!class_exists('WP_Http'))
	include_once( ABSPATH . WPINC . '/class-http.php' );
include_once 'twitteroauth.php';
include_once 'twitterbase.php';
include_once 'zserver.php';

if(!class_exists('OAuthException'))
	require_once(ZING_ROOT.'/modules/wall/classes/OAuth.php');

class zTwitter extends TwitterBase {

	/**
	 * the OAuth Twitter credentials
	 * @access private
	 */
	var $appKey = null;
	var $appSecret = null;
	var $baseUrl = null;
	var $rateLimits=null;
	var $requestTokenEndpoint = null;
	var $authorizeEndpoint = null;
	var $accessTokenEndpoint = null;
	var $callBackEndPoint = null;
	var $accessToken;
	var $oathToken;
	var $oathTokenSecret;
	var $RequestUri;
	var $dbHandler;

	/**
	 * Fills in the credentials
	 *
	 */
	public function __construct($plugin) {
		//$this->key = '_zsTWkey_' . md5($this->twitter_api_show_url);
		$this->plugin=$plugin;
		if($plugin->get_option('publish_feeds_from_twitter') || $plugin->get_option('publish_from_twitter') || $plugin->get_option('publish_to_twitter')) {
			$this->defaultOptions = array(
							'title' => null,
							'errmsg' => null,
							'fetchTimeOut' => '2',
							'username' => null,
							'items' => 10,
							'showts' => 3600
			);
			$this->key=array();
			$this->twitter=$plugin->get_option('twitter');

			add_action('wp_ajax_twitterGetTimeline', array(&$this, 'getTimeline'));
			add_action('wp_ajax_twitterFavorite', array(&$this, 'favoriteTweet'));
			add_action('wp_ajax_twitterDestroyFavorite', array(&$this, 'deleteFavorite'));
			add_action('wp_ajax_twitterReply', array(&$this, 'reply'));
			add_action('wp_ajax_nopriv_twitterReply', array(&$this, 'reply'));
			add_action('wp_ajax_twitterRetweet', array(&$this, 'retweet'));
			add_action('wp_ajax_nopriv_twitterRetweet', array(&$this, 'retweet'));
			add_action('wp_ajax_destroyRetweet', array(&$this, 'deleteRetweet'));
			add_action('wp_ajax_nopriv_destroyRetweet', array(&$this, 'deleteRetweet'));
			add_action('wp_ajax_destroyTweet', array(&$this, 'deleteTweet'));
			add_action('wp_ajax_nopriv_destroyTweet', array(&$this, 'deleteTweet'));
			

		}
	}

	/*Get Statuses*/
	function getStatuses() {

		global $wpdb;
		//Get latest Twitter status from database

		//Reference id is id of the newest post in database

		$id= $this->plugin->get_option('last_tw_post') ? $this->plugin->get_option('last_tw_post') : 1;

		//Call api to get Twitter statuses which id is greater than reference id
		$this->RequestUri=$this->getUserTimeline(array('since_id'=>$id),'json',$this->twitter);
		$this->key['user_timeline']='_zsTWkey_' . md5($this->RequestUri);

		$response = requestHandler ($this->key['user_timeline'])
						->expires_in (120) // cache for 2 minutes
						->updates_with (array($this, 'parseJson'), array($options))
						->get ();
    
    $tweets=array();
		if(!is_wp_error($response)) {
			//If api returned some results
			if(!empty($response)) {

				$this->plugin->set_option('last_tw_post',$response[0]->id_str,true);

				foreach ($response as $status) {

					//Insert each status in database
					if(!$wpdb->insert($wpdb->posts,array(
					'post_title'=>$this->formatTwitterLink($status),
					'post_type'=>'twitter',
					'post_date'=>date('Y-m-d H:i:s',strtotime($status->created_at)),
          'post_content'=>$status->user->name
					)))
						$this->plugin->log('error','Error while inserting Twitter data into zwall_social table');
					else {
            $id=$wpdb->insert_id;
            $tweets[]=get_post($wpdb->insert_id);
						add_post_meta($id,'twitter_id',$status->id_str);
					}
          
				}
				//Return data that api has returned
				return $tweets;
			}
			else
			//Otherwise return nothing
				$this->plugin->log('warning','Twitter returned an error',$data=array(),'twitter');
		}
		return false;
	}


	public function postStatusMessage($text,$id) {
		if($this->plugin->get_option('publish_to_twitter')) {
			global $wpdb;
			$this->RequestUri=$this->updateStatus($text,null,'json',$this->twitter);
			$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));
			if($response['headers']['status']>=200 && $response['headers']['status']<=300) {
				$this->plugin->set_option('last_tw_post',json_decode($response['body'])->id_str,true);
				return true;
			}
		}
		return false;
	}

	/*
  * Get Twitter 
	*/
	public function getTwitter() {

		//$html='<div id="zWallTwitter" class="zingram_div" style="display:none;">';
		$html = '';
		if($this->plugin->get_option('publish_feeds_from_twitter')) {
			$html.='<div class="zingramLoader"></div><script>jQuery(document).ready(function(){Twitter.init(\''.  admin_url('admin-ajax.php') .'\',\''.  wp_create_nonce('wpzing-security-nonce').'\');Twitter.load()})</script>';
		}
		else if(!$this->plugin->get_option('twitter'))
		//$html.='<script type="text/javascript" src="'.ZING_WWW_BASE.'/users/check_signin"></script>
			$html.='<iframe src="'.ZING_WWW_BASE.'/users/social_networks?token='.$this->plugin->get_option('blog_api_token').'" width="640" height="260" border="0"></iframe>
                <a href="http://support.zingsphere.com/zingsphere/topics/why_should_i_connect_zwall_with_my_facebook-2asa9"  target="_blank" title="Click for more information" class="m5L">Why should I connect my zWall with my Facebook account?</a>';
		return $html;
	}

	/*
  * Get Twitter timeline
	*/
	public function getTimeline() {

		if (!current_user_can('administrator')) {
			return  '';
			die;
		}
		if ($this->plugin->get_option('publish_feeds_from_twitter') != 1 && !$this->plugin->modules['wall']->getTwitterConnected()) {

			//return  $html.='<script type="text/javascript" src="'.ZING_WWW_BASE.'/users/check_signin"></script><iframe src="'.ZING_WWW_BASE.'/users/social_networks?token='.$this->plugin->get_option('blog_api_token').'" width="100%" height="300px" border="0"></iframe> <a href="http://support.zingsphere.com/zingsphere/topics/why_should_i_connect_zwall_with_my_twitter-cn5qd" target="_blank" title="Click for more information" class="m5L">Why should I connect my zWall with my Twitter account?</a>';
			return  $html.='<iframe src="'.ZING_WWW_BASE.'/users/social_networks?token='.$this->plugin->get_option('blog_api_token').'" width="100%" height="300px" border="0"></iframe> <a href="http://support.zingsphere.com/zingsphere/topics/why_should_i_connect_zwall_with_my_twitter-cn5qd" target="_blank" title="Click for more information" class="m5L">Why should I connect my zWall with my Twitter account?</a>';
			die;
		}


		$options = array(
						'screen_name' => $this->twitter->id,
						'user_id' => $this->twitter->id,
						'count' => 50,
						'include_entities' => 'true',
						'transport' => null
		);

		$this->RequestUri = $this->getHomeTimeLine($options, 'json',$this->twitter);

		$this->key['home_timeline'] = '_zsTWkey_' . md5($this->RequestUri);

		$response = requestHandler ($this->key['home_timeline'])
						->expires_in (120) // cache for 5 minutes
						->updates_with (array($this, 'parseJson'), array($options))
						->get ();


		if(!is_wp_error($response)) {
			$this->checkForErrors();

			if(!is_wp_error($response))
				if($response) {
					$html.='<div id="zWallTwitterContent">';
					foreach ($response as $tw) {
						if($tw->retweeted_status) {

							$html.=$this->formatRetweet($tw);
						}
						else {
							$html.=$this->formatTweet($tw);
						}
					}

				}

			$html.="</div><script>
      jQuery('.zWallTweet').hover(function(){
        jQuery(this).find('.zTweetExtraActions').show();
      },function(){
        if(!jQuery(this).find('.zTweetExtraActions').hasClass('visibleFixed'))
        jQuery(this).find('.zTweetExtraActions').hide();
        
      })</script>";
			return $html;
			die;
		}

		return '';
		die;
	}


	public function favoriteTweet() {

		$id=$_POST['id'];
		if($id) {

			$this->RequestUri=$this->createFavorite($id,'json',$this->twitter);

			$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));

			if($response['headers']['status']>=200 && $response['headers']['status']<=300) {
				echo 1;
				die;
			}
			echo -1;
			die;
		}

	}

	public function retweet() {

		$id=$_POST['id'];
		if($id) {

			$this->RequestUri=$this->createRetweet($id,'json',$this->twitter);


			$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));
			if($response['headers']['status']>=200 && $response['headers']['status']<=300) {
				echo true;
				die;
			}
			echo false;
			die;
		}

	}

	public function deleteRetweet() {

		$id=$_POST['id'];
		if($id) {

			$this->RequestUri=$this->getRetweetedByMe(array(),'json',$this->twitter);
			$response=wp_remote_get($this->RequestUri,array('sslverify'=>false));
			if($response['headers']['status']>=200 && $response['headers']['status']<=300)
				foreach (json_decode($response['body']) as $retweet) {

					if ($retweet->retweeted_status->id_str==$id) {

						$this->RequestUri=$this->destroyStatus(array('id'=>$retweet->id_str),'json',$this->twitter);
						$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));
						if($response['headers']['status']>=200 && $response['headers']['status']<=300)
							echo true;
						else
							echo false;
						die;
					}

				}
			$this->RequestUri=$this->destroyStatus(array('id'=>$id),'json',$this->twitter);

			$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));
			if($response['headers']['status']>=200 && $response['headers']['status']<=300) {
				echo true;
				die;
			}

		}
		echo FALSE;
		die;

	}

	public function deleteTweet() {

		$id=$_POST['id'];
		if($id) {

			$this->RequestUri=$this->destroyStatus(array('id'=>$id),'json',$this->twitter);

			$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));

			if($response['headers']['status']>=200 && $response['headers']['status']<=300) {
				echo true;
				die;
			}

		}
		echo FALSE;
		die;

	}

	public function deleteFavorite() {

		$id=$_POST['id'];
		if($id) {

			$this->RequestUri=$this->destroyFavorite($id,'json',$this->twitter);

			$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));

			if($response['headers']['status']>=200 && $response['headers']['status']<=300) {
				echo true;
				die;
			}

		}
		echo FALSE;
		die;

	}

	public function reply() {
		$id=$_POST['id'];
		$status=$_POST['status'];
		if($id) {

			$this->RequestUri=$this->updateStatus($status,$id,'json',$this->twitter);
			$response=wp_remote_post($this->RequestUri,array('sslverify'=>false));
			if($response['headers']['status']>=200 && $response['headers']['status']<=300) {
				echo true;
				die;
			}

		}
		echo FALSE;
		die;

	}



	public function formatTweet($tw) {

		$html = '
                      <div class="zWallTweet zTweetHover" id="'.$tw->id_str.'">
                      <span class="'. $this->getTweetClass($tw) .'">&nbsp;</span>
                      <div class="zWallTweetContent">    
                      <a href="http://twitter.com/'.$tw->user->screen_name.'" target="_blank">
                        <img src="' . $tw->user->profile_image_url . '" class="zWallPhoto" />
                      </a>
                        
                        <a href="http://twitter.com/'.$tw->user->screen_name.'" class="zWallTweetName" target="_blank">' . $tw->user->name . '</a>
                        <a href="http://twitter.com/'.$tw->user->screen_name.'"  target="_blank" class="zWallTwitterUsername"> @'.$tw->user->screen_name.'</a>
                        
                        <div class="zWallClear"></div>
                        '.$this->formatTwitterLink($tw).
                      $this->getTwitterActions($tw).
                      $this->getExpandContent($tw).
                      '
                      </div>
                      <div id="zRetweet'.$tw->id_str.'" class="zWallRetweet" style="display: none" title="Retweet this to your followers?">
                          <div class="zWallTweet">
                      <div class="zWallTtweetContent">    
                      <a href="http://twitter.com/'.$tw->user->screen_name.'" target="_blank">
                        <img src="' . $tw->user->profile_image_url . '" class="zWallPhoto" />
                      </a>
                        <div class="zWallTweetName">
                        <a href="http://twitter.com/'.$tw->user->screen_name.'" class="zWallTweetName" target="_blank">' . $tw->user->name . '</a>
                        <span><a href="http://twitter.com/'.$tw->user->screen_name.'"  target="_blank"> @'.$tw->user->screen_name.'</a></span>
                        </div>
                        <div class="zWallClear"></div>
                        <div class="zWallTweetText">
                        '.$tw->text.'
                            </div>
                      </div>
                      </div>
                      </div>
                      <div class="zWallClear"></div>';

		return $html;

	}

	public function formatRetweet($tw) {



		$html.='<div class="zWallRetweetData"><i class="zWallTweetRetweetBadge">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i> Retweeted by <a href="'.$tw->user->screen_name.'" target="_blank">'.$tw->user->name.'</a></div>';

		$html = '
                      <div class="zWallTweet zTweetHover" id="'.$tw->retweeted_status->id_str.'">
                      <span class="'. $this->getTweetClass($tw) .'">&nbsp;</span>
                      <div class="zWallTweetContent">    
                      <a href="http://twitter.com/'.$tw->retweeted_status->user->screen_name.'" target="_blank">
                        <img src="' . $tw->retweeted_status->user->profile_image_url . '" class="zWallPhoto" />
                      </a>
                        <div class="zWallTweetName">
                        <a href="http://twitter.com/'.$tw->retweeted_status->user->screen_name.'" class="zWallTweetName" target="_blank">' . $tw->retweeted_status->user->name . '</a>
                        <span><a href="http://twitter.com/'.$tw->retweeted_status->user->screen_name.'"  target="_blank"> @'.$tw->retweeted_status->user->screen_name.'</a></span>
                        </div>
                        <div class="zWallClear"></div>
                        '.$this->formatTwitterLink($tw->retweeted_status).
						'<div class="zWallRetweetData"><i class="zWallTweetRetweetBadge">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i> Retweeted by <a href="https://twitter.com/'.$tw->user->screen_name.'" target="_blank">'.$tw->user->name.'</a></div>'
						.$this->getTwitterActions($tw->retweeted_status).
						$this->getExpandContent($tw->retweeted_status).
						'
                      </div>
                      <div id="zRetweet'.$tw->retweeted_status->id_str.'" class="zWallRetweet" style="display: none" title="Retweet this to your followers?">
                          <div class="zWallTweet">
                      <div class="zWallTtweetContent">    
                      <a href="http://twitter.com/'.$tw->retweeted_status->user->screen_name.'" target="_blank">
                        <img src="' . $tw->retweeted_status->user->profile_image_url . '" class="zWallPhoto" />
                      </a>
                        <div class="zWallTweetName">
                        <a href="http://twitter.com/'.$tw->retweeted_status->user->screen_name.'" class="zWallTweetName" target="_blank">' .$tw->retweeted_status->user->name . '</a>
                        <span><a href="http://twitter.com/'.$tw->retweeted_status->user->screen_name.'"  target="_blank"> @'.$tw->retweeted_status->user->screen_name.'</a></span>
                        </div>
                        <div class="zWallClear"></div>
                        <div class="zWallTweetText">
                        '.$tw->retweeted_status->text.'
                            </div>
                      </div>
                      </div>
                      </div>
                      <div class="zWallClear"></div>';

		return $html;

	}




	/**
	 * Format links obtained from Twitter
	 */
	public function formatTwitterLink($tw) {

		if($tw->entities) {

			if($tw->entities->hashtags) {
				foreach ($tw->entities->hashtags as $hash) {

					$tw->text=str_replace('#'.$hash->text , '<a href="https://twitter.com/search?q=#' . $hash->text . '&src=hash" target="_blank">#'.$hash->text.'</a>', $tw->text);
				}

			}

			if($tw->entities->urls) {

				foreach ($tw->entities->urls as $link) {

					$tw->text=str_replace($link->url , '<a href="'. $link->expanded_url .'" target="_blank">'.$link->display_url.'</a>', $tw->text);

				}

			}


			if($tw->entities->user_mentions) {

				foreach ($tw->entities->user_mentions as $user) {

					$tw->text=str_replace('@'.$user->screen_name , '<a href="https:/twitter.com/'.$user->screen_name.'" target="_blank">@'.$user->screen_name.'</a>', $tw->text);

				}

			}

		}
		return $tw->text;

	}



	public function getExpandContent($tw) {



		$html.='</div><div class="zTweetExpandContent" style="display:none">';

		if ($tw->entities->media) {

			$html.='<a href="'.$tw->entities->media[0]->expanded_url.'" target="_blank"><img src="'.$tw->entities->media[0]->media_url.'" width="" height="" /></a>';


		}

		if($tw->retweet_count) {
			$html.='<ul class="zTweetData">';
			$html.='<li class="zWallTweetCount"> <a href="https://twitter.com/'.$tw->user->screen_name.'/status/'.$tw->id_str.'" target="_blank" ><strong>'.$tw->retweet_count.'</strong></a></li>
                            <li class="zWallTweetCountGray">RETWEETS</li></ul>';

		}
		$html.='<div class="zTwitterDate">';
		$html.=date('g:i A - j M y',  strtotime($tw->created_at)).' - <a href="https://twitter.com/'.$tw->user->screen_name.'/status/'.$tw->id_str.'" target="_blank">Details</a>';
		$html.='</div>';


		$html.='<div class="zTwitterReply">
                        <div class="zTwitterReplyContent">
                            <textarea class="zTwitterReplyText" id="reply-to-'.$tw->id_str.'" maxlength="140" onKeyUp="Twitter.counter(\''.$tw->id_str.'\',\''.$tw->user->screen_name.'\')" onFocus="Twitter.focusText(\''.$tw->id_str.'\',\''.$tw->user->screen_name.'\')" >Reply to @'.$tw->user->screen_name.'</textarea>                            
                        <div><input type="button" disabled="" onClick="Twitter.reply(\''.$tw->id_str.'\',\''.$tw->user->screen_name.'\')" class="zWallTweetButton" value="Tweet" id="zTweetButton'.$tw->id_str.'" style="display:none"/><span class="zTwitterCounter" id="zTwitterCounter'.$tw->id_str.'"></span></div>   
                        </div>
                </div>';
		$html.='</div>';
		return $html;
	}


	public function getTwitterActions($tw) {
		$links='<div class="zWallTweetActions"><ul>';
		if($tw->entities->media) {

			foreach ($tw->entities->media as $media) {

				switch ($media->type) {

					case 'photo': $links.='<li class="zTwitterAction" ><a href="Javascript:void(0)" class="zWallTweetExpand" onClick="Twitter.expand(\''.$tw->id_str.'\',this,true)" >View Photo</a></li></ul>';
						break;
					default:      $links.='<li class="zTwitterAction" ><a href="Javascript:void(0)" class="zWallTweetExpand" onClick="Twitter.expand(\''.$tw->id_str.'\',this,false)" >Expand</a></li></ul>';
						break;
				}
			}
		}
		else $links.='<li class="zTwitterAction" ><a href="Javascript:void(0)" class="zWallTweetExpand" onClick="Twitter.expand(\''.$tw->id_str.'\',this,false)" >Expand</a></li></ul>';
		if($tw->entities->media)
			$links.='<ul class="zTweetExtraActions" style="display:none"><li class="zTwitterAction"><a href="Javascript:void(0)" onClick="Twitter.replyLink(\''.$tw->id_str.'\',this,true)" ><i class="zWallTweetReply">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>Reply</a></li>';
		else
			$links.='<ul class="zTweetExtraActions" style="display:none"><li class="zTwitterAction"><a href="Javascript:void(0)" onClick="Twitter.replyLink(\''.$tw->id_str.'\',this,false)" ><i class="zWallTweetReply">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>Reply</a></li>';
//            die;
		if($tw->user->screen_name==$this->twitter->id) {
			$links.='<li class="zTwitterAction"><a href="Javascript:void(0)" onClick="Twitter.destroyTweet(\''.$tw->id_str.'\')" ><i class="zWallTweetDelete">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>Delete</a>';
		}
		else {
			if($tw->retweeted)
				$links.='<li class="zTwitterAction"><a href="Javascript:void(0)" onClick="Twitter.destroyRetweet(\''.$tw->id_str.'\')" class="zWallTweetActiveRetweet" ><i class="zWallTweetRetweeted">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>Retweeted</a>';
			else
				$links.='<li class="zTwitterAction"><a href="Javascript:void(0)"  class="retweetLink" onClick="Twitter.modal(\''.$tw->id_str.'\')"><i class="zWallTweetRetweet">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>Retweet</a></li>';
		}
		if($tw->favorited )
			$links.='<li class="zTwitterAction"><a href="Javascript:void(0)" class="zWallTweetActiveFavorite" onClick="Twitter.destroyFavorite(\''.$tw->id_str.'\')" ><i class="zWallTweetFavorited">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>Favorited</a></li>';
		else $links.='<li  class="zTwitterAction"><a href="Javascript:void(0)" class="favoriteLink" onClick="Twitter.favorite(\''.$tw->id_str.'\')"><i class="zWallTweetFavorite">&nbsp;&nbsp;&nbsp;&nbsp;</i>Favorite</a></li>';
		$links.='</ul></div><div class="zWallClear"></div>';
		return $links;


	}

	public function checkForErrors() {

		if($this->defaultOptions['errmsg']) {

			return $this->defaultOptions['errmsg'];

		}
		else {

			if($this->rateLimits["x-ratelimit-remaining"]<5) {

				return 'You\'ve reached Twitter requests rate limit.';

			}

		}


	}


	/**
	 * Pulls the JSON feed from Twitter and returns an array of objects
	 *
	 * @param array $widgetOptions - settings needed to get feed url, etc
	 * @return array
	 */
	public function checkResponse($requestResponse) {
		//hardcoded header response

		if (!is_wp_error($requestResponse) &&
						$requestResponse['response']['code'] >= 200 &&
						$requestResponse['response']['code'] < 300) {
			return true;
		} else {
			$this->defaultOptions['errmsg'] = __('Response Code =' . $requestResponse['response']['code'] . 'Response message=' . $requestResponse['response']['message'] . 'Error Twitter Response', 'Zingsphere');
			return false;
		}
	}

	public function parseJson() {

		$feedUrl = $this->getRequestUrl();

		$requestResponse = wp_remote_request($feedUrl, array('timeout' => $this->defaultOptions['fetchTimeOut'],'sslverify'=>false));

		if (!is_wp_error($requestResponse) &&
						$requestResponse['response']['code'] >= 200 &&
						$requestResponse['response']['code'] < 300) {
			if (isset($requestResponse['headers']['x-ratelimit-limit'])) {
				$this->rateLimits['x-ratelimit-limit'] = $requestResponse['headers']['x-ratelimit-limit'];
			}

			if (isset($requestResponse['headers']['x-ratelimit-remaining'])) {
				$this->rateLimits["x-ratelimit-remaining"]=$requestResponse['headers']['x-ratelimit-remaining'];
			}

			if (isset($requestResponse['headers']['x-ratelimit-reset'])) {
				$this->rateLimits["x-ratelimit-reset"]=date('jS F Y h:i:s A (T)', $requestResponse['headers']['x-ratelimit-reset']);
			}


			$decodedResponse = json_decode($requestResponse['body']);

			if (empty($decodedResponse) && !is_array($decodedResponse)) {
				if (!isset($this->defaultOptions['errmsg']))
					$this->defaultOptions['errmsg'] = 'Invalid Twitter Response.';
				return 0;
			}
			elseif (empty($decodedResponse)) {
				if (!isset($this->defaultOptions['errmsg'])) {
					$this->defaultOptions['errmsg'] = 'No data found';
					return 0;
				}
			} elseif (!empty($decodedResponse->errors)) {
				$this->defaultOptions['errmsg'] = $decodedResponse->errors;
			} else {

				return $decodedResponse;
			}
		} else {
			// Failed to fetch url;
			if (empty($this->defaultOptions['errmsg']))
//                $this->defaultOptions['errmsg'] = $requestResponse['body'];
				return 0;
		}
		throw new Exception($this->defaultOptions['errmsg']);
	}

	public function getRequestUrl() {
		return $this->RequestUri;
	}

	public function getTweetClass($tw) {

		if($tw->retweeted || $tw->favorited) {

			if($tw->retweeted && $tw->favorited) return 'refavorited';

			else if($tw->retweeted)                return 'retweeted';

			else                return 'favorited';

		}

		return 'rtf';

	}


}

?>

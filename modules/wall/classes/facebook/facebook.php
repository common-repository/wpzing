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

class zFacebook extends WallModule {

	public function  __construct($plugin) {
		$this->plugin=$plugin;
		if($plugin->get_option('publish_feeds_from_facebook') || $plugin->get_option('publish_from_facebook') || $plugin->get_option('publish_to_facebook')) {

			$this->facebookApiUrl='https://graph.facebook.com/';
			$this->zFacebook=$plugin->get_option('facebook');

			add_action('wp_ajax_facebookLike', array(&$this, 'facebookLike'));
			add_action('wp_ajax_nopriv_facebookLike', array(&$this, 'facebookLike'));
			add_action('wp_ajax_facebookComment', array(&$this, 'facebookComment'));
			add_action('wp_ajax_getFacebookFeed', array(&$this, 'getFacebookFeeds'));
			add_action('wp_ajax_nopriv_facebookComment', array(&$this, 'facebookComment'));
			wp_enqueue_script('facebook', plugins_url('js/facebook.js', ZING_PLUGIN_FILE),array('jquery'));
		}
	}


	public function getUserInfo($user_id) {
		$userInfo=  file_get_contents($this->facebookApiUrl.$user_id);
		$userInfo=  json_decode($userInfo);
		return $userInfo;
	}

	static function getUser() {
		$userInfo=  file_get_contents('https://graph.facebook.com/'.$this->zFacebook->id);
		$userInfo=  json_decode($userInfo);
		return $userInfo;
	}

	public function postStatusMessage($message,$id) {
		if ($this->plugin->get_option('publish_to_facebook') == 1) {
			//Call api to publish status update on Facebook
			$response = wp_remote_post('https://graph.facebook.com/'.$this->zFacebook->id.'/feed' , array('sslverify'=>false,'message'=>$message,'body'=>array('access_token' => $this->zFacebook->access_token,'message'=>$message)));

			//If there is no error
			if(!is_wp_error($response) && $response['response']['code']>=200 && $response['response']['code']<=300) {
				global  $wpdb;
				$fbpost=json_decode($response['body']);
				$wpdb->insert($wpdb->posts,array('post_title'=>$message,'post_type'=>'facebook','post_date'=>date('Y-m-d H:i:s')));
				$id=$wpdb->insert_id;
				$fbpost_id=  substr($fbpost->id,  strpos($fbpost->id, '_')+1,  strlen($fbpost->id));
				add_post_meta($id, 'facebook_post_id', $fbpost_id,true);
				add_post_meta($id, 'zs_post', '1',true);
				$this->log('debug', 'Status update inserted into posts table');
				return true;
			} else {
				$this->log('warning', 'Unable to publish wall post on Facebook');
			}
		}
		return false;
	}

	public function postComment($comment_id,$message) {
		$response=wp_remote_post('https://graph.facebook.com/'.$comment_id.'/comments', array(
						'body'=>array('access_token' => $this->zFacebook->access_token,
										'message' => $message)
		));
		if(!is_wp_error($response) && $response['response']['code']==200)
			return true;
		else
			return false;
	}



	public function getNewsFeed() {
		$newsFeed= wp_remote_get($this->facebookApiUrl.$this->zFacebook->id .'/home?access_token='.$this->zFacebook->access_token,array('sslverify'=>false));
		if(!is_wp_error($newsFeed))
			return json_decode($newsFeed);
		else return false;
	}

	/*
   * Get Facebook News Feed
	*/
	public function getFacebook() {
		$html=null;
    
		// If current user is admin and publishing news feeds from Facebook is turned on
		if( ! $this->canUserManageWall()) return $html.'</div>';

		if(!$this->plugin->get_option('publish_feeds_from_facebook') && !$this->getFbConnected())
			return $html.='<iframe src="'.ZING_WWW_BASE.'/users/social_networks?token='.$this->plugin->get_option('blog_api_token').'" width="100%" height="300px" border="0"></iframe><a href="http://support.zingsphere.com/zingsphere/topics/why_should_i_connect_zwall_with_my_facebook-2asa9"  target="_blank" title="Click for more information" class="m5L">Why should I connect my zWall with my Facebook account?</a></div>';
		//return $html.='<script type="text/javascript" src="'.ZING_WWW_BASE.'/users/check_signin"></script><iframe src="'.ZING_WWW_BASE.'/users/social_networks?token='.$this->plugin->get_option('blog_api_token').'" width="100%" height="300px" border="0"></iframe><a href="http://support.zingsphere.com/zingsphere/topics/why_should_i_connect_zwall_with_my_facebook-2asa9"  target="_blank" title="Click for more information" class="m5L">Why should I connect my zWall with my Facebook account?</a></div>';
		else
		if(!$this->plugin->get_option('publish_feeds_from_facebook'))
			return $html.='</div>';

		//Call Zingsphere api to get Facebook news feeds for current user

		$response= wp_remote_get('https://graph.facebook.com/' .$this->zFacebook->id.'/home?access_token='.$this->zFacebook->access_token,array('sslverify'=>false));
    
		if(is_wp_error($response)) return $html.'</div>';
		$response=json_decode($response['body']);
		if( ! isset($response->data)) return $html.'</div>';
		// Loop
		foreach($response->data as $fbpost) {

			// Depends on Facebook post type generate HTML
			switch($fbpost->type) {
				case 'status' :
				//If post type is status
					$html .= '<div class="zWallFbPost">
                                <img src="https://graph.facebook.com/' . $fbpost->from->id . '/picture" class="zWallFbPostAvatar" /> 
                                <div class="zWallFbPostContent">
                                <div class="zWallFbPostTitle">';
					if(!$fbpost->story) {
						$html.='<a href="https://facebook.com/"' . $fbpost->from->id . '" target="_blank">' . $fbpost->from->name . '</a>';
						if($fbpost->to) {
							$html.=' -> ' . '<a href="https://facebook.com/"' . $fbpost->to->data[0]->id.  '" target="_blank">' . $fbpost->to->data[0]->name . '</a>';
						}
						$html.=' </div><div class="zWallFbPostStatus">'. $this->parseFbStory($fbpost) . '</div>';
					}
					else {
						$html.=$this->parseFbStory($fbpost).'</div>';
					}

					$html.= $this->fbImage($fbpost) . $this->addFbActions($fbpost);
					'</div>';

					$html.= '</div></div></div><div class="zWallClear"></div>';
					break;
				case 'link'   :
				//If post type is link
					$html .= '
                            <div class="zWallFbPost">
                                <img src="https://graph.facebook.com/' . $fbpost->from->id . '/picture" class="zWallFbPostAvatar" />
                                <div class="zWallFbPostContent">
                                    <div class="zWallFbPostTitle">';
					if(!$fbpost->story) {
						$html.='<a href="https://facebook.com/"' . $fbpost->from->id . '" target="_blank">' . $fbpost->from->name . '</a>';
						if($fbpost->to) {
							$html.=' -> ' . '<a href="https://facebook.com/"' . $fbpost->to->data[0]->id.  '" target="_blank">' . $fbpost->to->data[0]->name . '</a>';
						}
						$html.=' </div> <div class="zWallFbPostStatus">'. $this->parseFbStory($fbpost) . '</div>';
					}else {
						$html.=$this->parseFbStory($fbpost).'</div>';
					}
					$html.='<div class="zWallFbPostLink">' . $this->fbImage($fbpost) . '</div>';
					$html.=$this->addFbActions($fbpost);
					//$html.='<span class="time">'. date('F j',  strtotime($fbpost->created_time)) .' at '. date('g:i a',  strtotime($fbpost->created_time)) . '</span>';

					$html.='</div></div></div>
                                        <div class="zWallClear"></div>';
					break;
				case 'photo'  :
				//If post type is photo
					$html .= '
                            <div class="zWallFbPost">
                                <img src="https://graph.facebook.com/' . $fbpost->from->id . '/picture" class="zWallFbPostAvatar" />
                                <div class="zWallFbPostContent">
                                    <div class="zWallFbPostTitle">';
					if(!$fbpost->story) {
						$html.='<a href="https://facebook.com/"' . $fbpost->from->id . '" target="_blank">' . $fbpost->from->name . '</a>';
						if($fbpost->to) {
							$html.=' -> ' . '<a href="https://facebook.com/"' . $fbpost->to->data[0]->id.  '" target="_blank">' . $fbpost->to->data[0]->name . '</a>';
						}
						$html.=' </div> <div class="zWallFbPostStatus">'. $this->parseFbStory($fbpost) . '</div>';
					}else {
						$html.=$this->parseFbStory($fbpost).'</div>';
					}
					$html.='
                                    <div class="zWallFbPostStatus">' . $fbpost->message . '</div>
                                    <div class="zWallFbPostLink">' . $this->fbImage($fbpost) . '</div>';
					$html.=$this->addFbActions($fbpost);
					$html.='</div></div></div>
                                <div class="zWallClear"></div>';
					break;
				case 'video'  :
				//If post type is video
					$html .= '
                            <div class="zWallFbPost">
                                <img src="https://graph.facebook.com/' . $fbpost->from->id . '/picture" class="zWallFbPostAvatar" />
                                <div class="zWallFbPostContent">
                                    <div class="zWallFbPostTitle">
                                        <a href="https://facebook.com/' . $fbpost->from->id . '" target="_blank">' . $fbpost->from->name . '</a>';
					if($fbpost->to) $html.=' -> ' . '<a href="https://facebook.com/"' . $fbpost->to->data[0]->id.  '" target="_blank">' . $fbpost->to->data[0]->name . '</a>';
					$html.= '
                                    <div class="zWallFbPostStatus">' . $fbpost->message . '</div>
                                    <div class="zWallFbPostLink">' . $this->fbVideo($fbpost) . '</div>';
					$html.=$this->addFbActions($fbpost).'
                                        </div>
                                
                            </div></div></div><div class="zWallClear"></div>';
					break;
			}
		}
		//Close the Facebook div
		//Return generated HTML
		return $html;
	}


	/**
	 * Get Facebook statuses that are not yet in database
	 */
	public function getFbPosts() {
		global $wpdb;
		$new=array();

		if($this->plugin->get_option('publish_from_facebook')) {
  if(!$this->plugin->get_option('facebook_pinged') || strtotime($this->plugin->get_option('facebook_pinged')) <  strtotime('-2 minutes')){
			//Callapi to get Facebook statuses that are not older then reference date
			$response= wp_remote_get($this->facebookApiUrl.$this->zFacebook->id .'/statuses?access_token='.$this->zFacebook->access_token,array('sslverify'=>false));
			if(!is_wp_error($response)) {
				$response=  json_decode($response['body']);

				//If api returned some results
				if(!empty($response->data)) {
					foreach ($response->data as $status) {

						$post=query_posts( array(
										'post_type' => 'facebook',
										'meta_query' => array(array(
																		'key'=>'facebook_post_id',
																		'value'=> $status->id,
																		'posts_per_page' => 1
														))
						));

						if(empty($post)) {
							if(!$wpdb->insert($wpdb->posts,array('post_title'=>$status->message,'post_type'=>'facebook','post_date'=>date('Y-m-d H:i:s',strtotime($status->updated_time))))) {
								$this->log('error','Error while inserting facebook status in db');
							} else {
								$id=$wpdb->insert_id;
								$new[]=  get_post($id);
								add_post_meta($id, 'facebook_post_id', $status->id);
								if(isset($status->comments) && !empty($status->comments)) {
									foreach ($status->comments->data as $comment) {
										$wpdb->insert($wpdb->comments,array('comment_post_ID'=>$id, 'comment_author'=>$comment->from->name,'comment_author_url'=>$comment->id,'comment_content'=>$comment->message,'comment_type'=>'facebook', 'comment_date'=>date('Y-m-d H:i:s',strtotime($comment->created_time))));
										$cid=$wpdb->insert_id;
                    add_comment_meta($cid,'fb_comment_id',$comment->id,true);
										add_comment_meta($cid,'comment_author_id',$comment->from->id,true);
									}
								}
							}
						} else {
							$id=$post[0]->ID;
							if(get_post_meta($id,'zs_post',true)!='1') {
								if(isset($status->comments) && !empty($status->comments)) {
									foreach ($status->comments->data as $comment) {
										$com=$wpdb->get_row('SELECT * FROM '.$wpdb->comments.' WHERE comment_type="facebook" AND comment_author_url="'.$comment->id.'"');
										if(empty($com)) {
											$wpdb->insert($wpdb->comments,array('comment_post_id'=>$id, 'comment_author'=>$comment->from->name,'comment_author_url'=>$comment->id,'comment_content'=>$comment->message,'comment_type'=>'facebook', 'comment_date'=>date('Y-m-d H:i:s',strtotime($comment->created_time))));
											$cid=$wpdb->insert_id;
                        add_comment_meta($cid,'fb_comment_id',$comment->id,true);
                        add_comment_meta($cid,'comment_author_id',$comment->from->id,true);
										}
									}
								}
							}
						}
					}
				}
			}
      //Set option ping
		$this->plugin->set_option('facebook_pinged',date('Y-m-d H:i:s'),true);
    }
	}
		
		//Return new posts
		return $new;

	}

	/**
	 * Format FB image
	 */
	protected function fbImage($post) {
		if (isset($post->picture)) {
			if($post->status_type!='approved_friend')
				return
								'<div class="content"><a href="'.$post->link.'"  target="_blank" class="fbVideo"><img src="'.$post->picture.'"/></a>
                            <div class="video_description">
                            <a href="'.$post->link.'" target="_blank" class="fbLinkTitle">'.$post->name.'</a>
                            <span><a href="'.$post->link.'" target="_blank" >'.$post->screen_name.'</a></span>
                            <p class="description" >'.$this->formatCaption($post).'</p>
                            <p class="description">'.$this->formatDescription($post).'</p>
                            </div></div>';
			else {
				$html='';
				if($post->story_tags)  foreach ($post->story_tags as $tag) {
						foreach ($tag as $user) {
							if($user->id!=$post->from->id)
								$html.='<a href="https://www.facebook.com/'.$user->id.'" title="'.$user->name.'" target="_blank"><img src="https://graph.facebook.com/'.$user->id.'/picture"/></a>';
						}
					}
				return $html;
			}
		} else
			return;

	}


	/**
	 * Format video link obtained from FB
	 */
	protected function fbVideo($post) {
		$p=   strpos($post->source,'autoplay');
		$post->source = substr($post->source, 0, $p);
		return '<div class="video_content">
                            <iframe width="180" height="120" src="'.$post->source.'autoplay=0" frameborder="0" allowfullscreen="1" class="fbVideo" ></iframe>
                            <div class="video_description">
                            <a href="'.$post->link.'" target="_blank" class="fbLinkTitle">'.$post->name.'</a> 
                            <p class="description">'.$this->formatDescription($post).'</p>
                            </div>
                            </div>';
	}

	/**
	 * Add FB actions
	 */
	public function addFbActions($fbpost) {
		$actions=array();
		if($fbpost->actions) {
			foreach ($fbpost->actions as $action) {
				switch ($action->name) {
					case 'Like'  :    if($fbpost->likes->count) {
							$unlike=false;
							foreach ($fbpost->likes->data as $like) {
								if ($like->id==$this->plugin->get_option('facebook')->id) $unlike=true;
							}
						}
						if (!$unlike)
							$html.='<span class="zWall-facebookAction" onClick="Facebook.facebookLike(\''.$fbpost->id.'\',\'' .  wp_create_nonce('wpzing-security-nonce') . '\',\'like\')" id="'.$fbpost->id.'_like">Like</span>';
						else
							$html.='<span class="zWall-facebookAction" onClick="Facebook.facebookLike(\''.$fbpost->id.'\',\'' .  wp_create_nonce('wpzing-security-nonce') . '\',\'unlike\')" id="'.$fbpost->id.'_like">Unlike</span>';
						break;
					default       :  $html.='<span class="zWall-facebookAction" onClick="Facebook.facebook'.$action->name.'(\''.$fbpost->id.'\',\'' .  wp_create_nonce('wpzing-security-nonce') . '\')">'.$action->name.'</span>';
				}
				$actions[]=$action->name;
			}
		}
		$html.='<span class="time">'. date('F j',  strtotime($fbpost->created_time)) .' at '. date('g:i a',  strtotime($fbpost->created_time)) . '</span>
               <div class="fbActions">';

		if($fbpost->likes && $fbpost->likes->count > 0 ) {
			$html.='<div class="likes">';
			if (!$unlike) {
				switch($fbpost->likes->count) {
					case 1 :   $html.= '<a href="https://www.facebook.com/'.$fbpost->likes->data[0]->id .'" target="_blank">'.$fbpost->likes->data[0]->name. '</a> like this';
						break;
					case 2 :   $html.= '<a href="https://www.facebook.com/'.$fbpost->likes->data[0]->id .'" target="_blank">'.$fbpost->likes->data[0]->name. '</a> and <a href="https://www.facebook.com/'.$fbpost->likes->data[1]->id .'" target="_blank">'.$fbpost->likes->data[1]->name. '</a> likes this';
						break;
					case 3 :   $html.= '<a href="https://www.facebook.com/'.$fbpost->likes->data[0]->id .'" target="_blank">'.$fbpost->likes->data[0]->name. '</a> , <a href="https://www.facebook.com/'.$fbpost->likes->data[1]->id .'" target="_blank">'.$fbpost->likes->data[1]->name. '</a> and <a href="https://www.facebook.com/'.$fbpost->likes->data[2]->id .'" target="_blank">'.$fbpost->likes->data[2]->name. '</a> likes this';
						break;
					default :  $html.= '<a href="https://www.facebook.com/'.$fbpost->likes->data[0]->id .'" target="_blank">'.$fbpost->likes->data[0]->name. '</a> , <a href="https://www.facebook.com/'.$fbpost->likes->data[1]->id .'" target="_blank">'.$fbpost->likes->data[1]->name. '</a> and <a href="https://www.facebook.com/'.$fbpost->id.'" target="_blank">'. (int)($fbpost->likes->count-2) .' others</a> likes this';
				}
			}
			else if($fbpost->likes->count==1) $html.= 'You like this.';
			else $html.= 'You and <a href="https://www.facebook.com/'.$fbpost->id.'" target="_blank">'.(int)($fbpost->likes->count-1). ' others</a> likes this';
//           $html.='<div class="likes">'.$fbpost->likes->count.' people like this</div>';
			$html.='</div>';
		}
		$html.='<div class="zWallFb_comments" id="zWallFb_comments'.$fbpost->id.'">';
		if($fbpost->comments && $fbpost->comments->count > 0 ) {
			if($fbpost->comments->count > count($fbpost->comments->data))
				$html.='<div class="likes"><a href="https://www.facebook.com/'.$fbpost->id.'" target="_blank">View all '.$fbpost->comments->count.' comments</a></div>';
			foreach ($fbpost->comments->data as $comment) {
				$html.='<div class="comment">
                            <a href="https://facebook.com/'.$comment->from->id.'"><img src="https://graph.facebook.com/' . $comment->from->id.'/picture" class="comment_avatar"/></a>
                            <div class="comment_data">
                            <a href="https://facebook.com/'.$comment->from->id.'">'.$comment->from->name.'</a> '.$comment->message.'<br/>';
				$html.='<span class="time">'. date('F j',  strtotime($comment->created_time)) .' at '. date('g:i a',  strtotime($comment->created_time)) . '</span>
                            </div></div>';
			}
		}
		$html.='</div>';
		if (in_array('Comment', $actions)) {
			$user=$this->plugin->get_option('facebook');
			$html.='<div class="comment">
                       <img src="https://graph.facebook.com/' . $user->id . '/picture" class="comment_avatar" />
                       <input type="text" placeholder="Write a comment..." id="'.$fbpost->id.'_comment" onKeyPress="return event.keyCode!=13,Facebook.facebookCommentKey(event,\''.$fbpost->id.'\')" class="fb_comment"/>
                       </div>';
		}
//           $html.='</div>';
		return $html;
	}
	/**
	 * Format FB image description
	 */
	protected function formatDescription($post) {
		if (strlen($post->description)>512) {
			return substr($post->description, 0,512).'<a href="'.$post->link.'" target="_blank">...see more</a>';
		} else
			return $post->description;
	}

	/**
	 * Format FB image caption
	 */
	protected function formatCaption($post) {
		if (strlen($post->caption)>512) {
			return substr($post->caption, 0,512).'<a href="'.$post->link.'" target="_blank">...see more</a>';
		} else
			return $post->caption;
	}
	/**
	 * Parse story obtained from FB
	 */
	protected function parseFbStory($post) {
		if (isset($post->story)) {
			$story=  $post->story;
			if($post->story_tags)
				foreach ($post->story_tags as $tag) {
					foreach ($tag as $user) {
						$story =  str_replace($user->name,'<a href="https://www.facebook.com/'.$user->id.'" target="_blank">'.$user->name.'</a>', $story);
						if($user->name!=$post->from->name) $add='<a href="https://www.facebook.com/'.$user->id.'" target="_blank"><img src="https://graph.facebook.com/'.$user->id.'/picture" class="" /></a>';
					}
				}
			return $story;
		} else if(isset ($post->message)) {
			$message=  $post->message;
			if($post->message_tags)
				foreach ($post->message_tags as $tag) {
					$message=  str_replace($tag->name,'<a href="https://www.facebook.com/'.$tag->id.'" target="_blank">'.$tag->name.'</a>', $message);
				}
			return $message;
		}
	}
	/*
  * Like/Unlike
	*/
	public function facebookLike() {
		//Check ajax referer
		$this->plugin->check_ajax('wall');
		if($_POST['like']=='like') {
			$response=wp_remote_post('https://graph.facebook.com/'.$_POST['id'].'/likes',
							array('sslverify'=>false,'body'=>array('access_token' => $this->plugin->get_option('facebook')->access_token)));
			if(!is_wp_error($response) &&$response['response']['code']==200) {
				echo 'Liked';
				die;
			}
		} else {
			$response=  wp_remote_request('https://graph.facebook.com/'.$_POST['id'].'/likes',
							array( 'method'=>'DELETE','sslverify'=>false,
							'body'=>array('access_token' => $this->plugin->get_option('facebook')->access_token) ));
			if(!is_wp_error($response) &&$response['response']['code']==200) {
				echo 'Unliked';
				die;
			}
		}
		echo -1;
		die;
	}
	
	/*
  * Comment
	*/
	public function facebookComment() {
		//Check ajax referer
		$this->plugin->check_ajax('wall');
		$response=wp_remote_post('https://graph.facebook.com/'.$_POST['comment_id'].'/comments',
						array('sslverify'=>false,'body'=>array('access_token' => $this->plugin->get_option('facebook')->access_token,
										'message' => $_POST['message'])));
		if (!is_wp_error($response) && $response['response']['code']==200) {
			$user=$this->plugin->get_option('facebook');
			echo   '<div class="comment">
                            <a href="https://facebook.com/'.$user->id.'"><img src="https://graph.facebook.com/'.$user->id.'/picture" class="comment_avatar"/></a>
                            <div class="comment_data">
                            <a href="https://facebook.com/'.$user->id.'">'.$user->name.'</a> '.$_POST['message'].'<br/>
                            <span class="time">'. date('F j') . ' at '. date('g:i a') . '</span>
                            </div>
                            </div>';
			die;
		} else
			echo -1;
		die;
	}

}

?>
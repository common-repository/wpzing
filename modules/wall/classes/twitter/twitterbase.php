<?php

/*
 * Class changes, updates and new features Pedja Delic
 * pedja@zingsphere.com
 *
 *
 *
 * Copyright (c) <2008> Justin Poliey <jdp34@njit.edu>
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * Twitterlibphp is a PHP implementation of the Twitter API, allowing you
 * to take advantage of it from within your PHP applications.
 *
 * @author Justin Poliey <jdp34@njit.edu>
 * @package twitterlibphp
 */

/**
 * Twitter API abstract class
 * @package twitterlibphp
 *
 * https://dev.twitter.com/docs/api/1
 */

if(!class_exists('OAuthException'))
require_once(ZING_ROOT.'/modules/wall/classes/OAuth.php');

abstract class TwitterBase {
    
    

    /**
     * the last HTTP status code returned
     * @access private
     * @var integer
     */
    private $http_status;

    /**
     * the whole URL of the last API call
     * @access private
     * @var string
     */
    private $last_api_call;

    /**
     * the application calling the API
     * @access private
     * @var string
     */
    public $useragent = 'Zingsphere Bot v1.0';
    public $http_info;
    public $twitter_api_url='https://api.twitter.com/1.1/';
    public $token;
    public $consumer;
    public $sha1_mnethod;

    /*
     * https://dev.twitter.com/docs/api/1/get/statuses/retweeted_by_me
     * Returns the 20 most recent retweets posted by the authenticating user.
     * Rate Limitied/Requires Authentication
     */

    protected function getRetweetedByMe($options = array(), $format = '',$twitter) {
        $this->twitter_api_url='https://api.twitter.com/1/';
        return $this->apiCall('statuses/retweeted_by_me', 'GET', $format, $options, true,$twitter);
    }

    /*
     * https://dev.twitter.com/docs/api/1/get/statuses/retweeted_to_me
     * Returns the 20 most recent retweets posted by users the authenticating user follow.
     * A maximum of 200 tweets will be available on this timeline.
     * Rate Limitied/Requires Authentication
     */

    protected function getRetweetedToMe($options = array(), $format = '') {
        return $this->apiCall('statuses/retweeted_to_me', 'GET', $format, $options, true);
    }

    /*
     * https://dev.twitter.com/docs/api/1/get/statuses/retweeted_to_user
     * Returns the 20 most recent retweets posted by users the specified user follows.
     * The user is specified using the user_id or screen_name parameters.
     * This method is identical to statuses/retweeted_to_me except you can choose the user to view.
     * Rate Limitied/Requires Authentication
     */

    protected function getRetweetedToUser($options = array(), $format = '') {
        return $this->apiCall('retweeted_to_user', 'GET', $format, $options, true);
    }

    /*
     * https://dev.twitter.com/docs/api/1/get/statuses/retweets_of_me
     * Returns the 20 most recent tweets of the authenticated user that have been retweeted by others.
     * Rate Limitied/Requires Authentication
     */

    protected function getRetweetedOfMe($options = array(), $format = '') {
        return $this->apiCall('statuses/retweeted_of_me', 'GET', $format, $options, true);
    }

    
    protected function getRetweets($id, $format = '',$twitter) {
        return $this->apiCall("statuses/retweets/{$id}", 'GET', $format, $options, true,$twitter);
    }
    
    
    /* https://dev.twitter.com/docs/api/1/get/statuses/home_timeline
     * Returns a collection of the most recent Tweets and retweets posted by the authenticating user
     * and the users they follow. The home timeline is central to how most users interact with the Twitter service.
     * Up to 800 Tweets are obtainable on the home timeline. It is more volatile for users that
     * follow many users or follow users who tweet frequently.
     * Rate Limitied/Requires Authentication
     */

    protected function getHomeTimeLine($options = array(), $format = '', $twitter) {
        return $this->apiCall('statuses/home_timeline', 'GET', $format, $options, true,$twitter);
    }

    /**
     * Returns the 20 most recent statuses from non-protected users who have set a custom user icon.
     * @param string $format Return format
     * @return string
     */
    protected function getPublicTimeline($options = array(), $format = '') {
        return $this->apiCall('statuses/public_timeline', 'GET', $format, $options, false);
    }

    /**
     * Returns the 20 most recent statuses posted by the authenticating user and that user's friends.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getFriendsTimeline($options = array(), $format = '') {
        return $this->apiCall('statuses/friends_timeline', 'GET', $format, $options);
    }

    /**
     * This method doesn't require authentication, shared 150 requests.
     * http://www.twitxr.com/api/reference/getUserTimeline
     * Returns the 20 most recent statuses posted from the authenticating user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getUserTimeline($options = array(), $format = '',$twitter) {
        return $this->apiCall('statuses/user_timeline', 'GET', $format, $options, true,$twitter);
    }

    /**
     * https://dev.twitter.com/docs/api/1/get/statuses/mentions
     * Returns the 20 most recent mentions (status containing @username) for the authenticating user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     * Rate Limitied/Requires Authentication
     */
    protected function getMentions($options = array(), $format = '') {
        return $this->apiCall("statuses/mentions", 'GET', $format, $options);
    }

    /**
     * Returns the 20 most recent @replies (status updates prefixed with @username) for the authenticating user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     * @deprecated
     */
    protected function getReplies($options = array(), $format = '') {
        return $this->apiCall('statuses/replies', 'GET', $format, $options);
    }

    /**
     * Returns a single status, specified by the $id parameter.
     * @param string|integer $id The numerical ID of the status to retrieve
     * @param string $format Return format
     * @return string
     */
    protected function getStatus($options = array(), $format = '') {
        return $this->apiCall("statuses/show/{$options['id']}", 'GET', $format, $options, false);
    }

    /**
     * Updates the authenticated user's status.
     * @param string $status Text of the status, no URL encoding necessary
     * @param string|integer $reply_to ID of the status to reply to. Optional
     * @param string $format Return format
     * @return string
     */
    protected function updateStatus($status, $reply_to = null, $format = '',$twitter) {
        $options = array('status' => $status);
        if ($reply_to) {
            $options['in_reply_to_status_id'] = $reply_to;
        }
        return $this->apiCall('statuses/update', 'POST', $format, $options,true,$twitter);
    }

    /**
     * Destroys the status specified by the required ID parameter. The authenticating user must be the author of the specified status.
     * @param integer|string $id ID of the status to destroy
     * @param string $format Return format
     * @return string
     */
    protected function destroyStatus($options = array(), $format = '',$twitter) {
        return $this->apiCall("statuses/destroy/{$options['id']}", 'POST', $format, $options,true,$twitter);
    }

    /**
     * Returns the authenticating user's friends, each with current status inline.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getFriends($options = array(), $format = 'xm') {
        return $this->apiCall('statuses/friends', 'GET', $format, $options, false);
    }

    /**
     * Returns the authenticating user's followers, each with current status inline.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getFollowers($options = array(), $format = '') {
        return $this->apiCall('statuses/followers', 'GET', $format, $options);
    }

    /**
     * Returns extended information of a given user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function showUser($options = array(), $format = '') {
        if (!array_key_exists('id', $options) && !array_key_exists('user_id', $options) && !array_key_exists('screen_name', $options)) {
            $options['id'] = substr($this->credentials, 0, strpos($this->credentials, ':'));
        }
        return $this->apiCall('users/show', 'GET', $format, $options, false);
    }

    /**
     * Returns a list of the 20 most recent direct messages sent to the authenticating user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getMessages($options = array(), $format = '') {
        return $this->apiCall('direct_messages', 'GET', $format, $options);
    }

    /**
     * Returns a list of the 20 most recent direct messages sent by the authenticating user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getSentMessages($options = array(), $format = '') {
        return $this->apiCall('direct_messages/sent', 'GET', $format, $options);
    }

    /**
     * Sends a new direct message to the specified user from the authenticating user.
     * @param string $user The ID or screen name of a recipient
     * @param string $text The message to send
     * @param string $format Return format
     * @return string
     */
    protected function newMessage($user, $text, $format = '') {
        $options = array(
            'user' => $user,
            'text' => $text
        );
        return $this->apiCall('direct_messages/new', 'POST', $format, $options, true);
    }

    /**
     * Destroys the direct message specified in the required $id parameter.
     * @param integer|string $id The ID of the direct message to destroy
     * @param string $format Return format
     * @return string
     */
    protected function destroyMessage($options, $format = '') {
        return $this->apiCall("direct_messages/destroy/{$options['id']}", 'POST', $format, $options);
    }

    /**
     * Befriends the user specified in the ID parameter as the authenticating user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function createFriendship($options = array(), $format = '') {
        if (!array_key_exists('follow', $options)) {
            $options['follow'] = 'true';
        }
        return $this->apiCall('friendships/create', 'POST', $format, $options);
    }

    /**
     * Discontinues friendship with the user specified in the ID parameter as the authenticating user.
     * @param integer|string $id The ID or screen name of the user to unfriend
     * @param string $format Return format
     * @return string
     */
    protected function destroyFriendship($options = array(), $format = '') {
        return $this->apiCall('friendships/destroy', 'POST', $format, $options);
    }

    /**
     * Tests if a friendship exists between two users.
     * @param integer|string $user_a The ID or screen name of the first user
     * @param integer|string $user_b The ID or screen name of the second user
     * @param string $format Return format
     * @return string
     */
    protected function friendshipExists($user_a, $user_b, $format = '') {
        $options = array(
            'user_a' => $user_a,
            'user_b' => $user_b
        );
        return $this->apiCall('friendships/exists', 'GET', $format, $options);
    }

    /**
     * Returns an array of numeric IDs for every user the specified user is followed by.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getFriendIDs($options = array(), $format = '') {
        return $this->apiCall('friends/ids', 'GET', $format, $options);
    }

    /**
     * Returns an array of numeric IDs for every user the specified user is following.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getFollowerIDs($options = array(), $format = '') {
        return $this->apiCall('followers/ids', 'GET', $format, $options);
    }

    /**
     * Returns an HTTP 200 OK response code and a representation of the requesting user if authentication was successful; returns a 401 status code and an error message if not.
     * @param string $format Return format
     * @return string
     */
    protected function verifyCredentials($options = array(), $format = '') {
        return $this->apiCall('account/verify_credentials', 'GET', $format, $options);
    }

    /**
     * Returns the remaining number of API requests available to the requesting user before the API limit is reached for the current hour.
     * @param boolean $authenticate Authenticate before calling method
     * @param string $format Return format
     * @return string
     */
    protected function rateLimitStatus($options, $format = '') {
        return $this->apiCall('account/rate_limit_status', 'GET', $format, $options, false);
    }

    /**
     * Ends the session of the authenticating user, returning a null cookie.
     * @param string $format Return format
     * @return string
     */
    protected function endSession($options = array(), $format = '') {
        return $this->apiCall('account/end_session', 'POST', $format, $options);
    }

    /**
     * Sets which device Twitter delivers updates to for the authenticating user.
     * @param string $device The delivery device used. Must be sms, im, or none
     * @return string
     */
    protected function updateDeliveryDevice($options = array(), $device = '', $format = '') {
        $options = array('device' => $device);
        return $this->apiCall('account/update_delivery_device', 'POST', $format, $options);
    }

    /**
     * Sets one or more hex values that control the color scheme of the authenticating user's profile page on twitter.com.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function updateProfileColors($options = array(), $format = '') {
        return $this->apiCall('account/update_profile_colors', 'POST', $format, $options);
    }

    /**
     * Sets values that users are able to set under the "Account" tab of their settings page.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function updateProfile($options = array(), $format = '') {
        return $this->apiCall('account/update_profile', 'POST', $format, $options);
    }

    /**
     * Returns the 20 most recent favorite statuses for the authenticating user or user specified by the ID parameter in the requested format.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getFavorites($options = array(), $format = '') {
        return $this->apiCall('favorites', 'GET', $format, $options);
    }

    /**
     * Favorites the status specified in the ID parameter as the authenticating user.
     * @param integer|string $id The ID of the status to favorite
     * @param string $format Return format
     * @return string
     */
    protected function createFavorite($id, $format = '',$twitter) {
        return $this->apiCall("favorites/create", 'POST', $format, array('id'=>$id),true,$twitter);
    }

    /**
     * Un-favorites the status specified in the ID parameter as the authenticating user.
     * @param integer|string $id The ID of the status to un-favorite
     * @param string $format Return format
     * @return string
     */
    protected function destroyFavorite($id, $format = '',$twitter) {
        return $this->apiCall("favorites/destroy", 'POST', $format, array('id'=>$id),true,$twitter);
    }

    
    protected function createRetweet($id, $format = '',$twitter){
        
        return $this->apiCall("statuses/retweet/{$id}", 'POST', $format, array(),true,$twitter);
        
    }
    
    protected function destroyRetweet(){
        
    }
    
    
    /**
     * Enables notifications for updates from the specified user to the authenticating user.
     * @param integer|string $id The ID or screen name of the user to follow
     * @param string $format Return format
     * @return string
     */
    protected function follow($id, $format = '') {
        $options = array('id' => $id);
        return $this->apiCall('notifications/follow', 'POST', $format, $options);
    }

    /**
     * Disables notifications for updates from the specified user to the authenticating user.
     * @param integer|string $id The ID or screen name of the user to leave
     * @param string $format Return format
     * @return string
     */
    protected function leave($id, $format = '') {
        $options = array('id' => $id);
        return $this->apiCall('notifications/leave', 'POST', $format, $options);
    }

    /**
     * Blocks the user specified in the ID parameter as the authenticating user.
     * @param integer|string $id The ID or screen name of the user to block
     * @param string $format Return format
     * @return string
     */
    protected function createBlock($id, $format = '') {
        $options = array('id' => $id);
        return $this->apiCall('blocks/create', 'POST', $format, $options);
    }

    /**
     * Unblocks the user specified in the ID parameter as the authenticating user.
     * @param integer|string $id The ID or screen name of the user to unblock
     * @param string $format Return format
     * @return string
     */
    protected function destroyBlock($id, $format = '') {
        $options = array('id' => $id);
        return $this->apiCall('blocks/destroy', 'POST', $format, $options);
    }

    /**
     * Returns if the authenticating user is blocking a target user.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function blockExists($options, $format = '') {
        return $this->apiCall('blocks/exists', 'GET', $format, $options);
    }

    /**
     * Returns an array of user objects that the authenticating user is blocking.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getBlocking($options, $format = '') {
        return $this->apiCall('blocks/blocking', 'GET', $format, $options);
    }

    /**
     * Returns an array of numeric user ids the authenticating user is blocking.
     * @param array $options Options to pass to the method
     * @param string $format Return format
     * @return string
     */
    protected function getBlockingIDs($format = '') {
        return $this->apiCall('blocks/blocking/ids', 'GET', $format, array());
    }

    /**
     * Returns the string "ok" in the requested format with a 200 OK HTTP status code.
     * @param string $format Return format
     * @return string
     */
    protected function test($format = '') {
        return $this->apiCall('help/test', 'GET', $format, array(), false);
    }

    /*
     * Return serach result
     *
     *
     */

    protected function apiSearch($options, $format = '', $searchTerm = '') {
        return $this->apiSearchCall('search', 'GET', $format, $options, $searchTerm, false);
    }

    protected function apiSearchCall($twitter_method, $http_method, $format, $options, $searchTerm, $require_credentials = false) {
        $api_url = sprintf(TWITTER_SEARCH_URL . '%s', urlencode($searchTerm));
        $api_url .= '?' . http_build_query($options);
        $this->request = $api_url;
        print_r($this->request);
        return $this->request;
    }

    /**
     * Executes an API call
     * @param string $twitter_method The Twitter method to call
     * @param string $http_method The HTTP method to use
     * @param string $format Return format
     * @param array $options Options to pass to the Twitter method
     * @param boolean $require_credentials Whether or not credentials are required
     * @return string
     */
    protected function apiCall($twitter_method, $http_method, $format, $options, $require_credentials = true, $twitter) {
        //$api_url = sprintf ('http:/twitter/twitter.php.com/%s.%s', $twitter_method, $format);
        //$api_url = sprintf ('http://api.twitter.com/1/%s.%s', $twitter_method, $format);
        
       $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
       $this->consumer = new OAuthConsumer($twitter->app_key, $twitter->app_secret);
       $this->token = new OAuthConsumer($twitter->oauth_token, $twitter->oauth_token_secret);
       
        $api_url = sprintf($this->twitter_api_url . '%s.%s', $twitter_method, $format);
        if (($http_method == 'GET') && (count($options) > 0)) {
            $api_url .= '?' . http_build_query($options);
        }

        $this->request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $http_method, $api_url, $options);

        $this->request->sign_request($this->sha1_method, $this->consumer, $this->token);

        return $this->request->to_url();
        
        switch ($http_method) {
            case 'GET':
                if ($options['transport'] != 'curl') {
                    echo "\n\n==DEBUG-METHOD-GET==\n";
                    print_r($this->request);
                    return $this->request->to_url();
                }
                echo "\n\n==DEBUG-CURL-METHOD-GET==\n";
                return $this->transport($this->request->to_url(), 'GET');
            default:
                if ($options['transport'] != 'curl') {
                    echo "\n\n==DEBUG-METHOD-DEFAULT==\n";
                    return $this->request->get_normalized_http_url();
                }
                //print_r ($this->request->get_normalized_http_url ());
                //return $this->transport($this->request->get_normalized_http_url(), $http_method, $this->request->to_postdata());
                echo "\n\n==DEBUG-CURL=METHOD-DEFAULT==\n";
                return $this->transport($this->request->get_normalized_http_url(), $http_method, $this->request->to_postdata());
        }
    }

    protected function transport($url, $method, $postfields = NULL) {
        $this->http_info = array();
        $curlHandle = curl_init();

        /* Curl settings */
        curl_setopt($curlHandle, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curlHandle, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($curlHandle, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case 'POST':
                curl_setopt($curlHandle, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
            case 'DELETE':
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($postfields)) {
                    $url = "{$url}?{$postfields}";
                }
        }

        curl_setopt($curlHandle, CURLOPT_URL, $url);
        echo "\n==DEBUG-URL-TRANSPORT==\n";
        print_r($url);
        echo "\n==END-DEBUG-URL-TRANSPORT==\n";
        $response = curl_exec($curlHandle);
        print_r($response);
        $this->http_code = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($curlHandle));
        $this->url = $url;
        curl_close($curlHandle);
        return $response;
    }

    /**
     * Get the header info to store.
     */
    protected function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }

    /**
     * Returns the last HTTP status code
     * @return integer
     */
    protected function lastStatusCode() {
        return $this->http_status;
    }

    /**
     * Returns the URL of the last API call
     * @return string
     */
    protected function lastApiCall() {
        return $this->last_api_call;
    }

}

?>

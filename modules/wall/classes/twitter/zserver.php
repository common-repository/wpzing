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

class ZSCache {

	public function __construct () {
		add_action ('init', array(&$this, 'init'), 10, 1);
	}

	/* function init
     * $params
     * refreshing cache depending on $key
	*/

	public function init () {
		if (isset ($_POST['__ZSCACHE__'])) {
			$update = get_transient ('__ZSCACHE__' . $_POST['key']);
			if ($update && $update[0] == $_POST['__ZSCACHE__']) {
				requestHandler ($update[1])
								->expires_in ($update[2])
								->updates_with ($update[3], (array) $update[4])
								->set_lock ($update[0])
								->fetch_and_cache ();
			}
			exit ();
		}
	}

}

new ZSCache();

class ZSRequestHander {

	private $key;
	private $lock;
	private $callback;
	private $params;
	private $expiration = 0;  // default
	private $force_background_updates = false;

	public function __construct ($key) {
		$this->key = substr ($key, 0, 37);
	}

	/*
     * don't be lazy provide info
	*/

	public function set ($data) {
		// We set the timeout as part of the transient data.
		// The actual transient has no TTL. This allows for soft expiration.
		$expiration = ( $this->expiration > 0 ) ? time () + $this->expiration : 0;
		set_transient ($this->key, array($expiration, $data));
		return $this;
	}

	/*
     * don't be lazy provide info
	*/

	public function get () {
		$data = get_transient ($this->key);
		if (false === $data) {
			// Hard expiration
			if ($this->force_background_updates) {
				// In this mode, we never do a just-in-time update
				// We return false, and schedule a fetch on shutdown
				$this->schedule_background_fetch ();
				return false;
			} else {
				return $this->fetch_and_cache ();
			}
		} else {
			// Soft expiration
			if ($data[0] !== 0 && $data[0] < time ())
				$this->schedule_background_fetch ();
			return $data[1];
		}
	}

	/*
     * don't be lazy provide info
	*/

	private function release_update_lock () {
		delete_transient ('__ZSCACHE__' . $this->key);
	}

	/*
     * don't be lazy provide info
	*/

	public function request_pull () {
		$server_url = home_url ('/?zscache-request');
		wp_remote_post ($server_url, array('body' => array('__ZSCACHE__' => $this->lock,
										'key' => $this->key),
						'timeout' => 0.01,
						'blocking' => false,
						'sslverify' => apply_filters ('https_local_ssl_verify', true)));
	}

	/*
     * don't be lazy provide info
	*/

	public function fetch_and_cache () {
		// If you don't supply a callback, we can't update it for you!
		if (empty ($this->callback))
			return false;
		if ($this->has_update_lock () && ! $this->owns_update_lock ())
			return; // Race... let the other process handle it
		try {
			$data = call_user_func_array ($this->callback, $this->params);
			$this->set ($data);
		} catch (Exception $e) {

		}
		$this->release_update_lock ();
		return $data;
	}

	/*
     * don't be lazy provide info
	*/

	private function schedule_background_fetch () {
		if ( ! $this->has_update_lock ()) {
			set_transient ('__ZSCACHE__' . $this->key, array($this->new_update_lock (),
							$this->key,
							$this->expiration,
							$this->callback,
							$this->params), 300);

			add_action ('requestpull', array($this, 'request_pull'));
		}
		return $this;
	}

	/*
     * don't be lazy provide info
	*/

	private function get_update_lock () {
		$lock = get_transient ('__ZSCACHE__' . $this->key);
		if ($lock)
			return $lock[0];
		else
			return false;
	}

	/*
     * don't be lazy provide info
	*/

	private function has_update_lock () {
		return (bool) $this->get_update_lock ();
	}

	/*
     * don't be lazy provide info
	*/

	private function owns_update_lock () {
		return $this->lock == $this->get_update_lock ();
	}

	public function expires_in ($seconds) {
		$this->expiration = (int) $seconds;
		return $this;
	}

	public function updates_with ($callback, $params = array()) {
		$this->callback = $callback;
		if (is_array ($params))
			$this->params = $params;
		return $this;
	}

	private function new_update_lock () {
		$this->lock = md5 (uniqid (microtime () . mt_rand (), true));
		return $this->lock;
	}

}

/*
 * don't be lazy provide info
*/

function requestHandler ($key) {
	$handler = new ZSRequestHander ($key);
	return $handler;
}

?>

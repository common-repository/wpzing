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
 * Zingsphere Module superclass
 */
class ZingsphereModule {

	var $name = null;
	var $dependecies = null;
	var $admin_tab = null;
	var $active = false;
	var $plugin = null;

	/**
	 * Method checks if module is active
	 * @return <boolean>
	 */
	public function is_active() {
		if($this->dependecies && is_array($this->dependecies)) {
			$this->active = true;
			foreach($this->dependecies AS $dependency)
				$this->active &= isset($this->plugin->modules[$dependency]) && $this->plugin->modules[$dependency]->is_active();
		}
		return $this->active;
	}

	/**
	 * Method initializes plugin module
	 */
	public function init(){
		
	}

	/**
	 * Method displays plugin module admin settings
	 */
	public function admin(){
		if(file_exists(ZING_MODULES . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . 'admin.php'))
			include(ZING_MODULES . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . 'admin.php');
	}

	/**
	 * Method called when plugin is connected
	 */
	public function zingsphere_connect() {

	}

	/**
	 * Method called on plugin disconnect
	 */
	public function zingsphere_disconnect() {

	}

	/**
	 * Method called on plugin activation
	 */
	public function zingsphere_activate() {

	}

	/**
	 * Method called on plugin deactivation
	 */
	public function zingsphere_deactivate() {

	}

	/**
	 * Method calling plugin log function
	 * @param <string> $severity
	 * @param <string> $message
	 */
	public function log($severity, $message, $data=array()){
		if($this->plugin)
			$this->plugin->log($severity, $message, $data, $this->name);
	}

}

?>
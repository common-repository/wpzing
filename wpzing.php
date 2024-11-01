<?php

/*

 Plugin Name: Zingsphere
 Version: 1.2.2
 Plugin URI: http://wordpress.org/extend/plugins/wpzing
 Description: Zingsphere’s plugin helps you to integrate Zingsphere’s services into your WordPress blog. It also helps you to manage your Zingsphere widgets on your blog. You will need to login into your Zingsphere account from your Wordpress blog in order to utilize the Zingsphere plugin. Click on Zingsphere’s plugin settings in order to login and begin the process.
 Author: Zingsphere Ltd.
 Author URI: http://www.zingsphere.com


 # WPzing: Integrate Zingsphere's services into your WordPress blog
 # Copyright (c) 2012 Zingsphere Ltd.
 #
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 #
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

define('ZING_PLUGIN_VERSION', '1.2.2');
define('ZING_PLUGIN_FILE', __FILE__);
define('ZING_ROOT', dirname(ZING_PLUGIN_FILE));
define('ZING_SLUG', str_replace(dirname(dirname(ZING_PLUGIN_FILE)).DIRECTORY_SEPARATOR, '', ZING_PLUGIN_FILE));

define('ZING_API_VERSION', '1');
define('ZING_UPDATE', true);

define('ZING_WWW_BASE', 'https://www.zingsphere.com');
define('ZING_API_BASE', 'https://api.zingsphere.com');
define('ZING_API_URL', ZING_API_BASE.'/v'.ZING_API_VERSION);
define('ZING_AFFILIATE_CODE', '');

define('ZING_INCLUDES', ZING_ROOT . DIRECTORY_SEPARATOR . 'includes');
define('ZING_MODULES', ZING_ROOT . DIRECTORY_SEPARATOR . 'modules');

require_once(ZING_INCLUDES . DIRECTORY_SEPARATOR . 'core.php');
require_once(ZING_INCLUDES . DIRECTORY_SEPARATOR . 'module.php');

register_activation_hook(ZING_PLUGIN_FILE, array('ZingspherePlugin', 'zingsphere_plugin_activate'));
register_deactivation_hook(ZING_PLUGIN_FILE, array('ZingspherePlugin', 'zingsphere_plugin_deactivate'));
add_action('plugins_loaded', array('ZingspherePlugin', 'zingsphere_create_object'));

?>
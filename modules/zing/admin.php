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

if(!$this->plugin->get_option('blog_api_token')) {
	if($this->plugin->error)
		echo '<p style="padding:5px;background-color:#FF0000;color:#FFFFFF;border:1px solid #FF0000;font-weight:bold">'.$this->plugin->error.'</p>';
	?>

<div>
	<h3>To use the plugin, you must first connect to your Zingsphere account.</h3>
	<script type="text/javascript">
		function zingsphere_login_social(){
			document.getElementById('zswaiting').style.display = 'none';
			document.getElementById('zssocial').style.display = 'block';
			document.getElementById('zslogin').style.display = 'none';
			return false;
		}
		function zingsphere_login_zing(){
			document.getElementById('zswaiting').style.display = 'none';
			document.getElementById('zssocial').style.display = 'none';
			document.getElementById('zslogin').style.display = 'block';
			return false;
		}
		function zslogin(){
			jQuery.post(ajaxurl, {action:"zingsphere_check"}, function(response) {
				if(response) document.write(response);
				else zingsphere_login_social();
			});
		}
	</script>
		<?php if($this->plugin->get_option('tos') != true): ?>
	<script type="text/javascript">
		function termsofservice(){
			if(jQuery('#tou').attr('checked') == 'checked'
				&& jQuery('#touafb').attr('checked') == 'checked'
				&& jQuery('#pp').attr('checked') == 'checked'){
				jQuery('#tos').removeAttr('disabled', 'disabled');
			} else {
				jQuery('#tos').attr('disabled', 'disabled');
			}
			return false;
		}
	</script>
	<h4>I have read and agreed with Zingsphere's</h4>
	<p><input type="checkbox" name="tou" id="tou" onchange="termsofservice()" /> <a href="<?php print ZING_WWW_BASE.'/terms-of-use' ?>" target="_blank">Terms of Use</a></p>
	<p><input type="checkbox" name="touafb" id="touafb" onchange="termsofservice()" /> <a href="<?php print ZING_WWW_BASE.'/terms-of-use/addendum-bloggers' ?>" target="_blank">Terms of Use Addendum for Bloggers</a></p>
	<p><input type="checkbox" name="pp" id="pp" onchange="termsofservice()" /> <a href="<?php print ZING_WWW_BASE.'/privacy-policy' ?>" target="_blank">Privacy Policy</a></p>
	<p><input type="submit" name="tos" id="tos" value="Submit" disabled="disabled" /></p>
		<?php else: ?>
			<?php if($this->user_exists): ?>
	<div>
		<h4>An email has been sent to: <b><?php print $this->user_exists ?></b></h4>
		<h4>You must <b>confirm</b> your <b>email address</b> before logging in. Enter the received code into the form below.</h4>
		<input type="text" name="confirm_code" />
		<input type="hidden" name="email" value="<?php print $this->user_exists ?>" /><br/>
		<input type="submit" name="confirm" value="Confirm" />
	</div>
			<?php else: ?>
				<?php if($this->login_check === true):?>
	<script type="text/javascript" src="<?php print ZING_WWW_BASE ?>/users/checkuser"></script>
	<script type="text/javascript">zslogin()</script>
	<div id="zswaiting"><img src="<?php print plugins_url('images/ajax-loader-icon.gif', ZING_PLUGIN_FILE) ?>" alt="" width="15" height="15" /> Please wait...</div>
				<?php endif; ?>
			<?php endif; ?>
	<div class="p30B" id="zssocial" style="display:<?php echo $this->login_check === 'social' ? 'block' : 'none'?>">
		<table cellspacing="0" cellpadding="0" width="100%" class="p30T p30B" id="start">
			<tr>
				<td width="433" class="p30L p30B"><a href="" onclick="return zingsphere_login_zing()"><img src="http://www.zingsphere.com/img/buttons/start/zingsphere.png" alt="zingsphere"></a></td>
				<td class="p30L p30B"><iframe src="<?php print ZING_WWW_BASE ?>/users/social/facebook/button?blog_url=<?php print urlencode(admin_url('admin.php?page=wpzing&tab=account')) ?>" frameBorder="0" scrolling="no" width="433" height="123"></iframe></td>
			</tr>
			<tr>
				<td class="p30T p30L"><iframe src="<?php print ZING_WWW_BASE ?>/users/social/twitter/button?blog_url=<?php print urlencode(admin_url('admin.php?page=wpzing&tab=account')) ?>" frameBorder="0" scrolling="no" width="433" height="123"></iframe></td>
				<td class="p30T p30L"><iframe src="<?php print ZING_WWW_BASE ?>/users/social/linkedin/button?blog_url=<?php print urlencode(admin_url('admin.php?page=wpzing&tab=account')) ?>" frameBorder="0" scrolling="no" width="433" height="123"></iframe></td>
			</tr>
		</table>
	</div>

	<div id="zslogin" style="display:<?php echo $this->login_check === 'zingsphere' ? 'block' : 'none'?>">
		<div style="float:left;width:40%;border-right:2px solid #AAAAAA">
			<h4>Do you need an account? If so, then sign up now!</h4>
			<table><tr>
					<td>Your email address:</td><td><input type="text" name="zinguser_signup" value="<?php print isset($_POST['zinguser_signup']) && $_POST['zinguser_signup'] ? $_POST['zinguser_signup'] : get_bloginfo('admin_email') ?>"/></td>
				</tr><tr>
					<td>Choose password:</td><td><input type="password" name="zingpass_signup"/></td>
				</tr><tr>
					<td>Re-enter password:</td><td><input type="password" name="zingpass_retype"/></td>
				</tr><tr>
					<td><p class="submit"><input type="submit" name="Signup" value="Signup..." /></p></td><td></td>
				</tr></table>
		</div>
		<div style="float:left;padding-left:5%">
			<h4>Do you have an account already? If so, then login now!</h4>
			<table><tr>
					<td>Username:</td><td><input type="text" name="zinguser" value="<?php print get_bloginfo('admin_email') ?>"/></td>
				</tr><tr>
					<td>Password:</td><td><input type="password" name="zingpass"/></td>
				</tr><tr>
					<td cellspan="2"><a href="<?php echo ZING_WWW_BASE.'/users/reset_password'?>" target="_blank">Forgot password?</a></td>
				</tr><tr>
					<td><p class="submit"><input type="submit" name="Login" value="Connect..." /></p></td><td></td>
				</tr></table>
		</div>
	</div>
		<?php endif; ?>
</div>
	<?php } else {
	echo '<br/><p style="font-size:14px">Connected to Zingsphere account:</p>';
	echo '<p style="font-size:18px;font-weight:bold">'.$this->plugin->get_option('blog_api_user').'</p>';

	if($this->language)
		echo '<p>Your blog is in '.$this->language.' language. Language and other settings can be changed <a href="'.ZING_WWW_BASE.'/user/blog/'.$this->plugin->get_option('blog_id').'/details" target="_blank">here</a>.</p>';
	else
		echo '<p><a href="'.ZING_WWW_BASE.'/user/blog/'.$this->plugin->get_option('blog_id').'/details" target="_blank">Edit your zingsphere blog details.</a></p>';
	echo '<iframe src="'.ZING_WWW_BASE.'/users/social_networks?token='.$this->plugin->get_option('blog_api_token').'" width="640" height="250" border="0"></iframe>';
	echo '<p><input type="submit" value="Disconnect" name="Disconnect"/></p>';
}
?>

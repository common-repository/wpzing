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

?>
<style type="text/css">
	.error{padding:5px;background-color:#FF0000;color:#FFFFFF;border:1px solid #FF0000;font-weight:bold}
	.message{padding:5px;background-color:#F2F5F2;color:#333333;border:1px solid #CED1CE;font-weight:bold}
</style>
<div id="zHelp">
	<?php if(!is_null($this->plugin->message)) echo $this->plugin->message; ?>
	<p class="message" style="display: none"></p>
	<hr/>
	<h3>Send activity log</h3>
	<hr/>
	<p> It will help us find out what went wrong and when.  Beam me up, Scotty! </p>
	<input type="button" value="Send" name="send_zLog" onClick="App.sendLog('<?php echo wp_create_nonce('wpzing-security-nonce'); ?>')"/><span id="send_loader" style="display:none;padding-left: 10px;"><img src="<?php print admin_url('images/loading.gif'); ?>" /></span>
	<hr/>
	<h3>Give us a word </h3>
	<hr/>
	<p>Praise us or curse us, either way, you will help us make things work even better.</p>
	<form name="zFeedback" method="post" action="">

    <p><input type="text" name="feedback_subject" placeholder="Subject..." ></p>
    <textarea name="feedback_text" placeholder="Describe your problem..." style="resize:none" rows="6" cols="60"></textarea>
    <p><input type="submit" name="send_feedback" value="Send"/></p>
	</form>
	<hr/>
	<h3>Help yourself</h3>
	<hr/>
	<p>If talking to us gives you too much of a dread, you can use our <a href="http://support.zingsphere.com" target="_blank">base of Frequently Asked Questions</a> and try solving your problem on your own.>

</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
    App.init('<?php echo admin_url('admin-ajax.php'); ?>','<?php echo wp_create_nonce('wpzing-security-nonce') ?>');
	});
</script> 

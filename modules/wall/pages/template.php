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
<!DOCTYPE html>
<html>
  <head>
    <title>zWall</title>
<?php $stylefiles=$this->getStyleFiles();   
   foreach ($stylefiles as $src){ 
 print '<style type="text/css">'."\n";  
 print '@import url('.$src.");\n";
 print '</style>';
 } 
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<?php $jsfiles=$this->getJsFiles();   foreach ($jsfiles as $src){ print '<script type="text/javascript" src="'.$src.'"></script>'."\n";  } ?>
<script type="text/javascript" src="<?php print ZING_WWW_BASE.'/users/checkuser'?>"></script>
<script type="text/javascript">var ajaxurl = '<?php print admin_url('admin-ajax.php')?>';var nonce = '<?php print $this->nonce?>'</script>
</head>
<body>
<div id="header">
  <div id="header_inner">
    <a href="<?php print ZING_WWW_BASE ?>" target="_blank"><img src="<?php print plugins_url('images/zWallLogo.png',ZING_PLUGIN_FILE) ?>"/></a>
  </div>
</div>
<div id="primary">
  <h1><?php print get_bloginfo('name'); ?></h1>
  <p class="description"><?php print get_bloginfo('description'); ?></p>
	<div id="content" role="main">
		<?php print $this->load_ajax_content('zwall', 'content') ?>
	</div><!-- #content -->
</div><!-- #primary -->
<div style="clear:both"></div>
<div id="footer">
  <center>
      <p class="zWallPowered">Powered by <i><a href="<?php print ZING_WWW_BASE ?>" target="_blank">Zingsphere</a></i></p>
      <a href="https://www.facebook.com/pages/Zingsphere-Ltd/112152758881502" target="_blank" title="Join us on Facebook"><img src="http://zingsphere.com/img/icons/social/facebook.png"></a>
      <a href="https://twitter.com/#!/zingsphere" target="_blank" class="m5L" title="Follow us on Twitter"><img src="http://zingsphere.com/img/icons/social/twitter.png"></a>
      <a href="https://www.linkedin.com/company/525292" class="m5L" title="Connect with us on LinkedIn" target="_blank"><img src="http://zingsphere.com/img/icons/social/linkedin.png"></a>
      <a href="http://www.youtube.com/user/Zingsphere" class="m5L" title="Join us on YouTube" target="_blank"><img src="http://zingsphere.com/img/icons/social/youtube.png"></a>
  </center>
</div>
<div id="copy">
  <center>
    Copyright © 2012, Zingsphere, Ltd. All Rights Reserved.
  </center>
</div>
</body>
</html>
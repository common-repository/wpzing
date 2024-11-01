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

<?php if( ! $this->plugin->modules['wall']) return; ?>
<?php if( ! $this->plugin->modules['wall']->is_active()) return; ?>

<!-- Style definitions -->
<style type="text/css">
    .nav-tab-wrapper span {cursor: pointer;}
    .page-title {padding: 5px; font-size: 13px; font-weight: normal; background: #eeeeee; border: 1px solid #c8c8c8;}
    .page-buttons {padding-top: 10px; border-top: 1px solid #c8c8c8;}
    .sortable, .nonsortable {position: relative; margin: 3px 0 0 0; padding: 5px 5px 5px 20px; background: #f5f5f5; border: 1px solid #e6e6e6;}
    .nonsortable {padding: 5px;}
    .sortable span {display: block; position: absolute; width: 9px; height: 12px; left: 5px; top: 6px; cursor: pointer;}
    .disabled {color: #c8c8c8;}
    .error{padding:5px;background-color:#FF0000;color:#FFFFFF;border:1px solid #FF0000;font-weight:bold}
    .message{padding:5px;background-color:#F2F5F2;color:#333333;border:1px solid #CED1CE;font-weight:bold}
    .zShareButton{cursor: move;font-weight: bold;background: url('<?php echo plugins_url('images/zingicon.png', ZING_PLUGIN_FILE) ?>') no-repeat 6px 6px #3991bf;padding:6px 12px;padding-left:28px;color:#FFF;text-decoration: none;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;}
    .zShareButton:hover{color:#FFF;border:1px solid #0b3a68}
    .newzFollower{background: #FB8181;border: 1px solid #F94B4B;color:#FFF;font-weight: bold;padding: 5px}
    .newzFollower span{float: right;font-weight: normal;text-decoration: underline;cursor: pointer}
    .m5L {margin-left: 5px;}
    .p5L {padding-left: 5px;}
    .p5R {padding-right: 5px;}
    .p15T {padding-top: 15px;}
</style>

<!-- Get options and check if wall is enabled -->
<?php $zingsphere_options = get_option('zingsphere_options'); $disabled = false; ?>
<?php if( ! $zingsphere_options['zWall_active']): $disabled = true; ?>
<div class="p15T">
    <p style="width: 60%;text-align: justify;font-size: 14px;"><b>zWall</b> is an experimental platform, that brings social networks to your blog, and not the other way around. 
    zWall can help you reach out to your friends and followers, making them active participants in spreading and discussing what you’ve just published. Whether your followers discuss your newest blog post on Facebook or Twitter, 
    with zWall you can follow and respond instantly to what they said about it – straight from your blog!</p>
    <input type="submit" name="activatezWall" value="Activate zWall" />
</div>
<?php return; endif; ?>

<!-- Can user manage wall? -->
<?php if( ! $this->plugin->modules['wall']->canUserManageWall()): ?>
<h3 class="page-title">You don't have enough privileges to manage zWall.</h3>
<?php return; endif; ?>

<!-- Display message if exists -->
<?php 
      if($this->plugin->message) echo '<p class="message">'.$this->plugin->message.'</p>'; 
      if($this->plugin->error)   echo '<p class="error">'.$this->plugin->error.'</p>';
      if(!empty($zingsphere_options['new_followers'])) {
        $followers=count($zingsphere_options['new_followers']);
        $ext=count($zingsphere_options['new_followers']) > 1 ? 's' : '';
      echo '<p class="newzFollower">You have ' . $followers . ' new follower' . $ext . ' <span class="zFollowerDismiss" >Dismiss</span></p>';
              }
      ?>

<!-- zWall tabs -->
<h3 id="zWall-tabs" class="nav-tab-wrapper">
    <span class="nav-tab<?php echo $disabled ? '' : ' nav-tab-active'; ?>"<?php if( ! $disabled): ?> onClick="App.adminTabs(this);"<?php endif; ?>>zWall</span>
    <span class="nav-tab"<?php if( ! $disabled): ?> onClick="App.adminTabs(this);"<?php endif; ?>>zFeeds</span>
    <span class="nav-tab"<?php if( ! $disabled): ?> onClick="App.adminTabs(this);"<?php endif; ?>>zSticker</span>
    <span class="nav-tab"<?php if( ! $disabled): ?> onClick="App.adminTabs(this);"<?php endif; ?>>zSettings</span>
</h3>

<?php $menus=get_terms('nav_menu');
    if (!$this->plugin->get_option('menu_choosen') && !empty($menus)){ $disabled=true;?>
    <div style="position: absolute;top: 150px;left: 0;height: 100%;width: 100%;background: #FFFFFF;opacity:0.6;filter:alpha(opacity=60);z-index: 100"></div>
    
    <div style="position: absolute;top: 200px;left: 15%;border: 2px solid #333333;padding: 10px;background: #FFFFFF;opacity:1;filter:alpha(opacity=100);z-index: 200;-moz-box-shadow:3px 3px 5px 6px #ccc;-webkit-box-shadow: 3px 3px 5px 6px #ccc;box-shadow:3px 3px 5px 6px #ccc;">
        <p>Please select in which menu you want to put zWall</p>
            <hr/>
        <?php   if(!empty($menus)) foreach ($menus as $menu) {?>
        <p><input type="radio" name="zWall_menu_select" value="<?php echo $menu->term_id ?>" checked/> <?php echo $menu->name ?></p>
        <?php } ?>
        <p><input type="radio" name="zWall_menu_select" value="none" /> None, I will decide later</p>
            <hr/>
           <input type="submit" name="zWallMenuSelect" value="Save"/>
    </div>
<?php } ?>
    
<?php include_once('pages/general.php'); ?>
    
<?php include_once('pages/newsfeed.php'); ?>
  
<?php include_once('pages/sticker.php'); ?>
    
<?php include_once('pages/settings.php'); ?>
    
<script type="text/javascript">
jQuery(document).ready(function() {
    App.subTabs("<?php echo $_GET['subtab'] ?>","<?php echo $_GET['subsubtab'] ?>");
    App.init('<?php echo admin_url('admin-ajax.php'); ?>','<?php echo wp_create_nonce('wpzing-security-nonce') ?>');
    jQuery('.zFollowerDismiss').click(function(){
      
        jQuery(this).parent().remove();
        data={action:'resetzFollowers'}
        jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function(response){})
          })
});
</script> 
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

class Embed extends WallModule {

	public function  __construct($plugin) {
		$this->plugin=$plugin;
		$this->nonce=  wp_create_nonce('wpzing-security-nonce');
	}

	public function embed() {
		$url=trim($_POST['url']);
		$data=$this->fetch($url);
		$html=$this->generate_embeded_content($data);
		echo json_encode(array('message'=>'ok','data'=>$html));
		die;
	}

	public function fetch($url) {

		require_once 'openGraph.php';

		if(is_wp_error(wp_remote_get($url,array('sslverify'=>false)))) {

			echo json_encode(array('message'=>'Invalid url'));
			die;
		}

		$ogtags=OpenGraph::fetch(trim($url));
		if($ogtags && $ogtags->url) {

			$data=$this->get_og_data($ogtags);
      
		}
		else {

			$data=$this->get_data($url);

		}

		return $data;




	}



	function get_og_data($ogtags) {

		if(!$ogtags->title || !$ogtags->image || !$ogtags->description)
			$data=$this->get_data($ogtags->url);

		if($ogtags->title) $data['title']=$ogtags->title;
		if($ogtags->image) $data['image'][]=$this->get_og_image($ogtags);
		if($ogtags->description) $data['description']=$ogtags->description;
		if($ogtags->type=='video') {

			$data['video']=$ogtags->video;

		}
		$data['url']=$ogtags->url;

		return $data ;

	}

	public function get_og_image($ogtags) {


		$img='<img src="'.$ogtags->image.'" class="zWallEmbedImage"  />';



		return $img;

	}
	public function get_data($url) {



		$response=wp_remote_get($url,array('sslverify'=>false));


		$data=array();

		if(in_array($response['headers']['content-type'],array('image/gif','image/jpeg','image/png','image/svg+xml','image/tiff','image/vnd.microsoft.icon'))) {

			$data['image'][]='<img src="'.$url.'" class="zWallEmbedImage" />';
			$data['title']=$data['description']=$url;
			$data['url']=$url;

			$image=$this->generate_embeded_content($data);

			echo json_encode(array('message'=>'ok','data'=>$image));
			die;

		}

		if(strpos($response['headers']['content-type'],'text/html')===false) {
			$data['title']=$url;
			$data['description']=$url;
			$data['url']=$url;

		}
		else {

//                  
			include_once ZING_ROOT.'/modules/wall/classes/rss_feeds/simple_html_dom.php';

			$html=file_get_html($url);

			$title=$html->find('title')->plaintext;
			$desc=$html->find('meta[description]')->plaintext;
			$title->plaintext;

			$data['title']=$title ? $title : $url;
			$data['description']=$desc ? $desc : $url;
			$data['url']=$url;

			$img=$html->find('img');

			if(!empty($img)) {
				foreach ($img as $i) {

					if($i->width>50 && $i->height>50) {
						if(strpos($i->src,'http')===false) {
							$base=parse_url($url);
							if(!strpos($base['host'],'http')) $base['host']='http://'.$base['host'];
							$i->src=$base['host'].$i->src;
						}
						$data['image'][]='<img src="'.$i->src.'" class="zWallEmbedImage" />';
					}
				}
			}

		}
		return $data;
	}



	function generate_embeded_content($data) {

		$html='<div class="zWallEmbed" id="zWallEmbed"><span class="zWallCloseEmbed" onClick="Embed.close()" title="Remove">x</span>';
		if(isset($data['image'])) {
      $style='';
			$html.='<div style="float:left"><a href="'.$data['url'].'" target="_blank"></a><ul class="zWallEmbedList">';
			foreach ($data['image'] as $img) {

				$html.='<li>'.$img.'</li>';

			}
			$html.='</ul>';
			if(count($data['image'])>1) $html.='<div class="zWallEmbedNav"><span class="zWallNavLeft" disabled="" onClick="Embed.navigate(this)">&nbsp;&nbsp;</span><span class="zWallNavRight" onClick="Embed.navigate(this)">&nbsp;&nbsp;</span></div>';
			$html.='</ul></div>';
		}
    else {
      $style='style="padding-left:4px"';
    }
		$html.='<p class="zWallEmbedTitle" '.$style.'><a href="'.$data['url'].'" target="_blank">'.$this->format($data['title'],64).'</a></p>';
		$html.='<p class="zWallEmbedDescription" '.$style.'>'.$this->format($data['description'],128).'</p>';
		$html.="<input type='hidden' id='zWallEmbededContentImage' value='".$data['image'][0]."'  name='embeded[image]'/>";
		$html.='
                            <input type="hidden" id="zWallEmbededContentTitle"       value="'.$this->format($data['title'],64).'" name="embeded[title]"/>
                            <input type="hidden" id="zWallEmbededContentDescription" value="'.$this->format($data['description'],128).'" name="embeded[description]"/>
                            <input type="hidden" id="zWallEmbededContentUrl"         value="'.$data['url'].'"  name="embeded[url]"/>';
		if(isset($data['video'])) {
			$html.='<input type="hidden" id="zWallEmbededVideo"  value="'.$data['video'].'"  name="embeded[video]"/>';
		}

		$html.='</div>';

		return $html;

	}

	function format($string,$length=128) {

		if(strlen($string)>$length) {

			$string=  substr($string,0,$length).'...';

		}

		return $string;


	}


	function format_meta_content($meta) {

    $style='';
		$html.='<div class="zWallEmbedPreview">
                        <div class="zWallEmbedPreviewContent">';

		if(isset($meta['video'])) {

			$html.='<div class="zWallEmbedImageLink"><a href="Javascript:void(0)" onClick="Embed.video(this,\''.$meta['video'].'\')" title="Play this video">'.$meta["image"];
			$html.='<img src="'.plugins_url('images/play.png', ZING_PLUGIN_FILE).'" class="zWallEmbedPlay" /></a></div>';
		}
		else {
      
			$html.='<div class="zWallEmbedImageLink"><a href="'.$meta["url"].'" target="_blank">'.$meta['image'].'</a></div>';
		}
    if(!$meta['image']) $style='style="padding-left:4px;"';
    
		$html.='<p class="zWallEmbedTitle" '.$style.'><a href="'.$meta["url"].'" target="_blank">'.$meta["title"].'</a> </p>
                        <p class="zWallEmbedDescription" '.$style.'>'.$meta['description'].'</p>
                        </div></div>';

		return $html;

	}

	public function zShare() {

		include_once ZING_ROOT . '/modules/wall/elements/zshare_form.php';
		die();

	}

}
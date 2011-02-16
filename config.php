<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php');
require_once('fgallery.php');

global $wpdb;

if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
    $gall_id = $_GET['gall_id'];
	$config = fgallery_get_album_settings($gall_id);
	$album = fgallery_get_album($gall_id);
	if (empty($album)) {
		$album['gall_width'] = 450;
		$album['gall_height'] = 385;
		$album['gall_bgcolor'] = "#ffffff";
		$album['gall_type'] = 3;
	}
	header("Content-type: text/xml");
	echo "<?xml version='1.0' encoding='UTF-8'?>";
			echo '<config>';
			switch ($album['gall_type']) {
			case 1:
					echo '<gallery>
							<source>'.$config['slide_source'].'</source>
						  </gallery>
						<slideshow>
							<enable>'.((bool)$config['sc_slideshow__enable']? 'true' : 'false').'</enable>
							<stopByClick>'.((bool)$config['sc_slideshow__stopByClick']? 'true':'false').'</stopByClick>
							<delay>'.$config['sc_slideshow__delay'].'</delay>
							<music>'.$config['sc_slideshow__music'].'</music>
							<musicPath>slide_melody.mp3</musicPath>
						</slideshow>
						<preview>
							<width>'.$config['sc_preview__width'].'</width>
							<height>'.$config['sc_preview__height'].'</height>
							<alpha>'.$config['sc_preview__alpha'].'</alpha>
							<cornerRadius>'.(int)$config['sc_preview__cornerRadius'].'</cornerRadius>
							<transitionEffect>'.$config['sc_preview__transitionEffect'].'</transitionEffect>
							<transitionDuration>'.$config['sc_preview__transitionDuration'].'</transitionDuration>
							<shadow>'.$config['sc_preview__shadow'].'</shadow>
							<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
							<distanceFromScroller>'.$config['sc_preview__distanceFromScroller'].'</distanceFromScroller>
							<shadowColor>0x'.str_replace('#','',$config['sc_preview__shadowColor']).'</shadowColor>
							<shadowAlpha>'.$config['sc_preview__shadowAlpha'].'</shadowAlpha>
							<shadowBlur>'.$config['sc_preview__shadowBlur'].'</shadowBlur>
							<shadowDistance>'.$config['sc_preview__shadowDistance'].'</shadowDistance>
							<borderWidth>'.$config['sc_preview__borderWidth'].'</borderWidth>
						</preview>
						<image>
							<scaleMode>'.$config['sc_image__scaleMode'].'</scaleMode>
							<cornerRadius>'.(int)$config['sc_image__cornerRadius'].'</cornerRadius>
							<transitionEffect>'.$config['sc_image__transitionEffect'].'</transitionEffect>
							<transitionDuration>'.$config['sc_image__transitionDuration'].'</transitionDuration>
						</image>
						<background>
							<type>'.$config['sc_background__type'].'</type>
							<src>'.$config['sc_background__src'].'</src>
							<alpha>'.$config['sc_background__alpha'].'</alpha>
							<color>0x'.str_replace('#','',$config['sc_background__color']).'</color>
						</background>
						<navigation>
							<enable>'.((bool)$config['sc_navigation__enable']? 'true' : 'false').'</enable>
							<align>'.$config['sc_navigation__align'].'</align>
							<visible>'.$config['sc_navigation__visible'].'</visible>
							<position>stage</position>
						</navigation>
						<caption>
							<align>'.$config['sc_caption__align'].'</align>
							<visible>'.$config['sc_caption__visible'].'</visible>
							<enable>'.((bool)$config['sc_caption__enable']? 'true':'false').'</enable>
							<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
							<textColor>0x'.str_replace('#','',$config['sc_caption__textColor']).'</textColor>
							<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
							<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
						</caption>
						<scroller>
							<scrollBy>'.$config['sc_scroller__scrollBy'].'</scrollBy>
							<speed>'.$config['sc_scroller__speed'].'</speed>
							<color>0x'.$config['sc_scroller__color'].'</color>
							<alpha>'.$config['sc_scroller__alpha'].'</alpha>
							<cornerRadius>'.(int)$config['sc_scroller__cornerRadius'].'</cornerRadius>
							<lineStyle>'.$config['sc_scroller__lineStyle'].'</lineStyle>
							<hideButtons>'.((bool)$config['sc_scroller__hideButtons']? 'true':'false').'</hideButtons>
							<itemDistance>'.$config['sc_scroller__itemDistance'].'</itemDistance>
							<borderWidth>'.$config['sc_scroller__borderWidth'].'</borderWidth>
							<borderColor>0x'.$config['sc_scroller__borderColor'].'</borderColor>
							<width>'.$config['sc_scroller__width'].'</width>
							<height>'.$config['sc_scroller__height'].'</height>
						</scroller>	
						<scrollerItem>
							<width>'.$config['sc_scrollerItem__width'].'</width>
							<height>'.$config['sc_scrollerItem__height'].'</height>
							<cornerRadius>'.(int)$config['sc_scrollerItem__cornerRadius'].'</cornerRadius>
							<alpha>'.$config['sc_scrollerItem__alpha'].'</alpha>
							<shadow>'.((bool)$config['sc_scrollerItem__shadow']? 'true':'false').'</shadow>
							<borderColor>0x'.str_replace('#','',$config['sc_scrollerItem__borderColor']).'</borderColor>
							<borderColorOnHover>0x'.str_replace('#','',$config['sc_scrollerItem__borderColorOnHover']).'</borderColorOnHover>
							<borderColorOnClick>0x'.str_replace('#','',$config['sc_scrollerItem__borderColorOnClick']).'</borderColorOnClick>
							<shadowColor>0x'.str_replace('#','',$config['sc_scrollerItem__shadowColor']).'</shadowColor>
							<shadowAlpha>'.$config['sc_scrollerItem__shadowAlpha'].'</shadowAlpha>
							<shadowBlur>'.$config['sc_scrollerItem__shadowBlur'].'</shadowBlur>
							<shadowDistance>'.$config['sc_scrollerItem__shadowDistance'].'</shadowDistance>
							<borderWidth>'.$config['sc_scrollerItem__borderWidth'].'</borderWidth>
							<gradientStart>0x'.str_replace('#','',$config['sc_scrollerItem__gradientStart']).'</gradientStart>
							<gradientEnd>0x'.str_replace('#','',$config['sc_scrollerItem__gradientEnd']).'</gradientEnd>
						</scrollerItem>';
				break;
				
				case 2: 
				echo '<gallery>
							<source>'.$config['slide_source'].'</source>
						  </gallery>
						<slideshow>
							<enable>'.(int)$config['sc_slideshow__enable'].'</enable>
							<stopByClick>'.(int)$config['sc_slideshow__stopByClick'].'</stopByClick>
							<delay>'.$config['sc_slideshow__delay'].'</delay>
							<music>'.$config['sc_slideshow__music'].'</music>
							<musicPath>slide_melody.mp3</musicPath>
						</slideshow>
						<preview>
							<width>'.$config['sc_preview__width'].'</width>
							<height>'.$config['sc_preview__height'].'</height>
							<alpha>'.$config['sc_preview__alpha'].'</alpha>
							<cornerRadius>'.(int)$config['sc_preview__cornerRadius'].'</cornerRadius>
							<transitionEffect>'.$config['sc_preview__transitionEffect'].'</transitionEffect>
							<transitionDuration>'.$config['sc_preview__transitionDuration'].'</transitionDuration>
							<shadow>'.$config['sc_preview__shadow'].'</shadow>
							<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
							<distanceFromScroller>'.$config['sc_preview__distanceFromScroller'].'</distanceFromScroller>
							<shadowColor>0x'.str_replace('#','',$config['sc_preview__shadowColor']).'</shadowColor>
							<shadowAlpha>'.$config['sc_preview__shadowAlpha'].'</shadowAlpha>
							<shadowBlur>'.$config['sc_preview__shadowBlur'].'</shadowBlur>
							<shadowDistance>'.$config['sc_preview__shadowDistance'].'</shadowDistance>
							<borderWidth>'.$config['sc_preview__borderWidth'].'</borderWidth>
						</preview>
						<image>
							<scaleMode>'.$config['sc_image__scaleMode'].'</scaleMode>
							<cornerRadius>'.(int)$config['sc_image__cornerRadius'].'</cornerRadius>
							<transitionEffect>'.$config['sc_image__transitionEffect'].'</transitionEffect>
							<transitionDuration>'.$config['sc_image__transitionDuration'].'</transitionDuration>
						</image>
						<background>
							<type>'.$config['sc_background__type'].'</type>
							<src>'.$config['sc_background__src'].'</src>
							<alpha>'.$config['sc_background__alpha'].'</alpha>
							<color>0x'.str_replace('#','',$config['sc_background__color']).'</color>
						</background>
						<navigation>
							<enable>'.(int)$config['sc_navigation__enable'].'</enable>
							<align>'.$config['sc_navigation__align'].'</align>
							<visible>'.$config['sc_navigation__visible'].'</visible>
							<position>stage</position>
						</navigation>
						<caption>
							<align>'.$config['sc_caption__align'].'</align>
							<visible>'.$config['sc_caption__visible'].'</visible>
							<enable>'.(int)$config['sc_caption__enable'].'</enable>
							<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
							<textColor>0x'.str_replace('#','',$config['sc_caption__textColor']).'</textColor>
							<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
							<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
						</caption>
						<scroller>
							<scrollBy>'.$config['sc_scroller__scrollBy'].'</scrollBy>
							<speed>'.$config['sc_scroller__speed'].'</speed>
							<color>0x'.str_replace('#','',$config['sc_scroller__color']).'</color>
							<alpha>'.$config['sc_scroller__alpha'].'</alpha>
							<cornerRadius>'.(int)$config['sc_scroller__cornerRadius'].'</cornerRadius>
							<lineStyle>'.$config['sc_scroller__lineStyle'].'</lineStyle>
							<hideButtons>'.(int)$config['sc_scroller__hideButtons'].'</hideButtons>
							<itemDistance>'.$config['sc_scroller__itemDistance'].'</itemDistance>
							<borderWidth>'.$config['sc_scroller__borderWidth'].'</borderWidth>
							<borderColor>0x'.str_replace('#','',$config['sc_scroller__borderColor']).'</borderColor>
							<width>'.$config['sc_scroller__width'].'</width>
							<height>'.$config['sc_scroller__height'].'</height>
							<align>'.$config['sc_scroller__align'].'</align>
						</scroller>	
						<scrollerItem>
							<width>'.$config['sc_scrollerItem__width'].'</width>
							<height>'.$config['sc_scrollerItem__height'].'</height>
							<cornerRadius>'.(int)$config['sc_scrollerItem__cornerRadius'].'</cornerRadius>
							<alpha>'.$config['sc_scrollerItem__alpha'].'</alpha>
							<shadow>'.$config['sc_scrollerItem__shadow'].'</shadow>
							<borderColor>0x'.str_replace('#','',$config['sc_scrollerItem__borderColor']).'</borderColor>
							<borderColorOnHover>0x'.str_replace('#','',$config['sc_scrollerItem__borderColorOnHover']).'</borderColorOnHover>
							<borderColorOnClick>0x'.str_replace('#','',$config['sc_scrollerItem__borderColorOnClick']).'</borderColorOnClick>
							<shadowColor>0x'.str_replace('#','',$config['sc_scrollerItem__shadowColor']).'</shadowColor>
							<shadowAlpha>'.$config['sc_scrollerItem__shadowAlpha'].'</shadowAlpha>
							<shadowBlur>'.$config['sc_scrollerItem__shadowBlur'].'</shadowBlur>
							<shadowDistance>'.$config['sc_scrollerItem__shadowDistance'].'</shadowDistance>
							<borderWidth>'.$config['sc_scrollerItem__borderWidth'].'</borderWidth>
						</scrollerItem>';
					
				break;
			case 3:
				echo '<gallery>
							<source>'.$config['slide_source'].'</source>
						  </gallery>
						  <slideshow>
							<autostart>'.(int)$config['sc_slideshow__autostart'].'</autostart>
							<delay>'.$config['sc_slideshow__delay'].'</delay>
							<music>'.$config['sc_slideshow__music'].'</music>
							<musicPath>slide_melody.mp3</musicPath>
							<source>'.$config['slide_source'].'</source>
						</slideshow>
						<image>
							<imageAsLink>'.(int)$config['sc_image__imageAsLink'].'</imageAsLink>
							<transitionEffect>'.$config['sc_image__transitionEffect'].'</transitionEffect>
							<transitionDuration>'.$config['sc_image__transitionDuration'].'</transitionDuration>
							<scaleMode>'.$config['sc_image__scaleMode'].'</scaleMode>
						</image>
						<caption>
							<align>'.$config['sc_caption__align'].'</align>
							<enable>'.(int)$config['sc_caption__enable'].'</enable>
							<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
							<fontColor>0x'.str_replace('#','',$config['sc_caption__fontColor']).'</fontColor>
							<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
							<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
						</caption>
						<scroller>
							<distanceFromBorder>'.$config['sc_scroller__distanceFromBorder'].'</distanceFromBorder>
							<align>'.$config['sc_scroller__align'].'</align>
							<enable>'.$config['sc_scroller__enable'].'</enable>
							<size>'.$config['sc_scroller__size'].'</size>
							<color>0x'.str_replace('#','',$config['sc_scroller__color']).'</color>
							<alpha>'.$config['sc_scroller__alpha'].'</alpha>
							<direction>'.$config['sc_scroller__direction'].'</direction>
							<rectangleIndent>'.$config['sc_scroller__rectangleIndent'].'</rectangleIndent>
							<rectangleDistance>'.$config['sc_scroller__rectangleDistance'].'</rectangleDistance>
							<rectangleColor>0x'.str_replace('#','',$config['sc_scroller__rectangleColor']).'</rectangleColor>
							<rectangleAlpha>'.$config['sc_scroller__rectangleAlpha'].'</rectangleAlpha>
							<rectangleBorderSize>'.$config['sc_scroller__rectangleBorderSize'].'</rectangleBorderSize>
							<rectangleBorderColor>0x'.str_replace('#','',$config['sc_scroller__rectangleBorderColor']).'</rectangleBorderColor>
							<rectangleWidth>'.$config['sc_scroller__rectangleWidth'].'</rectangleWidth>
							<rectangleHeight>'.$config['sc_scroller__rectangleHeight'].'</rectangleHeight>
							<useImagesInRectangle>'.(int)$config['sc_scroller__useImagesInRectangle'].'</useImagesInRectangle>
						</scroller>	';
				break;
				
			}		
			echo   '<flickr>
						<searchBy>'.$config['sc_flickr__searchBy'].'</searchBy>
						<userID>'.$config['sc_flickr__userID'].'</userID>
						<keyword>'.$config['sc_flickr__keyword'].'</keyword>
						<max>'.$config['sc_flickr__max'].'</max>
						<albumID>'.$config['sc_flickr__albumID'].'</albumID>
					</flickr>

					<picasa>
						<searchBy>'.$config['sc_picasa__searchBy'].'</searchBy> 
						<userName>'.$config['sc_picasa__userName'].'</userName>
						<keyword>'.$config['sc_picasa__keyword'].'</keyword>
						<max>'.$config['sc_picasa__max'].'</max>
						<albumID>'.$config['sc_picasa__albumID'].'</albumID>
						<albumName>'.$config['sc_picasa__albumName'].'</albumName>
					</picasa>

					<photobucket>
						<max>'.$config['sc_photobucket__max'].'</max> 
						<searchString>'.$config['sc_photobucket__searchString'].'</searchString>
					</photobucket>';
					
			echo '</config>';
} else {
    die();
}

die();

?>

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
			case 3:
				echo '<gallery>
							<source>'.$config['slide_source'].'</source>
						  </gallery>
						  <slideshow>
							<autostart>'.(int)$config['sc_slideshow__autostart'].'</autostart>
							<delay>'.$config['sc_slideshow__delay'].'</delay>
							<music>'.$config['sc_slideshow__music'].'</music>
							<mus1cPath>slide_melody.mp3</mus1cPath>
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

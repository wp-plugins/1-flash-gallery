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
	$gallery_type = fgallery_get_flash_type($album);
	$view = 0;
	if (isset($_GET['view']) && is_numeric($_GET['view'])) {
		$view = $_GET['view'];
	}
	if ($view) {
		header("Content-type: text/xml");
		// lem9 & loic1: IE need specific headers
		if (PMA_USR_BROWSER_AGENT == 'IE') {
			header('Content-Disposition: inline; filename="settings_'.$gallery_type.'.xml"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="settings_'.$gallery_type.'.xml"');
			header('Expires: 0');
			header('Pragma: no-cache');
		}
			readfile(FGALLERY_PATH.'/config.php?gall_id='.$gall_id);
		exit;
	} else {	
		header("Content-type: text/xml");
		echo "<?xml version='1.0' encoding='UTF-8'?>";
	}
	echo '<config>';
	switch ($album['gall_type']) {
	case 1:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
			</gallery>
			<slideshow>
				<autostart>'.$config['sc_slideshow__autostart'].'</autostart>
				<delay>'.$config['sc_slideshow__delay'].'</delay>';
				echo '<'.get_option('1_flash_gallery_acosta', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_acosta', MUSIC_PATH).'>';
		echo '</slideshow>
			<image>
				<changeByClick>'.$config['sc_image__changeByClick'].'</changeByClick>
				<imageAsLink>'.$config['sc_image__imageAsLink'].'</imageAsLink>
				<linkTarget>'.$config['sc_image__linkTarget'].'</linkTarget>
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
				<enable>'.$config['sc_navigation__enable'].'</enable>
				<align>'.$config['sc_navigation__align'].'</align>
				<visible>'.$config['sc_navigation__visible'].'</visible>
				<position>'.$config['sc_navigation__position'].'</position>
			</navigation>
			<caption>
				<align>'.$config['sc_caption__align'].'</align>
				<visible>'.$config['sc_caption__visible'].'</visible>
				<enable>'.(int)$config['sc_caption__enable'].'</enable>
				<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
				<fontColor>0x'.str_replace('#','',$config['sc_caption__fontColor']).'</fontColor>
				<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
				<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
			</caption>
			<scroller>
				<scrollBy>'.$config['sc_scroller__scrollBy'].'</scrollBy>
				<speed>'.$config['sc_scroller__speed'].'</speed>
				<color>0x'.str_replace('#','',$config['sc_scroller__color']).'</color>
				<alpha>'.$config['sc_scroller__alpha'].'</alpha>
				<lineStyle>'.$config['sc_scroller__lineStyle'].'</lineStyle>
				<hideButtons>'.$config['sc_scroller__hideButtons'].'</hideButtons>
				<itemDistance>'.$config['sc_scroller__itemDistance'].'</itemDistance>
				<borderWidth>'.$config['sc_scroller__borderWidth'].'</borderWidth>
				<borderColor>0x'.str_replace('#','',$config['sc_scroller__borderColor']).'</borderColor>
				<size>'.$config['sc_scroller__size'].'</size>
				<showPopupTitle>'.$config['sc_scroller__showPopupTitle'].'</showPopupTitle>
				<direction>'.$config['sc_scroller__direction'].'</direction>
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
				<gradientStart>0x'.str_replace('#','',$config['sc_scrollerItem__gradientStart']).'</gradientStart>
				<gradientEnd>0x'.str_replace('#','',$config['sc_scrollerItem__gradientEnd']).'</gradientEnd>
				<textColor>0x'.str_replace('#','',$config['sc_scrollerItem__textColor']).'</textColor>
				<fontSize>'.$config['sc_scrollerItem__fontSize'].'</fontSize>
			</scrollerItem>';
	break;
		
	case 2: 
		echo '<gallery>
					<source>'.$config['slide_source'].'</source>
				</gallery>
			<slideshow>
				<autostart>'.(int)$config['sc_slideshow__autostart'].'</autostart>
				<delay>'.$config['sc_slideshow__delay'].'</delay>';
				echo '<'.get_option('1_flash_gallery_airion', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_airion', MUSIC_PATH).'>';
		echo '</slideshow>
			<preview>
				<width>'.$config['sc_preview__width'].'</width>
				<height>'.$config['sc_preview__height'].'</height>
				<alpha>'.$config['sc_preview__alpha'].'</alpha>
				<cornerRadius>'.(int)$config['sc_preview__cornerRadius'].'</cornerRadius>
				<transitionEffect>'.$config['sc_preview__transitionEffect'].'</transitionEffect>
				<transitionDuration>'.$config['sc_preview__transitionDuration'].'</transitionDuration>
				<shadow>'.(int)$config['sc_preview__shadow'].'</shadow>
				<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
				<distanceFromScroller>'.$config['sc_preview__distanceFromScroller'].'</distanceFromScroller>
				<shadowColor>0x'.str_replace('#','',$config['sc_preview__shadowColor']).'</shadowColor>
				<shadowAlpha>'.$config['sc_preview__shadowAlpha'].'</shadowAlpha>
				<shadowBlur>'.$config['sc_preview__shadowBlur'].'</shadowBlur>
				<shadowDistance>'.$config['sc_preview__shadowDistance'].'</shadowDistance>
				<borderWidth>'.$config['sc_preview__borderWidth'].'</borderWidth>
			</preview>
			<image>
				<changeByClick>'.$config['sc_image__changeByClick'].'</changeByClick>
				<imageAsLink>'.$config['sc_image__imageAsLink'].'</imageAsLink>
				<linkTarget>'.$config['sc_image__linkTarget'].'</linkTarget>
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
				<position>'.$config['sc_navigation__position'].'</position>
				<hideAlbumButton>'.$config['sc_navigation__hideAlbumButton'].'</hideAlbumButton>
			</navigation>
			<caption>
				<align>'.$config['sc_caption__align'].'</align>
				<visible>'.$config['sc_caption__visible'].'</visible>
				<enable>'.(int)$config['sc_caption__enable'].'</enable>
				<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
				<fontColor>0x'.str_replace('#','',$config['sc_caption__fontColor']).'</fontColor>
				<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
				<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
			</caption>
			<scroller>
				<enable>'.(int)$config['sc_scroller__enable'].'</enable>
				<scrollBy>'.$config['sc_scroller__scrollBy'].'</scrollBy>
				<speed>'.$config['sc_scroller__speed'].'</speed>
				<color>0x'.str_replace('#','',$config['sc_scroller__color']).'</color>
				<alpha>'.$config['sc_scroller__alpha'].'</alpha>
				<cornerRadius>'.(int)$config['sc_scroller__cornerRadius'].'</cornerRadius>
				<lineStyle>'.$config['sc_scroller__lineStyle'].'</lineStyle>
				<hideButtons>'.((int)$config['sc_scroller__hideButtons'] == 1 ? '1':'0').'</hideButtons>
				<itemDistance>'.$config['sc_scroller__itemDistance'].'</itemDistance>
				<borderWidth>'.$config['sc_scroller__borderWidth'].'</borderWidth>
				<borderColor>0x'.str_replace('#','',$config['sc_scroller__borderColor']).'</borderColor>
				<size>'.$config['sc_scroller__size'].'</size>
				<direction>'.$config['sc_scroller__direction'].'</direction>
				<showPopupTitle>'.$config['sc_scroller__showPopupTitle'].'</showPopupTitle>
			</scroller>	
			<scrollerItem>
				<width>'.$config['sc_scrollerItem__width'].'</width>
				<height>'.$config['sc_scrollerItem__height'].'</height>
				<cornerRadius>'.(int)$config['sc_scrollerItem__cornerRadius'].'</cornerRadius>
				<alpha>'.$config['sc_scrollerItem__alpha'].'</alpha>
				<shadow>'.(int)$config['sc_scrollerItem__shadow'].'</shadow>
				<borderColor>0x'.str_replace('#','',$config['sc_scrollerItem__borderColor']).'</borderColor>
				<borderColorOnHover>0x'.str_replace('#','',$config['sc_scrollerItem__borderColorOnHover']).'</borderColorOnHover>
				<borderColorOnClick>0x'.str_replace('#','',$config['sc_scrollerItem__borderColorOnClick']).'</borderColorOnClick>
				<shadowColor>0x'.str_replace('#','',$config['sc_scrollerItem__shadowColor']).'</shadowColor>
				<shadowAlpha>'.$config['sc_scrollerItem__shadowAlpha'].'</shadowAlpha>
				<shadowBlur>'.$config['sc_scrollerItem__shadowBlur'].'</shadowBlur>
				<shadowDistance>'.$config['sc_scrollerItem__shadowDistance'].'</shadowDistance>
				<borderWidth>'.$config['sc_scrollerItem__borderWidth'].'</borderWidth>
				<forceItemToPreview>'.$config['sc_scrollerItem__forceItemToPreview'].'</forceItemToPreview>
			</scrollerItem>';
			
		break;
	case 3:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
			</gallery>
			<slideshow>
				<autostart>'.(int)$config['sc_slideshow__autostart'].'</autostart>
				<delay>'.$config['sc_slideshow__delay'].'</delay>';
				echo '<'.get_option('1_flash_gallery_arai', MUSIC_PATH).'>'
					.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_arai', MUSIC_PATH).'>';
		echo '</slideshow>
			<image>
				<changeByClick>'.$config['sc_image__changeByClick'].'</changeByClick>
				<linkTarget>'.$config['sc_image__linkTarget'].'</linkTarget>
				<imageAsLink>'.(int)$config['sc_image__imageAsLink'].'</imageAsLink>
				<transitionEffect>'.$config['sc_image__transitionEffect'].'</transitionEffect>
				<transitionDuration>'.$config['sc_image__transitionDuration'].'</transitionDuration>
				<scaleMode>'.$config['sc_image__scaleMode'].'</scaleMode>
			</image>
			<background>
				<type>'.$config['sc_background__type'].'</type>
				<src>'.$config['sc_background__src'].'</src>
				<alpha>'.$config['sc_background__alpha'].'</alpha>
				<color>0x'.str_replace('#','',$config['sc_background__color']).'</color>
			</background>
			<caption>
				<align>'.$config['sc_caption__align'].'</align>
				<enable>'.(int)$config['sc_caption__enable'].'</enable>
				<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
				<fontColor>0x'.str_replace('#','',$config['sc_caption__fontColor']).'</fontColor>
				<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
				<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
				<visible>'.$config['sc_caption__visible'].'</visible>
			</caption>
			<scroller>
				<distanceFromBorder>'.$config['sc_scroller__distanceFromBorder'].'</distanceFromBorder>
				<align>'.$config['sc_scroller__align'].'</align>
				<enable>'.$config['sc_scroller__enable'].'</enable>
				<size>'.$config['sc_scroller__size'].'</size>
				<color>0x'.str_replace('#','',$config['sc_scroller__color']).'</color>
				<alpha>'.$config['sc_scroller__alpha'].'</alpha>
				<direction>'.$config['sc_scroller__direction'].'</direction>
				<speed>'.$config['sc_scroller__speed'].'</speed>
				<borderColor>0x'.str_replace('#','',$config['sc_scroller__borderColor']).'</borderColor>
				<borderWidth>'.$config['sc_scroller__borderWidth'].'</borderWidth>
				<itemDistance>'.$config['sc_scroller__itemDistance'].'</itemDistance>
				<itemIndent>'.$config['sc_scroller__itemIndent'].'</itemIndent>
				<useImagesInItem>'.(int)$config['sc_scroller__useImagesInItem'].'</useImagesInItem>
				<showPopupTitle>'.$config['sc_scroller__showPopupTitle'].'</showPopupTitle>
			</scroller>	
			<scrollerItem>
				<width>'.$config['sc_scrollerItem__width'].'</width>
				<height>'.$config['sc_scrollerItem__height'].'</height>
				<alpha>'.$config['sc_scrollerItem__alpha'].'</alpha>
				<borderColor>0x'.str_replace('#','',$config['sc_scrollerItem__borderColor']).'</borderColor>
				<color>0x'.str_replace('#','',$config['sc_scrollerItem__color']).'</color>
				<borderWidth>'.$config['sc_scrollerItem__borderWidth'].'</borderWidth>
			</scrollerItem>';
	break;
		
	case 4:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
				<type>byFolder</type>
			</gallery>
			<slideshow>
				<enable>'.(int)$config['sc_slideshow__enable'].'</enable>
				<delay>'.$config['sc_slideshow__delay'].'</delay>';
				echo '<'.get_option('1_flash_gallery_pax', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_pax', MUSIC_PATH).'>';
		echo '</slideshow>
			<preview>
				<width>'.$config['sc_preview__width'].'</width>
				<height>'.$config['sc_preview__height'].'</height>
				<status>'.$config['sc_preview__status'].'</status>
				<description>'.$config['sc_preview__description'].'</description>
				<descriptionSize>'.$config['sc_preview__descriptionSize'].'</descriptionSize>
				<countByWidth>'.$config['sc_preview__countByWidth'].'</countByWidth>
				<countByHeight>'.$config['sc_preview__countByHeight'].'</countByHeight>
				<scrollingDirection>'.$config['sc_preview__scrollingDirection'].'</scrollingDirection>
				<shadow>'.(int)$config['sc_preview__shadow'].'</shadow>
				<border>'.(int)$config['sc_preview__border'].'</border>
				<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
				<shadowColor>0x'.str_replace('#','',$config['sc_preview__shadowColor']).'</shadowColor>
				<borderWidth>'.$config['sc_preview__borderWidth'].'</borderWidth>
				<selectTint>'.(int)$config['sc_preview__selectTint'].'</selectTint>
				<scrollingSpeed>'.$config['sc_preview__scrollingSpeed'].'</scrollingSpeed>
			</preview>
			<image>
				<width>'.$config['sc_image__width'].'</width>
				<height>'.$config['sc_image__height'].'</height>
				<status>'.$config['sc_image__status'].'</status>
				<description>'.$config['sc_image__description'].'</description>
				<descriptionSize>'.$config['sc_image__descriptionSize'].'</descriptionSize>
				<stageBlackout>'.(int)$config['sc_image__stageBlackout'].'</stageBlackout>
				<paginates>'.(int)$config['sc_image__paginates'].'</paginates>
				<isURL>'.(int)$config['sc_image__isURL'].'</isURL>
			</image>
			<background>
				<type>'.$config['sc_background__type'].'</type>
				<src>'.$config['sc_background__src'].'</src>
				<alpha>'.$config['sc_background__alpha'].'</alpha>
				<color>0x'.str_replace('#','',$config['sc_background__color']).'</color>
			</background>
			<navigation>
				<align>'.$config['sc_navigation__align'].'</align>
				<name>'.$config['sc_navigation__name'].'</name>
				<albumIcon>'.(int)$config['sc_navigation__albumIcon'].'</albumIcon>
			</navigation>
			<controls>
				<fullscreen>'.(int)$config['sc_controls__fullscreen'].'</fullscreen>
			</controls>
			<screen>
				<theme>'.$config['sc_screen__theme'].'</theme>
				<fog>'.(int)$config['sc_screen__fog'].'</fog>
				<fogWidth>'.(int)$config['sc_screen__fogWidth'].'</fogWidth>
				<mainPreloader>'.(int)$config['sc_screen__mainPreloader'].'</mainPreloader>
				<navigationsButton>'.(int)$config['sc_screen__navigationsButton'].'</navigationsButton>
				<previewPreloader>'.(int)$config['sc_screen__previewPreloader'].'</previewPreloader>
			</screen>';
	break;
		
	case 5:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
				<type>byFolder</type>
			</gallery>
			<slideshow>
				<showEnable>'.(int)$config['sc_slideshow__showEnable'].'</showEnable>
				<showDelay>'.$config['sc_slideshow__showDelay'].'</showDelay>
                                <imageWidth>'.$config['sc_slideshow__imageWidth'].'</imageWidth>
                                <imageHeight>'.$config['sc_slideshow__imageHeight'].'</imageHeight>
                                <backgroundType>'.$config['sc_slideshow__backgroundType'].'</backgroundType>
                                <backgroundPath>'.$config['sc_slideshow__backgroundPath'].'</backgroundPath>
                                <backgroundAlpha>'.$config['sc_slideshow__backgroundAlpha'].'</backgroundAlpha>
                                <backgroundColor>0x'.str_replace('#','',$config['sc_slideshow__backgroundColor']).'</backgroundColor>
                                <fullScreenEnable>'.$config['sc_slideshow__fullScreenEnable'].'</fullScreenEnable>
                                <dimmingBackground>'.$config['sc_slideshow__dimmingBackground'].'</dimmingBackground>
                                <target>'.$config['sc_slideshow__target'].'</target>';
				echo '<'.get_option('1_flash_gallery_pazin', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_pazin', MUSIC_PATH).'>';
		echo '</slideshow>
			<preview>
				<width>'.$config['sc_preview__width'].'</width>
				<height>'.$config['sc_preview__height'].'</height>
				<scale>'.$config['sc_preview__scale'].'</scale>
				<borderThickness>'.(int)$config['sc_preview__borderThickness'].'</borderThickness>
				<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
				<titlePosition>'.$config['sc_preview__titlePosition'].'</titlePosition>
				<titleAlign>'.$config['sc_preview__titleAlign'].'</titleAlign>
				<titleSize>'.$config['sc_preview__titleSize'].'</titleSize>
				<titleUseEmbedFont>'.$config['sc_preview__titleUseEmbedFont'].'</titleUseEmbedFont>
				<titleFont>'.$config['sc_preview__titleFont'].'</titleFont>
				<titleColor>0x'.str_replace('#','',$config['sc_preview__titleColor']).'</titleColor>
				<titleBold>'.$config['sc_preview__titleBold'].'</titleBold>
				<cornerRadius>'.(int)$config['sc_preview__cornerRadius'].'</cornerRadius>
				<shadowDistance>'.(int)$config['sc_preview__shadowDistance'].'</shadowDistance>
				<shadowSize>'.$config['sc_preview__shadowSize'].'</shadowSize>
                                <shadowColor>0x'.str_replace('#','',$config['sc_preview__shadowColor']).'</shadowColor>
                                <shadowAngle>'.$config['sc_preview__shadowAngle'].'</shadowAngle>
				<scatter>'.$config['sc_preview__scatter'].'</scatter>
                                <preloaderColor>0x'.str_replace('#','',$config['sc_preview__preloaderColor']).'</preloaderColor>
			</preview>
                        <navigation>
                            <flipping>'.$config['sc_navigation__flipping'].'</flipping>
                            <buttons>'.$config['sc_navigation__buttons'].'</buttons>
                            <buttonsColor>0x'.str_replace('#','',$config['sc_navigation__buttonsColor']).'</buttonsColor>
                            <buttonsBackColor>0x'.str_replace('#','',$config['sc_navigation__buttonsBackColor']).'</buttonsBackColor>
                            <buttonsAlpha>0x'.str_replace('#','',$config['sc_navigation__buttonsAlpha']).'</buttonsAlpha>
                        </navigation>';	
	break;
		
	case 6:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
				<type>byFolder</type>
				<color>0x'.str_replace('#','',$config['sc_gallery__color']).'</color>
				<alpha>'.$config['sc_gallery__alpha'].'</alpha>
				<photo>'.$config['sc_gallery__photo'].'</photo>
			</gallery>
			<slideshow>
				<enable>'.(int)$config['sc_slideshow__enable'].'</enable>
				<delay>'.$config['sc_slideshow__delay'].'</delay>
				<music>'.(int)$config['sc_slideshow__music'].'</music>';
				echo '<'.get_option('1_flash_gallery_postma', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_postma', MUSIC_PATH).'>';
	  echo '</slideshow>
			<scroller>
				<visible>'.(int)$config['sc_scroller__visible'].'</visible>
				<width>'.$config['sc_scroller__width'].'</width>
			</scroller>
			<caption>
				<align>'.$config['sc_caption__align'].'</align>
				<visible>'.$config['sc_caption__visible'].'</visible>
				<enable>'.(int)$config['sc_caption__enable'].'</enable>
				<font>'.$config['sc_caption__font'].'</font>
				<bold>'.(int)$config['sc_caption__bold'].'</bold>
				<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
				<textColor>0x'.str_replace('#','',$config['sc_caption__textColor']).'</textColor>
				<background>'.(int)$config['sc_caption__background'].'</background>
				<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
				<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
			</caption>
			<preview>
				<type>'.$config['sc_preview__type'].'</type>
				<color>0x'.str_replace('#','',$config['sc_gallery__color']).'</color>
				<scaleEffect>'.$config['sc_preview__scaleEffect'].'</scaleEffect>
				<reflection>'.(int)$config['sc_preview__reflection'].'</reflection>
				<reflectionAlpha>'.$config['sc_preview__reflectionAlpha'].'</reflectionAlpha>
				<reflectionDistance>'.(int)$config['sc_preview__reflectionDistance'].'</reflectionDistance>
				<reflectionGradientColorStart>0x'.str_replace('#','',$config['sc_preview__reflectionGradientColorStart']).'</reflectionGradientColorStart>
				<reflectionGradientColorFinish>0x'.str_replace('#','',$config['sc_preview__reflectionGradientColorFinish']).'</reflectionGradientColorFinish>
				<borderWidth>'.$config['sc_preview__borderWidth'].'</borderWidth>
				<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
				<borderEclipse>'.$config['sc_preview__borderWidth'].'</borderEclipse>
				<borderAlpha>'.$config['sc_preview__borderAlpha'].'</borderAlpha>
				<borderPhotoWidth>'.$config['sc_preview__borderPhotoWidth'].'</borderPhotoWidth>
				<borderPhotoColor>0x'.str_replace('#','',$config['sc_preview__borderPhotoColor']).'</borderPhotoColor>
				<borderPhotoAlpha>'.$config['sc_preview__borderPhotoAlpha'].'</borderPhotoAlpha>
				<backgroundPhoto>'.$config['sc_preview__backgroundPhoto'].'</backgroundPhoto>
				<alpha>'.$config['sc_preview__alpha'].'</alpha>
				<width>'.$config['sc_preview__width'].'</width>
				<height>'.$config['sc_preview__height'].'</height>
				<distanceX>'.$config['sc_preview__distanceX'].'</distanceX>
				<distanceZ>'.$config['sc_preview__distanceZ'].'</distanceZ>
				<rotation>'.$config['sc_preview__rotation'].'</rotation>
				<cornerRadius>'.$config['sc_preview__cornerRadius'].'</cornerRadius>
				<backgroundColor>0x'.str_replace('#','',$config['sc_preview__backgroundColor']).'</backgroundColor>
				<isURL>'.(int)$config['sc_preview__isURL'].'</isURL>
				<scaleSmall>'.$config['sc_preview__scaleSmall'].'</scaleSmall>
				<scalePhoto>'.$config['sc_preview__scalePhoto'].'</scalePhoto>
				<target>'.$config['sc_preview__target'].'</target>
			</preview>
			<tool_tip>
				  <color>0x'.str_replace('#','',$config['sc_tool_tip__color']).'</color>
				  <alpha>'.$config['sc_tool_tip__alpha'].'</alpha>
				  <max_width>'.$config['sc_tool_tip__max_width'].'</max_width>
				  <font>'.$config['sc_tool_tip__font'].'</font>
				  <font_color>'.$config['sc_tool_tip__font_color'].'</font_color>
				  <font_size>'.$config['sc_tool_tip__font_size'].'</font_size>
				  <time_show>'.$config['sc_tool_tip__time_show'].'</time_show>
			</tool_tip>';
	break;
	
	case 7:
		echo  '<slideshow>
				<autostart>'.(int)$config['sc_slideshow__autostart'].'</autostart>
				<delay>'.$config['sc_slideshow__delay'].'</delay>
				<music>'.$config['sc_slideshow__music'].'</music>';
				echo '<'.get_option('1_flash_gallery_pageflip', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_pageflip', MUSIC_PATH).'>';
		echo '</slideshow>';
		echo '<main>
				<flipWidth>'.$config['sc_main__flipWidth'].'</flipWidth>
				<flipHeight>'.$config['sc_main__flipHeight'].'</flipHeight>
				<backgroundColor>'.str_replace('#','0x',$config['sc_main__backgroundColor']).'</backgroundColor>
				<backgroundImage>'.$config['sc_main__backgroundImage'].'</backgroundImage>
				<backgroundImagePlacement>'.$config['sc_main__backgroundImagePlacement'].'</backgroundImagePlacement>
				<alwaysOpened>'.$config['sc_main__alwaysOpened'].'</alwaysOpened>
				<handOverCorner>'.$config['sc_main__handOverCorner'].'</handOverCorner>
				<dropShadowEnabled>'.$config['sc_main__dropShadowEnabled'].'</dropShadowEnabled>
				<dropShadowHideWhenFlipping>'.$config['sc_main__dropShadowHideWhenFlipping'].'</dropShadowHideWhenFlipping>
				<shadowDepth>'.$config['sc_main__shadowDepth'].'</shadowDepth>
				<flipSound>'.$config['sc_main__flipSound'].'</flipSound>
				<navigationBar>'.$config['sc_main__navigationBar'].'</navigationBar>
				<navigationBarPlacement>'.$config['sc_main__navigationBarPlacement'].'</navigationBarPlacement>
				<navigationBarTitle>'.$config['sc_main__navigationBarTitle'].'</navigationBarTitle>
				<navigationBarBgAlpha>'.$config['sc_main__navigationBarBgAlpha'].'</navigationBarBgAlpha>
				<fitToStageOnFullscreen>'.$config['sc_main__fitToStageOnFullscreen'].'</fitToStageOnFullscreen>
				<pageAlignContent>'.$config['sc_main__pageAlignContent'].'</pageAlignContent>
				<pageBackgroundColor>'.str_replace('#','0x',$config['sc_main__pageBackgroundColor']).'</pageBackgroundColor>
				<pageBackgroundImage>'.$config['sc_main__pageBackgroundImage'].'</pageBackgroundImage>
				<pageFrame>'.$config['sc_main__pageFrame'].'</pageFrame>
				<pageFrameColor>'.$config['sc_main__pageFrameColor'].'</pageFrameColor>
				<pageFrameAlpha>'.$config['sc_main__pageFrameAlpha'].'</pageFrameAlpha>
				<pageAngleProportion>'.$config['sc_main__pageAngleProportion'].'</pageAngleProportion>
				<coverFrame>'.$config['sc_main__coverFrame'].'</coverFrame>
				<coverFrameColor>'.str_replace('#','0x',$config['sc_main__coverFrameColor']).'</coverFrameColor>
				<coverFrameAlpha>'.$config['sc_main__coverFrameAlpha'].'</coverFrameAlpha>
			</main>
			<menu>
				<albumListBtn src="'.FGALLERY_PATH.'/swf/skin/albumListBtn.swf">'.$config['sc_menu__albumListBtn'].'</albumListBtn>
				<exactFitBtn src="'.FGALLERY_PATH.'/swf/skin/exactFitBtn.swf">'.$config['sc_menu__exactFitBtn'].'</exactFitBtn>
				<zoomInBtn src="'.FGALLERY_PATH.'/swf/skin/zoomInBtn.swf">'.$config['sc_menu__zoomInBtn'].'</zoomInBtn>
				<zoomOutBtn src="'.FGALLERY_PATH.'/swf/skin/zoomOutBtn.swf">'.$config['sc_menu__zoomOutBtn'].'</zoomOutBtn>
				<fitToStageBtn src="'.FGALLERY_PATH.'/swf/skin/fitToStageBtn.swf">'.$config['sc_menu__fitToStageBtn'].'</fitToStageBtn>
				<firstBtn src="'.FGALLERY_PATH.'/swf/skin/firstBtn.swf">'.$config['sc_menu__firstBtn'].'</firstBtn>
				<previousBtn src="'.FGALLERY_PATH.'/swf/skin/previousBtn.swf">'.$config['sc_menu__previousBtn'].'</previousBtn>
				<navigationString src="'.FGALLERY_PATH.'/swf/skin/gotoBtn.swf">'.$config['sc_menu__navigationString'].'</navigationString>
				<nextBtn src="'.FGALLERY_PATH.'/swf/skin/nextBtn.swf">'.$config['sc_menu__nextBtn'].'</nextBtn>
				<lastBtn src="'.FGALLERY_PATH.'/swf/skin/lastBtn.swf">'.$config['sc_menu__lastBtn'].'</lastBtn>
				<soundOnBtn src="'.FGALLERY_PATH.'/swf/skin/soundOnBtn.swf">'.$config['sc_menu__soundOnBtn'].'</soundOnBtn>
				<sounOffBtn src="'.FGALLERY_PATH.'/swf/skin/sounOffBtn.swf">'.$config['sc_menu__sounOffBtn'].'</sounOffBtn>
				<printBtn src="'.FGALLERY_PATH.'/swf/skin/printBtn.swf">'.$config['sc_menu__printBtn'].'</printBtn>
				<downloadBtn src="'.FGALLERY_PATH.'/swf/skin/downloadBtn.swf">'.$config['sc_menu__downloadBtn'].'</downloadBtn>
				<fullscreenBtn src="'.FGALLERY_PATH.'/swf/skin/fullscreenBtn.swf">'.$config['sc_menu__fullscreenBtn'].'</fullscreenBtn>
				<exitFullscreenBtn src="'.FGALLERY_PATH.'/swf/skin/exitFullscreenBtn.swf">'.$config['sc_menu__exitFullscreenBtn'].'</exitFullscreenBtn>
			</menu>';
	break;
		
	case 8:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
				<type>byFolder</type>
			</gallery>
			<slideshow>
				<enable>'.(int)$config['sc_slideshow__enable'].'</enable>
				<delay>'.$config['sc_slideshow__delay'].'</delay>
				<stopByClick>'.(int)$config['sc_slideshow__stopByClick'].'</stopByClick>';
		echo '<'.get_option('1_flash_gallery_nilus', MUSIC_PATH).'>'
					.$config['sc_slideshow__musicPath'].
					'</'.get_option('1_flash_gallery_nilus', MUSIC_PATH).'>';
		echo '</slideshow>
			<main_screen> 
				<width>'.$config['sc_main_screen__width'].'</width>
				<height>'.$config['sc_main_screen__height'].'</height>
				<shadow>'.$config['sc_main_screen__shadow'].'</shadow>
				<color>0x'.str_replace('#','',$config['sc_main_screen__color']).'</color>
				<time_change_screen>'.$config['sc_main_screen__time_change_screen'].'</time_change_screen>
				<small_width>'.$config['sc_main_screen__small_width'].'</small_width>
				<small_height>'.$config['sc_main_screen__small_height'].'</small_height>
				<small_color>0x'.str_replace('#','',$config['sc_main_screen__small_color']).'</small_color>
				<small_x>'.$config['sc_main_screen__small_x'].'</small_x>
				<small_y>'.$config['sc_main_screen__small_y'].'</small_y>
				<time_show_small>'.$config['sc_main_screen__time_show_small'].'</time_show_small>
				<resizable_small>'.$config['sc_main_screen__resizable_small'].'</resizable_small>
				<blackout_small>'.$config['sc_main_screen__blackout_small'].'</blackout_small>
				<blackout_alpha>'.$config['sc_main_screen__blackout_alpha'].'</blackout_alpha>
				<frame_small_size>'.$config['sc_main_screen__frame_small_size'].'</frame_small_size>
				<frame_small_color>0x'.str_replace('#','',$config['sc_main_screen__frame_small_color']).'</frame_small_color>
				<frame_small_alpha>'.$config['sc_main_screen__frame_small_alpha'].'</frame_small_alpha>
				<frame_big_size>'.$config['sc_main_screen__frame_big_size'].'</frame_big_size>
				<frame_big_color>0x'.str_replace('#','',$config['sc_main_screen__frame_big_color']).'</frame_big_color>
				<frame_big_alpha>'.$config['sc_main_screen__frame_big_alpha'].'</frame_big_alpha>
				<color_arrow>'.$config['sc_main_screen__color_arrow'].'</color_arrow>
				<arrow_alpha>'.$config['sc_main_screen__arrow_alpha'].'</arrow_alpha>
				<target>'.$config['sc_main_screen__target'].'</target>
			</main_screen>
			<big_foto> 
				<color>0x'.str_replace('#','',$config['sc_big_foto__color']).'</color>
				<background>'.$config['sc_big_foto__background'].'</background>
				<text_color>0x'.str_replace('#','',$config['sc_big_foto__text_color']).'</text_color>
				<text_background_color>0x'.str_replace('#','',$config['sc_big_foto__text_background_color']).'</text_background_color>
				<text_background_height>'.$config['sc_big_foto__text_background_height'].'</text_background_height>
				<text_background_alpha>'.$config['sc_big_foto__text_background_alpha'].'</text_background_alpha>
				<font>'.$config['sc_big_foto__font'].'</font>
				<font_size>'.$config['sc_big_foto__font_size'].'</font_size>
				<font_bold>'.$config['sc_big_foto__font_bold'].'</font_bold>
				<time_open_photo>'.$config['sc_big_foto__time_open_photo'].'</time_open_photo>
				<time_change_alpha_photo>'.$config['sc_big_foto__time_change_alpha_photo'].'</time_change_alpha_photo>
				<time_change_photo>'.$config['sc_big_foto__time_change_photo'].'</time_change_photo>
				<effect_change_photo>'.$config['sc_big_foto__effect_change_photo'].'</effect_change_photo>
				<time_show_text>'.$config['sc_big_foto__time_show_text'].'</time_show_text>
				<time_show_arrow>'.$config['sc_big_foto__time_show_arrow'].'</time_show_arrow>
				<color_arrow>0x'.str_replace('#','',$config['sc_big_foto__color_arrow']).'</color_arrow>
				<arrow_alpha>'.$config['sc_big_foto__arrow_alpha'].'</arrow_alpha>
				<scalePhoto>'.$config['sc_big_foto__scalePhoto'].'</scalePhoto>
			</big_foto>
			<slideshow_bar>
				<enable>'.$config['sc_slideshow_bar__enable'].'</enable>
				<width>'.$config['sc_slideshow_bar__width'].'</width>
				<height>'.$config['sc_slideshow_bar__height'].'</height>
				<x>'.$config['sc_slideshow_bar__x'].'</x>
				<y>'.$config['sc_slideshow_bar__y'].'</y>
				<color>0x'.str_replace('#','',$config['sc_slideshow_bar__color']).'</color>
				<color2>0x'.str_replace('#','',$config['sc_slideshow_bar__color2']).'</color2>
				<alpha>'.$config['sc_slideshow_bar__alpha'].'</alpha>
				<eclipse>'.$config['sc_slideshow_bar__eclipse'].'</eclipse>
				<select_albums>'.$config['sc_slideshow_bar__select_albums'].'</select_albums>
				<arrow_left>'.$config['sc_slideshow_bar__arrow_left'].'</arrow_left>
				<play_slideshow>'.$config['sc_slideshow_bar__play_slideshow'].'</play_slideshow>
				<arrow_right>'.$config['sc_slideshow_bar__arrow_right'].'</arrow_right>
				<full_screen>'.$config['sc_slideshow_bar__full_screen'].'</full_screen>
				<buttons_alpha>'.$config['sc_slideshow_bar__buttons_alpha'].'</buttons_alpha>
				<buttons_space>'.$config['sc_slideshow_bar__buttons_space'].'</buttons_space>
				<select_albums_color>0x'.str_replace('#','',$config['sc_slideshow_bar__select_albums_color']).'</select_albums_color>
				<select_albums_eclipse>'.$config['sc_slideshow_bar__select_albums_eclipse'].'</select_albums_eclipse>
				<select_albums_alpha>'.$config['sc_slideshow_bar__select_albums_alpha'].'</select_albums_alpha>
				<select_albums_font>'.$config['sc_slideshow_bar__select_albums_font'].'</select_albums_font>
				<select_albums_font_size>'.$config['sc_slideshow_bar__select_albums_font_size'].'</select_albums_font_size>
				<select_albums_font_color>0x'.str_replace('#','',$config['sc_slideshow_bar__select_albums_font_color']).'</select_albums_font_color>
				<select_albums_font_color_over>0x'.str_replace('#','',$config['sc_slideshow_bar__select_albums_font_color_over']).'</select_albums_font_color_over>
				<select_albums_space_text>'.$config['sc_slideshow_bar__select_albums_space_text'].'</select_albums_space_text>
				<select_albums_time_show>'.$config['sc_slideshow_bar__select_albums_time_show'].'</select_albums_time_show>
				<select_albums_time_hide>'.$config['sc_slideshow_bar__select_albums_time_hide'].'</select_albums_time_hide>
			</slideshow_bar>
			<tool_tip>
				<color>0x'.str_replace('#','',$config['sc_tool_tip__color']).'</color>
				<alpha>'.$config['sc_tool_tip__alpha'].'</alpha>
				<max_width>'.$config['sc_tool_tip__max_width'].'</max_width>
				<font>'.$config['sc_tool_tip__font'].'</font>
				<font_color>'.$config['sc_tool_tip__font_color'].'</font_color>
				<font_size>'.$config['sc_tool_tip__font_size'].'</font_size>
				<time_show>'.$config['sc_tool_tip__time_show'].'</time_show>
			</tool_tip>';
	break;
	
	case 9:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
				<type>byFolder</type>
				<color>0x'.str_replace('#','',$config['sc_gallery__color']).'</color>
				<alpha>'.$config['sc_gallery__alpha'].'</alpha>
				<photo>'.$config['sc_gallery__photo'].'</photo>
			</gallery>
			<slideshow>
				<enable>'.(int)$config['sc_slideshow__enable'].'</enable>
				<delay>'.$config['sc_slideshow__delay'].'</delay>
				<music>'.(int)$config['sc_slideshow__music'].'</music>';
				echo '<'.get_option('1_flash_gallery_nusl', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_nusl', MUSIC_PATH).'>';
		echo '</slideshow>
			<scroller>
				<visible>'.(int)$config['sc_scroller__visible'].'</visible>
				<width>'.$config['sc_scroller__width'].'</width>
			</scroller>
			<caption>
				<align>'.$config['sc_caption__align'].'</align>
				<visible>'.$config['sc_caption__visible'].'</visible>
				<enable>'.(int)$config['sc_caption__enable'].'</enable>
				<font>'.$config['sc_caption__font'].'</font>
				<bold>'.(int)$config['sc_caption__bold'].'</bold>
				<fontSize>'.$config['sc_caption__fontSize'].'</fontSize>
				<textColor>0x'.str_replace('#','',$config['sc_caption__textColor']).'</textColor>
				<background>'.(int)$config['sc_caption__background'].'</background>
				<backgroundColor>0x'.str_replace('#','',$config['sc_caption__backgroundColor']).'</backgroundColor>
				<backgroundAlpha>'.$config['sc_caption__backgroundAlpha'].'</backgroundAlpha>
			</caption>
			<preview>
				<color>0x'.str_replace('#','',$config['sc_gallery__color']).'</color>
				<scaleEffect>'.$config['sc_preview__scaleEffect'].'</scaleEffect>
				<reflection>'.(int)$config['sc_preview__reflection'].'</reflection>
				<reflectionAlpha>'.$config['sc_preview__reflectionAlpha'].'</reflectionAlpha>
				<reflectionDistance>'.(int)$config['sc_preview__reflectionDistance'].'</reflectionDistance>
				<reflectionGradientColorStart>0x'.str_replace('#','',$config['sc_preview__reflectionGradientColorStart']).'</reflectionGradientColorStart>
				<reflectionGradientColorFinish>0x'.str_replace('#','',$config['sc_preview__reflectionGradientColorFinish']).'</reflectionGradientColorFinish>
				<borderWidth>'.$config['sc_preview__borderWidth'].'</borderWidth>
				<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
				<borderEclipse>'.$config['sc_preview__borderWidth'].'</borderEclipse>
				<borderAlpha>'.$config['sc_preview__borderAlpha'].'</borderAlpha>
				<borderPhotoWidth>'.$config['sc_preview__borderPhotoWidth'].'</borderPhotoWidth>
				<borderPhotoColor>0x'.str_replace('#','',$config['sc_preview__borderPhotoColor']).'</borderPhotoColor>
				<borderPhotoAlpha>'.$config['sc_preview__borderPhotoAlpha'].'</borderPhotoAlpha>
				<backgroundPhoto>'.$config['sc_preview__backgroundPhoto'].'</backgroundPhoto>
				<alpha>'.$config['sc_preview__alpha'].'</alpha>
				<width>'.$config['sc_preview__width'].'</width>
				<height>'.$config['sc_preview__height'].'</height>
				<distanceX>'.$config['sc_preview__distanceX'].'</distanceX>
				<distanceY>'.$config['sc_preview__distanceY'].'</distanceY>
				<distanceZ>'.$config['sc_preview__distanceZ'].'</distanceZ>
				<rotation>'.$config['sc_preview__rotation'].'</rotation>
				<cornerRadius>'.$config['sc_preview__cornerRadius'].'</cornerRadius>
				<backgroundColor>0x'.str_replace('#','',$config['sc_preview__backgroundColor']).'</backgroundColor>
				<isURL>'.(int)$config['sc_preview__isURL'].'</isURL>
				<scaleSmall>'.$config['sc_preview__scaleSmall'].'</scaleSmall>
				<scalePhoto>'.$config['sc_preview__scalePhoto'].'</scalePhoto>
				<target>'.$config['sc_preview__target'].'</target>
			</preview>
			<tool_tip>
				<color>0x'.str_replace('#','',$config['sc_tool_tip__color']).'</color>
				<alpha>'.$config['sc_tool_tip__alpha'].'</alpha>
				<max_width>'.$config['sc_tool_tip__max_width'].'</max_width>
				<font>'.$config['sc_tool_tip__font'].'</font>
				<font_color>'.$config['sc_tool_tip__font_color'].'</font_color>
				<font_size>'.$config['sc_tool_tip__font_size'].'</font_size>
				<time_show>'.$config['sc_tool_tip__time_show'].'</time_show>
			</tool_tip>';
	break;
	
	case 10:
		echo '<gallery>
				<source>'.$config['slide_source'].'</source>
			</gallery>
			<slideshow>
				<backgroundColor>0x'.str_replace('#','',$config['sc_slideshow__backgroundColor']).'</backgroundColor>
				<logo>'.$config['sc_slideshow__logo'].'</logo>
				<time>'.$config['sc_slideshow__time'].'</time>';
			echo '<'.get_option('1_flash_gallery_kranjk', MUSIC_PATH).'>'
				.$config['sc_slideshow__musicPath'].
				'</'.get_option('1_flash_gallery_kranjk', MUSIC_PATH).'>
                                 <useEmbedFont>'.$config['sc_slideshow__useEmbedFont'].'</useEmbedFont>
                                 <target>'.$config['sc_slideshow_target'].'</target>';
		echo '</slideshow>
			<navigation>
				<enable>'.$config['sc_navigation__enable'].'</enable>
				<indent>'.$config['sc_navigation__indent'].'</indent>
				<home>'.$config['sc_navigation__home'].'</home>
				<itemFont>'.$config['sc_navigation__itemFont'].'</itemFont>
				<itemSize>'.$config['sc_navigation__itemSize'].'</itemSize>
				<itemBold>'.$config['sc_navigation__itemBold'].'</itemBold>
				<itemColorLowerText>0x'.str_replace('#','',$config['sc_navigation__itemColorLowerText']).'</itemColorLowerText>
				<itemColorUpperText>0x'.str_replace('#','',$config['sc_navigation__itemColorUpperText']).'</itemColorUpperText>
				<itemColorLowerField>0x'.str_replace('#','',$config['sc_navigation__itemColorLowerField']).'</itemColorLowerField>
				<itemColorUpperField>0x'.str_replace('#','',$config['sc_navigation__itemColorUpperField']).'</itemColorUpperField>
			</navigation>
			<scroller>
				<position>'.$config['sc_scroller__position'].'</position>
                                <numberColumns>'.$config['sc_scroller__numberColumns'].'</numberColumns>
				<itemWidth>'.$config['sc_scroller__itemWidth'].'</itemWidth>
				<itemHeight>'.$config['sc_scroller__itemHeight'].'</itemHeight>
				<itemScale>'.$config['sc_scroller__itemScale'].'</itemScale>
                                <itemMotion>'.$config['sc_scroller__itemMotion'].'</itemMotion>
				<itemBorderColor>0x'.str_replace('#','',$config['sc_scroller__itemBorderColor']).'</itemBorderColor>
				<itemBorderThickness>'.$config['sc_scroller__itemBorderThickness'].'</itemBorderThickness>
				<itemBorderAlpha>'.$config['sc_scroller__itemBorderAlpha'].'</itemBorderAlpha>
				<itemBorderTime>'.$config['sc_scroller__itemBorderTime'].'</itemBorderTime>
				<itemWaveColor>0x'.str_replace('#','',$config['sc_scroller__itemWaveColor']).'</itemWaveColor>
				<itemWaveAlpha>'.$config['sc_scroller__itemWaveAlpha'].'</itemWaveAlpha>
				<itemWaveTime>'.$config['sc_scroller__itemWaveTime'].'</itemWaveTime>
				<itemTitlePosition>'.$config['sc_scroller__itemTitlePosition'].'</itemTitlePosition> 
				<itemTitleFont>'.$config['sc_scroller__itemTitleFont'].'</itemTitleFont>
				<itemTitleSize>'.$config['sc_scroller__itemTitleSize'].'</itemTitleSize>
				<itemTitleBold>'.$config['sc_scroller__itemTitleBold'].'</itemTitleBold>
				<itemTitleColor>0x'.str_replace('#','',$config['sc_scroller__itemTitleColor']).'</itemTitleColor>
				<itemTitleColorBack>0x'.str_replace('#','',$config['sc_scroller__itemTitleColorBack']).'</itemTitleColorBack>
				<itemTitleAlphaBack>'.$config['sc_scroller__itemTitleAlphaBack'].'</itemTitleAlphaBack>
                                <navigationEnable>'.$config['sc_scroller__navigationEnable'].'</navigationEnable>
				<navigationColorButton>0x'.str_replace('#','',$config['sc_scroller__navigationColorButton']).'</navigationColorButton>
				<navigationColorRect>0x'.str_replace('#','',$config['sc_scroller__navigationColorRect']).'</navigationColorRect>
			</scroller>
			<preview>
				<width>'.$config['sc_preview__width'].'</width>
				<height>'.$config['sc_preview__height'].'</height>
				<scale>'.$config['sc_preview__scale'].'</scale> 
				<preloaderColor>0x'.str_replace('#','',$config['sc_preview__preloaderColor']).'</preloaderColor>
				<borderColor>0x'.str_replace('#','',$config['sc_preview__borderColor']).'</borderColor>
				<borderThickness>'.$config['sc_preview__borderThickness'].'</borderThickness>
				<borderAlpha>'.$config['sc_preview__borderAlpha'].'</borderAlpha>
				<borderTime>'.$config['sc_preview__borderTime'].'</borderTime>
				<captionShow>'.$config['sc_preview__captionShow'].'</captionShow>
				<captionFont>'.$config['sc_preview__captionFont'].'</captionFont>
				<captionSize>'.$config['sc_preview__captionSize'].'</captionSize>
				<captionBold>'.$config['sc_preview__captionBold'].'</captionBold>
				<captionColor>0x'.str_replace('#','',$config['sc_preview__captionColor']).'</captionColor>
				<captionColorBack>0x'.str_replace('#','',$config['sc_preview__captionColorBack']).'</captionColorBack>
				<captionAlphaBack>'.$config['sc_preview__captionAlphaBack'].'</captionAlphaBack>
				<barColor>0x'.str_replace('#','',$config['sc_preview__barColor']).'</barColor>
                                <barEnable>'.$config['sc_preview__barEnable'].'</barEnable>
				<barFont>'.$config['sc_preview__barFont'].'</barFont>
				<barSize>'.$config['sc_preview__barSize'].'</barSize>
				<barBold>'.$config['sc_preview__barBold'].'</barBold> 
				<barColorText>0x'.str_replace('#','',$config['sc_preview__barColorText']).'</barColorText>
				<barColorButton>0x'.str_replace('#','',$config['sc_preview__barColorButton']).'</barColorButton>
				<navigationMode>'.$config['sc_preview__navigationMode'].'</navigationMode>
				<navigationButtonMode>'.$config['sc_preview__navigationButtonMode'].'</navigationButtonMode>
				<navigationButtonColorBorder>0x'.str_replace('#','',$config['sc_preview__navigationButtonColorBorder']).'</navigationButtonColorBorder>
				<navigationButtonColorBack>0x'.str_replace('#','',$config['sc_preview__navigationButtonColorBack']).'</navigationButtonColorBack>
				<navigationButtonColorRect>0x'.str_replace('#','',$config['sc_preview__navigationButtonColorRect']).'</navigationButtonColorRect>
				<navigationButtonThickness>'.$config['sc_preview__navigationButtonThickness'].'</navigationButtonThickness>
				<navigationButtonSize>'.$config['sc_preview__navigationButtonSize'].'</navigationButtonSize>
				<navigationButtonAlpha>'.$config['sc_preview__navigationButtonAlpha'].'</navigationButtonAlpha>
			</preview>';
	break;
	}	
	if (!in_array($album['gall_type'],array(7,8)))
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

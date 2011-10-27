<?php

/*
 * Functions to deal with gallery settings
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Returns gallery settings for given gallery id
 * @global type $wpdb
 * @param integer $gall_id
 * @return array      
 */
function fgallery_get_album_settings($gall_id) {
    global $wpdb;
        $settings = $wpdb->get_var("SELECT value FROM ".ALBUMS_SETTINGS_TABLE." WHERE gall_id = ".$gall_id);
        if (empty($settings)) {
            return fgallery_default_album_settings();
        } else {
            return unserialize($settings);
        }
}

/**
 * Saves the albums settings to db
 * @global type $wpdb
 * @param integer $gall_id
 * @param array $data
 * @return boolean 
 */
function fgallery_save_album_settings($gall_id, $data) {
    global $wpdb;
	$res = $wpdb->get_row("SELECT * FROM ".ALBUMS_SETTINGS_TABLE." WHERE gall_id = ".$gall_id, ARRAY_A);
	if (empty($res)) {
		$wpdb->insert(ALBUMS_SETTINGS_TABLE, array('gall_id'=>$gall_id, 'value'=>serialize($data)));
	} else {
		$wpdb->update(ALBUMS_SETTINGS_TABLE, array('value'=>serialize($data)), array('gall_id'=>$gall_id));
	}
    return true;
}

/**
 * Returns the given parameter from settings array
 * @param array $param
 * @param array $settings
 * @return type 
 */
function fgallery_get_settings_param($param, $settings) {
    $name = (string)$param['element_name'];
    if ($param['values'] == 'true') {
            return $settings[$name] ? 'true' : 'false';
    } else {
            return $settings[$name];
    }
}

/**
 * Prepare post data before inserting into database
 * @param array $data
 * @return type 
 */
function fgallery_prepare_settings($data) {
    unset($data['gallery']);
    $new = fgallery_default_album_settings();
    foreach ($new as $key=>$value) {
        if ($data[$key] != '') {
            $new[$key] = $data[$key];
        }
    }	
    return $new;
}

/**
 * Returns default gallery settings if there are no user customized
 * @return array 
 */
function fgallery_default_album_settings() {
    $settings = array();
        $settings['sc_gallery__color'] = 'ffffff';
        $settings['sc_gallery__alpha'] = 0.5;
        $settings['sc_gallery__photo'] = '';
	   
        $settings['sc_slideshow__pauseMusic'] = 0;
        $settings['sc_slideshow__stopMusicWhenShowStop'] = 0;
        $settings['sc_slideshow__sendID'] = 0;
        $settings['sc_slideshow__autostart'] = 0;
        $settings['sc_slideshow__imageWidth'] = 550;
        $settings['sc_slideshow__imageHeight'] = 500;
        $settings['sc_slideshow__backgroundColor'] = 'ffffff';
        $settings['sc_slideshow__backgroundType'] = 'color';
        $settings['sc_slideshow__backgroundPath'] = '';
        $settings['sc_slideshow__backgroundAlpha'] = 1;
        $settings['sc_slideshow__fullscreenEnable'] = 0;
        $settings['sc_slideshow__showEnable'] = 0;
        $settings['sc_slideshow__showDelay'] = 5;
        $settings['sc_slideshow__dimmingBackground'] = 0;
        $settings['sc_slideshow__logo'] = '';
        $settings['sc_slideshow__enable'] = 0;
        $settings['sc_slideshow__delay'] = '2';
        $settings['sc_slideshow__time'] = '5000';
        $settings['sc_slideshow__auto'] = 0;
        $settings['sc_slideshow__autostartSlideshow'] = 0;
        $settings['sc_slideshow__music'] = '';
        $settings['sc_slideshow__musicPath'] = '';
        $settings['sc_slideshow__useEmbedFont'] = 0;
        $settings['sc_slideshow__target'] = '_blank';
        $settings['sc_slideshow__theme'] = 'black';
        $settings['sc_slideshow__stopByClick'] = 0;
        $settings['slide_source'] = 'local';
		
        $settings['sc_preview__width'] = 240;
        $settings['sc_preview__enable'] = 0;
        $settings['sc_preview__type'] = 'triangle';
        $settings['sc_preview__height'] = 120;
        $settings['sc_preview__color'] = 'ffffff';
        $settings['sc_preview__distanceX'] = 188;
        $settings['sc_preview__distanceY'] = 188;
        $settings['sc_preview__distanceZ'] = 180;
        $settings['sc_preview__alpha'] = 0.7;
        $settings['sc_preview__status'] = 'fill';
        $settings['sc_preview__description'] = 0;
        $settings['sc_preview__descriptionSize'] = 14;
        $settings['sc_preview__countByWidth'] = 4;
        $settings['sc_preview__countByHeight'] = 3;
        $settings['sc_preview__scrollingDirection'] = 'top';
        $settings['sc_preview__cornerRadius'] = 15;
        $settings['sc_preview__transitionEffect'] = 'alpha';
        $settings['sc_preview__transitionDuration'] = 2;
        $settings['sc_preview__shadow'] = 0;
        $settings['sc_preview__border'] = 0;
        $settings['sc_preview__borderEclipse'] = 5;
        $settings['sc_preview__borderColor'] = 'ffffff';
        $settings['sc_preview__shadowColor'] = 'ffffff';
        $settings['sc_preview__shadowAlpha'] = 0.7;
        $settings['sc_preview__borderAlpha'] = 0.7;
        $settings['sc_preview__shadowBlur'] = 4;
        $settings['sc_preview__shadowDistance'] = 2;
        $settings['sc_preview__shadowSize'] = 10;
        $settings['sc_preview__shadowAngle'] = 0;
        $settings['sc_preview__borderWidth'] = 2;
        $settings['sc_preview__borderPhotoWidth'] = 2;
        $settings['sc_preview__borderPhotoColor'] = '000000';
        $settings['sc_preview__borderPhotoAlpha'] = 0.7;
        $settings['sc_preview__distanceFromScroller'] = 1;
        $settings['sc_preview__selectTint'] = 0;
        $settings['sc_preview__scrollingSpeed'] = 1;
        $settings['sc_preview__scatter'] = 10;
        $settings['sc_preview__mouseClick'] = 0;
        $settings['sc_preview__font'] = 'Arial';
        $settings['sc_preview__backgroundColor'] = 'ffffff';
        $settings['sc_preview__backgroundPhoto'] = '';
        $settings['sc_preview__isURL'] = 0;
        $settings['sc_preview__target'] = '_self';
        $settings['sc_preview__rotation'] = 25;
        $settings['sc_preview__scaleEffect'] = 'spin';
        $settings['sc_preview__scaleSmall'] = 'fit';
        $settings['sc_preview__scalePhoto'] = 'noScale';
        $settings['sc_preview__reflection'] = 1;
        $settings['sc_preview__reflectionAlpha'] = 0.7;
        $settings['sc_preview__reflectionDistance'] = 8;
        $settings['sc_preview__reflectionGradientColorStart'] = 'ffffff';
        $settings['sc_preview__reflectionGradientColorFinish'] = '000000';
        $settings['sc_preview__scale'] = 'fit';
        $settings['sc_preview__preloaderColor'] = 'ffffff';
        $settings['sc_preview__borderThickness'] = 3;
        $settings['sc_preview__borderColor'] = 'ffffff';
        $settings['sc_preview__borderAlpha'] = 0.5;
        $settings['sc_preview__borderTime'] = 7;
        $settings['sc_preview__captionShow'] = 'allTime';
        $settings['sc_preview__captionPosition'] = 'bottom';
        $settings['sc_preview__captionAlign'] = 'left';
        $settings['sc_preview__captionFont'] = 'Tahoma';
        $settings['sc_preview__captionSize'] = 11;
        $settings['sc_preview__captionBold'] = 0;
        $settings['sc_preview__captionColor'] = 'ffffff';
        $settings['sc_preview__captionColorBack'] = 'ffffff';
        $settings['sc_preview__captionBackgroundColor'] = 'ffffff';
        $settings['sc_preview__captionAlphaBack'] = 0.3;
        $settings['sc_preview__barEnable'] = 0;
        $settings['sc_preview__barColor'] = 'ffffff';
        $settings['sc_preview__barFont'] = 'Tahoma';
        $settings['sc_preview__barSize'] = 11;
        $settings['sc_preview__barBold'] = 0;
        $settings['sc_preview__barColorText'] = 'ffffff';
        $settings['sc_preview__barColorButton'] = 'ffffff';
        $settings['sc_preview__navigationMode'] = 'button';
        $settings['sc_preview__navigationButtonMode'] = 'onMouseOver';
        $settings['sc_preview__navigationButtonColorBorder'] = 'ffffff';
        $settings['sc_preview__navigationButtonColorBack'] = 'ffffff';
        $settings['sc_preview__navigationButtonColorRect'] = 'ffffff';
        $settings['sc_preview__navigationButtonThickness'] = 5;
        $settings['sc_preview__navigationButtonSize'] = 35;
        $settings['sc_preview__navigationButtonAlpha'] = 0.5;
        $settings['sc_preview__titlePosition'] = 'bottom';
        $settings['sc_preview__titleAlign'] = 'left';
        $settings['sc_preview__titleSize'] = 20;
        $settings['sc_preview__titleUseEmbedFont'] = 0;
        $settings['sc_preview__titleFont'] = 'Tahoma';
        $settings['sc_preview__titleColor'] = 'ffffff';
        $settings['sc_preview__titleBold'] = 0;
        $settings['sc_preview__dimmingBackgroundEnable'] = 0;
        $settings['sc_preview__linkageEnable'] = 0;
        $settings['sc_preview__buttonsEnable'] = 0;
        $settings['sc_preview__bordercolor'] = 'ffffff';
        $settings['sc_preview__bordersize'] = 10;
        $settings['sc_preview__borderalpha'] = 0.5;
        $settings['sc_preview__showtitle'] = 0;

        $settings['sc_background__type'] = 'color';
        $settings['sc_background__src'] = '';
        $settings['sc_background__alpha'] = 0.7;
        $settings['sc_background__color'] = 'ffffff';
        $settings['sc_background__backgroundcolor'] = 'ffffff';
        $settings['sc_background__author'] = 'Custom Author';
        $settings['sc_background__fullAuthor'] = 'Custom Author';
        $settings['sc_background__authorSize'] = 15;
        $settings['sc_background__authorColor'] = 'ffffff';
        $settings['sc_background__borderAlpha'] = 0.5;
        $settings['sc_background__borderColor'] = 'ffffff';
        $settings['sc_background__typeback'] = 'background';
        $settings['sc_background__musicPath'] = '';
        $settings['sc_background__copytextsize'] = 12;
        $settings['sc_background__copytextcolor'] = 'ffffff';

        $settings['sc_navigation__enable'] = 0;
        $settings['sc_navigation__indent'] = 20;
        $settings['sc_navigation__home'] = 0;
        $settings['sc_navigation__itemFont'] = 'Arial';
        $settings['sc_navigation__itemSize'] = 11;
        $settings['sc_navigation__itemBold'] = 0;
        $settings['sc_navigation__itemColorLowerText'] = '1C6CC4';
        $settings['sc_navigation__itemColorUpperText'] = '1C6CC4';
        $settings['sc_navigation__itemColorLowerField'] = '1C6CC4';
        $settings['sc_navigation__itemColorUpperField'] = '1C6CC4';
        $settings['sc_navigation__align'] = 'top';
        $settings['sc_navigation__visible'] = 'onHover';
        $settings['sc_navigation__name'] = 'Navigation';
        $settings['sc_navigation__albumIcon'] = 0;
        $settings['sc_navigation__albumIconEnable'] = 0;
        $settings['sc_navigation__position'] = 'stage';
        $settings['sc_navigation__hideAlbumButton'] = 0;
        $settings['sc_navigation__flipping'] = 'button';
        $settings['sc_navigation__buttons'] = 0;
        $settings['sc_navigation__buttonsColor'] = '000000';
        $settings['sc_navigation__colorButton'] = '000000';
        $settings['sc_navigation__colorBackground'] = 'ffffff';
        $settings['sc_navigation__buttonsBackColor'] = 'ffffff';
        $settings['sc_navigation__buttonsAlpha'] = 0.5;
        $settings['sc_navigation__albumtextsize'] = 10;
        $settings['sc_navigation__albumtextcolor'] = 'ffffff';
        $settings['sc_navigation__hidePlayButton'] = 0;
        $settings['sc_navigation__hideNavigateButtons'] = 0;

        $settings['sc_controls__fullscreen'] = 0;
        
        $settings['sc_item__width'] = 150;
        $settings['sc_item__height'] = 100;
        $settings['sc_item__scale'] = 'fit';
        $settings['sc_item__cornerRadius'] = 20;
        $settings['sc_item__borderThickness'] = 4;
        $settings['sc_item__borderColor'] = '000000';
        $settings['sc_item__titlePosition'] = 'followMouse';
        $settings['sc_item__titleAlign'] = 'left';
        $settings['sc_item__titleFont'] = 'Verdana';
        $settings['sc_item__titleSize'] = 13;
        $settings['sc_item__titleFontColor'] = '000000';
        $settings['sc_item__titleBackgroundColor'] = 'ffffff';
        $settings['sc_item__titleBold'] = 0;
        $settings['sc_item__shadowDistance'] = 0;
        $settings['sc_item__shadowSize'] = 10;
        $settings['sc_item__shadowColor'] = '000000';
        $settings['sc_item__shadowAngle'] = 0;
        $settings['sc_item__selectTintEnable'] = 0;
        $settings['sc_item__preloaderEnalbe'] = 0;
        $settings['sc_item__preloaderColor'] = '000000';

        $settings['sc_screen__theme'] = 'black';
        $settings['sc_screen__fog'] = 0;
        $settings['sc_screen__fogWidth'] = 10;
        $settings['sc_screen__mainPreloader'] = 0;
        $settings['sc_screen__navigationsButton'] = 0;
        $settings['sc_screen__previewPreloader'] = 0;
	
        $settings['sc_scroller__distanceFromBorder'] = '30';
        $settings['sc_scroller__position'] = 'left';
        $settings['sc_scroller__numberColumns'] = 2;
        $settings['sc_scroller__visible'] = 0;
        $settings['sc_scroller__scrollBy'] = 'mouse';
        $settings['sc_scroller__speed'] = 0.5;
        $settings['sc_scroller__cornerRadius'] = 10;
        $settings['sc_scroller__lineStyle'] = 'curve';
        $settings['sc_scroller__hideButtons'] = 0;
		$settings['sc_scroller__hidePlayButton'] = 0;
        $settings['sc_scroller__itemDistance'] = 5;
        $settings['sc_scroller__itemIndent'] = 5;
        $settings['sc_scroller__borderWidth'] = 2;
        $settings['sc_scroller__width'] = 450;
        $settings['sc_scroller__itemWidth'] = 100;
        $settings['sc_scroller__height'] = 120;
        $settings['sc_scroller__itemHeight'] = 83;
        $settings['sc_scroller__itemScale'] = 'fit';
        $settings['sc_scroller__itemMotion'] = 0;
        $settings['sc_scroller__itemBorderColor'] = 'ffffff';
        $settings['sc_scroller__itemBorderThickness'] = 10;
        $settings['sc_scroller__itemBorderAlpha'] = 0.5;
        $settings['sc_scroller__itemBorderTime'] = 13;
        $settings['sc_scroller__itemWaveColor'] = 'ffffff';
        $settings['sc_scroller__itemWaveAlpha'] = 0.3;
        $settings['sc_scroller__itemWaveTime'] = 120;
        $settings['sc_scroller__itemTitlePosition'] = 'followMouse';
        $settings['sc_scroller__itemTitleFont'] = 'Tahoma';
        $settings['sc_scroller__itemTitleSize'] = 11;
        $settings['sc_scroller__itemTitleBold'] = 0;
        $settings['sc_scroller__itemTitleColor'] = 'ffffff';
        $settings['sc_scroller__itemTitleColorBack'] = 'ffffff';
        $settings['sc_scroller__itemTitleAlphaBack'] = 0.5;
        $settings['sc_scroller__navigationEnable'] = 0;
        $settings['sc_scroller__navigationColorButton'] = 'ffffff';
        $settings['sc_scroller__navigationColorRect'] = 'ffffff';
        $settings['sc_scroller__borderColor'] = 'ffffff';
        $settings['sc_scroller__align'] = 'top';
        $settings['sc_scroller__enable'] = 0;
        $settings['sc_scroller__size'] = 70;
        $settings['sc_scroller__color'] = 'abcbdf';
        $settings['sc_scroller__alpha'] = '0.5';
        $settings['sc_scroller__direction'] = 'horizontal';
        $settings['sc_scroller__useImagesInItem'] = 0;
        $settings['sc_scroller__showPopupTitle'] = 0;
        $settings['sc_scroller__fogEnable'] = 0;
        $settings['sc_scroller__fogWidth'] = 50;
        $settings['sc_scroller__xItemDistance'] = 50;
        $settings['sc_scroller__yItemDistance'] = 50;
        $settings['sc_scroller__scrollingDirection'] = 'top';
        $settings['sc_scroller__scrollingSpeed'] = 75;
        $settings['sc_scroller__shadow'] = 0;
        $settings['sc_scroller__shadowDistance'] = 0;
        $settings['sc_scroller__shadowSize'] = 0;
        $settings['sc_scroller__shadowColor'] = '000000';
        $settings['sc_scroller__shadowAngle'] = 0;

        $settings['sc_scrollerItem__width'] = 80;
        $settings['sc_scrollerItem__height'] = 40;
        $settings['sc_scrollerItem__cornerRadius'] = 5;
        $settings['sc_scrollerItem__alpha'] = 0.5;
        $settings['sc_scrollerItem__shadow'] = 0;
        $settings['sc_scrollerItem__borderColor'] = 'ffffff';
        $settings['sc_scrollerItem__color'] = 'ffffff';
        $settings['sc_scrollerItem__borderColorOnHover'] = 'ffffff';
        $settings['sc_scrollerItem__borderColorOnClick'] = 'ffffff';
        $settings['sc_scrollerItem__shadowColor'] = 'ffffff';
        $settings['sc_scrollerItem__shadowAlpha'] = 0.7;
        $settings['sc_scrollerItem__shadowBlur'] = 4;
        $settings['sc_scrollerItem__shadowDistance'] = 4;
        $settings['sc_scrollerItem__borderWidth'] = 2;
        $settings['sc_scrollerItem__gradientStart'] = '000000';
        $settings['sc_scrollerItem__gradientEnd'] = 'ffffff';
        $settings['sc_scrollerItem__textColor'] = '000000';
        $settings['sc_scrollerItem__fontSize'] = 13;
        $settings['sc_scrollerItem__forceItemToPreview'] = 0;
        $settings['sc_scrollerItem__shapeIsRect'] = 0;      
		
        $settings['sc_image__imageAsLink'] = 0;
        $settings['sc_image__width'] = 80;
        $settings['sc_image__height'] = 40;
        $settings['sc_image__status'] = 'fit';
        $settings['sc_image__descriptionSize'] = 14;
        $settings['sc_image__description'] = 0;
        $settings['sc_image__stageBlackout'] = 0;
        $settings['sc_image__paginates'] = 0;
        $settings['sc_image__isURL'] = 0;
        $settings['sc_image__imageAsLink'] = 0;
        $settings['sc_image__linkTarget'] = '_blank';
        $settings['sc_image__transitionDuration'] = '1';
        $settings['sc_image__scaleMode'] = 'fit';
        $settings['sc_image__transitionEffect'] = 'alpha';
        $settings['sc_image__cornerRadius'] = 2;
        $settings['sc_image__dimmingBackground'] = 0;
        $settings['sc_image__fullscreen'] = 0;
        $settings['sc_image__buttonsAlpha'] = 1;
        $settings['sc_image__changeByClick'] = 0;
        $settings['sc_image__stopByClick'] = 0;
        $settings['sc_image__shadow'] = 0;
        $settings['sc_image__shadowColor'] = 'ffffff';
        $settings['sc_image__shadowAlpha'] = 0.5;
        $settings['sc_image__shadowBlur'] = 4;
        $settings['sc_image__shadowDistance'] = 4;

        $settings['sc_caption__align'] = 'bottom';
        $settings['sc_caption__enable'] = 0;
        $settings['sc_caption__visible'] = 'onHover';
        $settings['sc_caption__fontSize'] = '14';
        $settings['sc_caption__font'] = 'Tahoma';
        $settings['sc_caption__bold'] = 0;
        $settings['sc_caption__fontColor'] = 'ffffff';
        $settings['sc_caption__textColor'] = 'ffffff';
        $settings['sc_caption__backgroundColor'] = '000000';
        $settings['sc_caption__background'] = 0;
        $settings['sc_caption__backgroundAlpha'] = '0.7';
		
        $settings['sc_main__flipWidth'] = '450';
        $settings['sc_main__flipHeight'] = '380';
        $settings['sc_main__flipAlign'] = '0';
        $settings['sc_main__backgroundColor'] = 'ffffff';
        $settings['sc_main__backgroundImage'] = '';
        $settings['sc_main__backgroundImagePlacement'] = 'fit';
        $settings['sc_main__alwaysOpened'] = '0';
        $settings['sc_main__handOverCorner'] = '0';
        $settings['sc_main__dropShadowEnabled'] = '0';
        $settings['sc_main__dropShadowHideWhenFlipping'] = '0';
        $settings['sc_main__shadowDepth'] = '0';
        $settings['sc_main__flipSound'] = '0';
        $settings['sc_main__navigationBar'] = '0';
        $settings['sc_main__navigationBarPlacement'] = 'top';
        $settings['sc_main__navigationBarTitle'] = '0';
        $settings['sc_main__navigationBarBgAlpha'] = '0.9';
        $settings['sc_main__fitToStageOnFullscreen'] = '0';

        $settings['sc_main__pageAlignContent'] = 'fit';
        $settings['sc_main__pageBackgroundColor'] = 'ffffff';
        $settings['sc_main__pageBackgroundImage'] = '';
        $settings['sc_main__pageFrame'] = '15';
        $settings['sc_main__pageFrameColor'] = 'ffffff';
        $settings['sc_main__pageFrameAlpha'] = '0.5';
        $settings['sc_main__pageAngleProportion'] = 10;
        $settings['sc_main__coverFrame'] = '0';
        $settings['sc_main__coverFrameColor'] = 'ffffff';
        $settings['sc_main__coverFrameAlpha'] = '0.5';

        $settings['sc_menu__albumListBtn'] = '0';
        $settings['sc_menu__exactFitBtn'] = '0';
        $settings['sc_menu__zoomInBtn'] = '0';
        $settings['sc_menu__zoomOutBtn'] = '0';
        $settings['sc_menu__fitToStageBtn'] = '0';
        $settings['sc_menu__firstBtn'] = '0';
        $settings['sc_menu__previousBtn'] = '0';
        $settings['sc_menu__navigationString'] = '0';
        $settings['sc_menu__nextBtn'] = '0';
        $settings['sc_menu__lastBtn'] = '0';
        $settings['sc_menu__soundOnBtn'] = '0';
        $settings['sc_menu__sounOffBtn'] = '0';
        $settings['sc_menu__printBtn'] = '0';
        $settings['sc_menu__downloadBtn'] = '0';
        $settings['sc_menu__fullscreenBtn'] = '0';
        $settings['sc_menu__exitFullscreenBtn'] = '0';

        $settings['sc_main_screen__width'] = 420;
        $settings['sc_main_screen__height'] = 360;
        $settings['sc_main_screen__shadow'] = 0;
        $settings['sc_main_screen__color'] = 'ffffff';
        $settings['sc_main_screen__time_change_screen'] = 0.5;
        $settings['sc_main_screen__small_width'] = 100;
        $settings['sc_main_screen__small_height'] = 120;
        $settings['sc_main_screen__small_color'] = 'ffffff';
        $settings['sc_main_screen__small_x'] = 5;
        $settings['sc_main_screen__small_y'] = 6;
        $settings['sc_main_screen__time_show_small'] = 0.5;
        $settings['sc_main_screen__resizable_small'] = 'fit';
        $settings['sc_main_screen__blackout_small'] = 0;
        $settings['sc_main_screen__blackout_alpha'] = 0.5;
        $settings['sc_main_screen__frame_small_size'] = 2;
        $settings['sc_main_screen__frame_small_color'] = 'ffffff';
        $settings['sc_main_screen__frame_small_alpha'] = 0.5;
        $settings['sc_main_screen__frame_big_size'] = 2;
        $settings['sc_main_screen__frame_big_color'] = 'ffffff';
        $settings['sc_main_screen__frame_big_alpha'] = 0.5;
        $settings['sc_main_screen__color_arrow'] = 'ffffff';
        $settings['sc_main_screen__arrow_alpha'] = 0.5;
        $settings['sc_main_screen__target'] = '_self';

        $settings['sc_big_foto__color'] = 'ffffff';
        $settings['sc_big_foto__background'] = '';
        $settings['sc_big_foto__text_color'] = 'ffffff';
        $settings['sc_big_foto__text_background_color'] = '000000';
        $settings['sc_big_foto__text_background_height'] = 40;
        $settings['sc_big_foto__text_background_alpha'] = 0.5;
        $settings['sc_big_foto__font'] = 'Tahoma';
        $settings['sc_big_foto__font_size'] = 24;
        $settings['sc_big_foto__font_bold'] = 0;
        $settings['sc_big_foto__time_open_photo'] = 0.1;
        $settings['sc_big_foto__time_change_alpha_photo'] = 0.1;
        $settings['sc_big_foto__time_change_photo'] = 1;
        $settings['sc_big_foto__effect_change_photo'] = 'linear';
        $settings['sc_big_foto__time_show_text'] = 1;
        $settings['sc_big_foto__time_show_arrow'] = 1;
        $settings['sc_big_foto__color_arrow'] = '000000';
        $settings['sc_big_foto__arrow_alpha'] = 0.5;
        $settings['sc_big_foto__scalePhoto'] = 'fit';

        $settings['sc_slideshow_bar__enable'] = 0;
        $settings['sc_slideshow_bar__width'] = 200;
        $settings['sc_slideshow_bar__height'] = 30;
        $settings['sc_slideshow_bar__x'] = 50;
        $settings['sc_slideshow_bar__y'] = 10;
        $settings['sc_slideshow_bar__color'] = '000000';
        $settings['sc_slideshow_bar__color2'] = '000000';
        $settings['sc_slideshow_bar__alpha'] = 0.5;
        $settings['sc_slideshow_bar__eclipse'] = 10;
        $settings['sc_slideshow_bar__select_albums'] = 0;
        $settings['sc_slideshow_bar__arrow_left'] = 0;
        $settings['sc_slideshow_bar__play_slideshow'] = 0;
        $settings['sc_slideshow_bar__arrow_right'] = 0;
        $settings['sc_slideshow_bar__full_screen'] = 0;
        $settings['sc_slideshow_bar__buttons_alpha'] = 0.5;
        $settings['sc_slideshow_bar__buttons_space'] = 35;
        $settings['sc_slideshow_bar__select_albums_color'] = '000000';
        $settings['sc_slideshow_bar__select_albums_eclipse'] = 10;
        $settings['sc_slideshow_bar__select_albums_alpha'] = 0.5;
        $settings['sc_slideshow_bar__select_albums_font'] = 'Tahoma';
        $settings['sc_slideshow_bar__select_albums_font_size'] = 14;
        $settings['sc_slideshow_bar__select_albums_font_color'] = 'ffffff';
        $settings['sc_slideshow_bar__select_albums_font_color_over'] ='ffffff';
        $settings['sc_slideshow_bar__select_albums_space_text'] = 10;
        $settings['sc_slideshow_bar__select_albums_time_show'] = 1;
        $settings['sc_slideshow_bar__select_albums_time_hide'] = 1;

        $settings['sc_customSkinItem__backgroundActiveColor'] = 'ffffff';
        $settings['sc_customSkinItem__backgroundPassiveColor'] = 'ffffff';
        $settings['sc_customSkinItem__backgroundActiveAlpha'] = 0.5;
        $settings['sc_customSkinItem__backgroundPassiveAlpha'] = 0.5;
        $settings['sc_customSkinItem__borderActiveColor'] = 'ffffff';
        $settings['sc_customSkinItem__borderPassiveColor'] = '000000';
        $settings['sc_customSkinItem__borderActiveAlpha'] = 0.5;
        $settings['sc_customSkinItem__borderPassiveAlpha'] = 0.5;
        $settings['sc_customSkinItem__mainPassiveAlpha'] = 0.5;
        $settings['sc_customSkinItem__mainActiveAlpha'] = 0.5;
        $settings['sc_customSkinItem__mainActiveColor'] = 'ffffff';
        $settings['sc_customSkinItem__mainPassiveColor'] = 'ffffff';
        $settings['sc_customSkinItem__shadowColor'] = 'ffffff';
        $settings['sc_customSkinItem__shadowAlpha'] = 0.7;
        $settings['sc_customSkinItem__shadowBlur'] = 4;
        $settings['sc_customSkinItem__shadowDistance'] = 4; 
		$settings['sc_customSkinItem__shadow'] = 1; 
		
        $settings['sc_flickr__searchBy'] = 'keyword';
        $settings['sc_flickr__userID'] = '';
        $settings['sc_flickr__keyword'] = '';
	$settings['sc_flickr__max'] = '20';
	$settings['sc_flickr__albumID'] = '';
		
        $settings['sc_picasa__searchBy'] = 'keyword';
        $settings['sc_picasa__userName'] = '';
        $settings['sc_picasa__keyword'] = '';
        $settings['sc_picasa__max'] = '20';
        $settings['sc_picasa__albumID'] = '';
        $settings['sc_picasa__albumName'] = '';
        
	$settings['sc_photobucket__searchString'] = '';
        $settings['sc_photobucket__max'] = '20';
		
    return $settings;
}
<?php

/*
 * This file is for filters
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Returns Help text on 1 Flash Gallery plugin pages
 * @param string $contextual_help
 * @param string $screen_id
 * @param string $screen
 * @return string 
 */
function fgallery_plugin_help($contextual_help, $screen_id, $screen) {
    if ($screen_id == 'toplevel_page_fgallery' || $screen_id == '1-flash-gallery_page_fgallery_add'
            || $screen_id == '1-flash-gallery_page_fgallery_add' || $screen_id == '1-flash-gallery_page_fgallery_images'
            || $screen_id == '1-flash-gallery_page_fgallery_upload') {
            $contextual_help = '<p><a href="http://1plugin.com/faq" target="_blank">'.__('FAQ').'</a></p>';
    }
    return $contextual_help;
}

/**
 * Filter for setting the number of elements on the screen
 * @param type $screen
 * @return string 
 */
function fgallery_screen_options($screen) {
    if ( is_string($screen) )
		$screen = convert_to_screen($screen);
	$option = str_replace( '-', '_', "{$screen->id}_per_page" );

	$per_page = (int) get_option( $option , 25);
        if ( $screen->id ==  '1-flash-gallery_page_fgallery_images') {
            $per_page_label = _x( 'Images', 'images per page (screen options)' );
        } else {
            $per_page_label = _x( 'Galleries', 'galleries per page (screen options)' );
        }

	$return = "<div class='screen-options'>\n";
	if ( !empty($per_page_label) )
	$return .= "<input type='text' class='screen-per-page' name='wp_screen_options[value]' id='$option' maxlength='3' value='$per_page' /> 
            <label for='$option'>$per_page_label</label>\n";
	$return .= "<input type='submit' class='button' value='" . esc_attr__('Apply') . "' />";
	$return .= "<input type='hidden' name='wp_screen_options[option]' value='" . esc_attr($option) . "' />";
	$return .= "</div>\n";
    return $return;
}

/**
 * Filter to set number of elements on the screen
 * @param string $current
 * @param string $screen
 * @return string 
 */
function fgallery_screen_settings($current, $screen){
    if ( $screen->id ==  '1-flash-gallery_page_fgallery_images' && !isset($_GET['action'])){
            $current = '<h5>'._x('Show on screen', 'Screen Options').'</h5>';
            $current .= fgallery_screen_options($screen);
            $current .= '<div>'. wp_nonce_field( 'screen-options-nonce', 'screenoptionnonce', true ).'</div>';
    } elseif ($screen->id == 'toplevel_page_fgallery' && !isset($_GET['action'])){
            $current = '<h5>'._x('Show on screen', 'Screen Options').'</h5>';
            $current .= fgallery_screen_options($screen);
            $current .= '<div>'. wp_nonce_field( 'screen-options-nonce', 'screenoptionnonce', true ).'</div>';
    }
    return $current;
}

/**
 * Filter for replacing gallery shortcode with flash movie itself
 * @param type $atts
 * @param type $content
 * @return string 
 */
function fgallery_shortcode_handler($atts, $content = null){
    // set default params if not present
    extract(shortcode_atts( array(
            'id' => 0,
            'w' => 450,
            'h' => 385,
            'bg' => 'ffffff',
            't' => 0,	
            'align' => '',
            'thumb' => '',
            'title' => '',
            'margin' => '0px',
            'ex' => 1,
            'conf' => 0,
    ), $atts ));
    // choose insertion type
    if ($t < 0 || $t > 2) {
            $t = 0;
    }
    if (!is_numeric($ex) || $ex > 2 || $ex < 0 )
        $ex = 1;
    // check if the gallery exist
    if ($id > 0) {
        $album = fgallery_get_album($id);
        if (empty($album)) {
           return '[fgallery 404 Not found]';
        }
    } else {
        return '[fgallery 404 Not found]';
    }
    // different output for different insert type
    switch ($t) {
        case '0' : 
                $rand = uniqid();
                $path = fgallery_search_flash_path($album);
                // if gallery should be aligned
                if (isset($align)) {
                    switch ($align) {
                        case 'left' : case 'right' :
                                $align_text = 'float:'.$align;
                        break;
                        case 'center' :
                            $margin = $margin.' auto';
                        default:
                                $align_text = '';
                        break;
                    }
                }
                if (!$conf){
                    $flashcontent = 'flashcontent_'.$id.$rand;
                } else {
                    $flashcontent = 'flashcontent';
                }
                $gallery_snippet = '<div style="width:'.$w.'px;margin:'.$margin.';'.$align_text.'" class="fgallery_'.$id.'">
                  <script type="text/javascript">
                  if (swfobject.getFlashPlayerVersion().release) {
                        var flashvars = {settings: "'.fgallery_get_config_url($id).'",';
                if ($album['gall_type'] == 13){
                    $gallery_snippet .= 'publications: "'.fgallery_get_publ_url($id).'",';
      //            $gallery_snippet .= 'style: "http://localhost/wp/wp-content/plugins/1-flash-gallery/swf/style.css",';
                }
                    $gallery_snippet .= 'images : "'.fgallery_get_images_url($id).'"};
                        var params = {bgcolor: "#'.$bg.'", allowFullScreen: "true", wmode: "transparent"};
                        swfobject.embedSWF("'.$path.'", "'.$flashcontent.'", "'.$w.'", "'.$h.'", "10.0.0",false, flashvars, params);
                  } else {
                    jQuery(document).ready(function(){
                        jQuery("#myGallery_'.$id.$rand.'").photoGallery({gall_id : "'.$id.'"});
                    });
                  }
                  </script>
                 <div id="'.$flashcontent.'">
                        <div id="myGallery_'.$id.$rand.'" class="configurator" style="height:'.$h.'px ;width: '.$w.';"></div>
                 </div>';
                 $gallery_snippet .= '<div class="flash_text"><p class="image_text"></p></div>';
                 if (get_option('1_flash_gallery_'.fgallery_get_flash_type($album)) == '') {
                        $gallery_snippet .= '<div class="fgallery_message">'.fgallery_slideshow($album).'</div>';
                 }
        break;
        case '1' : 
                if ($title == '') {
                   $insert_text = $album['gall_name'];
                } else {
                   $insert_text = $title;
                }
                $gallery_snippet = '<a href="'.fgallery_view_gallery_url($id, $w, $h, $bg).'"
                                    class="thickbox" title="'.$album['gall_name'].'">'.$insert_text.'</a>';
                if ($ex == 1)                    
                    $gallery_snippet .= '<p>'.fgallery_get_album_attributes($album, false).'</p>';
        break;
        case '2' :
                $gallery_snippet = '<a href="'.fgallery_view_gallery_url($id, $w, $h, $bg).'"
                                    class="thickbox" title="'.$album['gall_name'].'">'.fgallery_get_album_cover($album, $thumb).'</a>';
                switch ($ex) {
                    case 0:
                        $gallery_snippet .= '';
                    break;
                    case 1:
                        $gallery_snippet .= '<p>'.$album['gall_name'].'</p><p>'.fgallery_get_album_attributes($album, false).'</p>';
                    break;
                    case 2:    
                        $gallery_snippet .= '<p>'.$album['gall_name'].'</p>';
                    break;
                    default:
                        $gallery_snippet .= '<p>'.$album['gall_name'].'</p><p>'.fgallery_get_album_attributes($album, false).'</p>';
                    break;
                }             
                    
        break;
    }

   return $gallery_snippet;
}

/**
 * Filter to add the button (for inserting the gallery into post) to post edit page
 * @param string $buttons
 * @return string 
 */
function fgallery_add_button($buttons) {
     $fgallery_button = " <a href='" . esc_url( fgallery_get_add_album_url() ) . "' id='insert_gallery' class='thickbox' 
                            title='".__('Insert gallery into post', 'fgallery')."'>
        <img src='" . esc_url( fgallery_get_insert_button_url( ) ) . "' alt='".__('Insert gallery into post', 'fgallery')."' /></a>";
    $buttons .= $fgallery_button;
  return $buttons;
}

/**
 * Returns if there is an update for 1 Flash Gallery available
 * 
 * @global type $api_url
 * @global type $plugin_slug
 * @param type $checked_data
 * @return type array
 */
function check_for_fgallery_update($checked_data) {
	if (empty($checked_data->checked))
		return $checked_data;
	
	$request_args = array(
		'slug' => FGALLERY_SLUG,
		'version' => $checked_data->checked[FGALLERY_SLUG],
	);
	
	$request_string = fgallery_prepare_request('basic_check', $request_args);
	
	// Start checking for an update
	$raw_response = wp_remote_post(FGALLERY_API_URL, $request_string);
	
	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = unserialize($raw_response['body']);
	
	if (is_object($response) && !empty($response)) // Feed the update data into WP updater
		$checked_data->response[FGALLERY_SLUG] = $response;
	
	return $checked_data;
}

/**
 * Handles 1 Flash Gallery API calls
 * 
 * @param type $def
 * @param type $action
 * @param type $args
 * @return WP_Error 
 */
function fgallery_api_call($def, $action, $args) {
	if ($args->slug != FGALLERY_SLUG)
		return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[FGALLERY_SLUG];
	$args->version = $current_version;
	
	$request_string = fgallery_prepare_request($action, $args);
	
	$request = wp_remote_post(FGALLERY_API_URL, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}

/**
 * Prepares the request for API call
 * 
 * @global type $wp_version
 * @param type $action
 * @param type $args
 * @return type 
 */
function fgallery_prepare_request($action, $args) {
	global $wp_version;
	
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
			'api-key' => md5(get_bloginfo('url'))
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);	
}
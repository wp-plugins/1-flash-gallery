<?php
/**
 * 
 * Plugin Name: 1 Flash Gallery
 * Plugin URI: http://1plugin.com/
 * Description: 1 Flash Gallery is a Photo Gallery with slideshow function, many skins and powerfull admin to manage your image galleries without any program skills
 * Version: 1.9.0
 * Author: 1plugin.com
 * Author URI: http://1plugin.com/
 * 
 **/

// DENY direct access to the file 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('You are not allowed to call this page directly.'); 
}

// basic defines here to avoid directories nesting
define('FGALLERY_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)));
define('FGALLERY_ABSPATH', ABSPATH.'/wp-content/plugins/'.basename(dirname(__FILE__)));
// check if PHP 5 is available
if(version_compare(PHP_VERSION, '5.0.0', '<')){
    define('FGALLERY_PHP4_MODE', 1);
    require_once 'includes/simplexml.class.php';
    require_once 'includes/thumbs/Thumbnail.inc.php';   
    
    function simplexml_load_file($file){
      $sx = new SimpleXML;
      return $sx->xml_load_file($file);
    }

    function simplexml_load_string($string) {
      $sx = new SimpleXML;
      return $sx->xml_load_string($string);
    }   
} else {
    define('FGALLERY_PHP4_MODE', 0);
    require_once 'includes/thumbs/ThumbLib.inc.php';
}

include 'includes/lib/define.php';
include 'includes/lib/install.php';
include 'includes/lib/init.php';
include 'includes/lib/helper.php';
include 'includes/lib/gallery.php';
include 'includes/lib/image.php';
include 'includes/lib/settings.php';
include 'includes/lib/templates.php';
include 'includes/lib/widget.php';
include 'includes/lib/filters.php';
include 'includes/lib/xml.php';
include 'includes/ajax/actions.php';
include 'includes/ajax/upload.php';
include 'includes/ajax/config.php';
include 'includes/ajax/images.php';
include 'includes/render/configurator.php';
include 'includes/render/page_elements.php';
include 'includes/render/pages.php';
include 'includes/render/view.php';
include 'includes/render/urls.php';


$sections_path = pathinfo(__FILE__, PATHINFO_DIRNAME).'/sections';
$sections_dir = opendir ($sections_path);

while ($section_dir = readdir ($sections_dir)){
    if ($section_dir != '.' && $section_dir != '..'){
        require_once($sections_path.'/'.$section_dir.'/section.php');
    }
}
  
closedir ($sections_dir);
  

//registering install and uninstall hooks
register_activation_hook(__FILE__,'fgallery_install');
if (function_exists('register_update_hook')) {
    register_update_hook(__FILE__,'fgallery_update');
}
register_uninstall_hook(__FILE__,'fgallery_uninstall');

// register localizations
$plugin_dir = basename(dirname(__FILE__)).'/languages/';
load_plugin_textdomain( 'fgallery', null, $plugin_dir );

/*
 * List of actions
 */
add_action('init', 'fgallery_init');

global $wp_version;
if (version_compare($wp_version, '3.1', '>=')) {
    add_action('admin_bar_menu', 'fgallery_add_items', 100);
}
/*
 * Ajax callbacks
 */
add_action('wp_ajax_fgallery_save_album_image', 'fgallery_save_album_image');
add_action('wp_ajax_fgallery_sort_album_images', 'fgallery_sort_album_images');
add_action('wp_ajax_fgallery_remove_album_image', 'fgallery_remove_album_image');
add_action('wp_ajax_fgallery_set_album_cover', 'fgallery_set_album_cover');
add_action('wp_ajax_fgallery_delete_image', 'fgallery_delete_image_ajax');
add_action('wp_ajax_fgallery_folder_addimage', 'fgallery_folder_addimage');
add_action('wp_ajax_fgallery_delete_gallery', 'fgallery_delete_gallery');
add_action('wp_ajax_fgallery_sort_galleries', 'fgallery_sort_galleries');
add_action('wp_ajax_fgallery_rotate_image', 'fgallery_rotate_image');
add_action('wp_ajax_fgallery_add_preview', 'fgallery_add_preview');
add_action('wp_ajax_fgallery_add_preview_form', 'fgallery_add_preview_form');
add_action('wp_ajax_fgallery_get_image_text', 'fgallery_get_image_text');
add_action('wp_ajax_nopriv_fgallery_get_image_text', 'fgallery_get_image_text');
add_action('wp_ajax_fgallery_massedit','fgallery_massedit');
add_action('wp_ajax_fgallery_add_folder','fgallery_add_folder');
add_action('wp_ajax_fgallery_add_image','fgallery_add_image');
add_action('wp_ajax_fgallery_addimages','fgallery_addimages');
add_action('wp_ajax_fgallery_addgalleries','fgallery_addgalleries');
add_action('wp_ajax_fgallery_save_template','fgallery_save_template');
add_action('wp_ajax_fgallery_load_template','fgallery_load_template');
add_action('wp_ajax_fgallery_delete_template','fgallery_delete_template');
add_action('wp_ajax_fgallery_insert_gallery','fgallery_insert_gallery');
add_action('wp_ajax_fgallery_post_contact_us','fgallery_post_contact_us');
add_action('wp_ajax_fgallery_print_captcha','fgallery_print_captcha');
/*
 * Ajax for creating response without template
 */
add_action('wp_ajax_fgallery_show_image','fgallery_show_image');
add_action('wp_ajax_nopriv_fgallery_show_image','fgallery_show_image');
add_action('wp_ajax_fgallery_config','fgallery_config');
add_action('wp_ajax_nopriv_fgallery_config','fgallery_config');
add_action('wp_ajax_fgallery_images','fgallery_images');
add_action('wp_ajax_nopriv_fgallery_images','fgallery_images');
add_action('wp_ajax_fgallery_book_images','fgallery_book_images');
add_action('wp_ajax_nopriv_fgallery_book_images','fgallery_book_images');
add_action('wp_ajax_fgallery_publ','fgallery_publ');
add_action('wp_ajax_nopriv_fgallery_publ','fgallery_publ');
add_action('wp_ajax_fgallery_folder_form','fgallery_folder_form');
add_action('wp_ajax_fgallery_addimages_page','fgallery_addimages_page');
add_action('wp_ajax_fgallery_addgalleries_page','fgallery_addgalleries_page');
add_action('wp_ajax_fgallery_save_template_page','fgallery_save_template_page');
add_action('wp_ajax_fgallery_load_template_page','fgallery_load_template_page');
add_action('wp_ajax_fgallery_insert_page','fgallery_insert_page');
add_action('wp_ajax_fgallery_view_gallery','fgallery_view_gallery');
add_action('wp_ajax_nopriv_fgallery_view_gallery','fgallery_view_gallery');


/*
 * Ajax Upload
 */
add_action('wp_ajax_fgallery_scandir_upload','fgallery_scandir_upload');
add_action('wp_ajax_fgallery_local_upload','fgallery_local_upload');
add_action('wp_ajax_fgallery_uploadify_upload','fgallery_uploadify_upload');
add_action('wp_ajax_nopriv_fgallery_uploadify_upload','fgallery_uploadify_upload');
add_action('wp_ajax_fgallery_url_upload','fgallery_url_upload');
add_action('wp_ajax_fgallery_wpmedia_upload','fgallery_wpmedia_upload');
add_action('wp_ajax_fgallery_ftp_upload','fgallery_ftp_upload');
add_action('wp_ajax_fgallery_zip_upload','fgallery_zip_upload');
add_action('wp_ajax_fgallery_nextgen_upload','fgallery_nextgen_upload');
/*
 * Filters
 */
add_filter('screen_settings', 'fgallery_screen_settings', 10, 2);
add_filter('contextual_help', 'fgallery_plugin_help', 10, 3);
add_filter('media_buttons_context','fgallery_add_button',9);
add_shortcode('fgallery', 'fgallery_shortcode_handler');
// Take over the update check
add_filter('site_transient_update_plugins', 'check_for_fgallery_update');
// Take over the Plugin info screen
add_filter('plugins_api', 'fgallery_api_call', 10, 3);
/*
 * Widget
 */
add_action('widgets_init', create_function('', 'return register_widget("FgalleryWidget");'));
<?php

/**
 * 
 * Provides definitions for plugin constants
 * 
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

$siteurl = get_option('siteurl');
$extra_dir = str_replace($_SERVER['HTTP_HOST'],'',$siteurl);
$extra_dir = str_replace('http://www.','',$extra_dir);
$extra_dir = str_replace('http://','',$extra_dir);

//define table names and constants
define('FGALLERY_VERSION','1.7.0');
define('EXTRA_DIR',$extra_dir.'/');
define('ALBUMS_TABLE', $wpdb->prefix . "fgallery_albums");
define('IMAGES_TABLE', $wpdb->prefix . "fgallery_images");
define('IMAGES_TO_ALBUMS_TABLE', $wpdb->prefix . "fgallery_album_images");
define('ALBUMS_SETTINGS_TABLE', $wpdb->prefix . "fgallery_albums_settings");
define('TEMPLATES_TABLE', $wpdb->prefix . "fgallery_templates");
define('FGALLERY_DIR', ABSPATH . 'wp-content/uploads/fgallery');
define('FGALLERY_SHOW_IMAGE_ACTION',admin_url('admin-ajax.php?action=fgallery_show_image'));
define('COVER_PATH', FGALLERY_SHOW_IMAGE_ACTION.'&width=200&filename='.EXTRA_DIR);
define('THUMB_PATH', FGALLERY_SHOW_IMAGE_ACTION.'&width=600&filename='.EXTRA_DIR);
// if optimization of preview is enabled
if (get_option('1_flash_gallery_preview_opt',0) == 1){
    define('PREVIEW_PATH', FGALLERY_SHOW_IMAGE_ACTION.'&width=300&filename='.EXTRA_DIR);    
}
define('MUSIC_PATH','musicPath');

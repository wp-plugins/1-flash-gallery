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
global $wpdb;
//define table names and constants
define('FGALLERY_VERSION','1.9.0');
define('FGALLERY_SLUG','1-flash-gallery/fgallery.php');
define('FGALLERY_API_URL','http://1plugin.com/updates/plugin_update.php');
define('EXTRA_DIR',$extra_dir.'/');
define('ALBUMS_TABLE', $wpdb->prefix . "fgallery_albums");
define('IMAGES_TABLE', $wpdb->prefix . "fgallery_images");
define('IMAGES_TO_ALBUMS_TABLE', $wpdb->prefix . "fgallery_album_images");
define('ALBUMS_SETTINGS_TABLE', $wpdb->prefix . "fgallery_albums_settings");
define('TEMPLATES_TABLE', $wpdb->prefix . "fgallery_templates");
define('FGALLERY_DIR', ABSPATH . 'wp-content/uploads/fgallery');
define('FGALLERY_SHOW_IMAGE_ACTION',admin_url('admin-ajax.php?action=fgallery_show_image'));
define('MUSIC_PATH','musicPath');

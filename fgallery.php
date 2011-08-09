<?php
/**
 * 
 * Plugin Name: 1 Flash Gallery
 * Plugin URI: http://1plugin.com/
 * Description: 1 Flash Gallery is a Photo Gallery with slideshow function, many skins and powerfull admin to manage your image galleries without any program skills
 * Version: 1.5.6
 * Author: 1plugin.com
 * Author URI: http://1plugin.com/
 * 
 **/

// DENY direct access to the file 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('You are not allowed to call this page directly.'); 
}

if(version_compare(PHP_VERSION, '5.0.0', '<')){
    define('FGALLERY_PHP4_MODE', 1);
    require_once "includes/simplexml.class.php";

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
}

global $wpdb;

$siteurl = get_option('siteurl');
$extra_dir = str_replace($_SERVER['HTTP_HOST'],'',$siteurl);
$extra_dir = str_replace('http://www.','',$extra_dir);
$extra_dir = str_replace('http://','',$extra_dir);

//define table names and constants
define('FGALLERY_VERSION','1.5.5');
define('EXTRA_DIR',$extra_dir.'/');
define('ALBUMS_TABLE', $wpdb->prefix . "fgallery_albums");
define('IMAGES_TABLE', $wpdb->prefix . "fgallery_images");
define('IMAGES_TO_ALBUMS_TABLE', $wpdb->prefix . "fgallery_album_images");
define('ALBUMS_SETTINGS_TABLE', $wpdb->prefix . "fgallery_albums_settings");
define('TEMPLATES_TABLE', $wpdb->prefix . "fgallery_templates");
define('FGALLERY_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)));
define('FGALLERY_ABSPATH', ABSPATH.'/wp-content/plugins/'.basename(dirname(__FILE__)));
define('FGALLERY_DIR', ABSPATH . 'wp-content/uploads/fgallery');
if (FGALLERY_PHP4_MODE) {
    define('COVER_PATH',WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image.php?width=200&amp;filename='.EXTRA_DIR);
    if (get_option('1_flash_gallery_preview_opt',0) == 1){
        define('PREVIEW_PATH',WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image.php?width=300&amp;filename='.EXTRA_DIR);
    }
    define('THUMB_PATH',WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image.php?width=600&amp;filename='.EXTRA_DIR);
} else {
    define('COVER_PATH',WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image_5.php?width=200&amp;filename='.EXTRA_DIR);
    if (get_option('1_flash_gallery_preview_opt',0) == 1){
        define('PREVIEW_PATH',WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image_5.php?width=300&amp;filename='.EXTRA_DIR);
    }
    define('THUMB_PATH',WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image_5.php?width=600&amp;filename='.EXTRA_DIR);
}
define('MUSIC_PATH','musicPath');
define('FGALLERY_APP_ID','101920689894773');
define('FGALLERY_FB_PATH','http://test.1plugin.com/facebook/index.php');


//registering install and uninstall hooks
register_activation_hook(__FILE__,'fgallery_install');
if (function_exists('register_update_hook')) {
    register_update_hook(__FILE__,'fgallery_update');
}
register_uninstall_hook(__FILE__,'fgallery_uninstall');

//localizations
$plugin_dir = basename(dirname(__FILE__)).'/languages/';
load_plugin_textdomain( 'fgallery', null, $plugin_dir );

// installation script
function fgallery_install() {
   global $wpdb;
   global $wp_filesystem;
   //add option for screen options (number of elements per page)
   add_option('1_flash_gallery_page_fgallery_images_per_page', '25', '', 'no');
   add_option('toplevel_page_fgallery_per_page', '25', '', 'no');
   add_option('1_flash_gallery_watermark_enabled','0','','no');
   add_option('1_flash_gallery_watermark_path','','','no');
   add_option('1_flash_gallery_watermark_place','','','no');
   add_option('fgallery_db_version', '0','',no);

    if($wpdb->get_var("SHOW TABLES LIKE '" . ALBUMS_TABLE . "'") != ALBUMS_TABLE) {
	add_option("fgallery_db_version", FGALLERY_VERSION);
	// create albums table
	$create_albums = "CREATE TABLE " . ALBUMS_TABLE . " (
                            `gall_id` int(11) NOT NULL auto_increment,
                            `gall_name` varchar(255) collate utf8_unicode_ci NOT NULL,
                            `gall_description` text collate utf8_unicode_ci NULL default NULL,
                            `gall_cover` int(11) default '0',
                            `gall_createddate` datetime NOT NULL,
                            `gall_createdby` int(11) NOT NULL,
                            `gall_published` tinyint(1) default 1,
                            `gall_width` int(11) default '450',
                            `gall_height` int(11) default '385',
                            `gall_bgcolor` varchar(6) default 'ffffff',
                            `gall_type` smallint(3) default '3',
                            `gall_order` int(11) NOT NULL,
                            PRIMARY KEY  (`gall_id`)
			);" ;      
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($create_albums);
    }

    if($wpdb->get_var("SHOW TABLES LIKE '" . IMAGES_TABLE . "'") != IMAGES_TABLE) {
	// create images table
	$create_images = "CREATE TABLE " . IMAGES_TABLE . " (
                              `img_id` int(11) NOT NULL auto_increment,
                              `img_caption` varchar(255) collate utf8_unicode_ci NULL,
                              `img_description` text collate utf8_unicode_ci NULL,
                              `img_date` datetime NOT NULL,
                              `img_type` varchar(50) collate utf8_unicode_ci default '',
                              `img_size` int(11) default '0',
                              `img_path` varchar(255) collate utf8_unicode_ci default '',
                              `img_vs_folder` smallint(5) default '0',
                              `img_parent` int(11) default '0',
                              PRIMARY KEY  (`img_id`)
			)";       
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($create_images);
    } 
	
    if($wpdb->get_var("SHOW TABLES LIKE '" . IMAGES_TO_ALBUMS_TABLE . "'") != IMAGES_TO_ALBUMS_TABLE) {
	// create table for relation between images and albums
	$create_albums_images = "CREATE TABLE " . IMAGES_TO_ALBUMS_TABLE . " (
                                  `img_id` int(11) NOT NULL,
                                  `gall_id` int(11) NOT NULL,
                                  `gall_folder` int(11) default 0,
                                  `img_order` smallint(5) default 0,
                                  `img_url` varchar(255) collate utf8_unicode_ci NULL,
                                  `img_extra` text collate utf8_unicode_ci NULL,
                                  PRIMARY KEY  (`img_id`, `gall_id`)
                                )";       
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($create_albums_images);
    } 
    
    $version = get_option('fgallery_db_version', 0);
    
    if (version_compare($version, '1.0.7', '<')) {
            $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_url` VARCHAR( 255 ) collate utf8_unicode_ci NULL AFTER `img_order` ;");
    }
    
    if (version_compare($version, '1.2.2', '<')) {
       $array = array('acosta','airion','arai','pax','pazin','postma','pageflip','nilus');
       foreach ($array as $key=>$value) {
               $temp = get_option('1_flash_gallery_'.$value);
               if ($temp) {
                    $option = substr($temp, 1, 9);
                    update_option('1_flash_gallery_'.$value, $option);
               }
       }	   
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `gall_folder` `gall_folder` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `img_order` `img_order` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_parent` `img_parent` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_vs_folder` `img_vs_folder` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_size` `img_size` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_type` `img_type` varchar(50) collate utf8_unicode_ci default '' ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_path` `img_path` varchar(50) collate utf8_unicode_ci default '' ;");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_cover` `gall_cover` int(11) default 0 ;");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_description` `gall_description` text collate utf8_unicode_ci ;");
    }

    if($wpdb->get_var("SHOW TABLES LIKE '" . ALBUMS_SETTINGS_TABLE . "'") != ALBUMS_SETTINGS_TABLE) {
	// create table for albums settings
	$albums_settings = "CREATE TABLE " . ALBUMS_SETTINGS_TABLE . " (
                              `gall_id` int(11) NOT NULL,
                              `value` text NOT NULL,
                              PRIMARY KEY  (`gall_id`)
			)";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($albums_settings);
    }
    
    if (version_compare($version, '1.3.1', '<')) {
        if($wpdb->get_var("SHOW TABLES LIKE '" . TEMPLATES_TABLE . "'") != TEMPLATES_TABLE) {
            // create table for albums settings
            $templates = "CREATE TABLE " . TEMPLATES_TABLE . " (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `gall_type` smallint(5) NOT NULL,
                              `gall_settings` text NOT NULL,
                              `created` date NOT NULL,
                              `templ_title` varchar(255) collate utf8_unicode_ci NOT NULL,
                              `templ_description` text,
                              PRIMARY KEY (`id`)
                        )";
            require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
            dbDelta($templates);
        }
    }
    if (version_compare($version, '1.5.1', '<')) {
        add_option('1_flash_gallery_preview_opt','0','','no');
    }
    
    if (version_compare($version, '1.5.6', '<')) {
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_extra` text collate utf8_unicode_ci NULL;");
    }
	
    update_option("fgallery_db_version", FGALLERY_VERSION);

    // try to make gallery dir if not exists
    if (!is_dir(FGALLERY_DIR)) {
	WP_Filesystem();
        @ mkdir(FGALLERY_DIR);
        @ mkdir(FGALLERY_DIR.'/tmp');
    }
    return true;
}

function fgallery_update() {
    $version = get_option('fgallery_db_version', 0);
    if (version_compare($version, '1.0.7', '<')) {
            $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_url` VARCHAR( 255 ) collate utf8_unicode_ci NULL AFTER `img_order` ;");
    }
    if (version_compare($version, '1.2.2', '<')) {
       $array = array('acosta','airion','arai','pax','pazin','postma','pageflip','nilus');
       foreach ($array as $key=>$value) {
               $temp = get_option('1_flash_gallery_'.$value);
               if ($temp) {
                    $option = substr($temp, 1, 9);
                    update_option('1_flash_gallery_'.$value, $option);
               }
       }

        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `gall_folder` `gall_folder` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `img_order` `img_order` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_parent` `img_parent` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_vs_folder` `img_vs_folder` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_size` `img_size` int( 11 ) default 0 ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_type` `img_type` varchar(50) collate utf8_unicode_ci default '' ;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_path` `img_path` varchar(50) collate utf8_unicode_ci default '' ;");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_cover` `gall_cover` int(11) default 0 ;");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_description` `gall_description` text collate utf8_unicode_ci ;");
    }

    if (version_compare($version, '1.2.4', '<')) {
        add_option('1_flash_gallery_watermark_enabled','0','','no');
        add_option('1_flash_gallery_watermark_path','','','no');
        add_option('1_flash_gallery_watermark_place','','','no');
    }

    if (version_compare($version, '1.3.1', '<')) {
        if($wpdb->get_var("SHOW TABLES LIKE '" . TEMPLATES_TABLE . "'") != TEMPLATES_TABLE) {
        // create table for albums settings
        $templates = "CREATE TABLE " . TEMPLATES_TABLE . " (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `gall_type` smallint(5) NOT NULL,
                          `gall_settings` text NOT NULL,
                          `created` date NOT NULL,
                          `templ_title` varchar(255) collate utf8_unicode_ci NOT NULL,
                          `templ_description` text,
                          PRIMARY KEY (`id`)
                        )";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($templates);
        }
    }
    
    if (version_compare($version, '1.5.1', '<')) {
        add_option('1_flash_gallery_preview_opt','0','','no');
    }
    
    if (version_compare($version, '1.5.6', '<')) {
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_extra` text collate utf8_unicode_ci NULL;");
    }

    update_option("fgallery_db_version", FGALLERY_VERSION);

    return true;
}

// function that recursively removes directory and all files in it
function fgallery_delete_dir($path) {
    $files = glob("$path/*");
    foreach($files as $file) {
      if(is_dir($file) && !is_link($file)) {
        fgallery_delete_dir($file);
      } else {
        unlink($file);
      }
    }
    rmdir($path);
}

// uninstall script
function fgallery_uninstall() {
   global $wpdb;
   require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
   //drop tables after uninstall
   dbDelta("DROP TABLE ". ALBUMS_TABLE);
   dbDelta("DROP TABLE ". PHOTOS_TABLE);
   dbDelta("DROP TABLE ". IMAGES_TO_ALBUMS_TABLE);
   dbDelta("DROP TABLE ". ALBUMS_SETTINGS_TABLE);
   dbDelta("DROP TABLE ". TEMPLATES_TABLE);
   //remove gallery directory
   fgallery_delete_dir(FGALLERY_DIR); 
   return true;
}

function fgallery_get_current_user_role() {
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
    return $user_role;
}

// defines the current user access level 
function fgallery_access_level(){
    $role = fgallery_get_current_user_role();
    switch ($role) {
        case 'administrator':
                return 10;
        case 'editor':
                return 7;
        case 'author':
                return 2;
        default: 
                return 2;
    }
}

// initialization script
function fgallery_init() {
    global $wp_version;
    wp_register_script('uploadify', FGALLERY_PATH.'/js/jquery.uploadify.v2.1.4.min.js',array('jquery'));
    wp_register_script('uploadjs', FGALLERY_PATH.'/js/fgallery_upload.js',array('jquery'));
    wp_register_script('fgalleryedit', FGALLERY_PATH.'/js/fgallery_edit.js',array('jquery'));
    wp_register_script('fgalleryjs', FGALLERY_PATH.'/js/fgallery.js',array('jquery'));
    wp_register_script('fgalleryimages', FGALLERY_PATH.'/js/fgallery_images.js',array('jquery'));
    wp_register_script('configurator', FGALLERY_PATH.'/js/configurator.js',array('jquery'));
    if ( version_compare( $wp_version, '3.2', '>=' ) ) {
            wp_register_script('uislider', FGALLERY_PATH.'/js/ui.slider32.js',array('jquery','jquery-ui-core'));// some code
    } elseif ( version_compare( $wp_version, '3.1', '>=' ) ) {
            wp_register_script('uislider', FGALLERY_PATH.'/js/ui.slider31.js',array('jquery','jquery-ui-core'));
    } else {
            wp_register_script('uislider', FGALLERY_PATH.'/js/ui.slider.js',array('jquery','jquery-ui-core'));
    }
    wp_register_script('copytoclipboard', FGALLERY_PATH.'/js/ZeroClipboard.js',array('jquery'));
    wp_register_script('copypref', FGALLERY_PATH.'/js/copy.js',array('jquery'));
    wp_register_script('swfhelper', FGALLERY_PATH. '/js/swfhelper.js',array('jquery'));
    wp_register_style('uploadifycss', FGALLERY_PATH. '/css/uploadify.css');
    wp_register_style('uislidercss', FGALLERY_PATH. '/css/ui.slider.css');
    wp_register_style('uithemecss', FGALLERY_PATH. '/css/ui.theme.css');
    wp_register_style('uitabscss', FGALLERY_PATH. '/css/ui.tabs.css');
    wp_register_style('configuratorcss', FGALLERY_PATH. '/css/configurator.css');
    wp_register_style('farbtasticcss', FGALLERY_PATH. '/css/farbtastic.css');
    wp_register_style('fgallerycss', FGALLERY_PATH. '/css/fgallery.css');
    //needed for flash embeding
    wp_enqueue_script('swfobject');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('swfhelper');
    wp_enqueue_style('thickbox');
    // building menu items
    add_action('admin_menu', 'fgallery_add_pages');
}

add_action('init', 'fgallery_init');


// calls scripts for upload page
function fgallery_upload_scripts(){
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('uploadjs');
    wp_enqueue_script('uploadify');
    wp_enqueue_script('jquery-form');
}

// calls scripts for gallery index/add/edit pages
function fgallery_admin_scripts() {
    wp_enqueue_script('jquery-form');
    if ($_GET['action'] == 'edit' || $_GET['page'] == 'fgallery_add') {
            wp_enqueue_script('farbtastic');
            wp_enqueue_script('configurator');	
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('uislider');
            wp_enqueue_script('copytoclipboard');
            wp_enqueue_script('copypref');
    }
    if ($_GET['action'] == 'pref') {
            wp_enqueue_script('copytoclipboard');
            wp_enqueue_script('copypref');
    }
    if ($_GET['action'] == 'images') {
            wp_enqueue_script('jquery-color');
    }
    wp_enqueue_script('fgalleryedit');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('fgalleryjs');
}

// calls scripts for images index/add/edit pages
function fgallery_admin_images_scripts(){
    wp_enqueue_script('jquery-ui-droppable');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('fgalleryimages');
}

// calls css files for upload page
function fgallery_upload_styles() {
    wp_enqueue_style('uploadifycss');
    wp_enqueue_style('fgallerycss');
    wp_enqueue_style('uitabscss');
    wp_enqueue_style('uithemecss');
}

// calls css files for upload page
function fgallery_images_styles() {
    wp_enqueue_style('fgallerycss');
}

// calls css files for upload page
function fgallery_settings_styles() {
    wp_enqueue_style('fgallerycss');
}

// calls css files for upload page
function fgallery_admin_styles() {
    wp_enqueue_style('fgallerycss');
    wp_enqueue_style('uitabscss');
    wp_enqueue_style('uithemecss');
    wp_enqueue_style('uislidercss');
    wp_enqueue_style('farbtasticcss');
    wp_enqueue_style('configuratorcss');
}

// adding items to menu
function fgallery_add_pages() {
    $pref = add_menu_page(__('1 Flash Gallery','fgallery'), __('1 Flash Gallery', 'fgallery'), 'upload_files', 'fgallery', 'fgallery_admin_albums');
    if ( function_exists('add_submenu_page') ) {
            $main   = add_submenu_page('fgallery', __('Galleries List','fgallery'), __('Galleries List','fgallery'), 'upload_files', 'fgallery', 'fgallery_admin_albums');
            $add    = add_submenu_page('fgallery', __('Create gallery','fgallery'), __('Create Gallery','fgallery'), 'upload_files', 'fgallery_add', 'fgallery_add_gallery');
            $images = add_submenu_page('fgallery', __('Images List','fgallery'), __('Images List','fgallery'), 'upload_files', 'fgallery_images', 'fgallery_images_page');
            $upload = add_submenu_page('fgallery', __('Upload images','fgallery'), __('Upload Images','fgallery'), 'upload_files', 'fgallery_upload', 'fgallery_upload_page');
            $settings = add_submenu_page('fgallery', __('Settings','fgallery'), __('Settings','fgallery'), 'upload_files', 'fgallery_settings', 'fgallery_settings_page');
    }
    add_action('admin_print_scripts-' . $upload, 'fgallery_upload_scripts');
    add_action('admin_print_scripts-' . $main, 'fgallery_admin_scripts');
    add_action('admin_print_scripts-' . $add, 'fgallery_admin_scripts');
    add_action('admin_print_scripts-' . $images, 'fgallery_admin_images_scripts');
    add_action('admin_print_styles-' . $upload, 'fgallery_upload_styles' );
    add_action('admin_print_styles-' . $images, 'fgallery_images_styles' );
    add_action('admin_print_styles-' . $main, 'fgallery_admin_styles' );
    add_action('admin_print_styles-' . $add, 'fgallery_admin_styles' );
    add_action('admin_print_styles-' . $settings, 'fgallery_settings_styles' );
}

/*
*	Starting admin pages render
*	
*/

function fgallery_upload_page() {
$nextgen = false;
if (plugin_is_active('nextgen-gallery/nggallery.php')) {
	$nextgen = true;
}
$folders = fgallery_get_folders();
?>
<div id="fb-root"></div>
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({appId: '<?php echo FGALLERY_APP_ID?>', status: true, cookie: true, xfbml: true});
 
                /* All the events registered */
                FB.Event.subscribe('auth.login', function(response) {
                    // do something with response
                    login();
                });
                FB.Event.subscribe('auth.logout', function(response) {
                    // do something with response
                    logout();
                });
				fqlQuery();
            };
			function fqlQuery(){
                FB.api('/me', function(response) {
                     var query = FB.Data.query('SELECT pid, aid, owner, src, src_big, src_small, link, caption, created FROM photo WHERE aid IN (SELECT aid FROM album WHERE owner = {0} )', response.id);
                     query.wait(function(rows) {
                        for (var key in rows) {
                                var temp = rows[key];
                                jQuery('#facebook_photos').append('<div class="facebook_photo">\n\
                                <input type="checkbox" value="1" name="fgallery_url_check['+temp.pid+']"/>\n\
                                <input type="hidden" name="fgallery_url['+temp.pid+']" class="fgallery_url" value="'+temp.src_big+'" />\n\
                                <img src="'+temp.src_small+'" alt="'+temp.caption+'" />\n\
                                <div>\n\
                                <p>'+temp.caption+'</p>\n\
                                </div>');
                        }
                     });
                });
            }
            (function() {
                var e = document.createElement('script');
                e.type = 'text/javascript';
                e.src = document.location.protocol +
                    '//connect.facebook.net/en_US/all.js';
                e.async = true;
                document.getElementById('fb-root').appendChild(e);
            }());
 
            function login(){
                window.location.reload();
            }
            function logout(){
                window.location.reload();
            }
			
</script>
	<div class="wrap">
	<h2><?php _e('Upload images from:', 'fgallery');?></h2>
		<div id="upload_tabs">
			<ul>
				<li><a href="#second_tab"><?php _e('Local computer', 'fgallery')?></a></li>
				<li><a href="#ftp_tab"><?php _e('FTP folder', 'fgallery')?></a></li>
				<li><a href="#zip_tab"><?php _e('ZIP archive', 'fgallery')?></a></li>
				<li><a href="#flickr_tab"><?php _e('URL', 'fgallery')?></a></li>
				<li><a href="#wp_gall_tab"><?php _e('Wordpress Gallery', 'fgallery')?></a></li>
				<li><a href="#facebook_tab"><?php _e('Facebook', 'fgallery')?></a></li>
				<li><a href="#scandir_tab"><?php _e('Scan Server directory', 'fgallery')?></a></li>
				<?php if ($nextgen):?>
					<li><a href="#nextgen_tab"><?php _e('NextGEN', 'fgallery')?></a></li>
				<?php endif;?>
			</ul>
			<div id="second_tab">
				<div class="form_left">
				<label for="uploadify_img_folder"><?php _e('Images will be saved to:','fgallery')?></label>
				<select id="uploadify_img_folder">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
						<?php 
								if (!empty($folders)):?>
							  <?php foreach ($folders as $item): ?>
								<option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
							  <?php endforeach;?>  
							  <?php endif;
							?>
					</select> <br />
				<div id="fileQueue"></div>
				<input type="file" id="uploadify" name="uploadify" />
				<p><a href="javascript:void(0);"><?php _e('Cancel all uploads', 'fgallery');?></a> </p>
				<button><?php _e('Upload', 'fgallery');?></button>
				<?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
				<script type="text/javascript">
                                    jQuery(document).ready(function() {
                                            jQuery("#uploadify").uploadify({
                                                    'uploader'       : '<?php echo WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)) ?>/swf/uploadify.swf',
                                                    'script'         : '<?php echo fgallery_upload_uploadify_get_url(); ?>',
                                                    'cancelImg'      : '<?php echo WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)) ?>/images/cancel.png',
                                                    'queueID'        : 'fileQueue',
                                                    'fileDesc'       : 'Images',
                                                    'folder'         : '<?php echo EXTRA_DIR ?>wp-content/uploads/fgallery',
                                                    'fileExt'        : '*.jpg;*.jpeg;*.gif;*.bmp;*.png',
                                                    'auto'           : false,
                                                    'multi'          : true,
                                                    'removeCompleted': true,
                                                    'onComplete'     : function(event, queueID, fileObj, response, data){
                                                                    var array = response.split("_");
                                                                    var id = "image_"+array[1];
                                                                    jQuery("#uploaded_images").append('<div class="uploaded_image '+id+'"><img src="<?php echo COVER_PATH?>'+array[0]+'" alt="'+fileObj.name+'" /><p>'+fileObj.name+'</p><a href="javascript:void(0)" rel="admin.php?page=fgallery_images&amp;action=delete&amp;id='+array[1]+'" id="'+id+'" title="<?php _e('Delete','fgallery') ?>" class="fgallery_action delete" onclick="delete_img('+array[1]+');"><?php _e('Delete','fgallery') ?></a></div>');
                                                                                    },
                                                    'onAllComplete'  : function () {
                                                                    jQuery("#uploaded_images").append('<br clear="all" /><a href="admin.php?page=fgallery_images&amp;folder='+jQuery('#uploadify_img_folder').val()+'" class="fgallery_action"><?php _e('Save','fgallery')?></a>');
                                                                                    },
                                                    'onError'       : function(event, queueID, fileObj, errorObj) {
                                                                                            alert(errorObj.info);
                                                                                    }
                                            });
                                    });
				</script>
				</div>
				<div class="form_left" id="uploaded_images">
				</div>
				<br clear="all" />
			</div>
			<div id="ftp_tab">
				<form method="post" action="<?php echo fgallery_upload_ftp_get_url();?>" class="upload">
				<label for="fgallery_url_folder"><?php _e('Images will be saved to:','fgallery')?></label>
				<select name="fgallery_url_folder" id="fgallery_url_folder">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
						<?php if (!empty($folders)):?>
                                                <?php foreach ($folders as $item): ?>
                                                       <option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
                                                <?php endforeach;?>  
                                                <?php endif;?>
					</select> <br />
					<label for="ftp_name"><?php _e('FTP server name','fgallery')?></label><br />
					<input type="text" name="ftp_name" id="ftp_name" /><br />
					<label for="ftp_folder"><?php _e('FTP Folder name','fgallery')?></label><br />
					<input type="text" name="ftp_folder" id="ftp_folder" /><br />
					<label for="fgallery_ftp_subfolders"><?php _e('Include subfolders','fgallery')?></label>
					<input type="checkbox" name="fgallery_ftp_subfolders" id="fgallery_ftp_subfolders" value="1" /> <br />
					<label for="ftp_username"><?php _e('FTP username');?></label><br />
					<input type="text" name="ftp_username" id="ftp_username" value="anonymous" /> <br />
					<label for="ftp_pass"><?php _e('FTP password');?></label><br />
					<input type="password" name="ftp_pass" id="ftp_pass" />
					<?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?><br />
					<input type="submit" value="<?php _e('Upload', 'fgallery');?>" />
					<?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
				</form>
			</div>
			<div id="zip_tab">
				<form method="post" action="<?php echo fgallery_upload_zip_get_url();?>" enctype="multipart/form-data" class="upload">
				<label for="zip_folder"><?php _e('Images will be saved to:','fgallery')?></label>
				<select name="zip_folder" id="zip_folder">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
						<?php if (!empty($folders)):?>
                                                <?php foreach ($folders as $item): ?>
                                                      <option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
                                                <?php endforeach;?>  
                                                <?php endif; ?>
					</select> <br />
					<input type="file" name="fgallery_zip" id="fgallery_zip" />
					<input type="submit" value="<?php _e('Upload', 'fgallery');?>" />
					<?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
				</form>
			</div>
			<div id="flickr_tab">
				<?php _e('Choose images to add','fgallery')?> <br />
				<p class="details"><?php _e('Images will be downloaded from remote server','fgallery')?></p>
				<form method="post" action="<?php echo fgallery_upload_url_get_url();?>" id="url">
				<label for="fgallery_url_folder"><?php _e('Images will be saved to:','fgallery')?></label>
					<select name="fgallery_url_folder" id="fgallery_url_folder">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
						<?php if (!empty($folders)):?>
                                                <?php foreach ($folders as $item): ?>
                                                    <option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
						<?php endforeach;?>  
						<?php endif;?>
					</select>
					<?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
					<div class="input_fields">
					<label for="fgallery_ftp_0"><?php _e('Type the URL of the image','fgallery')?></label><br />
					<div id="fgallery_url_wrap_0" class="fgallery_url_wrap"><input type="text" name="fgallery_url[]" id="fgallery_url_0" class="fgallery_url" onchange="show_img_from_url(this.value,0)" /><img src="" id="fgallery_img_0" width="100"/><a class="delete_url" onclick="delete_url(0)" title="<?php _e('Delete url','fgallery')?>"></a>
					</div>
					</div>
					<a class="add_url" title="<?php _e('Add url','fgallery')?>"></a> <br />
					<input type="submit" value="<?php _e('Save', 'fgallery');?>" />
					<?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
				</form>
			</div>
			<div id="wp_gall_tab">
				<?php _e('Choose images from WordPress Gallery to add','fgallery')?>
				<?php 
					$args = array('post_type'=>'attachment','post_mime_type'=>'image','numberposts'=>999999999);
					$images = get_posts($args);
					?>
				<p class="details"><?php _e('Images won\'t be copied only the info','fgallery')?></p>
				<form method="post" action="<?php echo fgallery_upload_media_get_url();?>" class="upload">
				<label for="fgallery_url_folder"><?php _e('Images will be saved to:','fgallery')?></label>
					<select name="fgallery_url_folder" id="fgallery_url_folder">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
						<?php if (!empty($folders)):?>
                                                <?php foreach ($folders as $item): ?>
                                                    <option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
						<?php endforeach;?>  
						<?php endif;?>
					</select>
					<?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
					<div class="media_files">
					<?php fgallery_render_media_images_table($images) ?>
					</div>
					<input type="submit" value="<?php _e('Save', 'fgallery');?>" />
					</form>
			</div>
			<div id="facebook_tab">
				<p>
					<fb:login-button autologoutlink="true" perms="email,user_birthday,status_update,publish_stream"></fb:login-button>
				</p>
					
			<form method="post" action="<?php echo fgallery_upload_facebook_get_url();?>" class="upload">
				<div id="facebook_photos">

				</div>
				<br clear="all" />
				  <?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
				  <input type="submit" value="<?php _e('Save');?>" />
				  <?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
			</form>
			</div>
			<div id="scandir_tab">
				<?php _e('Enter directory path on your server to import images');?>
				<p class="details"><?php _e('Images won\'t be copied only the info','fgallery')?></p>
				<form method="post" action="<?php echo fgallery_upload_scandir_get_url();?>" class="upload">
				<label for="fgallery_url_folder"><?php _e('Images will be saved to:','fgallery')?></label>
					<select name="fgallery_url_folder" id="fgallery_url_folder">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
						<?php if (!empty($folders)):?>
                                                <?php foreach ($folders as $item): ?>
                                                    <option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
                                                <?php endforeach;?>  
						<?php endif;?>
					</select>
					<?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
					<input type="text" value="" name="directory" />
					<input type="submit" value="<?php _e('Save', 'fgallery');?>" />
				</form>
			</div>
			<?php if ($nextgen):?>
			<div id="nextgen_tab">
				<?php _e('Choose images from NextGEN Galleries to add','fgallery')?>
				<?php $images = fgallery_get_nextgen_images();?>
				<p class="details"><?php _e('Images won\'t be copied only the info','fgallery')?></p>
				<form method="post" action="<?php echo fgallery_upload_nextgen_get_url();?>" class="upload">
				<label for="fgallery_url_folder"><?php _e('Images will be saved to:','fgallery')?></label>
					<select name="fgallery_url_folder" id="fgallery_url_folder">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
						<?php if (!empty($folders)):?>
                                                <?php foreach ($folders as $item): ?>
                                                    <option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
						<?php endforeach;?>  
						<?php endif;?>
					</select>
					<?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
					<div class="media_files">
					<?php fgallery_render_nextgen_images_table($images) ?>
					</div>
					<input type="submit" value="<?php _e('Save', 'fgallery');?>" />
					</form>
			<?php endif;?>
			</div>
			<div class="ajax_loader"></div>
		</div>
		<div class="fgallery_box"><?php                                
                   if (!is_dir(FGALLERY_DIR)) {
                    echo '<p class="fgallery_error">'.__("Folder", 'fagllery').' wp-content/uploads/fgallery '.__("doesn't exist", 'fagllery').'</p>';
                   } elseif (!is_writeable(FGALLERY_DIR)) {
                       echo '<p class="fgallery_error">'.__("Folder", 'fagllery').' wp-content/uploads/fgallery '.__("is not writable", 'fagllery').'</p>';
                   }
                   _e('Note: Important! If single image is more then 1Mb galleries may work slower', 'fgallery');?> </div>
	</div>
<?php	
}

function fgallery_render_media_images_table($images) {
if (!empty($images)) {
	foreach ($images as $image) {
		?>
		<div class="image_wrap">
			<div class="fgallery_image_info">
				<input type="checkbox" name="media[]" value="<?php echo $image->ID?>" />
			</div>
			<div class="fgallery_image">
			<?php 				
				echo '<img src="'.COVER_PATH.str_replace('http://'.$_SERVER['HTTP_HOST'].EXTRA_DIR,'',$image->guid).'" alt="'.$image->post_title.'" />';	
			?>
			</div>
			<div class="fgallery_image_info">
				<?php echo $image->post_title?>
			</div>
		</div>
		<?php
	}
}

}

function fgallery_render_nextgen_images_table($images) {
if (!empty($images)) {
	foreach ($images as $image) {
		?>
		<div class="image_wrap">
			<div class="fgallery_image_info">
				<input type="checkbox" name="nextgen[]" value="<?php echo $image->pid?>" />
			</div>
			<div class="fgallery_image">
			<?php 				
				echo '<img src="'.EXTRA_DIR.$image->path.'/thumbs/thumbs_'.$image->filename.'" alt="'.$image->image_slug.'" />';
			?>
			<input type="hidden" name="nextgenpath[<?php echo $image->pid?>]" value="<?php echo $image->path.'/'.$image->filename;?>"/>
			</div>
			<div class="fgallery_image_info">
				<?php echo $image->image_slug?>
				<input type="hidden" name="nextgencaption[<?php echo $image->pid?>]" value="<?php echo $image->image_slug;?>"/>
			</div>
		</div>
		<?php
	}
}

}

function fgallery_preferences_page() {
    global $wp_version;
	?>
	<div class="wrap">
		<h2><?php _e('Preferences page','fgallery')?></h2>
		<div id="fgallery_faq" class="fgallery_box">
			<a href="http://1plugin.com/faq"><?php _e('FAQ','fgallery')?></a>
		</div>
		<div id="fgallery_settings" class="fgallery_box">
			<h2><?php _e('Server Settings', 'fgallery')?></h2>
                        <?php echo '<p><span class="lbl">'.__('Server Name:', 'fgallery').'</span>'.$_SERVER['SERVER_NAME']."</p>";
                               echo '<p><span class="lbl">'.__('Document Root:', 'fgallery').'</span>'.$_SERVER['DOCUMENT_ROOT']."</p>";
                               echo '<p><span class="lbl">'.__('Web server:', 'fgallery').'</span>'.$_SERVER['SERVER_SOFTWARE']."</p>";
                               echo '<p><span class="lbl">'.__('Host:', 'fagallery').'</span>'.$_SERVER['HTTP_HOST']."</p>";
                               echo '<p><span class="lbl">'.__('Client Agent:', 'fgallery').'</span>'.$_SERVER['HTTP_USER_AGENT']."</p>";
                               echo '<p><span class="lbl">'.__('Word Press version:', 'fagallery').'</span>'.$wp_version;
                               echo '<p><span class="lbl">'.__('Plugin version:', 'fgallery').'</span>'.FGALLERY_VERSION;
                               echo '<p><span class="lbl">'.__('Max size of uploaded file:', 'fgallery').'</span>'.ini_get('upload_max_filesize').'</p>';
                               if (!is_dir(FGALLERY_DIR)) {
                                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery '.__("doesn't exist", 'fgallery').'</p>';
                               } elseif (!is_writeable(FGALLERY_DIR)) {
                                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery '.__("is not writable", 'fgallery').'</p>';
                               }
                               if (!is_dir(FGALLERY_DIR.'/tmp')) {
                                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery/tmp '.__("doesn't exist", 'fgallery').'</p>';
                               } elseif (!is_writeable(FGALLERY_DIR.'/tmp')) {
                                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery/tmp '.__("is not writable", 'fgallery').'</p>';
                               }
                               if (FGALLERY_PHP4_MODE) {
                                            echo '<p class="fgallery_error">'.__("Your server runs PHP 4", 'fgallery').'</p>';
                               }
                         ?>
		</div>
                <?php echo '<button id="copypref" rel="'.FGALLERY_PATH.'/swf/ZeroClipboard.swf">'.__('Copy settings to clipboard', 'fagllery').'</button>';?>
		<div id="fgallery_updates" class="fgallery_box">
			<h2><?php _e('Updates', 'fgallery')?></h2>
			<h2><?php _e('Already Installed Updates', 'fgallery')?></h2>
			<?php $updated = false;
				  if (get_option('1_flash_gallery_acosta') != '') {
					echo '1 Flash Gallery Update Acosta <br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_airion') != '') {
					echo '1 Flash Gallery Update Airion <br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_arai') != '') {
					echo '1 Flash Gallery Update Arai <br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_pax') != '') {
					echo '1 Flash Gallery Update Pax <br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_pazin') != '') {
					echo '1 Flash Gallery Update Pazin <br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_postma') != '') {
					echo '1 Flash Gallery Update Postma <br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_pageflip') != '') {
					echo '1 Flash Gallery Update PageFlip<br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_nilus') != '') {
					echo '1 Flash Gallery Update Nilus<br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_nusl') != '') {
					echo '1 Flash Gallery Update Nusl<br />';
					$updated = true;
				  }
				  if (get_option('1_flash_gallery_kranjk') != '') {
					echo '1 Flash Gallery Update Kranjk<br />';
					$updated = true;
				  }
				  if (!$updated) {
					_e('There are no updates yet. You can get them at <a href="http://1plugin.com/order">1 Flash Gallery Wordpress Plugin Site</a>', 'fgallery');
				  }
				  ?>
		</div>
	</div>
	<?php 
}

function fgallery_settings_page() {
	if (isset($_POST) && wp_verify_nonce($_POST['fgallery_watermark_field'], 'fgallery_watermark')){
		update_option('1_flash_gallery_watermark_enabled',$_POST['watermark_enabled']);
		update_option('1_flash_gallery_preview_opt',$_POST['preview_opt']);
		if($_FILES['watermark_file']['size']){
			$ext = pathinfo($_FILES['watermark_file']['name']);
			$file_name = FGALLERY_DIR . '/watermark_'.date("His").'.'.$ext['extension'];
			move_uploaded_file($_FILES['watermark_file']['tmp_name'], $file_name);
			$fileinfo = getimagesize($file_name);
				$img_type = $fileinfo['mime'];
				if (strpos($img_type,'image') === false) {
					unlink($file_name);
					die(sprintf(__('%s is not an image'),$file_name).'<br />');
				}
			update_option('1_flash_gallery_watermark_path',$file_name);
		}
		update_option('1_flash_gallery_watermark_place',$_POST['wm_placement']);
	}
	
	$wm_enabled = get_option('1_flash_gallery_watermark_enabled');
	$wm_path = get_option('1_flash_gallery_watermark_path');
	$wm_place = get_option('1_flash_gallery_watermark_place');
	?>
	<div class="wrap">
            
	<form id="fgallery_watermark_form" method="post" enctype="multipart/form-data">
            <fieldset>
            <h2><?php _e('1 Flash Gallery Watermark','fgallery')?></h2>
		<div class="fgallery_box"><?php _e('Note: Watermark is placed only on images that were uploaded from "Local Computer", 
                    "ZIP" and "FTP" tabs. Other images will not have watermark due to copyright reasons. Images that were uploaded with watermark
                    will stay with it untill are deleted. Watermark could not be removed without file reuploading.', 'fgallery')?></div>	
            <label><?php _e('Watermark Position', 'fagllery')?></label>
		<table cellspacing="1" cellpadding="2" border="1" class="wm_placement">
			    <tbody>
				<tr>
			    	<td><input type="radio" value="TL" <?php if ($wm_place == 'TL') echo 'checked=""';?> name="wm_placement"></td>
			    	<td><input type="radio" value="TM" <?php if ($wm_place == 'TM') echo 'checked=""';?> name="wm_placement"></td>
			    	<td><input type="radio" value="TR" <?php if ($wm_place == 'TR') echo 'checked=""';?> name="wm_placement"></td>
			    </tr>
			    <tr>
			    	<td><input type="radio" value="CL" <?php if ($wm_place == 'CL') echo 'checked=""';?> name="wm_placement"></td>
			    	<td><input type="radio" value="C"  <?php if ($wm_place == 'C') echo 'checked=""';?> name="wm_placement"></td>
			    	<td><input type="radio" value="CR" <?php if ($wm_place == 'CR') echo 'checked=""';?> name="wm_placement"></td>
			    </tr>
			    <tr>
			    	<td><input type="radio" value="BL" <?php if ($wm_place == 'BL') echo 'checked=""';?> name="wm_placement"></td>
			    	<td><input type="radio" value="BM" <?php if ($wm_place == 'BM') echo 'checked=""';?> name="wm_placement"></td>
			    	<td><input type="radio" value="BR" <?php if ($wm_place == 'BR') echo 'checked=""';?> name="wm_placement"></td>
			    </tr>
				</tbody>
		</table>
				
		<label for="watermark_enabled"><?php _e('Enable Watermark','fgallery') ?></label>
		<input type="checkbox" name="watermark_enabled" value="1" <?php if ($wm_enabled == '1') echo 'checked=""';?> id="watermark_enabled" />
		<br />
		<label for="watermark_file"><?php _e('Upload New','fgallery') ?></label>
		<input type="file" name="watermark_file" id="watermark_file" /> <br />
                <?php if ($wm_path != '') {
			$wm_path = str_replace('\\','/', $wm_path);
			$src = str_replace($_SERVER['DOCUMENT_ROOT'],'',$wm_path);			
			echo '<p>Current Watermark</p><img src="'.$src.'" alt="watermark" />';
		}?>
                </fieldset>
                <fieldset>
                    <h2><?php _e('Thumbnails optimization','fgallery')?></h2>
                    <label for="preview_opt"><?php _e('Preview Optimization', 'fagllery')?></label>
                    <input type="checkbox" name="preview_opt" id="preview_opt" value="1" 
                        <?php if (get_option('1_flash_gallery_preview_opt',0) == 1) echo 'checked="checked"'?> /> 
                    <p><?php _e('This option allows to fasten the loading of previews and thumbnails in slideshow','fgallery')?></p>
                </fieldset>
		<input type="submit" value="<?php _e('Save')?>" /> <br />
		<?php wp_nonce_field('fgallery_watermark','fgallery_watermark_field');?>

	</form>
	</div>
	<?php
}

function fgallery_add_gallery() {
    fgallery_edit_album_page(0);
}

function fgallery_admin_albums() {
    global $wpdb;
        if (isset($_GET['action'])) {
                $action = $_GET['action'];
        }
	switch ($action) {
		case 'pref':
			fgallery_preferences_page();
		break;
		case 'edit':
                    	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
				$id = $_GET['id'];
			} else {
                            die();
                        }
                        if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_settings_field'], 'fgallery_settings')) 
                        {
                            $id = fgallery_edit_album($_POST['gallery'], $id);
                                if ($id) {
                                        $to_store = fgallery_prepare_settings($_POST);
                                        fgallery_save_album_settings($id, $to_store);
                                }
                        }
			fgallery_edit_album_page($id);
		break;
		case 'images':
                        if (!empty($_POST)) {
                            if (isset($_POST['album_id']) && is_numeric($_POST['album_id'])
                                    && isset($_POST['gall_id']) && is_numeric($_POST['gall_id'])){
                                        if (!empty($_POST['images'])) {
                                                $ids = $_POST['images'];
                                                $ids_string = implode(",",$ids);
                                                switch ($_POST['album_id']) {
                                                    case '-1':
                                                            $wpdb->query("DELETE FROM ". IMAGES_TO_ALBUMS_TABLE. " 
                                                                          WHERE img_id IN (".$ids_string.") AND gall_id = ".$_POST['gall_id']);
                                                    break;
                                                    default :
                                                            $wpdb->query("UPDATE ".IMAGES_TO_ALBUMS_TABLE." SET `gall_folder` =".$_POST['album_id']."
                                                                          WHERE `img_id` IN (".$ids_string.") AND `gall_id` = ".$_POST['gall_id']);
                                                    break;
                                                }
                                        } 
                                }
                        }
                        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
				$id = $_GET['id'];
				fgallery_edit_album_images_page($id);
			}
		break;			
		default:
		// -- pagination ------------
		if (!empty($_POST) && wp_verify_nonce($_POST['screenoptionnonce'], 'screen-options-nonce')) {
				update_option( $_POST['wp_screen_options']['option'],  $_POST['wp_screen_options']['value']);
			}
		$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
                $sort_by = isset($_GET['sort']) ? absint($_GET['sort']) : 4;
		if ( empty($pagenum) )
			$pagenum = 1;
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = get_option('toplevel_page_fgallery_per_page',25);
                        apply_filters('toplevel_page_fgallery_per_page', $per_page);
		$num_pages = ceil(fgallery_albums_count() / $per_page);
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $num_pages,
			'current' => $pagenum
		));
		// ---------------------------	
			$items = fgallery_get_albums($pagenum,$per_page, $sort_by); ?>
			<div class="wrap">
			<h2><?php _e('Gallery List', 'fgallery') ?></h2>
			<div class="tablenav">
                        <form action="" method="get" class="form_left">
                        <span><?php _e('Sort by:', 'fgallery')?></span>
                            <select name="sort" id="images_sortby">
                                <option value="0" <?php if ($_GET['sort'] == 0) echo 'selected="selected"'?>><?php _e('Title ASC', 'fgallery');?> </option>
                                <option value="1" <?php if ($_GET['sort'] == 1) echo 'selected="selected"'?>><?php _e('Title DESC', 'fgallery');?> </option>
                                <option value="2" <?php if ($_GET['sort'] == 2) echo 'selected="selected"'?>><?php _e('Date ASC', 'fgallery');?> </option>
                                <option value="3" <?php if ($_GET['sort'] == 3) echo 'selected="selected"'?>><?php _e('Date DESC', 'fgallery');?> </option>
                            </select>
                            <input type="hidden" name="page" value="fgallery" />
                            <input type="hidden" name="paged" value="<?php echo $pagenum; ?>" />
                        </form>
			<?php if ( $page_links ) { ?>
				<div class="tablenav-pages">
				<?php $count_posts = fgallery_albums_count();
                              $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                                    number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                                    number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
                                                                    number_format_i18n( $count_posts ),
                                                                    $page_links
                                                                    );
					echo $page_links_text; ?>
				</div>
			<?php } ?>
						<a href="<?php echo fgallery_createalbum_url()?>" title="<?php _e('Create gallery', 'fgallery')?>" id="add_gallery" class="fgallery_action"><?php _e('Create gallery', 'fgallery') ?></a>
			<a href="<?php echo fgallery_get_pref_url()?>" title="<?php _e('Preferences', 'fgallery')?>" class="fgallery_action"><?php _e('Preferences', 'fgallery') ?></a>
			</div>
			<?php 
			echo fgallery_render_albums_table($items);
			?>
				<div class="tablenav">
				<?php
				if ( $page_links ) { 
					echo "<div class='tablenav-pages'>$page_links_text</div>";
				} ?>
				</div>
			</div> <?php 
		break;
	}
	
	return true;
}

/*
 *   Begin crud routines
 */
 
 function fgallery_edit_album_images_page($id){
	$folder = isset($_GET['folder']) ? $_GET['folder'] : 0;
 	$images = fgallery_get_album_images($id, $folder);
	$album = fgallery_get_album($id);
		echo '<div class="wrap"><div id="fgallery_images">';
		echo '<h2>'.__('Images in', 'fgallery').'<a href="'.fgallery_get_edit_url($id).'"> '.$album['gall_name'].'</a></h2><div class="tablenav">';
		$title = __('Add images', 'fgallery');
		echo '<a href="'.fgallery_get_addimages_url($id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a>';
		$title = __('Create album', 'fgallery');
		echo '<a href="'.fgallery_create_folder_url(2,$id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a></div>';
		if (!empty($images)){
			echo fgallery_render_album_images($images, $id);	
		} else {
			_e('There are no images in this gallery', 'fgallery');
		}
		echo '</div></div>';
 }
 
 // render edit gallery page
function fgallery_edit_album_page($id){
	echo '<div class="wrap">';
	$album = fgallery_get_album($id);
	if (empty($album)) {
		$album['gall_id'] = 0;
		$album['gall_width'] = 450;
		$album['gall_height'] = 385;
		$album['gall_bgcolor'] = "ffffff";
		$album['gall_type'] = 3;
	}
	if ($id != 0) {
		echo '<h2>'.__('Edit gallery', 'fgallery').' "'.$album['gall_name'].'"</h2>';
	} else {
		echo '<h2>'.__('Add new gallery', 'fgallery').'</h2>';
	}
	echo '<div id="fgallery_settings_form">';
	if ($album['gall_published'] && $id != 0) {    
		echo '<p>'.__('Embed code', 'fgallery').'</p>';
		echo '<div id="shortcode_view">'.fgallery_do_shortcode($album['gall_id']).'</div>';
        echo '<button id="shortcode" rel="'.FGALLERY_PATH.'/swf/ZeroClipboard.swf">'.__('Copy to clipboard', 'fgallery').'</button>';
	}
	echo '<div id="configurator_wrap">';
		echo sc_params_pane($album);
	echo '</div>';
	if ($id != 0):
	$path = fgallery_search_flash_path($album);
	        echo '<script type="text/javascript">
                var flashvars = {settings: "'.FGALLERY_PATH.'/config.php?gall_id='.$id.'", images: "'.FGALLERY_PATH.'/images.php?gall_id='.$id.'"};
                var params = {bgcolor: "#'.$album['gall_bgcolor'].'", allowFullScreen: "true", wmode: "transparent"};
                swfobject.embedSWF("'.$path.'", "flashcontent", "'.$album['gall_width'].'", "'.$album['gall_height'].'", "10.0.0",false, flashvars, params);
              </script> ';
        echo '<div id="flashcontent">
                <strong>You need to upgrade your Flash Player</strong>
             </div><br />';
	
		echo '<div class="edit_gallery_urls">';
			$title = __('Add images', 'fgallery');
			echo '<a href="'.fgallery_get_addimages_url($id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a>';
			$title = __('List images', 'fgallery');
			echo '<a href="'.fgallery_get_album_images_url($id).'" title="'.$title.'" class="fgallery_action">'.$title.'</a>';
			$title = __('Save settings', 'fgallery');
			echo '<a href="'.fgallery_get_save_settings_url($id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a>';
			$title = __('Export settings', 'fgallery');
			echo '<a href="'.fgallery_get_export_settings_url($id).'" title="'.$title.'" class="fgallery_action">'.$title.'</a>';
			$title = __('Load settings', 'fgallery');
			echo '<a href="'.fgallery_get_load_settings_url($id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a>';
		echo '</div>';
		endif;
	echo '</div>';
		
	
	echo '</div>';
}

// renders image edit page
function fgallery_edit_image_page($id){
	echo '<div class="wrap">';
	if ( !empty($_POST) && check_admin_referer('fgallery_edit','fgallery_edit_image_field') ) {
		$save = fgallery_edit_image($_POST, $id);
		if ($save) {
			echo '<div id="message" class="updated fade"><p><strong>'.__('Image has been saved', 'fgallery').'</strong></p></div>';
		} else {
			echo '<div id="message" class="error"><p><strong>'.__('Image name cannot be empty', 'fgallery').'</strong></p></div>';
		}
	}
	$image = fgallery_get_image($id);
	if ($image['img_vs_folder'] == 1) {
		echo '<h2>'.__('Edit folder', 'fgallery').' "'.$image['img_caption'].'"</h2>';
	} elseif ($image['img_vs_folder'] == 0) {
		echo '<h2>'.__('Edit image', 'fgallery').' "#'.$image['img_id'].' '.$image['img_caption'].'"</h2>';
	}
	echo fgallery_render_edit_image_form($image);
	if ($image['img_vs_folder'] == 0) {
		echo '<a href="'.get_option('siteurl').'/'.$image['img_path'].'" title="'.__('Full view','fgallery').'" target="_blank">
				<img src="'.THUMB_PATH.$image['img_path'].'" alt="'.$image['img_caption'].'"/>
			 </a>';
	}
	echo '</div>';
}

// renders images index page
function fgallery_images_page() {
global $wpdb;
	if (isset($_GET['action'])) {
		$action = $_GET['action'];
	}
	switch ($action) {
		case 'edit':
			if (isset($_GET['id']) && is_numeric($_GET['id'])) {
				$id = $_GET['id'];
				fgallery_edit_image_page($id);
			}
		break;
		default:
		// -- pagination ------------
		if (!empty($_POST) && wp_verify_nonce($_POST['screenoptionnonce'], 'screen-options-nonce')) {
			update_option( $_POST['wp_screen_options']['option'],  $_POST['wp_screen_options']['value']);
		}
		$pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
                $sort_by = isset($_GET['sort']) ? absint($_GET['sort']) : 3;
		$folder = isset($_GET['folder']) ? absint($_GET['folder']) : 0;
		if ($folder) {
			$folder_name = fgallery_get_folder_name($folder);
		}
		if ( empty($pagenum) )
			$pagenum = 1;
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = get_option('1_flash_gallery_page_fgallery_images_per_page',25);
                        apply_filters('1_flash_gallery_page_fgallery_images_per_page', $per_page);
		$num_pages = ceil(fgallery_images_count($folder) / $per_page);
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $num_pages,
			'current' => $pagenum
		));
		// ---------------------------	
			$items = fgallery_get_images($pagenum,$per_page, $folder, $sort_by); ?>
			<div class="wrap">
			<?php if (!$folder){?>
			<h2><?php _e('Images List', 'fgallery'); ?></h2>
			<?php }else{ ?>
			<h2><a href="<?php echo fgallery_images_url();?>"><?php _e('Images', 'fgallery');?> </a> <?php echo '/ '.__('Folder','fgallery').' "'.$folder_name.'"'?> </h2>
			<?php }?>
			<div class="tablenav">
                        <form action="" method="get" class="form_left">
                        <label for="images_sortby"><?php _e('Sort by:','fgallery')?></label>
                            <select name="sort" id="images_sortby">
                                <option value="0" <?php if ($_GET['sort'] == 0) echo 'selected="selected"'?>><?php _e('Title ASC','fgallery');?> </option>
                                <option value="1" <?php if ($_GET['sort'] == 1) echo 'selected="selected"'?>><?php _e('Title DESC','fgallery');?> </option>
                                <option value="2" <?php if ($_GET['sort'] == 2) echo 'selected="selected"'?>><?php _e('Date ASC','fgallery');?> </option>
                                <option value="3" <?php if ($_GET['sort'] == 3) echo 'selected="selected"'?>><?php _e('Date DESC','fgallery');?> </option>
                                <option value="4" <?php if ($_GET['sort'] == 4) echo 'selected="selected"'?>><?php _e('Size ASC','fgallery');?> </option>
                                <option value="5" <?php if ($_GET['sort'] == 5) echo 'selected="selected"'?>><?php _e('Size DESC','fgallery');?> </option>
                            </select>
                            <input type="hidden" name="page" value="fgallery_images" />
                            <input type="hidden" name="paged" value="<?php echo $pagenum; ?>" />
							<div class="clear"></div>
                        </form>
                            
			<?php if ( $page_links ) { ?>
				<div class="tablenav-pages">
				<?php $count_posts = fgallery_images_count($folder);
					  $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
										number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
										number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
										number_format_i18n( $count_posts ),
										$page_links
										);
					echo $page_links_text; ?>
				</div>
			<?php } ?>
			
			<?php if(!$folder):?><a href="<?php echo fgallery_create_folder_url(1,0) ?>" class="fgallery_action thickbox" ><?php _e('Create new folder','fgallery')?></a> <?php endif;?>
			<a href="<?php echo fgallery_get_upload_url() ?>" class="fgallery_action" ><?php _e('Upload photo','fgallery')?></a>
			</div>
			<form action="<?php echo fgallery_get_massedit_url();?>" method="post" id="massedit_form">
				<?php wp_nonce_field('fgallery_massedit','fgallery_massedit_field'); ?>

				<?php if (fgallery_access_level() >=5) :?>
				<select name="image_action" id="image_action">
				<optgroup label="<?php _e('Actions', 'fgallery')?>">
					<option value="-" selected="selected"><?php _e('Choose action', 'fgallery')?></option>
					<option value="-2"><?php _e('Delete selected', 'fgallery')?></option>
					<option value="-1"><?php _e('Make new gallery from selected', 'fgallery')?></option>
				</optgroup>
					<?php $folders = fgallery_get_folders();
						if (!empty($folders)):?>
					<optgroup label="<?php _e('Move selected to', 'fgallery')?>">
						<option value="0"><?php _e('Root folder', 'fgallery')?></option>
					  <?php foreach ($folders as $item): ?>
								<option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
					  <?php endforeach;?>
					</optgroup>  
					  <?php endif;
					?>
				<?php $albums = fgallery_get_albums(1, 9999999, 4);
						if (!empty($albums)):?>
					<optgroup label="<?php _e('Add selected to', 'fgallery')?>">
					  <?php foreach ($albums as $item): ?>
							<option value="gall_<?php echo $item['gall_id']?>"><?php echo $item['gall_name']?></option>
					  <?php endforeach;?>
					</optgroup>  
					  <?php endif;
					?>	
				</select>
				<input type="submit" value="<?php _e('Go', 'fgallery')?>" />
			   <?php endif;?>
			<?php 
			echo fgallery_render_images_table($items);
			?>
			  </form>
			<div class="tablenav">
				<?php
				if ( $page_links ) { 
					echo "<div class='tablenav-pages'>$page_links_text</div>";
				} ?>
			</div>
                       
			</div> <?php 
		break;
	}
	
	return true;
}

/*
*   Begin gallery settings functions
*/

// returns default gallery settings if there is no user customize
function fgallery_default_album_settings() {
    $settings = array();
        $settings['sc_gallery__color'] = 'ffffff';
        $settings['sc_gallery__alpha'] = 0.5;
        $settings['sc_gallery__photo'] = '';
	   
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
        $settings['sc_slideshow__music'] = '';
        $settings['sc_slideshow__musicPath'] = '';
        $settings['sc_slideshow__useEmbedFont'] = 0;
        $settings['sc_slideshow__target'] = '_blank';
        $settings['slide_source'] = 'local';
		
        $settings['sc_preview__width'] = 240;
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
        $settings['sc_preview__captionFont'] = 'Tahoma';
        $settings['sc_preview__captionSize'] = 11;
        $settings['sc_preview__captionBold'] = 0;
        $settings['sc_preview__captionColor'] = 'ffffff';
        $settings['sc_preview__captionColorBack'] = 'ffffff';
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

        $settings['sc_background__type'] = 'color';
        $settings['sc_background__src'] = '';
        $settings['sc_background__alpha'] = 0.7;
        $settings['sc_background__color'] = 'ffffff';

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
        $settings['sc_navigation__position'] = 'stage';
        $settings['sc_navigation__hideAlbumButton'] = 0;
        $settings['sc_navigation__flipping'] = 'button';
        $settings['sc_navigation__buttons'] = 0;
        $settings['sc_navigation__buttonsColor'] = '000000';
        $settings['sc_navigation__buttonsBackColor'] = 'ffffff';
        $settings['sc_navigation__buttonsAlpha'] = 0.5;

        $settings['sc_controls__fullscreen'] = 0;

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

        $settings['sc_tool_tip__color'] = 'ffffff';
        $settings['sc_tool_tip__alpha'] = 0.5;
        $settings['sc_tool_tip__max_width'] = 180;
        $settings['sc_tool_tip__font'] = 'Tahoma';
        $settings['sc_tool_tip__font_color'] = '000000';
        $settings['sc_tool_tip__font_size'] = 14;
        $settings['sc_tool_tip__time_show'] = 1;
		
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

// returns gallery settings
function fgallery_get_album_settings($gall_id) {
    global $wpdb;
        $settings = $wpdb->get_var("SELECT value FROM ".ALBUMS_SETTINGS_TABLE." WHERE gall_id = ".$gall_id);
        if (empty($settings)) {
            return fgallery_default_album_settings();
        } else {
            return unserialize($settings);
        }
}

// saves the albums settings to db
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

// save changes to gallery into db
function fgallery_edit_album($data,$id) {
    global $wpdb;
    global $current_user;
        $name = htmlentities($data['fgallery_name'], ENT_NOQUOTES, "UTF-8");
        $desc = htmlentities($data['fgallery_description'], ENT_NOQUOTES, "UTF-8");
	$published = $data['fgallery_status'];
	$width = (int)$data['gall_width'];
	if ($width == 0) $width = 450;
	$height = (int)$data['gall_height'];
	if ($height == 0) $height = 385;
	$bgcolor = str_replace('#','',$data['gall_bgcolor']);
	$type = $data['gall_type'];
	if ($id == 0) {
		$user_id = $current_user->ID;//get_current_user_id();
		$max = $wpdb->get_var("SELECT MAX(`gall_order`) FROM ".ALBUMS_TABLE);
		$wpdb->insert(ALBUMS_TABLE, array('gall_name' => $name, 'gall_description' => $desc, 'gall_createddate' => date("Y-m-d H:i:s"), 'gall_createdby' => $user_id, 'gall_published' => $published, 'gall_width'=>$width, 'gall_height'=> $height, 'gall_bgcolor'=> $bgcolor, 'gall_type'=> $type, 'gall_order' => $max + 1));
		return $wpdb->insert_id;
	}
	if ($name != '') {
		$wpdb->update(ALBUMS_TABLE, array('gall_name' => $name, 'gall_description' => $desc, 'gall_published' => $published, 'gall_width'=>$width, 'gall_height'=> $height, 'gall_bgcolor'=> $bgcolor, 'gall_type'=> $type), array('gall_id' => $id));
		return $id;
    } else {
        return 0;
    } 
}

// save changes to image into db
function fgallery_edit_image($data,$id) {
    global $wpdb;
    $name = htmlentities($data['fgallery_image_caption'], ENT_NOQUOTES, "UTF-8");
    $desc = htmlentities($data['fgallery_image_description'], ENT_NOQUOTES, "UTF-8");
    if ($name != '') {
            $wpdb->update(IMAGES_TABLE, array('img_caption' => $name, 'img_description' => $desc), array('img_id' => $id));
            return 1;
    } else {
        return 0;
    } 
}

function fgallery_edit_album_image($data) {
	global $wpdb;
	if (isset($data['img_id']) && is_numeric($data['img_id'])) {
		$id = $data['img_id'];
	} else {
                die('0');
	}
	if (isset($data['gall_id']) && is_numeric($data['gall_id'])) {
		$gall_id = $data['gall_id'];
	} else {
		die('0');
	}
        $action = 'fgallery_edit_image_field_'.$id;
	if (!wp_verify_nonce($_POST['nonce'], 'fgallery_edit')) {
		die('0');
	}
	$name = htmlentities(urldecode($data['img_caption']), ENT_NOQUOTES, "UTF-8");
        $desc = htmlentities(urldecode($data['img_description']), ENT_NOQUOTES, "UTF-8");
	$url = urldecode($data['img_url']);
        $img_extra = array('img_type'=>$data['img_type']);
        $extra = serialize($img_extra);
	if ($name != '') {
		$wpdb->update(IMAGES_TABLE, array('img_caption' => $name, 'img_description' => $desc), array('img_id' => $id));
		$wpdb->update(IMAGES_TO_ALBUMS_TABLE, array('img_url' => $url, 'img_extra' => $extra), array('img_id' => $id, 'gall_id' => $gall_id));
                die('1');
        } else {
                die('0');
        } 
}

// deletes image from db and deletes file
function fgallery_delete_image($id) {
    global $wpdb;
        if (fgallery_access_level()>=5) {		
                $wpdb->query("DELETE FROM ".IMAGES_TO_ALBUMS_TABLE." WHERE `img_id` = ".$id);
                $image = fgallery_get_image($id);
                if ($image['img_vs_folder'] == 1){
                                $wpdb->query("UPDATE ".IMAGES_TABLE." SET `img_parent` = 0 WHERE `img_parent` =".$id);
                                $wpdb->query("DELETE FROM ".IMAGES_TABLE." WHERE `img_id` = ".$id);
                } elseif ($image['img_vs_folder'] == 0) {
                        // we shouldn't delete wp media files so .....
                        if (strpos($image['img_path'],'fgallery') === false) {
                                $wpdb->query("DELETE FROM ".IMAGES_TABLE." WHERE `img_id` = ".$id);
                        } else {
                                unlink(ABSPATH.$image['img_path']);
                                $wpdb->query("DELETE FROM ".IMAGES_TABLE." WHERE `img_id` = ".$id);
                        }
                }
            return true;
        } else {
            return false;
        }
}

// deletes gallery from db (does not delete files)
function fgallery_delete_album($id) {
	global $wpdb;
        if (fgallery_access_level()>=8) {
		$wpdb->query("DELETE FROM ".IMAGES_TO_ALBUMS_TABLE." WHERE `gall_id` = ".$id);
		$wpdb->query("DELETE FROM ".ALBUMS_SETTINGS_TABLE." WHERE `gall_id` = ".$id);
		$wpdb->query("DELETE FROM ".ALBUMS_TABLE." WHERE `gall_id` = ".$id);
	return true;
        } else {
            return false;
        }
}

// returns the way of gallery sorting
function fgallery_sort_albums_condition($sort) {
    switch ($sort){
        case 0:
            return 'gall_name ASC';
            break;
        case 1:
            return 'gall_name DESC';
            break;
        case 2:
            return 'gall_createddate ASC';
            break;
        case 3:
            return 'gall_createddate DESC';
            break;
        case 4: 
            return 'gall_order ASC';
            break;
        default:
            return 'gall_order ASC';
            break;
    }
}

// returns the way of image sorting
function fgallery_sort_images_condition($sort) {
    switch ($sort){
        case 0:
            return 'img_caption ASC';
            break;
        case 1:
            return 'img_caption DESC';
            break;
        case 2:
            return 'img_date ASC';
            break;
        case 3:
            return 'img_date DESC';
            break;
        case 4:
            return 'img_size ASC';
            break;
        case 5:
            return 'img_size DESC';
            break;
    }
}

function fgallery_get_nextgen_images() {
	global $wpdb;
		$images = $wpdb->get_results("SELECT a.pid, a.image_slug, a.filename, b.path FROM ".$wpdb->prefix."ngg_pictures as a LEFT JOIN ".$wpdb->prefix."ngg_gallery as b ON (a.galleryid = b.gid)");
	return $images;
}

// returns all folders
function fgallery_get_folders(){
	global $wpdb;
		$folders = $wpdb->get_results("SELECT * FROM " . IMAGES_TABLE." WHERE `img_vs_folder` = 1 ORDER BY `img_caption`", 'ARRAY_A');
	return $folders;
}

// Returns all albums 
function fgallery_get_albums($pagenum, $per_page, $sort) {
	global $wpdb;
        $cond = fgallery_sort_albums_condition($sort);
		$albums = $wpdb->get_results("SELECT * FROM " . ALBUMS_TABLE." ORDER BY ".$cond." LIMIT ".($pagenum-1)*$per_page.",".$pagenum*$per_page, 'ARRAY_A');
	return $albums;
}

// counts the number of galleries
function fgallery_albums_count() {
	global $wpdb;
		$count = $wpdb->get_var("SELECT COUNT(*) FROM " .ALBUMS_TABLE);
	return $count;
}

function fgallery_count_gallery_album_images($folder) {
	global $wpdb;
		$count = $wpdb->get_var("SELECT COUNT(*) FROM ".IMAGES_TO_ALBUMS_TABLE." WHERE `gall_folder` = ".$folder);
	return $count;
}

// returns all the images
function fgallery_get_images($pagenum, $per_page, $parent = 0, $sort = 3) {
	global $wpdb;
        $cond = fgallery_sort_images_condition($sort);
		$images = $wpdb->get_results("SELECT * FROM ". IMAGES_TABLE. " WHERE `img_parent` = ".$parent." AND `img_vs_folder` IN (0,1) ORDER BY `img_vs_folder` DESC, ".$cond." LIMIT ".($pagenum-1)*$per_page.",".$pagenum*$per_page, 'ARRAY_A');
	return $images;
}

// counts the number of images in the folder (0 - root folder or not in any folder)
function fgallery_images_count($folder) {
	global $wpdb;
		$count = $wpdb->get_var("SELECT COUNT(*) FROM " .IMAGES_TABLE." WHERE `img_parent` = ".$folder." AND `img_vs_folder` IN (0,1)");
	return $count;
}

// returns defined gallery
function fgallery_get_album($id) {
	global $wpdb;
		$album = $wpdb->get_row("SELECT * FROM ".ALBUMS_TABLE." WHERE gall_id = ".$id, ARRAY_A);
	return $album;
}

// returns defined image
function fgallery_get_image($id) {
	global $wpdb;
		$image = $wpdb->get_row("SELECT * FROM ".IMAGES_TABLE." WHERE img_id = ".$id, ARRAY_A);
	return $image;
}

// returns the images from defined gallery
function fgallery_get_album_images($gall_id, $folder = 0) {
	global $wpdb;
		$items = $wpdb->get_results("SELECT a.*, b.* FROM " . IMAGES_TABLE . " as a LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE." as b ON (a.img_id = b.img_id) WHERE b.gall_id = " .$gall_id." AND b.gall_folder = ".$folder." ORDER BY a.img_vs_folder DESC, b.img_order ASC", 'ARRAY_A');
	return $items;
}

// returns the galleries that have the defined image in them
function fgallery_image_get_albums($id) {
	global $wpdb;
		$albums = $wpdb->get_results("SELECT a.gall_name, a.gall_id FROM ". ALBUMS_TABLE ." as a LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE. " as b ON (a.gall_id = b.gall_id) WHERE b.img_id =".$id, ARRAY_A);
	return $albums;
}

// returns the folder name
function fgallery_get_folder_name($id) {
	global $wpdb;
		$name = $wpdb->get_var("SELECT `img_caption` FROM ".IMAGES_TABLE." WHERE `img_id` =".$id);
	return $name;
}

function fgallery_get_settings_param($param, $settings) {
$name = (string)$param['element_name'];
	if ($param['values'] == 'true') {
		return $settings[$name] ? 'true' : 'false';
	} else {
		return $settings[$name];
	}
}

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

/*
 *  Starting the configurator rendering
 */

function sc_params_pane($album) {
  $gall_id = $album['gall_id'];
  $type = $album['gall_type'];
  $form = '<form method="post" action="'.fgallery_get_edit_url($gall_id).'" id="sc-configurator-form" onSubmit="return fgallery_checkform()"><div>';
  $params_xml = simplexml_load_file(FGALLERY_ABSPATH . '/xml/params_'.$type.'.xml');
  $settings = fgallery_get_album_settings($gall_id);
  // Preparing jQuery UI tabs
  $tab_controls = "<ul>";
	$tab_controls .= '<li><a href="#sc-group-general">' . __('General','fgallery') . '</a></li>';
  foreach ($params_xml->params->group as $g) {
	// if php 4 is running
	if (FGALLERY_PHP4_MODE) {
		$temp = $g->attributes();
		$g = array();
		$g['name'] = $temp['name'];
		$g['title'] = $temp['title'];
	}
	// endif
	 $tab_controls .= '<li><a href="#sc-group-' . $g['name'] . '">' . $g['title'] . '</a></li>';
	}

  $tab_controls .= "</ul>";
  $form .= $tab_controls;
	$form .= fgallery_render_edit_album_form($album, $settings_xml->gallery->source);
  // Form the list of parameters in groups
  // if php 4 is running
  if (FGALLERY_PHP4_MODE) {
	  foreach ($params_xml->params->group as $g) {
		$temp = $g->attributes();
		$form .= '<div id="sc-group-' . $temp['name'] . '" class="sc-skin-params-tab-panel">';
		$zebra = 'even';
		foreach ($g->p as $p) {
		  $p_temp = $p->attributes();
		  if (function_exists($fn = 'sc_controls_' . $p_temp['control'])) {
			$p_temp['element_name'] = 'sc_' . $temp['name'] . '__' . $p_temp['name'];
			$p_temp['default'] = fgallery_get_settings_param($p_temp, $settings);
			$p_temp['zebra'] = $zebra;
			$form .= $fn($p_temp);
			$zebra = $zebra == 'even' ? 'odd' : 'even';
		  }
		}
		$form .= '</div>';
		}
	//end php 4 clause
  } else { 
	// if php 5 is running
	  foreach ($params_xml->params->group as $g) {
		$form .= '<div id="sc-group-' . $g['name'] . '" class="sc-skin-params-tab-panel">';
		$zebra = 'even';
		foreach ($g->p as $p) {
		  if (function_exists($fn = 'sc_controls_' . $p['control'])) {
			$p['element_name'] = 'sc_' . $g['name'] . '__' . $p['name'];
			$p['default'] = fgallery_get_settings_param($p, $settings);
			$p['zebra'] = $zebra;
			$form .= $fn($p);
			$zebra = $zebra == 'even' ? 'odd' : 'even';
		  }
		}
		$form .= '</div>';
		}
	// end php condition
   }
	// "Save" button
  $form .= '<input name="sc_submit"  type="submit" value="'.__('Save').'" /><span id="configurator_message"></span>';
  $form .= wp_nonce_field('fgallery_settings','fgallery_settings_field');
  $form .= '</div></form>';
  return $form;
}

/**
 * Element text
 */
function sc_controls_text($p) {
  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="text" value="' . (string)$p['default'] .'" size="10" maxlength="255" class="form-text '.$p['class'].'"/>'
       . '</div>';
}

/**
 * Element - checkbox.
 */
function sc_controls_checkbox($p) {
  $checked = (int)$p['default'] ? 'checked="checked"' : '';
  if ($checked == '' && (string)$p['default'] == 'true') $checked = 'checked = "checked"';
  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="checkbox" ' . $checked . ' class="form-checkbox" value="'.$p['values'].'"/>'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . '</label>'
       . '</div>';
}

/**
 * Element - select.
 */
function sc_controls_select($p) {
  $options_raw = explode(',', $p['values']);
  $options = '';
  foreach ($options_raw as $o) {
    $selected = (string) $p['default'] == $o ? 'selected="selected"' : '';
    $options .= '<option value="' . $o . '" ' . $selected . '>' . $o . '</option>';
  }
  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<select name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" class="form-select">' . $options . '</select>'
       . '</div>';
}

/**
 * Element - slider (jQuery UI slider).
 */
function sc_controls_slider($p) {
  $values = explode(':', $p['values']); // format "1..5:1"
  $range = $values[0];
  $range = explode('..', $range);
  $min = $range[0];
  $max = $range[1];

  $step = $values[1];

  // Validate values
  if (!is_numeric($step) || !is_numeric($min) || !is_numeric($max)) {
    return '<div class="form-item ' . $p['zebra'] . '" >Parse error</div>';
  }

  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="text" value="' . $p['default'] .'" size="3" maxlength="3" readonly="readonly" class="sc-slider-val form-text" min="' . $min . '" max="' . $max . '" step="' . $step .'"/>'
       . '</div>';
}

/**
 * Form element - color picker (Farbtastic).
 */
function sc_controls_color($p) {
  return '<div class="form-item ' . $p['zebra'] . '" >'
       . '<label for="' . $p['element_name'] . '">' . $p['title'] . ':</label>'
       . '<input name="' . $p['element_name'] . '" id="' . $p['element_name'] . '" type="text" value="' . str_replace('0x', '#', $p['default']) .'" size="10" maxlength="7" class="sc-color-val form-text"/>'
       . '</div>';
}



// Renders the list of all albums
function fgallery_render_albums_table($albums){
    $output .= '<table class="widefat fixed">
		  <thead>
			<tr>
                            <th id="cb" class="manage-column check-column column-cb" scope="col" ><input type="checkbox" name="check_all" class="check_all" /></th>
                            <th class="gall_id" scope="col">'.__('ID').'</th>
                            <th id="title" scope="col">'.__('Name','fgallery').'</th>
                            <th class="gall_cover" scope="col">'.__('Cover','fgallery').'</th>
                            <th class="gall_description" scope="col">'.__('Description','fgallery').'</th>
                            <th class="gall_attr" scope="col">'.__('Attributes','fgallery').'</th>
                            <th class="fgallery_actions" scope="col">'.__('Actions','fgallery').'</th>
			</tr>
		  </thead>';
    $output .= '<tfoot>
			<tr>
                            <th class="manage-column check-column column-cb" scope="col" ><input type="checkbox" name="check_all" class="check_all" /></th>
                            <th class="gall_id" scope="col">'.__('ID').'</th>
                            <th scope="col">'.__('Name','fgallery').'</th>
                            <th class="gall_cover" scope="col">'.__('Cover','fgallery').'</th>
                            <th class="gall_description" scope="col">'.__('Description','fgallery').'</th>
                            <th class="gall_attr" scope="col">'.__('Attributes','fgallery').'</th>
                            <th class="fgallery_actions" scope="col">'.__('Actions','fgallery').'</th>
			</tr>
		  </tfoot><tbody>';
    if (!empty($albums)) {
            foreach ($albums as $album) {
                    $output .= fgallery_render_album_item_row($album);
            }
    } else {
            $output .= '<tr><td colspan="7">'.__('There are no galleries yet. You can create new one using Add gallery menu','fgallery'). '</td></tr>';
    }
    $output .= '</tbody></table>';
    return $output;
}

// renders the row in the albums list
function fgallery_render_album_item_row($album) {
	$id = $album['gall_id'];
	$output = '<tr class="fgallery_album" id="album_'.$album['gall_id'].'">';
		$output .= '<th class="check-column"><input type="checkbox" value="'.$album['gall_id'].'" name="gallery[]" /></th>';
		$output .= '<td>'.$id.'<span class="order" style="display:none">'.$album['gall_order'].'</span></td>';
		$output .= '<td><a href="'.fgallery_get_edit_url($id).'">'.$album['gall_name'].'</a></td>';
		$output .= '<td><a href="'.fgallery_get_edit_url($id).'">'.fgallery_get_album_cover($album).'</a></td>';
		$output .= '<td>'.$album['gall_description'].'</td>';
		$output .= '<td>'.fgallery_get_album_attributes($album).'</td>';
		$output .= '<td>'.fgallery_get_album_actions($id).'</td>';
	$output .= '</tr>';
	return $output;
}

function fgallery_render_album_image_form($item, $gall_id, $type) {
	if ($gall_id == '') {
		return '';
	}

	$output .= '<p><label for="fgalleryImageCaption_'.$item['img_id'].'">'.__('Caption:', 'fgallery').'</label>
				<input type="text" id="fgalleryImageCaption_'.$item['img_id'].'" name="fgallery_image_caption" value="'.$item['img_caption'].'" /></p>';
	$output .= '<p><label for="fgalleryImageDescription_'.$item['img_id'].'">'.__('Description:', 'fgallery').'</label>
					<textarea cols="30" rows="2" id="fgalleryImageDescription_'.$item['img_id'].'" name="fgallery_image_description">'.$item['img_description'].'</textarea></p>';
	$output .= '<p><label for="fgalleryImageURL_'.$item['img_id'].'">'.__('URL:', 'fgallery').'</label>
				<input type="text" id="fgalleryImageURL_'.$item['img_id'].'" name="fgallery_image_url" value="'.$item['img_url'].'" /></p>';
	if ($type == 7) {
            $item_img_type = unserialize($item['img_extra']);
            if ($item_img_type['img_type'] == 'page') {
                $selected_page = ' selected="selected"';
                $selected_spread = '';
            } else {
                $selected_spread = ' selected="selected"';
                $selected_page = '';
            }
            $output .= '<p><label for="fgalleryImageType_'.$item['img_id'].'">'.__('Image Type:','fgallery').'</label>
                <select name="img_type" id="fgalleryImageType_'.$item['img_id'].'">
                        <option value="page"'.$selected_page.'>'.__('Page','fgallery').'</option>                    
                        <option value="spread"'.$selected_spread.'>'.__('Spread','fgallery').'</option>                 
                    </select></p>';
        }
        $output .= wp_nonce_field('fgallery_edit','fgallery_edit_image_field_'.$item['img_id']);
	$output .= '<input type="hidden" name="gall_id" id="fgalleryImageGall_'.$item['img_id'].'" value="'.$gall_id.'" />';
	$output .= '<a rel="'.$item['img_id'].'" class="save_album_image" href="javascript:void(0);">'.__('Save').'</a>';
	return $output;
}

// renders the list of album images on gallery edit page
function fgallery_render_album_images($items, $gall_id) {
	$title = __('Remove', 'fgallery');
	$title_2 = __('Set as cover', 'fgallery');
	$title_3 = __('Edit','fgallery');
	$album = fgallery_get_album($gall_id);
        
	$output = '<form method="post" action="'.fgallery_get_album_images_url($gall_id).'">';
	$output.= fgallery_get_listof_gallery_albums($gall_id);
	$output.= '<ul class="fgallery_list" id="'.$gall_id.'">';
        $output .= '<a class="save_all_album_images" href="javascript:void(0)">'.__('Save All Changes').'</a>';
	foreach ($items as $item) {
		if ($album['gall_cover'] == $item['img_id']) {
			$cover_class = 'album_cover';
		} else {
			$cover_class = '';
		}
		$output .= '<li id="image_'.$item['img_id'].'" class="'.$cover_class.'"><div class="image_wrap">
                <div class="fgallery_image_info">';
                if ($item['img_vs_folder'] != 2) {
                        $output .= '<input type="checkbox" name="images[]" value="'.$item['img_id'].'" />';
                }
                $output .= '</div>
                <div class="fgallery_image">';
                if ($item['img_vs_folder'] == 0){		
                        $output .= '<img src="'.COVER_PATH.$item['img_path'].'" alt="'.$item['img_caption'].'" />';
                }
                $output .= '</div>
                <div class="fgallery_image_info album_image_form">';
                if ($item['img_vs_folder'] == 2){
                        $output .= '<b><a href="'.fgallery_get_album_images_url($gall_id).'&folder='.$item['img_id'].'">'.$item['img_caption'].'</a></b>';
                        $output .= '('.fgallery_count_gallery_album_images($item['img_id']).')';
                } else {
                        $output .= fgallery_render_album_image_form($item, $gall_id, $album['gall_type']);
                }
                $output .='</div>
                <div class="fgallery_image_actions">
                        <a href="'.fgallery_get_image_edit_url($item['img_id']).'" title="'.$title_3.'">'.$title_3.'</a>
                        <a href="javascript:void(0);" class="image_remove" rel="image_'.$item['img_id'].'" 
                            id="image_'.$item['img_id'].'_'.$gall_id.'" title="'.$title.'">'.$title.'</a>';
                        if ($item['img_vs_folder'] != 2) {
                                $output .= '<a href="javascript:void(0);" class="image_cover" rel="image_'.$item['img_id'].'"
                                    id="cover_'.$item['img_id'].'_'.$gall_id.'" title="'.$title_2.'">'.$title_2.'</a>';
                        }
                $output .='</div></div></li>';
	}
        $output .= '<a class="save_all_album_images" href="javascript:void(0)">'.__('Save All Changes').'</a>';
	$output .= '</ul>';
	$output .= '</form>';
	return $output;
}

// renders the list of images
function fgallery_render_images_table($images){
$output .= '<table class="widefat fixed">
		  <thead>
			<tr>
				<th id="cb" class="manage-column check-column column-cb" scope="col" ><input type="checkbox" name="check_all" class="check_all" /></th>
				<th class="gall_id" scope="col">'.__('ID').'</th>
				<th id="title" scope="col">'.__('Caption', 'fgallery').'</th>
				<th class="gall_cover" scope="col">'.__('Preview', 'fgallery').'</th>
				<th class="gall_description" scope="col">'.__('Description', 'fgallery').'</th>
				<th class="gall_attr" scope="col">'.__('Attributes', 'fgallery').'</th>
				<th class="fgallery_actions" scope="col">'.__('Actions', 'fgallery').'</th>
			</tr>
		  </thead>';
$output .= '<tfoot>
			<tr>
				<th class="manage-column check-column column-cb" scope="col" ><input type="checkbox" name="check_all" class="check_all" /></th>
				<th class="gall_id" scope="col">'.__('ID').'</th>
				<th scope="col">'.__('Caption', 'fgallery').'</th>
				<th class="gall_cover" scope="col">'.__('Preview', 'fgallery').'</th>
				<th class="gall_description" scope="col">'.__('Description', 'fgallery').'</th>
				<th class="gall_attr" scope="col">'.__('Attributes', 'fgallery').'</th>
				<th class="fgallery_actions" scope="col">'.__('Actions', 'fgallery').'</th>
			</tr>
		  </tfoot><tbody>';
	if (isset($_GET['folder'])) {
		$output .=  '<tr class="droppable" id="folder_0"><td colspan="7"><a class="go_up" href="'.fgallery_images_url().'">'.__('Go up', 'fgallery').'...</a></td></tr>';
	}
	if (!empty($images)) {
		foreach ($images as $image) {
			$output .= fgallery_render_image_item_row($image);
		}
	} else {
		$output .= '<tr><td colspan="7">'.__('There are no images yet. You can upload new ones using Upload images menu', 'fgallery'). '</td></tr>';
	}

	$output .= '</tbody></table>';
	return $output;
}

// renders the row in the albums list
function fgallery_render_image_item_row($image) {
	$id = $image['img_id'];
	if ($image['img_vs_folder']){
		$output = '<tr class="fgallery_album droppable" id="folder_'.$id.'">';
	} else {
		$output = '<tr class="fgallery_album draggable" id="image_'.$id.'">';
	}
		$output .= '<th class="check-column"><input type="checkbox" value="'.$id.'" name="image[]" /></th>';
		$output .= '<td>'.$id.'</td>';
		
		if ($image['img_vs_folder']) {
			$output .= '<td><a href="'.fgallery_get_folder_url($id).'">'.$image['img_caption'].'</a></td>';
			$output .= '<td><a href="'.fgallery_get_folder_url($id).'"><img src="'.FGALLERY_PATH.'/images/folder.png" alt="'.$image['img_caption'].'"/></a></td>';
			$output .= '<td>'.$image['img_description'].'</td>';
			$output .= '<td class="folder_attr">'.fgallery_get_folder_attributes($image).'</td>';
			$output .= '<td>'.fgallery_get_folder_actions($id).'</td>';
		} else {
			$output .= '<td><a href="'.fgallery_get_image_edit_url($id).'">'.$image['img_caption'].'</a></td>';
			$output .= '<td><a href="'.fgallery_get_image_edit_url($id).'"><img src="'.COVER_PATH.$image['img_path'].'" alt="'.$image['img_caption'].'"/></a></td>';
			$output .= '<td>'.$image['img_description'].'</td>';
			$output .= '<td>'.fgallery_get_image_attributes($image).'</td>';
			$output .= '<td>'.fgallery_get_image_actions($id).'</td>';
		}
	$output .= '</tr>';
	return $output;
}

function fgallery_get_listof_gallery_albums($id) {
	global $wpdb;
	$albums =$wpdb->get_results("SELECT a.img_caption, a.img_id FROM ".IMAGES_TABLE." as a LEFT JOIN ".IMAGES_TO_ALBUMS_TABLE." as b ON (a.img_id = b.img_id) WHERE a.img_vs_folder = 2 AND b.gall_id = ".$id, ARRAY_A);
	if (count($albums) > 0) {
		$output = '<select name="album_id" id="album_id_images">';
			$output .= '<optgroup label="'.__('Choose album', 'fgallery').'">';
		foreach ($albums as $album) {
			$output .= '<option value="'.$album['img_id'].'">'.$album['img_caption'].'</option>';
		}
		$output .= '</optgroup>';
		$output .= '<optgroup label="'.__('Actions').'">';
			$output .= '<option value="-1">'.__('Remove selected').'</option>';
		$output .= '</optgroup>';
		$output .= '</select>';
		$output .= '<input type="hidden" value="'.$id.'" name="gall_id" />';
		$output .= '<input type="submit" value="'.__('Go', 'fgallery').'" />';
		
	}
	$output .= '<br clear="all" />';
	return $output;
}

function fgallery_create_thumb_url($width) {
	if (is_numeric($width)) {
            if (FGALLERY_PHP4_MODE) {
                return WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image_5.php?width='.$width.'&amp;filename='.EXTRA_DIR;
            } else {
                return WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/show_image.php?width='.$width.'&amp;filename='.EXTRA_DIR;
            }
	} else {
		return COVER_PATH;
	}
}

// returns the wrapped in img tag album cover
function fgallery_get_album_cover($album, $width ='') {
global $wpdb;
$gall_id = $album['gall_id'];
	if ($album['gall_cover'] != 0){
		$cover_id = $album['gall_cover'];
		$cover = $wpdb->get_row("SELECT `img_path`, `img_caption` FROM " . IMAGES_TABLE . " WHERE `img_id` = " .$cover_id." LIMIT 1", 'ARRAY_A');
	} else {
		$cover = $wpdb->get_row("SELECT a.`img_path`, a.`img_caption` FROM " . IMAGES_TABLE . " as a LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE ." as b ON (a.img_id = b.img_id) WHERE b.gall_id = " .$gall_id." ORDER BY b.img_order ASC LIMIT 1", 'ARRAY_A');
	}
	if ($cover['img_path']!=''){
		$gall_cover = '<img src="'.fgallery_create_thumb_url($width).$cover['img_path'].'" alt="'.$cover['img_caption'].'" />';
	}
	return $gall_cover;
}

//returns rendered album attributes (image quantity, total size, publish date and etc.)
function fgallery_get_album_attributes($album, $extra = true) {
global $wpdb;
$id = $album['gall_id'];
	$attr = $wpdb->get_row("SELECT COUNT(a.img_id) as 'quantity', SUM(a.img_size) as 'total_size' FROM " . IMAGES_TABLE . " as a LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE ." as b ON (a.img_id = b.img_id) WHERE b.gall_id = " .$id." AND a.img_vs_folder <> 2", 'ARRAY_A');
	$output = __('Number of photos:', 'fgallery').$attr['quantity']. '<br />';
	$output .= __('Total size:','fgallery').formatBytes($attr['total_size']). '<br />';
	if ($extra) {
		if ($album['gall_published']) {
			$status = __('Published');
		} else {
			$status = __('Draft');
		}
		$output .= __('Gallery status:','fgallery').$status. '<br />';
		$output .= date("d F Y",strtotime($album['gall_createddate'])). '<br />';
	}
	return $output;
}

//returns rendered album attributes
function fgallery_get_image_attributes($image){
	$id = $image['img_id'];
	$albums = fgallery_image_get_albums($id);
	$albums_name = '';
	if (count($albums)>0){
		foreach ($albums as $album) {
			$albums_name .= $album['gall_name'].',';
		}
		$albums_name = substr($albums_name, 0, -1);
	} else {
		$albums_name = __('Image is not in any gallery', 'fgallery');
	}
	$output = __('Galleries:', 'fgallery').$albums_name.'<br />';
	$output.= __('Image type:', 'fgallery').$image['img_type'].'<br />';
	$output.= __('Image size:', 'fgallery').formatBytes($image['img_size']).'<br />';
	$output.= __('Upload date:', 'fgallery').date("d F Y",strtotime($image['img_date']));
	return $output;
}

//returns rendered folder attributes 
function fgallery_get_folder_attributes($image){
	global $wpdb;
	if ($image['img_vs_folder']) {
		$id = $image['img_id'];
		$attr = $wpdb->get_row("SELECT COUNT(a.img_id) as 'quantity', SUM(a.img_size) as 'total_size' FROM " . IMAGES_TABLE . " as a  WHERE a.img_parent = " .$id, 'ARRAY_A');
			$output = __('Images:', 'fgallery').$attr['quantity'].'<br />';
			$output.= __('Images size:', 'fgallery').formatBytes($attr['total_size']).'<br />';
			$output.= __('Create date:', 'fgallery').date("d F Y",strtotime($image['img_date']));
		}
	return $output;
}

//renders the album actions
function fgallery_get_album_actions($id) {
	$title_1 = __('Add images','fgallery');
	$title_2 = __('Edit gallery','fgallery');
	$title_3 = __('Delete gallery','fgallery');
	$title_4 = __('Edit images', 'fgallery');
	$output = '<a href="'.fgallery_get_addimages_url($id).'" title="'.$title_1.'" class="fgallery_action thickbox">'.$title_1.'</a>';
	$output .= '<a href="'.fgallery_get_edit_url($id).'" title="'.$title_2.'" class="fgallery_action">'.$title_2.'</a>';
	$output .= '<a href="'.fgallery_get_album_images_url($id).'" title="'.$title_4.'" class="fgallery_action">'.$title_4.'</a>';
	if (fgallery_access_level()>= 8){
            $output .= '<a href="javascript:void(0);" rel="album_'.$id.'" title="'.$title_3.'" class="fgallery_action delete">'.$title_3.'</a>';
        }
        return $output;
}

//renders the folder actions
function fgallery_get_folder_actions($id) {
	$title_2 = __('Change name', 'fgallery');
	$title_3 = __('Delete folder', 'fgallery');
	$output .= '<a href="'.fgallery_get_image_edit_url($id).'" title="'.$title_2.'" class="fgallery_action">'.$title_2.'</a>';
	$output .= '<a href="#" class="fgallery_action create_from_folder" rel="'.$id.'">'.__('Create gallery', 'fgallery').'</a>';
	if (fgallery_access_level()>= 5){
            $output .= '<a href="javascript:void(0);" rel="folder_'.$id.'" title="'.$title_3.'" class="fgallery_action delete">'.$title_3.'</a>';
        }
        return $output;
}

//renders the image actions
function fgallery_get_image_actions($id) {
	$title_2 = __('Edit image', 'fgallery');
	$title_3 = __('Delete image', 'fgallery');
	$output = '<a href="'.fgallery_get_image_edit_url($id).'" title="'.$title_2.'" class="fgallery_action">'.$title_2.'</a>';
	if (fgallery_access_level()>= 5){
            $output .= '<a href="javascript:void(0);" rel="image_'.$id.'" title="'.$title_3.'" class="fgallery_action delete">'.$title_3.'</a>';
        }
        return $output;
}

// renders the shortcode for inserting the gallery into the content
function fgallery_do_shortcode($id, $type = 0) {
    $album = fgallery_get_album($id);
    return sprintf('[fgallery id=%d w=%d h=%d t=%d title="%s"]',$id, $album['gall_width'], $album['gall_height'], $type, trim($album['gall_name']));
}

// renders the gaellery edit form
function fgallery_render_edit_album_form($album, $source) {
	$id = $album['gall_id'];
	$output .= '<div id="sc-group-general" class="sc-skin-params-tab-panel">';
	$output .= '<div class="form-item even"><label for="fgalleryName">'.__('Name:', 'fgallery').'</label>
				<textarea cols="30" rows="5" id="fgalleryName" name="gallery[fgallery_name]" class="required">'.$album['gall_name'].'</textarea>
				</div>';
	$output .= '<div class="form-item odd"><label for="fgalleryDescription">'.__('Description:','fgallery').'</label>
				<textarea cols="30" rows="5" id="fgalleryDescription" name="gallery[fgallery_description]">'.$album['gall_description'].'</textarea>
				</div>';
	$output .= '<div class="form-item even" style="display:none;">
				<label for="fgallery_status">'.__('Status', 'fgallery').'</label>
					<select name="gallery[fgallery_status]" id="fgallery_status" class="form-select">
						<option value="1" '.$pubselect.'>'.__('Published').'</option>';
        $output .= '</select></div>';
	$output .= '<div class="form-item odd" style="display:none;">
		<label for="slide_source">'.__('Source', 'fgallery').'</label>
		<select name="slide_source" id="slide_source" class="form-select">
			<option value="local" '.$opt['local'].'>'.__('Local').'</option>';
	$output .= '</select></div>';
	$output .= '<div class="form-item even">
					<label for="fgalleryWidth">'.__('Width:', 'fgallery').'</label>
					<input type="text" id="fgalleryWidth" name="gallery[gall_width]" class="form-text numeric" value="'.$album['gall_width'].'" />
				</div>';
	$output .= '<div class="form-item odd">
					<label for="fgalleryHeight">'.__('Height:', 'fgallery').'</label>
					<input type="text" id="fgalleryHeight" name="gallery[gall_height]" class="form-text numeric" value="'.$album['gall_height'].'" />
				</div>';
	$output .= '<div class="form-item even" style="display:none;">
					<label for="fgalleryBgcolor">'.__('Background Color:', 'fgallery').'</label>
					<input type="text" id="fgalleryBgcolor" name="gallery[gall_bgcolor]" class="sc-color-val form-text" value="#'.$album['gall_bgcolor'].'" />
				</div>';
	$opt = array();
	$opt[$album['gall_type']] = 'selected ="selected"';
	$output .= '<div class="form-item odd">
					<label for="gall_type"><b>'.__('Gallery Type:', 'fgallery').'</b></label> 
		<select name="gallery[gall_type]" id="gall_type" class="form-select" onchange="save_settings();">
			<option value="1" '.$opt[1].'>'.__('Acosta').'</option>
			<option value="2" '.$opt[2].'>'.__('Airion').'</option>
			<option value="3" '.$opt[3].'>'.__('Arai').'</option>
			<option value="4" '.$opt[4].'>'.__('Pax').'</option>
			<option value="5" '.$opt[5].'>'.__('Pazin').'</option>
			<option value="6" '.$opt[6].'>'.__('Postma').'</option>
			<option value="7" '.$opt[7].'>'.__('Pageflip').'</option>
			<option value="8" '.$opt[8].'>'.__('Nilus').'</option>
			<option value="9" '.$opt[9].'>'.__('Nusl').'</option>
			<option value="10" '.$opt[10].'>'.__('Kranjk').'</option>
		</select></div>';
	$output .= '</div>';

	return $output;
}

function fgallery_get_flash_type($album) {
	switch ($album['gall_type']) {
		case 1: return 'acosta';
		case 2: return 'airion';
		case 3: return 'arai';
		case 4: return 'pax';
		case 5: return 'pazin';
		case 6: return 'postma';
		case 7: return 'pageflip';
		case 8: return 'nilus';
		case 9: return 'nusl';
		case 10: return 'kranjk';
		default: return 'arai';
	}
}

// renders the image edit form
function fgallery_render_edit_image_form($image) {
	$id = $image['img_id'];
	$output = '<form method="post" action="'.fgallery_get_image_edit_url($id).'">';
	$output .= '<p><label for="fgalleryImageCaption">'.__('Caption:', 'fgallery').'</label><br /><input type="text" id="fgalleryImageCaption" name="fgallery_image_caption" value="'.$image['img_caption'].'" /></p>';
	if (!$image['img_vs_folder']){
		$output .= '<p><label for="fgalleryImageDescription">'.__('Description:', 'fgallery').'</label><br /><textarea cols="30" rows="5" id="fgalleryImageDescription" name="fgallery_image_description">'.$image['img_description'].'</textarea></p>';
	}
	$output .= wp_nonce_field('fgallery_edit','fgallery_edit_image_field');
	$output .= '<p><input type="submit" name="fgallery_image_submit" value="'.__('Save').'" /></p>';
	$output .='</form>';	
	return $output;
}

// formats the image/folder/gallery size 
function formatBytes($b,$p = null) {
    /**
     *
     * @author Martin Sweeny
     * @version 2010.0617
     *
     * returns formatted number of bytes.
     * two parameters: the bytes and the precision (optional).
     * if no precision is set, function will determine clean
     * result automatically.
     *
     **/
    $units = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
    $c=0;
    if(!$p && $p !== 0) {
        foreach($units as $k => $u) {
            if(($b / pow(1024,$k)) >= 1) {
                $r["bytes"] = $b / pow(1024,$k);
                $r["units"] = $u;
                $c++;
            }
        }
        return number_format($r["bytes"],2) . " " . $r["units"];
    } else {
        return number_format($b / pow(1024,$p)) . " " . $units[$p];
    }
}

function mixed_to_utf8($data) {
    $search = array('&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;',
                    '&sect;','&uml;','&copy;','&ordf;','&laquo;','&not','&shy;','&reg;',
                    '&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;',
                    '&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;',
                    '&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;',
                    '&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave','&Eacute;',
                    '&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;',
                    '&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;',
                    '&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;',
                    '&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;',
                    '&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;',
                    '&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;',
                    '&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;',
                    '&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;');
    $replace = array('&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;',
                     '&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;',
                     '&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;',
                     '&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;',
                     '&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;',
                     '&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;',
                     '&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;',
                     '&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;',
                     '&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;',
                     '&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;',
                     '&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;',
                     '&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;');
    return str_replace($search,$replace,$data);
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

function fgallery_save_album_image() {
    fgallery_edit_album_image($_POST);
    die(); // this is required to return a proper result
}

function fgallery_sort_album_images(){
    global $wpdb;
    if (isset($_POST['img_id']) && isset($_POST['gall_id']) && is_numeric($_POST['img_id']) && is_numeric($_POST['gall_id'])) {
            $id = $_POST['img_id'];
            $gall_id = $_POST['gall_id'];
            $order = $_POST['order'];
            $wpdb->update(IMAGES_TO_ALBUMS_TABLE, array('img_order' => $order), array('img_id' => $id, 'gall_id' => $gall_id));
    } else {
        die('0');
    }
    die();
}

function fgallery_remove_album_image(){
    global $wpdb;
    if (isset($_POST['img_id']) && isset($_POST['gall_id']) && is_numeric($_POST['img_id']) && is_numeric($_POST['gall_id'])) {
            $id = $_POST['img_id'];
            $gall_id = $_POST['gall_id'];
            $wpdb->query("UPDATE ".IMAGES_TO_ALBUMS_TABLE." SET `gall_folder` = 0 WHERE `gall_folder` =".$id);
            $wpdb->query("DELETE FROM ". IMAGES_TO_ALBUMS_TABLE. " WHERE img_id =".$id." AND gall_id = ".$gall_id);
    }
    die();
}

function fgallery_set_album_cover(){
    global $wpdb;
    if (isset($_POST['img_id']) && isset($_POST['gall_id']) && is_numeric($_POST['img_id']) && is_numeric($_POST['gall_id'])) {
            $id = $_POST['img_id'];
            $gall_id = $_POST['gall_id'];
            $wpdb->update(ALBUMS_TABLE, array('gall_cover'=>$id), array('gall_id' => $gall_id));
    }
    die('1');
}

function fgallery_delete_image_ajax() {
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $id = $_POST['id'];
                fgallery_delete_image($id);
        }  
        die('1');
}

function fgallery_folder_addimage(){
    global $wpdb;
    if (!empty($_POST)) {
	$wpdb->update(IMAGES_TABLE, array('img_parent' => $_POST['folder_id']), array('img_id' => $_POST['img_id']));
	$folder = fgallery_get_image($_POST['folder_id']);
	echo fgallery_get_folder_attributes($folder);
    } else {
            die('Not saved');
    }
    die();
}

function fgallery_delete_gallery(){
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $id = $_POST['id'];
                fgallery_delete_album($id);
        }
    die();
}

function fgallery_sort_galleries(){
    global $wpdb;
    if (isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['order']) && is_numeric($_POST['order'])) {
            $id = $_POST['id'];
            $order = $_POST['order'];
            $wpdb->update(ALBUMS_TABLE, array('gall_order' => $order), array('gall_id' => $id));
    }
    die();
}

/*
* Starting URLs()
*
*/

function fgallery_images_url() {
	return admin_url('admin.php?page=fgallery_images');
}

function fgallery_get_upload_url() {
	return admin_url('admin.php?page=fgallery_upload');
}

function fgallery_createalbum_url() {
	return admin_url('admin.php?page=fgallery_add');
}

function fgallery_get_addimages_url($id) {
	return FGALLERY_PATH.'/addimage.php?id='.$id.'&TB_iframe=1';
}

function fgallery_get_addimages_url_clean($id) {
	return FGALLERY_PATH.'/addimage.php?id='.$id;
}

function fgallery_get_album_images_url($id) {
	return admin_url('admin.php?page=fgallery&amp;action=images&amp;id='.$id);
}

function fgallery_create_folder_url($type,$id) {
	return FGALLERY_PATH.'/folder.php?&amp;action=create&amp;type='.$type.'&amp;gall_id='.$id.'&amp;TB_iframe=1';
}

function fgallery_get_save_settings_url($id, $clean = false) {
	if ($clean) {
		return FGALLERY_PATH.'/templates.php?gall_id='.$id.'&amp;action=save';
	} else {
		return FGALLERY_PATH.'/templates.php?gall_id='.$id.'&amp;action=save&amp;TB_iframe=1';
	}
}

function fgallery_get_export_settings_url($id) {
	return FGALLERY_PATH.'/config.php?gall_id='.$id.'&amp;view=1';
}

function fgallery_get_load_settings_url($id, $clean = false) {
	if ($clean) {
		return FGALLERY_PATH.'/templates.php?gall_id='.$id.'&amp;action=load';
	} else {
		return FGALLERY_PATH.'/templates.php?gall_id='.$id.'&amp;action=load&amp;TB_iframe=1';
	}
}

function fgallery_get_template_delete_url($id, $templ_id) {
	return FGALLERY_PATH.'/templates.php?gall_id='.$id.'&amp;templ_id='.$templ_id.'&amp;action=delete';
}

function fgallery_get_edit_url($id) {
	return admin_url('admin.php?page=fgallery&amp;action=edit&amp;id='.$id);
}

function fgallery_get_edit_url_clean($id) {
	return admin_url('admin.php?page=fgallery&action=edit&id='.$id);
}

function fgallery_get_image_edit_url($id) {
	return admin_url('admin.php?page=fgallery_images&amp;action=edit&amp;id='.$id);
}

function fgallery_get_folder_url($id) {
	return admin_url('admin.php?page=fgallery_images&amp;folder='.$id);
}

function fgallery_upload_uploadify_get_url() {
	return FGALLERY_PATH.'/upload.php?action=uploadify';
}

function fgallery_upload_zip_get_url() {
	return FGALLERY_PATH.'/upload.php?action=unzip';
}

function fgallery_upload_ftp_get_url() {
	return FGALLERY_PATH.'/upload.php?action=ftp';
}

function fgallery_upload_url_get_url() {
	return FGALLERY_PATH.'/upload.php?action=upload_url';
}

function fgallery_upload_media_get_url() {
	return FGALLERY_PATH.'/upload.php?action=media';
}

function fgallery_upload_facebook_get_url() {
	return FGALLERY_PATH.'/upload.php?action=facebook';
}

function fgallery_upload_nextgen_get_url() {
	return FGALLERY_PATH.'/upload.php?action=nextgen';
}

function fgallery_upload_scandir_get_url() {
	return FGALLERY_PATH.'/upload.php?action=scandir';
}

function fgallery_get_massedit_url() {
    return FGALLERY_PATH.'/massedit.php';
}

function fgallery_get_add_album_url(){
    return FGALLERY_PATH.'/insert_gallery.php?TB_iframe=1';
}

function fgallery_get_insert_button_url(){
    return FGALLERY_PATH.'/images/icon_gallery.gif';
}

function fgallery_get_pref_url() {
	return admin_url('admin.php?page=fgallery&action=pref');
}

function fgallery_view_gallery_url($id, $width, $height, $bgcolor) {
	$height = $height+30;
	return FGALLERY_PATH.'/view.php?gall_id='.$id.'&amp;width='.$width.'&amp;height='.$height.'&amp;bg='.$bgcolor.'&amp;TB_iframe=1';
}


/*
 * Filters
 *
 */

add_filter('screen_settings', 'fgallery_screen_settings', 10, 2);
add_filter('contextual_help', 'fgallery_plugin_help', 10, 3);
add_filter('media_buttons_context','fgallery_add_button',9);
add_shortcode( 'fgallery', 'fgallery_shortcode_handler' );


function fgallery_plugin_help($contextual_help, $screen_id, $screen) {
	if ($screen_id == 'toplevel_page_fgallery' || $screen_id == '1-flash-gallery_page_fgallery_add'
		|| $screen_id == '1-flash-gallery_page_fgallery_add' || $screen_id == '1-flash-gallery_page_fgallery_images'
                || $screen_id == '1-flash-gallery_page_fgallery_upload') {
		$contextual_help = '<p><a href="http://1plugin.com/faq" target="_blank">'.__('FAQ').'</a></p>';
	}
	return $contextual_help;
}

// filter for setting the number of elements on the screen
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

function plugin_is_active($plugin_path) {
    $return_var = in_array( $plugin_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
    return $return_var;
  }


function fgallery_search_flash_path($album) {
	$type = fgallery_get_flash_type($album);
	if (get_option('1_flash_gallery_'.fgallery_get_flash_type($album)) !='') {
		if (plugin_is_active('1-flash-gallery-update/update_fgallery.php')) {
			return WP_PLUGIN_URL .'/1-flash-gallery-update/swf/'.fgallery_get_flash_type($album).'.swf';
		} elseif (plugin_is_active('1-flash-gallery-update-'.fgallery_get_flash_type($album).'/update_'.fgallery_get_flash_type($album).'.php')) {
			return WP_PLUGIN_URL .'/1-flash-gallery-update-'.fgallery_get_flash_type($album).'/swf/'.fgallery_get_flash_type($album).'.swf';
		} else {
			return FGALLERY_PATH.'/swf/'.fgallery_get_flash_type($album).'.swf';
		}
	} else {
		return FGALLERY_PATH.'/swf/'.fgallery_get_flash_type($album).'.swf';
	}
}

// filter for replacing gallery shortcode with flash movie itself
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
        ), $atts ));

        if ($t < 0 || $t > 2) {
                $t = 0;
        }
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
                        $rand = rand(0, 150);
                        $path = fgallery_search_flash_path($album);
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
                        $gallery_snippet = '<div style="width:'.$w.'px;margin:'.$margin.';'.$align_text.'">
                        <script type="text/javascript">
                                var flashvars = {settings: "'.FGALLERY_PATH.'/config.php?gall_id='.$id.'", images : "'.FGALLERY_PATH.'/images.php?gall_id='.$id.'"};
                                var params = {bgcolor: "#'.$bg.'", allowFullScreen: "true", wmode: "transparent"};
                                swfobject.embedSWF("'.$path.'", "flashcontent_'.$id.$rand.'", "'.$w.'", "'.$h.'", "10.0.0",false, flashvars, params);
                          </script>
                        <div id="flashcontent_'.$id.$rand.'">'.fgallery_get_album_cover($album, $w).'<br />
                                <strong>You need to upgrade your Flash Player</strong>
                         </div>';
                         if (get_option('1_flash_gallery_'.fgallery_get_flash_type($album)) =='') {
                                $gallery_snippet .= '<div class="fgallery_message"></div>';
                         }
                         $gallery_snippet .= '</div>';
                break;
                case '1' : 
                        if ($title == '') {
                                $insert_text = $album['gall_name'];
                        } else {
                                $insert_text = $title;
                        }
                        $gallery_snippet = '<a href="'.fgallery_view_gallery_url($id, $w, $h, $bg).'"
                                            class="thickbox" title="'.$album['gall_name'].'">'.$insert_text.'</a>
                                            <p>'.fgallery_get_album_attributes($album, false).'</p>';
                break;
                case '2' :
                        $gallery_snippet = '<a href="'.fgallery_view_gallery_url($id, $w, $h, $bg).'"
                                            class="thickbox" title="'.$album['gall_name'].'">'.fgallery_get_album_cover($album, $thumb).'</a>
                                            <p>'.$album['gall_name'].'</p><p>'.fgallery_get_album_attributes($album, false).'</p>';
                break;
        }

   return $gallery_snippet;
}

// filter to add the button (for inserting the gallery into post) to post edit page
function fgallery_add_button($buttons) {
     $fgallery_button = " <a href='" . esc_url( fgallery_get_add_album_url() ) . "' id='insert_gallery' class='thickbox' 
                            title='".__('Insert gallery into post', 'fgallery')."'>
        <img src='" . esc_url( fgallery_get_insert_button_url( ) ) . "' alt='".__('Insert gallery into post', 'fgallery')."' /></a>";
    $buttons .= $fgallery_button;
  return $buttons;
}

class FgalleryWidget extends WP_Widget {
    /** constructor */
    function FgalleryWidget() {
        parent::WP_Widget(false, $name = '1 Flash Gallery Widget', array('description'=>__('Use this widget to place any gallery from 1 Flash Gallery plugin anywhere on your page')));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$gall_id = esc_attr($instance['gall_id']);
		$type = esc_attr($instance['type']);
		$album = fgallery_get_album($gall_id);
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                 <?php echo do_shortcode('[fgallery id='.$gall_id.' w='.$album['gall_width'].' h='.$album['gall_height'].' t='.$type.']');?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['gall_id'] = strip_tags($new_instance['gall_id']);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['type'] = strip_tags($new_instance['type']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $gall_id = esc_attr($instance['gall_id']);
		$title = esc_attr($instance['title']);
		$type = esc_attr($instance['type']);
		$albums = fgallery_get_albums(1,99999999,4);
        ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

            <p>
			<label for="<?php echo $this->get_field_id('gall_id'); ?>"><?php _e('Choose gallery:', 'fgallery'); ?> 
			<select class="widefat" id="<?php echo $this->get_field_id('gall_id'); ?>" name="<?php echo $this->get_field_name('gall_id'); ?>">
				<?php foreach ($albums as $album): ?>
						<option value="<?php echo $album['gall_id']?>" <?if ($album['gall_id'] == $gall_id) echo 'selected="selected"'?>><?php echo $album['gall_name']?></option>
				<?php endforeach; ?>
			</select>
			</label>
			</p>
			<p>
				<input type="radio" name="<?php echo $this->get_field_name('type'); ?>" value="0" <?php if ($type == 0) echo 'checked'?>> <?php _e('Flash object', 'fgallery') ?> <br />
				<input type="radio" name="<?php echo $this->get_field_name('type'); ?>" value="1" <?php if ($type == 1) echo 'checked'?>> <?php _e('Text link to gallery', 'fgallery'); ?> <br />
				<input type="radio" name="<?php echo $this->get_field_name('type'); ?>" value="2" <?php if ($type == 2) echo 'checked'?>> <?php _e('Cover as link to gallery', 'fgallery'); ?><br />
			</p>
        <?php 
    }

} // class FGalleryWidget

add_action('widgets_init', create_function('', 'return register_widget("FgalleryWidget");'));
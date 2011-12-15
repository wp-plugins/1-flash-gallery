<?php

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Provides 1 Flash Gallery Installation
 * 
 * @global type $wpdb
 * @global type $wp_filesystem
 * @return void
 */
// installation script
function fgallery_install() {
   global $wpdb;
   global $wp_filesystem;
   //add option for screen options (number of elements per page)
   add_option('1_flash_gallery_page_fgallery_images_per_page', '25', '', 'no');
   add_option('toplevel_page_fgallery_per_page', '25', '', 'no');
   // watermark options
   add_option('1_flash_gallery_watermark_enabled','0','','no');
   add_option('1_flash_gallery_watermark_path','','','no');
   add_option('1_flash_gallery_watermark_place','','','no');
   // database version to provide updates
   add_option('fgallery_db_version', '0','',no);
   add_option('1_flash_gallery_show_link', '0','',no);
   add_option('1_flash_gallery_preview_size', '200','',no);
   add_option('1_flash_gallery_display_view_size', '1200','',no);
   add_option('1_flash_gallery_save_original', '0','',no);
   add_option('1_flash_gallery_caption_type','0','',no);
   

   if($wpdb->get_var("SHOW TABLES LIKE '" . ALBUMS_TABLE . "'") != ALBUMS_TABLE) {
	add_option("fgallery_db_version", FGALLERY_VERSION);
	// create albums table
	$create_albums = "CREATE TABLE " . ALBUMS_TABLE . " (
                            `gall_id` int(11) NOT NULL auto_increment,
                            `gall_name` varchar(255) collate utf8_general_ci NOT NULL,
                            `gall_description` text collate utf8_general_ci NULL default NULL,
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
                              `img_caption` varchar(255) collate utf8_general_ci NULL,
                              `img_description` text collate utf8_general_ci NULL default NULL,
                              `img_date` datetime NOT NULL,
                              `img_type` varchar(50) collate utf8_general_ci default '',
                              `img_size` int(11) default '0',
                              `img_path` varchar(255) collate utf8_general_ci default NULL,
                              `img_preview_path` varchar(255) collate utf8_general_ci default '',
                              `img_full_view_path` varchar(255) collate utf8_general_ci default '',
                              `img_vs_folder` smallint(5) default '0',
                              `img_parent` int(11) default '0',
                              PRIMARY KEY  (`img_id`)
			);";       
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
                                  `img_url` varchar(255) collate utf8_general_ci NULL default NULL,
                                  `img_extra` text collate utf8_general_ci NULL default NULL,
                                  PRIMARY KEY  (`img_id`,`gall_id`)
                                );";       
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($create_albums_images);
    } 
    
    if($wpdb->get_var("SHOW TABLES LIKE '" . ALBUMS_SETTINGS_TABLE . "'") != ALBUMS_SETTINGS_TABLE) {
	// create table for albums settings
	$albums_settings = "CREATE TABLE " . ALBUMS_SETTINGS_TABLE . " (
                              `gall_id` int(11) NOT NULL,
                              `value` text NOT NULL,
                              PRIMARY KEY  (`gall_id`)
			);";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($albums_settings);
    }
    
    if($wpdb->get_var("SHOW TABLES LIKE '" . TEMPLATES_TABLE . "'") != TEMPLATES_TABLE) {
        // create table for albums settings
        $templates = "CREATE TABLE " . TEMPLATES_TABLE . " (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `gall_type` smallint(5) NOT NULL,
                          `gall_settings` text NOT NULL,
                          `created` date NOT NULL,
                          `templ_title` varchar(255) collate utf8_general_ci NOT NULL,
                          `templ_description` text NULL default NULL,
                          PRIMARY KEY  (`id`)
                    );";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($templates);
    }
    
    $version = get_option('fgallery_db_version', 0);
    
    /**
     *  For WordPress versions lower 3.0 there is no update hook
     *  so here is the update actions
     */
    if (version_compare($version, '1.0.7', '<')) {
       $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_url` VARCHAR( 255 ) collate utf8_general_ci NULL AFTER `img_order`;");
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
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `gall_folder` `gall_folder` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `img_order` `img_order` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_parent` `img_parent` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_vs_folder` `img_vs_folder` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_size` `img_size` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_type` `img_type` varchar(50) collate utf8_general_ci default '';");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_path` `img_path` varchar(50) collate utf8_general_ci default '';");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_cover` `gall_cover` int(11) default 0;");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_description` `gall_description` text collate utf8_general_ci;");
    }
   
    if (version_compare($version, '1.5.1', '<')) {
        add_option('1_flash_gallery_preview_opt','0','','no');
    }
    
    if (version_compare($version, '1.5.6', '<')) {
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_extra` text collate utf8_general_ci NULL default NULL;");
    }
	
    if (version_compare($version,'1.7.9','<')){
        $wpdb->query("ALTER TABLE ".IMAGES_TABLE." 
                        ADD `img_preview_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `img_path`,
                        ADD `img_full_view_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `img_preview_path`;
                     ");
    }
    
    if (version_compare($version, '1.8.0','<')) {
        $wpdb->query("ALTER TABLE ".IMAGES_TABLE." CHANGE `img_path` `img_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }
    
    if (version_compare($version, '1.8.1','<')) {
       $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_description` `img_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
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


/**
 * Provides update of 1 Flash Gallery
 * 
 * @return type 
 */
function fgallery_update() {
    global $wpdb;
    $version = get_option('fgallery_db_version', 0);
    if (version_compare($version, '1.0.7', '<')) {
            $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_url` VARCHAR( 255 ) collate utf8_unicode_ci NULL AFTER `img_order`;");
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

        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `gall_folder` `gall_folder` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `img_order` `img_order` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_parent` `img_parent` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_vs_folder` `img_vs_folder` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_size` `img_size` int( 11 ) default 0;");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_type` `img_type` varchar(50) collate utf8_general_ci default '';");
        $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_path` `img_path` varchar(50) collate utf8_general_ci default '';");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_cover` `gall_cover` int(11) default 0;");
        $wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_description` `gall_description` text collate utf8_general_ci default NULL;");
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
                          `templ_title` varchar(255) collate utf8_general_ci NOT NULL,
                          `templ_description` text NULL default NULL,
                          PRIMARY KEY  (`id`)
                        );";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        dbDelta($templates);
        }
    }
    
    if (version_compare($version, '1.5.1', '<')) {
        add_option('1_flash_gallery_preview_opt','0','','no');
    }
    
    if (version_compare($version, '1.5.6', '<')) {
        $wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_extra` text collate utf8_unicode_ci NULL default NULL;");
    }
    
    if (version_compare($version,'1.7.9','<')){
        add_option('1_flash_gallery_show_link', '0','',no);
        add_option('1_flash_gallery_preview_size', '200','',no);
        add_option('1_flash_gallery_display_view_size', '1200','',no);
        add_option('1_flash_gallery_save_original', '0','',no);
        $wpdb->query("ALTER TABLE ".IMAGES_TABLE." 
                        ADD `img_preview_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `img_path`,
                        ADD `img_full_view_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `img_preview_path`;
                     ");
    }
    if (version_compare($version, '1.8.0','<')) {
        add_option('1_flash_gallery_caption_type','0','',no);
        $wpdb->query("ALTER TABLE ".IMAGES_TABLE." CHANGE `img_path` `img_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }
    if (version_compare($version, '1.8.1','<')) {
       $wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_description` `img_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }
    update_option("fgallery_db_version", FGALLERY_VERSION);

    return true;
}

// uninstall script
function fgallery_uninstall() {
   global $wpdb;
   //drop tables after uninstall
   $wpdb->query("DROP TABLE IF EXISTS ". ALBUMS_TABLE);
   $wpdb->query("DROP TABLE IF EXISTS ". IMAGES_TABLE);
   $wpdb->query("DROP TABLE IF EXISTS ". IMAGES_TO_ALBUMS_TABLE);
   $wpdb->query("DROP TABLE IF EXISTS ". ALBUMS_SETTINGS_TABLE);
   $wpdb->query("DROP TABLE IF EXISTS ". TEMPLATES_TABLE);
   //remove gallery directory
   fgallery_delete_dir(FGALLERY_DIR); 
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
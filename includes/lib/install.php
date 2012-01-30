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
    
    if(version_compare(get_option('fgallery_db_version', 0), '1.9.0', '<')){
        fgallery_first_install();
        fgallery_update(true);
    }else {
        fgallery_update();      
    }
}


function fgallery_first_install() {
   global $wpdb;
   global $wp_filesystem;
   $S = true;

   //add option for screen options (number of elements per page)
   add_option('1_flash_gallery_page_fgallery_images_per_page', '25', '', 'no');
   add_option('toplevel_page_fgallery_per_page', '25', '', 'no');
   // database version to provide updates
   add_option('fgallery_db_version', '0','','no');

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
        $S=$S&dbDelta($create_albums);
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
        $S=$S&dbDelta($create_images);
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
        $S=$S&dbDelta($create_albums_images);
    }

    if($wpdb->get_var("SHOW TABLES LIKE '" . ALBUMS_SETTINGS_TABLE . "'") != ALBUMS_SETTINGS_TABLE) {
	// create table for albums settings
	$albums_settings = "CREATE TABLE " . ALBUMS_SETTINGS_TABLE . " (
                              `gall_id` int(11) NOT NULL,
                              `value` text NOT NULL,
                              PRIMARY KEY  (`gall_id`)
			);";
        require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
        $S=$S&dbDelta($albums_settings);
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
        $S=$S&dbDelta($templates);
    }

    /**
     *  For WordPress versions lower 3.0 there is no update hook
     *  so here is the update actions
     */

    // try to make gallery dir if not exists
    if (!is_dir(FGALLERY_DIR)) {
	WP_Filesystem();
        @ mkdir(FGALLERY_DIR);
        @ mkdir(FGALLERY_DIR.'/tmp');
    }
    
    if ($S == false){
        echo 'Error during install';
        die();
    }
    
    return $S;
}


/**
 * Provides update of 1 Flash Gallery
 *
 * @return type
 */
function fgallery_update($fi = false) {
    global $wpdb;
    $version = ($fi)?0:get_option('fgallery_db_version', 0);
    $S = true;

    if (version_compare($version, '1.0.7', '<')) {
       if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_url` VARCHAR( 255 ) collate utf8_unicode_ci NULL AFTER `img_order`;");
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

        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `gall_folder` `gall_folder` int( 11 ) default 0;");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` CHANGE `img_order` `img_order` int( 11 ) default 0;");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_parent` `img_parent` int( 11 ) default 0;");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_vs_folder` `img_vs_folder` int( 11 ) default 0;");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_size` `img_size` int( 11 ) default 0;");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_type` `img_type` varchar(50) collate utf8_general_ci default '';");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_path` `img_path` varchar(50) collate utf8_general_ci default '';");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_cover` `gall_cover` int(11) default 0;");
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".ALBUMS_TABLE."` CHANGE `gall_description` `gall_description` text collate utf8_general_ci default NULL;");
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
        if(!$fi) dbDelta($templates);
        }
    }

    if (version_compare($version, '1.5.1', '<')) {
        add_option('1_flash_gallery_preview_opt','0','','no');
    }

    if (version_compare($version, '1.5.6', '<')) {
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TO_ALBUMS_TABLE."` ADD `img_extra` text collate utf8_unicode_ci NULL default NULL;");
    }

    if (version_compare($version,'1.7.9','<')){
        add_option('1_flash_gallery_preview_size', '200','',no);
        add_option('1_flash_gallery_display_view_size', '1200','',no);
        add_option('1_flash_gallery_save_original', '0','',no);
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE ".IMAGES_TABLE."
                        ADD `img_preview_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `img_path`,
                        ADD `img_full_view_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `img_preview_path`;
                     ");
    }
    if (version_compare($version, '1.8.0','<')) {
        add_option('1_flash_gallery_caption_type','0','',no);
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE ".IMAGES_TABLE." CHANGE `img_path` `img_path` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }
    if (version_compare($version, '1.8.1','<')) {
        if(!$fi)$S=$S&$wpdb->query("ALTER TABLE `".IMAGES_TABLE."` CHANGE `img_description` `img_description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
    }
    if (version_compare($version, '1.8.3','<')) {
        add_option('1_flash_gallery_access_level', 'activate_plugins','',no);
    }
    if (version_compare($version, '1.9.0','<')) {
        
        $S=$S&db_update_to_190();
        
    }
    if ($S == true){
        update_option("fgallery_db_version", FGALLERY_VERSION);
    }else {
        echo 'Error during update';
        //die();
    }
    return $S;
}

// uninstall script
function fgallery_uninstall() {
   global $wpdb;
   //drop tables after uninstall
   $S =  $wpdb->query("DROP TABLE IF EXISTS ". ALBUMS_TABLE);
   $S=$S&$wpdb->query("DROP TABLE IF EXISTS ". IMAGES_TABLE);
   $S=$S&$wpdb->query("DROP TABLE IF EXISTS ". IMAGES_TO_ALBUMS_TABLE);
   $S=$S&$wpdb->query("DROP TABLE IF EXISTS ". ALBUMS_SETTINGS_TABLE);
   $S=$S&$wpdb->query("DROP TABLE IF EXISTS ". TEMPLATES_TABLE);
   //remove gallery directory
   if ($S == true) update_option("fgallery_db_version", 0);
   fgallery_delete_dir(FGALLERY_DIR);
   return $S;
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

function db_update_to_190 () {
    
    $update = array();

    //Initialization
    
    for ($i = 1; $i <= 13; $i++){
        $update[$i] = new gallery_settings_upd($i);
    }
    
    //All of them
    for ($i = 1; $i <= 13; $i++){
        $update[$i]->add_rule('slideshow.source',           'gallery.source',                   'local'     );    
    }

    //Name ===================================================
    
    $type_id = 0;
    
    //$update[$type_id]->add_rule('','','');
    
    //Acosta ===================================================
    
    $type_id = 1;
    
    $update[$type_id]->add_rule('image.stopByClick',        'slideshow.stopByClick',            1           );
    $update[$type_id]->add_rule('navigation.position',      ''                                              );
    $update[$type_id]->add_rule('scroller.direction',       ''                                              );
    $update[$type_id]->add_rule('',                         'navigation.hideFullScreenButton',  1           );
    $update[$type_id]->add_rule('',                         'scroller.cornerRadius',            1           );
    
    //Airion ===================================================
    
    $type_id = 2;
    
    $update[$type_id]->add_rule('image.stopByClick',        'slideshow.stopByClick',            1           );
    $update[$type_id]->add_rule('',                         'image.cornerRadius',               30          );
    $update[$type_id]->add_rule('',                         'scroller.align',                   'top'       );    
    $update[$type_id]->add_rule('',                         'scroller.useImagesInItem',         1           );    
    $update[$type_id]->add_rule('',                         'scrollerItem.textColor',           '0x000000'  );    
    $update[$type_id]->add_rule('',                         'scrollerItem.fontSize',            13          );    
  
    //Arai ===================================================
    
    $type_id = 3;
    
    $update[$type_id]->add_rule('caption.visible',          ''                                              );
    $update[$type_id]->add_rule('image.stopByClick',        'slideshow.stopByClick',            1           );
    $update[$type_id]->add_rule('scroller.speed',           ''                                              );
    $update[$type_id]->add_rule('',                         'scrollerItem.shadow',              1           );
    $update[$type_id]->add_rule('',                         'scrollerItem.shadowColor',         '0xff0000'  );
    $update[$type_id]->add_rule('',                         'scrollerItem.shadowAlpha',         0.7         );
    $update[$type_id]->add_rule('',                         'scrollerItem.shadowBlur',          4           );
    $update[$type_id]->add_rule('',                         'scrollerItem.shadowDistance',      4           );

    //Pax ===================================================
    
    $type_id = 4;
    
    $update[$type_id]->add_rule('',                         'gallery.type',                     'byFolder'  );
    $update[$type_id]->add_rule('preview.captionAlign',     'image.captionAlign',               'left'      );
    $update[$type_id]->add_rule('preview.captionBackgroundColor', 'image.captionBackgroundColor', '0x000000');
    $update[$type_id]->add_rule('preview.captionBold',      'image.captionBold',                0           );
    $update[$type_id]->add_rule('preview.captionColor',     'image.captionColor',               '0xffffff'  );
    $update[$type_id]->add_rule('preview.captionFont',      'image.captionFont',                'Verdana'   );
    $update[$type_id]->add_rule('preview.captionPosition',  'image.captionPosition',            'top'       );
    $update[$type_id]->add_rule('preview.captionSize',      'image.captionSize',                15          );
    $update[$type_id]->add_rule('preview.dimmingBackgroundEnable', 'image.dimmingBackgroundEnable', 0       );
    $update[$type_id]->add_rule('preview.height',           'image.height',                     400         );
    $update[$type_id]->add_rule('preview.scale',            'image.scale',                      'fit'       );
    $update[$type_id]->add_rule('preview.width',            'image.width',                      660         );
    $update[$type_id]->add_rule('',                         'image.cornerRadius',               0           );
    $update[$type_id]->add_rule('',                         'image.shadowDistance',             0           );
    $update[$type_id]->add_rule('',                         'image.shadowSize',                 0           );
    $update[$type_id]->add_rule('',                         'image.shadowColor',                '0xFFFFFF'  );
    $update[$type_id]->add_rule('',                         'image.shadowAngle',                0           );
    $update[$type_id]->add_rule('slideshow.target',         'image.linkTarget',                 '_blank'    );
    $update[$type_id]->add_rule('',                         'image.imageAsLink',                1           );
    $update[$type_id]->add_rule('',                         'image.navigationMode',             'button'    );
    $update[$type_id]->add_rule('',                         'image.playButtonBackgroundColorOver', '0x000000'  );
    $update[$type_id]->add_rule('',                         'image.playButtonBackgroundColorOut', '0xFFFFFF'  );
    $update[$type_id]->add_rule('',                         'image.playButtonColorOver',        '0xFFFFFF'  );
    $update[$type_id]->add_rule('',                         'image.playButtonColorOut',         '0x000000'  );
    $update[$type_id]->add_rule('',                         'image.playButtonAlphaOver',        0.9         );
    $update[$type_id]->add_rule('',                         'image.playButtonAlphaOut',         0.5         );
    $update[$type_id]->add_rule('',                         'image.fullscreenButtonBackgroundColorOver', '0x000000');
    $update[$type_id]->add_rule('',                         'image.fullscreenButtonBackgroundColorOut', '0xFFFFFF');
    $update[$type_id]->add_rule('',                         'image.fullscreenButtonColorOver',  '0xFFFFFF'  );
    $update[$type_id]->add_rule('',                         'image.fullscreenButtonColorOut',   '0x000000'  );
    $update[$type_id]->add_rule('',                         'image.fullscreenButtonAlphaOver',  0.9         );
    $update[$type_id]->add_rule('',                         'image.fullscreenButtonAlphaOut',   0.5         );
    $update[$type_id]->add_rule('',                         'image.flipButtonBackgroundColorOver', '0xFFFFFF'  );
    $update[$type_id]->add_rule('',                         'image.flipButtonBackgroundColorOut', '0xFFFFFF'  );
    $update[$type_id]->add_rule('',                         'image.flipButtonColorOver',        '0x666666'  );
    $update[$type_id]->add_rule('',                         'image.flipButtonColorOut',         '0xCCCCCC'  );
    $update[$type_id]->add_rule('',                         'image.flipButtonAlphaOver',        1           );
    $update[$type_id]->add_rule('',                         'image.flipButtonAlphaOut',         0.7         );
    $update[$type_id]->add_rule('',                         'image.preloaderEnable',            1           );
    $update[$type_id]->add_rule('',                         'image.preloaderColor',             '0xFFFFFF'  );
    $update[$type_id]->add_rule('preview.buttonsEnable',    ''                                              );
    $update[$type_id]->add_rule('preview.linkageEnable',    ''                                              );
    $update[$type_id]->add_rule('',                         'slideshow.stopMusicWhenShowStop',  1           );
    $update[$type_id]->add_rule('',                         'slideshow.autostartSlideshow',     0           );
    $update[$type_id]->add_rule('',                         'slideshow.sendID',                 1           );

    //Pazin ===================================================
    
    $type_id = 5;
    
    $update[$type_id]->add_rule('',                         'gallery.type',                     'byFolder'  );
    $update[$type_id]->add_rule('preview.borderColor',      'image.borderColor',                '0x000000'  );
    $update[$type_id]->add_rule('preview.borderThickness',  'image.borderThickness',            4           );
    $update[$type_id]->add_rule('preview.cornerRadius',     'image.cornerRadius',               25          );
    $update[$type_id]->add_rule('preview.height',           'image.height',                     300         );
    $update[$type_id]->add_rule('preview.preloaderColor',   'image.preloaderColor',             '0xff00ff'  );
    $update[$type_id]->add_rule('preview.scale',            'image.scale',                      'fit'       );
    $update[$type_id]->add_rule('preview.scatter',          'image.scatter',                    100         );
    $update[$type_id]->add_rule('preview.shadowAngle',      'image.shadowAngle',                0           );
    $update[$type_id]->add_rule('preview.shadowColor',      'image.shadowColor',                '0x000000'  );
    $update[$type_id]->add_rule('preview.shadowDistance',   'image.shadowDistance',             0           );
    $update[$type_id]->add_rule('preview.shadowSize',       'image.shadowSize',                 10          );
    $update[$type_id]->add_rule('preview.titleAlign',       'image.titleAlign',                 'left'      );
    $update[$type_id]->add_rule('preview.titleBold',        'image.titleBold',                  1           );
    $update[$type_id]->add_rule('preview.titleColor',       'image.titleColor',                 0x000000    );
    $update[$type_id]->add_rule('preview.titleFont',        'image.titleFont',                  'Tahoma'    );
    $update[$type_id]->add_rule('preview.titlePosition',    'image.titlePosition',              'bottom'    );
    $update[$type_id]->add_rule('preview.titleSize',        'image.titleSize',                  20          );
    $update[$type_id]->add_rule('preview.titleUseEmbedFont','image.titleUseEmbedFont',          1           );
    $update[$type_id]->add_rule('preview.width',            'image.width',                      430         );
    $update[$type_id]->add_rule('slideshow.target',         'image.linkTarget',                 '_blank'    );
    $update[$type_id]->add_rule('',                         'image.imageAsLink',                1           );
    $update[$type_id]->add_rule('',                         'image.borderType',                 1           );
    $update[$type_id]->add_rule('',                         'image.borderPath',                 1           );
    $update[$type_id]->add_rule('',                         'image.borderAlpha',                1           );
    $update[$type_id]->add_rule('',                         'slideshow.stopMusicWhenShowStop',  1           );
    $update[$type_id]->add_rule('',                         'slideshow.autostartSlideshow',     0           );
    $update[$type_id]->add_rule('',                         'slideshow.sendID',                 1           );
    
    //Nilus ===================================================
    
    $type_id = 8;
        
    $update[$type_id]->add_rule('',                         'gallery.type',                     'byFolder'  );

    $update[$type_id]->add_rule('main_screen.width',        'mainScreen.width',                 530         );
    $update[$type_id]->add_rule('main_screen.height',       'mainScreen.height',                380         );
    $update[$type_id]->add_rule('main_screen.shadow',       'mainScreen.shadow',                1           );
    $update[$type_id]->add_rule('main_screen.color',        'mainScreen.color',                 '0xFFFFFF'  );
    $update[$type_id]->add_rule('main_screen.small_width',  'mainScreen.smallWidth',            170         );
    $update[$type_id]->add_rule('main_screen.small_height', 'mainScreen.smallHeight',           120         );
    $update[$type_id]->add_rule('main_screen.small_color',  'mainScreen.smallColor',            '0x000000'  );
    $update[$type_id]->add_rule('main_screen.small_x',      'mainScreen.smallX',                2           );
    $update[$type_id]->add_rule('main_screen.small_y',      'mainScreen.smallY',                2           );
    $update[$type_id]->add_rule('main_screen.color_arrow',  'mainScreen.colorArrow',            '0xFFFFFF'  ); 
    $update[$type_id]->add_rule('main_screen.arrow_alpha',  'mainScreen.arrowAlpha',            1           );
    $update[$type_id]->add_rule('image.target',             ''                                              );
    $update[$type_id]->add_rule('main_screen.target',       ''                                              );
    $update[$type_id]->add_rule('main_screen.time_change_screen',   'mainScreen.timeChangeScreen', 1.5      );
    $update[$type_id]->add_rule('main_screen.time_show_small',      'mainScreen.timeShowSmall', 0.5         );
    $update[$type_id]->add_rule('main_screen.resizable_small',      'mainScreen.resizableSmall','fit'       );
    $update[$type_id]->add_rule('main_screen.blackout_small',       'mainScreen.blackoutSmall', 0           );
    $update[$type_id]->add_rule('main_screen.blackout_alpha',       'mainScreen.blackoutAlpha', 0.7         );
    $update[$type_id]->add_rule('main_screen.frame_small_size',     'mainScreen.frameSmallSize',2           );
    $update[$type_id]->add_rule('main_screen.frame_small_color',    'mainScreen.frameSmallColor', '0x000000');
    $update[$type_id]->add_rule('main_screen.frame_small_alpha',    'mainScreen.frameSmallAlpha', 0.5       );
    $update[$type_id]->add_rule('main_screen.frame_big_size',       'mainScreen.frameBigSize',  5           );  
    $update[$type_id]->add_rule('main_screen.frame_big_color',      'mainScreen.frameBigColor', '0x000000'  );
    $update[$type_id]->add_rule('main_screen.frame_big_alpha',      'mainScreen.frameBigAlpha', 0.5         );

    $update[$type_id]->add_rule('big_foto.color',           'image.color',                      '0x000000'  );
    $update[$type_id]->add_rule('big_foto.background',      'image.background',                 '0xAAAAAA'  );
    $update[$type_id]->add_rule('big_foto.text_color',      'image.textColor',                  '0xFFFFFF'  );
    $update[$type_id]->add_rule('big_foto.font',            'image.font',                       'Tahoma'    );
    $update[$type_id]->add_rule('big_foto.font_size',       'image.fontSize',                   14          );
    $update[$type_id]->add_rule('big_foto.font_bold',       'image.fontBold',                   0           );
    $update[$type_id]->add_rule('big_foto.time_open_photo', 'image.timeOpenPhoto',              1           );
    $update[$type_id]->add_rule('big_foto.time_show_text',  'image.timeShowText',               1           );
    $update[$type_id]->add_rule('big_foto.time_show_arrow', 'image.timeShowArrow',              1           );
    $update[$type_id]->add_rule('big_foto.color_arrow',     'image.colorArrow',                 '0xFFFFFF'  );
    $update[$type_id]->add_rule('big_foto.arrow_alpha',     'image.arrowAlpha',                 1           );
    $update[$type_id]->add_rule('big_foto.scalePhoto',      'image.scaleMode',                  'noScale'   );
    $update[$type_id]->add_rule('',                         'image.timeChangeScreen',           1           );
    $update[$type_id]->add_rule('',                         'image.timeShowSmall',              1           );
    $update[$type_id]->add_rule('big_foto.text_background_color',   'image.textBackgroundColor','0x000000'  );
    $update[$type_id]->add_rule('big_foto.text_background_height',  'image.textBackgroundHeight',40         );
    $update[$type_id]->add_rule('big_foto.text_background_alpha',   'image.textBackgroundAlpha',0.5         );
    $update[$type_id]->add_rule('big_foto.time_change_alpha_photo', 'image.timeChangeAlphaPhoto',1          );
    $update[$type_id]->add_rule('big_foto.time_change_photo',       'image.timeChangePhoto',    0.6         );
    $update[$type_id]->add_rule('big_foto.effect_change_photo',     ''                                      );
    
    $update[$type_id]->add_rule('',                         'slideshow.sendID',                 1           );
    $update[$type_id]->add_rule('slideshow_bar.enable',     'scroller.enable',                  1           );
    $update[$type_id]->add_rule('slideshow_bar.width',      'scroller.width',                   200         );
    $update[$type_id]->add_rule('slideshow_bar.height',     'scroller.height',                  30          );
    $update[$type_id]->add_rule('slideshow_bar.x',          'scroller.x',                       350         );
    $update[$type_id]->add_rule('slideshow_bar.y',          'scroller.y',                       10          );
    $update[$type_id]->add_rule('slideshow_bar.color',      'scroller.color',                   '0x000000'  );
    $update[$type_id]->add_rule('slideshow_bar.color2',     'scroller.color2',                  '0x000000'  );
    $update[$type_id]->add_rule('slideshow_bar.alpha',      'scroller.alpha',                   0.9         );
    $update[$type_id]->add_rule('slideshow_bar.eclipse',    'scroller.eclipse',                 10          );
    $update[$type_id]->add_rule('slideshow_bar.arrow_right','scroller.showArrowRight',          1           );
    $update[$type_id]->add_rule('slideshow_bar.full_screen','scroller.showFullScreen',          1           );
    $update[$type_id]->add_rule('slideshow_bar.arrow_left', 'scroller.showArrowLeft',           1           );
    $update[$type_id]->add_rule('slideshow_bar.select_albums',  'scroller.showSelectAlbums',    1           );
    $update[$type_id]->add_rule('slideshow_bar.play_slideshow', 'scroller.hidePlayButton',      0,          'rev');
    $update[$type_id]->add_rule('slideshow_bar.buttons_alpha',  'scroller.buttonsAlpha',        0.8         );
    $update[$type_id]->add_rule('slideshow_bar.buttons_space',  'scroller.buttonsSpace',        35          );
    $update[$type_id]->add_rule('slideshow_bar.select_albums_color',    'scroller.selectAlbumsColor', '0x000000');
    $update[$type_id]->add_rule('slideshow_bar.select_albums_eclipse',  'scroller.selectAlbumsEclipse', 10  );
    $update[$type_id]->add_rule('slideshow_bar.select_albums_alpha',    'scroller.selectAlbumsAlpha', 0.8   );
    $update[$type_id]->add_rule('slideshow_bar.select_albums_font',     'scroller.selectAlbumsFont','Tahoma');
    $update[$type_id]->add_rule('slideshow_bar.select_albums_font_size','scroller.selectAlbumsFontSize', 12 );
    $update[$type_id]->add_rule('slideshow_bar.select_albums_font_color','scroller.selectAlbumsFontColor', '0xFFFFFF');
    $update[$type_id]->add_rule('slideshow_bar.select_albums_font_color_over', 'scroller.selectAlbumsFontColorOver', '0xFF8A00');
    $update[$type_id]->add_rule('slideshow_bar.select_albums_space_text','scroller.selectAlbumsSpaceText',10);
    $update[$type_id]->add_rule('slideshow_bar.select_albums_time_show', 'scroller.selectAlbumsTimeShow', 1 );
    $update[$type_id]->add_rule('slideshow_bar.select_albums_time_hide', 'scroller.selectAlbumsTimeHide', 1 );

    $update[$type_id]->add_rule('tool_tip.color',           'toolTip.color',                    '0x000000'  );
    $update[$type_id]->add_rule('tool_tip.alpha',           'toolTip.alpha',                    0.7         );
    $update[$type_id]->add_rule('tool_tip.max_width',       'toolTip.maxWidth',                 180         );
    $update[$type_id]->add_rule('tool_tip.font',            'toolTip.font',                     'Tahoma'    );
    $update[$type_id]->add_rule('tool_tip.font_color',      'toolTip.fontColor',                '0xFFFFFF'  );
    $update[$type_id]->add_rule('tool_tip.font_size',       'toolTip.fontSize',                 14          );
    $update[$type_id]->add_rule('tool_tip.time_show',       'toolTip.timeShow',                 0.5         );
    
    //Postma ===================================================
    
    $type_id = 6;
    
    $update[$type_id]->add_rule('preview.type',             'scrollerItem.type',                'line'      );
    
    //Postma && Nusl ===================================================
    
    $type_ids = array(6, 9);
        
    foreach($type_ids as $type_id){
    
    $update[$type_id]->add_rule('',                         'gallery.type',                     'flat'      );
    $update[$type_id]->add_rule('',                         'slideshow.autostart',              0           );
    $update[$type_id]->add_rule('',                         'slideshow.pauseMusic',             1           );
    $update[$type_id]->add_rule('',                         'slideshow.stopByClick',            1           );
    $update[$type_id]->add_rule('navigation.showFullScreen','navigation.hideFullScreenButton',  1,          'rev');
    $update[$type_id]->add_rule('navigation.showPlay',      'navigation.hidePlayButton',        0,          'rev');
    $update[$type_id]->add_rule('caption.textColor',        'caption.fontColor',                '0x196b94'  );
    $update[$type_id]->add_rule('scroller.visible',         'scroller.enable',                  1           );
    $update[$type_id]->add_rule('scroller.width',           'scroller.size',                    400         );
    $update[$type_id]->add_rule('preview.color',            'scrollerItem.color',               '0xff0000'  );
    $update[$type_id]->add_rule('preview.scaleEffect',      'scrollerItem.scaleEffect',         'spin'      );
    $update[$type_id]->add_rule('preview.reflection',       'scrollerItem.reflection',          0           );
    $update[$type_id]->add_rule('preview.reflectionAlpha',  'scrollerItem.reflectionAlpha',     0.7         );
    $update[$type_id]->add_rule('preview.reflectionDistance','scrollerItem.reflectionDistance', 8           );
    $update[$type_id]->add_rule('preview.reflectionGradientColorStart',  'scrollerItem.reflectionGradientColorStart',  '0x000000');
    $update[$type_id]->add_rule('preview.reflectionGradientColorFinish', 'scrollerItem.reflectionGradientColorFinish', '0x000000');
    $update[$type_id]->add_rule('preview.borderWidth',      'scrollerItem.borderWidth',         2           );
    $update[$type_id]->add_rule('preview.borderColor',      'scrollerItem.borderColor',         '0xffffff'  );
    $update[$type_id]->add_rule('preview.borderEclipse',    'scrollerItem.borderEclipse',       5           );
    $update[$type_id]->add_rule('preview.borderAlpha',      'scrollerItem.borderAlpha',         1           );
    $update[$type_id]->add_rule('preview.borderPhotoWidth', 'scrollerItem.borderPhotoWidth',    10          );
    $update[$type_id]->add_rule('preview.borderPhotoColor', 'scrollerItem.borderPhotoColor',    '0xfedcba'  );
    $update[$type_id]->add_rule('preview.borderPhotoAlpha', 'scrollerItem.borderPhotoAlpha',    1           );
    $update[$type_id]->add_rule('preview.backgroundPhoto',  'scrollerItem.backgroundPhoto',     ''          );
    $update[$type_id]->add_rule('preview.alpha',            'scrollerItem.alpha',               1           );
    $update[$type_id]->add_rule('preview.width',            'scrollerItem.width',               80          );
    $update[$type_id]->add_rule('preview.height',           'scrollerItem.height',              40          );
    $update[$type_id]->add_rule('preview.distanceX',        'scrollerItem.distanceX',           25          );
    $update[$type_id]->add_rule('preview.distanceY',        'scrollerItem.distanceY',           0           );
    $update[$type_id]->add_rule('preview.distanceZ',        'scrollerItem.distanceZ',           20          );
    $update[$type_id]->add_rule('preview.rotation',         'scrollerItem.rotation',            10          );
    $update[$type_id]->add_rule('preview.cornerRadius',     'scrollerItem.cornerRadius',        5           );
    $update[$type_id]->add_rule('preview.backgroundColor',  'scrollerItem.backgroundColor',     '0x000000'  );
    $update[$type_id]->add_rule('preview.scaleSmall',       'scrollerItem.scaleSmall',          'fit'       );
    $update[$type_id]->add_rule('preview.scalePhoto',       'scrollerItem.scalePhoto',          'fit'       );
    $update[$type_id]->add_rule('preview.isURL',            'image.imageAsLink',                0           );
    $update[$type_id]->add_rule('preview.target',           'image.linkTarget',                 '_blank'    );
    
    }
    
    //Kranjk ===================================================
    
    $type_id = 10; //Not working
    
    $update[$type_id]->add_rule('',                         'scroller.itemEmptyColor',          '0x000000'  );
    $update[$type_id]->add_rule('slideshow.target',         'image.linkTarget',                 '_blank'    );
    $update[$type_id]->add_rule('',                         'image.imageAsLink',                0           );
    $update[$type_id]->add_rule('slideshow.useEmbedFont',   ''                                              );

    $update[$type_id]->add_rule('',                         'scrollerItem.color','0x000000'                 );
    $update[$type_id]->add_rule('',                         'scrollerItem.titlePosition','top'              );
    
    //Perona ===================================================
    
    $type_id = 11;
    
    $update[$type_id]->add_rule('','slideshow.disableBigImage',0);
    $update[$type_id]->add_rule('background.typeback','');
    
    //Ables ===================================================
    
    $type_id = 12;
    
    $update[$type_id]->add_rule('preview.enable','');
    
    
    
    
    foreach ($update as $upd) $upd->execute();

    return true;

}


class gallery_settings_upd {

    private $type_id;
    private $galleries;
    private $rules;

    function __construct($type_id){

        $this->type_id = $type_id;
        
        
        global $wpdb;
  
        $galleries = $wpdb->get_results('SELECT `gall_id` FROM '.ALBUMS_TABLE.' WHERE `gall_type` = '.$this->type_id, OBJECT_K);

        if (count($galleries) > 0){
            
            foreach ($galleries as $gallery){

                $settings = $wpdb->get_results('SELECT * FROM '.ALBUMS_SETTINGS_TABLE.' WHERE `gall_id` = '.$gallery->gall_id, OBJECT);
                $settings = unserialize($settings[0]->value);

                $this->galleries[$gallery->gall_id] = $settings;
            }
            
        }else {

            $this->galleries = false;
            
        }
    }
    
    public function add_rule($old, $new, $default = NULL, $convertation = NULL){
        
        $this->rules[] = new element_settings_upd($old, $new, $default, $convertation);
        
    }
    


    public function execute(){

        global $wpdb;
        
        //Validation
        
        if (!is_array($this->rules)) return false;
        if (!is_array($this->galleries)) return false;
        
        //Main processing

        foreach ($this->rules as $rule){
            
            foreach ($this->galleries as $gall_id => $settings){
                
                if (($rule->old_name == NULL && !isset($settings[$rule->new_name]))|| !isset($settings[$rule->old_name])){ //Add new option
                    
                    $this->galleries[$gall_id][$rule->new_name] = $rule->get_value();
                    
                }elseif ($rule->new_name == NULL){ //Del unused option
                
                    unset ($this->galleries[$gall_id][$rule->old_name]);
                    
                }elseif (isset($settings[$rule->old_name])){ //Rename
                    
                    $this->galleries[$gall_id][$rule->new_name] = $rule->get_value($this->galleries[$gall_id][$rule->old_name]);
                    unset ($this->galleries[$gall_id][$rule->old_name]);
                    
                }
            }
        }
        
        //Save into db
        
        foreach ($this->galleries as $gall_id => $settings){
            fgallery_save_album_settings($gall_id, $settings);
        }
            

    }
    
}

class element_settings_upd {


    public  $old_name;
    public  $new_name;
    private $default_value;
    private $convertation;

    function __construct($old, $new, $default = NULL, $convertation = NULL){

        $this->old_name = ($old != '')?'sc_'.str_replace('.', '__', $old):NULL;
        $this->new_name = ($new != '')?'sc_'.str_replace('.', '__', $new):NULL;
        $this->default_value = $default;
        $this->convertation = $convertation;

    }

    function get_value($old_value = NULL){
        
        if ($this->old_name == '' || $old_value == NULL){
            
            return $this->default_value;
            
        }else {
            
            switch ($this->convertation){

            case 'rev':
                return (int) !$old_value;
            break;

            default :
                return $old_value;
            break;

            }
        }
        
    }

}

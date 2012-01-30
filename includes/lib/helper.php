<?php

/*
 * Helper functions
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Returns User system role
 * @global type $current_user
 * @return integer 
 */
function fgallery_get_current_user_role() {
    global $current_user;
        $user_roles = $current_user->roles;
        $user_role = array_shift($user_roles);
    return $user_role;
}

/**
 * Defines the current user access level 
 * @return type 
 */
function fgallery_access_level(){
    $role = fgallery_get_current_user_role();
    switch ($role) {
        case 'administrator':return 10;
        case 'editor':return 7;
        case 'author':return 2;
        default: return 2;
    }
}

// returns the way of gallery sorting
function fgallery_sort_albums_condition($sort) {
    switch ($sort){
        case 0:return 'gall_name ASC';
        case 1:return 'gall_name DESC';
        case 2:return 'gall_createddate ASC';
        case 3:return 'gall_createddate DESC';
        case 4:return 'gall_order ASC';
        default:return 'gall_order ASC';
    }
}

// returns the way of image sorting
function fgallery_sort_images_condition($sort) {
    switch ($sort){
        case 0:return 'img_caption ASC';
        case 1:return 'img_caption DESC';
        case 2:return 'img_date ASC';
        case 3:return 'img_date DESC';
        case 4:return 'img_size ASC';
        case 5:return 'img_size DESC';
        default: return 'img_date ASC';
    }
}

/**
 * Renders the shortcode for inserting the gallery into the content
 * @param type $id
 * @param type $type
 * @return type 
 */
function fgallery_do_shortcode($id, $type = 0) {
    $album = fgallery_get_album($id);
    return sprintf('[fgallery id=%d w=%d h=%d t=%d title="%s"]',$id, 
            $album['gall_width'], $album['gall_height'], $type, trim($album['gall_name']));
}


/**
 * Retruns the string value of gallery skin
 * @param array $album
 * @return string 
 */
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
        case 11: return 'perona';
	case 12: return 'ables';
	case 13: return 'flipbook';
        default: return 'arai';
    }
}

function fgallery_create_slideshow($album) {
    $output = '<h2 style="font-size:8px;line-height:12px;text-align:center;">';
       switch ($album['gall_type']) {
        case 1: 
            $output .= '<a href="http://1plugin.com/galleries/acosta" title="Photo Gallery">Photo Gallery</a>';
            break;
        case 2:
            $output .= '<a href="http://1plugin.com/galleries/airion" title="Flash Gallery">Flash Gallery</a>';
            break;
        case 3: 
            $output .= '<a href="http://1plugin.com/galleries/arai" title="Slideshow">Slideshow</a>';
            break;
        case 4:
            $output .= '<a href="http://1plugin.com/galleries/pax" title="Wordpress Flash Gallery">Wordpress Flash Gallery</a>';
            break;            
        case 5: 
            $output .= '<a href="http://1plugin.com/galleries/pazin" title="Pop Up Image Gallery Wordpres">Pop Up Image Gallery Wordpres</a>';
            break;            
        case 6: 
            $output .= '<a href="http://1plugin.com/galleries/postma" title="Wordpress Flash Gallery Plugin">Wordpress Flash Gallery Plugin</a>';
            break;            
        case 7: 
            $output .= '<a href="http://1plugin.com/page-flip" title="Flipping Book">Flipping Book</a>';
            break;            
        case 8: 
            $output .= '<a href="http://1plugin.com/galleries/nilus" title="Wordpress Image Gallery">Wordpress Image Gallery</a>';
            break;            
        case 9: 
            $output .= '<a href="http://1plugin.com/galleries/nusl-image-gallery" title="Wordpress Image Gallery Plugin">Wordpress Image Gallery Plugin</a>';
            break;            
        case 10: 
            $output .= '<a href="http://1plugin.com/galleries/kranjk" title="Photo Gallery">Photo Gallery</a>';
            break;            
        case 11: 
            $output .= '<a href="http://1plugin.com/galleries/perona-image-gallery" title="Image Gallery">Image Gallery</a>';
            break;            
	case 12: 
            $output .= '<a href="http://1plugin.com/galleries/ables-banner-slider" title="Banner Rotator">Banner Rotator</a>';
            break;
        default:  
            $output .= '<a href="http://1plugin.com/galleries/arai" title="Slideshow">Slideshow</a>';
            break;;
    } 
    $output .= '</h2>';
    
    return $output;
}

/**
 * Returns path to swf file of a given Gallery
 * @param array $album
 * @return string 
 */
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

/**
 * Formats the image/folder/gallery size 
 * @param type $b
 * @param type $p
 * @return string 
 */
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
    if ($b != 0){
        $units = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
        if(!$p && $p !== 0) {
            foreach($units as $k => $u) {
                if(($b / pow(1024,$k)) >= 1) {
                    $r["bytes"] = $b / pow(1024,$k);
                    $r["units"] = $u;
                }
            }
            return number_format($r["bytes"],2) . " " . $r["units"];
        } else {
            return number_format($b / pow(1024,$p)) . " " . $units[$p];
        }
    }else {
        return 0;
    }
}

/**
 * Return UTF-8 converted string
 * @param string $data
 * @return string 
 */
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

/**
 * Sanitize string
 * @param string $string
 * @return string
 */
function fgallery_sanitize_string($string) {
    return mixed_to_utf8(stripslashes($string));
}

/**
 * Clean string and convert to UTF-8 string
 * @param string $string 
 * @return string
 */
function fgallery_escape_string($string) {
    return stripslashes(html_entity_decode($string,ENT_NOQUOTES,"UTF-8"));
}

/**
 * Returns if plugin is active even in front-end
 * @param string $plugin_path
 * @return boolean
 */
function plugin_is_active($plugin_path) {
    $return_var = in_array( $plugin_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
    return $return_var;
}
<?php
/* 
 * Returns the configuration xml file for given gallery id
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Function to get settings.xml file
 */
function fgallery_config(){
    if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
        $gall_id = $_GET['gall_id'];
	$config = fgallery_get_album_settings($gall_id);
	$album = fgallery_get_album($gall_id);
	if (empty($album)) {
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
//		if (PMA_USR_BROWSER_AGENT == 'IE') {
//			header('Content-Disposition: inline; filename="settings_'.$gallery_type.'.xml"');
//			header('Expires: 0');
//			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//			header('Pragma: public');
//		} else {
			header('Content-Disposition: attachment; filename="settings_'.$gallery_type.'.xml"');
			header('Expires: 0');
			header('Pragma: no-cache');
//		}
            $file = fgallery_get_config_url($gall_id, $view);
			readfile($file);
            die();
	} else {
            header("Content-type: text/xml");
	}
        fgallery_build_configXML($config, $gallery_type);    
        die();
        
    } else {
        die();
    }
    die();
}

/**
 * Builds the XML DOMDocument 
 * 
 * @param type $config
 * @return XML file 
 */
function fgallery_build_configXML($config, $gallery_type) {
    // create new XML document
    $configXML = fgallery_create_document();
    // create root element
    $xmlRoot = fgallery_create_element('config', '', $configXML);
    fgallery_append_child($configXML, $xmlRoot);
    // sort array to group by parameter's group
    ksort($config);
    // set current group to empty string be default
    $current_group = '';
    foreach ($config as $key=>$value) {   
        // check if this is a parameter
        if (preg_match('/sc_(.*)__(.*)/', $key, $matches)){
            if ($matches[1]!='') {
                // if group name is not empty
                if ($current_group != $matches[1]){
                    // add new group of the parameters to the list
                    $group = fgallery_create_element($matches[1], '', $configXML);
                    fgallery_append_child($xmlRoot, $group);
                }
                if ($matches[2]!='') {
                    // if param name is not empty
//                    if (preg_match('/color/i',$matches[2])) {
//                        // if this is color control parameter
//                        if (strpos($value,'#')===false) {
//                            $val = $value;    
//                        } else {
//                            $val = '0x'.str_replace('#','',$value);
//                        }
//                    }
                    
                    if (preg_match('/\#[0-9A-Fa-f]{6}/', $value) && strlen($value) == 7){
                        if (strpos($value,'#')===false) {
                            $val = $value;
                        } else {
                            $val = '0x'.str_replace('#','',$value);
                        }                    
                    }else {
                        $val = $value;
                    }
                    if ($matches[2] == MUSIC_PATH) {
                        // if this is music parameter
                        $matches[2] = get_option('1_flash_gallery_'.$gallery_type , MUSIC_PATH);
                    }
                    // add new param to group element
                    $param = fgallery_create_element($matches[2], $val, $configXML);
                    fgallery_append_child($group, $param);
                }
            }
            $current_group = $matches[1];
        }
    }
    // save document as XML
    fgallery_show_document($configXML);
}

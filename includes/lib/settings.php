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
            return fgallery_default_album_settings(3);
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
    return $settings[$name];
}

/**
 * Prepare post data before inserting into database
 * @param array $data
 * @return type 
 */
function fgallery_prepare_settings($data) {
    if ($data['gallery']['gall_type'] != $data['gallery']['old_gall_type'])
    $data = fgallery_default_album_settings($data['gallery']['gall_type']);
    unset($data['gallery']);
    return $data;
}

/**
 * Returns default gallery settings if there are no user customized
 * @return array 
 */
function fgallery_default_album_settings($gall_type) {
    $settings = array();
    $params_xml = simplexml_load_file(FGALLERY_ABSPATH . '/xml/params_'.$gall_type.'.xml');   
    if (FGALLERY_PHP4_MODE) {
      foreach ($params_xml->params->group as $g) {
            $temp = $g->attributes();
            foreach ($g->p as $p) {
              $p_temp = $p->attributes();
              $name = 'sc_' . $temp['name'] . '__' . $p_temp['name'];
              if ($p['control'] == 'color') {
                $settings[$name] = str_replace('0x','#',(string)$p_temp['default']);
              } else {
                $settings[$name] = (string)$p_temp['default'];
              }
            }
      }
    //end php 4 clause
  } else { 
    // if php 5 is running
      foreach ($params_xml->params->group as $g) {
          foreach ($g->p as $p) {
              $name = 'sc_' . $g['name'] . '__' . $p['name'];
              if ($p['control'] == 'color') {
                $settings[$name] = str_replace('0x','#',(string)$p['default']);
              } else {
                $settings[$name] = (string)$p['default'];
              }
          }
      }
    // end php condition
   }
    
   return $settings;
}
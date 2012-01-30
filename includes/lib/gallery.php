<?php

/*
 * Here are functions to get data about Gallery from database
 * and operations over galleries
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Saves changes to gallery with given id into db
 * @global type $wpdb
 * @global type $current_user
 * @param array $data (actually $_POST)
 * @param integer $id
 * @return integer 
 */
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
		$wpdb->insert(ALBUMS_TABLE, array('gall_name' => $name,
                                                  'gall_description' => $desc, 
                                                  'gall_createddate' => date("Y-m-d H:i:s"),
                                                  'gall_createdby' => $user_id,
                                                  'gall_published' => $published,
                                                  'gall_width'=>$width,
                                                  'gall_height'=> $height,
                                                  'gall_bgcolor'=> $bgcolor,
                                                  'gall_type'=> $type,
                                                  'gall_order' => $max + 1));
		return $wpdb->insert_id;
	}
	if ($name != '') {
		$wpdb->update(ALBUMS_TABLE, array('gall_name' => $name,
                                                  'gall_description' => $desc,
                                                  'gall_published' => $published,
                                                  'gall_width'=>$width,
                                                  'gall_height'=> $height,
                                                  'gall_bgcolor'=> $bgcolor,
                                                  'gall_type'=> $type),
                                            array('gall_id' => $id));
		return $id;
        } else {
            return 0;
        }
}

/**
 * Saves data about Gallery Image at List Images page
 * @global type $wpdb
 * @param array $data (actually $_POST)
 */
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
        $text = htmlentities(urldecode($data['img_text']), ENT_NOQUOTES, "UTF-8");
	$url = urldecode($data['img_url']);
        $img_extra = array('img_type'=>$data['img_type'],'img_text'=>$text);
        $extra = serialize($img_extra);
	if ($name != '') {
		$wpdb->update(IMAGES_TABLE, array('img_caption' => $name, 'img_description' => $desc), array('img_id' => $id));
		$wpdb->update(IMAGES_TO_ALBUMS_TABLE, array('img_url' => $url, 'img_extra' => $extra), array('img_id' => $id, 'gall_id' => $gall_id));
                die('1');
        } else {
                die('0');
        } 
}

/**
 * Deletes gallery with given ID from db (does not delete files)
 * @global type $wpdb
 * @param integer $id
 * @return boolean 
 */
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

/**
 * Returns paginated items for Galleries List
 * @global type $wpdb
 * @param integer $pagenum
 * @param integer $per_page
 * @param integer $sort
 * @return array 
 */
function fgallery_get_albums($pagenum, $per_page, $sort) {
    global $wpdb;
    $cond = fgallery_sort_albums_condition($sort);
            $albums = $wpdb->get_results("SELECT * FROM " . ALBUMS_TABLE." 
                                          ORDER BY ".$cond." 
                                          LIMIT ".($pagenum-1)*$per_page.",".$pagenum*$per_page, 'ARRAY_A');
    return $albums;
}

/**
 * Counts the number of galleries
 * @global type $wpdb
 * @return type 
 */
function fgallery_albums_count() {
    global $wpdb;
            $count = $wpdb->get_var("SELECT COUNT(*) FROM " .ALBUMS_TABLE);
    return $count;
}

/**
 * Counts images inside given folder or if folder = 0 inside gallery root folder
 * @global type $wpdb
 * @param type $folder
 * @return type 
 */
function fgallery_count_gallery_album_images($folder) {
    global $wpdb;
          $count = $wpdb->get_var("SELECT COUNT(*) FROM ".IMAGES_TO_ALBUMS_TABLE." WHERE `gall_folder` = ".$folder);
    return $count;
}

/**
 * Returns gallery by given ID
 * @global type $wpdb
 * @param type $id
 * @return type 
 */
function fgallery_get_album($id) {
    global $wpdb;
            $album = $wpdb->get_row("SELECT * FROM ".ALBUMS_TABLE." WHERE gall_id = ".$id, ARRAY_A);
    return $album;
}

/**
 * Returns the list of images from defined gallery
 * @global type $wpdb
 * @param integer $gall_id
 * @param integer $folder
 * @return array 
 */
function fgallery_get_album_images($gall_id, $folder = 0, $sort = 10) {
    global $wpdb;
	 switch ($sort) {
            case 0: 
                $order = 'a.img_vs_folder DESC, a.img_caption ASC';
            break;
            case 1:
                $order = 'a.img_vs_folder DESC, a.img_caption DESC';
            break;
            case 2:
                $order = 'a.img_vs_folder DESC, a.img_date ASC';
            break;
            case 3:
                $order = 'a.img_vs_folder DESC, a.img_date DESC';
            break;
            case 4:
                $order = 'a.img_vs_folder DESC, a.img_size ASC';
            break;
            case 5:
                $order = 'a.img_vs_folder DESC, a.img_size DESC';
            break;
            default:
                $order = 'a.img_vs_folder DESC, b.img_order ASC';
            break;
    }

      $items = $wpdb->get_results("SELECT a.*, b.* FROM " . IMAGES_TABLE . " as a
                                   LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE." as b ON (a.img_id = b.img_id)
                                   WHERE b.gall_id = " .$gall_id." AND b.gall_folder = ".$folder." 
                                   ORDER BY ".$order, 'ARRAY_A');
    return $items;
}

/**
 * Return galleries for gallery type 13
 * @param type $gall_id
 * @return type 
 */

function fgallery_get_gallery_items($gall_id) {
    global $wpdb;
    $items = $wpdb->get_results("SELECT a.gall_id as id, a.gall_name, a.gall_cover, a.gall_type, b.* FROM " . ALBUMS_TABLE . " as a
                                   LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE." as b ON (a.gall_id = b.img_id)
                                   WHERE b.gall_id = " .$gall_id."
                                   ORDER BY b.img_order", 'ARRAY_A');
    return $items;
}

/**
 * Count for images that are not in given gallery
 * @global type $wpdb
 * @param int $folder
 * @param int $id
 * @return int 
 */
function fgallery_images_count_rest($folder, $id) {
    global $wpdb;
            $count = $wpdb->get_var("SELECT COUNT(*) FROM " .IMAGES_TABLE." 
                                     WHERE `img_parent` = ".$folder." AND `img_vs_folder` IN (0,1)
                                     AND `img_id` NOT IN (
                                                          SELECT img_id FROM ". IMAGES_TO_ALBUMS_TABLE."
                                                          WHERE gall_id = ".$id.")");
    return $count;
}

/**
 * Returns paginated Images List for Add images page
 * @global type $wpdb
 * @param int $id
 * @param int $pagenum
 * @param int $per_page
 * @param int $folder
 * @param int $sort
 * @return type 
 */
function fgallery_get_restof_images($id, $pagenum, $per_page, $folder, $sort = 3) {
    global $wpdb;
    $cond = fgallery_sort_images_condition($sort);
       $images = $wpdb->get_results("SELECT * FROM ". IMAGES_TABLE. " 
                                     WHERE img_id NOT IN (
                                        SELECT img_id FROM ". IMAGES_TO_ALBUMS_TABLE." 
                                        WHERE gall_id = ".$id.") 
                                     AND `img_parent` = ".$folder." AND `img_vs_folder` IN (0,1)
                                     ORDER BY `img_vs_folder` DESC, ".$cond." 
                                     LIMIT ".($pagenum-1)*$per_page.",".$pagenum*$per_page, ARRAY_A);
    return $images;
}

/**
 * Returns the list of the galleries that could be added to the current gallery
 * with gallery type 13
 * 
 * @global object $wpdb
 * @param int $id
 * @return array
 */
function fgallery_get_restof_galleries($id) {
    global $wpdb;
    $galleries = $wpdb->get_results("SELECT * FROM " .ALBUMS_TABLE. "
                                     WHERE gall_id NOT IN (
                                        SELECT img_id FROM " .IMAGES_TO_ALBUMS_TABLE. "
                                        WHERE gall_id = ".$id.")
                                     AND gall_id <> ".$id."
                                     AND gall_type <> 13
                                     ORDER BY gall_order", ARRAY_A);
    return $galleries;
}

function fgallery_addgallery_box($id) {
    $galleries = fgallery_get_restof_galleries($id);
    echo fgallery_render_addgallery_box($galleries, $id);
}

/**
 * Returns Add Images box
 * @param int $id
 * @param int $pagenum
 * @param int $per_page
 * @param int $folder 
 */
function fgallery_addimage_box($id, $pagenum, $per_page, $folder) {
    $images = fgallery_get_restof_images($id, $pagenum, $per_page, $folder);
    echo fgallery_render_addimage_box($images, $id);
}

/**
 * Returns the name of the gallery
 * @global type $wpdb
 * @param int $id
 * @return string 
 */
function fgallery_get_album_name($id) {
	global $wpdb;
		$name = $wpdb->get_row("SELECT gall_name FROM ". ALBUMS_TABLE ." 
                                        WHERE gall_id =".$id, ARRAY_A);
	return $name['gall_name'];
}

function fgallery_slideshow($album) {
    return fgallery_create_slideshow($album);
}

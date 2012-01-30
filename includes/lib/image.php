<?php

/*
 * Here we have all the functions to get data about images
 * and operations over images
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Returns Image by given image ID
 * @global type $wpdb
 * @param integer $id
 * @return array 
 */
function fgallery_get_image($id) {
    global $wpdb;
        $image = $wpdb->get_row("SELECT * FROM ".IMAGES_TABLE." WHERE img_id = ".$id, ARRAY_A);
    return $image;
}

/**
 * Returns the path to image preview for given image id
 * if there is no path in database for preview
 * it is created automatically from img_path image
 * and is written to the images table
 * 
 * @global type $wpdb
 * @param int $id
 * @return string
 */
function fgallery_get_image_preview($id) {
    global $wpdb;
    $siteurl = get_option('siteurl');
    $image = fgallery_get_image($id);
    // if there is already entry in database about image preview just return
    // preview path
    if ($image['img_preview_path']!='' && file_exists(ABSPATH.$image['img_preview_path'])) {
        return $siteurl.'/'.$image['img_preview_path'];
    } else {
        return $siteurl.'/'.$image['img_path'];
        $preview_opt = get_option('1_flash_gallery_preview_opt');
        $preview_size = get_option('1_flash_gallery_preview_size',200);
        // if preview saving is enabled
        if ($preview_opt) {
            // set the name to preview file according to big file name
            $preview = preg_replace("/[^0-9]/", '', $image['img_path']);
            $file_parts = pathinfo($image['img_path']);
            if ($preview == '') {
                if (defined('PATHINFO_FILENAME')) {
                    $preview = pathinfo($file_parts['basename'],PATHINFO_FILENAME);
                } elseif (strstr($file_parts['basename'], '.')) {
                    $preview = substr($file_parts['basename'],0,strrpos($file_parts['basename'],'.'));
                }
            }
            $preview_name = FGALLERY_DIR.'/'.$preview.'_prev.'.$file_parts['extension'];
            $targetFile = ABSPATH.$image['img_path'];
            // check if big image file exists and readable
            if (file_exists($targetFile) && is_readable($targetFile)) {
                // create preview image for given image
                //var_dump($targetFile); die();
                fgallery_resemple($targetFile, $preview_size, $preview_size, $preview_name);
                $img_preview_path = str_replace(ABSPATH,'',$preview_name);
                // save preview path to database
                $wpdb->update(IMAGES_TABLE, array('img_preview_path'=>$img_preview_path), array('img_id'=>$id));
                return $siteurl.'/'.$img_preview_path;
            }
        }
        
    }
    // if there is no preview and preview creation is disabled
    // return path to big image
    return $siteurl.'/'.$image['img_path'];
}

/**
 * Returns the folder name by given id
 * @global type $wpdb
 * @param integer $id
 * @return string
 */
function fgallery_get_folder_name($id) {
    global $wpdb;
       $name = $wpdb->get_var("SELECT `img_caption` FROM ".IMAGES_TABLE." WHERE `img_id` =".$id);
    return $name;
}

/**
 * Returns paginated Images List
 * @global type $wpdb
 * @param integer $pagenum
 * @param integer $per_page
 * @param integer $parent
 * @param integer $sort
 * @return array 
 */
function fgallery_get_images($pagenum, $per_page, $parent = 0, $sort = 3) {
    global $wpdb;
    $cond = fgallery_sort_images_condition($sort);
            $images = $wpdb->get_results("SELECT * FROM ". IMAGES_TABLE. " 
                        WHERE `img_parent` = ".$parent." AND `img_vs_folder` IN (0,1) 
                        ORDER BY `img_vs_folder` DESC, ".$cond." 
                        LIMIT ".($pagenum-1)*$per_page.",".$pagenum*$per_page, 'ARRAY_A');
    return $images;
}

/**
 * Counts the number of images in the folder (0 - root folder or not in any folder)
 * @global type $wpdb
 * @param integer $folder
 * @return integer 
 */
function fgallery_images_count($folder) {
    global $wpdb;
         $count = $wpdb->get_var("SELECT COUNT(*) FROM " .IMAGES_TABLE." WHERE 
                    `img_parent` = ".$folder." AND `img_vs_folder` IN (0,1)");
    return $count;
}

/**
 * Returns images from NextGEN plugin
 * @global type $wpdb
 * @return array 
 */
function fgallery_get_nextgen_images() {
    global $wpdb;
        $images = $wpdb->get_results("SELECT a.pid, a.description, a.alttext, a.filename, b.path 
                         FROM ".$wpdb->prefix."ngg_pictures as a 
                         LEFT JOIN ".$wpdb->prefix."ngg_gallery as b ON (a.galleryid = b.gid)");
    return $images;
}

/**
 * Returns all folders
 * @global type $wpdb
 * @return type 
 */
function fgallery_get_folders(){
    global $wpdb;
           $folders = $wpdb->get_results("SELECT * FROM " . IMAGES_TABLE." 
                    WHERE `img_vs_folder` = 1 ORDER BY `img_caption`", 'ARRAY_A');
    return $folders;
}

/**
 * Deletes image with given id from db and deletes file
 * @global type $wpdb
 * @param integer $id
 * @return boolean 
 */
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
                            // delete all the files linked to current image entry
                                @unlink(ABSPATH.$image['img_path']);
                                @unlink(ABSPATH.$image['img_preview_path']);
                                @unlink(ABSPATH.$image['img_full_view_path']);
                                $wpdb->query("DELETE FROM ".IMAGES_TABLE." WHERE `img_id` = ".$id);
                        }
                }
            return true;
        } else {
            return false;
        }
}

/**
 * Saves changes for image with given id into db
 * @global type $wpdb
 * @param array $data (actually $_POST)
 * @param integer $id
 * @return type 
 */
function fgallery_edit_image($data, $id, $files = array()) {
    global $wpdb;
    $name = htmlentities($data['fgallery_image_caption'], ENT_NOQUOTES, "UTF-8");
    $desc = htmlentities($data['fgallery_image_description'], ENT_NOQUOTES, "UTF-8");
    $img_preview_path = fgallery_handle_upload_preview($data, $files);
    if ($img_preview_path == '') {
        $img_preview_path = $data['img_preview_path'];
    }
    if ($name != '') {
        $wpdb->update(IMAGES_TABLE, array('img_caption' => $name, 
                                          'img_description' => $desc,
                                          'img_preview_path'=> $img_preview_path),
                      array('img_id' => $id));
        return 1;
    } else {
        return 0;
    } 
}

/**
 * Returns the galleries that have given image in them
 * @global type $wpdb
 * @param integer $id
 * @return array 
 */
function fgallery_image_get_albums($id) {
    global $wpdb;
           $albums = $wpdb->get_results("SELECT a.gall_name, a.gall_id FROM ". ALBUMS_TABLE ." as a 
                                        LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE. " as b ON (a.gall_id = b.gall_id)
                                        WHERE b.img_id =".$id, ARRAY_A);
    return $albums;
}

/**
 * Resemple images. If new_name is not empty saves the resampled image to new file
 * 
 * @param string $fullpath 
 * @param int $width
 * @param int $height
 * @param string $new_name
 */
function fgallery_resemple($fullpath, $width, $height, $new_name = '') {
    if (FGALLERY_PHP4_MODE){
        $thumb = new Thumbnail($fullpath, 0);
    } else {
        $thumb = PhpThumbFactory::create($fullpath, array(), false, 0);
    }
    $thumb->resize($width, $height);
    if ($new_name == '') {
        $thumb->save($fullpath); 
    } else {
        $thumb->save($new_name);
    }
}

/**
 * Watermark given image
 * @param string $fullpath
 * @return image 
 */
function fgallery_watermark($fullpath) {
    $wm_file = get_option('1_flash_gallery_watermark_path','');
    if ($wm_file == '') return false;
    $wm_place = get_option('1_flash_gallery_watermark_place','C');
    if (FGALLERY_PHP4_MODE){
        $thumb = new Thumbnail($fullpath, 0);
    } else {
        $thumb = PhpThumbFactory::create($fullpath, array(), false, 0);
    }
    return $thumb->watermarkImageGD($fullpath,$fullpath,$wm_file,$wm_place);
}

/**
 * Watermark given image by id
 * @param string $fullpath
 * @return image 
 */
function fgallery_watermark_by_id($id) {
    //$path = $_SERVER['DOCUMENT_ROOT']. EXTRA_DIR;
    $image = fgallery_get_image($id);
    $img_path = ABSPATH.$image['img_path'];
    fgallery_watermark($img_path);
}

/**
 * Rotates given image by id
 * @param string $fullpath
 * @return image 
 */
function fgallery_rotate_by_id($id, $direction) {
    //$path = $_SERVER['DOCUMENT_ROOT']. EXTRA_DIR;
    $image = fgallery_get_image($id);
    $img_path = ABSPATH.$image['img_path'];
    if (FGALLERY_PHP4_MODE){
        $thumb = new Thumbnail($img_path, 0);
    } else {
        $thumb = PhpThumbFactory::create($img_path, array(), false, 0);
    }
    $thumb->rotateImage($direction);
    $thumb->save($img_path);
    if ($image['img_preview_path'] != '') {
        $img_path = ABSPATH.$image['img_preview_path'];
        if (FGALLERY_PHP4_MODE){
            $thumb_prev = new Thumbnail($img_path, 0);
        } else {
            $thumb_prev = PhpThumbFactory::create($img_path, array(), false, 0);
        }
        $thumb_prev->rotateImage($direction);
        $thumb_prev->save($img_path);
    }
   if ($image['img_full_view_path'] != '') {
        $img_path = ABSPATH.$image['img_full_view_path'];
        if (FGALLERY_PHP4_MODE){
            $thumb_full = new Thumbnail($img_path, 0);
        } else {
            $thumb_full = PhpThumbFactory::create($img_path, array(), false, 0);
        }
        $thumb_full->rotateImage($direction);
        $thumb_full->save($img_path);
    }
}

/**
 * Handles massedit actions
 * @global type $wpdb
 * @global type $current_user 
 */
function fgallery_massedit(){
    global $wpdb;
    global $current_user;

    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_massedit_field'],'fgallery_massedit')) {
         if (is_numeric($_POST['image_action'])){
		switch ($_POST['image_action']) {
                    case '-3':
                        if (!empty($_POST['image'])){
                                $ids = $_POST['image'];
                                foreach ($ids as $id) {
                                    fgallery_watermark_by_id($id);
                                }
                            }
                            echo fgallery_images_url();
                            break;
                    case '-2':
                            if (!empty($_POST['image'])){
                                $ids = $_POST['image'];
                                foreach ($ids as $id) {
                                        fgallery_delete_image($id);
                                }
                            }
                            echo fgallery_images_url();
                            break;
                    case '-1':
                            if (!empty($_POST['image'])) {
                                $ids = $_POST['image'];
                                $user_id = $current_user->ID;//get_current_user_id();
                                $max = $wpdb->get_var("SELECT MAX(`gall_order`) FROM ".ALBUMS_TABLE);
                                $wpdb->insert(ALBUMS_TABLE, array('gall_name' => __('New gallery'), 'gall_createddate' => date("Y-m-d H:i:s"), 'gall_createdby' => $user_id, 'gall_published' => 1, 'gall_order' => $max+1, 'gall_type' => 3));
                                $gall_id = $wpdb->insert_id;
                                foreach ($ids as $id) {
                                                $image = fgallery_get_image($id);
                                                if ($image['img_vs_folder'] == 1) {
                                                        $images = fgallery_get_images(1, 99999999, $image['img_id'], $sort = 3);
                                                        foreach ($images as $row) {
                                                                $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id'=>$row['img_id'],
                                                                                                            'gall_id'=>$gall_id,
                                                                                                            'img_order' => 0));	
                                                        }
                                                } elseif ($image['img_vs_folder'] == 0) {
                                                        $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id'=>$id,
                                                                                                    'gall_id'=>$gall_id,
                                                                                                    'img_order' => 0));
                                                }
                                }
                                echo admin_url('admin.php?page=fgallery&action=edit&id='.$gall_id);
                            }
                            break;
                    default:
                            if (!empty($_POST['image'])) {
                                $ids = $_POST['image'];
                                $ids_string = implode(",",$ids);
                                $wpdb->query("UPDATE ".IMAGES_TABLE." SET `img_parent` =".$_POST['image_action']."
                                              WHERE `img_id` IN (".$ids_string.") AND `img_vs_folder` = 0");
                            }
                            echo fgallery_get_folder_url($_POST['image_action']);
                            break;
                }
	} else {
            $gall_id = str_replace('gall_','',$_POST['image_action']);
            if (is_numeric($gall_id)) {
                if (!empty($_POST['image'])) {
                    $ids = $_POST['image'];
                    foreach ($ids as $id) {
                        $image = fgallery_get_image($id);
                        if ($image['img_vs_folder'] == 1) {
                                $images = fgallery_get_images(1, 99999999, $image['img_id'], $sort = 3);
                                foreach ($images as $row) {
                                        $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id'=>$row['img_id'],
                                                                                    'gall_id'=>$gall_id,
                                                                                    'img_order' => 0));	
                                }
                        } elseif ($image['img_vs_folder'] == 0) {
                                $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id'=>$id,
                                                                            'gall_id'=>$gall_id, 
                                                                            'img_order' => 0));
                        }
                    }
                    echo admin_url('admin.php?page=fgallery&action=edit&id='.$gall_id);
                }
            } else {
                    echo fgallery_images_url();
            }
	}
    } else {
            echo fgallery_images_url();
    }
    die();
}
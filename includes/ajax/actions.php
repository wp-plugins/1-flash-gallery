<?php

/*
 * Here are all actions implementations
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Action to show resized image
 * @param string $filename
 * @param integer $width
 * @param integer $height
 * @return image
 */
function fgallery_show_image(){
    if (!isset($_GET['filename'])) {
        exit;
    }
    if (isset($_GET['width']) && is_numeric($_GET['width'])) {
        $width = $_GET['width'];
    } else {
        $width = 0;
    }
    if (isset($_GET['height']) && is_numeric($_GET['height'])) {
        $height = $_GET['height'];
    } else {
        $height = 0;
    }
    if (FGALLERY_PHP4_MODE) {
        $thumb = new Thumbnail($_GET['filename'], 1);
    } else {
        $thumb = PhpThumbFactory::create($_GET['filename'], array(), false, 1);
    }
    $thumb->resize($width,$height);
    $thumb->show();
    die();
}

/**
 * Returns text for image from gallery
 * @global type $wpdb 
 * @param integer $img_id
 * @param integer $gall_id
 * @return string
 */
function fgallery_get_image_text(){
    global $wpdb;
    if (isset($_GET['img_id']) && is_numeric($_GET['img_id'])) {
        $img_id = $_GET['img_id'];
    } else {
        die();
    }
    if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
        $gall_id = $_GET['gall_id'];
    } else {
        die();
    }
    $res = $wpdb->get_row("SELECT `img_extra` FROM ".IMAGES_TO_ALBUMS_TABLE." WHERE gall_id = ".$gall_id." AND img_id = ".$img_id, ARRAY_A);
    if ($res) {
        $img_extra = unserialize($res['img_extra']);
        echo fgallery_escape_string($img_extra['img_text']);
    }
    die();
}

/**
 * Rotate image
 * @param integer $id
 * @param string $dir
 * @return image
 */
function fgallery_rotate_image(){
    if (isset($_POST['id']) && is_numeric($_POST['id'])) {
        $id = $_POST['id'];
        if (isset($_POST['dir']) && is_numeric($_POST['dir'])) {
            $dir = $_POST['dir'];
        } 
        if ($dir){
            fgallery_rotate_by_id($id, 'CW');
            die('1');
        } else {
            fgallery_rotate_by_id($id, 'CC');
            die('1');
        }
    }
    die();
}

/**
 * Saves Gallery Image data
 */
function fgallery_save_album_image() {
    fgallery_edit_album_image($_POST);
    die(); // this is required to return a proper result
}

/**
 * Sorts Gallery images
 * @global type $wpdb 
 */
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

/**
 * Removes images from gallery images list
 * @global type $wpdb 
 */
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

/**
 * Sets image as cover for gallery
 * @global type $wpdb 
 */
function fgallery_set_album_cover(){
    global $wpdb;
    if (isset($_POST['img_id']) && isset($_POST['gall_id']) && is_numeric($_POST['img_id']) && is_numeric($_POST['gall_id'])) {
            $id = $_POST['img_id'];
            $gall_id = $_POST['gall_id'];
            $wpdb->update(ALBUMS_TABLE, array('gall_cover'=>$id), array('gall_id' => $gall_id));
    }
    die('1');
}

/**
 * Deletes image from database
 */
function fgallery_delete_image_ajax() {
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $id = $_POST['id'];
                fgallery_delete_image($id);
        }  
        die('1');
}

/**
 * Adds image to folder
 * @global type $wpdb 
 */
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

/**
 * Deletes Gallery
 */
function fgallery_delete_gallery(){
        if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                $id = $_POST['id'];
                fgallery_delete_album($id);
        }
    die();
}

/**
 * Sorts gallery
 * @global type $wpdb 
 */
function fgallery_sort_galleries(){
    global $wpdb;
    if (isset($_POST['id']) && is_numeric($_POST['id']) && isset($_POST['order']) && is_numeric($_POST['order'])) {
            $id = $_POST['id'];
            $order = $_POST['order'];
            $wpdb->update(ALBUMS_TABLE, array('gall_order' => $order), array('gall_id' => $id));
    }
    die();
}

/**
 * Renders create folder form
 */
function fgallery_folder_form(){
    if (isset($_GET['type']) && is_numeric($_GET['type'])) {
        $type = $_GET['type'];
    } else {
        $type = 1;
    }
    if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
        $gall_id = $_GET['gall_id'];
    } else {
        $gall_id = 0;
    }
    if (isset($_GET['upload']) && is_numeric($_GET['upload'])) {
        $upload = $_GET['upload'];
    } else {
        $upload = 0;
    }
    fgallery_create_folder_form($gall_id, $type, $upload);
    die();
}

/**
 * Saves new folder to database
 */
function fgallery_add_folder(){
    global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_folder_field'],'fgallery_create_folder')) {
        if (is_numeric($_POST['folder_type'])){
            $save = $wpdb->insert(IMAGES_TABLE, array('img_caption'=>$_POST['fgallery_image_caption'],
                                                  'img_vs_folder' => $_POST['folder_type'],
                                                  'img_parent' => 0,
                                                  'img_date' => date("Y-m-d H:i:s")));
        } else {
            die(0);
        }
	if ($_POST['folder_type'] == 2) {
            if (is_numeric($_POST['gall_id'])){
                $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id'=>$wpdb->insert_id ,
                                                        'gall_id' => $_POST['gall_id'],
                                                        'gall_folder' => 0));
            } else {
                die(0);
            }
	}
       if (isset($_POST['upload']) && is_numeric($_POST['upload'])) {
           if ($_POST['upload']) {
               $folders = fgallery_get_folders();
               echo fgallery_folder_list_options($folders, $wpdb->insert_id);
               die();
           }
       }
    }
    die(1);
}

/**
 * Adds image to session variable
 */
function fgallery_add_image(){
    if (!isset($_SESSION)) {
        @session_start();
    }
    if (!empty($_POST) && isset($_POST['state']) && is_numeric($_POST['state'])) {
        if ($_POST['state'] == 1) {
            $_SESSION['image'][] = $_POST['value'];
        } else {
            $key = array_search($_POST['value'], $_SESSION['image']);
            unset($_SESSION['image'][$key]);
        }
        die();
    }
    die();
}

/**
 * Adds gallery to gallery type 13
 */
function fgallery_addgalleries(){
    global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_add_galleries_to_gallery'],'fgallery_add_gallery')) {
        if (isset($_POST['gall_id']) && is_numeric($_POST['gall_id'])) {
            $gall_id = $_POST['gall_id'];
        } else {
            die();
        }
        if (isset($_POST['gallery'])) {
             $ids = $_POST['gallery'];
             if (count($ids) > 0) {
                foreach ($ids as $id) {
                    $gallery = fgallery_get_album($id);
                    if (!empty($gallery)) {
                        $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id' => $id,
                                            'gall_id' => $gall_id,
                                            'img_order' => 0,
                                            'gall_folder' => 0));
                    }
                }
            }
        }
    }
    echo fgallery_get_edit_url_clean($gall_id);
    die();
}
/**
 * Adds images to gallery
 */
function fgallery_addimages() {
   global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_add_images_to_gallery'],'fgallery_add_images')) {
        if (isset($_SESSION['image'])){
            $ids = $_SESSION['image'];
        }
        if (empty($ids) && isset($_POST['image'])) {
             $ids = $_POST['image'];
        }
        if (isset($_POST['gall_id']) && is_numeric($_POST['gall_id'])) {
            $gall_id = $_POST['gall_id'];
        } else {
            die();
        }
        if (isset($_POST['fid']) && is_numeric($_POST['fid'])){
            $fid = $_POST['fid'];
        } else {
            $fid = 0;
        }
        if (count($ids) > 0) {
            foreach ($ids as $id) {
                $image = fgallery_get_image($id);
                if ($image['img_vs_folder'] == 1) {
                        $wpdb->insert(IMAGES_TABLE, array('img_caption' => $image['img_caption'],
                                                          'img_vs_folder' => 2,
                                                          'img_date'=>date("Y-m-d H:i:s")));
                        $parent = $wpdb->insert_id;
                        $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id' => $parent,
                                                                    'gall_id' => $gall_id,
                                                                    'img_order' => 0,
                                                                    'gall_folder' => 0));
                        $images = fgallery_get_images(1, 99999999, $image['img_id'], $sort = 3);
                        foreach ($images as $row) {
                           $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id' => $row['img_id'],
                                                                       'gall_id' => $gall_id,
                                                                       'img_order' => 0,
                                                                       'gall_folder' => $parent));	
                        }
                } elseif ($image['img_vs_folder'] == 0) {
                    $wpdb->insert(IMAGES_TO_ALBUMS_TABLE, array('img_id' => $id,
                                                                'gall_id' => $gall_id,
                                                                'img_order' => 0,
                                                                'gall_folder' => $fid));
                }
            }
        }
        @session_unset();
        @session_destroy();
    } 
    echo fgallery_get_edit_url_clean($gall_id);
    die();
}

function fgallery_insert_gallery(){
if (!empty($_POST) && wp_verify_nonce($_POST['insert_gallery_field'], 'insert_gallery')){
    $send_ids = $_POST['gallery'];
    $type = $_POST['sn_type'];
    if ( !empty($send_ids) ) {
        $html = '';
        foreach ($send_ids as $id){
            $gall_code = fgallery_do_shortcode($id, $type).'<br />';
            $html .= apply_filters('media_send_to_editor', $gall_code, $id, '');
        }
        echo $html;
    }
}
die();
}

function fgallery_add_preview_form() {
    if (isset($_GET['img_id']) && is_numeric($_GET['img_id'])) {
        $id = $_GET['img_id'];
        $image = fgallery_get_image($id);
        echo fgallery_render_add_preview_form($image);
    }
    die();
}

function fgallery_add_preview(){
    global $wpdb;
    if (!empty($_POST)) {
        $post = $_POST;
        if (isset($post['img_id']) && is_numeric($post['img_id'])) {
            $id = $post['img_id'];
            if (!empty($_FILES)) {
                $files = $_FILES;
                $img_preview_path = fgallery_handle_upload_preview($post, $files);
                $wpdb->update(IMAGES_TABLE, array('img_preview_path'=> $img_preview_path),array('img_id' => $id));
            }
        }
    }
    die($id);
}

function fgallery_add_items($admin_bar)
{

    $admin_bar->add_menu( array(
        'id'    => 'fgallery',
        'title' => '1 Flash Gallery',
        'href'  => admin_url('admin.php?page=fgallery_main'),
        'meta'  => array(
            'title' => __('1 Flash Gallery', 'fgallery'),
        ),
    ) );

    $admin_bar->add_menu( array(
        'id'    => 'fgallery_main',
        'parent' => 'fgallery',
        'title' => __('Main Page', 'fgallery'),
        'href'  => admin_url('admin.php?page=fgallery_main'),
        'meta'  => array(
            'title' => __('Main Page', 'fgallery'),
        ),
    ) );
    
    $admin_bar->add_menu( array(
        'id'    => 'fgallery_list',
        'parent' => 'fgallery',
        'title' => __('Galleries List', 'fgallery'),
        'href'  => admin_url('admin.php?page=fgallery'),
        'meta'  => array(
            'title' => __('Galleries List', 'fgallery'),
        ),
    ) );
    
    $admin_bar->add_menu( array(
        'id'    => 'fgallery_add',
        'parent' => 'fgallery',
        'title' => __('Create Gallery', 'fgallery'),
        'href'  => admin_url('admin.php?page=fgallery_add'),
        'meta'  => array(
            'title' => __('Create Gallery', 'fgallery'),
        ),
    ) );
    
    $admin_bar->add_menu( array(
        'id'    => 'new_fgallery_add',
        'parent' => 'new-content',
        'title' => __('Gallery', 'fgallery'),
        'href'  => admin_url('admin.php?page=fgallery_add'),
        'meta'  => array(
            'title' => __('Gallery', 'fgallery'),
        ),
    ) );
    
    $admin_bar->add_menu( array(
        'id'    => 'fgallery_images',
        'parent' => 'fgallery',
        'title' => __('Images List', 'fgallery'),
        'href'  => admin_url('admin.php?page=fgallery_images'),
        'meta'  => array(
            'title' => __('Images List', 'fgallery'),
        ),
    ) );
    
    $admin_bar->add_menu( array(
        'id'    => 'fgallery_upload',
        'parent' => 'fgallery',
        'title' => __('Upload Images', 'fgallery'),
        'href'  => admin_url('admin.php?page=fgallery_upload'),
        'meta'  => array(
            'title' => __('Upload Images', 'fgallery'),
        ),
    ) );
    
     $admin_bar->add_menu( array(
        'id'    => 'fgallery_settings',
        'parent' => 'fgallery',
        'title' => __('Settings', 'fgallery'),
        'href'  => admin_url('admin.php?page=fgallery_settings'),
        'meta'  => array(
            'title' => __('Settings', 'fgallery'),
        ),
    ) );

}
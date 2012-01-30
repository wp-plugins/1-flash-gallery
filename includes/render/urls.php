<?php

/*
 * Functions to get proper urls
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Returns thumbnail create URL
 * @param integer $width
 * @return string 
 */
function fgallery_create_thumb_url($width) {
    if (is_numeric($width)) {
        return FGALLERY_SHOW_IMAGE_ACTION.'&amp;width='.$width.'&amp;filename='.EXTRA_DIR;
    } else {
        return COVER_PATH;
    }
}

// Images List URL
function fgallery_images_url() {
    return admin_url('admin.php?page=fgallery_images');
}

// Upload Images page URL
function fgallery_get_upload_url() {
    return admin_url('admin.php?page=fgallery_upload');
}

// Create Gallery URL
function fgallery_createalbum_url() {
    return admin_url('admin.php?page=fgallery_add');
}

// Add images to folder URL
function fgallery_get_addimages_page_url($id, $folder_id = 0) {
    return admin_url('admin-ajax.php?action=fgallery_addimages_page&id='.$id.'&fid='.$folder_id);
}

// Add galleries to folder URL
function fgallery_get_addgalleries_page_url($id) {
    return admin_url('admin-ajax.php?action=fgallery_addgalleries_page&id='.$id);
}

// Add images to folder URL
function fgallery_get_addimages_url($id, $folder_id = 0) {
    return admin_url('admin-ajax.php?action=fgallery_addimages&id='.$id.'&fid='.$folder_id);
}

// Add images to folder URL
function fgallery_get_addgalleries_url($id) {
    return admin_url('admin-ajax.php?action=fgallery_addgalleries&id='.$id);
}

// List Images URL for given Gallery ID
function fgallery_get_album_images_url($id) {
    return admin_url('admin.php?page=fgallery&amp;action=images&amp;id='.$id);
}

// List Images URL for given Gallery ID
function fgallery_get_album_galleries_url($id) {
    return admin_url('admin.php?page=fgallery&amp;action=galleries&amp;id='.$id);
}

// Create folder or album URL
function fgallery_create_folder_url($type, $id, $upload = 0) {
    return admin_url('admin-ajax.php?action=fgallery_folder_form&type='.$type.'&gall_id='.$id.'&upload='.$upload);
}

// Save folder url
function fgallery_save_folder_url(){
    return admin_url('admin-ajax.php?action=fgallery_add_folder');
}

// Export XML settings 
function fgallery_get_export_settings_url($id) {
    return admin_url('admin-ajax.php?action=fgallery_config&amp;gall_id='.$id.'&amp;view=1');
}

// Settings XML
function fgallery_get_config_url($id, $escape = 0) {
    if ($escape) {
        return admin_url('admin-ajax.php?action=fgallery_config&gall_id='.$id);
    } else {
        return admin_url('admin-ajax.php?action=fgallery_config%26gall_id='.$id);
    } 
}

function fgallery_get_publ_url($id, $escape = 0) {
    if ($escape) {
        return admin_url('admin-ajax.php?action=fgallery_publ&gall_id='.$id);
    } else {
        return admin_url('admin-ajax.php?action=fgallery_publ%26gall_id='.$id);
    }   
}

function fgallery_js_config_url(){
   return admin_url('admin-ajax.php?action=fgallery_config');
}

// Images XML
function fgallery_get_images_url($id) {
    return admin_url('admin-ajax.php?action=fgallery_images%26gall_id='.$id);
}

// Images XML for gallery type 13
function fgallery_get_book_images_url($id) {
    return admin_url('admin-ajax.php?action=fgallery_book_images&amp;gall_id='.$id);
}

function fgallery_js_images_url() {
    return admin_url('admin-ajax.php?action=fgallery_images');
}

// Save template URL
function fgallery_get_save_settings_url($id) {
    return admin_url('admin-ajax.php?action=fgallery_save_template&gall_id='.$id);
}

// Save template page URL
function fgallery_save_template_page_url($id){
    return admin_url('admin-ajax.php?action=fgallery_save_template_page&gall_id='.$id);
}

// Load Settings URL
function fgallery_get_load_settings_url($id) {
    return admin_url('admin-ajax.php?action=fgallery_load_template&gall_id='.$id);
}

// Load template page URL
function fgallery_load_template_page_url($id){
    return admin_url('admin-ajax.php?action=fgallery_load_template_page&gall_id='.$id);
}


// Delete template URL
function fgallery_get_template_delete_url($id, $templ_id) {
    return admin_url('admin-ajax.php?action=fgallery_delete_template&gall_id='.$id.'&templ_id='.$templ_id);
}

// Gallery edit page URL
function fgallery_get_edit_url($id) {
    return admin_url('admin.php?page=fgallery&amp;action=edit&amp;id='.$id);
}

function fgallery_get_edit_url_clean($id) {
    return admin_url('admin.php?page=fgallery&action=edit&id='.$id);
}

// Image edit page URL
function fgallery_get_image_edit_url($id) {
    return admin_url('admin.php?page=fgallery_images&amp;action=edit&amp;id='.$id);
}

// Rotate image URL
function fgallery_get_rotate_image_url($id,$direction) {
    return admin_url('admin.php?page=fgallery_images&amp;action=rotate&amp;dir='.$direction.'&amp;id='.$id);
}

function fgallery_add_preview_url(){
    return admin_url('admin-ajax.php?action=fgallery_add_preview');
}

function fgallery_add_preview_form_url($id){
    return admin_url('admin-ajax.php?action=fgallery_add_preview_form&img_id='.$id);
}

// Watermark image URL
function fgallery_get_watermark_image_url($id) {
    return admin_url('admin.php?page=fgallery_images&amp;action=watermark&amp;id='.$id);
}

// Get Folder URL
function fgallery_get_folder_url($id) {
    return admin_url('admin.php?page=fgallery_images&folder='.$id);
}

// Get Uploadify url
function fgallery_upload_uploadify_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_uploadify_upload');
}

// Get Local Uploader url
function fgallery_upload_local_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_local_upload');
}

// Get Upload ZIP url
function fgallery_upload_zip_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_zip_upload');
}

// Get Upload FTP url
function fgallery_upload_ftp_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_ftp_upload');
}

// Get Upload via URL 
function fgallery_upload_url_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_url_upload');
}

// Get WordPress Media Library items import
function fgallery_upload_media_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_wpmedia_upload');
}

// Get NextGEN items import url
function fgallery_upload_nextgen_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_nextgen_upload');
}

// Get Scandir import URL
function fgallery_upload_scandir_get_url() {
    return admin_url('admin-ajax.php?action=fgallery_scandir_upload');
}

// Get massedit actions URL
function fgallery_get_massedit_url() {
    return admin_url('admin-ajax.php?action=fgallery_massedit');
}

// Insert Gallery into post page 
function fgallery_get_add_album_url(){
    return admin_url('admin-ajax.php?action=fgallery_insert_page&amp;TB_iframe=1');
}

// Insert gallery into page
function fgallery_insert_gallery_url() {
    return admin_url('admin-ajax.php?action=fgallery_insert_gallery');
}

// Insert Button URL
function fgallery_get_insert_button_url(){
    return FGALLERY_PATH.'/images/icon_gallery.gif';
}

// Preferences page URL
function fgallery_get_pref_url() {
    return admin_url('admin.php?page=fgallery&action=pref');
}

// View single gallery page url
function fgallery_view_gallery_url($id, $width, $height, $bgcolor) {
    $height = $height+30;
    return admin_url('admin-ajax.php?action=fgallery_view_gallery&amp;gall_id='.$id.'&amp;width='.$width.'&amp;height='.$height.'&amp;bg='.$bgcolor.'&amp;TB_iframe=1');
}
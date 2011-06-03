<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php');
require_once('fgallery.php');

global $wpdb;

if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
    $gall_id = $_GET['gall_id'];
} else {
    die();
}

if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_settings_field'], 'fgallery_settings')) 
{
	if ($gall_id = fgallery_edit_album($_POST['gallery'], $gall_id)) {
		$to_store = fgallery_prepare_settings($_POST);
		if (fgallery_save_album_settings($gall_id, $to_store)) {
			wp_redirect(fgallery_get_edit_url_clean($gall_id));
		}
	}
}
?>
<?php

/**
 * 
 * Provides templates to render plugin pages
 * 
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Renders Upload Images Page
 */
function fgallery_upload_page() {
    $nextgen = false;
    if (plugin_is_active('nextgen-gallery/nggallery.php')) {
            $nextgen = true;
    }
    $folders = fgallery_get_folders();
?>
<div class="wrap fgallery">
    <h2><?php _e('Upload images from:', 'fgallery');?></h2>
    <div id="upload_tabs">
        <ul>
            <li><a href="#first_tab"><?php _e('Browser Uploader', 'fgallery')?></a></li>
            <li><a href="#second_tab"><?php _e('Flash Uploader', 'fgallery')?></a></li>
            <li><a href="#ftp_tab"><?php _e('FTP Folder', 'fgallery')?></a></li>
            <li><a href="#zip_tab"><?php _e('ZIP Archive', 'fgallery')?></a></li>
            <li><a href="#flickr_tab"><?php _e('URL', 'fgallery')?></a></li>
            <li><a href="#wp_gall_tab"><?php _e('Wordpress Gallery', 'fgallery')?></a></li>
            <li><a href="#scandir_tab"><?php _e('Scan Server directory', 'fgallery')?></a></li>
            <?php if ($nextgen):?>
                    <li><a href="#nextgen_tab"><?php _e('NextGEN', 'fgallery')?></a></li>
            <?php endif;?>
        </ul>
        <div id="first_tab">
                <?php echo fgallery_render_folder_list($folders, 'local_img_folder') ?>
                <label for="local_resize"><?php _e('Resize images','fgallery') ?></label>
                <input id="local_resize" type="checkbox" value="1" checked="checked" />
                <br clear="all" />
            	<form action="<?php echo fgallery_upload_local_get_url();?>" method="post" enctype="multipart/form-data">

		<input type="file" name="userfile" class="fileUpload" multiple />
		<input type="hidden" class="local_img_folder_field" name="local_img_folder" value="0" />
		<input type="hidden" class="local_resize_field" name="resize" value="1" />
		<button id="px-submit" type="submit"><?php _e('Upload','fgallery')?></button>
		<button id="px-clear" type="reset"><?php _e('Cancel', 'fgallery');?></button>
                <br />
	</form>
        </div>
        <div id="second_tab">
            <div class="form_left">
                 <?php echo fgallery_render_folder_list($folders, 'uploadify_img_folder') ?>
                <div id="fileQueue"></div>
                <?php _e('Resize images ','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" /><br />
                <span><input type="file" id="uploadify" name="uploadify" />
                <button style="padding-right:20px; cursor:pointer" class="ui-state-default ui-corner-all"><span style="vertical-align: sub" class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-n"></span><?php _e('Upload', 'fgallery');?></button>
                <a style="padding:3px 20px 3px 10px; text-decoration:none; margin-left: 12px;" class="ui-state-default ui-corner-all" href="javascript:void(0);"><span style="vertical-align: sub" class="ui-button-icon-primary ui-icon ui-icon-circle-close"></span><?php _e('Cancel', 'fgallery');?></a> </span>
            </div>
            <div class="form_left" id="uploaded_images">
            </div>
            <br clear="all" />
        </div>
        <div id="ftp_tab">
            <form method="post" action="<?php echo fgallery_upload_ftp_get_url();?>" class="upload">
                <?php echo fgallery_render_folder_list($folders, 'fgallery_ftp_folder') ?>
                <label for="ftp_name"><?php _e('FTP server name','fgallery')?></label><br />
                <input type="text" name="ftp_name" id="ftp_name" /><br />
                <label for="ftp_folder"><?php _e('FTP Folder name','fgallery')?></label><br />
                <input type="text" name="ftp_folder" id="ftp_folder" /><br />
                <label for="fgallery_ftp_subfolders"><?php _e('Include subfolders','fgallery')?></label>
                <input type="checkbox" name="fgallery_ftp_subfolders" id="fgallery_ftp_subfolders" value="1" /> <br />
                <label for="ftp_username"><?php _e('FTP username');?></label><br />
                <input type="text" name="ftp_username" id="ftp_username" value="anonymous" /> <br />
                <label for="ftp_pass"><?php _e('FTP password');?></label><br />
                <input type="password" name="ftp_pass" id="ftp_pass" />
                <?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?><br />
                <input type="submit" value="<?php _e('Upload', 'fgallery');?>" />
                <?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
            </form>
        </div>
        <div id="zip_tab">
            <form method="post" action="<?php echo fgallery_upload_zip_get_url();?>" enctype="multipart/form-data" class="upload">
                <?php echo fgallery_render_folder_list($folders, 'zip_folder') ?>
                <input type="file" name="fgallery_zip" id="fgallery_zip" />
                <input type="submit" value="<?php _e('Upload', 'fgallery');?>" />
                <?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
             </form>
        </div>
        <div id="flickr_tab">
            <?php _e('Choose images to add','fgallery')?> <br />
            <p class="details"><?php _e('Images will be downloaded from remote server','fgallery')?></p>
            <form method="post" action="<?php echo fgallery_upload_url_get_url();?>" id="url">
                <?php echo fgallery_render_folder_list($folders, 'fgallery_url_folder') ?>
                <?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
                <div class="input_fields">
                <label for="fgallery_ftp_0"><?php _e('Type the URL of the image','fgallery')?></label><br />
                <div id="fgallery_url_wrap_0" class="fgallery_url_wrap"><input type="text" name="fgallery_url[]" id="fgallery_url_0" class="fgallery_url" onChange="show_img_from_url(this.value,0)" /><img src="" id="fgallery_img_0" width="100"/><a class="delete_url" onClick="delete_url(0)" title="<?php _e('Delete url','fgallery')?>"></a>
                </div>
                </div>
                <a class="add_url" title="<?php _e('Add url','fgallery')?>"></a> <br />
                <input type="submit" value="<?php _e('Save', 'fgallery');?>" />
                <?php _e('Resize images','fgallery') ?><input id="resize" name="resize" type="checkbox" value="1" checked="checked" />
            </form>
        </div>
        <div id="wp_gall_tab">
            <?php _e('Choose images from WordPress Gallery to add','fgallery')?>
            <?php 
                    $args = array('post_type'=>'attachment','post_mime_type'=>'image','numberposts'=>999999999);
                    $images = get_posts($args);
                    ?>
            <p class="details"><?php _e('Images won\'t be copied only the info','fgallery')?></p>
            <form method="post" action="<?php echo fgallery_upload_media_get_url();?>" class="upload">
                <?php echo fgallery_render_folder_list($folders, 'fgallery_url_folder') ?>
                <?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
                <div class="media_files">
                <?php fgallery_render_media_images_table($images) ?>
                </div>
                <input type="submit" value="<?php _e('Save', 'fgallery');?>" />
            </form>
        </div>
        <div id="scandir_tab">
            <?php _e('Enter directory path on your server to import images');?>
            <p class="details"><?php _e('Images won\'t be copied only the info','fgallery')?></p>
            <form method="post" action="<?php echo fgallery_upload_scandir_get_url();?>" class="upload">
                <?php echo fgallery_render_folder_list($folders, 'fgallery_url_folder') ?>
                <?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
                <input type="text" value="" name="directory" />
                <input type="submit" value="<?php _e('Save', 'fgallery');?>" />
            </form>
        </div>
        <?php if ($nextgen):?>
        <div id="nextgen_tab">
            <?php _e('Choose images from NextGEN Galleries to add','fgallery')?>
            <?php $images = fgallery_get_nextgen_images();?>
            <p class="details"><?php _e('Images won\'t be copied only the info','fgallery')?></p>
            <form method="post" action="<?php echo fgallery_upload_nextgen_get_url();?>" class="upload">
                <?php echo fgallery_render_folder_list($folders, 'fgallery_url_folder') ?>
                <?php wp_nonce_field('fgallery_upload_files','fgallery_upload_files_field');?>
                <div class="media_files">
                <?php fgallery_render_nextgen_images_table($images) ?>
                </div>
                <input type="submit" value="<?php _e('Save', 'fgallery');?>" />
            </form>
        </div>
        <?php endif;?>
            
        <div class="ajax_loader"></div>
    </div>
    
    <div class="fgallery_box"><?php                                
       if (!is_dir(FGALLERY_DIR)) {
           echo '<p class="fgallery_error">'.__("Folder", 'fagllery').' wp-content/uploads/fgallery '.__("doesn't exist", 'fagllery').'</p>';
       } elseif (!is_writeable(FGALLERY_DIR)) {
           echo '<p class="fgallery_error">'.__("Folder", 'fagllery').' wp-content/uploads/fgallery '.__("is not writable", 'fagllery').'</p>';
       }
       _e('Note: Important! If single image is more then 1Mb galleries may work slower', 'fgallery');?> 
    </div>
</div>
<?php	
}


// Renders preferences page
function fgallery_preferences_page() {
    global $wp_version;
?>
<div class="wrap fgallery">
    <h2><?php _e('Preferences page','fgallery')?></h2>
    <div id="fgallery_faq" class="fgallery_box">
        <a href="http://1plugin.com/faq"><?php _e('FAQ','fgallery')?></a>
    </div>
    <div id="fgallery_settings" class="fgallery_box">
        <h2><?php _e('Server Settings', 'fgallery')?></h2>
        <?php echo '<p><span class="lbl">'.__('Server Name:', 'fgallery').'</span>'.$_SERVER['SERVER_NAME']."</p>";
               echo '<p><span class="lbl">'.__('Document Root:', 'fgallery').'</span>'.$_SERVER['DOCUMENT_ROOT']."</p>";
               echo '<p><span class="lbl">'.__('Web server:', 'fgallery').'</span>'.$_SERVER['SERVER_SOFTWARE']."</p>";
               echo '<p><span class="lbl">'.__('Client Agent:', 'fgallery').'</span>'.$_SERVER['HTTP_USER_AGENT']."</p>";
               echo '<p><span class="lbl">'.__('Word Press version:', 'fagallery').'</span>'.$wp_version;
               echo '<p><span class="lbl">'.__('Plugin version:', 'fgallery').'</span>'.FGALLERY_VERSION;
               echo '<p><span class="lbl">'.__('Max size of uploaded file:', 'fgallery').'</span>'.ini_get('upload_max_filesize').'</p>';
               echo '<p><span class="lbl">'.__('PHP memory limit:', 'fgallery').'</span>'.ini_get('memory_limit').'</p>';
               if (extension_loaded('gd') && function_exists('gd_info')) {
                    $gd = __('Installed', 'fgallery');
                } else {
                    $gd = '<span class="fgallery_error">'.__('Not Installed','fgallery').'</span>';
                }
               echo '<p><span class="lbl">'.__('GD Library:', 'fgallery').'</span>'.$gd.'</p>';
               if (!is_dir(FGALLERY_DIR)) {
                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery '.__("doesn't exist", 'fgallery').'</p>';
               } elseif (!is_writeable(FGALLERY_DIR)) {
                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery '.__("is not writable", 'fgallery').'</p>';
               }
               if (!is_dir(FGALLERY_DIR.'/tmp')) {
                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery/tmp '.__("doesn't exist", 'fgallery').'</p>';
               } elseif (!is_writeable(FGALLERY_DIR.'/tmp')) {
                   echo '<p class="fgallery_error">'.__("Folder", 'fgallery').' wp-content/uploads/fgallery/tmp '.__("is not writable", 'fgallery').'</p>';
               }
               if (FGALLERY_PHP4_MODE) {
                   echo '<p class="fgallery_error">'.__("Your server runs PHP 4", 'fgallery').'</p>';
               }
         ?>
    </div>
    <?php echo '<button id="copypref" rel="'.FGALLERY_PATH.'/swf/ZeroClipboard.swf">'.__('Copy settings to clipboard', 'fagllery').'</button>';?>
    <div id="fgallery_updates" class="fgallery_box">
        <h2><?php _e('Updates', 'fgallery')?></h2>
        <h2><?php _e('Already Installed Updates', 'fgallery')?></h2>
        <?php $updated = false;
              if (get_option('1_flash_gallery_ables') != '') {
                    echo '1 Flash Gallery Update Ables <br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_acosta') != '') {
                    echo '1 Flash Gallery Update Acosta <br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_airion') != '') {
                    echo '1 Flash Gallery Update Airion <br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_arai') != '') {
                    echo '1 Flash Gallery Update Arai <br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_pax') != '') {
                    echo '1 Flash Gallery Update Pax <br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_pazin') != '') {
                    echo '1 Flash Gallery Update Pazin <br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_postma') != '') {
                    echo '1 Flash Gallery Update Postma <br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_pageflip') != '') {
                    echo '1 Flash Gallery Update PageFlip<br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_nilus') != '') {
                    echo '1 Flash Gallery Update Nilus<br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_nusl') != '') {
                    echo '1 Flash Gallery Update Nusl<br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_kranjk') != '') {
                    echo '1 Flash Gallery Update Kranjk<br />';
                    $updated = true;
              }
              if (get_option('1_flash_gallery_perona') != '') {
                    echo '1 Flash Gallery Update Perona <br />';
                    $updated = true;
              }
              if (!$updated) {
                    _e('There are no updates yet. You can get them at <a href="http://1plugin.com/order">1 Flash Gallery Wordpress Plugin Site</a>', 'fgallery');
              }
        ?>
    </div>
</div>
<?php 
}

// Renders settings page
function fgallery_settings_page() {
    if (isset($_POST) && wp_verify_nonce($_POST['fgallery_watermark_field'], 'fgallery_watermark')){
            update_option('1_flash_gallery_watermark_enabled',$_POST['watermark_enabled']);
            update_option('1_flash_gallery_preview_opt',$_POST['preview_opt']);
            update_option('1_flash_gallery_preview_size',$_POST['preview_size']);
            update_option('1_flash_gallery_display_view_size',$_POST['display_view_size']);
            update_option('1_flash_gallery_save_original',$_POST['save_or_opt']);
            update_option('1_flash_gallery_caption_type',$_POST['caption_type']);
            update_option('1_flash_gallery_access_level',$_POST['access_level']);
            if($_FILES['watermark_file']['size']){
                    $ext = pathinfo($_FILES['watermark_file']['name']);
                    $file_name = FGALLERY_DIR . '/watermark_'.date("His").'.'.$ext['extension'];
                    move_uploaded_file($_FILES['watermark_file']['tmp_name'], $file_name);
                    $fileinfo = getimagesize($file_name);
                            $img_type = $fileinfo['mime'];
                            if (strpos($img_type,'image') === false) {
                                    unlink($file_name);
                                    die(sprintf(__('%s is not an image'),$file_name).'<br />');
                            }
                    update_option('1_flash_gallery_watermark_path',$file_name);
            }
            update_option('1_flash_gallery_watermark_place',$_POST['wm_placement']);
    }
	
    $wm_enabled = get_option('1_flash_gallery_watermark_enabled');
    $wm_path = get_option('1_flash_gallery_watermark_path');
    $wm_place = get_option('1_flash_gallery_watermark_place');
?>
<div class="wrap fgallery">
    <form id="fgallery_watermark_form" method="post" enctype="multipart/form-data">
        <fieldset>
            <h2><?php _e('1 Flash Gallery Watermark','fgallery')?></h2>
            <div class="fgallery_box"><?php _e('Note: Watermark is placed only on images that were uploaded from "Local Computer", 
                "ZIP" and "FTP" tabs. Other images will not have watermark due to copyright reasons. Images that were uploaded with watermark
                will stay with it untill are deleted. Watermark could not be removed without file reuploading.', 'fgallery')?>
            </div>	
            <label><?php _e('Watermark Position', 'fagllery')?></label>
            <table cellspacing="1" cellpadding="2" border="1" class="wm_placement">
              <tbody>
                <tr>
                    <td><input type="radio" value="TL" <?php if ($wm_place == 'TL') echo 'checked=""';?> name="wm_placement"></td>
                    <td><input type="radio" value="TM" <?php if ($wm_place == 'TM') echo 'checked=""';?> name="wm_placement"></td>
                    <td><input type="radio" value="TR" <?php if ($wm_place == 'TR') echo 'checked=""';?> name="wm_placement"></td>
                </tr>
                <tr>
                    <td><input type="radio" value="CL" <?php if ($wm_place == 'CL') echo 'checked=""';?> name="wm_placement"></td>
                    <td><input type="radio" value="C"  <?php if ($wm_place == 'C') echo 'checked=""';?> name="wm_placement"></td>
                    <td><input type="radio" value="CR" <?php if ($wm_place == 'CR') echo 'checked=""';?> name="wm_placement"></td>
                </tr>
                <tr>
                    <td><input type="radio" value="BL" <?php if ($wm_place == 'BL') echo 'checked=""';?> name="wm_placement"></td>
                    <td><input type="radio" value="BM" <?php if ($wm_place == 'BM') echo 'checked=""';?> name="wm_placement"></td>
                    <td><input type="radio" value="BR" <?php if ($wm_place == 'BR') echo 'checked=""';?> name="wm_placement"></td>
                </tr>
              </tbody>
            </table>
            <label for="watermark_enabled"><?php _e('Enable Watermark','fgallery') ?></label>
            <input type="checkbox" name="watermark_enabled" value="1" <?php if ($wm_enabled == '1') echo 'checked=""';?> id="watermark_enabled" />
            <br />
            <label for="watermark_file"><?php _e('Upload New','fgallery') ?></label>
            <input type="file" name="watermark_file" id="watermark_file" /> <br />
            <?php if ($wm_path != '') {
                    $wm_path = str_replace('\\','/', $wm_path);
                    $src = str_replace($_SERVER['DOCUMENT_ROOT'],'',$wm_path);			
                    echo '<p>Current Watermark</p><img src="'.$src.'" alt="watermark" />';
            }?>
            </fieldset>
            <fieldset>
                <h2><?php _e('Thumbnails optimization','fgallery')?></h2>
                <p>
                    <label for="preview_opt"><?php _e('Preview Optimization', 'fgallery')?></label>
                    <input type="checkbox" name="preview_opt" id="preview_opt" value="1" 
                        <?php if (get_option('1_flash_gallery_preview_opt',0) == 1) echo 'checked="checked"'?> /> 
                    <input type="text" name="preview_size" id="preview_size" value="<?php echo get_option('1_flash_gallery_preview_size','200')?>" />px <br />
                    <?php _e('This option allows to fasten the loading of previews and thumbnails in slideshow','fgallery')?>
                </p>
                
                <p>
                    <label for="display_view_size"><?php _e('Display View Image Size', 'fgallery')?></label>
                    <input type="text" name="display_view_size" id="display_view_size" 
                           value="<?php echo get_option('1_flash_gallery_display_view_size','1200')?>" />px <br />
                    <?php _e('This option allows to resize image for big images view in the slideshow','fgallery')?>
                </p>
                <p>
                    <label for="save_or_opt"><?php _e('Save Original Image', 'fgallery')?></label>
                    <input type="checkbox" name="save_or_opt" id="save_or_opt" value="1" 
                        <?php if (get_option('1_flash_gallery_save_original',0) == 1) echo 'checked="checked"'?> /> <br />
                    <?php _e('This option allows to save the original file and use it at Full Screen View','fgallery')?>
                </p>
                
            </fieldset>
            <fieldset>
                <h2><?php _e('General Settings','fgallery')?></h2>
                <label for="caption_type"><?php _e('Import caption from:', 'fgallery')?></label>
                <?php $capt = get_option('1_flash_gallery_caption_type', 0);?>
                <select name="caption_type" id="caption_type">
                    <option value="0" <?php if ($capt == 0) echo 'selected="selected"'?>>
                        <?php _e('Filename without extension', 'fgallery')?>
                    </option>
                    <option value="1" <?php if ($capt == 1) echo 'selected="selected"'?>>
                        <?php _e('Headline from meta data', 'fgallery')?>
                    </option>
                    <option value="2" <?php if ($capt == 2) echo 'selected="selected"'?>>
                        <?php _e('Date', 'fgallery')?>
                    </option>
                </select> <br />
                <?php $access = get_option('1_flash_gallery_access_level','activate_plugins') ?>                                
                <label for="access_level"><?php _e('Access level:', 'fgallery')?></label>
                <select name="access_level" id="access_level">
                    <option value="upload_files" <?php if ($access == 'upload_files') echo 'selected="selected"'?>>
                        <?php _e('Author')?>
                    </option>
                    <option value="moderate_comments" <?php if ($access == 'moderate_comments') echo 'selected="selected"'?>>
                        <?php _e('Editor')?>
                    </option>
                    <option value="activate_plugins" <?php if ($access == 'activate_plugins') echo 'selected="selected"'?>>
                        <?php _e('Administrator')?>
                    </option>
                    <option value="manage_network" <?php if ($access == 'manage_network') echo 'selected="selected"'?>>
                        <?php _e('Super Admin')?>
                    </option>
                </select>
                <p class="fgallery_error"><?php _e('Super Admin access level is only for WordPress Multi Site installation. 
                             If you do not use multi site set Administrator as highest access level. If you set Super Admin at common installation
                             you will not have access to 1 Flash Gallery plugin at all', 'fgallery')?>
            </fieldset>
        <input type="submit" value="<?php _e('Save')?>" /> <br />
        <?php wp_nonce_field('fgallery_watermark','fgallery_watermark_field');?>
    </form>
</div>
<?php
}

// Renders Galleries List Page
function fgallery_admin_albums() {
    global $wpdb;
    if (isset($_GET['action'])) {
       $action = $_GET['action'];
    } else {
       $action = '';
    }
    switch ($action) {
       case 'pref':
            fgallery_preferences_page();
       break;
       case 'edit':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = $_GET['id'];
            } else {
                die();
            }
            if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_settings_field'], 'fgallery_settings')) 
            {
                $post = $_POST;
                $id = fgallery_edit_album($_POST['gallery'], $id);
                if ($id) {
                    $post = fgallery_store_slideshow_files($post, $_FILES);
                    $to_store = fgallery_prepare_settings($post);
                    fgallery_save_album_settings($id, $to_store);
                }
            }
            fgallery_edit_album_page($id);
       break;
       case 'images':
            if (!empty($_POST)) {
                if (isset($_POST['album_id']) && is_numeric($_POST['album_id'])
                        && isset($_POST['gall_id']) && is_numeric($_POST['gall_id'])){
                            if (!empty($_POST['images'])) {
                                    $ids = $_POST['images'];
                                    $ids_string = implode(",",$ids);
                                    switch ($_POST['album_id']) {
                                        case '-1':
                                                $wpdb->query("DELETE FROM ". IMAGES_TO_ALBUMS_TABLE. " 
                                                              WHERE img_id IN (".$ids_string.") AND gall_id = ".$_POST['gall_id']);
                                        break;
                                        default :
                                                $wpdb->query("UPDATE ".IMAGES_TO_ALBUMS_TABLE." SET `gall_folder` =".$_POST['album_id']."
                                                              WHERE `img_id` IN (".$ids_string.") AND `gall_id` = ".$_POST['gall_id']);
                                        break;
                                    }
                            } 
                    }
            }
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = $_GET['id'];
                fgallery_edit_album_images_page($id);
            }
       break;	
       case 'galleries':
           if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = $_GET['id'];
                fgallery_edit_album_galleries_page($id);
           }
           break;
       default:
        // -- pagination ------------
        if (!empty($_POST) && wp_verify_nonce($_POST['screenoptionnonce'], 'screen-options-nonce')) {
            update_option( $_POST['wp_screen_options']['option'],  $_POST['wp_screen_options']['value']);
        }
        $pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
        $sort_by = isset($_GET['sort']) ? absint($_GET['sort']) : 4;
        if ( empty($pagenum) )
            $pagenum = 1;
        if ( empty( $per_page ) || $per_page < 1 )
            $per_page = get_option('toplevel_page_fgallery_per_page',25);
            apply_filters('toplevel_page_fgallery_per_page', $per_page);
            $num_pages = ceil(fgallery_albums_count() / $per_page);
            $page_links = paginate_links( array(
                'base' => add_query_arg( 'paged', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $num_pages,
                'current' => $pagenum
             ));
        // ---------------------------	
        $items = fgallery_get_albums($pagenum,$per_page, $sort_by); ?>
        <div class="wrap fgallery">
            <h2><?php _e('Gallery List', 'fgallery') ?></h2>
            <div class="tablenav">
                <form action="" method="get" class="form_left">
                <span><?php _e('Sort by:', 'fgallery')?></span>
                    <select name="sort" id="images_sortby">
                        <option value="0" <?php if (@$_GET['sort'] == 0) echo 'selected="selected"'?>><?php _e('Title ASC', 'fgallery');?> </option>
                        <option value="1" <?php if (@$_GET['sort'] == 1) echo 'selected="selected"'?>><?php _e('Title DESC', 'fgallery');?> </option>
                        <option value="2" <?php if (@$_GET['sort'] == 2) echo 'selected="selected"'?>><?php _e('Date ASC', 'fgallery');?> </option>
                        <option value="3" <?php if (@$_GET['sort'] == 3) echo 'selected="selected"'?>><?php _e('Date DESC', 'fgallery');?> </option>
                    </select>
                    <input type="hidden" name="page" value="fgallery" />
                    <input type="hidden" name="paged" value="<?php echo $pagenum; ?>" />
                </form>
                <?php if ( $page_links ) { ?>
                        <div class="tablenav-pages">
                        <?php $count_posts = fgallery_albums_count();
                              $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                    number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                    number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
                                                    number_format_i18n( $count_posts ),
                                                    $page_links
                                                    );
                        echo $page_links_text; ?>
                        </div>
                <?php } ?>
                <a href="<?php echo fgallery_createalbum_url()?>" title="<?php _e('Create gallery', 'fgallery')?>" 
                   id="add_gallery" class="fgallery_action"><?php _e('Create gallery', 'fgallery') ?></a>
                 <a href="<?php echo fgallery_get_pref_url()?>" title="<?php _e('Preferences', 'fgallery')?>" 
                    class="fgallery_action"><?php _e('Preferences', 'fgallery') ?></a>
            </div>
            <?php 
                 echo fgallery_render_albums_table($items);
            ?>
            <div class="tablenav">
            <?php
            if ( $page_links ) { 
                    echo "<div class='tablenav-pages'>$page_links_text</div>";
            } ?>
            </div>
       </div> 
    <?php 
       break;
    }

    return true;
}

// Renders Images List page
function fgallery_images_page() {
    global $wpdb;
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    } else {
        $action = '';
    }
    switch ($action) {
        case 'edit':
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $id = $_GET['id'];
                        fgallery_edit_image_page($id);
                }
        break;
        case 'rotate':
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $id = $_GET['id'];
                        if (isset($_GET['dir']) && is_numeric($_GET['dir'])) {
                            $dir = $_GET['dir'];
                            if ($dir) {
                                fgallery_rotate_by_id($id, 'CW');
                            } else {
                                fgallery_rotate_by_id($id, 'CC');
                            }
                        }
                        fgallery_edit_image_page($id);
                }
        break;
        case 'watermark':
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $id = $_GET['id'];
                        fgallery_watermark_by_id($id);
            }
            fgallery_edit_image_page($id);
        break;
        default:
        // -- pagination ------------
            if (!empty($_POST) && wp_verify_nonce($_POST['screenoptionnonce'], 'screen-options-nonce')) {
                update_option( $_POST['wp_screen_options']['option'],  $_POST['wp_screen_options']['value']);
            }
            $pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
            $sort_by = isset($_GET['sort']) ? absint($_GET['sort']) : 3;
            $folder = isset($_GET['folder']) ? absint($_GET['folder']) : 0;
            if ($folder) {
                $folder_name = fgallery_get_folder_name($folder);
            }
            if ( empty($pagenum) )
                $pagenum = 1;
            if ( empty( $per_page ) || $per_page < 1 )
                $per_page = get_option('1_flash_gallery_page_fgallery_images_per_page',25);
            apply_filters('1_flash_gallery_page_fgallery_images_per_page', $per_page);
            $num_pages = ceil(fgallery_images_count($folder) / $per_page);
            $page_links = paginate_links( array(
                    'base' => add_query_arg( 'paged', '%#%' ),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => $num_pages,
                    'current' => $pagenum
            ));
            // ---------------------------	
            $items = fgallery_get_images($pagenum,$per_page, $folder, $sort_by); ?>
            <div class="wrap fgallery">
                <?php if (!$folder){?>
                    <h2><?php _e('Images List', 'fgallery'); ?></h2>
                <?php }else{ ?>
                    <h2><a href="<?php echo fgallery_images_url();?>"><?php _e('Images', 'fgallery');?> </a> 
                        <?php echo '/ '.__('Folder','fgallery').' "'.$folder_name.'"'?> 
                    </h2>
                <?php }?>
                <div class="tablenav">
                    <form action="" method="get" class="form_left">
                        <label for="images_sortby"><?php _e('Sort by:','fgallery')?></label>
                        <select name="sort" id="images_sortby">
                            <option value="0" <?php if (@$_GET['sort'] == 0) echo 'selected="selected"'?>><?php _e('Title ASC','fgallery');?> </option>
                            <option value="1" <?php if (@$_GET['sort'] == 1) echo 'selected="selected"'?>><?php _e('Title DESC','fgallery');?> </option>
                            <option value="2" <?php if (@$_GET['sort'] == 2) echo 'selected="selected"'?>><?php _e('Date ASC','fgallery');?> </option>
                            <option value="3" <?php if (@$_GET['sort'] == 3) echo 'selected="selected"'?>><?php _e('Date DESC','fgallery');?> </option>
                            <option value="4" <?php if (@$_GET['sort'] == 4) echo 'selected="selected"'?>><?php _e('Size ASC','fgallery');?> </option>
                            <option value="5" <?php if (@$_GET['sort'] == 5) echo 'selected="selected"'?>><?php _e('Size DESC','fgallery');?> </option>
                        </select>
                        <input type="hidden" name="page" value="fgallery_images" />
                        <input type="hidden" name="paged" value="<?php echo $pagenum; ?>" />
                        <div class="clear"></div>
                    </form>

                    <?php if ( $page_links ) { ?>
                            <div class="tablenav-pages">
                            <?php $count_posts = fgallery_images_count($folder);
                                  $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                            number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                            number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
                                                            number_format_i18n( $count_posts ),
                                                            $page_links
                                                            );
                             echo $page_links_text; ?>
                            </div>
                    <?php } ?>

                    <?php if(!$folder):?>
                        <a href="<?php echo fgallery_create_folder_url(1,0) ?>" class="fgallery_action thickbox" >
                            <?php _e('Create New Folder','fgallery')?>
                        </a> 
                    <?php endif;?>
                    <a href="<?php echo fgallery_get_upload_url() ?>" class="fgallery_action" ><?php _e('Upload Images','fgallery')?></a>
                </div>
                <form action="<?php echo fgallery_get_massedit_url();?>" method="post" id="massedit_form">
                        <?php wp_nonce_field('fgallery_massedit','fgallery_massedit_field'); ?>
                        <?php if (fgallery_access_level() >=5) :?>
                        <select name="image_action" id="image_action">
                            <optgroup label="<?php _e('Actions', 'fgallery')?>">
                                <option value="-" selected="selected"><?php _e('Choose action', 'fgallery')?></option>
                                <option value="-2"><?php _e('Delete selected', 'fgallery')?></option>
                                <option value="-1"><?php _e('Make new gallery from selected', 'fgallery')?></option>
                                <?php $wm = get_option('1_flash_gallery_watermark_enabled', 0); 
                                        if ($wm): ?>
                                <option value="-3"><?php _e('Watermark selected', 'fgallery')?></option>
                                <?php endif;?>
                            </optgroup>
                                <?php $folders = fgallery_get_folders();
                                        if (!empty($folders)):?>
                            <optgroup label="<?php _e('Move selected to', 'fgallery')?>">
                                    <option value="0"><?php _e('Root folder', 'fgallery')?></option>
                              <?php foreach ($folders as $item): ?>
                                                    <option value="<?php echo $item['img_id']?>"><?php echo $item['img_caption']?></option>
                              <?php endforeach;?>
                            </optgroup>  
                        <?php endif;?>
                        <?php $albums = fgallery_get_albums(1, 9999999, 4);
                              if (!empty($albums)):?>
                            <optgroup label="<?php _e('Add selected to', 'fgallery')?>">
                              <?php foreach ($albums as $item): ?>
                                            <option value="gall_<?php echo $item['gall_id']?>"><?php echo $item['gall_name']?></option>
                              <?php endforeach;?>
                            </optgroup>  
                        <?php endif;?>	
                        </select>
                        <input type="submit" value="<?php _e('Go', 'fgallery')?>" />
                        <?php endif;?>
                <?php echo fgallery_render_images_table($items);?>
                </form>
                <div class="tablenav">
                    <?php
                    if ( $page_links ) { 
                            echo "<div class='tablenav-pages'>$page_links_text</div>";
                    } ?>
                </div>
            </div> 
    <?php 
        break;
    }

    return true;
}


function fgallery_edit_album_galleries_page($id) {
    $galleries = fgallery_get_gallery_items($id);
    $album = fgallery_get_album($id);
    echo '<div class="wrap fgallery"><div id="fgallery_images">';
    echo '<h2>'.__('Galleries in', 'fgallery').'<a href="'.fgallery_get_edit_url($id).'"> '.$album['gall_name'].'</a>
        '.__('gallery','fgallery').'</h2>
		<div style="float: none; margin:3px 0 15px 0;" class="tablenav">';	
		
    $title = __('Add galleries', 'fgallery');
    echo '<a href="'.fgallery_get_addgalleries_page_url($id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a>';
    echo '</div>';
    if (!empty($galleries)){
            echo fgallery_render_album_galleries($galleries, $id);	
    } else {
            _e('There are no galleries in this gallery', 'fgallery');
    }
    echo '</div></div>';
}
/**
 *
 * Renders List Images page for a give Gallery
 * @param integer $id Gallery ID
 */
function fgallery_edit_album_images_page($id){
    $folder = isset($_GET['folder']) ? $_GET['folder'] : 0;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 10;
    $images = fgallery_get_album_images($id, $folder, $sort);
    $album = fgallery_get_album($id);
    echo '<div class="wrap fgallery"><div id="fgallery_images">';
    echo '<h2>'.__('Images in', 'fgallery').'<a href="'.fgallery_get_edit_url($id).'"> '.$album['gall_name'].'</a>
        '.__('gallery','fgallery').'</h2>
		<div class="tablenav">';	
?>
        <form action="" method="get" class="form_left">
                        <label for="images_sortby"><?php _e('Sort by:','fgallery')?></label>
                        <select name="sort" id="images_sortby" onChange="jQuery(this).parent().submit();">
                            <option value="0" <?php if ($sort == 0) echo 'selected="selected"'?>><?php _e('Title ASC','fgallery');?> </option>
                            <option value="1" <?php if ($sort == 1) echo 'selected="selected"'?>><?php _e('Title DESC','fgallery');?> </option>
                            <option value="2" <?php if ($sort == 2) echo 'selected="selected"'?>><?php _e('Date ASC','fgallery');?> </option>
                            <option value="3" <?php if ($sort == 3) echo 'selected="selected"'?>><?php _e('Date DESC','fgallery');?> </option>
                            <option value="4" <?php if ($sort == 4) echo 'selected="selected"'?>><?php _e('Size ASC','fgallery');?> </option>
                            <option value="5" <?php if ($sort == 5) echo 'selected="selected"'?>><?php _e('Size DESC','fgallery');?> </option>
                        </select>
                        <div class="clear"></div>
                        <input type="hidden" name="page" value="fgallery" />
                        <input type="hidden" name="action" value="images" />
                        <input type="hidden" name="id" value="<?php echo $id?>" />
                    </form>
                    
    <?php	
	
                    
	$title = __('Add images', 'fgallery');
    echo '<a href="'.fgallery_get_addimages_page_url($id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a>';
    $title = __('Create album', 'fgallery');
    echo '<a href="'.fgallery_create_folder_url(2,$id).'" title="'.$title.'" class="thickbox fgallery_action">'.$title.'</a></div>';
    if (!empty($images)){
            echo fgallery_render_album_images($images, $id);	
    } else {
            _e('There are no images in this gallery', 'fgallery');
    }
    echo '</div></div>';
 }
 
 /**
  * Renders Gallery Edit Page
  * @param integer $id Gallery ID
  */
function fgallery_edit_album_page($id){
    echo '<div class="wrap fgallery">';
        $album = fgallery_get_album($id);
        if (empty($album)) {
            $album['gall_id'] = 0;
            $album['gall_width'] = 450;
            $album['gall_height'] = 385;
            $album['gall_bgcolor'] = "ffffff";
            $album['gall_type'] = 3;
        }
        if ($id != 0) {
          echo '<h2>'.__('Edit Gallery', 'fgallery').' "'.$album['gall_name'].'"</h2>';
        } else {
          echo '<h2>'.__('Add New Gallery', 'fgallery').'</h2>';
        }
        echo '<div id="fgallery_settings_form">';
        if (isset($album['gall_published']) && $id != 0) {
            echo '<p>'.__('Embed Code', 'fgallery').'</p>';
            echo '<div id="shortcode_view">'.fgallery_do_shortcode($album['gall_id']).'</div>';
            echo '<button id="shortcode" rel="'.FGALLERY_PATH.'/swf/ZeroClipboard.swf">'.__('Copy to Clipboard', 'fgallery').'</button>';
        }
        echo '<div id="configurator_wrap">';
              echo sc_params_pane($album);
        echo '</div>';
        if ($id != 0):
            echo '<div class="edit_gallery_urls">';
                    if ($album['gall_type']!= 13) {
                        $title = __('Add Images', 'fgallery');
                        echo '<a id="add_images" href="'.fgallery_get_addimages_page_url($id).'" 
                                title="'.$title.'" class="thickbox fgallery_action">'.$title.
                              '</a>';
                        /*$title = __('Batch Adding Images', 'fgallery');
                        echo '<a href="'.fgallery_get_album_images_url($id).'" 
                                title="'.$title.'" class="fgallery_action">'.$title.
                              '</a>';*/
                        $title = __('Images List', 'fgallery');
                        echo '<a href="'.fgallery_get_album_images_url($id).'" 
                                title="'.$title.'" class="fgallery_action">'.$title.
                              '</a>';
                    } else {
                        $title = __('Add Galleries', 'fgallery');
                        echo '<a id="add_images" href="'.fgallery_get_addgalleries_page_url($id).'" 
                                title="'.$title.'" class="thickbox fgallery_action">'.$title.
                              '</a>';
                        $title = __('Galleries List', 'fgallery');
                        echo '<a href="'.fgallery_get_album_galleries_url($id).'" 
                                title="'.$title.'" class="fgallery_action">'.$title.
                              '</a>';    
                    }
                    $title = __('Save Settings', 'fgallery');
                    echo '<a href="'.fgallery_save_template_page_url($id).'" 
                            title="'.$title.'" class="thickbox fgallery_action">'.$title.
                          '</a>';
                    $title = __('Export Settings', 'fgallery');
                    echo '<a href="'.fgallery_get_export_settings_url($id).'" 
                            title="'.$title.'" class="fgallery_action">'.$title.
                          '</a>';
                    $title = __('Load Settings', 'fgallery');
                    echo '<a href="'.fgallery_load_template_page_url($id).'" 
                            title="'.$title.'" class="thickbox fgallery_action">'.$title.
                          '</a>';
            echo '</div>';
            if (isset($_POST['fgallery_just_added']) && is_numeric($_POST['fgallery_just_added'])) {
                echo '<div id="add_images_box"></div>';
                if ($album['gall_type'] == 13) {
                    $url = fgallery_get_addgalleries_page_url($id);
                } else {
                    $url = fgallery_get_addimages_page_url($id);
                }
                echo '<script type="text/javascript">
                        jQuery(document).ready(function(){
                            jQuery("#add_images_box").load("'.$url.'");
                        });
                    </script>';
            } else {
                echo do_shortcode('[fgallery id='.$id.' w='.$album['gall_width'].' h='.$album['gall_height'].' bg='.$album['gall_bgcolor'].' t=0 conf=1]');
            }
        endif;
        echo '</div>';
    echo '</div>';
    
}

/**
 * Renders Image edit page
 * @param integer $id Image ID
 */
function fgallery_edit_image_page($id){
    echo '<div class="wrap fgallery">';
    if ( !empty($_POST) && check_admin_referer('fgallery_edit','fgallery_edit_image_field') ) {
        $save = fgallery_edit_image($_POST, $id, $_FILES);
        if ($save) {
           echo '<div id="message" class="updated fade">
                    <p><strong>'.__('Image has been saved', 'fgallery').'</strong></p>
                 </div>';
        } else {
           echo '<div id="message" class="error">
                    <p><strong>'.__('Image name cannot be empty', 'fgallery').'</strong></p>
                 </div>';
        }
    }
    $image = fgallery_get_image($id);
    if ($image['img_vs_folder'] == 1) {
        echo '<h2>'.__('Edit Folder', 'fgallery').' "'.$image['img_caption'].'"</h2>';
    } elseif ($image['img_vs_folder'] == 0) {
        echo '<h2>'.__('Edit Image', 'fgallery').' "#'.$image['img_id'].' '.$image['img_caption'].'"</h2>';
    }
    echo fgallery_render_edit_image_form($image);
    if ($image['img_vs_folder'] == 0) {
        echo '<div class="edit_gallery_urls"><a class="fgallery_action" href="'.fgallery_get_rotate_image_url($id,0).'">'.__('Rotate CW').'</a>';
        echo '<a class="fgallery_action" href="'.fgallery_get_rotate_image_url($id,1).'">'.__('Rotate CC').'</a>';
        $wm = get_option('1_flash_gallery_watermark_enabled', 0); 
        if ($wm):
        echo '<a class="fgallery_action" href="'.fgallery_get_watermark_image_url($id).'">'.__('Watermark Image').'</a>';
        endif;
        if ($image['img_full_view_path'] != '') {
            $full_view = get_option('siteurl').'/'.$image['img_full_view_path'];
        } else {
            $full_view = get_option('siteurl').'/'.$image['img_path'];
        }
        echo '</div>
              <a href="'.$full_view.'" title="'.__('Full View','fgallery').'" target="_blank">
                        <img src="'.get_option('siteurl').'/'.$image['img_path'].'" alt="'.$image['img_caption'].'" class="img_display" />
              </a><br />';
        if ($image['img_preview_path'] != '') {
              _e('Preview:', 'fgallery');
              echo '<br /><img src="'.get_option('siteurl').'/'.$image['img_preview_path'].'" alt="'.$image['img_caption'].'"/>';
        }
    }
    echo '</div>';
}

// Renders Create Gallery page
function fgallery_add_gallery() {
    fgallery_edit_album_page(0);
}

/**
 * Creates add gallery dialog
 */
function fgallery_addgalleries_page() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $album = fgallery_get_album($id);
        if ($album['gall_type'] != 13) {
            die('Wrong Gallery Type');
        }
        fgallery_addgallery_box($id);
    }
    die();
}

/**
 * Creates add images dialog
 */
function fgallery_addimages_page(){
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = $_GET['id'];
            $pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
            $folder = isset( $_GET['folder'] ) ? absint( $_GET['folder'] ) : 0;
            if ( empty($pagenum) ) $pagenum = 1;
            if ( empty( $per_page ) || $per_page < 1 )
                $per_page = get_option('1_flash_gallery_page_fgallery_images_per_page',25);
                $num_pages = ceil(fgallery_images_count_rest($folder, $id) / $per_page);
                $page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => __('&laquo;'),
			'next_text' => __('&raquo;'),
			'total' => $num_pages,
			'current' => $pagenum
		));
         ?>
        <?php if ( $page_links != '') { ?>
            <div class="tablenav">
                <div class="tablenav-pages addimages">
                <?php $count_posts = fgallery_images_count_rest($folder, $id);
                          $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
                                                number_format_i18n( $count_posts ),
                                                $page_links
                                             );
                        echo $page_links_text; ?>
                </div>
            </div>
        <?php } ?>
        <?php  fgallery_addimage_box($id, $pagenum, $per_page, $folder);  ?>
        <?php if ( $page_links != '') { ?>
                <div class="tablenav">
                    <div class="tablenav-pages addimages">
                    <?php echo $page_links_text; ?>
                    </div>
                </div>
        <?php } 
     }
     die();
}

function fgallery_insert_page(){ ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Insert Gallery', 'fgallery'); ?> &#8212; <?php _e('WordPress'); ?></title>
<?php
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
wp_enqueue_style('fgallerycss');
// Check callback name for 'media'
wp_enqueue_style( 'ie' );
wp_enqueue_script('jquery-form');
?>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var userSettings = {'url':'<?php echo SITECOOKIEPATH; ?>','uid':'<?php if ( ! isset($current_user) ) $current_user = wp_get_current_user(); echo $current_user->ID; ?>','time':'<?php echo time(); ?>'};
//]]>
</script>
<?php
do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');
?>
</head>
<body<?php if ( isset($GLOBALS['body_id']) ) echo ' id="' . $GLOBALS['body_id'] . '"'; ?>>  
<?php  $pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 0;
        if ( empty($pagenum) ) $pagenum = 1;
        if ( empty( $per_page ) || $per_page < 1 )
                    $per_page = get_option('toplevel_page_fgallery_per_page',25);
        $num_pages = ceil(fgallery_albums_count() / $per_page);
            $page_links = paginate_links( array(
                    'base' => add_query_arg( 'paged', '%#%' ),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => $num_pages,
                    'current' => $pagenum
            ));
    ?>
     <div class="tablenav">
            <?php if ( $page_links ) { ?>
                    <div class="tablenav-pages">
                    <?php $count_posts = fgallery_albums_count();
                              $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
                                                number_format_i18n( $count_posts ),
                                                $page_links
                                                );
                            echo $page_links_text; ?>
                    </div>
            <?php } ?>
    </div>
        <form action="<?php echo fgallery_insert_gallery_url()?>" method="post" id="fgallery_insert">
        <?php
            $items = fgallery_get_albums($pagenum,$per_page,4);
            foreach ($items as $item) {
                if (count(fgallery_get_album_images($item['gall_id']))>0 && $item['gall_published']):
               ?>
                        <div class="image_wrap">
                            <div class="fgallery_image_actions">
                                    <input type="checkbox" value="<?php echo $item['gall_id'] ?>" name="gallery[]" />
                            </div>
                            <div class="fgallery_image">
                                    <?php echo fgallery_get_album_cover($item) ?>
                            </div>
                            <div class="fgallery_image_info">
                                    <b><?php echo $item['gall_name'] ?></b><br />
                                    <?php echo $item['gall_description'] ?>
                            </div>
                        </div>
                <?php
                endif;
            }
            wp_nonce_field('insert_gallery','insert_gallery_field');
            ?>
            <input type="radio" name="sn_type" value="0" checked> <?php _e('Flash object', 'fgallery') ?>
            <input type="radio" name="sn_type" value="1"> <?php _e('Text link to gallery', 'fgallery'); ?>
            <input type="radio" name="sn_type" value="2"> <?php _e('Cover as link to gallery', 'fgallery'); ?>
				
            <input type="submit" value="<?php _e('Insert into post', 'fgallery');?>" name="insert_submit" />
        </form>
         <div class="tablenav">
            <?php if ( $page_links ) { ?>
                    <div class="tablenav-pages">
                    <?php $count_posts = fgallery_albums_count();
                              $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                                        number_format_i18n( ( $pagenum - 1 ) * $per_page + 1 ),
                                                        number_format_i18n( min( $pagenum * $per_page, $count_posts ) ),
                                                        number_format_i18n( $count_posts ),
                                                        $page_links
                                                        );
                            echo $page_links_text; ?>
                    </div>
            <?php } ?>
          </div>
       <?php
	do_action('admin_print_footer_scripts');
       ?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
</body>
</html>
<?php
    die();
}

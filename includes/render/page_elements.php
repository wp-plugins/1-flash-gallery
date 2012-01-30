<?php

/*
 * Renders Page Elements for 1 Flash Gallery Pages
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Renders table of images from WordPress Media Library
 * @param array $images 
 */
function fgallery_render_media_images_table($images) {
    if (!empty($images)) {
	foreach ($images as $image):?>
            <div class="image_wrap">
                <div class="fgallery_image_info">
                     <input type="checkbox" name="media[]" value="<?php echo $image->ID?>" />
                </div>
                <div class="fgallery_image">
                <?php 				
                    $post_meta = get_post_meta($image->ID, '_wp_attached_file', true);
                    $uploads_dir = pathinfo($post_meta);
                    $img_meta = get_post_meta($image->ID, '_wp_attachment_metadata', true);
                    if (is_array($img_meta)) {
                        echo '<img src="'.get_option('siteurl').'/wp-content/uploads/'.$uploads_dir['dirname'].'/'.$img_meta['sizes']['thumbnail']['file'].'"
                            alt="'.$image->post_title.'" />';	
                    } else {
                        echo '<img src="'.get_option('siteurl').'/wp-content/uploads/'.$post_meta.'" alt="'.$image->post_title.'" />';	
                    }
                ?>
                </div>
                <div class="fgallery_image_info">
                     <?php echo $image->post_title?>
                </div>
            </div>
 <?php endforeach;
   }
}

/**
 * Renders table of images from NextGEN
 * @param array $images 
 */
function fgallery_render_nextgen_images_table($images) {
    if (!empty($images)) {
	foreach ($images as $image) :?>
            <div class="image_wrap">
                <div class="fgallery_image_info">
                     <input type="checkbox" name="nextgen[]" value="<?php echo $image->pid?>" />
                </div>
                <div class="fgallery_image">
                <?php 				
                     echo '<img src="'.EXTRA_DIR.$image->path.'/thumbs/thumbs_'.$image->filename.'" 
                         alt="'.$image->alttext.'" />';
                ?>
                    <input type="hidden" name="nextgenpreviewpath[<?php echo $image->pid?>]" value="<?php echo $image->path.'/thumbs/thumbs_'.$image->filename;?>"/>
                    <input type="hidden" name="nextgenpath[<?php echo $image->pid?>]" value="<?php echo $image->path.'/'.$image->filename;?>"/>
                </div>
                <div class="fgallery_image_info">
                    <b><?php echo $image->alttext?></b> <br />
                    <?php echo $image->description?> 
                    <input type="hidden" name="nextgencaption[<?php echo $image->pid?>]" value="<?php echo $image->alttext;?>"/>
                    <input type="hidden" name="nextgendescription[<?php echo $image->pid?>]" value="<?php echo $image->description;?>"/>
                </div>
            </div>
  <?php endforeach;
    }
}

/**
 * Renders the Galleries List
 * @param array $albums
 * @return string 
 */
function fgallery_render_albums_table($albums){
    $output = '<table class="widefat fixed">
		  <thead>
			<tr>
                            <th id="cb" class="manage-column check-column column-cb" scope="col" >
                                <input type="checkbox" name="check_all" class="check_all" />
                            </th>
                            <th class="gall_id" scope="col">'.__('ID').'</th>
                            <th id="title" scope="col">'.__('Name','fgallery').'</th>
                            <th class="gall_cover" scope="col">'.__('Cover','fgallery').'</th>
                            <th class="gall_description" scope="col">'.__('Description','fgallery').'</th>
                            <th class="gall_attr" scope="col">'.__('Attributes','fgallery').'</th>
                            <th class="fgallery_actions" scope="col">'.__('Actions','fgallery').'</th>
			</tr>
		  </thead>';
    $output .= '<tfoot>
			<tr>
                            <th class="manage-column check-column column-cb" scope="col" >
                                <input type="checkbox" name="check_all" class="check_all" />
                            </th>
                            <th class="gall_id" scope="col">'.__('ID').'</th>
                            <th scope="col">'.__('Name','fgallery').'</th>
                            <th class="gall_cover" scope="col">'.__('Cover','fgallery').'</th>
                            <th class="gall_description" scope="col">'.__('Description','fgallery').'</th>
                            <th class="gall_attr" scope="col">'.__('Attributes','fgallery').'</th>
                            <th class="fgallery_actions" scope="col">'.__('Actions','fgallery').'</th>
			</tr>
		  </tfoot><tbody>';
    if (!empty($albums)) {
        foreach ($albums as $album) {
           $output .= fgallery_render_album_item_row($album);
        }
    } else {
           $output .= '<tr><td colspan="7">'.__('There are no galleries yet. You can create new one using Add gallery menu','fgallery'). '</td></tr>';
    }
    $output .= '</tbody></table>';
    return $output;
}

/**
 * Renders the row in the Galleries List
 * see fgallery_render_albums_table
 * 
 * @param array $album
 * @return string 
 */
function fgallery_render_album_item_row($album) {
    $id = $album['gall_id'];
    $output = '<tr class="fgallery_album" id="album_'.$album['gall_id'].'">';
            $output .= '<th class="check-column"><input type="checkbox" value="'.$album['gall_id'].'" name="gallery[]" /></th>';
            $output .= '<td>'.$id.'<span class="order" style="display:none">'.$album['gall_order'].'</span></td>';
            $output .= '<td><a href="'.fgallery_get_edit_url($id).'">'.$album['gall_name'].'</a></td>';
            $output .= '<td><a href="'.fgallery_get_edit_url($id).'">'.fgallery_get_album_cover($album).'</a></td>';
            $output .= '<td>'.$album['gall_description'].'</td>';
            $output .= '<td>'.fgallery_get_album_attributes($album).'</td>';
            $output .= '<td>'.fgallery_get_album_actions($id, $album['gall_type']).'</td>';
    $output .= '</tr>';
    return $output;
}

/**
 * Renders form for Image from a given Gallery ID
 * @param array $item
 * @param integer $gall_id
 * @param integer $type
 * @return string 
 */
function fgallery_render_album_image_form($item, $gall_id, $type) {
    if ($gall_id == '') {
            return '';
    }
    $item_extra = unserialize($item['img_extra']);
    $output = '<p><label for="fgalleryImageCaption_'.$item['img_id'].'">'.__('Thumbnail Caption:', 'fgallery').'</label>
                    <input type="text" id="fgalleryImageCaption_'.$item['img_id'].'" name="fgallery_image_caption" value="'.stripslashes($item['img_caption']).'" /></p>';
    $output .= '<p><label for="fgalleryImageDescription_'.$item['img_id'].'">'.__('Full View Caption:', 'fgallery').'</label>
                    <textarea cols="30" rows="2" id="fgalleryImageDescription_'.$item['img_id'].'" name="fgallery_image_description">'.stripslashes($item['img_description']).'</textarea></p>';
    $output .= '<p><label for="fgalleryImageText_'.$item['img_id'].'">'.__('Text Under Slideshow:', 'fgallery').'</label>
                    <textarea cols="30" rows="2" id="fgalleryImageText_'.$item['img_id'].'" name="fgallery_image_text">'.stripslashes($item_extra['img_text']).'</textarea></p>';
    $output .= '<p><label for="fgalleryImageURL_'.$item['img_id'].'">'.__('URL:', 'fgallery').'</label>
                    <input type="text" id="fgalleryImageURL_'.$item['img_id'].'" name="fgallery_image_url" value="'.$item['img_url'].'" /></p>';
    if ($type == 7) {
        if ($item_extra['img_type'] == 'page') {
            $selected_page = ' selected="selected"';
            $selected_spread = '';
        } else {
            $selected_spread = ' selected="selected"';
            $selected_page = '';
        }
        $output .= '<p><label for="fgalleryImageType_'.$item['img_id'].'">'.__('Image Type:','fgallery').'</label>
            <select name="img_type" id="fgalleryImageType_'.$item['img_id'].'">
                    <option value="page"'.$selected_page.'>'.__('Page','fgallery').'</option>                    
                    <option value="spread"'.$selected_spread.'>'.__('Spread','fgallery').'</option>                 
                </select></p>';
    }
    $output .= wp_nonce_field('fgallery_edit','fgallery_edit_image_field_'.$item['img_id']);
    $output .= '<input type="hidden" name="gall_id" id="fgalleryImageGall_'.$item['img_id'].'" value="'.$gall_id.'" />';
    $output .= '<a rel="'.$item['img_id'].'" class="save_album_image" href="javascript:void(0);">'.__('Save').'</a>';
    return $output;
}

function fgallery_render_album_galleries($items, $gall_id) {
    $title = __('Remove', 'fgallery');
    $title_2 = __('Images List', 'fgallery');
    $title_3 = __('Edit','fgallery');
    $title_4 = __('Add images','fgallery');
    $album = fgallery_get_album($gall_id);
    $output = '<ul class="fgallery_list" id="'.$gall_id.'">';
    foreach ($items as $item) {
        $output .= '<li id="image_'.$item['img_id'].'"><table class="image_wrap"><tr>';
        $output .= '<td class="fgallery_image">';
        $output .= '<div class="edit_gallery_urls">'.fgallery_get_album_cover($item['id']).'<br />';               
        $output .='</div><td class="fgallery_name">
                        <b><a href="'.fgallery_get_edit_url($item['id']).'" target="_blank">'.$item['gall_name'].'</a></b></td>';
        $output .= '</td><td class="fgallery_image_info album_image_form">';
        $temp = fgallery_get_album($item['id']);
        $output .= fgallery_get_album_attributes($temp);
        $output .=  '</td><td class="fgallery_image_actions">
            <a href="'.fgallery_get_edit_url($item['id']).'" target="_blank" title="'.$title_3.'">'.$title_3.'</a>
            <a href="'.fgallery_get_album_images_url($item['id']).'" target="_blank" title="'.$title_2.'">'.$title_2.'</a>
            <a href="javascript:void(0);" class="image_remove" rel="image_'.$item['id'].'" 
                id="image_'.$item['id'].'_'.$gall_id.'" title="'.$title.'">'.$title.'</a>';
        $output .= '<a href="'.fgallery_get_addimages_page_url($item['id']).'"
                            class="thickbox" title="'.$title_4.'">'.$title_4.'</a>';       
        $output .='</td></tr></table></li>';
    }
        $output .= '</ul>';
        return $output;
        
}

/**
 * Renders the list of gallery images on List Images
 * @param array $items
 * @param integer $gall_id
 * @return string 
 */
function fgallery_render_album_images($items, $gall_id) {
    $title = __('Remove', 'fgallery');
    $title_2 = __('Set as cover', 'fgallery');
    $title_3 = __('Edit','fgallery');
    $title_4 = __('Add images','fgallery');
    $album = fgallery_get_album($gall_id);
    $output = '<form method="post" action="'.fgallery_get_album_images_url($gall_id).'" id="gallery_images_form">';
    $output.= fgallery_get_listof_gallery_albums($gall_id);
    $output.= '<ul class="fgallery_list" id="'.$gall_id.'">';
    $output .= '<a class="save_all_album_images" href="javascript:void(0)">'.__('Save All Changes').'</a>';
    foreach ($items as $item) {
        if ($album['gall_cover'] == $item['img_id']) {
            $cover_class = 'album_cover';
        } else {
            $cover_class = '';
        }
        $output .= '<li id="image_'.$item['img_id'].'" class="'.$cover_class.'"><table class="image_wrap"><tr>
       <th class="check-column">';
        if ($item['img_vs_folder'] != 2) {
            $output .= '<input type="checkbox" name="images[]" value="'.$item['img_id'].'" />';
        }
        $output .= '</th>
	        <td class="fgallery_image">';
        if ($item['img_vs_folder'] == 0){		
            $output .= '<div class="edit_gallery_urls">
                <img src="'.fgallery_get_image_preview($item['img_id']).'" alt="'.fgallery_escape_string($item['img_caption']).'" /><br /><br />
                <a href="javascript:void(0);" class="fgallery_action rotate_image" rel="0" id="rotate_'.$item['img_id'].'">'.__('Rotate CW','fgallery').'</a>
                <a href="javascript:void(0);" class="fgallery_action rotate_image" rel="1" id="rotate_'.$item['img_id'].'">'.__('Rotate CC','fgallery').'</a>
                <a href="'.fgallery_add_preview_form_url($item['img_id']).'" class="fgallery_action thickbox" id="preview_'.$item['img_id'].'">';
                if ($item['img_preview_path'] == '') {
                    $output .= __('Add Preview','fgallery').'</a>';
                } else {
                    $output .= __('Change Preview','fgallery').'</a>';
                }
              $output .='</div>';
        }
        $output .= '</td>
        <td class="fgallery_image_info album_image_form">';
        if ($item['img_vs_folder'] == 2){
            $output .= '<b><a href="'.fgallery_get_album_images_url($gall_id).'&folder='.$item['img_id'].'">'.
                            fgallery_escape_string($item['img_caption']).'</a></b>';
            $output .= '('.fgallery_count_gallery_album_images($item['img_id']).')';
        } else {
            $output .= fgallery_render_album_image_form($item, $gall_id, $album['gall_type']);
        }
        $output .='</td>
		<td style="width:400px"></td>
        <td class="fgallery_image_actions">
            <a href="'.fgallery_get_image_edit_url($item['img_id']).'" title="'.$title_3.'">'.$title_3.'</a>
            <a href="javascript:void(0);" class="image_remove" rel="image_'.$item['img_id'].'" 
                id="image_'.$item['img_id'].'_'.$gall_id.'" title="'.$title.'">'.$title.'</a>';
            if ($item['img_vs_folder'] != 2) {
                    $output .= '<a href="javascript:void(0);" class="image_cover" rel="image_'.$item['img_id'].'"
                        id="cover_'.$item['img_id'].'_'.$gall_id.'" title="'.$title_2.'">'.$title_2.'</a>';
            } else {
                $output .= '<a href="'.fgallery_get_addimages_page_url($gall_id, $item['img_id']).'"
                            class="thickbox" title="'.$title_4.'">'.$title_4.'</a>';
            }
        $output .='</td></tr></table></li>';
    }
    $output .= '<a class="save_all_album_images" href="javascript:void(0)">'.__('Save All Changes').'</a>';
    $output .= '</ul>';
    $output .= '</form>';
    return $output;
}

/**
 * Renders the table of Images List
 * @param array $images
 * @return string 
 */
function fgallery_render_images_table($images){
    $output = '<table class="widefat fixed">
              <thead>
                    <tr>
                        <th id="cb" class="manage-column check-column column-cb" scope="col" >
                            <input type="checkbox" name="check_all" class="check_all" />
                        </th>
                        <th class="gall_id" scope="col">'.__('ID').'</th>
                        <th id="title" scope="col">'.__('Caption', 'fgallery').'</th>
                        <th class="gall_cover" scope="col">'.__('Preview', 'fgallery').'</th>
                        <th class="gall_description" scope="col">'.__('Description', 'fgallery').'</th>
                        <th class="gall_attr" scope="col">'.__('Attributes', 'fgallery').'</th>
                        <th class="fgallery_actions" scope="col">'.__('Actions', 'fgallery').'</th>
                    </tr>
              </thead>';
    $output .= '<tfoot>
                    <tr>
                        <th class="manage-column check-column column-cb" scope="col" >
                            <input type="checkbox" name="check_all" class="check_all" />
                        </th>
                        <th class="gall_id" scope="col">'.__('ID').'</th>
                        <th scope="col">'.__('Caption', 'fgallery').'</th>
                        <th class="gall_cover" scope="col">'.__('Preview', 'fgallery').'</th>
                        <th class="gall_description" scope="col">'.__('Description', 'fgallery').'</th>
                        <th class="gall_attr" scope="col">'.__('Attributes', 'fgallery').'</th>
                        <th class="fgallery_actions" scope="col">'.__('Actions', 'fgallery').'</th>
                    </tr>
		  </tfoot><tbody>';
    if (isset($_GET['folder'])) {
        $output .=  '<tr class="droppable" id="folder_0">
                        <td colspan="7">
                            <a class="go_up" href="'.fgallery_images_url().'">'.__('Go up', 'fgallery').'...</a>
                        </td>
                     </tr>';
    }
    if (!empty($images)) {
        foreach ($images as $image) {
           $output .= fgallery_render_image_item_row($image);
        }
    } else {
           $output .= '<tr><td colspan="7">'.__('There are no images yet. You can upload new ones using Upload images menu', 'fgallery'). '</td></tr>';
    }
    $output .= '</tbody></table>';
    return $output;
}

/**
 * Renders the row from Images List page
 * @param array $image
 * @return string 
 */
function fgallery_render_image_item_row($image) {
    $id = $image['img_id'];
    if ($image['img_vs_folder']){
        $output = '<tr class="fgallery_album droppable" id="folder_'.$id.'">';
    } else {
        $output = '<tr class="fgallery_album draggable" id="image_'.$id.'">';
    }
    $output .= '<th class="check-column"><input type="checkbox" value="'.$id.'" name="image[]" /></th>';
    $output .= '<td>'.$id.'</td>';
    if ($image['img_vs_folder']) {
        $output .= '<td><a href="'.fgallery_get_folder_url($id).'">'.$image['img_caption'].'</a></td>';
        $output .= '<td><a href="'.fgallery_get_folder_url($id).'">                        
                            <img src="'.FGALLERY_PATH.'/images/folder.png" alt="'.fgallery_escape_string($image['img_caption']).'" class="folder" />
                        </a>
                   </td>';
        $output .= '<td>'.fgallery_escape_string($image['img_description']).'</td>';
        $output .= '<td class="folder_attr">'.fgallery_get_folder_attributes($image).'</td>';
        $output .= '<td>'.fgallery_get_folder_actions($id).'</td>';
    } else {
        $output .= '<td><a href="'.fgallery_get_image_edit_url($id).'">'.
                            fgallery_escape_string($image['img_caption']).'
                        </a>
                   </td>';
        $output .= '<td><a href="'.fgallery_get_image_edit_url($id).'">
                            <img src="'.fgallery_get_image_preview($image['img_id']).'" alt="'.fgallery_escape_string($image['img_caption']).'"/>
                        </a>
                    </td>';
        $output .= '<td>'.$image['img_description'].'</td>';
        $output .= '<td>'.fgallery_get_image_attributes($image).'</td>';
        $output .= '<td>'.fgallery_get_image_actions($id).'</td>';
    }
    $output .= '</tr>';
    return $output;
}

/**
 * Renders the list of Gallery Albums
 * @global type $wpdb
 * @param integer $id
 * @return string 
 */
function fgallery_get_listof_gallery_albums($id) {
    global $wpdb;
    $albums =$wpdb->get_results("SELECT a.img_caption, a.img_id 
                                 FROM ".IMAGES_TABLE." as a 
                                 LEFT JOIN ".IMAGES_TO_ALBUMS_TABLE." as b 
                                     ON (a.img_id = b.img_id)
                                 WHERE a.img_vs_folder = 2 AND b.gall_id = ".$id, ARRAY_A);
    if (count($albums) > 0) {
        $output = '<select name="album_id" id="album_id_images">';
                $output .= '<optgroup label="'.__('Choose Album', 'fgallery').'">';
        foreach ($albums as $album) {
                $output .= '<option value="'.$album['img_id'].'">'.$album['img_caption'].'</option>';
        }
        $output .= '</optgroup>';
        $output .= '<optgroup label="'.__('Actions').'">';
                $output .= '<option value="-1">'.__('Remove Selected').'</option>';
        $output .= '</optgroup>';
        $output .= '</select>';
        $output .= '<input type="hidden" value="'.$id.'" name="gall_id" />';
        $output .= '<input type="submit" value="'.__('Go', 'fgallery').'" />';
    }
    $output .= '<br clear="all" />';
    return $output;
}

/**
 * Renders the Gallery edit form
 * @param array $album
 * @param string $source
 * @return string 
 */
function fgallery_render_edit_album_form($album) {
    $id = $album['gall_id'];
    $output = '<div id="sc-group-general" class="sc-skin-params-tab-panel">';
    $output .= '<div class="form-item even">
                    <label for="fgalleryName">'.__('Name:', 'fgallery').'</label>
                    <textarea cols="30" rows="5" id="fgalleryName" 
                    name="gallery[fgallery_name]" class="required">'.
                    fgallery_escape_string(@$album['gall_name']).'</textarea>
                </div>';
    $output .= '<div class="form-item odd">
                    <label for="fgalleryDescription">'.__('Description:','fgallery').'</label>
                    <textarea cols="30" rows="5" id="fgalleryDescription" 
                    name="gallery[fgallery_description]">'.
                    fgallery_escape_string(@$album['gall_description']).'</textarea>
               </div>';
    $output .= '<div class="form-item even" style="display:none;">
                    <label for="fgallery_status">'.__('Status', 'fgallery').'</label>
                    <select name="gallery[fgallery_status]" id="fgallery_status" class="form-select">
                       <option value="1" '.@$pubselect.'>'.__('Published').'</option>
                    </select>
               </div>';
    $output .= '<div class="form-item even">
                    <label for="fgalleryWidth">'.__('Width:', 'fgallery').'</label>
                    <input type="text" id="fgalleryWidth" name="gallery[gall_width]" 
                    class="form-text numeric" value="'.$album['gall_width'].'" />
                </div>';
    $output .= '<div class="form-item odd">
                    <label for="fgalleryHeight">'.__('Height:', 'fgallery').'</label>
                    <input type="text" id="fgalleryHeight" name="gallery[gall_height]" 
                    class="form-text numeric" value="'.$album['gall_height'].'" />
                </div>';
    $output .= '<div class="form-item even" style="display:none;">
                    <label for="fgalleryBgcolor">'.__('Background Color:', 'fgallery').'</label>
                    <input type="text" id="fgalleryBgcolor" name="gallery[gall_bgcolor]" 
                    class="sc-color-val form-text" value="#'.$album['gall_bgcolor'].'" />
                </div>';
    $opt = array();
    $opt[$album['gall_type']] = 'selected ="selected"';
    $output .= '<div class="form-item odd">
                    <label for="gall_type"><b>'.__('Gallery Type:', 'fgallery').'</b></label> 
                    <select name="gallery[gall_type]" id="gall_type" class="form-select" onchange="save_settings();">
                            <option value="1" '.@$opt[1].'>'.__('Acosta').'</option>
                            <option value="2" '.@$opt[2].'>'.__('Airion').'</option>
                            <option value="3" '.@$opt[3].'>'.__('Arai').'</option>
                            <option value="4" '.@$opt[4].'>'.__('Pax').'</option>
                            <option value="5" '.@$opt[5].'>'.__('Pazin').'</option>
                            <option value="6" '.@$opt[6].'>'.__('Postma').'</option>
                            <option value="7" '.@$opt[7].'>'.__('PageFlip').'</option>
                            <option value="8" '.@$opt[8].'>'.__('Nilus').'</option>
                            <option value="9" '.@$opt[9].'>'.__('Nusl').'</option>
                            <option value="10" '.@$opt[10].'>'.__('Kranjk').'</option>
                            <option value="11" '.@$opt[11].'>'.__('Perona').'</option>
                            <option value="12" '.@$opt[12].'>'.__('Ables').'</option>
                            './*<option value="13" '.@$opt[13].'>'.__('New PageFlip').'</option>*/'
                    </select>
                    <input type="hidden" name="gallery[old_gall_type]" value="'.$album['gall_type'].'" />
                </div>';
    $output .= '</div>';

    return $output;
}

/**
 * Renders the Image edit form
 * @param array $image
 * @return string 
 */
function fgallery_render_edit_image_form($image) {
    $id = $image['img_id'];
    $output = '<form method="post" action="'.fgallery_get_image_edit_url($id).'" enctype="multipart/form-data">';
    $output .= '<p><label for="fgalleryImageCaption">'.__('Caption:', 'fgallery').'</label>
                    <br />
                    <input type="text" id="fgalleryImageCaption" name="fgallery_image_caption"
                    value="'.fgallery_escape_string($image['img_caption']).'" />
                </p>';
    if (!$image['img_vs_folder']){
        $output .= '<p><label for="fgalleryImageDescription">'.__('Description:', 'fgallery').'</label><br />
                <textarea cols="30" rows="5" id="fgalleryImageDescription" name="fgallery_image_description">'.
                fgallery_escape_string($image['img_description']).'</textarea></p>';
        $output .= '<p><label for="preview">'.__('You can upload image to be a preview in a slideshow instead of big image','fgallery').
                    '</label><br /><input type="file" name="preview_path" id="preview" /> <br clear="all" /></p>';
        $output .= '<input type="hidden" name="img_path" value="'.$image['img_path'].'" />';
                                
    }
    $output .= wp_nonce_field('fgallery_edit','fgallery_edit_image_field');
    $output .= '<p><input type="submit" name="fgallery_image_submit" value="'.__('Save').'" /></p>';
    $output .= '<input type="hidden" name="img_preview_path" value="'.$image['img_preview_path'].'" />';
    $output .='</form>';	
    return $output;
}

/**
 * Returns the wrapped in img tag album cover
 * @global type $wpdb
 * @param array $album
 * @param integer $width
 * @return string 
 */
function fgallery_get_album_cover($album, $url_only = 0, $width ='') {
    global $wpdb;
        $gall_id = $album['gall_id'];
	if ($album['gall_cover'] != 0){
            $cover_id = $album['gall_cover'];
            $cover = $wpdb->get_row("SELECT `img_id` as id, `img_preview_path`, `img_path`, `img_caption` 
                                     FROM " . IMAGES_TABLE . " 
                                     WHERE `img_id` = " .$cover_id."
                                     LIMIT 1", 'ARRAY_A');
	} else {
            if ($album['gall_type'] == 13) {
                 $cover = $wpdb->get_row("SELECT a.`img_id` as id, a.`img_path`, a.`img_preview_path`, a.`img_caption` 
                                     FROM " . IMAGES_TABLE . " as a 
                                     LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE ." as b 
                                         ON (a.img_id = b.img_id) 
                                     WHERE b.gall_id IN (
                                         SELECT c.img_id FROM ". IMAGES_TO_ALBUMS_TABLE ." as c
                                         WHERE c.gall_id = ".$gall_id."
                                     )
                                     ORDER BY b.img_order ASC 
                                     LIMIT 1", 'ARRAY_A');
            } else {
                $cover = $wpdb->get_row("SELECT a.`img_id` as id, a.`img_path`, a.`img_preview_path`, a.`img_caption` 
                                     FROM " . IMAGES_TABLE . " as a 
                                     LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE ." as b 
                                         ON (a.img_id = b.img_id) 
                                     WHERE b.gall_id = " .$gall_id." 
                                     ORDER BY b.img_order ASC 
                                     LIMIT 1", 'ARRAY_A');
            }
	}
        if ($width == '') {
            $width = 200;
        }
        if ($url_only) {
            return fgallery_get_image_preview($cover['id']);
        }
	if ($cover['img_path']!=''){
            $gall_cover = '<img src="'.fgallery_get_image_preview($cover['id']).'" style="width:'.$width.'px;" alt="'.fgallery_escape_string($cover['img_caption']).'" />';
            return $gall_cover;
	}
    /* if we get here then there is no cover image or any image in the gallery at all
     * so return false
     */
    return false; //Maybe place default cover here
}

/** 
 * Returns rendered album attributes 
 * (image quantity, total size, publish date and etc.)
 *
 * @global type $wpdb
 * @param type $album
 * @param type $extra
 * @return string 
 */

function fgallery_get_album_attributes($album, $extra = true) {
    global $wpdb;
        $id = $album['gall_id'];
        if ($album['gall_type'] == 13) {
            $attr = $wpdb->get_row("SELECT COUNT(a.img_id) as 'quantity', SUM(a.img_size) as 'total_size'
                                FROM " . IMAGES_TABLE . " as a 
                                LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE ." as b ON (a.img_id = b.img_id)
                                WHERE b.gall_id IN (
                                    SELECT c.img_id FROM ". IMAGES_TO_ALBUMS_TABLE ." as c
                                    WHERE c.gall_id = " .$id."
                                ) AND a.img_vs_folder <> 2", 'ARRAY_A');
        } else {
            $attr = $wpdb->get_row("SELECT COUNT(a.img_id) as 'quantity', SUM(a.img_size) as 'total_size'
                                FROM " . IMAGES_TABLE . " as a 
                                LEFT JOIN ". IMAGES_TO_ALBUMS_TABLE ." as b ON (a.img_id = b.img_id)
                                WHERE b.gall_id = " .$id." AND a.img_vs_folder <> 2", 'ARRAY_A');
        }
	$output = __('Number of Photos: ', 'fgallery').$attr['quantity']. '<br />';
	$output .= __('Total Size: ','fgallery').formatBytes($attr['total_size']). '<br />';
	if ($extra) {
            if ($album['gall_published']) {
                $status = __('Published');
            } else {
                $status = __('Draft');
            }
            $output .= __('Gallery Status: ','fgallery').$status. '<br />';
            $output .= date("d F Y",strtotime($album['gall_createddate'])). '<br />';
	}
    return $output;
}

/**
 * Returns rendered Image attributes
 * @param array $image
 * @return string
 */
 
function fgallery_get_image_attributes($image){
    $id = $image['img_id'];
    $albums = fgallery_image_get_albums($id);
    $albums_name = '';
    if (count($albums)>0){
        foreach ($albums as $album) {
            $albums_name .= $album['gall_name'].',';
        }
        $albums_name = substr($albums_name, 0, -1);
    } else {
        $albums_name = __('Image is not in any gallery', 'fgallery');
    }
    $output = __('Galleries:', 'fgallery').$albums_name.'<br />';
    $output.= __('Image Type:', 'fgallery').$image['img_type'].'<br />';
    $output.= __('Image Size:', 'fgallery').formatBytes($image['img_size']).'<br />';
    $output.= __('Upload Date:', 'fgallery').date("d F Y",strtotime($image['img_date']));
    return $output;
}

/**
 * Returns rendered folder attributes
 * @param array $image
 * @return string 
 */
function fgallery_get_folder_attributes($image){
    global $wpdb;
    if ($image['img_vs_folder']) {
        $id = $image['img_id'];
        $attr = $wpdb->get_row("SELECT COUNT(a.img_id) as 'quantity', SUM(a.img_size) as 'total_size'
                                FROM " . IMAGES_TABLE . " as a  WHERE a.img_parent = " .$id, 'ARRAY_A');
        $output = __('Images:', 'fgallery').$attr['quantity'].'<br />';
        $output.= __('Images Size:', 'fgallery').formatBytes($attr['total_size']).'<br />';
        $output.= __('Create Date:', 'fgallery').date("d F Y",strtotime($image['img_date']));
    }
    return $output;
}

/**
 * Renders the Gallery actions
 */
function fgallery_get_album_actions($id, $gall_type = 3) {
    if ($gall_type == 13) {
        $title_1 = __('Add Galleries','fgallery');
        $title_4 = __('Edit Galleries', 'fgallery');
    } else {
        $title_1 = __('Add Images','fgallery');
        $title_4 = __('Edit Images', 'fgallery');
    }
    $title_2 = __('Edit Gallery','fgallery');
    $title_3 = __('Delete Gallery','fgallery');
    if ($gall_type == 13) {
        $output = '<a href="'.fgallery_get_addgalleries_page_url($id).'" title="'.$title_1.'" class="fgallery_action thickbox">'.$title_1.'</a>';
        $output .= '<a href="'.fgallery_get_album_galleries_url($id).'" title="'.$title_4.'" class="fgallery_action">'.$title_4.'</a>';
    } else {
        $output = '<a href="'.fgallery_get_addimages_page_url($id).'" title="'.$title_1.'" class="fgallery_action thickbox">'.$title_1.'</a>';
        $output .= '<a href="'.fgallery_get_album_images_url($id).'" title="'.$title_4.'" class="fgallery_action">'.$title_4.'</a>';
    }
    $output .= '<a href="'.fgallery_get_edit_url($id).'" title="'.$title_2.'" class="fgallery_action">'.$title_2.'</a>';
    
    if (fgallery_access_level()>= 8){
        $output .= '<a href="javascript:void(0);" rel="album_'.$id.'" title="'.$title_3.'" class="fgallery_action delete">'.$title_3.'</a>';
    }
    return $output;
}

/*
 * Renders the folder actions
 */
function fgallery_get_folder_actions($id) {
    $title_2 = __('Change Name', 'fgallery');
    $title_3 = __('Delete Folder', 'fgallery');
    $output .= '<a href="'.fgallery_get_image_edit_url($id).'" title="'.$title_2.'" class="fgallery_action">'.$title_2.'</a>';
    $output .= '<a href="#" class="fgallery_action create_from_folder" rel="'.$id.'">'.__('Create Gallery', 'fgallery').'</a>';
    if (fgallery_access_level()>= 5){
        $output .= '<a href="javascript:void(0);" rel="folder_'.$id.'" title="'.$title_3.'" class="fgallery_action delete">'.$title_3.'</a>';
    }
    return $output;
}

/**
 * Renders the image actions
 */
function fgallery_get_image_actions($id) {
    $title_2 = __('Edit Image', 'fgallery');
    $title_3 = __('Delete Image', 'fgallery');
    $output = '<a href="'.fgallery_get_image_edit_url($id).'" title="'.$title_2.'" class="fgallery_action">'.$title_2.'</a>';
    if (fgallery_access_level()>= 5){
        $output .= '<a href="javascript:void(0);" rel="image_'.$id.'" title="'.$title_3.'" class="fgallery_action delete">'.$title_3.'</a>';
    }
    return $output;
}

/**
 * Renders folder create form
 */
function fgallery_create_folder_form($gall_id, $type, $upload = 0){
    if (!is_numeric($type)){
        $type = 1;
    } 
    if(!is_numeric($gall_id)){ 
        $gall_id = 0;
    };
    if ($type == 1) : ?>
    <h2><?php _e('Create New Folder')?></h2>
    <?php else : ?>
    <h2><?php _e('Create New Album')?></h2>
    <?php endif; ?>
    <form action="<?php echo fgallery_save_folder_url()?>" method="POST" id="folder_add_form">
        <p><label for="fgalleryImageCaption"><?php _e('Name:', 'fgallery') ?></label><br />
        <input type="text" id="fgalleryImageCaption" name="fgallery_image_caption" value="" /></p>
        <input type="hidden" name="folder_type" value="<?php echo $type;?>" />
        <input type="hidden" name="gall_id" value="<?php echo $gall_id;?>" />
        <input type="hidden" name="upload" value="<?php echo $upload;?>" />
        <?php wp_nonce_field('fgallery_create_folder','fgallery_folder_field'); ?>
        <p><input type="submit" name="fgallery_image_submit" id="shortcode" value="<?php _e('Save') ?>" /></p>
    </form>
    <?php
}

/**
 * Renders the add gallery box for given gallery id
 * @param array $items
 * @param int $id
 * @return string 
 */

function fgallery_render_addgallery_box($items, $id) {
    $output = '<h2>'.__('Adding Images to ', 'fgallery').'"'.fgallery_get_album_name($id).'"'.$album_name.'</h2>';
    $output .= '<form action="'.fgallery_get_addgalleries_url($id).'" method="post" id="add_images_form">';
    $output .= '<input type="submit" class="fgallery_action" value="'.__('Add To Gallery', 'fgallery').'" /> 
                <input type="checkbox" class="fgallery_addimages_all" value="1" />
                <label class="fgallery_addimages_all">'.__('Select All','fgallery').'</label><br clear="all" />';
    $output .= '<ul>';
    if (!empty($items)) {
            foreach ($items as $item) {
                $output .= '<li><table class="image_wrap"><tr>
                                    <th class="fgallery_image_actions">
                                            <input type="checkbox" value="'.$item['gall_id'].'" name="gallery[]" />
                                    </th>
                                    <td class="fgallery_image">';
                $output .= fgallery_get_album_cover($item);
                $output .= '</td><td class="fgallery_image_info">';
                $output .='<b>'.$item['gall_name'].'</b> <br />'.$item['gall_description'].'<br />';
                $output .= fgallery_get_album_attributes($item);
                $output .= '</td></tr></table></li>';
            }
                $output .= '</ul><input type="hidden" value="'.$id.'" name="gall_id" id="gall_id" />
                <input type="submit" class="fgallery_action" value="'.__('Add To Gallery', 'fgallery').'" />
                <input type="checkbox" class="fgallery_addimages_all" value="1" />
                <label class="fgallery_addimages_all">'.__('Select All','fgallery').'</label><br clear="all" />';
                $output .= wp_nonce_field('fgallery_add_gallery','fgallery_add_galleries_to_gallery');
                $output .= '</form>';
            
    } else {
            $output = 'You need to create at least one gallery <a href="'.fgallery_createalbum_url().'" target="_blank">here</a>';
    }
    return $output;
}

/**
 * Renders the add image box for given gallery id
 * @param array $items
 * @param int $id
 * @return string 
 */
function fgallery_render_addimage_box($items, $id) {
    if (isset($_GET['fid']) && is_numeric($_GET['fid'])) {
        $fid = $_GET['fid'];
    } else {
        $fid = 0;
    }
    if ($fid) {
        $album_name = ' ("'.fgallery_get_folder_name($fid).'" album)';
    } else {
        $album_name = '';
    }
    if (count($items) > 0) {
    $output = '<h2>'.__('Adding Images to ', 'fgallery').'"'.fgallery_get_album_name($id).'"'.$album_name.'</h2>';
    if (isset($_GET['folder']) && is_numeric($_GET['folder'])) {
        $output .= '<p>'.__('You are here : ', 'fgallery').
                '<a href="'.fgallery_get_addimages_page_url($id).'" class="add_images_folder">'.__('All Images', 'fgallery').'...</a>->'
                .fgallery_get_folder_name($_GET['folder']).'</p>';
    }
    $output .= '<form action="'.fgallery_get_addimages_url($id).'" method="post" id="add_images_form">';
    $output .= '<input type="submit" class="fgallery_action" value="'.__('Add To Gallery', 'fgallery').'" /> 
                <input type="checkbox" class="fgallery_addimages_all" value="1" />
                <label class="fgallery_addimages_all">'.__('Select All','fgallery').'</label><br clear="all" />';
    if (isset($_GET['folder'])) {
        $output .=  '<a class="go_up add_images_folder" href="'.fgallery_get_addimages_page_url($id).'">'.__('Go Up', 'fgallery').'...</a>';
    }
    $output .= '<ul>';
            foreach ($items as $item) {
                    $checked = '';
                    if (@in_array($item['img_id'],$_SESSION['image'])) {
                            $checked = 'checked="checked"';
                    } else {
                            $checked = '';
                    }
                    $output .= '<li><table class="image_wrap"><tr>
                                    <th class="fgallery_image_actions">
                                            <input type="checkbox" value="'.$item['img_id'].'" name="image[]" '.$checked.' />
                                    </th>
                                    <td class="fgallery_image">';
                    if ($item['img_vs_folder'] == 1){
                                    $output .= '<img src="'.FGALLERY_PATH.'/images/folder.png" />';
                            } elseif($item['img_vs_folder'] == 0) {
                                            $output .='<img src="'.fgallery_get_image_preview($item['img_id']).'" /> ';
                            }
                    $output .= '</td><td class="fgallery_image_info">';
                    if ($item['img_vs_folder'] == 1){
                            $output .= '<a href="'.fgallery_get_addimages_page_url($id).'&folder='.$item['img_id'].'" class="add_images_folder">
                                            <b>'.$item['img_caption'].'</b>
                                        </a>';
                            $output .= '<br />'.fgallery_get_folder_attributes($item);
                    } elseif ($item['img_vs_folder'] == 0) {
                            $output .='<b>'.$item['img_caption'].'</b> <br />'.$item['img_description'];
                    }
                    $output .= '</td></tr></table></li>';
            }
            $output .= '</ul><input type="hidden" value="'.$id.'" name="gall_id" id="gall_id" />
                <input type="hidden" value="'.$fid.'" name="fid" />
                <input type="submit" class="fgallery_action" value="'.__('Add To Gallery', 'fgallery').'" />
                <input type="checkbox" class="fgallery_addimages_all" value="1" />
                <label class="fgallery_addimages_all">'.__('Select All','fgallery').'</label><br clear="all" />';
            $output .= wp_nonce_field('fgallery_add_images','fgallery_add_images_to_gallery');
            $output .= '</form>';
    } else {
            $output = 'You need to upload images for your gallery <a href="'.fgallery_get_upload_url().'" target="_blank">here</a>';
    }
    return $output;
}


/**
 * Renders the form for adding the image preview
 * 
 * @params array $images
 * @return string 
 */
function fgallery_render_add_preview_form($image) {
        $output = '<form id="add_preview_form" method="post" enctype="multipart/form-data" action="'.  fgallery_add_preview_url().'">';
        if ($image['img_preview_path'] != '') {
            $output .= __('Current Preview:','fgallery');
            $output .= '<br /><img src="'.get_option('siteurl').'/'.$image['img_preview_path'].'" alt="'.$image['img_caption'].'" /><br />';
        } 
        $output .= __('Upload New Preview','fgallery');
        $output .=  '<input type="file" name="preview" />';
        $output .=  '<input type="hidden" name="img_id" value="'.$image['img_id'].'" />';
        $output .=  '<input type="hidden" name="img_path" value="'.$image['img_path'].'" /><br />';
        $output .=  '<input type="submit" value="'.__('Save').'" class="fgallery_action" />';
        $output .=  '</form>';
       return $output;
}

function fgallery_render_folder_list($folders , $name) {
   $output = '<label for="'.$name.'">'.__('Images will be saved to:','fgallery').'</label>
                <select id="'.$name.'" name="'.$name.'" class="choose_folder">';
    $output .= fgallery_folder_list_options($folders);
    $output .= '</select> 
            <a href="'.fgallery_create_folder_url(1,0,1).'" class="fgallery_action thickbox" >
                '.__('Create New Folder','fgallery').'</a> <br clear="all" />';
    return $output;
}

function fgallery_folder_list_options($folders, $id = 0) {
        $output = '<option value="0">'.__('Root folder', 'fgallery').'</option>';
        if (!empty($folders)):
                  foreach ($folders as $item): 
                        if ($item['img_id'] == $id) {
                            $selected = ' selected="selected"';
                        } else {
                            $selected = '';
                        }
                        $output .= '<option value="'.$item['img_id'].'"'.$selected.'>'.$item['img_caption'].'</option>';
                  endforeach;  
              endif;
       return $output;
}
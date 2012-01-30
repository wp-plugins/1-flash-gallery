<?php
/* 
 * Templates functions
 */

/**
 * Returns all the templates for a given gallery skin
 * @global type $wpdb
 * @param int $gall_type
 * @return array 
 */
function fgallery_get_templates($gall_type) {
    global $wpdb;
        $templates = $wpdb->get_results("SELECT * FROM ".TEMPLATES_TABLE." 
                                         WHERE `gall_type` = ".$gall_type." 
                                         ORDER BY created DESC", 'ARRAY_A');
        return $templates;
}

/**
 * Returns gallery settings by template id
 * @global type $wpdb
 * @param int $id
 * @return array 
 */
function fgallery_get_template_settings($id) {
    global $wpdb;
	$settings = $wpdb->get_var("SELECT `gall_settings` FROM ".TEMPLATES_TABLE." 
                                    WHERE id = ".$id);
	return unserialize($settings);
}

/**
 * Saves the template to database
 * @global type $wpdb 
 */
function fgallery_save_template(){
    global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_templates_field'],'fgallery_templates')) {		
        if (isset($_POST['gall_id']) && is_numeric($_POST['gall_id'])) {
                $gall_id = $_POST['gall_id'];
        } else {
                die('Invalid gallery');
        }
        if (isset($_POST['gall_type']) && is_numeric($_POST['gall_type'])) {
                $gall_type = $_POST['gall_type'];
        } else {
                die('Invalid gallery');
        }
        $settings = fgallery_get_album_settings($gall_id);
        if ($_POST['templ_title'] != '') {
            $wpdb->insert(TEMPLATES_TABLE, array('gall_type' => $gall_type,
                                                 'gall_settings' => serialize($settings),
                                                 'created' => date("Y-m-d"),
                                                 'templ_title' => $_POST['templ_title'],
                                                 'templ_description'=>$_POST['templ_description']));
            if ($wpdb->insert_id > 0) {
                _e('Template was saved successfully','fgallery');
            }
        } elseif (isset($_POST['update_ex']) && is_numeric($_POST['update_ex'])) {
            $templ_id = $_POST['update_ex'];
            $to_update = array('gall_type' => $gall_type, 'gall_settings' => serialize($settings));
            if ($_POST['templ_description'] != '') {
                $templ_description = $_POST['templ_description'];
                $to_update['templ_description'] = $templ_description;
            }
            $wpdb->update(TEMPLATES_TABLE, $to_update, array('id'=>$templ_id));
            _e('Template was saved successfully','fgallery');
        }
    }
    die();
}

/**
 * Loads the template
 */
function fgallery_load_template(){
  if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_load_settings_field'],'fgallery_load_settings')) {	
        if (isset($_POST['gall_id']) && is_numeric($_POST['gall_id'])) {
                $gall_id = $_POST['gall_id'];
        } else {
                die('Invalid gallery');
        }
        if (isset($_POST['template']) && is_numeric($_POST['template'])) {
            $templ_id = $_POST['template'];
            $settings = fgallery_get_template_settings($templ_id);
        } else {
            if (!empty($_FILES)) {
                $to_store = simplexml_load_file($_FILES['settings_file']['tmp_name']);
                $settings_array = array();
                
                //var_dump($to_store);
                //die();
                
                foreach ($to_store as $key=>$value) {
                    $name = 'sc_'.$key.'__';
                    foreach ($value as $key_2=>$value_2) {
                        $attr_name = $name.$key_2;
                        $new_value = (string)$value_2;
                        $settings_array[$attr_name] = str_replace('0x','#',$new_value);
                    }
                }
                if (empty($settings_array)) {
                    die('Invalid settings');
                } 
                $settings = $settings_array;//fgallery_prepare_settings($settings_array);
            } else {
                die('Invalid template');
            }
        }
        
                //var_dump($gall_id, $settings_array);
                
        
        if(fgallery_save_album_settings($gall_id, $settings)) {
            echo '1';
            die();
        } else {
            _e('There was error while loading template. Please try again', 'fgallery');
        }
    }
    die();
}

/**
 * Deletes the template from db
 */
function fgallery_delete_template(){
    global $wpdb;
    if(isset($_GET['templ_id']) && is_numeric($_GET['templ_id'])) {
        $templ_id = $_GET['templ_id'];
        $wpdb->query("DELETE FROM ".TEMPLATES_TABLE." WHERE `id` = ".$templ_id);
    }
    die();
}

/**
 * Returns the rendered form to save template for given gallery
 * @param array $gallery 
 */
function fgallery_render_template_form($gallery) {
    $templates = fgallery_get_templates($gallery['gall_type']);
?>
    <form action="<?php echo fgallery_get_save_settings_url($gallery['gall_id']) ?>" method="post" id="fgallery_save_template">
        <label for="templ_title"><?php _e('Template Title','fgallery') ?></label><br />
        <input type="text" name="templ_title" value="" id="templ_title" /><br />
        <?php 
            if (!empty($templates)) {
                echo '<p>'.__('You can update existing template','fgallery').'</p>';
                echo '<select name="update_ex">';
                echo '<option value="-" selected="selected">--------</option>';
                foreach ($templates as $template) {
                    echo '<option value="'.$template['id'].'">'.$template['templ_title'].'</option>';
                }
                echo '</select>';
            }
        ?><br />
        <label for="templ_description"><?php _e('Template Description','fgallery') ?></label><br />
        <textarea cols="70" rows="10" name="templ_description" id="templ_description"></textarea><br />
        <input type="submit" class="shortcode" value="<?php _e('Save') ?>" />
        <input type="hidden" name="gall_id" value="<?php echo $gallery['gall_id'] ?>" />
        <input type="hidden" name="gall_type" value="<?php echo $gallery['gall_type'] ?>" />
        <?php wp_nonce_field('fgallery_templates', 'fgallery_templates_field')?>
    </form>
<?php 
}

/**
 * Returns the rendered form to load template for given gallery
 * @param array $gallery 
 */
function fgallery_render_load_template_form($gallery) {
    $templates = fgallery_get_templates($gallery['gall_type']);
?>
    <form action="<?php echo fgallery_get_load_settings_url($gallery['gall_id']) ?>" method="post" 
          enctype="multipart/form-data" id="fgallery_load_template">
        <?php 
            if (!empty($templates)) {
                echo '<h2>'.__('Choose template from the list','fgallery').'</h2>';
                foreach ($templates as $template) {
                    echo '<div class="image_wrap" id="template_'.$template['id'].'">';
                    echo '<div class="fgallery_image_actions">';
                    echo '<a href="'.fgallery_get_template_delete_url($gallery['gall_id'], $template['id']).'"
                            rel="template_'.$template['id'].'" title="'.__('Delete','fgallery').'" 
                            class="fgallery_action delete">'.__('Delete','fgallery').'</a></div>';
                    echo '<div class="fgallery_image_info"><input type="radio" name="template" 
                            value="'.$template['id'].'" /><b>'.$template['templ_title'].'</b></div>';
                    echo '<div class="fgallery_image_info">'.$template['templ_description'].'</div>';
                    echo '</div>';  
                }
                echo '<br /><input type="submit" class="shortcode" value="'.__('Load').'" /><br /><br />';
            } else {
                echo '<p>'.sprintf(__('There are no templates for <b>%s</b> gallery type yet. Please create new or'),
                        fgallery_get_flash_type($gallery)).'<p>';
            }
        ?><br />

        <label for="settings_file">
                <?php _e('You can load template file from your local computer','fgallery');?>
        </label><br />
        <input type="hidden" name="gall_id" value="<?php echo $gallery['gall_id']?>" />
        <br />
        <input type="file" name="settings_file" id="settings_file" /> <br />
        <input type="submit" class="shortcode" value="<?php _e('Upload');?>" />
        <?php echo sprintf(__('Note: You need to be sure that you load settings file for <b>%s</b> gallery type','fgallery'),
                fgallery_get_flash_type($gallery)); ?>
        <?php wp_nonce_field('fgallery_load_settings','fgallery_load_settings_field'); ?>
    </form>
<?php
    die();
}

/**
 * Renders template save page
 */
function fgallery_save_template_page() {
    if (isset($_REQUEST['gall_id']) && is_numeric($_REQUEST['gall_id'])) {
            $gallery = fgallery_get_album($_REQUEST['gall_id']);
    } else {
            die('Invalid Gallery');
    }
    fgallery_render_template_form($gallery);
    die();
}

/**
 * Renders template load page
 */
function fgallery_load_template_page(){
    if (isset($_REQUEST['gall_id']) && is_numeric($_REQUEST['gall_id'])) {
            $gallery = fgallery_get_album($_REQUEST['gall_id']);
    } else {
            die('Invalid Gallery');
    }
    fgallery_render_load_template_form($gallery);
    die();
}
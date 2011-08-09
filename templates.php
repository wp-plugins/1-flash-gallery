<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/wp-load.php');
require_once('fgallery.php');

function fgallery_get_templates($gall_type) {
global $wpdb;
	$templates = $wpdb->get_results("SELECT * FROM ".TEMPLATES_TABLE." WHERE `gall_type` = ".$gall_type." ORDER BY created DESC", 'ARRAY_A');
	return $templates;
}

function fgallery_get_template_settings($id) {
global $wpdb;
	$settings = $wpdb->get_var("SELECT `gall_settings` FROM ".TEMPLATES_TABLE." WHERE id = ".$id);
	return unserialize($settings);
}

function fgallery_render_template_form($gallery) {
$templates = fgallery_get_templates($gallery['gall_type']);
?>
	<form action="<?php echo fgallery_get_save_settings_url($gallery['gall_id'], true) ?>" method="post">
		<label for="templ_title"><?php _e('Template Title','fgallery') ?></label><br />
		<input type="text" name="templ_title" value="" id="templ_title" /><br />
		<?php 
			if (!empty($templates)) {
				echo '<p>'.__('You can update existing template','fgallery').'</p>';
				echo '<select name="update_ex">';
				echo '<option value="-" selected="selected">--------</option>';
				foreach ($templates as $template) {
					echo '<option value="'.$template['id'].'" />'.$template['templ_title'].'</option>';
				}
				echo '</select>';
			}
		?><br />
		<label for="templ_description"><?php _e('Template Description','fgallery') ?></label><br />
		<textarea cols="70" rows="10" name="templ_description" id="templ_description"></textarea><br />
		<input type="submit" value="<?php _e('Save') ?>" />
		<input type="hidden" name="gall_id" value="<?php echo $gallery['gall_id'] ?>" />
		<input type="hidden" name="gall_type" value="<?php echo $gallery['gall_type'] ?>" />
		<?php wp_nonce_field('fgallery_templates', 'fgallery_templates_field')?>
	</form>
<?php 
}

function fgallery_render_load_template_form($gallery) {
	$templates = fgallery_get_templates($gallery['gall_type']);
?>
	<form action="<?php echo fgallery_get_load_settings_url($gallery['gall_id'], true) ?>" method="post" enctype="multipart/form-data">
		<?php 
			if (!empty($templates)) {
				echo '<h2>'.__('Choose template from the list','fgallery').'</h2>';
				foreach ($templates as $template) {
					echo '<div class="image_wrap" id="template_'.$template['id'].'">';
					echo '<div class="fgallery_image_actions"><input type="radio" name="template" value="'.$template['id'].'" /><br /><br />';
					echo '<a href="'.fgallery_get_template_delete_url($gallery['gall_id'], $template['id']).'" rel="template_'.$template['id'].'" title="'.__('Delete','fgallery').'" class="fgallery_action delete">'.__('Delete','fgallery').'</a></div>';
					echo '<div class="fgallery_image_info"><b>'.$template['templ_title'].'</b><br />';
					echo $template['templ_description'].'</div>';
					echo '</div>';
				}
			} else {
				echo '<p>'.sprintf(__('There are no templates for <b>%s</b> gallery type yet. Please create new or'),fgallery_get_flash_type($gallery)).'<p>';
			}
		?><br />
		<input type="submit" value="<?php _e('Load');?>" /><br /><br />
		<label for="settings_file">
			<?php _e('You can load template file from your local computer','fgallery');?>
		</label><br />
		<input type="hidden" name="gall_id" value="<?php echo $gallery['gall_id']?>" />
		<br />
		<input type="file" name="settings_file" id="settings_file" /> <br />
		<input type="submit" value="<?php _e('Upload');?>" />
		<?php echo sprintf(__('Note: You need to be sure that you load settings file for <b>%s</b> gallery type','fgallery'),fgallery_get_flash_type($gallery)); ?>
		<?php wp_nonce_field('fgallery_load_settings','fgallery_load_settings_field'); ?>
	</form>
<?php
}

if (isset($_REQUEST['gall_id']) && is_numeric($_REQUEST['gall_id'])) {
	$gallery = fgallery_get_album($_REQUEST['gall_id']);
} else {
	die('Invalid Gallery');
}

$main_output = 0;
global $wpdb;
if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'save':
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
					$wpdb->insert(TEMPLATES_TABLE, array('gall_type' => $gall_type, 'gall_settings' => serialize($settings), 'created' => date("Y-m-d"), 'templ_title' => $_POST['templ_title'], 'templ_description'=>$_POST['templ_description']));
					if ($wpdb->insert_id > 0) {
				?>
					<script type="text/javascript">
					/* <![CDATA[ */
					var win = window.dialogArguments || opener || parent || top;
					alert('<?php _e('Template was saved successfully','fgallery')?>');
					win.tb_remove();
					/* ]]> */
					</script>
					<?php }
					die();
				} elseif (isset($_POST['update_ex']) && is_numeric($_POST['update_ex'])) {
					$templ_id = $_POST['update_ex'];
					$to_update = array('gall_type' => $gall_type, 'gall_settings' => serialize($settings));
					if ($_POST['templ_description'] != '') {
						$templ_description = $_POST['templ_description'];
						$to_update['templ_description'] = $templ_description;
					}
					$wpdb->update(TEMPLATES_TABLE, $to_update, array('id'=>$templ_id));
					?>
					<script type="text/javascript">
					/* <![CDATA[ */
					var win = window.dialogArguments || opener || parent || top;
					alert('<?php _e('Template was saved successfully','fgallery')?>');
					win.tb_remove();
					/* ]]> */
					</script>
					<?php }
					die();
			} else {
				$main_output = 1; 
			}
		break;
		case 'load' :
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
						foreach ($to_store as $key=>$value) {
							$name = 'sc_'.$key.'__';
							foreach ($value as $key_2=>$value_2) {
								$attr_name = $name.$key_2;
								$new_value = (string)$value_2;
								$settings_array[$attr_name] = str_replace('0x','',$new_value);
							}
						}
						if (empty($settings_array)) {
							die('Invalid settings');
						} 
						$settings = fgallery_prepare_settings($settings_array);
					} else {
						die('Invalid template');
					}
				}
				if(fgallery_save_album_settings($gall_id, $settings)) {
					?>
					<script type="text/javascript">
					/* <![CDATA[ */
					var win = window.dialogArguments || opener || parent || top;
						win.tb_remove();
						win.location.reload();
					/* ]]> */
					</script>
				<?php }
			} else {
				$main_output = 2;
			}
		break;
		case 'delete':
			if(isset($_GET['templ_id']) && is_numeric($_GET['templ_id'])) {
				$templ_id = $_GET['templ_id'];
				$wpdb->query("DELETE FROM ".TEMPLATES_TABLE." WHERE `id` = ".$templ_id);
			}
			die();
		break;
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e('Insert Gallery', 'fgallery'); ?> &#8212; <?php _e('WordPress'); ?></title>
<?php
wp_enqueue_style( 'global' );
wp_enqueue_style( 'wp-admin' );
wp_enqueue_style( 'colors' );
wp_enqueue_style( 'fgallerycss' );
// Check callback name for 'media'
wp_enqueue_style( 'ie' );
wp_enqueue_script('fgalleryjs');
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
<div class="wrap">
<?php
	switch ($main_output) {
		case 1:
			fgallery_render_template_form($gallery);
		break;
		case 2:
			fgallery_render_load_template_form($gallery);
		break;
	}
?>
</div>
<?php
	do_action('admin_print_footer_scripts');
?>
</body>
</html>
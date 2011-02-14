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
	$images = fgallery_get_album_images($gall_id);
	$album = fgallery_get_album($gall_id);
	if (empty($album)) {
		$album['gall_width'] = 450;
		$album['gall_height'] = 385;
		$album['gall_bgcolor'] = "#ffffff";
		$album['gall_type'] = 0;
	}
	header("Content-type: text/xml");
	echo "<?xml version='1.0' encoding='UTF-8'?>";
	echo '<images>';
		$siteurl = get_option('siteurl').'/';
		$nofolder = '';
			foreach ($images as $image){
				if ($image['img_vs_folder'] == 2) {
					$rows = fgallery_get_album_images($gall_id, $image['img_id']);
					echo '<folder name="'.$image['img_caption'].'">';
					foreach ($rows as $row) {
						if (in_array($album['gall_type'],array(0,1,2,3))) {
							echo "<img file='".$siteurl.$row['img_path']."' title='".$row['img_caption']."' />"; 
						} else {
							echo "<img file='".$siteurl.$row['img_path']."' title='".$row['img_caption']."'><![CDATA[".$row['img_description']."]]></img>"; 
						}
					}
					echo "</folder>";
				} else {
					if (in_array($album['gall_type'],array(0,1,2,3))) {
							$nofolder .= "<img file='".$siteurl.$image['img_path']."' title='".$image['img_caption']."' />";
						} else {
							$nofolder .= "<img file='".$siteurl.$image['img_path']."' title='".$image['img_caption']."'><![CDATA[".$image['img_description']."]]></img>";
						}
				}
			}
			if ($nofolder !='') {
				$nofolder = '<folder name="'.$album['gall_name'].'">'.$nofolder.'</folder>';
				echo $nofolder;
			}
		echo '</images>';
} else {
    die();
}

die();

?>

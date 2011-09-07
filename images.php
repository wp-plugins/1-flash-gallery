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
		$album['gall_type'] = 3;
	}
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<images>';
		$siteurl = get_option('siteurl').'/';
                if (!defined('PREVIEW_PATH')) {
                    define('PREVIEW_PATH',$siteurl);
                }
		$nofolder = '';
			foreach ($images as $image){
				if ($image['img_vs_folder'] == 2) {
					$rows = fgallery_get_album_images($gall_id, $image['img_id']);
					if (count($rows) > 0) {
						echo '<folder name="'.mixed_to_utf8($image['img_caption']).'">';
						foreach ($rows as $row) {
                                                    $title = mixed_to_utf8($row['img_caption']);
                                                    $img_path = $row['img_path'];
                                                    $description = mixed_to_utf8($row['img_description']);
							switch ($album['gall_type']) {
								case 1: case 2: case 3:
									echo '<img file="'.PREVIEW_PATH.$img_path.'" file_b="'.$siteurl.$img_path.'" title="'.$title.'" url="'.$row['img_url'].'"><![CDATA['.$description.']]></img>'; 
								break;
								case 4: case 5: case 6: case 8: case 9:
									echo '<img file_b="'.$siteurl.$img_path.'" file="'.PREVIEW_PATH.$img_path.'" title="'.$title.'" url="'.$row['img_url'].'"><![CDATA['.$description.']]></img>'; 
								break;
								case 7:
                                                                    $array = unserialize($row['img_extra']);
                                                                    $img_type = $array['img_type'];
                                                                    if ($img_type == '') {
                                                                        $img_type = 'page';
                                                                    }
									echo '<img type="'.$img_type.'" align="fill" title="'.$row['img_caption'].'" file="'.$siteurl.$img_path.'" url="'.$row['img_url'].'" />';
								break;
								case 10:
									echo '<img file="'.PREVIEW_PATH.$img_path.'" file_b="'.$siteurl.$img_path.'" title="'.$title.'" link="'.$row['img_url'].'" target="_blank"><![CDATA['.$description.']]></img>';
								break;
							}
						}
						echo "</folder>";
					}
				} else {
                                    $title = mixed_to_utf8($image['img_caption']);
                                    $img_path = $image['img_path'];
                                    $description = mixed_to_utf8($image['img_description']);
                                    switch ($album['gall_type']) {
                                            case 1: case 2: case 3:
                                                    $nofolder .= '<img file_b="'.$siteurl.$img_path.'" file="'.PREVIEW_PATH.$img_path.'" title="'.$title.'" url="'.$image['img_url'].'"><![CDATA['.$description.']]></img>'; 
                                            break;
                                            case 4: case 5: case 6: case 8: case 9:
                                                    $nofolder .= '<img file_b="'.$siteurl.$img_path.'" file="'.PREVIEW_PATH.$img_path.'" title="'.$title.'" url="'.$image['img_url'].'"><![CDATA['.$description.']]></img>'; 
                                            break;
                                            case 7:
                                                $array = unserialize($image['img_extra']);
                                                $img_type = $array['img_type'];
                                                if ($img_type == '') {
                                                    $img_type = 'page';
                                                }
                                                    $nofolder .= '<img type="'.$img_type.'" align="fill" title="'.$title.'" file="'.$siteurl.$img_path.'" url="'.$image['img_url'].'" />';
                                            break;
                                            case 10:
                                                    $nofolder .= '<img file="'.PREVIEW_PATH.$img_path.'" file_b="'.$siteurl.$img_path.'" title="'.$title.'" link="'.$image['img_url'].'" target="_blank"><![CDATA['.$description.']]></img>';
                                            break;
                                        }
				}
			}
			if ($nofolder !='') {
				$nofolder = '<folder name="'.mixed_to_utf8($album['gall_name']).'">'.$nofolder.'</folder>';
				echo $nofolder;
			}
		echo '</images>';
} else {
    die();
}
 
die();

?>

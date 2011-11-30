<?php

/* 
 * Returns images list xml file for given gallery
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Function to get images.xml file for flash
 */
function fgallery_images(){
    if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
    $gall_id = $_GET['gall_id'];
	$images = fgallery_get_album_images($gall_id);
	$album = fgallery_get_album($gall_id);
	if (empty($album)) {
            $album['gall_type'] = 3;
	}
	header("Content-type: text/xml");
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<response list="true">';
        $siteurl = get_option('siteurl').'/';
        if (!defined('PREVIEW_PATH')) {
            define('PREVIEW_PATH',$siteurl);
        }
        $nofolder = '';
        $js = 0;
        if (isset($_GET['js']) && is_numeric($_GET['js'])) {
            $js = 1;
            echo '<folder name="'.fgallery_sanitize_string($album['gall_name']).'">';
        }
        foreach ($images as $image){
            if ($image['img_vs_folder'] == 2) {
                    $rows = fgallery_get_album_images($gall_id, $image['img_id']);
                    if (count($rows) > 0) {
                            if (!$js) echo '<folder name="'.fgallery_sanitize_string($image['img_caption']).'">';
                            foreach ($rows as $row) {
                                $title = fgallery_sanitize_string($row['img_caption']);
                                $img_path = $row['img_path'];
                                $description = fgallery_sanitize_string($row['img_description']);
                                    switch ($album['gall_type']) {
                                            case 1: case 2: case 3:
                                            case 4: case 5: case 6:
                                            case 8: case 9: case 11: 
                                            case 12:
                                                    echo '<img id="'.$row['img_id'].'_'.$album['gall_id'].'" file="'.PREVIEW_PATH.$img_path.'" file_b="'.$siteurl.$img_path.'" title="'.$title.'" url="'.$row['img_url'].'"><![CDATA['.$description.']]></img>'; 
                                            break;
                                            case 7:
                                                $array = unserialize($row['img_extra']);
                                                $img_type = $array['img_type'];
                                                if ($img_type == '') {
                                                    $img_type = 'page';
                                                }
                                                echo '<img id="'.$row['img_id'].'_'.$album['gall_id'].'" type="'.$img_type.'" align="fill" title="'.$row['img_caption'].'" file="'.$siteurl.$img_path.'" url="'.$row['img_url'].'" />';
                                            break;
                                            case 10:
                                                echo '<img id="'.$row['img_id'].'_'.$album['gall_id'].'" file="'.PREVIEW_PATH.$img_path.'" file_b="'.$siteurl.$img_path.'" title="'.$title.'" link="'.$row['img_url'].'" target="_blank"><![CDATA['.$description.']]></img>';
                                            break;
											
                           
                                    }
                            }
                            if (!$js) echo "</folder>";
                    }
            } else {
                $title = fgallery_sanitize_string($image['img_caption']);
                $img_path = $image['img_path'];
                $description = fgallery_sanitize_string($image['img_description']);
                switch ($album['gall_type']) {
                        case 1: case 2: case 3:
                        case 4: case 5: case 6:
                        case 8: case 9: case 11: 
			case 12:
                                $nofolder .= '<img id="'.$image['img_id'].'_'.$album['gall_id'].'" file_b="'.$siteurl.$img_path.'" file="'.PREVIEW_PATH.$img_path.'" title="'.$title.'" url="'.$image['img_url'].'"><![CDATA['.$description.']]></img>'; 
                        break;
                        case 7:
                            $array = unserialize($image['img_extra']);
                            $img_type = $array['img_type'];
                            if ($img_type == '') {
                                $img_type = 'page';
                            }
                            $nofolder .= '<img id="'.$image['img_id'].'_'.$album['gall_id'].'" type="'.$img_type.'" align="fill" title="'.$title.'" file="'.$siteurl.$img_path.'" url="'.$image['img_url'].'" />';
                        break;
                        case 10:
                             $nofolder .= '<img id="'.$image['img_id'].'_'.$album['gall_id'].'" file="'.PREVIEW_PATH.$img_path.'" file_b="'.$siteurl.$img_path.'" title="'.$title.'" link="'.$image['img_url'].'" target="_blank"><![CDATA['.$description.']]></img>';
                        break;
						
                    }
            }
        }
        if ($nofolder !='') {
            if (!$js){
                $nofolder = '<folder name="'.fgallery_sanitize_string($album['gall_name']).'">'.$nofolder.'</folder>';
            } else {
                $nofolder .= '</folder>';
            }
            echo $nofolder;
        }
        echo '</response>';
    } else {
        die();
    }
    die();
}
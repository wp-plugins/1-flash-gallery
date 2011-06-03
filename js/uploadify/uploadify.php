<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/wp-load.php');
if (!FGALLERY_PHP4_MODE) {
	require_once('../../includes/thumbs/ThumbLib.inc.php');
}

function fgallery_resemple($fullpath) {
if (FGALLERY_PHP4_MODE) return false;
	$thumb = PhpThumbFactory::create($fullpath);
    $thumb->resize(1200, 1200);
    $thumb->save($fullpath); 
}

function fgallery_watermark($fullpath) {
if (FGALLERY_PHP4_MODE) return false;
	$wm_file = get_option('1_flash_gallery_watermark_path','');
	if ($wm_file == '') return false;
	$wm_place = get_option('1_flash_gallery_watermark_place','C');
	$thumb = PhpThumbFactory::create($fullpath);
	return $thumb->watermarkImageGD($fullpath,$fullpath,$wm_file,$wm_place);
}

global $wpdb;

$siteurl = get_option('siteurl');
$wm = get_option('1_flash_gallery_watermark_enabled', 0);
$extra_dir = str_replace($_SERVER['HTTP_HOST'],'',$siteurl);
$extra_dir = str_replace('http://','',$extra_dir);

define('EXTRA_DIR',$extra_dir.'/');
define('IMAGES_TABLE', $wpdb->prefix . "fgallery_images");

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT']. EXTRA_DIR. '/wp-content/uploads/fgallery/';
	$file_name = $_FILES['Filedata']['name'];
	$file_pathinfo = pathinfo($file_name);
	$filename = str_replace('.'.$file_pathinfo['extension'],'',$file_name);
	$file_name = date("YmdHis").'.'.$file_pathinfo['extension'];
	$targetFile =  str_replace('//','/',$targetPath) . $file_name;
	if (isset($_REQUEST['resize']) && is_numeric($_REQUEST['resize'])) {
		$resize = $_REQUEST['resize'];
	} else {
		$resize = 0;
	}

	if (isset($_REQUEST['img_parent']) && is_numeric($_REQUEST['img_parent'])) {
		$img_parent = $_REQUEST['img_parent'];
	} else {
		$img_parent = 0;
	}
	 $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	 $fileTypes  = str_replace(';','|',$fileTypes);
	 $typesArray = split('\|',$fileTypes);
	 $fileParts  = pathinfo($_FILES['Filedata']['name']);
	
	 if (in_array(strtolower($fileParts['extension']),$typesArray)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
		
		if (move_uploaded_file($tempFile,$targetFile)) {
			$file = getimagesize($targetFile);
			$img_type = $file['mime'];
			if ($resize) fgallery_resemple($targetFile);
			if ($wm) fgallery_watermark($targetFile);
			$img_path = str_replace($_SERVER['DOCUMENT_ROOT']. EXTRA_DIR,'',$targetFile);
			$img_size = filesize($targetFile);
			$wpdb->insert(IMAGES_TABLE, array('img_caption'=>$filename , 'img_vs_folder' => 0, 'img_parent' => $img_parent, 'img_date' => date("Y-m-d H:i:s"), 'img_type' => $img_type, 'img_size' => $img_size, 'img_path' => $img_path));
			$wpdb->print_error();
		}
           echo $img_path.'_'.$wpdb->insert_id;
	 } else {
	 	echo 'Invalid file type.';
	 }
}

?>
<?php
/* 
 * Here are all upload rutines
 */

// No direct access to this file
defined('ABSPATH') or die('Restricted access');

/**
 * Scan all files on FTP server route $link.$dir
 * @global string $filename (the results are collected here)
 * @param string $link
 * @param string $dir
 * @param integer $rec (include subfolders)
 */
function scan_ftp($link, $dir, $rec) { 
    global $filename; 
    $file_list = ftp_rawlist($link, $dir); 
    foreach($file_list as $file) { 
      list($acc, 
           $bloks, 
           $group, 
           $user, 
           $size, 
           $month, 
           $day, 
           $year, 
           $file) = preg_split("/[\s]+/", $file); 
      if(substr($file, 0, 1) == '.') continue; 
      if(substr($acc, 0, 1) == 'd') { 
        if ($rec) scan_ftp($link, $dir.$file."/", $rec); 
      } 
      if(substr($acc, 0, 1) == '-') { 
        $filename[] = $dir.$file; 
      } 
    } 
  } 
  
/**
* Getting files from ftp to server and inserting info into db
* @global type $wpdb
* @param array $array
* @param string $ftp_folder
* @param int $conn_id
* @param integer $resize
* @return int 
*/
function process_ftp($array, $ftp_folder, $conn_id, $resize) {
    global $wpdb;
    $wm = get_option('1_flash_gallery_watermark_enabled', 0);
    @set_time_limit(0);
    $error = '';
    $i = 0;
    if (!empty($array)) {
        foreach ($array as $key=>$value) {
            $file_parts = pathinfo($value);
            $targetFile = ABSPATH. 'wp-content/uploads/fgallery/'.date("YmdHsi").mt_rand(0,10).'.'.$file_parts['extension'];
            $handle = fopen($targetFile, 'w');
            if (@ftp_fget($conn_id, $handle, $value, FTP_BINARY, 0)) {
                $error .= "successfully written to $targetFile\n";
            } else {
                $error .= "There was a problem while downloading $value to $targetFile\n";
            }
            fclose($handle);
            $file = @getimagesize($targetFile);
            if (strpos($file['mime'],'image') === false) {
                $error .= "$targetFile is not an image file";
                unlink($targetFile);
            } else {
                $i++;
                if ($resize) fgallery_resemple($targetFile);
                if ($wm) fgallery_watermark($targetFile);
                $img_size = filesize($targetFile);
                $img_path = str_replace(ABSPATH,'',$targetFile);
                $wpdb->insert(IMAGES_TABLE, array('img_caption'=>$file_parts['basename'] ,
                                                  'img_vs_folder' => 0,
                                                  'img_parent' => $ftp_folder,
                                                  'img_date' => date("Y-m-d H:i:s"),
                                                  'img_type' => $file['mime'],
                                                  'img_size' => $img_size,
                                                  'img_path' => $img_path));
            }
        }
    }
    if ($error == '') {
        $error = $i;
    }
    return $error;
}

/*
 * Extract archive to temp dir
 * @param array $data (actually $_FILES)
 * @param int $folder
 * @param int $resize
 */
function fgallery_process_zip($data, $folder, $resize) {
    if($data['fgallery_zip']['tmp_name']['size'] && $data['fgallery_zip']['type'] == 'application/zip'){
        $tmp_fname = substr(md5($time), 0, 10) . '.zip';
        move_uploaded_file($data['fgallery_zip']['tmp_name'], FGALLERY_DIR . '/tmp/'. $tmp_fname);
        $filename = FGALLERY_DIR . '/tmp/'. $tmp_fname;
        if (!is_dir($filename. '_dir')) {
            mkdir($filename. '_dir');
        }
        WP_Filesystem();
        if(!unzip_file($filename, $filename. '_dir')){
			unlink($filename);
			continue;
        }   
        unlink($filename);
        $uploaded_a_file = fgallery_process_directory($filename. '_dir', FGALLERY_DIR, $folder, $resize);
        fgallery_delete_dir($filename. '_dir');
        unset($data['fgallery_zip']);
	return $uploaded_a_file;
    }
}

/**
 * Process temp directory and insert images to db
 * @global type $wpdb
 * @param string $dir
 * @param string $fgallery_dir
 * @param int $folder
 * @param int $resize
 * @return int 
 */
function fgallery_process_directory($dir, $fgallery_dir, $folder, $resize){     
    global $wpdb;
    $wm = get_option('1_flash_gallery_watermark_enabled', 0);
    $items = list_files($dir);
    $i = 0;
    foreach($items as $item){
        $file_name = str_replace($dir.'/', '',$item);
        $file_name = str_replace(' ','_',$file_name);
        $file_name = strtolower($file_name);
        $file_pathinfo = pathinfo($file_name);
        $filename = str_replace('.'.$file_pathinfo['extension'],'',$file_name);
        $file_name = date("YmdHis").rand(0,100).'.'.$file_pathinfo['extension'];
        $newitem = $fgallery_dir . '/'. $file_name;
        if (copy($item, $newitem)){
            $fileinfo = getimagesize($newitem);
            $img_type = $fileinfo['mime'];
            if ($resize) fgallery_resemple($newitem);
            if ($wm) fgallery_watermark($newitem);
            $img_path = str_replace(ABSPATH,'',$newitem);
            $img_size = filesize($newitem);
            $wpdb->insert(IMAGES_TABLE, array('img_date' => date("Y-m-d H:i:s"),
                                              'img_type' => $img_type,
                                              'img_size' => $img_size,
                                              'img_path' => $img_path,
                                              'img_vs_folder' => 0,
                                              'img_parent' => $folder));
            $i++;
        }
    }
    return $i;
}

/**
 * Scan directory upload
 */
function fgallery_scandir_upload(){
    global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_upload_files_field'], 'fgallery_upload_files')) {
        if ($_POST['directory'] != '') {
            $path = ABSPATH.$_POST['directory'];
            $i = 0;
            $files = glob("$path/*");
            foreach($files as $file) {
              if(!is_dir($file)) {
                    $info = @getimagesize($file);
                    if (strpos($info['mime'],'image') === false) {
                            continue;
                    }
                    $size = filesize($file);
                    $file_pathinfo = pathinfo($file);
                    $img_path = str_replace(ABSPATH, '', $file);
                    $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $file_pathinfo['basename'],
                                                      'img_vs_folder' => 0,
                                                      'img_parent' => $_POST['fgallery_url_folder'],
                                                      'img_date' => date("Y-m-d H:i:s"),
                                                      'img_path' => $img_path,
                                                      'img_size'=>$size,
                                                      'img_type' => $info['mime']));
                    $i++;
              } 
            }
            echo sprintf(__('%d image(s) were added to the list'),$i);
            die();
        } else {
            echo $_POST['directory'].' doesn\'t exist on your server';
            die();
        }
    }
    die();
}

/**
 * NextGEN import
 * @global type $wpdb 
 */
function fgallery_nextgen_upload(){
    global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_upload_files_field'], 'fgallery_upload_files')) {
        if (!empty($_POST['nextgen'])) {
            $ids = $_POST['nextgen'];
                foreach ($ids as $key=>$value) {
                    $img_path = $_POST['nextgenpath'][$value];
                    $size = filesize(ABSPATH.$img_path);
                    $fileinfo = getimagesize(ABSPATH.$img_path);
                    $img_type = $fileinfo['mime'];
                    $img_path = $img_path;
                    $caption = $_POST['nextgencaption'][$value];
                    $description = $_POST['nextgendescription'][$value];
                    $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $caption,
                                                      'img_description' => $description,
                                                      'img_vs_folder' => 0,
                                                      'img_parent' => $_POST['fgallery_url_folder'],
                                                      'img_date' => date("Y-m-d H:i:s"),
                                                      'img_path' => $img_path,
                                                      'img_size'=>$size,
                                                      'img_type' => $img_type));
                }
        }
    }
    echo sprintf(__('%d image(s) were added to the list'),count($ids));
    die();  
}

/**
 * ZIP upload
 */
function fgallery_zip_upload(){
    if (!empty($_FILES)) {
        $zip_folder = $_POST['zip_folder'];
        if (isset($_POST['resize']) && is_numeric($_POST['resize'])) {
                $resize = $_POST['resize'];
        } else {
                $resize = 0;
        }
        $i = fgallery_process_zip($_FILES, $zip_folder, $resize);
    }
    echo sprintf(__('%d image(s) were added to the list'),$i);
    die(); 
}

/**
 * URL Upload
 * @global type $wpdb 
 */
function fgallery_url_upload(){
    global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_upload_files_field'], 'fgallery_upload_files')) {
        if (!empty($_POST['fgallery_url'])) {
            if (isset($_POST['resize']) && is_numeric($_POST['resize'])) {
                    $resize = $_POST['resize'];
            } else {
                    $resize = 0;
            }
            $i = 0;
            @set_time_limit(0);
            foreach ($_POST['fgallery_url'] as $url) {
                $info = parse_url($url);
                $file_pathinfo = pathinfo($info['path']);
                $file_name = ABSPATH. 'wp-content/uploads/fgallery/'.date("YmdHis").rand(0,100).'.'.$file_pathinfo['extension'];
                $fp = fopen($file_name, 'w');
                fwrite($fp, file_get_contents($url));
                fclose($fp); 
                $fileinfo = getimagesize($file_name);
                $img_type = $fileinfo['mime'];
                if (strpos($img_type,'image') === false) {
                    echo sprintf(__('%s is not an image'),$url).'<br />';
                    unlink($file_name);
                    continue;
                }
                if ($resize) fgallery_resemple($file_name);
                $img_path = str_replace(ABSPATH,'',$file_name);
                $size = filesize($file_name);
                $wpdb->insert(IMAGES_TABLE, array('img_caption'=> __('Image from url', 'fgallery'),
                                                  'img_vs_folder' => 0,
                                                  'img_parent' => $_POST['fgallery_url_folder'],
                                                  'img_date' => date("Y-m-d H:i:s"),
                                                  'img_path' => $img_path,
                                                  'img_size'=>$size, 
                                                  'img_type' => $img_type));	
                $i++;
            }
        }
    }
    echo sprintf(__('%d image(s) were added to the list'),$i);
    die();  
}

/**
 * FTP Upload
 */
function fgallery_ftp_upload(){
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_upload_files_field'], 'fgallery_upload_files')) {
        if ($_POST['ftp_name'] != '') {
            $ftp_name = $_POST['ftp_name'];
            $ftp_user = $_POST['ftp_username'];
            $ftp_pass = $_POST['ftp_pass'];
            if (isset($_POST['resize']) && is_numeric($_POST['resize'])) {
                    $resize = $_POST['resize'];
            } else {
                    $resize = 0;
            }
            $connect = ftp_connect($ftp_name) or die("Couldn't connect to $ftp_name");
            if ($ftp_pass !='') {
                    if (!@ftp_login($connect, $ftp_user, $ftp_pass)) {
                            die("Couldn't login to $ftp_name");
                    }
            }
            $ftp_folder = $_POST['ftp_folder'];
            $rec = $_POST['fgallery_ftp_subfolders'];
            ftp_chdir($connect, $ftp_folder);
            // enabling passive mode
            ftp_pasv( $connect, true );
            // get contents of the current directory
            scan_ftp($connect, $ftp_folder, $rec); 
            $result = process_ftp($filename,$ftp_foldet,$connect,$resize);
            ftp_close($connect);
        }
        echo sprintf(__('%d image(s) were added to the list'),$i);
    }
    die();
}

/**
 * WordPress Media Library Import
 * @global type $wpdb 
 */
function fgallery_wpmedia_upload(){
    global $wpdb;
    if (!empty($_POST) && wp_verify_nonce($_POST['fgallery_upload_files_field'], 'fgallery_upload_files')) {
        if (!empty($_POST['media'])) {
            $ids = $_POST['media'];
            $uploads = wp_upload_dir();
            foreach ($ids as $key=>$value) {
                    $post = get_post($value);
                    $post_meta = get_post_meta($value, '_wp_attached_file', true);
                    $img_path = $uploads['basedir'].'/'.$post_meta;
                    $size = filesize($img_path);
                    $img_path = 'wp-content/uploads/'.$post_meta;
                    $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $post->post_title,
                                                      'img_vs_folder' => 0, 
                                                      'img_parent' => $_POST['fgallery_url_folder'],
                                                      'img_date' => $post->post_date, 
                                                      'img_path' => $img_path,
                                                      'img_size'=>$size,
                                                      'img_type'=> $post->post_mime_type));
            }
        }
    }
    echo sprintf(__('%d image(s) were added to the list'),count($ids));
    die();
}

function fgallery_uploadify_upload(){
    global $wpdb;
    $wm = get_option('1_flash_gallery_watermark_enabled', 0);
    if (!empty($_FILES)) {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = ABSPATH. '/wp-content/uploads/fgallery/';
        $file_name = $_FILES['Filedata']['name'];
        $file_pathinfo = pathinfo($file_name);
        $filename = str_replace('.'.$file_pathinfo['extension'],'',$file_name);
        $file_name = date("YmdHis").rand(0,99).'.'.$file_pathinfo['extension'];
        $targetFile =  str_replace('//','/',$targetPath) . $file_name;
        if (isset($_REQUEST['resize']) && is_numeric($_REQUEST['resize'])) {
           $resize = $_REQUEST['resize'];
        } else {
           $resize = 0;
        }
        if (isset($_REQUEST['parent']) && is_numeric($_REQUEST['parent'])) {
           $img_parent = $_REQUEST['parent'];
        } else {
           $img_parent = 0;
        }
        $fileTypes  = array('jpg','jpeg','gif','bmp','png');
        $fileParts  = pathinfo($_FILES['Filedata']['name']);
        if (in_array(strtolower($fileParts['extension']),$fileTypes)) {		
            if (move_uploaded_file($tempFile,$targetFile)) {
                $file = getimagesize($targetFile);
                $img_type = $file['mime'];
                if (strpos($img_type,'image') === false) {
                        unlink($targetFile);
                        echo 'Invalid file type.';
                        die();
                }
                if ($resize) fgallery_resemple($targetFile);
                if ($wm) fgallery_watermark($targetFile);
                $img_path = str_replace(ABSPATH,'',$targetFile);
                $img_size = filesize($targetFile);
                $wpdb->insert(IMAGES_TABLE, array('img_caption'=>$filename,
                                                  'img_vs_folder' => 0,
                                                  'img_parent' => $img_parent,
                                                  'img_date' => date("Y-m-d H:i:s"),
                                                  'img_type' => $img_type,
                                                  'img_size' => $img_size,
                                                  'img_path' => $img_path));
                $wpdb->print_error();
            }
            echo $img_path.'__'.$wpdb->insert_id;
        } else {
            echo 'Invalid file type.';
        }
    }
    die();
}
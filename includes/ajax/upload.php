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
    global $filenames; 
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
        $filenames[] = $dir.$file; 
      } 
    } 
  } 
  
  /**
   * Creates previews and watermark images
   * @param array $file_parts 
   */
  function fgallery_create_uploads($file_parts, $short_name, $resize = 1) {
      
    $wm = get_option('1_flash_gallery_watermark_enabled', 0);
    $preview = get_option('1_flash_gallery_preview_opt',0);
    $preview_size = get_option('1_flash_gallery_preview_size',200);
    $full_view_size = get_option('1_flash_gallery_display_view_size',1200);
    $save_original = get_option('1_flash_gallery_save_original',0);
    
    $targetFile = ABSPATH. 'wp-content/uploads/fgallery/'.$short_name.'_full.'.$file_parts['extension'];
    $targetFile_preview = ABSPATH. 'wp-content/uploads/fgallery/'.$short_name.'_prev.'.$file_parts['extension'];
    $targetFile_display = ABSPATH. 'wp-content/uploads/fgallery/'.$short_name.'.'.$file_parts['extension'];
    
    if ($preview) {
        fgallery_resemple($targetFile, $preview_size, $preview_size, $targetFile_preview);
        $img_preview_path = str_replace(ABSPATH,'',$targetFile_preview);    
    } else {
        $img_preview_path = '';
    }
    
    if ($resize){
        fgallery_resemple($targetFile, $full_view_size, $full_view_size, $targetFile_display);
    } else {
        $targetFile_display = $targetFile;
    }

    if ($wm) fgallery_watermark($targetFile_display);
    $img_size = filesize($targetFile_display);
    $img_path = str_replace(ABSPATH,'',$targetFile_display);
    if ($save_original) {
        $img_full_view_path = str_replace(ABSPATH,'',$targetFile);
    } else {
        $img_full_view_path = '';
        if ($resize) {
            @unlink($targetFile);
        }
    }
    
    $info = array(
        'img_size' => $img_size,
        'img_path' => $img_path,
        'img_full_view_path' => $img_full_view_path,
        'img_preview_path' => $img_preview_path,
    );
    
    return $info;
    
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
    
    @set_time_limit(0);
    $error = '';
    $i = 0;
    if (!empty($array)) {
        foreach ($array as $key=>$value) {
            $file_parts = pathinfo($value);
            $short_name = date("YmdHsi").mt_rand(0,10);
            $targetFile = ABSPATH. 'wp-content/uploads/fgallery/'.$short_name.'_full.'.$file_parts['extension'];
            $handle = fopen($targetFile, 'w');
            if (!@ftp_fget($conn_id, $handle, $value, FTP_BINARY, 0)) {
                $error .= "There was a problem while downloading $value to $targetFile\n";
            }
            fclose($handle);
            $file = @getimagesize($targetFile);
            if (strpos($file['mime'],'image') === false) {
                $error .= "$targetFile is not an image file";
                @unlink($targetFile);
            } else {
                $i++;
                
                $filename = fgallery_create_upload_name($file_parts['basename'], $file_parts, $targetFile);
                $info = fgallery_create_uploads($file_parts, $short_name, $resize);

                $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $filename,
                                                  'img_vs_folder' => 0,
                                                  'img_parent' => $ftp_folder,
                                                  'img_date' => date("Y-m-d H:i:s"),
                                                  'img_type' => $file['mime'],
                                                  'img_size' => $info['img_size'],
                                                  'img_path' => $info['img_path'],
                                                  'img_preview_path' => $info['img_preview_path'],
                                                  'img_full_view_path' => $info['img_full_view_path'],
                    ));
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
    if($data['fgallery_zip']['tmp_name']['size'] && strpos($data['fgallery_zip']['type'],'zip') > 0) {
        $tmp_fname = substr(md5($time), 0, 10) . '.zip';
        if (!move_uploaded_file($data['fgallery_zip']['tmp_name'], FGALLERY_DIR . '/tmp/'. $tmp_fname)){
            die("File couldn't be moved to temp directory");
        }
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
    $items = list_files($dir);
    $i = 0;
    foreach($items as $item){
        $file_name = str_replace($dir.'/', '',$item);
        $file_name = str_replace(' ','_',$file_name);
        $file_name = strtolower($file_name);
        $file_parts = pathinfo($file_name);
        $short_name = date("YmdHis").rand(0,100);
        $file_name = $short_name.'_full.'.$file_parts['extension'];
        $targetFile = $fgallery_dir . '/'. $file_name;
        if (copy($item, $targetFile)){
            $file = @getimagesize($targetFile);
            $img_type = $file['mime'];
            if (strpos($file['mime'],'image') === false) {
                @unlink($targetFile);
            }
            
            $filename = fgallery_create_upload_name($file_parts['basename'], $file_parts, $targetFile);
            $info = fgallery_create_uploads($file_parts, $short_name, $resize);
            
            $wpdb->insert(IMAGES_TABLE, array('img_caption' => $filename,
                                              'img_date' => date("Y-m-d H:i:s"),
                                              'img_type' => $img_type,
                                              'img_size' => $info['img_size'],
                                              'img_path' => $info['img_path'],
                                              'img_preview_path' => $info['img_preview_path'],
                                              'img_full_view_path' => $info['img_full_view_path'],
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
            $files = list_files($path);
            foreach($files as $file) {
              if(!is_dir($file)) {
                    $info = @getimagesize($file);
                    if (strpos($info['mime'],'image') === false) {
                            continue;
                    }
                    $size = filesize($file);
                    $file_pathinfo = pathinfo($file);
                    $img_path = str_replace(ABSPATH, '', $file);
                    $filename = fgallery_create_upload_name($file_pathinfo['basename'], $file_pathinfo, $file);
                    
                    if (isset($_POST['fgallery_url_folder']) && is_numeric($_POST['fgallery_url_folder'])) {
                        $img_parent = $_POST['fgallery_url_folder'];
                    } else {
                        $img_parent = 0;
                    }
                    
                    $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $filename,
                                                      'img_vs_folder' => 0,
                                                      'img_parent' => $img_parent,
                                                      'img_date' => date("Y-m-d H:i:s"),
                                                      'img_path' => $img_path,
                                                      'img_size'=> $size,
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
                    $img_preview_path = $_POST['nextgenpreviewpath'][$value];
                    $caption = $_POST['nextgencaption'][$value];
                    $description = $_POST['nextgendescription'][$value];
                    $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $caption,
                                                      'img_description' => $description,
                                                      'img_vs_folder' => 0,
                                                      'img_parent' => $_POST['fgallery_url_folder'],
                                                      'img_date' => date("Y-m-d H:i:s"),
                                                      'img_path' => $img_path,
                                                      'img_preview_path' => $img_preview_path,
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
        if (isset($_POST['zip_folder']) && is_numeric($_POST['zip_folder'])) {
            $zip_folder = $_POST['zip_folder'];
        } else {
            $zip_folder = 0;
        }
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
                $file_parts = pathinfo($info['path']);
                $short_name = date("YmdHis").rand(0,100);
                $targetFile = ABSPATH. 'wp-content/uploads/fgallery/'.$short_name.'_full.'.$file_parts['extension'];
                $fp = fopen($targetFile, 'w');
                fwrite($fp, file_get_contents($url));
                fclose($fp); 
                $file = @getimagesize($targetFile);
                $img_type = $file['mime'];
                if (strpos($img_type,'image') === false) {
                    echo sprintf(__('%s is not an image'),$url).'<br />';
                    @unlink($targetFile);
                    continue;
                }
                
                if (isset($_POST['fgallery_url_folder']) && is_numeric($_POST['fgallery_url_folder'])) {
                       $img_parent = $_POST['fgallery_url_folder'];
                    } else {
                       $img_parent = 0;
                    }
                    
                $filename = fgallery_create_upload_name($file_parts['basename'], $file_parts, $targetFile);
                
                $info = fgallery_create_uploads($file_parts, $short_name, $resize);
                
                $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $filename,
                                                  'img_vs_folder' => 0,
                                                  'img_parent' => $img_parent,
                                                  'img_date' => date("Y-m-d H:i:s"),
                                                  'img_path' => $info['img_path'],
                                                  'img_preview_path' => $info['img_preview_path'],
                                                  'img_full_view_path' => $info['img_full_view_path'],
                                                  'img_size'=> $info['img_size'], 
                                                  'img_type' => $img_type
                             ));	
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
    global $filenames;
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
            if (isset($_POST['fgallery_ftp_folder']) && is_numeric($_POST['fgallery_ftp_folder'])){
                $parent = $_POST['fgallery_ftp_folder'];
            } else {
                $parent = 0;
            }
            if (isset($_POST['fgallery_ftp_subfolders']) && is_numeric($_POST['fgallery_ftp_subfolders'])){
                $rec = $_POST['fgallery_ftp_subfolders'];
            } else {
                $rec = 0;
            }
            
            ftp_chdir($connect, $ftp_folder);
            // enabling passive mode
            ftp_pasv( $connect, true );
            // get contents of the current directory
            scan_ftp($connect, $ftp_folder, $rec); 
            
            $result = process_ftp($filenames,$parent,$connect,$resize);
            ftp_close($connect);
        }
        echo sprintf(__('%d image(s) were added to the list'),$result);
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
            foreach ($ids as $key=>$value) {
                    $post = get_post($value);
                    $post_meta = get_post_meta($value, '_wp_attached_file', true);
                    $uploads_dir = pathinfo($post_meta);
                    $img_full_view_path = 'wp-content/uploads/'.$post_meta;
                    
                    $img_meta = get_post_meta($value, '_wp_attachment_metadata', true);
                    $img_preview_path = 'wp-content/uploads/'.$uploads_dir['dirname'].'/'.$img_meta['sizes']['thumbnail']['file'];
                    $img_path = 'wp-content/uploads/'.$uploads_dir['dirname'].'/'.$img_meta['sizes']['large-feature']['file'];
                    if (!file_exists(ABSPATH.$img_path) || !is_file(ABSPATH.$img_path) || !is_readable(ABSPATH.$img_path)) {
                        $img_path = $img_full_view_path;
                    }
                    $size = filesize(ABSPATH.$img_path);
                    if (isset($_POST['fgallery_url_folder']) && is_numeric($_POST['fgallery_url_folder'])) {
                       $img_parent = $_POST['fgallery_url_folder'];
                    } else {
                       $img_parent = 0;
                    }
                    $wpdb->insert(IMAGES_TABLE, array('img_caption'=> $post->post_title,
                                                      'img_vs_folder' => 0, 
                                                      'img_parent' => $img_parent,
                                                      'img_date' => date("Y-m-d H:i:s"), 
                                                      'img_path' => $img_path,
                                                      'img_preview_path' => $img_preview_path,
                                                      'img_full_view_path' => $img_full_view_path,
                                                      'img_size'=> $size,
                                                      'img_type'=> $post->post_mime_type));
            }
        }
    }
    echo sprintf(__('%d image(s) were added to the list'),count($ids));
    die();
}

function fgallery_local_upload() {
    global $wpdb;
    
    include(FGALLERY_ABSPATH."/includes/lib/upload_class.php"); //classes is the map where the class file is stored
	
    $upload = new file_upload();

    $upload->upload_dir = FGALLERY_DIR.'/';
    $upload->extensions = array('.png', '.jpg', '.jpeg', '.bmp', '.gif'); // specify the allowed extensions here
    $upload->rename_file = true;

    if(!empty($_FILES)) {
            $upload->the_temp_file = $_FILES['userfile']['tmp_name'];
            $upload->the_file = $_FILES['userfile']['name'];
            $upload->http_error = $_FILES['userfile']['error'];
            $upload->do_filename_check = 'y'; // use this boolean to check for a valid filename
            $short_name = date("YmdHis").rand(0,99);
            if ($upload->upload($short_name.'_full')){
                    if (isset($_REQUEST['resize']) && is_numeric($_REQUEST['resize'])) {
                       $resize = $_REQUEST['resize'];
                    } else {
                       $resize = 0;
                    }
                    if (isset($_REQUEST['local_img_folder']) && is_numeric($_REQUEST['local_img_folder'])) {
                       $img_parent = $_REQUEST['local_img_folder'];
                    } else {
                       $img_parent = 0;
                    }
                    $file_name = $upload->upload_dir.$upload->file_copy;
                    $file_parts = pathinfo($file_name);
                    $file = getimagesize($file_name);
                    $img_type = $file['mime'];
                    
                    $info = fgallery_create_uploads($file_parts, $short_name, $resize);
                    $filename = fgallery_create_upload_name($upload->the_file, $file_parts, $upload->the_temp_file);

                    $wpdb->insert(IMAGES_TABLE, array('img_caption'=>$filename,
                                                  'img_vs_folder' => 0,
                                                  'img_parent' => $img_parent,
                                                  'img_date' => date("Y-m-d H:i:s"),
                                                  'img_type' => $img_type,
                                                  'img_size' => $info['img_size'],
                                                  'img_path' => $info['img_path'],
                                                  'img_preview_path' => $info['img_preview_path'],
                                                  'img_full_view_path' => $info['img_full_view_path'],
                                ));
                    //echo $wpdb->print_error();
                    echo '<div id="status">success</div>';
                    echo '<div id="message">'. $upload->file_copy .' '.__('Successfully Uploaded','fgallery').'</div>';
                    //return the upload file
                    echo '<div id="uploadedfile">'. $upload->file_copy .'</div>';

            } else {
                    echo '<div id="status">failed</div>';
                    echo '<div id="message">'. $upload->show_error_string() .'</div>';

            }
    }
    die();
}

/**
 * Uploadify uploader
 * @global type $wpdb 
 */
function fgallery_uploadify_upload(){
    global $wpdb;
    if (!empty($_FILES)) {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = ABSPATH. '/wp-content/uploads/fgallery/';
        $file_name = $_FILES['Filedata']['name'];
        $file_parts = pathinfo($file_name);
        $filename = fgallery_create_upload_name($file_name, $file_parts, $_FILES['Filedata']['tmp_name']);
        $short_name = date("YmdHis").rand(0,99);
        $targetFile =  str_replace('//','/',$targetPath).$short_name.'_full.'.$file_parts['extension'];
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
        if (in_array(strtolower($file_parts['extension']),$fileTypes)) {		
            if (move_uploaded_file($tempFile, $targetFile)) {
                $file = @getimagesize($targetFile);
                $img_type = $file['mime'];
                if (strpos($img_type,'image') === false) {
                        @unlink($targetFile);
                        echo 'Invalid file type.';
                        die();
                }
                
                $info = fgallery_create_uploads($file_parts, $short_name, $resize);
                
                $wpdb->insert(IMAGES_TABLE, array('img_caption'=>$filename,
                                                  'img_vs_folder' => 0,
                                                  'img_parent' => $img_parent,
                                                  'img_date' => date("Y-m-d H:i:s"),
                                                  'img_type' => $img_type,
                                                  'img_size' => $info['img_size'],
                                                  'img_path' => $info['img_path'],
                                                  'img_preview_path' => $info['img_preview_path'],
                                                  'img_full_view_path' => $info['img_full_view_path']
                            ));
                //$wpdb->print_error();
            }
            if ($info['img_preview_path']!='') {
                echo $info['img_preview_path'].'__'.$wpdb->insert_id;
            } else {
                echo $info['img_path'].'__'.$wpdb->insert_id;
            }
        } else {
            echo 'Invalid file type.';
        }
    }
    die();
}


function fgallery_handle_upload_preview($data, $files = array()){
    $img_preview_path = '';
    if (!empty($files)) {
        foreach ($files as $key=>$value) {
                if ($files[$key]['name'] == '') {
                    continue;
                }
                //any errors the server registered on uploading
                    $fileError = $files[$key]['error'];
                    if ($fileError > 0) 
                    {
                        echo $files[$key]['name'].'<br />';
                         switch ($fileError) {
                            case 1:
                             _e( 'FILE TOO LARGE THAN PHP INI ALLOWS','fgallery');
                            return;
                            case 2:
                             _e( 'FILE TOO LARGE THAN HTML FORM ALLOWS', 'fgallery');
                            return;
                            case 3:
                             _e( 'ERROR PARTIAL UPLOAD', 'fgallery' );
                            return;
                         }
                    }
                    $fileTemp = $files[$key]['tmp_name'];
                    $fileName = $files[$key]['name'];
                    $uploaded_file_type = wp_check_filetype(basename($fileName));

                     // Set an array containing a list of acceptable formats
                    $allowed_file_types = array('image/jpg','image/jpeg','image/gif','image/png','image/bmp');
                     // If the uploaded file is the right format
                    if(in_array($uploaded_file_type['type'], $allowed_file_types)) {
                        //lose any special characters in the filename
                        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);
                        $file_name = $data['img_path'];
                        if (defined('PATHINFO_FILENAME')) {
                            $file_name = pathinfo($file_name,PATHINFO_FILENAME);
                        } elseif (strstr($file_name, '.')) {
                            $file_name = substr($file_name,0,strrpos($file_name,'.'));
                        }
                        $fileName = $file_name.'_prev.'.$file_ext;
                        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
                        $uploadPath = FGALLERY_DIR.'/'.$fileName;
                        // if the file already exists - delete it
                        @unlink($uploadPath);
                        // copy new preview file
                        if(!move_uploaded_file($fileTemp, $uploadPath)) {
                                _e( 'ERROR MOVING FILE','fgallery');
                                return;
                        }
                        $img_preview_path = str_replace(ABSPATH,'',$uploadPath);
                    } 
        }
    }
    return $img_preview_path;
}

function fgallery_create_upload_name($name, $file_parts, $file) {
    $capt = get_option('1_flash_gallery_caption_type', 0);
    $filename = '';
    switch ($capt) {
        case 0: 
            $filename = str_replace('.'.$file_parts['extension'],'',$name);
            break;
        case 1:
            $image_meta = @wp_read_image_metadata($file);
            $filename = utf8_decode($image_meta['caption']);
            break;
        case 2:
            $filename = date("Y-m-d H:i:s");
            break;
        default:
            $filename = str_replace('.'.$file_parts['extension'],'',$name);
            break;
    }
    if ($filename == '') {
        $filename = str_replace('.'.$file_parts['extension'],'',$name);
    }
    return $filename;
}
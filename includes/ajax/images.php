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
        $js = 0;
        if (isset($_GET['js']) && is_numeric($_GET['js'])) {
            $js = 1;
        }
        header("Content-type: text/xml");
        fgallery_build_imagesXML($images, $album, $js);
        die();
    } else {
        die();
    }
    die();
}

/**
 * Function to build images.xml with DOMDocument
 * 
 * @param array $images
 * @param array $album
 * @param int (boolean) $js 
 */
function fgallery_build_imagesXML($images, $album, $js) {
    if (empty($album)) {
        $album['gall_type'] = 3;
    }
    // create XML document
    $imagesXML = fgallery_create_document();
    // create root element
    $xmlRoot = fgallery_create_element('response','',$imagesXML);
    fgallery_append_child($imagesXML, $xmlRoot);
    // create folders
    $nofolder = fgallery_create_folder($album['gall_name'], $imagesXML, $xmlRoot);
    if ($js) {
        $folder = fgallery_create_folder($album['gall_name'], $imagesXML, $xmlRoot);
    }
    foreach ($images as $image){
        if ($image['img_vs_folder'] == 2) {
                $rows = fgallery_get_album_images($album['gall_id'], $image['img_id']);
                if (count($rows) > 0) {
                        if (!$js) {
                            $folder = fgallery_create_folder($image['img_caption'], $imagesXML, $xmlRoot);
                        }
                        foreach ($rows as $row) {
                            fgallery_create_image($row, $album, $imagesXML, $folder);
                        }
                }
        } else {
            fgallery_create_image($image, $album, $imagesXML, $nofolder);
        }
    }
    /* Move nofolder element to the end of the list
     * because this is folder for all the images that are no in any album
     */
    $nofolder = fgallery_remove_child($xmlRoot, $nofolder);
    fgallery_append_child($xmlRoot, $nofolder);

    // output XML file
    fgallery_show_document($imagesXML);
}

/**
 * Function to get images.xml for each publication
 */
function fgallery_book_images() {
    if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
        $gall_id = $_GET['gall_id'];
        $album = fgallery_get_album($gall_id);
    } else {
        die('Invalid Gallery');
    }
    $images = fgallery_get_album_images($gall_id);
    header("Content-type: text/xml");
    fgallery_build_book_imagesXML($images, $album);
    die();
}

/**
 * Function to build images.xml with DOMDocument
 * 
 * @param array $images
 * @param array $album
 * @param ing (boolean) $js 
 */
function fgallery_build_book_imagesXML($images, $album) {
    if (empty($album)) {
        $album['gall_type'] = 13;
    }
    $settings = fgallery_get_album_settings($album['gall_id']);
    $coverType = @$settings['sc_book__coverType'];
    if ($coverType == '') {
        $coverType = 'hard';
    }
    // create new XML document
    $imagesXML = fgallery_create_document();
    // create root element
    $xmlRoot = fgallery_create_element('folders','',$imagesXML);
    fgallery_append_child($imagesXML, $xmlRoot);
    $coverTypeAttr = fgallery_create_attribute('coverType', $coverType, $xmlRoot, $imagesXML);
    // create folders 
    $nofolder = fgallery_create_folder($album['gall_name'], $imagesXML, $xmlRoot);
    $gall_name = fgallery_sanitize_string($album['gall_description']);
    $description = fgallery_create_element('description', $gall_name, $imagesXML);
    fgallery_append_child($nofolder, $description);
    foreach ($images as $image){
        if ($image['img_vs_folder'] == 2) {
                $rows = fgallery_get_album_images($album['gall_id'], $image['img_id']);
                if (count($rows) > 0) {
                        $folder = fgallery_create_folder($image['img_caption'], $imagesXML, $xmlRoot);
                        fgallery_append_child($folder, $description);
                        foreach ($rows as $row) {
                            fgallery_create_book_image($row, $album, $imagesXML, $folder);
                        }
                }
        } else {
            fgallery_create_book_image($image, $album, $imagesXML, $nofolder);
        }
    }

    /* Move nofolder element to the end of the list
     * because this is folder for all the images that are no in any album
     */
    $nofolder = fgallery_remove_child($xmlRoot, $nofolder);
    fgallery_append_child($xmlRoot, $nofolder);
    
    // output XML document
    fgallery_show_document($imagesXML);
            
}

/**
 * Function to get publications.xml file for flash
 */
function fgallery_publ() {
    if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
        $gall_id = $_GET['gall_id'];
        $album = fgallery_get_album($gall_id);
    } else {
        die('Invalid Gallery');
    }
    if ($album['gall_type'] == 13) {
        $items = fgallery_get_gallery_items($album['gall_id']);
        header("Content-type: text/xml");
        fgallery_build_publXML($items);
    } else {
        die('Wrong Gallery Type');
    }
    die();
}

/**
 * Function to build XML for publications
 * @param array $items 
 */
function fgallery_build_publXML($items) {
    // create new XML document
    $publXML = fgallery_create_document();
    // create root element
    $xmlRoot = fgallery_create_element('items', '', $publXML);
    fgallery_append_child($publXML, $xmlRoot);
    // create items
    if (!empty($items)) {
        foreach ($items as $item) {
            $publ = fgallery_create_element('item', '', $publXML);
            $images_url = fgallery_get_book_images_url($item['img_id']);
            $cover_url = fgallery_get_album_cover($item, 1);
            $title = fgallery_sanitize_string($item['gall_name']);
            $attributeSrc = fgallery_create_attribute('src', $images_url, $publ, $publXML);
            $attributeCover = fgallery_create_attribute('cover', $cover_url, $publ, $publXML);
            $attributeTitle = fgallery_create_attribute('title', $title, $publ, $publXML);
            fgallery_append_child($xmlRoot, $publ);
        }
    }
    // output XML document 
    fgallery_show_document($publXML);
    
}

/**
 * Function to add folder element to the images.xml
 * 
 * @param string $name
 * @param DOMDocument $imagesXML
 * @param DOMElement $xmlRoot
 * @return DOMElement folder 
 */
function fgallery_create_folder($name, &$imagesXML, &$xmlRoot) {
    $clean_name = fgallery_sanitize_string($name);
    $folder = fgallery_create_element('folder', '', $imagesXML);
    $attribute = fgallery_create_attribute('name', $clean_name, $folder, $imagesXML);
    fgallery_append_child($xmlRoot, $folder);
    return $folder;
}

/**
 * Adds image to folder in the list for gallery type 13
 * @param array $image
 * @param array $album
 * @param DOMDocument $imagesXML
 * @param DOMElement $folder
 * @return DOMElement 
 */
function fgallery_create_book_image($image, $album, &$imagesXML, &$folder) {
    $siteurl = get_option('siteurl').'/';
    $title = fgallery_sanitize_string($image['img_caption']);
    $img_path = $image['img_path'];
    if (!file_exists(ABSPATH.$img_path) || !is_file(ABSPATH.$img_path) || !is_readable(ABSPATH.$img_path)) {
        $img_path = $image['img_full_view_path'];
    }
    if ($image['img_preview_path'] != '') {
        $preview_path = $image['img_preview_path'];
    } else {
        $preview_path = $img_path;
    }
    $img_full_view = $image['img_full_view_path'];
    $description = fgallery_sanitize_string($image['img_description']);
    $array = unserialize($image['img_extra']);
    $img_type = $array['img_type'];
    if ($img_type == '') {
        $img_type = 'page';
    }
    $img_scale = $array['img_scaling'];
    if ($img_scale == '') {
        $img_scale = 'fit';
    }
    $img_align = $array['img_align'];
    if ($img_align == '') {
        $img_align = 'center';
    }
    $img_valign = $array['img_valign'];
    if ($img_valign == '') {
        $img_valign = 'middle';
    }
    $imageXML = fgallery_create_element('page', '', $imagesXML);
    // create attributes for page element
    $attributeType = fgallery_create_attribute('type',$img_type,$imageXML,$imagesXML);
    $attributeScaling = fgallery_create_attribute('scaling',$img_scale,$imageXML,$imagesXML);
    $attributeAlign = fgallery_create_attribute('align',$img_align,$imageXML,$imagesXML);
    $attributeValign = fgallery_create_attribute('valign',$img_valign,$imageXML,$imagesXML);
    // create elements for paths to images
    $big = fgallery_create_element('big', $siteurl.$img_full_view, $imagesXML);
    fgallery_append_child($imageXML, $big);
    $normal = fgallery_create_element('normal', $siteurl.$img_path, $imagesXML);
    fgallery_append_child($imageXML, $normal);
    $thumb = fgallery_create_element('thumb', $siteurl.$preview_path, $imagesXML);
    fgallery_append_child($imageXML, $thumb);
    // create image description
    $descriptionField = fgallery_create_element('description', '', $imagesXML);
    $descriptionCData = fgallery_create_cdata_section($description, $imagesXML);
    fgallery_append_child($descriptionField, $descriptionCData);
    fgallery_append_child($imageXML, $descriptionField);
    
    // Add image to folder element
    fgallery_append_child($folder, $imageXML);
    
    return $imageXML;
}

/**
 * Function to add image to images.xml
 * 
 * @param type $image
 * @param type $album
 * @param type $imagesXML
 * @param type $folder
 * @return type 
 */
function fgallery_create_image($image, $album, &$imagesXML, &$folder) {
    $siteurl = get_option('siteurl').'/';
    $title = fgallery_sanitize_string($image['img_caption']);
    $img_path = $image['img_path'];
    if (!file_exists(ABSPATH.$img_path) || !is_file(ABSPATH.$img_path) || !is_readable(ABSPATH.$img_path)) {
        $img_path = $image['img_full_view_path'];
    }
    if ($image['img_preview_path'] != '') {
        $preview_path = $image['img_preview_path'];
    } else {
        $preview_path = $img_path;
    }
    $description = fgallery_sanitize_string($image['img_description']);
    $array = unserialize($image['img_extra']);
    $img_type = $array['img_type'];
    if ($img_type == '') {
        $img_type = 'page';
    }
    // create new image element
    $imageXML = fgallery_create_element('img', '', $imagesXML);
    // create Id attribute
    $attributeId = fgallery_create_attribute('id',$image['img_id'].'_'.$album['gall_id'],$imageXML,$imagesXML);
    // create file attribute
    if ($album['gall_type'] != 7) {
        $file = $siteurl.$preview_path;
    } else {
        $file = $siteurl.$img_path;
    }
    $attributeFile = fgallery_create_attribute('file',$file,$imageXML,$imagesXML);
    if ($album['gall_type'] != 7) {
        // create file_b attribute
        $attributeFile_b = fgallery_create_attribute('file_b',$siteurl.$img_path,$imageXML,$imagesXML);
        // create CDATA section
        $cdata = fgallery_create_cdata_section($description, $imagesXML);
        fgallery_append_child($imageXML, $cdata);
    } else {
        // create type attribute
        $attributeType = fgallery_create_attribute('type',$img_type,$imageXML,$imagesXML);
        // create align attribute
        /*$attributeAlign = fgallery_create_attribute('align','fill',$imageXML,$imagesXML);*/
    }
    // create title attribute 
    $attributeTitle = fgallery_create_attribute('title',$title,$imageXML,$imagesXML);
    // create URL attribute 
    if ($album['gall_type'] == 10) {
        $attributeUrl = fgallery_create_attribute('link',$image['img_url'],$imageXML,$imagesXML);
    } else {
        $attributeUrl = fgallery_create_attribute('url',$image['img_url'],$imageXML,$imagesXML);
    }
    // append image to folder
    fgallery_append_child($folder, $imageXML);
    
    return $imageXML;
}


<?php
/**
 * show_image.php
 * 
 * Example utility file for dynamically displaying images
 * 
 * @author      1plugin.com
 * @version     1.0 (php 5 version)
 */

//reference thumbnail class
require_once('includes/thumbs/ThumbLib.inc.php');
if (!isset($_GET['filename'])) {
    exit;
}
if (isset($_GET['width']) && is_numeric($_GET['width'])) {
    $width = $_GET['width'];
} else {
    $width = 0;
}
if (isset($_GET['height']) && is_numeric($_GET['height'])) {
    $height = $_GET['height'];
} else {
    $height = 0;
}
    $thumb = PhpThumbFactory::create($_GET['filename'], array(), false, 1);
    $thumb->resize($width,$height);
    $thumb->show();
    $thumb->destruct();

?>
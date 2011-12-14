<?php
/**
 * Function to render Gallery View page.
 */
function fgallery_view_gallery(){
    if (isset($_GET['gall_id']) && is_numeric($_GET['gall_id'])) {
       $id = $_GET['gall_id'];
    } else {
       die();
    }
    if (isset($_GET['width']) && is_numeric($_GET['width'])) {
       $width = $_GET['width'];
    } else {
       $width = 450;
    }
    if (isset($_GET['height']) && is_numeric($_GET['height'])) {
       $height = $_GET['height'];
       $height = $height-30;
    } else {
       $height = 385;
    }
    if (isset($_GET['bg'])) {
       $bgcolor = $_GET['bg'];
    } else {
       $bgcolor = 'ffffff';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
    <head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
    <title><?php bloginfo('name') ?> &rsaquo; <?php _e('Insert Gallery','fgallery'); ?> &#8212; <?php _e('WordPress'); ?></title>
    <?php
    wp_enqueue_style( 'global' );
    // Check callback name for 'media'
    wp_enqueue_style( 'ie' );
    wp_enqueue_script( 'swfobject' );
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
        <?php 
            echo do_shortcode('[fgallery id='.$id.' w='.$width.' h='.$height.' bg='.$bgcolor.' t=0]');
                 do_action('admin_print_footer_scripts');
        ?>
    <script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>
    </body>
    </html>
<?php 
die();
} 
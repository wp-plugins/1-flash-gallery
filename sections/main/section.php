<?php

function fgallery_main_section(){
    
    require_once 'lib.php';
    require_once 'view.php';
    
    wp_enqueue_script('dashboard');
    wp_enqueue_style('wp-admin');
    wp_enqueue_style('admin-bar');
    wp_enqueue_style('wp-jquery-ui-dialog');

       
    $page = new fgallery_main_page();
    $widget = new fgallery_main_page_widgets;
    $w_data = new fgallery_main_page_widgets_lib;
    
    $page->add_block('id1', __('Welcome to the Photo Gallery Wordpress plugin'), $widget->intro(), 'L');
    $page->add_block('id2', 'Server Settings', $widget->info($w_data->get_sys_info()), 'L');
    $page->add_block('id3', 'Graphic Library', $widget->info($w_data->get_gd_info()), 'L');
    
    $page->add_block('dashboard_quick_press', __('Third Header'), $widget->contact_us($w_data->define_contact_form_url(), /*$w_data->get_captcha()*/false, $w_data->c_u_js()), 'R');
    $page->add_block('id4', __('News and Docs'), $widget->news($w_data->get_news()), 'R');
    
    
    
    
    $page->view();
    
    
    
}


function fgallery_post_contact_us(){

    require_once 'lib.php';
    require_once 'view.php';

    ini_set('max_execution_time', 60);
    
    $x = file_get_contents('php://input');
    $ch = curl_init();

    curl_setopt($ch,CURLOPT_URL,FGALLERY_CONTACT_FORM_URL);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$x);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    //execute post
    $result = curl_exec($ch);
    
    //close connection
    curl_close($ch);
  
    $error_div_start = stripos($result, '<div class="messages error">');
    $error_div = substr($result, $error_div_start);
    $error_div = substr($error_div, 0, stripos($error_div, '</div>') + 6);
    $error_div = str_replace('ul', 'ol', $error_div);
    
    if (strlen($error_div) != 0){
        echo $error_div;
    }else {
        echo '<a>Your message has been sent</a>';
    }
    
    $widget = new fgallery_main_page_widgets;
    $w_data = new fgallery_main_page_widgets_lib;
    
    echo $widget->contact_us($w_data->define_contact_form_url(), $w_data->get_captcha(), false, $_POST);
    die();
}

function fgallery_print_captcha(){
    
    require_once 'lib.php';
    echo fgallery_main_page_widgets_lib::get_captcha();
    die();
}

?>
<?php

//Config
define('FGALLERY_NEWS_SRC', 'http://1plugin.com/news.html');
define('FGALLERY_CONTACT_FORM_URL', 'http://1plugin.com/contact');



class fgallery_main_page_widgets_lib {
    
    
    function define_contact_form_url(){
    
        if (extension_loaded('curl')){
            return admin_url('admin-ajax.php?action=fgallery_post_contact_us');
        }else {
            return FGALLERY_CONTACT_FORM_URL;
        }
    }
    
    
    
    function get_news(){
        
        $news_src = FGALLERY_NEWS_SRC;
        
        if ($handle = @fopen($news_src, 'r')){
            $w  = '';
            while (($buffer = fgets($handle, 4096)) !== false) {
                $w .= $buffer;
            }
        }else {
            $w = '<div style="height:320px;width:100%;display:block;"><iframe src="'.$news_src.'" width="100%" height="300px" align="left"></iframe></div>';
        }
        
        return $w;
    }
    
    function get_captcha(){
        
    $contact_form_src = FGALLERY_CONTACT_FORM_URL;
    
        if ($handle = @fopen($contact_form_src, 'r')){
            
            ;
            
            $old_buffer = '';
            
            while (($buffer = fgets($handle, 1024)) !== false) {

                if (!isset($captcha)){
                
                    $search_part = $old_buffer.$buffer;
                    $old_buffer = $buffer;
                    
                    if (($pos = strpos($search_part, '<div class="captcha">')) !== false){
                        $captcha = substr($search_part, $pos);
                        $skip = true;
                    }
                
                }
                
                if (isset($captcha)){
                    
                    if ($skip == true){
                        $skip = false;
                    }else {
                        $captcha .= $buffer;
                    }
                    
                    if (($pos = strpos($captcha, 'name="form_id"')) !== false){
                        
                        $captcha = substr($captcha, 0, $pos).'value="contact_mail_page" id="edit-contact-mail-page" name="form_id">';
                        $captcha = str_replace('src="/image_captcha', 'src="http://1plugin.com/image_captcha', $captcha);
                        
                        break;
                   
                    }
                }
            }
            

            return $captcha;
        }
    }

    function c_u_js(){

        $script = '
<script lang="javascript">

var $j = jQuery.noConflict();

$j(".contact_us_form").bind("change", function(){
    $j.ajax({
        url:"'.admin_url('admin-ajax.php').'",
        type:"POST",
        data:"action=fgallery_print_captcha",
        success:function(results){
              $j("#contact_us_captcha").html(results);
        }
    });
    
    $j(".contact_us_form").unbind("change");
});
</script>';
        
        return $script;
        
    }


    function get_sys_info(){

        $sys_info = array();
       
        $sys_info[] = new fgallery_sys_info_param(__('Operating System'), PHP_OS);
        $sys_info[] = new fgallery_sys_info_param(__('PHP Version'), PHP_VERSION);
        $sys_info[] = new fgallery_sys_info_param(__('Server Software'), $_SERVER['SERVER_SOFTWARE']);
        $sys_info[] = new fgallery_sys_info_param(__('MySQL'), mysql_get_server_info());
        $sys_info[] = new fgallery_sys_info_param(__('PHP Safe Mode'), $this->on_off(ini_get('safe_mode')));
        $sys_info[] = new fgallery_sys_info_param(__('PHP Allow URL Fopen'), $this->on_off(ini_get('allow_url_fopen')));
        $sys_info[] = new fgallery_sys_info_param(__('PHP Memory Limit'), ini_get('memory_limit'));
        $sys_info[] = new fgallery_sys_info_param(__('PHP Max Post Size'), ini_get('post_max_size'));
        $sys_info[] = new fgallery_sys_info_param(__('PHP Max Upload Filesize'), ini_get('upload_max_filesize'));
        $sys_info[] = new fgallery_sys_info_param(__('PHP Max Script Execute Time'), ini_get('max_execution_time'));
        $sys_info[] = new fgallery_sys_info_param(__('PHP EXIF Support'), $this->yes_no(extension_loaded('exif')));
        $sys_info[] = new fgallery_sys_info_param(__('PHP EXIF Version'), phpversion('exif'));
        $sys_info[] = new fgallery_sys_info_param(__('PHP XML Support'), $this->yes_no(extension_loaded('libxml')));
        $sys_info[] = new fgallery_sys_info_param(__('PHP CURL Support'), $this->yes_no(extension_loaded('curl')));
        
        return $sys_info;
        
    }
    
    function get_gd_info(){
        
        $gd_info[] = new fgallery_sys_info_param(__('GD Support'), $this->yes_no(extension_loaded('gd')));
        
        $gd_array = gd_info();
        
        foreach ($gd_array as $name => $value){
            
            if (is_bool($value)) $value = $this->yes_no($value);
            $gd_info[] = new fgallery_sys_info_param(__($name), $value);
            
        }
        
        return $gd_info;
    }

    function on_off($bool){
        if (true) return 'On';
        else return 'Off';
    }
    
    function yes_no($bool){
        if (true) return 'Yes';
        else return 'No';
    }
    
}

class fgallery_sys_info_param {
    
    public $name;
    public $value;
    
    function __construct($name, $value){
        
        $this->name = $name;
        $this->value = $value;
        
    }
}


?>

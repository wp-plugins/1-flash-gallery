<?php


class fgallery_main_page{
    
    
    private $blocks;
    
    
    function __construct (){

        $this->blocks['L'] = array();
        $this->blocks['R'] = array();
        
        
    }
    
    
    
    function view(){

        echo '<h1>1 Flash Gallery v '.FGALLERY_VERSION.'</h1>
        <div class="wrap"><div>
        <div id="dashboard-widgets" class="metabox-holder">';
    //============================================        
        echo '<div id="postbox-container-1" class="postbox-container" style="width:50%;">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">';
        foreach ($this->blocks['L'] as $block){
            echo $block;
        }
        echo '</div></div>';
    //============================================
        echo '<div id="postbox-container-2" class="postbox-container" style="width:50%;">
        <div id="side-sortables" class="meta-box-sortables ui-sortable">';
        foreach ($this->blocks['R'] as $block){
            echo $block;
        }
        echo '</div></div>';
    //============================================    
        echo '</div></div></div>';
        
        
        
    }
    
    
    function add_block($id, $title, $content, $orient = 'L'){
    
        $this->blocks[$orient][] = '
            <div id="'.$id.'" class="postbox" style="display: block">
            <div class="handlediv" title="Click to toggle"><br></div>
            <h3 class="hndle">'.$title.'</h3>
            <div class="inside">'.$content.'</div>
            </div>
        ';
        
    }
    
    
    
    
}

class fgallery_main_page_widgets{
    
    function contact_us($url, $captcha = '', $js = '', $sd = array()){
        //quick-press
        $w='<form id="'.((extension_loaded('curl')?'quick-press':'contact_us_form')).'" method="post" action="'.$url.'">
        
        <h4 id="quick-post-title">
            <label for="edit-name"><nobr>Name&nbsp;:&nbsp;&nbsp;</nobr></label>
        </h4>
        <div>
            <input type="text" class="contact_us_form" id="edit-name" name="name" '.((isset($sd['name']))?'value="'.$sd['name'].'"':'').' placeholder="John Doe" maxlength="255"/>
        </div>
            
        <h4 id="quick-post-title">
            <label for="edit-mail"><nobr>E-mail&nbsp;:&nbsp;&nbsp;</nobr></label>
        </h4>
        <div>
            <input type="text" class="contact_us_form" id="edit-mail" name="mail" '.((isset($sd['mail']))?'value="'.$sd['mail'].'"':'').' placeholder="mail@example.com" maxlength="255"/>
        </div>
            
        <h4 id="quick-post-title">
            <label for="edit-subject"><nobr>Subject&nbsp;:&nbsp;&nbsp;</nobr></label>
        </h4>
        <div>
            <input type="text" class="contact_us_form" id="edit-subject" name="subject" '.((isset($sd['subject']))?'value="'.$sd['subject'].'"':'').' placeholder="Subject" maxlength="255"/>
        </div>            

        <h4 id="quick-post-title">
            <label for="edit-cid"><nobr>Category&nbsp;:&nbsp;&nbsp;</nobr></label>
        </h4>
        <div>
            <select id="edit-cid" class="contact_us_form" name="cid"><option value="0">- Please choose -</option><option value="3">Gallery Creation</option><option value="2">Images Uploading</option><option value="4">Inserting Gallery</option><option value="7">Other</option><option value="5">Plugin Bugs and Errors</option><option value="6">Upgarde to Pro</option></select>
        </div>             

        <h4 id="quick-post-title">
            <label for="edit-message">Message: </label>
        </h4>
        <div class="textarea-wrap">
        <textarea id="edit-message" class="contact_us_form" name="message" cols="15" rows="3" class="mceEditor" placeholder="
Type your meassage here. If you have any troubles, please describe it accurately. If it`s possibe send us:
(text of the errors, print screen of errors, link to the webpages where you try to insert gallery, and any other information)
        ">'.((isset($sd['message']))?$sd['message']:'').'</textarea>
        </div>';
        
        $w .= '<div id="contact_us_captcha">'.$captcha.'</div>';
        
        //$w .= '<input class="button-primary" type="submit" value="Send">';
        $w .= '</form>';
        
        $w .= $js;
       
        if (!extension_loaded('curl')){
            $w .= 'Attention! Your site doesn`t have CURL support and after submitting this form you will be redirected to another site. Please install the CURL extension for better functionallity.';
        }
        
        return $w;
        
    }
    
    
    function news($text){

        return $text;
        
    }
    
    function intro(){
        
        $w = '
<p>To create image gallery:
<ol>
<li>Upload images</li>
<li>Create gallery</li>
<li>Assign images to the gallery</li>
<li>Insert gallery in to the post or theme</li>
</ol>
If you have any questions, review <a target="_blank" href="http://1plugin.com/faq/90">Step-by-Step</a> manuall</p>';
        
        return $w;
        
    }
    
    
    function info($info){

        $w = '';
        
        foreach ($info as $line){
            
            $w .= '<p>'.$line->name.'&nbsp;:&nbsp;&nbsp;&nbsp;<a><strong>'.$line->value.'</strong></a></p>';
            
        }
        
        return $w;
    }

}

?>
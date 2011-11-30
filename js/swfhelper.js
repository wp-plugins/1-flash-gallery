function fgallery_gallery_image_text(id) {
    var array = id.split('_');
    img_id = parseInt(array[0]);
    gall_id = parseInt(array[1]);
    var data = {
            img_id : img_id,
            gall_id: gall_id,
            action : 'fgallery_get_image_text'
        };
    var selector = ".fgallery_"+gall_id+" .flash_text";
    var object = jQuery(selector);
    jQuery.get(FGallery.ajax_url,data,function(response){
        if (!object.hasClass('active')) {
            object.addClass('active');
        }
        object.find('.image_text').text(response);
    });

}

jQuery(document).ready(function(){ 
	if(jQuery('input[name=sc_slideshow__showLink]').is(':checked')==true){
	jQuery('.fgallery_message').html('<a  title="Photo Gallery Wordpress Plugin" href="http://1plugin.com" target="_blank">Photo Gallery Wordpress Plugin</a>').css('font-size','8px').css('line-height','12px').css('text-align','center');}
	   else {jQuery('.fgallery_message').css('display','none');
        jQuery('.addimages .page-numbers').live('click', function(e){
           e.preventDefault();
           jQuery('#TB_ajaxContent').load(jQuery(this).attr('href'));
       });
       }

        jQuery('.addimages .page-numbers').live('click', function(e){
           e.preventDefault();
           jQuery('#TB_ajaxContent').load(jQuery(this).attr('href'));
       });
       jQuery('#fgallery_insert').live('submit', function(){
           jQuery(this).ajaxSubmit({
               success : function(response){
                   var win = window.dialogArguments || opener || parent || top;
                    win.send_to_editor(response);
                    win.tb_remove();
               }
           });
           return false;
       });});
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
	  if (response != '') {
		  jQuery(selector).css({
			  'border' : "1px solid #E6E6E6",
			  'background' : "white url("+plugin_url+"/images/caption_bg.png) 0 0 repeat-x",
			  'padding' : "10px",
			  'margin' : "0px"});
		  jQuery(selector).slideUp("fast");
		  if (!object.hasClass('active')) {
				object.addClass('active');
				  }
		  object.find('.image_text').text(response);
		  jQuery(selector).slideDown("slow");
	  } else {
		  jQuery(selector).slideUp("fast");
		  }});
}
jQuery(document).ready(function(){ 
        jQuery('object, embed').css('outline','0');
        jQuery('.addimages .page-numbers').live('click', function(e){
           e.preventDefault();
           jQuery('#TB_ajaxContent').load(jQuery(this).attr('href'));
       });
       jQuery('#add_images_box .page-numbers, #add_images_box .add_images_folder').live('click', function(e){
           e.preventDefault();
           jQuery('#add_images_box').load(jQuery(this).attr('href'));
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
       });
 });
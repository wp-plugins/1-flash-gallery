jQuery(document).ready(function(){
	jQuery('.create_from_folder').click(function(e){	
		id = 'folder_'+jQuery(this).attr('rel');
		jQuery('#'+id).find('input[type=checkbox]').attr('checked','checked');
		jQuery('#image_action').val('-1');
		jQuery('#massedit_form').submit();
		e.preventDefault();
	});
        
	jQuery('.fgallery_action.delete').click(function(){	
		rel = jQuery(this).attr('rel');
                img_id = rel.replace("image_", "");
                img_id = img_id.replace("folder_", "");
		if(confirm('Are you sure you want to delete this item?')) {
                        var data = {
                                    id : img_id,
                                    action : 'fgallery_delete_image'
                                };
                        jQuery.post(ajaxurl,data,function(){
                            id = '#'+rel;
                            jQuery(id).remove();
                        });
		}
	});
	
	jQuery('.droppable').droppable({
                drop: function( event, ui ) {
                        //drop_url = jQuery(this).find('.folder_addimage').attr('href');
                        img = ui.draggable.attr('id');
                        img = parseInt(img.replace('image_',''));
                        folder_obj = jQuery(this);
                        folder = jQuery(this).attr('id');
                        folder = parseInt(folder.replace('folder_',''));
                        var data = {
                          img_id    : img,
                          folder_id : folder,
                          action    : 'fgallery_folder_addimage'
                        };
                        jQuery.post(ajaxurl,data,function(response){
                                ui.draggable.remove();
                                folder_obj.find('.folder_attr').html(response);
                                folder_obj.css('background', '#FFFFFF').css('border','1px solid #DFDFDF');
                        });
                }, 
                over : function ( event , ui) {
                        jQuery(this).css('background', '#FBF9EA').css('border','1px solid #D54321');
                },
                out : function ( event, ui) {
                        jQuery(this).css('background', '#FFFFFF').css('border','1px solid #DFDFDF');
                }
        });
	
        jQuery('.draggable').draggable({ 
                cursor: 'move', 
                helper: 'clone', 
                start : function ( event , ui) {
                        ui.helper.css('border','1px solid #D54321').css('background', '#FFFFFF'); 
                }			
        });
	
	jQuery('#images_sortby').change(function(){
            jQuery(this).parent().submit();
        });
});
var isDirty = false;

   window.onbeforeunload = function(){
      if(isDirty) {
             return false;
      }
   };

function save_image(object){                          
    var id = object.attr('rel');
    var caption = object.parent().find('#fgalleryImageCaption_'+id).val();
    var description = object.parent().find('#fgalleryImageDescription_'+id).val();
    var new_url = object.parent().find('#fgalleryImageURL_'+id).val();
    var new_type = object.parent().find('#fgalleryImageType_'+id).val();
    var new_text = object.parent().find('#fgalleryImageText_'+id).val();
    var album = object.parent().find('#fgalleryImageGall_'+id).val();
    var token = object.parent().find('#fgallery_edit_image_field_'+id).val();
    var data = {
                img_id : id,
                img_caption: caption,
                img_description: description,
                img_url : new_url,
                img_text: new_text,
                img_type: new_type,
                gall_id : album,
                nonce : token,
                action: 'fgallery_save_album_image'
            };
    jQuery.post(ajaxurl,data,function(response){
        if (response) {
            object.parent().css('backgroundColor', '#f00');
            object.parent().animate({ backgroundColor: "#ffffff" }, "8000");
            isDirty = false;
        } else {
            alert('Item '+caption+' not saved');
        }
    });
}

jQuery(document).ready(function(){
    jQuery(".rotate_image").live("click",function(){
        object = jQuery(this);
        img_id = jQuery(this).attr('id');
        img_id = img_id.replace("rotate_", "");
        var data = {
            id     : img_id,
            dir    : object.attr('rel'),
            action : 'fgallery_rotate_image'
        };
        jQuery.post(ajaxurl,data, function(response){
            if (response){
                var timestamp = new Date().getTime();
                img = object.parent().find('img');
                img.attr('src',img.attr('src') + '?' +timestamp );
            }
        });
    });
    
	jQuery( ".fgallery_list" ).sortable({ 
		items: 'li',
		cursor: 'move', 
		forcePlaceholderSize: true,
		update: function(event, ui) {
			album = jQuery(this).attr('id');
			jQuery('.fgallery_list li').each(function(index){
				img_id = jQuery(this).attr('id');
				img_id = img_id.replace("image_", "");
                                var data = {
                                            img_id : img_id,
                                            order  : index,
                                            gall_id: album,
                                            action : 'fgallery_sort_album_images'
                                        };
                                jQuery.post(ajaxurl,data);
			});
		}
	});
	jQuery('.image_remove').click(function(){	
                rel = jQuery(this).attr('rel');
                img_id = rel.replace("image_", "");
                gall_id = jQuery(this).attr('id');
                gall_id = gall_id.replace(rel+'_', "");
		if(confirm('Are you sure you want to remove this item?')) { 
                        var data = {
                                    img_id : img_id,
                                    gall_id: gall_id,
                                    action : 'fgallery_remove_album_image'
                                };
                        jQuery.post(ajaxurl,data,function(){
                            id = '#'+rel;
                            jQuery(id).remove();
                        });
		}
	});
	jQuery('.image_cover').click(function(){	
		rel = jQuery(this).attr('rel');
                img_id = rel.replace("image_", "");
                gall_id = jQuery(this).attr('id');
                gall_id = gall_id.replace('cover_'+img_id+'_', "");
                var data = {
                            img_id : img_id,
                            gall_id: gall_id,
                            action : 'fgallery_set_album_cover'
                        };
                jQuery.post(ajaxurl,data,function(response){
                    if(response == '1'){
                        id = '#'+rel;
                        jQuery('.fgallery_list').find('.album_cover').removeClass('album_cover');
                        jQuery(id).addClass('album_cover');
                    } else {
                        alert('Cover is not set');
                    }
                });
	});
        jQuery('.save_all_album_images').live('click',function(){
            jQuery('.save_album_image').each(function(){
                object = jQuery(this);
                save_image(object);
            });
            alert('Items were saved successfully');
        });
        
	jQuery('.save_album_image').live('click',function(e) {
		e.preventDefault();
		object = jQuery(this);
                save_image(object);
	});
        
       jQuery('#gallery_images_form input, #gallery_images_form select, #gallery_images_form textarea').change(function(){
          if(!isDirty){
             isDirty = true;
          }
       });
       jQuery("#folder_add_form").live("submit", function(){
           jQuery(this).ajaxSubmit({
               success:function(){
                            var win = window.dialogArguments || opener || parent || top;
                            win.tb_remove();
                            win.location.reload();
                       }
           });
           return false;
       });
       
       jQuery("#add_preview_form").live("submit", function(){
           jQuery(this).ajaxSubmit({
               success:function(resp){
                            var win = window.dialogArguments || opener || parent || top;
                            win.tb_remove();
                            id = parseInt(resp);
                            var timestamp = new Date().getTime();
                            selector = "#image_"+id+" .edit_gallery_urls img"
                            jQuery(selector).attr('src',jQuery(selector).attr('src')+'?'+timestamp);
                       }
           });
           return false;
       })
       
       jQuery("#fgallery_save_template").live("submit", function(){
           jQuery(this).ajaxSubmit({
               success:function(response){
                            var win = window.dialogArguments || opener || parent || top;
                            win.tb_remove();
                            alert(response);
                            win.location.reload();
                       }
           });
           return false;
       });
       
       jQuery("#fgallery_load_template").live("submit", function(){
           jQuery(this).ajaxSubmit({
               success:function(response){
                            if (response == '1') {
                                var win = window.dialogArguments || opener || parent || top;
                                win.tb_remove();
                                win.location.reload();
                            } else {
                                alert(response);
                            }
                       }
           });
           return false;
       });
       
       
       jQuery('.add_images_folder').live('click', function(e){
           e.preventDefault();
           jQuery('#TB_ajaxContent').load(jQuery(this).attr('href'));
       });
       
        jQuery('.fgallery_image_actions input[type="checkbox"]').live("change",function(){
            c = jQuery(this).attr('checked');
            if (c) {
                    state = 1;
            } else {
                    state = 0;
            }
            var data = {'value' : jQuery(this).val(),
                        'state' : state, 
                        'id'    : jQuery("#gall_id").val(),
                        'action': 'fgallery_add_image'};
            jQuery.post(ajaxurl,data);
        });
        
        jQuery("#add_images_form").live('submit',function(){
            jQuery(this).ajaxSubmit({
                success : function(response){
                  		var win = window.dialogArguments || opener || parent || top;
                                win.tb_remove();
                                win.location.href = response;  
                }
            });
            return false;
        })
});
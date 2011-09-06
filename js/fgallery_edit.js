jQuery(document).ready(function(){	
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
		var id = jQuery(this).attr('rel');
		caption = jQuery(this).parent().find('#fgalleryImageCaption_'+id).val();
		description = jQuery(this).parent().find('#fgalleryImageDescription_'+id).val();
		new_url = jQuery(this).parent().find('#fgalleryImageURL_'+id).val();
		new_type = jQuery(this).parent().find('#fgalleryImageType_'+id).val();
		album = jQuery(this).parent().find('#fgalleryImageGall_'+id).val();
		token = jQuery(this).parent().find('#fgallery_edit_image_field_'+id).val();
                var data = {
                            img_id : id,
                            img_caption: caption,
                            img_description: description,
                            img_url : new_url,
                            img_type: new_type,
                            gall_id : album,
                            nonce : token,
                            action: 'fgallery_save_album_image'
                        };
                jQuery.post(ajaxurl,data,function(response){
                    if (response == '0') alert(alert('Item '+caption+' not saved'));
                });
            });
            alert('Items were saved successfully');
        });
	jQuery('.save_album_image').live('click',function(e) {
		e.preventDefault();
		object = jQuery(this);
		var id = jQuery(this).attr('rel');
		caption = jQuery(this).parent().find('#fgalleryImageCaption_'+id).val();
		description = jQuery(this).parent().find('#fgalleryImageDescription_'+id).val();
		new_url = jQuery(this).parent().find('#fgalleryImageURL_'+id).val();
                new_type = jQuery(this).parent().find('#fgalleryImageType_'+id).val();
		album = jQuery(this).parent().find('#fgalleryImageGall_'+id).val();
		token = jQuery(this).parent().find('#fgallery_edit_image_field_'+id).val();
                var data = {
                            img_id : id,
                            img_caption: caption,
                            img_description: description,
                            img_url : new_url,
                            img_type: new_type,
                            gall_id : album,
                            nonce : token,
                            action: 'fgallery_save_album_image'
                        };
                jQuery.post(ajaxurl,data,function(response){
                    if (response == '1') {
                        object.parent().css('backgroundColor', '#f00');
                        object.parent().animate({ backgroundColor: "#ffffff" }, "8000");
                    } else {
                        alert(alert('Item '+caption+' not saved'));
                    }
                });
	});
	
});
jQuery(document).ready(function(){	
	jQuery( ".fgallery_list" ).sortable({ 
		items: 'li',
		cursor: 'move', 
		forcePlaceholderSize: true,
		update: function(event, ui) {
			album = jQuery(this).attr('id');
			sort_url = jQuery('.fgallery_list a').attr('href');
			jQuery('.fgallery_list li').each(function(index){
				img_id = jQuery(this).attr('id');
				img_id = img_id.replace("image_", "");
				jQuery.ajax({
					url : sort_url,
					type: "GET",
					data: ({id : img_id, gall_id : album, order: index})
				});
			});
		}
	});
	jQuery('.image_remove').click(function(e){	
		rel = jQuery(this).attr('rel');
		if(confirm('Are you sure you want to remove this item?')) {
			jQuery.ajax({
				url     : jQuery(this).attr('href'),
				success : function(){
					id = '#'+rel;
					jQuery(id).remove();
				}
			});
		}
		e.preventDefault();
	});
	jQuery('.image_cover').click(function(e){	
		rel = jQuery(this).attr('rel');
			jQuery.ajax({
				url     : jQuery(this).attr('href'),
				success : function(){
					id = '#'+rel;
					jQuery('.fgallery_list').find('.album_cover').removeClass('album_cover');
					jQuery(id).addClass('album_cover');
				}
			});
		e.preventDefault();
	});
});
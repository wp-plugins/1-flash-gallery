jQuery(document).ready(function(){	

	var options = {
	    beforeSubmit: showLoader,
		success:  cleanFields
	}
	
	function showLoader() {
		jQuery('#upload_tabs').css('opacity','0.5');		
		jQuery(".ajax_loader").show();
	}
	
	function cleanFields(responseText, statusText, xhr, $form)  { 
		jQuery("#upload_tabs form")[0].reset();
		jQuery("#upload_tabs form")[1].reset();
		jQuery("#upload_tabs form")[2].reset();
		jQuery(".ajax_loader").hide();
		jQuery('#upload_tabs').css('opacity','1');
		jQuery('.input_fields').html(''); 
		alert(responseText);
	} 
		
	jQuery("#upload_tabs").tabs();
	jQuery("#upload_tabs form").ajaxForm(options);
	jQuery('#second_tab a').click(function(e){
		jQuery('#uploadify').uploadifyClearQueue();
		e.preventDefault();
	});
	jQuery('#second_tab button').click(function(e){
		jQuery('#uploadify').uploadifySettings('scriptData', {'img_parent' : jQuery("#uploadify_img_folder").val()});
		jQuery('#uploadify').uploadifyUpload();
		e.preventDefault();
	});
	jQuery('.add_url').click(function(){
		id = jQuery('.fgallery_url').length;
		jQuery('.input_fields').append('<div id="fgallery_url_wrap_'+id+'" class="fgallery_url_wrap"><input type="text" name="fgallery_url[]" id="fgallery_url_'+id+'" class="fgallery_url" onchange="show_img_from_url(this.value,'+id+')" /><img src="" id="fgallery_img_'+id+'" width="100"/><a class="delete_url" onclick="delete_url('+id+')" title="Delete url"></a></div>');
	});
		
});

function delete_img(id) {
	href = jQuery('#image_'+id).attr('rel');
		if(confirm('Are you sure you want to delete this item?')) {
			jQuery.ajax({
				url     : href,
				success : function(){
					jQuery('.image_'+id).remove();
				}
			});
		}
}

function show_img_from_url(val,id) {
	jQuery('#fgallery_img_'+id).attr('src',val);
}

function delete_url(id) {
	jQuery('#fgallery_url_wrap_'+id).remove();
}


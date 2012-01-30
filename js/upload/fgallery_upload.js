jQuery(document).ready(function(){	
         jQuery("#folder_add_form").live("submit", function(event){
           event.preventDefault();
           jQuery(this).ajaxSubmit({
               success:function(resp){
                            var win = window.dialogArguments || opener || parent || top;
                            win.tb_remove();
                            jQuery('.choose_folder').html(resp);
                       }
           });
       });
	var options1 = { 
		beforeSubmit: validateData,
                success:  cleanFields
        }; 

	var options = {
                beforeSubmit: showLoader,
		success:  cleanFields
	};
	
        jQuery('.fileUpload').fileUploader();
        jQuery("#upload_tabs").tabs();
	jQuery("#upload_tabs .upload").ajaxForm(options);
	//jQuery("#upload_tabs .facebook_confirm").ajaxForm();
	jQuery("#upload_tabs #url").ajaxForm(options1);
	jQuery('#second_tab a').click(function(e){
		jQuery('#uploadify').uploadifyClearQueue();
		e.preventDefault();
	});
	jQuery('#second_tab button').click(function(e){
		if (jQuery("#resize").attr('checked')) {
			res = 1;
		} else {
			res = 0;
		}
                var img_parent = jQuery("#uploadify_img_folder").val();
		jQuery('#uploadify').uploadifySettings('scriptData', {'resize' : res, 'parent' : img_parent});
                jQuery('#uploadify').uploadifySettings('script', uploadifyObj.upload_url);
		jQuery('#uploadify').uploadifyUpload();
		e.preventDefault();
	});
	jQuery('.add_url').click(function(){
		id = jQuery('.fgallery_url').length;
		jQuery('.input_fields').append('<div id="fgallery_url_wrap_'+id+'" class="fgallery_url_wrap"><input type="text" name="fgallery_url[]" id="fgallery_url_'+id+'" class="fgallery_url" onchange="show_img_from_url(this.value,'+id+')" /><img src="" id="fgallery_img_'+id+'" width="100"/><a class="delete_url" onclick="delete_url('+id+')" title="Delete url"></a></div>');
	});
        
        jQuery('#local_img_folder').live('change', function(){
           jQuery('.local_img_folder_field').val(jQuery(this).val()); 
        });
        
        jQuery('#local_resize').live('click', function(){
           if (jQuery(this).attr('checked') == 'checked') {
               jQuery('.local_resize_field').val(1);
           } else {
               jQuery('.local_resize_field').val(0);
           }
        });
        
        jQuery("#uploadify").uploadify({
            'uploader'       : uploadifyObj.fgallery_url+'/swf/uploadify.swf',
            'cancelImg'      : uploadifyObj.fgallery_url+'/images/cancel.png',
            'queueID'        : 'fileQueue',
            'fileDesc'       : 'Images',
            'folder'         : uploadifyObj.extradir+'wp-content/uploads/fgallery',
            'fileExt'        : '*.jpg;*.jpeg;*.gif;*.bmp;*.png',
            'auto'           : false,
            'multi'          : true,
            'buttonImg'      : uploadifyObj.fgallery_url+'/images/button.png',
            'removeCompleted': true,
            'onComplete'     : function(event, queueID, fileObj, response, data){
                                    if (response == "Invalid file type."){
                                        alert(response);
                                        exit;
                                    }
                                    var array = response.split("__");
                                    var id = "image_"+array[1];
                                    jQuery("#uploaded_images").append('\n\
                                        <div class="fgallery_album uploaded_image '+id+'">\n\
                                        <img src="'+uploadifyObj.cover_path+array[0]+'" alt="'+fileObj.name+'" />\n\
                                        <p>'+fileObj.name+'</p>\n\
                                        <a href="javascript:void(0)" rel="'+ajaxurl+'?action=fgallery_delete_image&id='+array[1]+'"\n\
                                         id="'+id+'" title="'+uploadifyObj.delete_text+'" class="fgallery_action delete" \n\
                                         onclick="delete_img('+array[1]+');">\n\
                                        '+uploadifyObj.delete_text+'</a></div>');
                               },
            'onAllComplete'  : function () {
				if(jQuery('#save_button').length <= 0){
					jQuery("#second_tab").append('<br clear="all" />\n\
                                            <a href="admin.php?page=fgallery_images&amp;folder='+jQuery('#uploadify_img_folder').val()+'"\n\
                                             class="fgallery_action" id="save_button"">'+uploadifyObj.save_text+'</a>');}; 
                                    
                               },
            'onError'        : function(event, queueID, fileObj, errorObj) {
                                     alert(errorObj.info);
                               }
        });
    });
		
function showLoader() {
        jQuery('#upload_tabs').css('opacity','0.5');		
        jQuery(".ajax_loader").show();
}

function cleanFields(responseText, statusText, xhr, $form)  { 
        jQuery("#upload_tabs form").each(function(index){
           jQuery("#upload_tabs form")[index].reset(); 
        });
        jQuery(".ajax_loader").hide();
        jQuery('#upload_tabs').css('opacity','1');
        jQuery('.input_fields').html(''); 
        alert(responseText);
} 

function validateData(formData, jqForm, options) {
        jQuery('#upload_tabs').css('opacity','0.5');		
        jQuery(".ajax_loader").show();
        var res = true;
        jQuery('#url .fgallery_url').each(function(){
            if(/^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(jQuery(this).val())) {
              res = true;
            } else {
              res = false;
              alert(jQuery(this).val()+' is not valid url');
              return false;
            }
        });
        return res;
}


function delete_img(id) {
    href = jQuery('#image_'+id).attr('rel');
    if(confirm('Are you sure you want to delete this item?')) {
            data = {id : id};
            jQuery.post(href, data,       
                   function(){
                            jQuery('.image_'+id).remove();
                    }
            );
    }
}

function show_img_from_url(val,id) {
	jQuery('#fgallery_img_'+id).attr('src',val);
}

function delete_url(id) {
	jQuery('#fgallery_url_wrap_'+id).remove();
}


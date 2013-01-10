
$(document).ready(function() { 
       //Slider Scale Photo
  var photo_width = $("#img_to_upload").width();
  var photo_height = $("#img_to_upload").height();
  var used = 0;
  
  $("#slider_scale_photo").slider({ animate: true, range: "min", value: 100, min: 1, max: 600, step: 0.01, 
  slide: function( event, ui ) {
	  $( "#slider_result_photo" ).html( ui.value);
	  
	   if(used == 0)
	   {
		   photo_width = $("#img_to_upload").width();
		   used = 1;
	   }
	  
	  $("#img_to_upload").width(photo_width / 100 * ui.value);
	  $("#img_to_upload").height(photo_height / 100 * ui.value);
  },
  change: function(event, ui) {
  $('#hdn_photo').attr('value', ui.value);
  }	
  });
    
    
    


    var photo_width;

    function set_things(responseText, statusText, xhr, $form){

//temporary hack: not accessing assetic for the value of the image path from here, placed a hidden field, holds the server path in twig template.
        
        $(".zoom_in").fadeIn(500, function(){$(".zoom_in").removeClass("hide");})
        
        var url = document.getElementById('hdn_serverpath').value + responseText.imageurl;
        $('#uploaded_photo').html("<img id='img_to_upload' src='"+url+"' class='preview'>");
    
        var uploaded_img_src = document.getElementById('img_to_upload');
        var uploaded_img_obj = new Image();
	 
        uploaded_img_obj.onload = function() {
		 
            photo_width =  document.getElementById('img_to_upload').width;
            photo_height = document.getElementById('img_to_upload').height;		 
            photo_width =  document.getElementById('img_to_upload').style.width = photo_width + "px";
		 
        };
        uploaded_img_obj.src = uploaded_img_src.src;
    }

    $('#user_file').live('change', function()
    { 
        var photo_file_name = $("#user_file").val();
        $("#inp_txt_file_name").val(photo_file_name);
        $("#play_area").removeClass("hide");
        $(".int_fitting_room").addClass("hide");
        //$("#play_area").append('<img src="loader.gif" alt="Uploading...."/>');
        $("#frmUserImage").ajaxForm(
        {
            target: '#uploaded_photo',
            success: set_things
        }
        ).submit();
    });
});


//------------ image resize & slide related ---------------------------------------------------------






        
        
        
//----------------------------------------------------------------------------------
function post_content_of_canvas(){
    //temporary hack: not accessing assetic value for the url, placed a hidden field, holds the server path in twig template.
    var entity_id = document.getElementById('hdn_entity_id').value;
    var img_update_url = document.getElementById('hdn_image_update_url').value;
    
    var data = document.getElementById('cnv_img_crop').toDataURL();
              $.post(img_update_url, {
                      imageData : data,
                      id : entity_id
              }, function(data) {
                      alert(data);
              });  
  		
}
function shift_to_canvas (){
    var canvas = document.getElementById('cnv_img_crop');
    var context = canvas.getContext('2d');
    var img = document.getElementById('img_to_upload');
    var img_hldr = document.getElementById('uploaded_photo');
    var x = img_hldr.offsetLeft;
    var y = img_hldr.offsetTop;
    var width = img.width;
    var height = img.height;
    var imageObj = new Image();

    imageObj.onload = function() {
        context.clearRect(0,0,364,505);
        context.drawImage(imageObj, x, y, width, height);
        setTimeout('post_content_of_canvas()','600');
    };
    imageObj.src = img.src;
	  
}
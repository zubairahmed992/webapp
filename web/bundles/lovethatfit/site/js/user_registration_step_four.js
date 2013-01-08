
		

//----------------------------------------------------------------------

$(document).ready(function() { 

//--------input file display hack -------------
$("#user_file").change(function (){
                    var photo_file_name = $("#user_file").val();
		$("#inp_txt_file_name").val(photo_file_name);
	});
//---------------------------------------------


var photo_width;

function set_things(){
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
$("#play_area").removeClass("hide");
$(".int_fitting_room").addClass("hide");
$("#play_area").append('<img src="loader.gif" alt="Uploading...."/>');
$("#frmUserImage").ajaxForm(
{
target: '#uploaded_photo',
success: set_things
}
).submit();
});
});


//----------------------------------------------------------------------------------



    $("#slider_wrap").mousedown(function (){
		alert(photo_width);
		$("#slider_wrap").css('background','#4d4d4d');
		$(this).css('background','url(images/bg_focus_grad.gif)');
	});


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
        
        
        
//----------------------------------------------------------------------------------



function save_me(){
		
		 var data = document.getElementById('cnv_img_crop').toDataURL();		
		$.post("process.php", {
			imageData : data
		}, function(data) {
			alert("image saved");
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
		  //alert(imageObj +"|"+ x +"|"+ y +"|"+ width +"|"+ height);
		  context.clearRect(0,0,364,505)
          context.drawImage(imageObj, x, y, width, height);
		  //alert(canvas.toDataURL());
		  setTimeout(save_me(),600);
      };
      imageObj.src = img.src;
	  
	   

	  
	}
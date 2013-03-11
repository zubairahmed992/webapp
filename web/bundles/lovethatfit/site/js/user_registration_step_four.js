
$(document).ready(function() { 
 
  var user_height = 66 * 8 / 4 * 3;
  var user_back = 15 * 8 / 4 * 3;
  //var left_hand
  //alert(user_back);
  
  $("#user_body_marks").css({height: user_height, width: user_back + 194 - 20});
  $("#top_mid_body").css({width: user_back});
  $("#adj_top_hldr").css({width: user_back + 194 - 20, marginTop: user_height / 7.27 - 10, height: $("#top_adj_marks").height() + 30});
  $("#top_adj_marks").css({width: user_back + 194 - 20, top:10});
  
  
 
 
       //Slider Scale Photo
  var photo_width = $("#img_to_upload").width();
  var photo_height = $("#img_to_upload").height();
  var used = 0;
  
  $("#slider_scale_photo").slider({ animate: true, range: "min", value: 100, min: 1, max: 200, step: 0.01, 
  slide: function( event, ui ) {
	  $( "#slider_result_photo" ).attr('value' , ui.value);
	  
	   if(used == 0)
	   {
		   photo_width = $("#img_to_upload").width();
		   used = 1;
	   }
	  
	  $("#img_to_upload").width(photo_width / 100 * ui.value);
	  $("#img_to_upload").height(photo_height / 100 * ui.value);
  },
  change: function(event, ui) {
       $( "#slider_result_photo" ).attr('value' , ui.value);
	  
	   if(used == 0)
	   {
		   photo_width = $("#img_to_upload").width();
		   used = 1;
	   }
	  
	  $("#img_to_upload").width(photo_width / 100 * ui.value);
	  $("#img_to_upload").height(photo_height / 100 * ui.value);
  //$('#hdn_photo').attr('value', ui.value);
  $( "#slider_result_photo" ).attr('value' , ui.value);
  }	
  });
    
$( "#slider_result_photo" ).change(function (){
  
  $("#slider_scale_photo").slider("value", $( "#slider_result_photo" ).attr("value") );
  
  //alert("asd");
  //$(".ui-slider-handle").trigger('slider:slide');
});

function chk_overall(){
            if(photo_width == null){
                //alert("not loaded yet");
                setTimeout(chk_overall, 200);
                return false;
            }else {
                set_things();
            }
        }

function call_settings(responseText, statusText, xhr, $form){
      //var wwwe = document.getElementById('img_to_upload').width;
        //var hhhe = document.getElementById('img_to_upload').height;
        
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

        chk_overall();
        
        
    
   //else{
     //       alert("ts else");
       // }
}


    var photo_width;

    function set_things(){

//temporary hack: not accessing assetic for the value of the image path from here, placed a hidden field, holds the server path in twig template.
        
        
        $(".uploading_in_progress").fadeOut(300).remove();
        $(".zoom_in").fadeIn(500, function(){$(".zoom_in").removeClass("hide");})
        $(".step_4_tip").fadeIn(50);
        $(".step_4 .reg_next_step").attr("value","Save Photo");
        document.getElementById("hdn_skip_flag").value="process";
        
                
    }

    $('#user_file').live('change', function()
    { 
        
        var photo_file_name = $("#user_file").val();
        $("#inp_txt_file_name").val(photo_file_name);
        $("#play_area").removeClass("hide");
        $(".int_fitting_room").addClass("hide");
        $("#play_area").prepend('<div class="uploading_in_progress"></div>');
        $(".input_file_hldr").css("display","none");
        $(".upload_again_hldr").css("display","block");
        $("#frmUserImage").ajaxForm(
        {
            target: '#uploaded_photo',
            success: call_settings
        }
        ).submit();
    });
    
    
    //--Gragable Top--//
    $("#top_adj_marks").draggable({handle: "#top_moveable", axis: "y", containment: "parent",
    start: function() {
        //$("#dummy_mark").addClass("put_me_top");
    },
    drag: function() {
    },
    stop: function() {
        //$("#dummy_mark").removeClass("put_me_top");
    }
});
    
    
});


//------------ image resize & slide related ---------------------------------------------------------



function post_content_of_canvas(){
    //temporary hack: not accessing assetic value for the url, placed a hidden field, holds the server path in twig template.
    var entity_id = document.getElementById('hdn_entity_id').value;
    var img_update_url = document.getElementById('hdn_image_update_url').value;
    
    var data = document.getElementById('cnv_img_crop').toDataURL();
              $.post(img_update_url, {
                      imageData : data,
                      id : entity_id
              }, function(data) {
                      //alert(data);
              });  
  		
}

function go_to_index(){
    
    window.location = document.getElementById("hdn_inner_site_index_url").value;
        //window.location = "../inner_site/index";
    }
    
function next_button_click()
{
    var hd_flag=document.getElementById("hdn_skip_flag").value;
    
    if (hd_flag=='skip'){
        go_to_index();
    }else{
        $(".step_4 .reg_next_step").attr("value","Uploading...");
        shift_to_canvas ();
        setTimeout(go_to_index,'3000');
    }
    
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


//$( "#draggable5" ).draggable({ containment: "parent" });

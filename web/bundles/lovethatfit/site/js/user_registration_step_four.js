
$(document).ready(function() { 
 
  var user_height = $("#user_height_frm_3").attr("value");
  var user_back = $("#user_back_frm_3").attr("value");

set_markings();


function set_markings(){
user_height = user_height * 8 / 4 * 3;  
user_back = user_back * 8 / 4 * 3;
  //var left_hand
  //alert(user_back);
  var hands_inside = 20;
  
  $("#user_body_marks").css({height: user_height, width: user_back + 194 - hands_inside});
  $("#top_mid_body").css({width: user_back});
  $("#adj_top_hldr").css({width: user_back + 194 - hands_inside, height: $("#top_adj_marks").height() + 30});
  $("#top_adj_marks").css({width: user_back + 194 - hands_inside, top: user_height / 7.27 - 10});

  $("#right_hand").css({height:user_height / 2.2});
  $("#left_hand").css({height:user_height / 2.2});
  
    
  $("#adj_belt_hldr").css({position: 'absolute', top: user_height / 2.2 - 40, height: $("#bottom_adj_marks").height() + 80});
  $("#bottom_adj_marks").css({top: 15});
  
}
 
 
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
        
        $(".reg_next_step2").css("display","block");
        $(".uploading_in_progress").fadeOut(300).remove();
        $(".zoom_in").fadeIn(500, function(){$(".zoom_in").removeClass("hide");})
        $(".step_4_tip").fadeIn(50);
        $(".reg_next_step2").attr("value","Save Photo");
        document.getElementById("hdn_skip_flag").value="process";
        
                
    }

    $('#user_file').live('change', function()
    { 
        $("body").addClass("remove_bg");
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
    $("#top_adj_marks").draggable({handle: "#top_moveable", axis: "y", containment: "#adj_top_hldr", scroll: false,
    create: function() {
        prod_top_pos = $("#top_adj_marks").css("top");
        $("#measurement_shoulder_height").attr('value', prod_top_pos);
        $("#adj_popout_top").css("top", prod_top_pos);
    },
    start: function() {
        $("#dummy_mark").addClass("put_me_top");
    },
    drag: function() {
        prod_top_pos = $("#top_adj_marks").css("top");
        $("#measurement_shoulder_height").attr('value', prod_top_pos);
        $("#adj_popout_top").css("top", prod_top_pos);
    },
    stop: function() {
        prod_top_pos = $("#top_adj_marks").css("top");
        $("#measurement_shoulder_height").attr('value', prod_top_pos);
        $("#adj_popout_top").css("top", prod_top_pos);
    }
    });
    
    //--Gragable Bottom--//
    $("#bottom_adj_marks").draggable({handle: "#bottom_moveable", axis: "y", containment: "parent",
    create: function() {
       prod_bottom_pos = $("#bottom_adj_marks").css("top");
       $("#measurement_waist_height").attr('value', prod_bottom_pos);
       $("#adj_popout_bottom").css("top", prod_bottom_pos);
    },
    start: function() {
        //$("#dummy_mark").addClass("put_me_top");
    },
    drag: function() {
       prod_bottom_pos = $("#bottom_adj_marks").css("top");
       $("#measurement_waist_height").attr('value', prod_bottom_pos);
       $("#adj_popout_bottom").css("top", prod_bottom_pos);
    },
    stop: function() {
       prod_bottom_pos = $("#bottom_adj_marks").css("top");
       $("#measurement_waist_height").attr('value', prod_bottom_pos);
       $("#adj_popout_bottom").css("top", prod_bottom_pos);
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
        
        var act= $("#frmUserMeasurement").attr('action');
        $("#frmUserMeasurement").ajaxSubmit({url: act, type: 'post'})
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

function validateStepFourImageName()
{
var extensions = new Array("jpg","jpeg","gif","png","bmp","png");
   
var image_file = document.getElementById('user_file').value;
var image_length = document.getElementById('user_file').length;

var pos = image_file.lastIndexOf('.') + 1;
var ext = image_file.substring(pos, image_length);
if(image_file!=" ")
{
var final_ext = ext.toLowerCase();
for (i = 0; i < extensions.length; i++)
{
if(extensions[i] == final_ext)
{
return false;
}
}
document.getElementById('error').style.display="block";
document.getElementById('play_area').style.display="none";
return false;
}
return true;
}    
//$( "#draggable5" ).draggable({ containment: "parent" });

var maxfilesize = 2097152;//2MB;
        $('#user_file').live("change", function () {
            $("#msg").text("");
            var tt = $(this).val();
            var size = this.files[0].size;
            $("#msg").append("Filesize: " + size + " bytes");
            $("#msg").append("<br>Filesize: " + Math.ceil(size / 1024) + " Kb");
            $("#msg").append("<br>Filesize: " + Math.ceil(size / 1024 / 1024) + " MB");

            if (size > maxfilesize) {
                document.getElementById('sizeerror').style.display="block";
                return false;
            }
            else {
               return true;
            }
        });


function give_zoom_pos(obj_slider_hldr, obj_slider ){
    start_mark_position = $("#adj_markers_hldr").offset().top;
    slider_position_hldr = obj_slider_hldr.offset().top;
    slider_curr_pos = obj_slider.css("top");
    
    slider_curr_pos = parseInt(slider_curr_pos.slice(0,-2));
    slider_curr_pos = slider_curr_pos + (slider_position_hldr - start_mark_position) + 5;
    obj_slider_hldr.find(".zoomed_img_inner" ).css("top", -(slider_curr_pos*2 + 36));
    
}

function fade_in_all(eles){
    for(incr = 0; incr < 3; incr++){
        eles[incr].fadeIn(200);
    }
}
function fade_in_func(ele_one){
    ele_one.fadeIn(200);
}


function set_vertical_step(){

    
    //$('.rotate_buts').animate({ opacity: 0, height: 0 }, 500, function() { $('.rotate_buts').css("display","none") });
    
    
    //$('.left_move').animate({ top: 20 }, 500, function() { });    
    //$('.right_move').animate({ top: 20 }, 500, function() { });
    
    //$('#rotate_move_box').fadeOut(300);
    
    $(".hiw_upload_photo").css("display","none");
    
    $("#stella_box").css("display","none");
    $("#pic_step_1").css("display","none");
    $("#pic_step_2").css("display","none");
    
    $(".next_step2").fadeOut(200, function(){$(".save_btn_4").fadeIn(200);});
    
        
    $("#adj_popout_top").fadeIn(500);
    $("#adj_popout_bottom").fadeIn(500);
    $("#adj_top_hldr").fadeIn(500);
    $("#adj_belt_hldr").fadeIn(500);
    $("#uploaded_photo").addClass("uploaded_photo");
    $("#dummy_mark").addClass("put_me_top");
    
    
    $(".step_4").hide(300);
    
    
    $("#step_three").removeClass("active");
    $("#step_one").removeClass("active");
    $("#step_two").addClass("active");
    $("#step_one").addClass("make_clickable");
    
    $("#adj_markers_hldr").fadeOut(500);
    
   
    
    $("#adj_markers_hldr #shoulder_mark_hldr .v_pos").css("top", parseInt($("#measurement_shoulder_height").val()));
    $("#adj_markers_hldr #bust_mark_hldr .v_pos").css("top", parseInt($("#measurement_bust_height").val()));
    $("#adj_markers_hldr #waist_mark_hldr .v_pos").css("top", parseInt($("#measurement_waist_height").val()));
    $("#adj_markers_hldr #hip_mark_hldr .v_pos").css("top", parseInt($("#measurement_hip_height").val()));
    
    
    $("#adj_markers_hldr #shoulder_mark_hldr .h_pos").css("top", $("#adj_markers_hldr #shoulder_mark_hldr .v_pos").css("top"));
    $("#adj_markers_hldr #bust_mark_hldr .h_pos").css("top", $("#adj_markers_hldr #bust_mark_hldr .v_pos").css("top"));
    $("#adj_markers_hldr #waist_mark_hldr .h_pos").css("top", $("#adj_markers_hldr #waist_mark_hldr .v_pos").css("top"));
    $("#adj_markers_hldr #hip_mark_hldr .h_pos").css("top", $("#adj_markers_hldr #hip_mark_hldr .v_pos").css("top"));
    
        
    
    $(".h_pos").fadeOut(200, function(){$(".v_pos").fadeIn(200, function(){$("#verti_shoulder").fadeIn(200);});});
    set_w_tips($("#verti_shoulder"));
}

function set_horizontal_step(){
    
    $("#step_three").addClass("active");
    $("#step_one").removeClass("active");
    $("#step_two").removeClass("active");

    
    //$("#rotate_move_box").fadeOut(200);
    
    $(".next_btn_4").fadeOut(200, function(){$(".save_btn_4").fadeIn(200);});
    
    $(".adj_popout_msg").fadeOut(200);
    $(".adj_popout_msg_verti").fadeOut(200);
    
    $(".v_pos").fadeOut(200, function(){$(".h_pos").fadeIn(200, function(){$("#shoulder_mark_hldr .adj_popout_msg").fadeIn(200);});});
    
    
    
    give_zoom_pos($("#shoulder_mark_hldr"), $("#shoulder_mark_v"));
    
    
    $("#uploaded_photo").addClass("uploaded_photo");
    
   
   var step_3_b = $("#frmUserMeasurement").attr('action');
        $("#frmUserMeasurement").ajaxSubmit({url: step_3_b, type: 'post'})
   console.log(croped_img_path);
}


$(document).ready(function() { 


submit_all_step_3 = false;
submit_step_3_first = false;
submit_step_3_second = false;


$(".left_move").click(function(){
    $("#uploaded_photo").css({left: '-=' + 1});
});
$(".top_move").click(function(){
    $("#uploaded_photo").css({top: '-=' + 1}); 
});
$(".bottom_move").click(function(){
    $("#uploaded_photo").css({top: '+=' + 1});
});
$(".right_move").click(function(){
    $("#uploaded_photo").css({left: '+=' + 1});
});



/////Step3 > step 1//////

$(".edt_link").click(function(){
    window.reload();
});


$(".next_step3").click(function(){
    submit_all_step_3 = false;
    submit_step_3_first = false;
    submit_step_3_second = true;
    set_horizontal_step();
    
});




$(".next_step2").click(function(){
    
    submit_all_step_3 = false;
    submit_step_3_first = true;
    submit_step_3_second = false;
    $('.rotate_buts').fadeIn();
});




function pre_step_1(){

    $("#adj_markers_hldr").fadeOut(500);
    $("#step_one").removeClass("make_clickable");
    $('.rotate_buts').animate({ opacity: 1, height: 47 }, 500, function() {$('.rotate_buts').css("display","block")});
    $(".top_move").fadeIn(500);
    $(".bottom_move").fadeIn(500);
    $(".next_btn_4").fadeIn(200, function(){$(".save_btn_4").fadeOut(200);});
    $("#adj_popout_top").fadeOut(500);
    $("#adj_popout_bottom").fadeOut(500);
    $("#adj_top_hldr").fadeOut(500);
    $("#adj_belt_hldr").fadeOut(500);
    $("#uploaded_photo").removeClass("uploaded_photo");
    $("#dummy_mark").removeClass("put_me_top");
    $(".step_4").show(300);
    $("#step_two").removeClass("active");
    $("#step_three").removeClass("active");
    $("#step_one").addClass("active");
    
    $("#stella_box").css("display","none");
    $("#pic_step_1").css("display","none");
    $("#pic_step_2").css("display","none");
    $("#pic_step_3").css("display","none");
    
    $(".hiw_step_2").css("display","none");
    
    $(".hiw_step_1").fadeIn(200);
    
    $("#rotate_move_box").fadeIn(100);
    $('.left_move').animate({ top: 46 }, 500, function() { });    
    $('.right_move').animate({ top: 46 }, 500, function() { });
    $('#rotate_move_box').animate({ height: 190 }, 500, function() { });

}

$("#step_one_a").mousedown(function(){
    pre_step_1();
});

/////Step3 > step 1//////



var req_deg = 0;
function rotate_me_to(rotate_side, deg_to_rotate){
    
if(rotate_side == "cw" || rotate_side == "acw"){

if(rotate_side == "cw"){
    req_deg += deg_to_rotate; 
 }
 if(rotate_side == "acw"){
    req_deg -= deg_to_rotate; 
 }
    $('#img_to_upload').css({
     '-moz-transform':'rotate(' + req_deg + 'deg)',
     '-webkit-transform':'rotate(' + req_deg + 'deg)',
     '-o-transform':'rotate(' + req_deg + 'deg)',
     '-ms-transform':'rotate(' + req_deg + 'deg)',
     'transform':'rotate(' + req_deg + 'deg)'
    });
   
}
if(rotate_side == "just_shift"){

   var my_x = $('#uploaded_photo').css("left");
   var my_y = $('#uploaded_photo').css("top");
   var my_w = $('#img_to_upload').css("width");
   var my_h = $('#img_to_upload').css("height");
  
   var final_my_x = my_x.slice(0,-2);
   var final_my_y = my_y.slice(0,-2);
   var final_my_w = my_w.slice(0,-2);
   var final_my_h = my_h.slice(0,-2);
   
   parseInt(final_my_x);
   parseInt(final_my_y);
   parseInt(final_my_w);
   parseInt(final_my_h);
   
   var calculated_x = final_my_x + final_my_x/2;
   var calculated_y = final_my_y + final_my_y/2;
   

shift_to_canvas(req_deg,calculated_x,calculated_y);

}


}

$('.img_cw').click(function(){
    rotate_me_to("cw", 90);
});

$('.img_acw').click(function(){
    rotate_me_to("acw", 90);
});
$('.img_cw_min').click(function(){
    rotate_me_to("cw", 0.2);
});

$('.img_acw_min').click(function(){
    rotate_me_to("acw", 0.2);
});
$('.reg_next_step2').click(function(){
   $(".action_buts_bar").hide();
    //submit_all_step_3 = false;
    submit_step_3_first = true;
    //submit_step_3_second = false;
   rotate_me_to("just_shift");
});
$('.next_step2').click(function(){
   rotate_me_to("just_shift");
   //$(".action_buts_bar").hide();

    $(".hiw_step_1").fadeOut(200);
    $(".hiw_step_3").fadeOut(200);
    $(".hiw_step_2").fadeIn(200);
   
});

$('.next_step3').click(function(){
   rotate_me_to("just_shift");
   //$(".action_buts_bar").hide();

    $(".hiw_step_2").fadeOut(200);
    $(".hiw_step_3").fadeIn(200);
   
});



  var user_height = $("#user_height_frm_3").attr("value");
  var user_back = $("#user_back_frm_3").attr("value");

set_markings();


function set_markings(){
user_height = user_height * 8 / 4 * 3;
user_back = user_back * 8 / 4 * 3;

marking_with = user_back + 194 - hands_inside;

  var hands_inside = 20;
  inch_ratio = 6;
  
  $(".center_mark").css({height: user_height + 2, left: marking_with/2});
  $(".height_bottom_mark").css("top", user_height + 17);
  $("#user_body_marks").css({height: user_height, width: user_back + 194 - hands_inside});
  
  $("#user_body_marks").css({display: "none"});
  
  $("#adj_markers_hldr").css({height: user_height, marginTop: 23});
  
  $("#top_mid_body").css({width: user_back});
  $("#adj_top_hldr").css({width: user_back + 194 - hands_inside, height: $("#top_adj_marks").height() + 30});
  $("#top_adj_marks").css({width: user_back + 194 - hands_inside});

  $("#right_hand").css({height:user_height / 2.2});
  $("#left_hand").css({height:user_height / 2.2});

  get_db_shoulder_hgt = $("#measurement_shoulder_height").attr('value');
  
  if(get_db_shoulder_hgt == 0){

     get_db_shoulder_hgt = user_height - 70;
  }else{
      get_db_shoulder_hgt = $("#measurement_shoulder_height").attr('value') * inch_ratio;
  }
  
  initial_top_pos = user_height - get_db_shoulder_hgt;
  
 
  
  $("#top_adj_marks").css("top", initial_top_pos - 10);




    
  $("#adj_belt_hldr").css({position: 'absolute', top: user_height / 2.2 - 40, height: $("#bottom_adj_marks").height() + 80});
  
  get_db_outseam = $("#measurement_outseam").attr('value');
  
   if(get_db_outseam == 0){

     get_db_outseam = user_height / 2.2 - 40;
  }else{
      get_db_outseam = $("#measurement_outseam").attr('value') * inch_ratio;
  }
  
  initial_btm_pos = user_height - get_db_outseam;
  
  curr_belt_hldr_pos = $("#adj_belt_hldr").css('top');
  
  curr_belt_hldr_pos = curr_belt_hldr_pos.slice(0,-2);
    
  final_btm_pos = (initial_btm_pos - curr_belt_hldr_pos) - 16;
  
  //final_btm_pos = final_btm_pos.slice(0,-2);   
    
  
  $("#bottom_adj_marks").css("top", final_btm_pos);

}
 
 


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
  $( "#slider_result_photo" ).attr('value' , ui.value);
  }	
  });
    

function set_zoom_slider_edit(){
    
  var photo_width_edit = 364;
  var photo_height_edit = 505;
  var used_edit = 0;
    
    $("#slider_scale_photo_edit").slider({ animate: true, range: "min", value: 100, min: 1, max: 200, step: 0.01, 
  slide: function( event, ui ) {
	  $( "#slider_result_photo_edit" ).attr('value' , ui.value);
	  
	   if(used_edit == 0)
	   {
		photo_width_edit = $("#img_to_upload").width();
		used_edit = 1;
	   }
	  
	  $("#img_to_upload").width(photo_width_edit / 100 * ui.value);
	  $("#img_to_upload").height(photo_height_edit / 100 * ui.value);
  },
  change: function(event, ui) {
       $( "#slider_result_photo_edit" ).attr('value' , ui.value);
	  
	   if(used_edit == 0)
	   {
		   photo_width_edit = $("#img_to_upload").width();
		   used_edit = 1;
	   }
	  
	  $("#img_to_upload").width(photo_width_edit / 100 * ui.value);
	  $("#img_to_upload").height(photo_height_edit / 100 * ui.value);
  $( "#slider_result_photo_edit" ).attr('value' , ui.value);
  }	
  });
}

    
    
$( "#slider_result_photo" ).change(function (){
  
  $("#slider_scale_photo").slider("value", $( "#slider_result_photo" ).attr("value") );

});

function chk_overall(){
            if(photo_width == null){
                setTimeout(chk_overall, 200);
                return false;
            }else {
                set_things();
            }
        }
$("#load_current_pic_clicked").click(function(){
    load_set_pre_img();
});


//---------- Image reload and edit postion --------//


function load_set_pre_img(){

$("body").addClass("remove_bg");
$('#uploaded_photo').html("<img id='img_to_upload' src='"+croped_img_path+"' class='preview pre_uploaded' width='364' height='505'>");

$(".hiw_upload_photo").css("display","none");
$(".hiw_step_2").css("display","none");
$(".hiw_step_3").css("display","none");
$(".hiw_step_1").fadeIn(200);

$("#play_area").removeClass("hide");
$("#how_it_works").removeClass("hide");
$(".int_fitting_room").addClass("hide");
$(".upload_again_hldr").css("display","block");
$(".action_buts_bar").fadeIn(500);

set_zoom_slider_edit();
set_things();
$(".zoom_in").hide();
$(".zoom_edit").fadeIn(500, function(){$(".zoom_edit").removeClass("hide");})


}


//////////////////////////////////////////////////////


croped_img_path = $("#hdn_user_cropped_image_url").attr('value');


var chk_no_img_path = false;

if(croped_img_path == "/webapp/web/"){
    chk_no_img_path = true;
}else{
    if(croped_img_path == "/")
    chk_no_img_path = true;
}

if(chk_no_img_path == false){
    load_set_pre_img();

}else{

    $("#measurement_shoulder_height").attr("value", 20);
    $("#measurement_bust_height").attr("value", 24);
    $("#measurement_waist_height").attr("value", 15);
    $("#measurement_hip_height").attr("value", 30);
    
    $("#measurement_shoulder_width").attr("value", 120);
    $("#measurement_bust_width").attr("value", 130);
    $("#measurement_waist_width").attr("value", 100);
    $("#measurement_hip_width").attr("value", 130);

    $("#load_current_pic").addClass("hide");
    $(".fitting_step").addClass("hide");
    $("#rotate_move_box").addClass("hide");
    $(".next_btn_4").css("display","none");
    
    $(".hiw_step_1").css("display","none");
    $(".hiw_step_2").css("display","none");
    $(".hiw_step_3").css("display","none");
    $(".hiw_upload_photo").fadeIn(200);
    
    $("#stella_box").css("display","none");
    $("#pic_step_1").css("display","none");
    $("#pic_step_2").css("display","none");
    
    
}




function call_settings(responseText, statusText, xhr, $form){
    
          
        $(".zoom_edit").hide();
        
   var url = document.getElementById('hdn_serverpath').value + responseText.imageurl;
   
   $("#img_path_paper").attr("value", url);
   
  $('#uploaded_photo').html("<img id='img_to_upload' src='"+url+"' class='preview'>");
        
        
        
        var uploaded_img_src = document.getElementById('img_to_upload');
        var uploaded_img_obj = new Image();
	 
        uploaded_img_obj.onload = function() {
            photo_width =  document.getElementById('img_to_upload').width;
            photo_height = document.getElementById('img_to_upload').height;		 
            photo_width =  document.getElementById('img_to_upload').style.width = photo_width + "px";
            
            //alert(uploaded_img_obj.width);
            //checking_bhai();
        };
        
        uploaded_img_obj.src = uploaded_img_src.src;
        chk_overall();
        
}


    var photo_width;

    function set_things(){

        $(".reg_next_step2").css("display","block");
        $(".uploading_in_progress").fadeOut(300).remove();
        
        //$(".zoom_in").fadeIn(500, function(){$(".zoom_in").removeClass("hide");})
        
        $(".step_4_tip").fadeIn(50);
        $(".reg_next_step2").attr("value","Save Photo");
        //document.getElementById("hdn_skip_flag").value="process";
    }

    $('#user_file').live('change', function()
    { 
        
        $("body").addClass("remove_bg");
        var photo_file_name = $("#user_file").val();
        $("#inp_txt_file_name").val(photo_file_name);
        $("#play_area").removeClass("hide");
        $("#how_it_works").removeClass("hide");
        $(".int_fitting_room").addClass("hide");
        $("#play_area").prepend('<div class="uploading_in_progress"></div>');
        $(".input_file_hldr").css("display","none");
        $(".upload_again_hldr").css("display","block");
        $(".action_buts_bar").fadeIn(500);
        $(".fitting_step").removeClass("hide");
        $("#rotate_move_box").removeClass("hide");
        $(".next_btn_4").css("display","block");
        
        $(".hiw_step_1").css("display","none");
        $(".hiw_step_2").css("display","none");
        $(".hiw_upload_photo").fadeOut(200, function(){$(".hiw_step_1").fadeIn(200);});
        
        $("#stella_box").css("display","none");
        $("#pic_step_1").css("display","none");
        $("#pic_step_2").css("display","none");
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

    },
    start: function() {
       
    },
    drag: function() {
        prod_top_pos = $("#top_adj_marks").css("top");
        $("#adj_popout_top").css("top", prod_top_pos);
    },
    stop: function() {
    curr_top_adj_mark = $("#top_adj_marks").css('top');
    curr_top_moveable = $("#top_moveable").css('top');
    
    curr_top_adj_mark = parseInt(curr_top_adj_mark.slice(0,-2));
    curr_top_moveable = parseInt(curr_top_moveable.slice(0,-2));
    
    total_top_pos = curr_top_adj_mark + curr_top_moveable - 6;
    
    total_top_pos = total_top_pos/inch_ratio;
    
       
    total_top_pos = $("#user_height_frm_3").attr("value") - total_top_pos;
    
    
    
    $("#measurement_shoulder_height").attr('value', total_top_pos);
    }
    });
    
    //--Gragable Bottom--//
    
    $("#bottom_adj_marks").draggable({handle: "#bottom_moveable", axis: "y", containment: "parent",
    create: function() {
              
       
    },
    start: function() {
        
    },
    drag: function() {
       
       prod_bottom_pos = $("#bottom_adj_marks").css("top");
       $("#adj_popout_bottom").css("top", prod_bottom_pos);
    },
    stop: function() {
       
    
    curr_btm_hldr = $("#adj_belt_hldr").css('top');
    curr_btm_adj_mark = $("#bottom_adj_marks").css('top');
    curr_btm_moveable = $("#bottom_moveable").css('top');
    
    curr_btm_hldr = parseInt(curr_btm_hldr.slice(0,-2));
    curr_btm_adj_mark = parseInt(curr_btm_adj_mark.slice(0,-2));
    curr_btm_moveable = parseInt(curr_btm_moveable.slice(0,-2));
    
    total_btm_pos = curr_btm_hldr + curr_btm_adj_mark + curr_btm_moveable + 4;
    
    total_btm_pos = total_btm_pos/inch_ratio;
    
    total_btm_pos = $("#user_height_frm_3").attr("value") - total_btm_pos;
    
    $("#measurement_outseam").attr('value', total_btm_pos);
       
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
  
              var obj_url = jQuery.parseJSON( data );
               
              console.log("i am checked bhai");
                
                      if(obj_url.status === "check"){
                
                          if(submit_all_step_3 == true){
                              
                              console.log("Chalo bhai chalo");
                              go_to_index();
                          }
                          
                          if(submit_step_3_first == true){
                              console.log("First");
                              $("#hdn_user_cropped_image_url").attr("value", $("#hdn_serverpath").attr("value") + obj_url.url);
                              
                              $('.zoomed_img').html("<img class='zoomed_img_inner' src='"+ $("#hdn_serverpath").attr("value") + obj_url.url+"' width='728' height='1010'>");
                                set_vertical_step();
                                //set_horizontal_step();
                                //$("#adj_markers_hldr").fadeIn(500);
                                console.log("Setp two path variable: "+ obj_url.url );
                          }
                          
                          if(submit_step_3_second == true){
                              console.log("2nd 3nd");
                              $("#hdn_user_cropped_image_url").attr("value", $("#hdn_serverpath").attr("value") + obj_url.url);
                              $('.zoomed_img').html("<img class='zoomed_img_inner' src='"+ $("#hdn_serverpath").attr("value") + obj_url.url+"' width='728' height='1010'>");
                                set_horizontal_step();
                                console.log("Setp Three path variable: "+ obj_url.url );
                          }
                          
                      }
              });  
  		
}

function go_to_index(){
     
    window.location = document.getElementById("hdn_inner_site_index_url").value;
    }
    
function next_button_click()
{
    
    
        $(".reg_next_step2").attr("value","Uploading...");
        
        
        submit_all_step_3 = true;
        var act_final= $("#frmUserMeasurement").attr('action');
        $("#frmUserMeasurement").ajaxSubmit({url: act_final, type: 'post'})
        
        var step_3_final = $("#frmUserMeasurement_2").attr('action');
        $("#frmUserMeasurement_2").ajaxSubmit({url: step_3_final, type: 'post'})
        setTimeout(go_to_index,'3500');
    
    
}


function shift_to_canvas (rotate_deg, x1, y1){
    
  

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
        context.save();
        context.translate(x + width/2, y + 1 + height/2);
        
        context.rotate(0);
        context.rotate(rotate_deg * 0.0174532925);
        
        context.translate(-width/2, -height/2);
       
        context.drawImage(imageObj, 0, 0, width, height);
        context.restore();
        
 
        post_content_of_canvas();
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
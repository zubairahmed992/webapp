//--Gragable Top--//

function get_give_zoom_pos(get_obj_slider_hldr, get_obj_slider ){
    get_start_mark_position = $("#adj_markers_hldr").offset().top;
    //get_slider_position_hldr = get_obj_slider_hldr.offset().top;
    get_slider_curr_pos = get_obj_slider.offset().top;
    
    //get_slider_curr_pos = parseInt(get_slider_curr_pos.slice(0,-2));
    get_slider_curr_pos = get_slider_curr_pos - get_start_mark_position + 5;
    
    get_full_height = $("#user_height_frm_3").attr("value") * 6;
    
    console.log(get_full_height);
    console.log(get_slider_curr_pos);
    
    //get_slider_curr_pos = get_full_height - get_slider_curr_pos;
    
    
    return get_slider_curr_pos;
    
    //get_obj_slider_hldr.find(".zoomed_img_inner" ).css("top", -(get_slider_curr_pos*2 + 36));
    
    //console.log(- (Math.round(slider_curr_pos)*2));
    //console.log(-(slider_curr_pos*2 + 36));
    
}

function set_w_tips(verti_obj){
   
    slider_top_sholder = 0;
    slider_top_sholder = $("#shoulder_mark_v").css("top");
    $("#adj_markers_hldr #shoulder_mark_hldr .h_pos").css("top",slider_top_sholder);
    $("#adj_markers_hldr #shoulder_mark_hldr .adj_popout_tip").css("top",slider_top_sholder);
    $("#adj_markers_hldr #shoulder_mark_hldr .adj_popout_tip").css({top: "-=" + 29});
    
    $("#adj_markers_hldr #verti_shoulder").offset({top: parseInt($("#adj_markers_hldr #shoulder_mark_v").offset().top) - 29});
    
    console.log(get_give_zoom_pos($("#shoulder_mark_hldr"), $("#shoulder_mark_v")));
    
    $("#measurement_shoulder_height").val(slider_top_sholder);
    
    
    
    
    slider_top_bust = 0;
    slider_top_bust = $("#bust_mark_v").css("top");
    $("#adj_markers_hldr #bust_mark_hldr .h_pos").css("top",slider_top_bust);
    $("#adj_markers_hldr #bust_mark_hldr .adj_popout_tip").css("top",slider_top_bust);
    $("#adj_markers_hldr #bust_mark_hldr .adj_popout_tip").css({top: "-=" + 29});
    
    $("#adj_markers_hldr #verti_bust").offset({top: parseInt($("#adj_markers_hldr #bust_mark_v").offset().top) - 29});
    
    $("#measurement_bust_height").val(slider_top_bust);
    
    slider_top_waist = 0;
    slider_top_waist = parseInt($("#waist_mark_v").css("top"));
    $("#adj_markers_hldr #waist_mark_hldr .h_pos").css("top",slider_top_waist);
    $("#adj_markers_hldr #waist_mark_hldr .adj_popout_tip").css("top",slider_top_waist);
    $("#adj_markers_hldr #waist_mark_hldr .adj_popout_tip").css({top: "-=" + 29});
    
    $("#adj_markers_hldr #verti_waist").offset({top: parseInt($("#adj_markers_hldr #waist_mark_v").offset().top) - 29});
    
    $("#measurement_waist_height").val(slider_top_waist);
    
    slider_top_hip = 0;
    slider_top_hip = $("#hip_mark_v").css("top");
    $("#adj_markers_hldr #hip_mark_hldr .h_pos").css("top",slider_top_hip);
    $("#adj_markers_hldr #hip_mark_hldr .adj_popout_tip").css("top",slider_top_hip);
    $("#adj_markers_hldr #hip_mark_hldr .adj_popout_tip").css({top: "-=" + 29});
    
   $("#adj_markers_hldr #verti_hip").offset({top: parseInt($("#adj_markers_hldr #hip_mark_v").offset().top) - 29});
    
    $("#measurement_hip_height").val(slider_top_hip);
    
    
    $("#verti_shoulder").css("display","none");
    $("#verti_bust").css("display","none");
    $("#verti_waist").css("display","none");
    $("#verti_hip").css("display","none");

    verti_obj.css("display","block");
}


$(document).ready(function() {   
    
    var test_value_of = $("#adj_markers_hldr").offset();
    
    console.log(test_value_of.top);
    
    

    $("#shoulder_mark_h").click(function(){
        $("#adj_markers_hldr .adj_popout_tip").fadeOut(1, function(){$("#shoulder_mark_hldr .adj_popout_tip").fadeIn(2)});
        give_zoom_pos($("#shoulder_mark_hldr"), $("#shoulder_mark_v"));
    });
    $("#bust_mark_h").click(function(){
        $("#adj_markers_hldr .adj_popout_tip").fadeOut(1, function(){$("#bust_mark_hldr .adj_popout_tip").fadeIn(2)});
        give_zoom_pos($("#bust_mark_hldr"), $("#bust_mark_v"));
    });
    $("#waist_mark_h").click(function(){
        $("#adj_markers_hldr .adj_popout_tip").fadeOut(1, function(){$("#waist_mark_hldr .adj_popout_tip").fadeIn(2)});
        give_zoom_pos($("#waist_mark_hldr"), $("#waist_mark_v"));
    });
    $("#hip_mark_h").click(function(){
        $("#adj_markers_hldr .adj_popout_tip").fadeOut(1, function(){$("#hip_mark_hldr .adj_popout_tip").fadeIn(2)});
        give_zoom_pos($("#hip_mark_hldr"), $("#hip_mark_v"));
    });
    
  $("#shoulder_mark_v").mousedown(function(){
      set_w_tips($("#verti_shoulder"));
      set_w_tips($("#verti_shoulder"));
    });
    $("#bust_mark_v").mousedown(function(){
        set_w_tips($("#verti_bust"));
        set_w_tips($("#verti_bust"));
    });
    $("#waist_mark_v").click(function(){
        set_w_tips($("#verti_waist"));
        set_w_tips($("#verti_waist"));
    });
    $("#hip_mark_v").click(function(){
        set_w_tips($("#verti_hip"));
        set_w_tips($("#verti_hip"));
    });
    
    
    
    



if(parseInt($("#measurement_shoulder_width").val()) == 0 && parseInt($("#measurement_bust_width").val()) == 0 && parseInt($("#measurement_waist_width").val()) == 0 && parseInt($("#measurement_hip_width").val()) == 0){
    $("#measurement_shoulder_width").attr("value", 110);
    $("#measurement_bust_width").attr("value", 120);
    $("#measurement_waist_width").attr("value", 96);
    $("#measurement_hip_width").attr("value", 120);
}

$("#shoulder_mark_h").css({width: parseInt($("#measurement_shoulder_width").val())});
$("#bust_mark_h").css({width: parseInt($("#measurement_bust_width").val())});
$("#waist_mark_h").css({width: parseInt($("#measurement_waist_width").val())});
$("#hip_mark_h").css({width: parseInt($("#measurement_hip_width").val())});

$("#shoulder_mark_h_zoom").css({width: parseInt($("#measurement_shoulder_width").val()) * 2});
$("#bust_mark_h_zoom").css({width: parseInt($("#measurement_bust_width").val()) * 2});
$("#waist_mark_h_zoom").css({width: parseInt($("#measurement_waist_width").val()) * 2});
$("#hip_mark_h_zoom").css({width: parseInt($("#measurement_hip_width").val()) * 2});


$("#shoulder_mark_v").draggable({axis: "y", containment: "#shoulder_mark_hldr", scroll: false,
        create: function() {
            
        },
        start: function() {
            set_w_tips($("#verti_shoulder"));
        },
        drag: function() {
            set_w_tips($("#verti_shoulder"));
        },
        stop: function() {
            set_w_tips($("#verti_shoulder"));
        }
    });
    $("#bust_mark_v").draggable({axis: "y", containment: "#bust_mark_hldr", scroll: false,
        create: function() {
            
        },
        start: function() {
            set_w_tips($("#verti_bust"));
        },
        drag: function() {
            set_w_tips($("#verti_bust"));
        },
        stop: function() {
            set_w_tips($("#verti_bust"));
        }
    });
    $("#waist_mark_v").draggable({animate: true, axis: "y", containment: "#waist_mark_hldr", scroll: false,
        create: function() {
        },
        start: function() {
            set_w_tips($("#verti_waist"));
        },
        drag: function() {
            set_w_tips($("#verti_waist"));
        },
        stop: function() {
            set_w_tips($("#verti_waist"));
        }
    });
    $("#hip_mark_v").draggable({axis: "y", containment: "#hip_mark_hldr", scroll: false,
        create: function() {
        },
        start: function() {
            set_w_tips($("#verti_hip"));
        },
        drag: function() {
            set_w_tips($("#verti_hip"));
        },
        stop: function() {
            set_w_tips($("#verti_hip"));
        }
    });
    
    
    $("#shoulder_slider_set").slider({
        animate: true,
        range: "min",
        value: parseInt($("#measurement_shoulder_width").val()),
        min: 30,
        max: 130,
        step: 2,
        slide: function( event, ui ) {           
           $( "#shoulder_mark_h" ).css({width: ui.value});
           $("#measurement_shoulder_width").attr("value", ui.value);
           $("#shoulder_mark_h_zoom").css({width: ui.value * 2});
        }
        });
        
     $("#bust_slider_set").slider({
        animate: true,
        range: "min",
        value: parseInt($("#measurement_bust_width").val()),
        min: 30,
        max: 130,
        step: 2,
        slide: function( event, ui ) {           
           $( "#bust_mark_h" ).css({width: ui.value});
           $("#measurement_bust_width").attr("value", ui.value);
           $("#bust_mark_h_zoom").css({width: ui.value * 2});
        }
        });
        
      $("#waist_slider_set").slider({
        animate: true,
        range: "min",
        value: parseInt($("#measurement_waist_width").val()),
        min: 30,
        max: 130,
        step: 2,
        slide: function( event, ui ) {           
           $( "#waist_mark_h" ).css({width: ui.value});
           $("#measurement_waist_width").attr("value", ui.value);
           $("#waist_mark_h_zoom").css({width: ui.value * 2});
        }
        });
        
      $("#hip_slider_set").slider({
        animate: true,
        range: "min",
        value: parseInt($("#measurement_hip_width").val()),
        min: 30,
        max: 130,
        step: 2,
        slide: function( event, ui ) {           
           $( "#hip_mark_h" ).css({width: ui.value});
           $("#measurement_hip_width").attr("value", ui.value);
           $("#hip_mark_h_zoom").css({width: ui.value * 2});
        }
        });
    
});
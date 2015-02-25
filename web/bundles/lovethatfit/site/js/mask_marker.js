hitOptions = {
	segments: true,
	stroke: false,
	fill: false,
	tolerance: 22
};

inc_ratio = 1;
curr_screen_height = 505  ;
center_pos = 161;
def_pos_x = -500;
def_path_diff = 500;

gap_top_head = -20;

curr_view = "normal";
curr_crop = "normal";

dv_user_px_h = parseInt($("#dv_user_px_height").attr("value"));
dv_top_bar = parseInt($("#dv_top_bar").attr("value"));
dv_bottom_bar = parseInt($("#dv_bottom_bar").attr("value"));
dv_per_inch_px = parseInt($("#dv_per_inch_px").attr("value"));
dv_type = parseInt($("#dv_type").attr("value"));
dv_scr_h = parseInt($("#dv_scr_h").attr("value"));
dv_edit_type = parseInt($("#dv_edit_type").attr("value"));



//////// From JS file

croped_img_path = $("#hdn_user_cropped_image_url").attr('value');

chk_no_img_path = true;
//true
//alert(croped_img_path);



//chk_no_img_path = true;

if(croped_img_path == "/webapp/web/" || croped_img_path == "/cs-ltf-webapp/web/"){
    chk_no_img_path = true;
    //alert("Ahoo");
}else{
    if(croped_img_path == "/")
    chk_no_img_path = true;
}
//chk_no_img_path = false;
//////// From JS file --- End

$(document).ready(function() {
    //document.getElementById("canv_mask").setAttribute("height", window.screen.height);
    //document.getElementById("canv_mask").setAttribute("width", window.screen.width);
    createBlob();
});

function createBlob() {


        var pathData = $("#img_path_paper").attr("value");
        
        if(chk_no_img_path == true){
            
            pathData = $("#default_user_path").html();
            $("#measurement_shoulder_height").attr("value", "66.6");
            $("#measurement_hip_height").attr("value", "159.4");
        }
        
mid_area_path = new Path(pathData);
mid_area_path.opacity = 0.6;
trans_bg = new Path.Rectangle(new Point(-300, -300), new Size(1000, 2000));
trans_bg.style = {
	fillColor: '#666666',
	stroke: 2,
	strokeColor: '#ffcc00'
};





//var p_user_height = parseInt($('#user_height_frm_3').attr('value')) + 3.375;

p_user_height = parseInt($('#user_height_frm_3').attr('value'));

//var pkpkpk = parseInt($('#dv_user_px_height').attr('value'));

p_user_height_new = curr_screen_height / p_user_height;

var p_user_zoom_ratio = curr_screen_height / 450;

var final_user_height_ratio = curr_screen_height / p_user_height;

p_user_height = p_user_height * final_user_height_ratio;
//alert(p_user_height);
p_user_height_px = p_user_height;

p_user_height = p_user_height * 100 / curr_screen_height;

p_user_height = p_user_height / 100;




user_img_url = $("#hdn_user_cropped_image_url").attr("value");
user_image = new Raster(user_img_url);

user_image.scale(inc_ratio,p_user_height*inc_ratio);
//user_image.position = new Point(center_pos,(p_user_height_px * inc_ratio /2)+10);
user_image.position = new Point(center_pos,568/2);

user_image.position = new Point(center_pos,user_image.position.y - dv_top_bar + 40 + ((dv_user_px_h / 100) * 3.75));

if(chk_no_img_path == true){
    
    //alert((dv_user_px_h + (((dv_user_px_h / 100) * 3.75) / 100)));

    mid_area_path.scale(inc_ratio, p_user_height * ((dv_user_px_h + ((dv_user_px_h / 100) * 3.75)) / 450));
    //alert("in side");
   mid_area_path.position = new Point(center_pos,(dv_user_px_h /2)  + 40 + ((dv_user_px_h / 100) * 3.75) + 8);
   
   def_shape_h = p_user_height; //65in
   
   def_head_h = (14.66 * def_shape_h) / 100;
   def_torso_h = (35.96 * def_shape_h) / 100;
   def_inseam_h = (47.61 * def_shape_h) / 100;
   
   def_head_base_point = mid_area_path.segments[0].point.y;
   
  // alert("Head base: " + def_head_base_point + " User Height: "+def_shape_h);
   
  if(parseInt($('#user_height_frm_3').attr('value')) >= 75){
    def_head_p_incr = (parseInt($('#user_height_frm_3').attr('value')) - 75)/5;
  }
  else{
    def_head_p_incr = (75 - parseInt($('#user_height_frm_3').attr('value')))/6;
  }

  var head_segments = [1,2,3,4,5,6,7,69,68,67,66,65,64,63];
  function adj_head_points(){
      for(var i = 0; i < head_segments.length; i++) {
          if(head_segments[i] == 5 || head_segments[i] == 65) {
            mid_area_path.segments[head_segments[i]].point.y += def_head_p_incr * 1.5;
          }
          
          else {
            mid_area_path.segments[head_segments[i]].point.y +=  def_head_p_incr;
          }
      };
  }
  adj_head_points();
  
  var torso_adj_segments = [16,17,18,19,20,21,35,54,53,52,51,50,49];
  function adj_torso_points(){
        
  //var arm_pit_dis = mid_area_path.segments[54].point.y - mid_area_path.segments[64].point.y;
  var arm_pit_dis = 43;
  var arm_pit_dis_curr = mid_area_path.segments[54].point.y - mid_area_path.segments[64].point.y;
  var final_arm_pit_dis = (arm_pit_dis - arm_pit_dis_curr) + def_head_p_incr;
  
      for(var i = 0; i < torso_adj_segments.length; i++) {
          if(false) {
            mid_area_path.segments[torso_adj_segments[i]].point.y = (mid_area_path.segments[64].point.y + arm_pit_dis);
          }
          else {
            mid_area_path.segments[torso_adj_segments[i]].point.y += final_arm_pit_dis;
          }
      };
  }
  adj_torso_points();
  
  
  var inseam_adj_segments = [22,23,24,25,48,47,46,45,44];
  function adj_inseam_points(){
        
  var arm_pit_dis = 43;
  var arm_pit_dis_curr = mid_area_path.segments[54].point.y - mid_area_path.segments[64].point.y;
  var final_arm_pit_dis = (arm_pit_dis - arm_pit_dis_curr) + def_head_p_incr;
  
      for(var i = 0; i < inseam_adj_segments.length; i++) {
          if(false) {
            mid_area_path.segments[inseam_adj_segments[i]].point.y = (mid_area_path.segments[64].point.y + arm_pit_dis);
          }
          else {
            mid_area_path.segments[inseam_adj_segments[i]].point.y += final_arm_pit_dis;
          }
      };
  }
  adj_inseam_points();
  
  
  var user_shoulder_width = parseInt($("#user_back_frm_3").attr("value"));
  
  user_shoulder_width = user_shoulder_width * dv_per_inch_px;
  
  //alert(dv_per_inch_px);
  
  var torso_w_adj_left = [54,53,52,51,50,49];
  var torso_w_adj_right = [16,17,18,19,20,21];
  
  var dm_front_shoulder = mid_area_path.segments[7].point.x - mid_area_path.segments[63].point.x;
  var user_front_shoulder = user_shoulder_width;
  
  var front_shoulder_diff = user_front_shoulder - dm_front_shoulder;
  //alert(front_shoulder_diff/2);
  mid_area_path.segments[7].point.x += front_shoulder_diff/2;
  mid_area_path.segments[63].point.x -= front_shoulder_diff/2;
  
  //var front_shoulder_diff = 60;
  
  var diff_apply = front_shoulder_diff/2;

  function adj_torso_points_w(){
    for(var i = 0; i < torso_w_adj_left.length; i++) {
          mid_area_path.segments[torso_w_adj_left[i]].point.x -= diff_apply;
    };
    for(var i = 0; i < torso_w_adj_right.length; i++) {
          mid_area_path.segments[torso_w_adj_right[i]].point.x += diff_apply;
    };
  }
  adj_torso_points_w();
  
  
  var arm_w_adj_out_left = [8,9,10];
  var arm_w_adj_out_right = [62,61,60];
  var diff_apply = front_shoulder_diff/4;
  
  mid_area_path.segments[8].point.x += (diff_apply*2)+((diff_apply*32)/100);
  mid_area_path.segments[9].point.x += (diff_apply*2)+((diff_apply*32)/100);
  mid_area_path.segments[10].point.x += (diff_apply*2)+((diff_apply*32)/100);
  mid_area_path.segments[62].point.x -= (diff_apply*2)-((diff_apply*32)/100);
  mid_area_path.segments[61].point.x -= (diff_apply*2)-((diff_apply*32)/100);
  mid_area_path.segments[60].point.x -= (diff_apply*2)-((diff_apply*32)/100);
  
  mid_area_path.segments[14].point.x += diff_apply*2;
  mid_area_path.segments[15].point.x += diff_apply*2;
  
  mid_area_path.segments[14].point.x -= ((diff_apply*32)/100);
  mid_area_path.segments[15].point.x -= ((diff_apply*32)/100);
  
  mid_area_path.segments[56].point.x -= diff_apply*2;
  mid_area_path.segments[55].point.x -= diff_apply*2;
  
  mid_area_path.segments[56].point.x += (diff_apply*32)/100;
  mid_area_path.segments[55].point.x += (diff_apply*32)/100;
  
//var chk_da_lala = front_shoulder_diff*54/100;
//alert(chk_da_lala);
  
  //mid_area_path.segments[48].point.x -= (front_shoulder_diff/2)*54/100;
  mid_area_path.segments[48].point.x -= front_shoulder_diff*54/100;
  
  mid_area_path.segments[47].point.x -= front_shoulder_diff*44/100;
  mid_area_path.segments[46].point.x -= front_shoulder_diff*44/100;
  mid_area_path.segments[45].point.x -= front_shoulder_diff*44/100;
  
  mid_area_path.segments[44].point.x -= ((front_shoulder_diff*33.5)/100);
  
  mid_area_path.segments[43].point.x -= ((diff_apply*33.5)/100);

  mid_area_path.segments[22].point.x += front_shoulder_diff*54/100;
  mid_area_path.segments[23].point.x += ((front_shoulder_diff*44)/100);
  mid_area_path.segments[24].point.x += ((front_shoulder_diff*44)/100);
  mid_area_path.segments[25].point.x += ((front_shoulder_diff*44)/100);
  
  mid_area_path.segments[26].point.x += ((front_shoulder_diff*44)/100);
  
  mid_area_path.segments[27].point.x += ((diff_apply*33.5)/100);
  
  
 
    mid_area_path.segments[34].point.x -= ((front_shoulder_diff*14)/100);
    mid_area_path.segments[36].point.x += ((front_shoulder_diff*14)/100);
    
    mid_area_path.segments[33].point.x -= ((front_shoulder_diff*11)/100);
    mid_area_path.segments[37].point.x += ((front_shoulder_diff*11)/100);
    
    mid_area_path.segments[31].point.x -= ((front_shoulder_diff*7.5)/100);
    mid_area_path.segments[39].point.x += ((front_shoulder_diff*7.5)/100);
  

    
    
   }
   else{
    
    mid_area_path.position = new Point(parseInt($('#mask_x').attr('value')),parseInt($('#mask_y').attr('value')));
    
    //mid_area_path.position = new Point(parseInt($('#mask_x').attr('value')),parseInt($('#mask_y').attr('value')));
    //mid_area_path.scale(inc_ratio,p_user_height * inc_ratio);
    mid_area_path.position = new Point(center_pos,(p_user_height_px * inc_ratio /2)+gap_top_head);
    
    //mid_area_path.pivot = new Point(center_pos,0);
    //mid_area_path.scale(1.15,1.15);
    //mid_area_path.scale(1.15,1.15);
    
    
    
    //alert(parseInt($('#dv_bottom_bar').attr('value')) - parseInt($('#dv_top_bar').attr('value')));
    //mid_area_path.position = new Point(140, -17 + 50 );
    //alert(p_user_height_new);
    
   }


//mid_area_path.segments[0].point.x = 20;

//mid_area_path.segments[15].point.y += 60;

//alert(mid_area_path.segments[15].point.y);



////////////////////////////////

    /*
                var sholder_left = mid_area_path.segments[6].point.y;
                var sholder_right = mid_area_path.segments[62].point.y;
                
                
                if(sholder_left <= sholder_right){
                    $("#measurement_shoulder_height").attr("value", sholder_left);
                }else{
                    $("#measurement_shoulder_height").attr("value", sholder_right);
                }
                
                
                var bottom_left = mid_area_path.segments[21].point.y - 66;
                var bottom_right = mid_area_path.segments[49].point.y - 66;
                
                
                
                //alert(sholder_right);
                
                if(bottom_left <= bottom_right){
                    $("#measurement_hip_height").attr("value", bottom_left);
                }else{
                    $("#measurement_hip_height").attr("value", bottom_right);
                }
                
          */      
////////////////////////////////////////////////////////////// 


path_com = new CompoundPath({
    children: [
		trans_bg,
                //default_path,
                mid_area_path
    ],
    //fillColor: '#666666',
    selected: false,
    //strokeWidth: 1,
    //strokeColor: '#ffcc00',
    fillColor: '#666666'
    
	//strokeColor: '#ffcc00'
});

//path_com.children[0].strokeWidth = 4;
//path_com.children[0].strokeColor = '#ffcc00';

mid_area_path.strokeWidth = 4;

path_com.opacity = 0.85;

     if(chk_no_img_path == true){      
     
        export_svg_data(path_com);
     
        var default_adjusted_path_data = $("#default_marker_svg").attr("value");
        
        //alert(default_adjusted_path_data);
        
        d_adj_path = new Path(default_adjusted_path_data);
        d_adj_path.strokeColor = 'black';
        d_adj_path.position = new Point(def_pos_x,(p_user_height_px * inc_ratio /2)+gap_top_head);
        d_adj_path.opacity = 0.5;
        
        d_adj_path.scale(inc_ratio,p_user_height * inc_ratio);
     
     
      

    }else {
    
    
        var default_adjusted_path_data = $("#default_marker_svg").attr("value");
        console.log(default_adjusted_path_data);
        d_adj_path = new Path(default_adjusted_path_data);
        d_adj_path.strokeColor = 'black';
        d_adj_path.position = new Point(def_pos_x,(p_user_height_px * inc_ratio /2)+gap_top_head);
        d_adj_path.opacity = 0.5;
        
        d_adj_path.scale(inc_ratio,p_user_height * inc_ratio);
        
    }
      return path_com;
}

function export_svg_data(path_com){




var export_path_full = path_com.exportSVG({asString: true});
        
        
        
        export_path_full.toString();
        var export_path_remove_start = export_path_full.substr(60);
        
        var export_path_final = export_path_remove_start.substr(0, export_path_remove_start.length - 39);
        
        console.log(export_path_final);
        
        $("#img_path_paper").attr("value", export_path_final);
        $("#default_marker_svg").attr("value", export_path_final);
        
        main_path = project.getItem({
            class: Path,
            segments: function(segments) {
                   return segments.length > 20;
            }
            });
            
            $("#mask_x").attr("value", main_path.position.x);
            $("#mask_y").attr("value", main_path.position.y);
}

//var rgt_arm_ref = $("#rgt_arm_ref").attr("value");

rgt_arm_ref = new Path({});

lft_arm_ref = new Path({});

rgt_leg_ref = new Path({});

lft_leg_ref = new Path({});

 function get_path_seg(ref_part_obj, obj_path_for_ref, int_seg_num, end_seg_num){  
    
    ref_part_obj.removeSegments();
    
    for(var i = int_seg_num; i < end_seg_num; i++) {
        ref_part_obj.add(obj_path_for_ref.segments[i]);
    }
    
    return ref_part_obj;
  }  
  

  
var an_inc = 20;  
  
var curr_path_prefix = $("#hdn_serverpath").attr("value");

console.log(curr_path_prefix);

var but_zoom_in_url = curr_path_prefix + "bundles/lovethatfit/site/images/zoom_inw.png";
var but_zoom_in = new Raster(but_zoom_in_url);

but_zoom_in.position = new Point(26, 24 + an_inc);

var but_zoom_out_url = curr_path_prefix + "bundles/lovethatfit/site/images/zoom_out.png";
var but_zoom_out = new Raster(but_zoom_out_url);

but_zoom_out.position = new Point(-500, 24 + an_inc);

var but_move_left_url = curr_path_prefix + "bundles/lovethatfit/site/images/move_left.png";
var but_move_left = new Raster(but_move_left_url);

but_move_left.position = new Point(26, 68 + an_inc);

var but_move_right_url = curr_path_prefix + "bundles/lovethatfit/site/images/move_right.png";
var but_move_right = new Raster(but_move_right_url);

but_move_right.position = new Point(26, 112 + an_inc);

var but_move_up_url = curr_path_prefix + "bundles/lovethatfit/site/images/move_up.png";
var but_move_up = new Raster(but_move_up_url);

but_move_up.position = new Point(26, 156 + an_inc);

var but_move_down_url = curr_path_prefix + "bundles/lovethatfit/site/images/move_down.png";
var but_move_down = new Raster(but_move_down_url);

but_move_down.position = new Point(26, 200 + an_inc);

var but_rotate_left_url = curr_path_prefix + "bundles/lovethatfit/site/images/rotate_left.png";
var but_rotate_left = new Raster(but_rotate_left_url);

but_rotate_left.position = new Point(26, 244 + an_inc);

var but_rotate_right_url = curr_path_prefix + "bundles/lovethatfit/site/images/rotate_right.png";
var but_rotate_right = new Raster(but_rotate_right_url);

but_rotate_right.position = new Point(26, 288 + an_inc);

var but_crop_icon_url = curr_path_prefix + "bundles/lovethatfit/site/images/crop_icon.png";
var but_crop_icon = new Raster(but_crop_icon_url);

but_crop_icon.position = new Point(26, 332 + an_inc);


var scr1_but_hiw_icon_url = curr_path_prefix + "bundles/lovethatfit/site/images/how_it_works_icon.png";
var scr1_but_hiw_icon = new Raster(scr1_but_hiw_icon_url);

scr1_but_hiw_icon.position = new Point(294, 50);

var scr1_but_camera_icon_url = curr_path_prefix + "bundles/lovethatfit/site/images/camera_icon.png";
var scr1_but_camera_icon = new Raster(scr1_but_camera_icon_url);

scr1_but_camera_icon.position = new Point(294, 100);

var scr1_but_save_icon_url = curr_path_prefix + "bundles/lovethatfit/site/images/scr1_next_btn.png";
var scr1_but_save_icon = new Raster(scr1_but_save_icon_url);

scr1_but_save_icon.position = new Point(281, 550);




//////////////////////////////////  Dragable Bigger Points - Step One ///////////////////////////////////

var bigger_point_url = curr_path_prefix + "bundles/lovethatfit/site/images/bigger_point.png";


var but_bp_head_top = new Raster(bigger_point_url);
but_bp_head_top.position = new Point(mid_area_path.segments[69].point);
but_bp_head_top.opacity = 0;

var but_bp_lft_shoulder = new Raster(bigger_point_url);
but_bp_lft_shoulder.position = new Point(mid_area_path.segments[62].point);

var but_bp_rgt_shoulder = new Raster(bigger_point_url);
but_bp_rgt_shoulder.position = new Point(mid_area_path.segments[6].point);

var but_bp_lft_arm_pit = new Raster(bigger_point_url);
but_bp_lft_arm_pit.position = new Point(mid_area_path.segments[53].point);

var but_bp_rgt_arm_pit = new Raster(bigger_point_url);
but_bp_rgt_arm_pit.position = new Point(mid_area_path.segments[15].point);

var but_bp_lft_waist = new Raster(bigger_point_url);
but_bp_lft_waist.position = new Point(mid_area_path.segments[51].point);

var but_bp_rgt_waist = new Raster(bigger_point_url);
but_bp_rgt_waist.position = new Point(mid_area_path.segments[17].point);

var but_bp_lft_hip = new Raster(bigger_point_url);
but_bp_lft_hip.position = new Point(mid_area_path.segments[48].point);

var but_bp_rgt_hip = new Raster(bigger_point_url);
but_bp_rgt_hip.position = new Point(mid_area_path.segments[20].point);


var but_bp_rgt_hand = new Raster(bigger_point_url);
but_bp_rgt_hand.position = new Point(mid_area_path.segments[10].point);

var but_bp_lft_hand = new Raster(bigger_point_url);
but_bp_lft_hand.position = new Point(mid_area_path.segments[58].point);



var but_bp_inseam = new Raster(bigger_point_url);
but_bp_inseam.position = new Point(mid_area_path.segments[34].point);




var but_bp_lft_foot = new Raster(bigger_point_url);
but_bp_lft_foot.position = new Point(mid_area_path.segments[40].point);

var but_bp_rgt_foot = new Raster(bigger_point_url);
but_bp_rgt_foot.position = new Point(mid_area_path.segments[28].point);



var single_item = null;
function hide_big_points(){
    
    but_bp_head_top.position = new Point(-500, -500);
    but_bp_lft_shoulder.position = new Point(-500, -500);
    but_bp_rgt_shoulder.position = new Point(-500, -500);
    but_bp_lft_arm_pit.position = new Point(-500, -500);
    but_bp_rgt_arm_pit.position = new Point(-500, -500);
    but_bp_lft_waist.position = new Point(-500, -500);
    but_bp_rgt_waist.position = new Point(-500, -500);
    but_bp_lft_hip.position = new Point(-500, -500);
    but_bp_rgt_hip.position = new Point(-500, -500);
    but_bp_lft_hand.position = new Point(-500, -500);
    but_bp_rgt_hand.position = new Point(-500, -500);
    but_bp_inseam.position = new Point(-500, -500);
    but_bp_lft_foot.position = new Point(-500, -500);
    but_bp_rgt_foot.position = new Point(-500, -500);
}
function set_big_points(single_item){
    if(single_item != null){
        single_item.position = new Point(-500, -500);
    }else{
        but_bp_head_top.position = new Point(mid_area_path.segments[69].point);
        but_bp_lft_shoulder.position = new Point(mid_area_path.segments[62].point);
        but_bp_rgt_shoulder.position = new Point(mid_area_path.segments[6].point);
        but_bp_lft_arm_pit.position = new Point(mid_area_path.segments[53].point);
        but_bp_rgt_arm_pit.position = new Point(mid_area_path.segments[15].point);
        but_bp_lft_waist.position = new Point(mid_area_path.segments[51].point);
        but_bp_rgt_waist.position = new Point(mid_area_path.segments[17].point);
        but_bp_lft_hip.position = new Point(mid_area_path.segments[48].point);
        but_bp_rgt_hip.position = new Point(mid_area_path.segments[20].point);
        but_bp_rgt_hand.position = new Point(mid_area_path.segments[10].point);
        but_bp_lft_hand.position = new Point(mid_area_path.segments[58].point);
        but_bp_inseam.position = new Point(mid_area_path.segments[34].point);
        but_bp_lft_foot.position = new Point(mid_area_path.segments[40].point);
        but_bp_rgt_foot.position = new Point(mid_area_path.segments[28].point);
        
        
    }
}

function hide_ele_img_export(){
    hide_big_points();
    //but_zoom_in.opacity = 0;
    //but_zoom_out.opacity = 0;
   // but_move_left.opacity = 0;
   // but_move_right.opacity = 0;
   // but_move_up.opacity = 0;
   // but_move_down.opacity = 0;
  //  but_rotate_left.opacity = 0;
   // but_rotate_right.opacity = 0;
  //  but_crop_icon.opacity = 0;
   // scr1_but_hiw_icon.opacity = 0;
   // scr1_but_camera_icon.opacity = 0;
   // scr1_but_save_icon.opacity = 0;
  //  path_com.opacity = 0;
    
    
but_zoom_in.position = new Point(-1000,-1000);
but_zoom_out.position = new Point(-1000,-1000);
but_move_left.position = new Point(-1000,-1000);
but_move_right.position = new Point(-1000,-1000);
but_move_up.position = new Point(-1000,-1000);
but_move_down.position = new Point(-1000,-1000);
but_rotate_left.position = new Point(-1000,-1000);
but_rotate_right.position = new Point(-1000,-1000);
but_crop_icon.position = new Point(-1000,-1000);
scr1_but_hiw_icon.position = new Point(-1000,-1000);
scr1_but_camera_icon.position = new Point(-1000,-1000);
scr1_but_save_icon.position = new Point(-1000,-1000);
path_com.opacity = 0;
    
    //post_img();
    //upload();
    
}

function show_loader(){
    
}


curr_get_values = null;




big_point = false;
big_point_ele = false;

ijazat = "yes";


var segment;
var movePath = false;
function onMouseDown(event) {
    
	segment = path = null;
	var hitResult = project.hitTest(event.point, hitOptions);
        
	if (!hitResult)
		return;

	if (event.modifiers.shift) {
		if (hitResult.type == 'segment') {
			//hitResult.segment.remove();
			
		};
		return;
	}
        
        if (hitResult.type == 'segment') {
            if(curr_view == "normal"){
                console.log("Normal hay bhai");
		return false;
            }
	};
        
        
       
        
        
	if (hitResult && hitResult.item != user_image) {
                var ratio_zoom_value = 1/3;
		path = hitResult.item;
                
		if (hitResult.type == 'segment') {
			segment = hitResult.segment;
		} else if (hitResult.type == 'pixel') {
			//segment = null;
                        if(hitResult.item == user_image){
                        }else if(curr_view == "normal" && hitResult.item == but_zoom_in && ijazat == "yes"){
                            
                            x_pos_main_path = main_path.position.x;
                            x_pos_trans_bg = trans_bg.position.x;
                            x_pos_def_path = def_path.position.x;
                            x_pos_user_image = user_image.position.x;
                            
                            y_pos_main_path = main_path.position.y;
                            y_pos_trans_bg = trans_bg.position.y;
                            y_pos_def_path = def_path.position.y;
                            y_pos_user_image = user_image.position.y;
                            
                            
                            curr_view = "zoomed";
                            hitOptions.fill = true;
                            main_path.scale(inc_ratio * 3,(p_user_height*inc_ratio) * 3);
                            trans_bg.scale(inc_ratio * 3,(p_user_height*inc_ratio) * 3);
                            def_path.scale(inc_ratio * 3,(p_user_height*inc_ratio) * 3);
                            user_image.scale(inc_ratio * 3,(p_user_height*inc_ratio) * 3);
                            mid_area_path.selected = true;
                            
                            //user_image.position.y += 66;
                            
                            user_image.position = new Point(center_pos,(user_image.position.y - dv_top_bar + 40 + ((dv_user_px_h / 100) * 3.75)) + 24);
                            
                            but_zoom_in.position.x = -500;
                            but_zoom_out.position.x = 26;
                            
                            
                            hide_big_points();
                            
                            //window.location.href = "scr1_but_save_mask";
                            
                            
                        }else if(curr_view == "zoomed" && hitResult.item == but_zoom_out){
                            
                            
                            
                            curr_view = "normal";
                            hitOptions.fill = true;
                            main_path.scale(inc_ratio / 3,(p_user_height*inc_ratio) / 3);
                            mid_area_path.selected = true;
                            trans_bg.scale(inc_ratio / 3,(p_user_height*inc_ratio) / 3);
                            def_path.scale(inc_ratio / 3,(p_user_height*inc_ratio) / 3);
                            user_image.scale(inc_ratio / 3,(p_user_height*inc_ratio) / 3);
                            user_image.position.y -= 66;
                            
                            main_path.position.x = x_pos_main_path;
                            trans_bg.position.x = x_pos_trans_bg;
                            def_path.position.x = x_pos_def_path;
                            user_image.position.x = x_pos_user_image;
                            
                            main_path.position.y = y_pos_main_path;
                            trans_bg.position.y = y_pos_trans_bg;
                            def_path.position.y = y_pos_def_path;
                            user_image.position.y = y_pos_user_image;
                            
                            but_zoom_in.position.x = 26;
                            but_zoom_out.position.x = -500;
                            
                            set_big_points();
                        }
                        else if(hitResult.item == but_move_left){
                            user_image.position.x -= 1;
                            
                            if(curr_view == "zoomed"){
                                x_pos_user_image -= ratio_zoom_value;
                            }
                            
                        }
                        else if(hitResult.item == but_move_right){
                            user_image.position.x += 1;
                            
                            if(curr_view == "zoomed"){
                                x_pos_user_image += ratio_zoom_value;
                            }
                        }
                        else if(hitResult.item == but_move_up){
                            user_image.position.y -= 1;
                            
                            if(curr_view == "zoomed"){
                                y_pos_user_image -= ratio_zoom_value;
                            }
                        }
                        else if(hitResult.item == but_move_down){
                            user_image.position.y += 1;
                            
                            if(curr_view == "zoomed"){
                                y_pos_user_image += ratio_zoom_value;
                            }
                            
                        }
                        else if(hitResult.item == but_rotate_left){
                            user_image.rotate(-0.5); 
                        }
                        else if(hitResult.item == but_rotate_right){
                            user_image.rotate(0.5);
                        }
                        else if(curr_crop == "normal" && hitResult.item == but_crop_icon){
                            curr_crop = "checked";
                            path_com.fillColor = "#fff";
                            path_com.opacity = 1;
                        }else if(curr_crop == "checked" && hitResult.item == but_crop_icon){
                            curr_crop = "normal";
                            path_com.fillColor = "#666";
                            path_com.opacity = 0.6;
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_head_top){
                            big_point = true;
                            big_point_ele = hitResult.item;
                            console.log("but_bp_head_top");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_lft_shoulder){
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 62;
                            
                            console.log("but_bp_lft_shoulder");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_rgt_shoulder){
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 6;
                            console.log("but_bp_rgt_shoulder");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_lft_arm_pit){
                            
                            //ijazat = "no";
                            
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 53;
                            
                            curr_range_1 = but_bp_lft_arm_pit;
                            curr_range_2 = but_bp_lft_waist;
                            big_move_adj = [52, 54];
                            get_ele_pos();
                            
                            console.log("but_bp_lft_arm_pit");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_rgt_arm_pit){
                            
                            //ijazat = "no";
                            
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 15;
                            
                            curr_range_1 = but_bp_rgt_arm_pit;
                            curr_range_2 = but_bp_rgt_waist;
                            big_move_adj = [16, 14];
                            get_ele_pos();
                            
                            console.log("but_bp_rgt_arm_pit");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_lft_waist){
                            
                            //ijazat = "no";
                            
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 51;
                            
                            curr_range_1 = but_bp_lft_waist;
                            curr_range_2 = but_bp_lft_hip;
                            big_move_adj = [50,49];
                            get_ele_pos();
                            
                            console.log("but_bp_lft_waist");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_rgt_waist){
                            
                            //ijazat = "no";
                            
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 17;
                            
                            curr_range_1 = but_bp_rgt_waist;
                            curr_range_2 = but_bp_rgt_hip;
                            big_move_adj = [18,19];
                            get_ele_pos();
                            
                            console.log("but_bp_rgt_waist");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_lft_hip){
                            
                            //ijazat = "no";
                            
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 48;
                            
                            curr_range_1 = but_bp_lft_hip;
                            curr_range_2 = but_bp_lft_foot;
                            //big_move_adj = [47,46,45,44,43,37,36,35];
                            big_move_adj = [47];
                            get_ele_pos();
                            
                            console.log("but_bp_lft_hip");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_rgt_hip){
                            
                            //ijazat = "no";
                            
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 20;
                            
                            curr_range_1 = but_bp_rgt_hip;
                            curr_range_2 = but_bp_rgt_foot;
                            //big_move_adj = [21,22,23,24,25,31,32,33];
                            big_move_adj = [21];
                            get_ele_pos();
                            
                            console.log("but_bp_rgt_hip");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_rgt_hand){
                            
                            //ijazat = "no";
                            
                            get_path_seg(rgt_arm_ref, mid_area_path, 7, 15);
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 10;
                            console.log("but_bp_rgt_hand");
                            
                             
                            
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_lft_hand){
                            
                            //ijazat = "no";
                            
                            get_path_seg(lft_arm_ref, mid_area_path, 54, 62);
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 58;
                            console.log("but_bp_lft_hand");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_inseam){
                            
                            //ijazat = "no";
                            
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 34;
                            
                            curr_range_1 = but_bp_inseam;
                            curr_range_2 = but_bp_rgt_foot;
                            big_move_adj = [21,22,23,24,25,31,32,33,47,46,45,44,43,37,36,35];
                            get_ele_pos();
                            
                            console.log("but_bp_inseam");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_lft_foot){
                            
                            //ijazat = "no";
                            
                            get_path_seg(lft_leg_ref, mid_area_path, 35, 48);
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 40;
                            console.log("but_bp_lft_foot");
                        }else if(curr_crop == "normal" && hitResult.item == but_bp_rgt_foot){
                            
                            //ijazat = "no";
                            
                            get_path_seg(rgt_leg_ref, mid_area_path, 21, 34);
                            big_point = true;
                            big_point_ele = hitResult.item;
                            curr_big_seg = 28;
                            console.log("but_bp_rgt_foot");
                        }else if(curr_crop == "normal" && hitResult.item == scr1_but_hiw_icon){
                            window.location.href = "scr1_but_how_it_works";
                            //$("#scr1_but_how_it_works").trigger( "click" );
                            //alert("How It Works Button Tap");
                            //console.log("scr1_but_hiw_icon");
                        }else if(curr_crop == "normal" && hitResult.item == scr1_but_camera_icon){
                            window.location.href = "scr1_but_camera_options";
                            
                            //$("#scr1_but_camera_options").trigger();
                            
                            //$("#scr1_but_camera_options").live('click', function() { alert("That tickles!") });
                            //$("#scr1_but_camera_options").live('click');
                            
                            //alert(window.location.href);
                            
                            //$("#scr1_but_camera_options").trigger( "click" );
                            
                            //alert("Camera Button Tap");
                            console.log("scr1_but_camera_icon");
                        }else if(curr_crop == "normal" && hitResult.item == scr1_but_save_icon){
                            
                            //hide_ele_img_export();
                            //show_loader();
                            //post_img();
                            window.location.href = "scr1_but_save_mask";
                            
                            //
                            //
                            //
                            //alert("Save Button Tap");
                            //$("#scr1_but_save_mask").trigger( "click" );
                            console.log("scr1_but_save_icon");
                        }
                        
		}
	}
	//movePath = hitResult.type == 'fill';
	//if (movePath)
		//project.activeLayer.addChild(hitResult.item);
       //project.activeLayer.addChild(main_path);           
       
       if(hitResult.item == main_path){
              console.log("Its me lala");
          }         
       
                
                
}





function onMouseUp(event){
    big_point_ele = null;
    curr_big_seg = null;
  if (segment) {
        $("#img_path_json").attr("value", getPathArrayJson());
    }   
    
    
   
   //alert(rgt_arm_ref);
}
function getPathArrayJson(){
    var mp_array=[];
    for(var i = 0; i < path.segments.length; i++) {
        mp_array.push([path.segments[i].point.x, path.segments[i].point.y]);            
    };
    return JSON.stringify(mp_array);
}


main_path = project.getItem({
            class: Path,
            segments: function(segments) {
                   return segments.length > 20;
            }
            });
        
            
        
          //main_path = path;
            
            def_path = project.getItem({
            opacity: function(value) {
                    return value == 0.5;
                }
            });


function set_pivot(curr_item,point_to_set, point_x, point_y){
    if(point_to_set != null){
        
        console.log(point_to_set);
        
        curr_item.pivot = new Point(point_to_set);
        
    }else{
        console.log(point_x +"====="+point_y);
        curr_item.pivot = new Point(point_x,point_y);
    }
}
function give_per (max_value, value_per){
    return value_per * max_value / 100;
}
curr_range_1 = curr_range_2 = null;
curr_ele_pos_per = false;
mid_move_adj_per = [];
function get_ele_pos(){
    
    total_range = curr_range_2.position.y - curr_range_1.position.y;
    
    for(var i = 0; i < big_move_adj.length; i++) {
        
        curr_ele_pos = main_path.segments[big_move_adj[i]].point.y - curr_range_1.position.y;
        curr_ele_pos_per = curr_ele_pos * 100 / total_range;
        mid_move_adj_per[i] = curr_ele_pos_per;
        
    }
    
}

function set_ele_pos_per(){
    if(curr_range_1 != null){
        total_range = curr_range_2.position.y - curr_range_1.position.y;
        for(var i = 0; i < big_move_adj.length; i++) {

            main_path.segments[big_move_adj[i]].point.y = curr_range_1.position.y + mid_move_adj_per[i] * total_range / 100;
            def_path.segments[big_move_adj[i]].point.y = curr_range_1.position.y + mid_move_adj_per[i] * total_range / 100;
        }
    }
}

last_pos_ele = mid_area_path.segments[62].point.x;















function set_path_seg(event, set_ref_part_obj, pivot_x, pivot_y, rotate_min, rotate_max, seg_srt, seg_end, main_obj_seg_srt, main_obj_seg_end){
    curr_pos_rotate = main_path.segments[curr_big_seg].point.x;
            //var rotate_min = 240;rotate_min
            //var rotate_max = 280;
            final_rotate_value = rotate_max - curr_pos_rotate;
            if(final_rotate_value > 0 && final_rotate_value < 40){
               if(event.delta.x > 0){ 
                //set_ref_part_obj.rotate(-0.5, mid_area_path.segments[63].point.x, mid_area_path.segments[63].point.y + ((mid_area_path.segments[53].point.y - mid_area_path.segments[63].point.y)/2));
                set_ref_part_obj.rotate(-0.5, pivot_x, pivot_y);
                for(i=0; i < set_ref_part_obj.segments.length; i++){
                    
                    //alert(mid_area_path.segments[seg_srt+i].point.x +" : " + def_path.segments[seg_srt+i].point.x);
                    
                    mid_area_path.segments[seg_srt+i].point = set_ref_part_obj.segments[i].point;
                    
                    //def_path.segments[seg_srt+i].point.x = mid_area_path.segments[seg_srt+i].point.x - 1000;
                    //def_path.segments[seg_srt+i].point.y = set_ref_part_obj.segments[i].point.y;
                    
                    
                    //mid_area_path.segments[seg_srt+i] = set_ref_part_obj.segments[i];
                }
                
               }else {
                set_ref_part_obj.rotate(0.5, pivot_x, pivot_y);
                for(i=0; i < set_ref_part_obj.segments.length; i++){
                    
                    mid_area_path.segments[seg_srt+i].point = set_ref_part_obj.segments[i].point;
                    //def_path.segments[seg_srt+i].point.x = set_ref_part_obj.segments[i].point.x - 500;
                    //def_path.segments[seg_srt+i].point.y = set_ref_part_obj.segments[i].point.y;
                    
                    
                    
                    //mid_area_path.segments[seg_srt+i] = set_ref_part_obj.segments[i];
                }
               }
            }else if(final_rotate_value > 40){
                set_ref_part_obj.rotate(-0.5, pivot_x, pivot_y);
                for(i=0; i < set_ref_part_obj.segments.length; i++){
                    
                    mid_area_path.segments[seg_srt+i].point = set_ref_part_obj.segments[i].point;
                    //def_path.segments[seg_srt+i].point.x = set_ref_part_obj.segments[i].point.x - 500;
                    //def_path.segments[seg_srt+i].point.y = set_ref_part_obj.segments[i].point.y;
                   
                   
                   
                   // mid_area_path.segments[seg_srt+i] = set_ref_part_obj.segments[i];
                }
                                
            }else if(final_rotate_value < 0){
                set_ref_part_obj.rotate(0.5, pivot_x, pivot_y);
                for(i=0; i < set_ref_part_obj.segments.length; i++){
                    
                    mid_area_path.segments[seg_srt+i].point = set_ref_part_obj.segments[i].point;
                    //def_path.segments[seg_srt+i].point.x = set_ref_part_obj.segments[i].point.x - 500;
                    //def_path.segments[seg_srt+i].point.y = set_ref_part_obj.segments[i].point.y;
                    
                    
                    //mid_area_path.segments[seg_srt+i] = set_ref_part_obj.segments[i];
                }
 
            }
}



            //set_path_seg(set_ref_part_obj, rotate_min, rotate_max, seg_srt, seg_end, pivot_x, pivot_y, main_obj_seg_srt, main_obj_seg_end);
            //set_path_seg(set_ref_part_obj, pivot_x, pivot_y, rotate_min, rotate_max, seg_srt, seg_end, main_obj_seg_srt, main_obj_seg_end);
            
            























function onMouseDrag(event) {
    
    console.log(rgt_arm_ref);
    
        if(big_point_ele == but_bp_head_top){       
            //big_point_ele.position.y += event.delta.y;
            //set_pivot(main_path, main_path.segments[28].point , 50, 50);
            
            //main_path.scale(1, 0.6);
        }else if(big_point_ele){
            
              //alert(mid_area_path.segments[40].point.x - mid_area_path.segments[34].point.x); // 24px
              
            
            if(big_point_ele == but_bp_lft_hand){
                set_path_seg(event, lft_arm_ref, mid_area_path.segments[63].point.x, mid_area_path.segments[63].point.y + ((mid_area_path.segments[53].point.y - mid_area_path.segments[63].point.y)/2), mid_area_path.segments[48].point.x + 10, mid_area_path.segments[48].point.x + 50, 54, 62, 53, 63);
                but_bp_lft_hand.position = mid_area_path.segments[curr_big_seg].point;
            }else if(big_point_ele == but_bp_rgt_hand){
                set_path_seg(event, rgt_arm_ref, mid_area_path.segments[6].point.x, mid_area_path.segments[6].point.y + ((mid_area_path.segments[16].point.y - mid_area_path.segments[6].point.y)/2), mid_area_path.segments[20].point.x - 50, mid_area_path.segments[20].point.x - 10, 7, 15, 7, 16);
                but_bp_rgt_hand.position = mid_area_path.segments[curr_big_seg].point;
            }else if(big_point_ele == but_bp_lft_foot){
                set_path_seg(event, lft_leg_ref, mid_area_path.segments[35].point.x + ((mid_area_path.segments[49].point.x - mid_area_path.segments[35].point.x)/2), mid_area_path.segments[35].point.y, mid_area_path.segments[34].point.x + 14, mid_area_path.segments[34].point.x + 54, 35, 48, 35, 49);
                but_bp_lft_foot.position = mid_area_path.segments[curr_big_seg].point;
                console.log("asf");
            }else if(big_point_ele == but_bp_rgt_foot){
                set_path_seg(event, rgt_leg_ref, mid_area_path.segments[21].point.x + ((mid_area_path.segments[33].point.x - mid_area_path.segments[21].point.x)/2), mid_area_path.segments[21].point.y, mid_area_path.segments[34].point.x - 4, mid_area_path.segments[34].point.x - 14, 21, 35, 20, 35);
                but_bp_rgt_foot.position = mid_area_path.segments[curr_big_seg].point;
                console.log("asf");
            }else if(false){            
            curr_pos_rotate = main_path.segments[curr_big_seg].point.x;
            var rotate_min = 240;
            var rotate_max = 280;
            final_rotate_value = rotate_max - curr_pos_rotate;
            if(final_rotate_value > 0 && final_rotate_value < 40){
               if(event.delta.x > 0){ 
                lft_arm_ref.rotate(-0.5, mid_area_path.segments[63].point.x, mid_area_path.segments[63].point.y + ((mid_area_path.segments[53].point.y - mid_area_path.segments[63].point.y)/2));
                for(i=0; i < lft_arm_ref.segments.length; i++){
                    mid_area_path.segments[54+i] = lft_arm_ref.segments[i];
                }
                
               }else {
                lft_arm_ref.rotate(0.5, mid_area_path.segments[63].point.x, mid_area_path.segments[63].point.y + ((mid_area_path.segments[53].point.y - mid_area_path.segments[63].point.y)/2));
                for(i=0; i < lft_arm_ref.segments.length; i++){
                    
                    mid_area_path.segments[54+i] = lft_arm_ref.segments[i];
                    
                }
               }
            }else if(final_rotate_value > 40){
                lft_arm_ref.rotate(-0.5, mid_area_path.segments[63].point.x, mid_area_path.segments[63].point.y + ((mid_area_path.segments[53].point.y - mid_area_path.segments[63].point.y)/2));
                
                for(i=0; i < lft_arm_ref.segments.length; i++){
                    
                    mid_area_path.segments[54+i] = lft_arm_ref.segments[i];
                    
                }
                                
            }else if(final_rotate_value < 0){
                
                lft_arm_ref.rotate(0.5, mid_area_path.segments[63].point.x, mid_area_path.segments[63].point.y + ((mid_area_path.segments[53].point.y - mid_area_path.segments[63].point.y)/2));
                for(i=0; i < lft_arm_ref.segments.length; i++){
                    
                    mid_area_path.segments[54+i] = lft_arm_ref.segments[i];
                    
                }
                                
            }
            
                but_bp_lft_hand.position = mid_area_path.segments[curr_big_seg].point;
            
            }else if(big_point_ele != but_bp_lft_shoulder || big_point_ele != but_bp_rgt_shoulder){
                
                main_path.segments[curr_big_seg].point += event.delta;
                def_path.segments[curr_big_seg].point += event.delta;
                big_point_ele.position = main_path.segments[curr_big_seg].point;
                
                set_ele_pos_per();
                if(big_point_ele == but_bp_inseam){
                    main_path.segments[20].point.y += event.delta.y;
                    def_path.segments[20].point.y += event.delta.y;
                    main_path.segments[48].point.y += event.delta.y;
                    def_path.segments[48].point.y += event.delta.y;
                    but_bp_rgt_hip.position.y += event.delta.y;
                    but_bp_lft_hip.position.y += event.delta.y;
                }
            }
            
            
            
            
            
            
        }else if(segment) {
        
        function get_index_num(){
            for(var i = 0; i < path.segments.length; i++) {
                if(segment == path.segments[i]){
                    return i;
                }
            }
        }
        curr_dragged_seg = get_index_num();
        //alert(curr_dragged_seg);
        
        
        
        var def_segment = def_path.segments[curr_dragged_seg].point.x;
        
        
        
        def_segment = def_segment + def_path_diff + center_pos;
        //alert(def_segment);
        
        //var def_segment = 200;
            var px_range = 70;
            var active_segment = segment.point.x;
            
            if(active_segment > (def_segment + px_range)){
                    path.segments[curr_dragged_seg].point.x = def_segment + px_range;
               }else{
                   if(active_segment < (def_segment - px_range)){
                    path.segments[curr_dragged_seg].point.x = def_segment - px_range;
                   }else {
                    segment.point += event.delta;
                   }
               }
               
        var active_segment_y = path.segments[curr_dragged_seg].point.y;
        
        
            if(curr_dragged_seg == 68 || curr_dragged_seg == 67 || curr_dragged_seg == 66 || curr_dragged_seg == 65 || curr_dragged_seg == 64 || curr_dragged_seg == 63 || curr_dragged_seg == 61 || curr_dragged_seg == 60 || curr_dragged_seg == 52 || curr_dragged_seg == 51 || curr_dragged_seg == 50 || curr_dragged_seg == 49 || curr_dragged_seg == 48 || curr_dragged_seg == 47 || curr_dragged_seg == 46 || curr_dragged_seg == 45 || curr_dragged_seg == 44 || curr_dragged_seg == 43 || curr_dragged_seg == 42 || curr_dragged_seg == 33 || curr_dragged_seg == 32 || curr_dragged_seg == 31 || curr_dragged_seg == 30 || curr_dragged_seg == 14 || curr_dragged_seg == 13 ){
                    
                    var active_segment_top = path.segments[curr_dragged_seg - 1].point.y - 5;
                    var active_segment_bottom = path.segments[curr_dragged_seg + 1].point.y + 5;
            
            
                    if(active_segment_y >= active_segment_top){
                            path.segments[curr_dragged_seg].point.y = active_segment_top;
                    }else{
                    }
                    if(active_segment_y <= active_segment_bottom){
                            path.segments[curr_dragged_seg].point.y = active_segment_bottom;
                    }else{
                    }
                    console.log("Not me! " + curr_dragged_seg);
               }else{
               
                        var active_segment_top = path.segments[curr_dragged_seg - 1].point.y + 5;
                        var active_segment_bottom = path.segments[curr_dragged_seg + 1].point.y - 5;
                        
                        
                        if(curr_dragged_seg == 54 || curr_dragged_seg == 55 || curr_dragged_seg == 38 || curr_dragged_seg == 37 || curr_dragged_seg == 36 || curr_dragged_seg == 35 || curr_dragged_seg == 26 || curr_dragged_seg == 25 || curr_dragged_seg == 24 || curr_dragged_seg == 23 || curr_dragged_seg == 22 || curr_dragged_seg == 21 || curr_dragged_seg == 20 || curr_dragged_seg == 19 || curr_dragged_seg == 18 || curr_dragged_seg == 17 || curr_dragged_seg == 16 || curr_dragged_seg == 8 || curr_dragged_seg == 7 || curr_dragged_seg == 4 || curr_dragged_seg == 3 || curr_dragged_seg == 2 || curr_dragged_seg == 1){

                                if(active_segment_y <= active_segment_top){
                                        path.segments[curr_dragged_seg].point.y = active_segment_top;
                                }else{
                                }
                                if(active_segment_y >= active_segment_bottom){
                                        path.segments[curr_dragged_seg].point.y = active_segment_bottom;
                                }else{
                                }

                        }else{
                        
                                    var def_segment = def_path.segments[curr_dragged_seg].point.y;
                                    //def_segment = def_segment + 500 + center_pos;
                                    var px_range = 20;
                                    var active_segment = segment.point.y;
            
            
                                if(active_segment > (def_segment + px_range)){
                                        path.segments[curr_dragged_seg].point.y = def_segment + px_range;
                                }else{
                                    if(active_segment < (def_segment - px_range)){
                                        path.segments[curr_dragged_seg].point.y = def_segment - px_range;
                                    }else {
                                        segment.point += event.delta;
                                    }
                                }
                        }
                        
                        
                console.log("Not me! " + curr_dragged_seg);
               }
               
        
        
        var export_path_full = path.exportSVG({asString: true});
        export_path_full.toString();
        var export_path_remove_start = export_path_full.substr(44);
        
        var export_path_final = export_path_remove_start.substr(0, export_path_remove_start.length - 15);
        
        //$("#default_user_path").html(export_path_final);
        
        //alert(export_path_final);
        
        $("#img_path_paper").attr("value", export_path_final);
       
        
       
       
            
            $("#mask_x").attr("value", main_path.position.x);
            $("#mask_y").attr("value", main_path.position.y);
       
            
                
                
                var sholder_left = path.segments[6].point.y - 22;
                var sholder_right = path.segments[62].point.y - 22;
                
                
                
                //alert(sholder_left);
                
                if(sholder_left <= sholder_right){
                    $("#measurement_shoulder_height").attr("value", sholder_left);
                }else{
                    $("#measurement_shoulder_height").attr("value", sholder_right);
                }
                
                
                var bottom_left = path.segments[20].point.y - 66;
                var bottom_right = path.segments[48].point.y - 66;
                                
                //alert(bottom_right);
                
                if(bottom_left <= bottom_right){
                    $("#measurement_hip_height").attr("value", bottom_left);
                }else{
                    $("#measurement_hip_height").attr("value", bottom_right);
                }
                
                
                
		//path.smooth();
		
		
		
		console.log("Me Hit!");
	} else if (curr_view == "zoomed") {
            //alert(this.type);
		path_com.position += event.delta;
		def_path.position += event.delta;
                user_image.position += event.delta;
	}
	

	
}
//var rectangle = new Rectangle(new Point(20, 20), new Size(60, 60));
//var cornerSize = new Size(10, 10);
//var but_zoom = new Shape.Rectangle(rectangle, cornerSize);







function upload(){
var $url=$('#marker_update_url').attr('value');
var value_ar = {
rect_x: $('#p_selected_pic_x').attr('value'),
rect_y: $('#p_selected_pic_y').attr('value'),
rect_height: $('#p_selected_pic_h').attr('value'),
rect_width:$('#p_selected_pic_w').attr('value'),
mask_x: $('#mask_x').attr('value'),
mask_y: $('#mask_y').attr('value'),
marker_json:$('#img_path_json').attr('value'),
default_marker_json:$('#default_marker_json').attr('value'),
default_marker_svg:$('#default_marker_svg').attr('value'),
svg_path:$('#img_path_paper').attr('value')};


 $.ajax({
        type: "POST",
        url: $url,//"http://localhost/cs-ltf-webapp/web/app_dev.php/user/marker/save",
        data: value_ar,  
       success: function(data){//alert(data);
           //alert("Thi viyo");
           //setTimeout(go_to_index,'500');
     console.log(data);    
    },
        failure: function(errMsg) {
            //setTimeout(go_to_index,'500');
            alert(errMsg);
        }
  });

//alert(JSON.stringify(value_ar));


}


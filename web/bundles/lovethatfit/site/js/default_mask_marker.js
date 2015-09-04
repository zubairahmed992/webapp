hitOptions = {
	segments: true,
	stroke: false,
	fill: false,
	tolerance: 22
};

inc_ratio = 1;

var liquid_mask = {
    user_height: parseInt($('#user_height_frm_3').attr('value')),
    def_mask: $("#default_user_path").html(),
    device_type: $("#dv_type").attr("value"),
    def_zoom_ratio: 1,
    scr_empty_top: 26,
    px_per_inch_ratio: function(){return 6.891},
    adjusted_user_mask: function(){return}
}
//alert("asdf");



//dv_gap_top = 26;
//dv_gap_bottom = 32;

//Total height of iPhone5 - gap from top and bottom, devide by max height decided (74)//
if(liquid_mask.device_type == "iphone5"){
    fixed_px_inch_ratio = 6.891;
    
    // adjusting 66.666% value of top empty area ----- 19.5/3*2 = 13
    // 3.83 is 1% value
    adj_btm_fix = 13 + 3.83;
}
if(liquid_mask.device_type == "iphone6"){
    fixed_px_inch_ratio = 8.094;
    
    // adjusting 66.666% value of top empty area ----- 23/3*2 = 15.333
    // 4.49 is 1% value
    adj_btm_fix = 15.333 + 4.49;
}
//////// From JS file
chk_no_img_path = true;

$(document).ready(function() {
    createBlob();
});

function createBlob() {
 
path_data = $("#default_user_path").html();
 
mid_area_path = new Path(path_data);
mid_area_path.opacity = 0.6;


var p_user_height = parseInt($('#user_height_frm_3').attr('value'));


handleOut_41 = new Point(mid_area_path.segments[41].handleOut);
handleOut_40 = new Point(mid_area_path.segments[40].handleOut);
handleIn_29 = new Point(mid_area_path.segments[29].handleIn);
handleIn_30 = new Point(mid_area_path.segments[30].handleIn);


mid_area_path.segments[41].handleOut = 0;
mid_area_path.segments[40].handleOut = 0;
mid_area_path.segments[29].handleIn = 0;
mid_area_path.segments[30].handleIn = 0;

mid_area_path.segments[41].point.y = mid_area_path.segments[40].point.y;
mid_area_path.segments[29].point.y = mid_area_path.segments[28].point.y;




//mid_area_path.segments[29].point.y = mid_area_path.segments[28].point.y;
//mid_area_path.segments[41].point.y -= 50;

//var p_user_height_add = 3.75 * p_user_height / 100;

//p_user_height = p_user_height + p_user_height_add;


                //p_user_height_px = p_user_height * fixed_px_inch_ratio;

//p_user_height_add_px = p_user_height_add * fixed_px_inch_ratio;

                //var p_extra_foot_area = 0;


p_user_height = p_user_height * fixed_px_inch_ratio;

//p_user_height = p_user_height + p_extra_foot_area;

p_user_height = p_user_height * 100 / 430;

p_user_height = p_user_height / 100;

if(chk_no_img_path == true){
            mid_area_path.scale(inc_ratio, p_user_height);            
            
           
   ///////////Check Impect///////////
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
  
  user_shoulder_width = user_shoulder_width * fixed_px_inch_ratio;
  
  
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
    
    
    
    
    
    if(liquid_mask.device_type == "iphone5"){
      mid_area_path.scale(0.750, 0.750);
      //One percent adjustment
      //mid_area_path.scale(1, 1.01);
      mid_area_path.pivot = new Point(mid_area_path.bounds.bottomCenter.x,mid_area_path.bounds.bottomCenter.y);
      mid_area_path.position = new Point(160,403.50 - adj_btm_fix);
      
        mid_area_path.segments[41].point.y += 16.56; 
        mid_area_path.segments[41].handleOut = handleOut_41;
        mid_area_path.segments[40].handleOut = handleOut_40;

        mid_area_path.segments[29].point.y += 16.56;
        mid_area_path.segments[29].handleIn = handleIn_29;
        mid_area_path.segments[30].handleIn = handleIn_30;
    }
    if (liquid_mask.device_type == "iphone6"){
      //mid_area_path.scale(0.9, 0.9);
      mid_area_path.scale(0.748, 0.748);
      //One percent adjustment
      //mid_area_path.scale(1, 1.01);
      mid_area_path.pivot = new Point(mid_area_path.bounds.bottomCenter.x,mid_area_path.bounds.bottomCenter.y);
      mid_area_path.position = new Point(screen.width/2,472 + 2 - adj_btm_fix);
      //alert("6_6_6");
      
        mid_area_path.segments[41].point.y += 19; 
        mid_area_path.segments[41].handleOut = handleOut_41;
        mid_area_path.segments[40].handleOut = handleOut_40;

        mid_area_path.segments[29].point.y += 19;
        mid_area_path.segments[29].handleIn = handleIn_29;
        mid_area_path.segments[30].handleIn = handleIn_30;
    } 
    
    
    


    
    
    
    mid_area_path.selected = true;
    mid_area_path.strokeWidth = 1;
    mid_area_path.strokeColor = new Color(1, 0, 0);
    mid_area_path.opacity = 0.85;

    $("#svg_path_data").attr("value", mid_area_path.pathData);
    //testEcho();
    window.location.href = "svg_path_created";
    return mid_area_path;
}

function testEcho(){
    var nameValue = $("#svg_path_data").attr("value");
    window.JSInterface.doEchoTest(nameValue);
    
}
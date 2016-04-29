hitOptions = {
	segments: true,
	stroke: false,
	fill: false,
	tolerance: 22
};

inc_ratio = 1;

var liquid_mask = {
    user_height: parseFloat($('#user_height_frm_3').attr('value')),
    def_mask: $("#default_user_path").html(),
    device_type: $("#dv_type").attr("value"),
    device_model: $("#dv_model").attr("value"),
    def_zoom_ratio: 1,
    scr_empty_top: 26,
    px_per_inch_ratio: function(){return 6.891},
    adjusted_user_mask: function(){return}
}
//alert("asdf");



//dv_gap_top = 26;
//dv_gap_bottom = 32;

//Total height of iPhone5 - gap from top and bottom, devide by max height decided (74)//
if(liquid_mask.device_type == "iphone5" || liquid_mask.device_type == "android"){

  if(liquid_mask.device_model == "iphone5"){
        fixed_px_inch_ratio = 6.891;
        adj_btm_fix = 0; // Adjustment of iPhone5S
  }
  if(liquid_mask.device_model == "iphone5c"){
        fixed_px_inch_ratio = 6.891;

        // adjusting 66.666% value of top empty area ----- 19.5/3*2 = 13
        //
        //
        // 3.83 is 1% value
        //adj_btm_fix = (13 + 3.83)-3;

        //adj_btm_fix = 13; (Old setting when move mask upside)
        adj_btm_fix = 0;
    }
    if(liquid_mask.device_model == "iphone5s"){
        fixed_px_inch_ratio = 6.891;
        adj_btm_fix = 4; // Adjustment of iPhone5S
    }
}
if(liquid_mask.device_type == "iphone6"){
    if(liquid_mask.device_model == "iphone6"){

        //fixed_px_inch_ratio = 8.094;

        fixed_px_inch_ratio = 6.891;

        // adjusting 66.666% value of top empty area ----- 23/3*2 = 15.333
        // 4.49 is 1% value
        //adj_btm_fix = 15.333 + 4.49;

        //fix adjustment for iphone6 camera view.

        fix_add_btm = 8.5;

        adj_btm_fix = 15.333 + fix_add_btm;

        adj_btm_fix = adj_btm_fix - 19;

        //adj_btm_fix = 0;
    }
    if(liquid_mask.device_model == "iphone6s"){
        fixed_px_inch_ratio = 6.891;

        fix_add_btm = 8.5;

        adj_btm_fix = 15.333 + fix_add_btm;

        adj_btm_fix = adj_btm_fix - 19;

    }
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

//var mask_retake_full = new Path("M144.50834,98.44952c-1.976,8.97343 0.105,10.82146 0.705,12.51622c-3,-1.00645 -7.424,6.2274 -7.118,7.5c1.774,7.28479 6.626,14.18177 6.827,13.73513c0.689,1.85077 3.727,12.02949 4.736,13.91289c0.151,3.36707 -3.993,16.71134 -6.217,18.608c-5.672,4.80384 -6.603,-0.52892 -25.214,5.24262c-10.398,3.60875 -15.71376,13.16336 -19.79376,39.6727c-0.216,3.61486 -5.055,26.0108 -7.413,41.94986c-2.808,25.09705 -1.86,19.59164 -2.425,66.97135c-4.963,13.1787 0.60876,34.61533 13.18976,43.82227c1.369,1.16451 23.889,1.11861 12.124,-21.06819c1.121,-2.11385 -8.818,-15.83981 -11.492,-26.2551c0.415,-1.40618 5.60424,-13.98018 14.72624,-61.88503c2.328,-19.65385 -0.386,-31.20416 0.525,-38.91621c0.314,-1.71107 -1.83324,-13.14742 -0.17724,-13.14742c0.797,0.05914 1.499,9.25657 1.842,13.6872c2.861,18.72897 6.093,29.57772 6.216,44.57762c-0.505,8.80008 -3.786,8.6964 -5.778,14.54035c-2.22,5.8001 -1.505,6.17161 -4.041,19.79695c-1.527,16.70077 -3.925,13.98051 -4.58,29.73805c1.261,17.63992 0.11988,3.95237 3.41788,29.40835c5.515,27.64632 5.8888,24.33189 7.4508,48.24506c-0.007,11.01692 1.913,7.03263 0.824,23.05938c-1.268,19.14093 -2.122,14.25242 -1.488,23.97941c3.1,23.72146 3.74494,29.41075 5.49694,42.74853c0.795,8.34223 5.87028,18.73311 5.52629,33.67183c-0.876,9.52203 -5.3759,16.05292 -7.24791,20.5019c-1.229,4.35415 -5.32,19.97182 13.567,19.93582c2.331,0.03671 6.557,1.1723 8.633,-19.9297c0.266,-5.59106 2.2271,-23.90369 1.0861,-28.00598c1.709,-26.77855 2.9239,-40.24375 4.7249,-60.93668c0.194,-6.8249 -2.09292,-26.98045 -2.31992,-32.33391c3.125,-13.65899 3.31284,-32.64696 4.90284,-66.92036c0.005,-7.48465 0.79908,-20.79898 3.26008,-20.79898c2.362,0 2.83908,10.38062 3.27108,20.75513c0.857,34.30399 1.79084,53.47449 4.72884,66.81635c-0.098,5.24028 -2.25192,25.58651 -2.22892,32.38592c2.082,20.77655 0.7649,41.17852 2.7949,68.04986c-1.328,3.97278 0.0381,15.27216 -0.3349,20.95907c1.893,21.066 6.248,21.58464 8.014,21.52549c17.533,0.059 14.76,-17.3712 12.963,-21.5c-1.758,-4.48875 -4.77991,-12.67973 -5.10091,-22.01822c-0.564,-14.84593 3.06823,-21.27759 3.93923,-29.64226c1.535,-13.46422 2.816,-7.62088 5.754,-31.11801c0.711,-9.93603 3.213,-13.07912 1.881,-32.18233c-1.315,-15.62396 1.609,-9.68623 1.363,-20.38297c1.457,-23.98862 6.2988,-26.37508 11.4718,-53.88578c3.746,-25.23879 3.56788,-12.52641 4.50688,-29.87674c-0.762,-15.89011 -2.94,-14.71369 -4.259,-31.22683c-2.628,-13.58557 -3.773,-18.75688 -6.194,-24.31326c-2.164,-5.96733 -2.431,-7.39322 -3.043,-15.99446c-0.32,-15.2742 2.263,-23.1719 5.201,-41.60414c0.596,-4.60296 2.037,-12.08325 2.68,-12.08325c1.287,-0.00102 -3.17624,20.25388 -3.02824,22.00574c0.838,7.76405 -5.155,10.6043 -2.514,30.40091c7.926,47.91199 13.98624,64.55839 14.44024,65.90033c-2.94,10.20625 -10.773,21.9628 -10.123,24.26632c-8.449,21.86253 9.924,20.28368 11.68,19.13039c14.952,-9.46901 14.98724,-31.70475 11.97324,-44.86918c-0.274,-47.60404 -1.003,-39.29238 -3.571,-63.96013c-3.115,-15.96556 0.097,-33.86048 -0.426,-37.48962c-3.579,-26.64292 -1.65124,-39.78523 -13.40424,-43.18086c-19.662,-5.79398 -23.745,-7.61315 -30.005,-12.27831c-2.991,-2.09448 -3.957,-9.20931 -4.133,-12.43873c1.111,-1.67538 3.537,-13.40873 3.769,-14.22246c1.899,1.82834 5.721,-9.88342 6.108,-11c2.675,-7.92007 -1.747,-6.2325 -2.101,-6c3.409,-5.86842 0.177,-15.4217 -0.168,-16.87071c-3.816,-13.5356 -21.406,-15 -21.406,-15c-17.327,0 -17.159,11.37251 -17.475,12.3188z");
   
   var mask_retake_full = new Path(mid_area_path.pathData);



    if(liquid_mask.device_type == "iphone5"){
      
      scr_height = 568;
      
      mid_area_path.scale(0.750, 0.750);
      //One percent adjustment
      mid_area_path.scale(1, 1.01);
      
      mask_retake_full.scale(1, 1.01);
      
      mid_area_path.pivot = new Point(mid_area_path.bounds.bottomCenter.x,mid_area_path.bounds.bottomCenter.y);
      mid_area_path.position = new Point(160,403.50 - adj_btm_fix);


        

        mask_retake_full.pivot = new Point(mask_retake_full.bounds.bottomCenter.x,mask_retake_full.bounds.bottomCenter.y);
        mask_retake_full.position = new Point(160,scr_height - 48.333);


        mid_area_path.segments[41].point.y += 16.56;
        mid_area_path.segments[41].handleOut = handleOut_41;
        mid_area_path.segments[40].handleOut = handleOut_40;

        mid_area_path.segments[29].point.y += 16.56;
        mid_area_path.segments[29].handleIn = handleIn_29;
        mid_area_path.segments[30].handleIn = handleIn_30;
        
        
        
        
        
        
        
        mask_retake_full.segments[41].point.y += 22; 
        mask_retake_full.segments[41].handleOut = handleOut_41;
        mask_retake_full.segments[40].handleOut = handleOut_40;
 
        mask_retake_full.segments[29].point.y += 22;
        mask_retake_full.segments[29].handleIn = handleIn_29;
        mask_retake_full.segments[30].handleIn = handleIn_30;
        
        
    }
    if (liquid_mask.device_type == "iphone6"){

      //
      //,New fix
      mid_area_path.scale(1.174,1.174);


      //mid_area_path.scale(0.9, 0.9);
      mid_area_path.scale(0.748, 0.748);
      //One percent adjustment
      mid_area_path.scale(1, 1.01);

      mid_area_path.scale(0.952, 0.952);

			mid_area_path.scale(1.081, 1.081);

      mid_area_path.pivot = new Point(mid_area_path.bounds.bottomCenter.x,mid_area_path.bounds.bottomCenter.y);
      mid_area_path.position = new Point(screen.width/2,466);
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
    
    mask_retake_full.selected = true;
    mask_retake_full.strokeWidth = 1;
    mask_retake_full.strokeColor = new Color(1, 0, 0);
    mask_retake_full.opacity = 0.85;
    

    $("#svg_path_data").attr("value", mid_area_path.pathData);
    
    $("#svg_path_data_full").attr("value", mask_retake_full.pathData);
    
    if(liquid_mask.device_type == "android"){
        testEcho();
    }
    window.location.href = "svg_path_created";
    return mid_area_path;
}

function testEcho(){
    var nameValue = $("#svg_path_data").attr("value");
    window.JSInterface.doEchoTest(nameValue);
}

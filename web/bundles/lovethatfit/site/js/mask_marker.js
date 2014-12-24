var hitOptions = {
	segments: true,
	stroke: false,
	fill: false,
	tolerance: 15
};

inc_ratio = 4;

//////// From JS file

croped_img_path = $("#hdn_user_cropped_image_url").attr('value');

chk_no_img_path = false;

if(croped_img_path == "/webapp/web/"){
    chk_no_img_path = true;
}else{
    if(croped_img_path == "/")
    chk_no_img_path = true;
}

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
trans_bg = new Path.Rectangle(new Point(-300, -300), new Size(1000, 1000));
trans_bg.style = {
	fillColor: '#666666',
	stroke: 2,
	strokeColor: '#ffcc00'
};


var p_user_height = parseInt($('#user_height_frm_3').attr('value')) + 3.375;

//var p_user_height = parseInt($('#user_height_frm_3').attr('value'));

p_user_height = p_user_height * 6;
//alert(p_user_height);
var p_user_height_px = p_user_height;

p_user_height = p_user_height * 100 / 450;

p_user_height = p_user_height / 100;


console.log(p_user_height);

//alert(p_user_height);

if(chk_no_img_path == true){

    mid_area_path.scale(inc_ratio,p_user_height*inc_ratio);
    //alert("in side");
   mid_area_path.position = new Point(181,(p_user_height_px * inc_ratio /2)+17);
   
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
  
  user_shoulder_width = user_shoulder_width*6;
  
  //alert(user_shoulder_width);
  
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
  
    
    mid_area_path.scale(inc_ratio,p_user_height * inc_ratio);
    mid_area_path.position = new Point(181,(p_user_height_px * inc_ratio /2)+17);
   }
   else{
    mid_area_path.position = new Point(parseInt($('#mask_x').attr('value')),parseInt($('#mask_y').attr('value')));
    //alert("me fine till here");
    mid_area_path.scale(inc_ratio,p_user_height * inc_ratio);
    mid_area_path.position = new Point(181,(p_user_height_px * inc_ratio /2)+17);
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


var path = new CompoundPath({
    children: [
		trans_bg,
                //default_path,
                mid_area_path
    ],
    fillColor: '#666666',
    selected: true
	//strokeColor: '#ffcc00'
});


path.opacity = 0.6;

     if(chk_no_img_path == true){      
     
        export_svg_data(path);
     
        var default_adjusted_path_data = $("#default_marker_svg").attr("value");
        d_adj_path = new Path(default_adjusted_path_data);
        d_adj_path.strokeColor = 'black';
        d_adj_path.position = new Point(-500,(p_user_height_px * inc_ratio /2)+17);
        d_adj_path.opacity = 0.5;
        
        d_adj_path.scale(inc_ratio,p_user_height * inc_ratio);
     
      

    }else {
    
    
        var default_adjusted_path_data = $("#default_marker_svg").attr("value");
        d_adj_path = new Path(default_adjusted_path_data);
        d_adj_path.strokeColor = 'black';
        d_adj_path.position = new Point(-500,(p_user_height_px * inc_ratio /2)+17);
        d_adj_path.opacity = 0.5;
        
        d_adj_path.scale(2,p_user_height * inc_ratio);
        
    }
      return path;
}

function export_svg_data(path){




var export_path_full = path.exportSVG({asString: true});
        
        
        
        export_path_full.toString();
        var export_path_remove_start = export_path_full.substr(94);
        
        var export_path_final = export_path_remove_start.substr(0, export_path_remove_start.length - 17);
        
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

	if (hitResult) {
                
		path = hitResult.item;
		if (hitResult.type == 'segment') {
			segment = hitResult.segment;
		} else if (hitResult.type == 'stroke') {
			
		}
	}
	//movePath = hitResult.type == 'fill';
	//if (movePath)
		//project.activeLayer.addChild(hitResult.item);
}





function onMouseUp(event){
  if (segment) {
        $("#img_path_json").attr("value", getPathArrayJson());
    }   
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

function onMouseDrag(event) {
	if (segment) {
        
        
          
          
           
            
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
        
        
        
        def_segment = def_segment + 500 + 181;
        //alert(def_segment);
        
        //var def_segment = 200;
            var px_range = 10;
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
                                    //def_segment = def_segment + 500 + 181;
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
	} else if (path) {
		path.position += event.delta;
		def_path.position += event.delta;
	}
	

	
}
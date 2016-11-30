paper.install(window);
var tool_1, tool_2;
$(document).ready(function() {
   paper.setup('canv_mask_makeover');
   
    tool_1 = new Tool();
    tool_1.onMouseDown = onMouseDown;
    tool_1.onMouseDrag = onMouseDrag;
    tool_1.onMouseUp = onMouseUp;

   
//   lyr_user_img = new Layer();
//   project.addLayer(lyr_user_img);
   
   init();
   

   load_user_masks();
   

   
    full_mask.insertAbove(set_circle_in());
    full_mask.insertAbove(set_circle_out());
   
   
   set_action_bar();
   
   console.log(project.layers);
   //paper.project.view.scale(5,5);

});

function set_action_bar(){
    scr1_but_save_icon_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/scr1_save_btn.png";
    scr1_but_save_icon = new Raster(scr1_but_save_icon_url);
    scr1_but_save_icon.position = new Point(920, 1250);
    
    but_move_left_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/move_left.png";
    but_move_left = new Raster(but_move_left_url);
    but_move_left.position = new Point(50, 30);

    but_move_right_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/move_right.png";
    but_move_right = new Raster(but_move_right_url);
    but_move_right.position = new Point(100, 30);

    but_move_up_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/move_up.png";
    but_move_up = new Raster(but_move_up_url);
    but_move_up.position = new Point(150, 30);

    but_move_down_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/move_down.png";
    but_move_down = new Raster(but_move_down_url);
    but_move_down.position = new Point(200, 30);

    but_rotate_left_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/rotate_left.png";
    but_rotate_left = new Raster(but_rotate_left_url);
    but_rotate_left.position = new Point(250, 30);

    but_rotate_right_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/rotate_right.png";
    but_rotate_right = new Raster(but_rotate_right_url);
    but_rotate_right.position = new Point(300, 30);
}

function set_circle_in(){
    circle_in = new paper.Path.Circle({
        center: new paper.Point(10, 10),
        radius: 2,
        fillColor: 'red'
    });
    return circle_in;
}
function set_circle_out(){
    circle_out = new paper.Path.Circle({
        center: new paper.Point(10, 10),
        radius: 2,
        fillColor: 'blue'
    });
    return circle_out;
}

var maskConfig = {
    dv_scr_h: 1280,
    dv_scr_w: 960,
    dv_type: $("#dv_type").attr("value"),
    dv_model: $("#dv_model").attr("value"),
    dv_px_per_inch_ratio: 15.29166666666667,
    globle_pivot:64,
    user_gender: $('#user_gender').attr('value'),
    dv_edit_type : $('#dv_edit_type').attr('value'),
    default_user_mask: function(){
        if(this.dv_edit_type == "edit"){
            default_mask_ratio = this.default_user_mask_height_px / this.user_height_inch;
            required_mask_ratio = 1;
            image_actions_count = JSON.parse($('#image_actions').attr('value'));
            alert(image_actions_count.move_up_down);
            return $('#img_path_paper').attr('value');
        }else{
            image_actions_count = {
                move_up_down: 0,
                move_left_right: 0,
                img_rotate: 0
            };
            default_mask_ratio = this.default_user_mask_height_px / this.user_height_inch;
            required_mask_ratio = this.dv_px_per_inch_ratio / default_mask_ratio;
            return "M101.981,17.702c0,0,16.212-1.743,19.603,15.583c0.009,1.918,0.591,4.187-0.328,14.223c0.353-0.227,4.353-1.519,2.602,6.979c-0.386,1.092-1.204,7.784-3.599,7.163c-0.232,0.796-1.149,7.858-2.257,9.497c0.175,3.158-0.109,11.927,1.122,13.392c6.244,4.563,15.563,10.799,38.178,16.299c11.723,3.321,13.953,20.679,12.016,46.907c0.271,3.717-0.938,27.021,0.25,38.299c-0.091,20.908-2.881,36.976-5.639,58.339c2.09,12.8,6.088,28.382-9.13,37.033c-1.752,1.128-12.064,7.058-7.76-12.266c-0.649-2.253-2.682-19.773,6.588-24.794c-1.603-15.352-7.945-36.479-8.09-52.304c-0.131-9.101,1.919-34.793,2.253-40.301c-0.149-1.713-0.469-15.521-1.753-15.52c-0.64,0-1.658,3.007-2.253,7.509c-2.93,18.029-5.742,32.704-6.258,39.551c0.193,6.076,1.263,9.599,2.504,19.024c0.913,4.851,1.551,10.408,2.753,17.022c1.315,16.151,1.994,8.238,2.754,23.78c0.132,16.513,0.29,21.971-1.002,36.046c-2.335,26.526-2.323,37.381-4.005,52.066c0.397,7.333,0.281,9.988,0.751,15.27c1.1,16.243,1.602,23.199,1.502,29.788c-1.249,17.258-2.744,27.257-4.506,37.297c-0.716,6.73-1.191,11.331-2.002,18.523c-0.215,4.554,2.111,12.407,3.254,15.27c1.105,2.969,15.524,21.59,0.25,19.775c-8.555,0.745-17.71,1.656-19.828-19.66c-0.315-4.494-0.442-14.59,0.804-17.638c0.212-13.266-2.656-37.211-3.003-53.567c1.603-11.838,1.428-14.517,0-31.541c-2.07-8.744-6.422-41.502-8.261-65.583c-0.923-8.426-2.794-22.779-6.258-22.779c-3.316,0-5.085,14.539-5.758,22.779c-1.586,33.524-4.893,52.225-8.01,65.583c-3.362,11.495-0.975,21.611-0.5,31.541c0.637,14.314-2.502,34.992-3.254,53.567c2.302,5.388,1.266,12.054,1.001,17.522c-2.812,25.174-17.2,19.812-19.525,19.775c-14.499,0-2.601-11.707,0-19.775c1.867-4.351,3.266-10.718,3.504-15.27c-0.609-4.773-1.71-10.112-2.503-18.272c-1.473-10.711-3.62-20.224-4.005-37.548c-0.633-9.515-0.013-11.065,1.252-29.788c-0.082-4.662,0.744-10.585,1.001-15.27c-1.725-18.468-3.678-35.371-4.005-52.317c-1.537-19.225-0.578-23.548-1.252-36.045c1.071-10.49,2.398-18.042,3.004-23.53c1.027-4.149,1.374-10.431,2.253-17.022c1.486-6.384,2.25-10.417,2.753-19.024c-1.207-14.005-2.904-21.231-5.757-39.551c-0.342-4.334-0.957-7.451-1.752-7.509c-1.652,0-1.439,13.846-1.752,15.52c-0.909,7.543,3.824,21.077,1.502,40.301c-0.754,15.4-7.596,42.013-7.76,52.317c8.675,5.266,7.376,22.713,6.258,24.782c4.142,19.615-6.896,13.404-8.26,12.266c-10.879-4.833-13.711-24.156-8.761-37.047c-1.438-12.051-5.054-34.861-5.507-58.324c0.601-12.921,0.201-21.329-0.25-38.299c-0.103-23.176-1.944-41.611,12.266-47.06c19.647-3.392,32.891-11.322,38.549-16.021c1.006-1.289,0.901-10.224,0.751-13.517c-1.006-1.842-1.007-7.354-1.752-9.512c-0.433,0.554-2.352,0-4.005-7.259c-0.537-2.463,0.846-7.278,2.503-6.758c-0.599-1.657-0.975-9.218,0-14.519C85.942,31.674,89.597,17.794,101.981,17.702z"
        }
    },
    default_user_mask_height_px: 430,
    adjusted_user_mask: function(){return},
    user_img_url: $("#hdn_user_cropped_image_url").attr('value'),
    user_height_inch: parseInt($('#user_height_frm_3').attr('value')),
    user_height_px: parseInt($('#user_height_frm_3').attr('value')) * 15.29166666666667, 
    head_percent: 12,
    neck_percent: 4,
    torso_percent: 42,
    inseam_percent:42,
    arm_percent:46,
    toe_shape_px: 42,
    curr_path_prefix: $("#hdn_serverpath").attr("value")
}


var active_items = {
    segment: -1,
    drag: false,
    cir_in: false,
    cir_out: false
}
var recent_items = {
    mask: []
}
hitOptions = {
    segments: true,
    stroke: false,
    fill: false,
    tolerance: 6
};
function init(){
    var user_img_path = maskConfig.user_img_url;
    user_image = new Raster(user_img_path);
    user_image.on('load', function() {
        user_image.position = new Point(maskConfig.dv_scr_w/2,maskConfig.dv_scr_h/2);
        //preset_user_image(move_up_down, move_left_right, img_rotate);
//        if(old_account_img){}else{
//            preset_user_image(parseInt(image_actions_count.move_up_down),parseInt(image_actions_count.move_left_right),parseFloat(image_actions_count.img_rotate));
//        }
    });
//console.log(paper.project.layers[1]);
}
function load_user_masks(){    
    var full_mask_path = maskConfig.default_user_mask();    
    full_mask = new Path(full_mask_path);
    
    full_mask.segments[41].handleIn = 0;
    full_mask.segments[41].handleOut = 0;
    
    full_mask.segments[40].handleIn = 0;
    full_mask.segments[40].handleOut = 0;
    
    full_mask.segments[29].handleIn = 0;
    full_mask.segments[29].handleOut = 0;
    
    full_mask.segments[30].handleIn = 0;
    full_mask.segments[30].handleOut = 0;

    full_mask.segments[42].handleIn = 0;
    full_mask.segments[42].handleOut = 0;

    full_mask.segments[28].handleIn = 0;
    full_mask.segments[28].handleOut = 0;

    full_mask.segments[41].point.y = full_mask.segments[40].point.y;
    full_mask.segments[42].point.y = full_mask.segments[40].point.y;
    full_mask.segments[29].point.y = full_mask.segments[40].point.y;
    full_mask.segments[28].point.y = full_mask.segments[40].point.y;
    full_mask.segments[30].point.y = full_mask.segments[40].point.y;
    
    
    full_mask.style = {
        strokeColor: 'white',
        strokeWidth: 1
    };
    full_mask.opacity = 0.1;
    full_mask.selected = true;
    full_mask.scale(required_mask_ratio);
    full_mask.pivot = new Point(full_mask.bounds.bottomCenter.x,full_mask.bounds.bottomCenter.y);
    full_mask.position = new Point(maskConfig.dv_scr_w/2 + 4,maskConfig.dv_scr_h - maskConfig.globle_pivot);
    
    
    full_mask.segments[41].point.y += maskConfig.toe_shape_px; 
    full_mask.segments[41].handleOut = new Point(-maskConfig.toe_shape_px,0);
    full_mask.segments[40].handleOut = new Point(0,maskConfig.toe_shape_px);

    full_mask.segments[29].point.y += maskConfig.toe_shape_px;
    full_mask.segments[29].handleIn = new Point(maskConfig.toe_shape_px,0);
    full_mask.segments[30].handleIn = new Point(0,maskConfig.toe_shape_px);
   
    //view.update([true]);
    //console.log(paper.project.layers);
    
    
}

function getPathArrayJson(){
    var mp_array=[];
    for(var i = 0; i < full_mask.segments.length; i++) {
        mp_array.push([full_mask.segments[i].point.x * 2, full_mask.segments[i].point.y * 2]);

        console.log(full_mask.segments[i].point.x * 2 + " ::: "+full_mask.segments[i].point.y * 2);
    };

    return JSON.stringify(mp_array);
}

function onMouseDown(event) {
    var hitResult = paper.project.hitTest(event.point, hitOptions);
    
    console.log(hitResult);
    if(hitResult.type == "segment"){
        
        if (event.modifiers.shift) {
            
        }
        
        active_items.drag = true;
        
        if(hitResult.segment.point != active_items.segment.point){
            active_items.segment.selected = false;
        }
        for(i=0; i<full_mask.segments.length; i++){
            if(full_mask.segments[i].point == hitResult.segment.point){
                
                active_items.segment = full_mask.segments[i];
                //active_items.segment.selected = true;                
                
                //circle_in.position = new paper.Point(paper.full_mask.segments[i].point + paper.full_mask.segments[i].handleIn);
                //circle_out.position = new paper.Point(paper.full_mask.segments[i].point + paper.full_mask.segments[i].handleOut);
                console.log(active_items.segment);
                
            }
        }
        if(hitResult.item == circle_in){
            active_items.drag = false;
            active_items.cir_out = false;
            active_items.cir_in = true;
        }
        if(hitResult.item == circle_out){
            active_items.drag = false;            
            active_items.cir_in = false;
            active_items.cir_out = true;            
        }
    }else {
        if(hitResult.type == 'stroke') {
            //alert(hitResult.location);
            var hit_location = hitResult.location;
            full_mask.insert(hit_location.index + 1, event.point);}
        }
        
        
    if(hitResult.item == scr1_but_save_icon){
      
                    $("#img_path_json").attr("value", getPathArrayJson());

                    $("#page_wrap").fadeIn(160);
                    //image_export_layer = new Layer();
                    //image_export_layer.activate();
                    //image_export_layer.addChild(user_image);
                    
                    upload();
                    
                    //to_image();

                    //show_loader();

                    //setTimeout(function(){ to_image(); }, 500);
                    //$("#me_button").trigger( "click" );
                    //alert("Browser Support: " + supportsToDataURL());

                    //
                    //
                    //
                    //alert("Save Button Tap");
                    //$("#scr1_but_save_mask").trigger( "click" );


                    console.log("scr1_but_save_icon");

                
            }
        
       
    
    if(hitResult.item == but_move_left){
        user_image.position.x -= 1;
        //image_actions_count.move_left_right -= 1;
    }
    if(hitResult.item == but_move_right){
        user_image.position.x += 1;
        image_actions_count.move_left_right += 1;
    }
    if(hitResult.item == but_move_up){
        user_image.position.y -= 1;
        image_actions_count.move_up_down -= 1;
    }
    if(hitResult.item == but_move_down){
        user_image.position.y += 1;
        image_actions_count.move_up_down += 1;
    }
    if(hitResult.item == but_rotate_left){
        user_image.rotate(-0.1);
        image_actions_count.img_rotate += -0.1;
    }
    if(hitResult.item == but_rotate_right){
        user_image.rotate(0.1);
        image_actions_count.img_rotate += 0.1;

    }
    
}


function onMouseDrag(event) {
    if(active_items.drag){
        active_items.segment.point = event.point;
        circle_in.position = event.point;
        circle_out.position = event.point;
    }
    if(active_items.cir_in){
        active_items.cir_out = false;
        circle_in.position = event.point;
        active_items.segment.handleIn = new Point(event.point);
    }
    if(active_items.cir_out){
        active_items.cir_in = false;
        circle_out.position = event.point;
        active_items.segment.handleOut = new Point(event.point);
    }
    
}
function onMouseUp(event) {
    active_items.drag = false;
    active_items.cir_in = false;
    active_items.cir_out = false;
}

function upload(){
//    $("#img_path_json").attr("value",getPathArrayJson());
//    
//    var $url=$('#marker_update_url').attr('value');
//    var value_ar = {
//        auth_token:$('#user_auth_token').attr('value'),
//        mask_x: $('#mask_x').attr('value'),
//        mask_y: $('#mask_y').attr('value'),
//        marker_json:$('#img_path_json').attr('value'),
//        svg_path:$('#img_path_paper').attr('value')};
//    
//    
//    if(old_account_img){
//        $('#image_actions').attr('value',"");
//    }else{
//        $('#image_actions').attr('value',JSON.stringify(image_actions_count));
//    }
    $('#img_path_paper').attr('value', full_mask.pathData);
    
    var $url=$('#marker_update_url').attr('value');

    var value_ar = {
        archive_id:$('#hdn_archive_id').attr('value'),
        auth_token:$('#user_auth_token').attr('value'),
        rect_x: $('#p_selected_pic_x').attr('value'),
        rect_y: $('#p_selected_pic_y').attr('value'),
        rect_height: $('#p_selected_pic_h').attr('value'),
        rect_width:$('#p_selected_pic_w').attr('value'),
        mask_x: $('#mask_x').attr('value'),
        mask_y: $('#mask_y').attr('value'),
        marker_json:$('#img_path_json').attr('value'),
        default_marker_json:$('#default_marker_json').attr('value'),
        default_marker_svg:$('#default_marker_svg').attr('value'),
        shoulder_height: 160,
        hip_height: 337,
        svg_path:$('#img_path_paper').attr('value'),
        image_actions:$('#image_actions').attr('value')};
console.log(value_ar);
    $.ajax({
        type: "POST",
        url: "$url",//"http://localhost/cs-ltf-webapp/web/app_dev.php/user/marker/save",
        data: value_ar,
        success: function(data){
            alert(data);
        },
        failure: function(errMsg) {
            alert(errMsg);
        }
    });

//alert(JSON.stringify(value_ar));


}

//main_layer = new Layer();
//main_layer.activate();

paper.install(window);
var dom_event_interaction_s1;
$(document).ready(function() {
   paper.setup('canv_mask_makeover');
   
   dom_event_interaction_s1 = new Tool();
   dom_event_interaction_s1.onMouseDown = onMouseDown;
   dom_event_interaction_s1.onMouseDrag = onMouseDrag;
   dom_event_interaction_s1.onMouseUp = onMouseUp;
   dom_event_interaction_s1.onKeyDown = onKeyDown;
   dom_event_interaction_s1.onKeyUp = onKeyUp;
   
   lyr_ref_rect = new Layer();
   lyr_ref_rect.activate();
   canvas_ref_rect();
   
   lyr_stage_coor = new Layer();
   lyr_stage_coor.activate();
   canvas_area_hldr();
   
   lyr_area_hldr = new Layer();
   lyr_area_hldr.activate();
   
   init();
   load_user_masks();
   full_mask.insertAbove(set_circle_in());
   full_mask.insertAbove(set_circle_out());   
   
   lyr_misc = new Layer();
   lyr_misc.activate();
   
   console.log("lyr_area_hldr: "+lyr_area_hldr.position);
   console.log("lyr_stage_coor: "+lyr_stage_coor.position);
   console.log("canvas_area_hldr: "+canvas_area_hldr.position);
   console.log("canv_ref_rect: "+canv_ref_rect.position);  
});
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
            
            console.log(image_actions_count);
            
            return $('#img_path_paper').attr('value');
        }else{
            image_actions_count = {
                move_up_down: 0,
                move_left_right: 0,
                img_rotate: 0
            };
            default_mask_ratio = this.default_user_mask_height_px / this.user_height_inch;
            required_mask_ratio = this.dv_px_per_inch_ratio / default_mask_ratio;
            //alert($("#default_user_path").html());
            //return "M101.981,17.702c0,0,16.212-1.743,19.603,15.583c0.009,1.918,0.591,4.187-0.328,14.223c0.353-0.227,4.353-1.519,2.602,6.979c-0.386,1.092-1.204,7.784-3.599,7.163c-0.232,0.796-1.149,7.858-2.257,9.497c0.175,3.158-0.109,11.927,1.122,13.392c6.244,4.563,15.563,10.799,38.178,16.299c11.723,3.321,13.953,20.679,12.016,46.907c0.271,3.717-0.938,27.021,0.25,38.299c-0.091,20.908-2.881,36.976-5.639,58.339c2.09,12.8,6.088,28.382-9.13,37.033c-1.752,1.128-12.064,7.058-7.76-12.266c-0.649-2.253-2.682-19.773,6.588-24.794c-1.603-15.352-7.945-36.479-8.09-52.304c-0.131-9.101,1.919-34.793,2.253-40.301c-0.149-1.713-0.469-15.521-1.753-15.52c-0.64,0-1.658,3.007-2.253,7.509c-2.93,18.029-5.742,32.704-6.258,39.551c0.193,6.076,1.263,9.599,2.504,19.024c0.913,4.851,1.551,10.408,2.753,17.022c1.315,16.151,1.994,8.238,2.754,23.78c0.132,16.513,0.29,21.971-1.002,36.046c-2.335,26.526-2.323,37.381-4.005,52.066c0.397,7.333,0.281,9.988,0.751,15.27c1.1,16.243,1.602,23.199,1.502,29.788c-1.249,17.258-2.744,27.257-4.506,37.297c-0.716,6.73-1.191,11.331-2.002,18.523c-0.215,4.554,2.111,12.407,3.254,15.27c1.105,2.969,15.524,21.59,0.25,19.775c-8.555,0.745-17.71,1.656-19.828-19.66c-0.315-4.494-0.442-14.59,0.804-17.638c0.212-13.266-2.656-37.211-3.003-53.567c1.603-11.838,1.428-14.517,0-31.541c-2.07-8.744-6.422-41.502-8.261-65.583c-0.923-8.426-2.794-22.779-6.258-22.779c-3.316,0-5.085,14.539-5.758,22.779c-1.586,33.524-4.893,52.225-8.01,65.583c-3.362,11.495-0.975,21.611-0.5,31.541c0.637,14.314-2.502,34.992-3.254,53.567c2.302,5.388,1.266,12.054,1.001,17.522c-2.812,25.174-17.2,19.812-19.525,19.775c-14.499,0-2.601-11.707,0-19.775c1.867-4.351,3.266-10.718,3.504-15.27c-0.609-4.773-1.71-10.112-2.503-18.272c-1.473-10.711-3.62-20.224-4.005-37.548c-0.633-9.515-0.013-11.065,1.252-29.788c-0.082-4.662,0.744-10.585,1.001-15.27c-1.725-18.468-3.678-35.371-4.005-52.317c-1.537-19.225-0.578-23.548-1.252-36.045c1.071-10.49,2.398-18.042,3.004-23.53c1.027-4.149,1.374-10.431,2.253-17.022c1.486-6.384,2.25-10.417,2.753-19.024c-1.207-14.005-2.904-21.231-5.757-39.551c-0.342-4.334-0.957-7.451-1.752-7.509c-1.652,0-1.439,13.846-1.752,15.52c-0.909,7.543,3.824,21.077,1.502,40.301c-0.754,15.4-7.596,42.013-7.76,52.317c8.675,5.266,7.376,22.713,6.258,24.782c4.142,19.615-6.896,13.404-8.26,12.266c-10.879-4.833-13.711-24.156-8.761-37.047c-1.438-12.051-5.054-34.861-5.507-58.324c0.601-12.921,0.201-21.329-0.25-38.299c-0.103-23.176-1.944-41.611,12.266-47.06c19.647-3.392,32.891-11.322,38.549-16.021c1.006-1.289,0.901-10.224,0.751-13.517c-1.006-1.842-1.007-7.354-1.752-9.512c-0.433,0.554-2.352,0-4.005-7.259c-0.537-2.463,0.846-7.278,2.503-6.758c-0.599-1.657-0.975-9.218,0-14.519C85.942,31.674,89.597,17.794,101.981,17.702z";
            
            return overall_mask();
            
        }
    },
    default_user_mask_height_px: 430,
    adjusted_user_mask: function(){return},
    user_img_url: $("#hdn_user_cropped_image_url").attr('value'),
    user_height_inch: parseFloat($('#user_height_frm_3').attr('value')),
    user_height_px: parseFloat($('#user_height_frm_3').attr('value')) * 15.29166666666667, 
    head_percent: 12,
    neck_percent: 4,
    torso_percent: 42,
    inseam_percent:42,
    arm_percent:46,
    toe_shape_px: 42,
    curr_path_prefix: $("#hdn_serverpath").attr("value")
}

/////////////////////////////// from mask generation pca file ///////////////////////////

function overall_mask(){
    pre_init();
    function pre_init(){
        var liquid_mask = {
            //user_height: parseFloat($('#user_height_frm_3').attr('value')),
            user_height: 72,
            def_mask: $("#default_user_path").html(),
            dm_body_parts_details_json: JSON.parse($("#dm_body_parts_details_json").attr('value')),
            device_type: $("#dv_type").attr("value"),
            device_model: $("#dv_model").attr("value"),
            adjusted_user_mask: function(){return}
        }
        console.log('Jason Data'+ liquid_mask);
        $('#user_bust_px').attr('value',parseFloat(liquid_mask.dm_body_parts_details_json.bust_px));    
        $('#user_waist_px').attr('value',parseFloat(liquid_mask.dm_body_parts_details_json.waist_px));
        $('#user_hip_px').attr('value',parseFloat(liquid_mask.dm_body_parts_details_json.hip_px));

    px_per_inch_ratio = px_per_in_ratio(liquid_mask);
    init(liquid_mask);
    set_arm_rgt();
    }

    hitOptions = {
            segments: true,
            stroke: false,
            fill: true,
            tolerance: 6
    };


    //////// Pixel per inch Ratio based on device type and model
    function px_per_in_ratio(liquid_mask){
        if(liquid_mask.device_type == "iphone6"){
            return 7.958;
        }
    }
    function init(liquid_mask){
        adjusted_mask_height_px = parseFloat(liquid_mask.user_height) * px_per_inch_ratio;
        path_data = "M79.455,0c7.108,0,20.321,5.66,20.857,20.108c0,3.721,0.007,6.054,0,6.867c-0.011,1.091,2.608-2.602,2.608,5.743c0,4.296-2.366,9.903-5.054,8.937c-1.025,2.34-5.504,13.31-5.444,14.491c0.058,1.253,0.964,10.473,1.375,10.708c1.485,2.146,20.987,7.739,26.146,8.623c14.09,2.414,19.705,14.798,21.612,39.95c0.698,5.082,1.888,37.947,5.508,47.839c3.194,8.364,7.201,57.149,6.448,62.805c-0.632,4.735,15.809,25.89-5.905,47.022c-8.116,7.898-3.771-7.298-4.779-11.292c-7.34-14.66-3.574-28.72-1.302-34.74c0.724-2.605-14.161-41.228-17.126-54.275c-9.765-42.965-2.553-41.912,1.179-57.359c0.727-6.97-0.686-15.036-0.832-15.103c-0.164-0.067,1.768,6.526,0.832,14.117c-2.331,9.806-8.679,22.954-8.679,37.221c0,4.536,1.82,20.22,3.544,24.463c0.583,1.435,2.229,7.75,2.811,9.936c0.968,3.636,2.135,12.348,4.224,20.397c1.278,7.491,5.634,23.716,1.352,46.621c-1.565,10.619-10.74,31.778-13.684,57.761c-0.65,4.184-1.39,11.374-1.116,17.05c0.274,6.275,1.764,18.013,1.496,22.291c-0.372,11.08-12.044,57.033-12.867,63.681c-0.536,4.31,1.666,12.577,2.218,15.636c0.688,3.822,4.708,19.182,6.001,20.477c-0.603,0.059-10.042,0-11.797,0c-1.736,0-11.503,0-11.799,0c0.009-1.847,0.531-33.07,1.036-36.05c0.9-7.582-4.277-49.957-3.646-63.958c0.165-3.204,0.665-15.831-0.105-22.079c-3.317-18.44-3.884-34.317-5.089-74.925c-0.142-2.823,0.125-14.02-0.024-14.02c-0.151,0,0.092,11.197-0.05,14.02c-1.205,40.607-1.772,56.484-5.089,74.925c-0.77,6.248-0.271,18.875-0.105,22.079c0.631,14.001-4.546,56.376-3.646,63.958c0.505,2.98,1.027,34.204,1.036,36.05c-0.296,0-10.063,0-11.799,0c-1.756,0-11.194,0.059-11.797,0c1.293-1.295,5.313-16.654,6.001-20.477c0.552-3.059,2.754-11.326,2.218-15.636c-0.823-6.648-12.495-52.602-12.867-63.681c-0.267-4.278,1.222-16.016,1.496-22.291c0.274-5.676-0.466-12.866-1.115-17.05c-2.944-25.982-12.119-47.142-13.684-57.761c-4.283-22.904,0.074-39.129,1.352-46.621c2.089-8.049,3.256-16.761,4.224-20.397c0.582-2.187,2.228-8.501,2.812-9.936c1.724-4.243,3.544-19.927,3.544-24.463c0-14.267-6.349-27.415-8.68-37.221c-0.936-7.591,0.995-14.185,0.832-14.117c-0.146,0.067-1.559,8.133-0.832,15.103c3.732,15.447,10.943,14.394,1.179,57.359c-2.965,13.047-17.85,51.67-17.126,54.275c2.272,6.021,6.038,20.08-1.302,34.74c-1.008,3.994,3.337,19.19-4.779,11.292c-21.712-21.133-5.272-42.287-5.904-47.022c-0.753-5.656,3.253-54.441,6.447-62.805c3.621-9.892,4.811-42.757,5.509-47.839c1.907-25.151,7.523-37.536,21.613-39.95c5.158-0.884,24.66-6.477,26.145-8.623c0.411-0.235,1.316-9.455,1.375-10.708c0.061-1.181-4.418-12.151-5.444-14.491c-2.688,0.966-5.054-4.641-5.054-8.937c0-8.344,2.619-4.652,2.608-5.743c-0.007-0.813,0-3.146,0-6.867C59.107,5.66,72.463,0,79.455,0z";
        full_scr_mask = new Path(path_data);
        def_mask_height = full_scr_mask.bounds.height;
        adjusted_mask_height_px = (adjusted_mask_height_px * 100) / 450;
        full_scr_mask.scale(init_scale_ratio(),adjusted_mask_height_px/100);
    function init_scale_ratio(){
            var def_scale = full_scr_mask.segments[17].point.x - full_scr_mask.segments[53].point.x;
            var init_req_scale = parseFloat(liquid_mask.dm_body_parts_details_json.bust_px) * 0.5213;

            var set_init_scale_ratio = init_req_scale / def_scale;

            return set_init_scale_ratio;
        }
    function implement_body_part_px(dm_body_part_name,dm_seg_1,dm_seg_2,user_body_part_ele_id){
        var dm_body_part = full_scr_mask.segments[dm_seg_1].point.x - full_scr_mask.segments[dm_seg_2].point.x;
        var user_body_part_px = parseFloat($("#"+user_body_part_ele_id).attr("value"));

        user_body_part_px = user_body_part_px * 0.5213;

        var body_part_diff = Math.abs(dm_body_part - user_body_part_px);

        full_scr_mask.segments[dm_seg_1].point.x += body_part_diff/2;
        full_scr_mask.segments[dm_seg_2].point.x -= body_part_diff/2;
    }

    // Bust adjustment 
    implement_body_part_px("bust",17,53,"user_bust_px");
    implement_body_part_px("waist",18,52,"user_waist_px");
    implement_body_part_px("hip",21,49,"user_hip_px");

    // iPhone6 retake screen settings /// available screen for mask 633.5 in camera view.
    full_scr_mask.pivot = new Point(full_scr_mask.bounds.bottomCenter);
    full_scr_mask.position = new Point(187.5,667 - 33.5);


    // Arms settings
    rgt_arm_ref = new Path({});
    lft_arm_ref = new Path({});



    rgt_arm_ref = get_path_seg(rgt_arm_ref, full_scr_mask, 7, 16);

    lft_arm_ref = get_path_seg(lft_arm_ref, full_scr_mask, 55, 64);


    //rgt_arm_ref.position.x += 60;
    rgt_arm_ref.selected = true;
    lft_arm_ref.selected = true;


    //rgt_arm_ref.segments[7].point.x += 20;

    pivot_path = new Path();
    pivot_path.add(rgt_arm_ref.segments[0]);
    rgt_arm_ref.pivot = new Point(pivot_path.position);

    pivot_path_lft = new Path();
    pivot_path_lft.add(lft_arm_ref.segments[8]);
    lft_arm_ref.pivot = new Point(pivot_path_lft.position);

    }

    function get_path_seg(ref_part_obj, obj_path_for_ref, int_seg_num, end_seg_num){

        ref_part_obj.removeSegments();

        for(var i = int_seg_num; i < end_seg_num; i++) {
            ref_part_obj.add(obj_path_for_ref.segments[i]);
        }

        return ref_part_obj;
    }

    function set_path_seg(ref_part_obj, obj_path_for_ref, int_seg_num, end_seg_num){
        ind = 0;
        for(var i = int_seg_num; i < end_seg_num; i++) {
            //debugger;
            obj_path_for_ref.segments[i].point = ref_part_obj.segments[ind].point;
            obj_path_for_ref.segments[i].handleIn = ref_part_obj.segments[ind].handleIn;
            obj_path_for_ref.segments[i].handleOut = ref_part_obj.segments[ind].handleOut;
            ind++;
        }

        return ref_part_obj;
    }
    function set_arm_inner_to_bust(){
    full_scr_mask.segments[55].point.x = full_scr_mask.segments[53].point.x;
    full_scr_mask.segments[15].point.x = full_scr_mask.segments[17].point.x; 
    }
    function set_arm_rgt(){
        if(rgt_arm_ref.segments[7].point.x <= full_scr_mask.segments[21].point.x + 2) {
            console.log(rgt_arm_ref.segments[7].point.x +" <= "+ full_scr_mask.segments[19].point.x);

            rgt_arm_ref.rotate(-0.5);
            lft_arm_ref.rotate(0.5);
            view.update();
            set_arm_rgt();
        }else{
            rgt_arm_ref = set_path_seg(rgt_arm_ref, full_scr_mask, 7, 16);
            lft_arm_ref = set_path_seg(lft_arm_ref, full_scr_mask, 55, 64);


            set_arm_inner_to_bust();
            set_camera_mask();
        }
        rgt_arm_ref.visible = false;
        lft_arm_ref.visible = false;

        view.update();
    }

    function set_camera_mask(){
        camera_scr_mask = new Path(full_scr_mask.pathData);
        camera_scr_mask.pivot = new Point(camera_scr_mask.bounds.bottomCenter);
        
        // Setting size
        camera_scr_mask = camera_scr_mask.scale(1.919);
        
        //camera_scr_mask.selected = true;

        /// iPhone6 camera screen settings /// available screen for mask 475.125 in camera view.
//        camera_scr_mask.position = new Point(187.5,500.25 - 25.125);
//        camera_scr_mask.visible = false;

        full_scr_mask.selected = false;
        full_scr_mask.visible = false;

//        $("#svg_path_data").attr("value", camera_scr_mask.pathData);
//        $("#svg_path_data_full").attr("value", full_scr_mask.pathData);

//        $("#side_svg_path_data").attr("value", camera_scr_mask.pathData);
//        $("#side_svg_path_data_full").attr("value", full_scr_mask.pathData);
//
//        $("#back_svg_path_data").attr("value", camera_scr_mask.pathData);
//        $("#back_svg_path_data_full").attr("value", full_scr_mask.pathData);
        view.update();

    }
    return camera_scr_mask.pathData;
 
 }
 
 /////////////////////////////////////////////////////



function canvas_area_hldr(){
    canvas_area_hldr = new Path.Rectangle({
        from: [0, 0],
        to: [960, 1280]
    });
    canvas_area_hldr.style = {
        fillColor: 'green',
        opacity: 0.5
    };
    canvas_area_hldr.pivot = new Point(480,640);
    canvas_area_hldr.position = new Point(480,640);
}
function canvas_ref_rect(){
    canv_ref_rect = new Path.Rectangle({
        from: [0, 0],
        to: [960, 1280]
    });
    canv_ref_rect.style = {
        fillColor: 'red',
        opacity: 0.5
    };
    canv_ref_rect.pivot = new Point(480,640);
    canv_ref_rect.position = new Point(480,640);
}
function set_action_bar(){
    scr1_but_save_icon_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/scr1_save_btn.png";
    scr1_but_save_icon = new Raster(scr1_but_save_icon_url);
    scr1_but_save_icon.position = new Point(920, 1250);
    
    but_move_left_url = maskConfig.curr_path_prefix + "bundles/lovethatfit/site/images/move_left.png";
    but_move_left = new Raster(but_move_left_url);
    but_move_left.position = new Point(0,0);

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
change_x_pos_diff = 0;
change_y_pos_diff = 0;
function set_circle_in(){
    circle_in = new paper.Path.Circle({
        center: new paper.Point(10, 10),
        radius: 2,
        fillColor: 'red',
        opacity: 1
    });
    return circle_in;
}
function set_circle_out(){
    circle_out = new paper.Path.Circle({
        center: new paper.Point(10, 10),
        radius: 2,
        fillColor: 'blue',
        opacity: 1
    });
    return circle_out;
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
current_status ={
    control_indi: false,
    min_zoom_level: 1,
    max_zoom_level: 2,
    zoom_level: 1,
    zoom: false
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
        user_image.position = new Point(480,640);
        preset_user_image(parseFloat(image_actions_count.move_up_down),parseFloat(image_actions_count.move_left_right),parseFloat(image_actions_count.img_rotate));
    });
}
function preset_user_image(move_up_down, move_left_right, img_rotate) {
    user_image.position.y = user_image.position.y += move_up_down;
    user_image.position.x = user_image.position.x += move_left_right;
    user_image.rotate(img_rotate); // -0.1 for left, 0.1 for right
}

function load_user_masks(){
        full_mask = new Path(maskConfig.default_user_mask());

    if(maskConfig.dv_edit_type != "edit"){   
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

    //Setting Position    
        //full_mask.scale(required_mask_ratio);

        full_mask.pivot = new Point(full_mask.bounds.bottomCenter.x,full_mask.bounds.bottomCenter.y);
        full_mask.position = new Point(maskConfig.dv_scr_w/2 + 4,maskConfig.dv_scr_h - maskConfig.globle_pivot);

        $("#mask_x").attr("value", full_mask.pivot);
        $("#mask_y").attr("value", full_mask.position);

//        full_mask.segments[41].point.y += maskConfig.toe_shape_px; 
//        full_mask.segments[41].handleOut = new Point(-maskConfig.toe_shape_px,0);
//        full_mask.segments[40].handleOut = new Point(0,maskConfig.toe_shape_px);
//
//        full_mask.segments[29].point.y += maskConfig.toe_shape_px;
//        full_mask.segments[29].handleIn = new Point(maskConfig.toe_shape_px,0);
//        full_mask.segments[30].handleIn = new Point(0,maskConfig.toe_shape_px);
    }else{
        full_mask.scale(required_mask_ratio);
        $("#mask_x").attr("value", full_mask.pivot);
        full_mask.pivot = new Point($("#mask_x").attr("value"));
        full_mask.position = new Point($("#mask_y").attr("value"));
    }
    full_mask.style = {
        strokeColor: 'white',
        strokeWidth: 1
    };
    full_mask.opacity = 0.1;
    full_mask.selected = true;
}
function remove_index_numbers(){
    $(".index_note").remove();
}
function show_index_numbers(){
    for(a=0; a<full_mask.segments.length; a++){
    $(".canv_hldr_2").prepend('<div id="i_note_'+a+'" class="index_note">' + a +'</div>');
        $("#i_note_" + a).css('position','absolute');
        $("#i_note_" + a).css('z-index','1000');
        $("#i_note_" + a).css('background-color','#ffcc00');
        $("#i_note_" + a).css('font-size','10px');
        $("#i_note_" + a).css('top',full_mask.segments[a].point.y + 50);
        $("#i_note_" + a).css('left', full_mask.segments[a].point.x + 100);
    }
}
function set_out_update(){
    $('#image_actions').attr('value',JSON.stringify(image_actions_count));
    //alert($('#image_actions').attr('value'));
    view.update();
}
function move_left(){
    if(current_status.zoom){
        user_image.position.x -= 0.5;
        image_actions_count.move_left_right -= 0.5;
    }else{
        user_image.position.x -= 1;
        image_actions_count.move_left_right -= 1;
    }
    set_out_update();
}
function move_right(){
    if(current_status.zoom){
        user_image.position.x += 0.5;
        image_actions_count.move_left_right += 0.5;
    }else{
        user_image.position.x += 1;
        image_actions_count.move_left_right += 1;
    }
    set_out_update();
}
function move_up(){
    if(current_status.zoom){
        user_image.position.y -= 0.5;
        image_actions_count.move_up_down -= 0.5;
    }else{
        user_image.position.y -= 1;
        image_actions_count.move_up_down -= 1;
    }
    set_out_update();
}
function move_down(){
    if(current_status.zoom){
        user_image.position.y += 0.5;
        image_actions_count.move_up_down += 0.5;
    }else{
        user_image.position.y += 1;
        image_actions_count.move_up_down += 1;
    }
    set_out_update();
}
function rotate_left(){
    if(current_status.zoom){
        user_image.rotate(-0.05);
        image_actions_count.img_rotate += -0.05;
    }else{
        user_image.rotate(-0.1);
        image_actions_count.img_rotate += -0.1;
    }
    set_out_update();
}
function rotate_right(){
    if(current_status.zoom){
        user_image.rotate(0.05);
        image_actions_count.img_rotate += 0.05;
    }else{
        user_image.rotate(0.1);
        image_actions_count.img_rotate += 0.1;
    }
    set_out_update();
}
function zoom_in(){
  if(current_status.zoom_level < 2){
        current_status.zoom_level *= 2;
        lyr_area_hldr.scale(2);
        lyr_stage_coor.scale(2);
        
        current_status.zoom = true;
        
        view.update();
        
        console.log("lyr_area_hldr: "+lyr_area_hldr.position);
        console.log("lyr_stage_coor: "+lyr_stage_coor.position);
        console.log("canvas_area_hldr: "+canvas_area_hldr.position);
        console.log("canv_ref_rect: "+canv_ref_rect.position);
    }
}
function zoom_out(){
    if(current_status.zoom_level > 1){
        current_status.zoom_level /= 2;
        lyr_area_hldr.scale(0.5);
        lyr_stage_coor.scale(0.5);
        
        lyr_x = 480 - parseFloat(lyr_area_hldr.position.x);
        lyr_y = 640 - parseFloat(lyr_area_hldr.position.y);
        
        stage_x = 480 - parseFloat(lyr_stage_coor.position.x);
        stage_y = 640 - parseFloat(lyr_stage_coor.position.y);
        
        console.log("lyr_area_hldr: " + lyr_x + "----" + lyr_y);
        console.log("lyr_stage_coor: " + stage_x + "----" + stage_y);
        
        lyr_area_hldr.position.x += stage_x;
        lyr_area_hldr.position.y += stage_y;
        
        lyr_stage_coor.position.x += stage_x;
        lyr_stage_coor.position.y += stage_y;

        console.log("lyr_area_hldr: "+lyr_area_hldr.position);
        console.log("lyr_stage_coor: "+lyr_stage_coor.position);
        console.log("canvas_area_hldr: "+canvas_area_hldr.position);
        console.log("canv_ref_rect: "+canv_ref_rect.position);
        
        current_status.zoom = false;

        view.update();
    }
}
function save(){
    if(current_status.zoom_level > 1){
        zoom_out();
        
        $("#img_path_json").attr("value", getPathArrayJson());
        $("#page_wrap").fadeIn(160);
        //image_export_layer = new Layer();
        //image_export_layer.activate();
        //image_export_layer.addChild(user_image);
        upload();
    }else{
        $("#img_path_json").attr("value", getPathArrayJson());
        $("#page_wrap").fadeIn(160);
        //image_export_layer = new Layer();
        //image_export_layer.activate();
        //image_export_layer.addChild(user_image);
        upload();
    }
}
function getPathArrayJson(){
    var mp_array=[];
    for(var i = 0; i < full_mask.segments.length; i++) {
        mp_array.push([full_mask.segments[i].point.x * 2, full_mask.segments[i].point.y * 2]);

        console.log(full_mask.segments[i].point.x * 2 + " ::: "+full_mask.segments[i].point.y * 2);
    };

    return JSON.stringify(mp_array);
}
function onKeyDown(event) {
    if(event.key == "n"){
        remove_index_numbers();
        show_index_numbers();
        if(event.modifiers.shift){
            remove_index_numbers();
        }
    }
}
function onKeyUp(){
}
function onMouseDown(event) {
    if(event.modifiers.control && event.modifiers.space && event.modifiers.option){
        if(current_status.zoom_level > current_status.min_zoom_level){
            zoom_out();
        }
    }else if(event.modifiers.control && event.modifiers.space){
        if(current_status.zoom_level < current_status.max_zoom_level){
            zoom_in();
        }
    }else if(event.modifiers.control && current_status.zoom_level != 1){
        current_status.control_indi = true;
    }else{
        current_status.control_indi = false;
    }
   
    var hitResult = paper.project.hitTest(event.point, hitOptions);
    
    if (!hitResult){
            return;
        }
    
    if(hitResult.type == "segment"){
        
        active_items.drag = true;
        
        if(hitResult.segment.point != active_items.segment.point){
            active_items.segment.selected = false;
        }
        for(i=0; i<full_mask.segments.length; i++){
            if(full_mask.segments[i].point == hitResult.segment.point){
                active_items.segment = full_mask.segments[i];
                //active_items.segment.selected = true;
                circle_in.position = new Point(active_items.segment.point.x + active_items.segment.handleIn.x, active_items.segment.point.y + active_items.segment.handleIn.y);
                circle_out.position = new Point(active_items.segment.point.x + active_items.segment.handleOut.x, active_items.segment.point.y + active_items.segment.handleOut.y);
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
//                var hit_location = hitResult.location;
//                full_mask.insert(hit_location.index + 1, event.point);
            }
        }
}
function onMouseDrag(event) {
    if(active_items.drag){
        active_items.segment.point = event.point;
        circle_in.position = new Point(active_items.segment.point.x + active_items.segment.handleIn.x, active_items.segment.point.y + active_items.segment.handleIn.y);
        circle_out.position = new Point(active_items.segment.point.x + active_items.segment.handleOut.x, active_items.segment.point.y + active_items.segment.handleOut.y);
    }
    if(active_items.cir_in){
        active_items.cir_out = false;
        circle_in.position = event.point;
        active_items.segment.handleIn.x = circle_in.position.x - active_items.segment.point.x;
        active_items.segment.handleIn.y = circle_in.position.y - active_items.segment.point.y;
    }
    if(active_items.cir_out){
        active_items.cir_in = false;
        circle_out.position = event.point;
        active_items.segment.handleOut.x = circle_out.position.x - active_items.segment.point.x;
        active_items.segment.handleOut.y = circle_out.position.y - active_items.segment.point.y;
    }
    
    if(current_status.control_indi){
        if(lyr_area_hldr.position.x + event.delta.x >= 0 && lyr_area_hldr.position.x + event.delta.x <= 960){
            lyr_area_hldr.position.x += event.delta.x;
            lyr_stage_coor.position.x += event.delta.x;
            change_x_pos_diff += event.delta.x;
        }
        if(lyr_area_hldr.position.y + event.delta.y >= 0 && lyr_area_hldr.position.y + event.delta.y <= 1280){
            lyr_area_hldr.position.y += event.delta.y;
            lyr_stage_coor.position.y += event.delta.y;
            change_y_pos_diff += event.delta.y;
        }
        
        console.log("lyr_area_hldr: " + lyr_area_hldr.position);
        console.log("lyr_stage_coor" +  lyr_stage_coor.position);
        console.log(canvas_area_hldr.position);
    }
}
function onMouseUp(event) {
    active_items.drag = false;
    active_items.cir_in = false;
    active_items.cir_out = false;
}
function upload(){
    $('#image_actions').attr('value',JSON.stringify(image_actions_count));    
    $("#mask_x").attr("value", full_mask.pivot);
    $("#mask_y").attr("value", full_mask.position);    
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
    
    $.ajax({
        type: "POST",
        url: $url,//"http://localhost/cs-ltf-webapp/web/app_dev.php/user/marker/save",
        data: value_ar,
        success: function(data){
            alert(data);
            $("#page_wrap").fadeOut(160);
            act_btn();
        },
        failure: function(errMsg) {
            alert(errMsg);
        }
    });

//alert(JSON.stringify(value_ar));


}

//main_layer = new Layer();
//main_layer.activate();    



//
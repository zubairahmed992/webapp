paper.install(window);
$(document).ready(function() {
   paper.setup('canv_mask');  
   overall_mask();
});


function overall_mask(){
     var toe_shape_px=21.885;
    
    pre_init();
    function pre_init(){
        var liquid_mask = {
            user_height: parseFloat($('#user_height_frm_3').attr('value')),
            //user_height: 74,
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
            stroke: true,
            fill: true,
            tolerance: 22
    };


    //////// Pixel per inch Ratio based on device type and model
    function px_per_in_ratio(liquid_mask){
        if(liquid_mask.device_type == "iphone6"){
            return 7.958;
        }
    }
    function init(liquid_mask){
        adjusted_mask_height_px = parseFloat(liquid_mask.user_height) * px_per_inch_ratio;
        //mask_final_v1.svg 
        path_data = "M91.258,0c7.525,0,21.508,5.761,22.065,20.469c0,3.788,0.007,9.222,0,10.049c-0.01,1.111,2.747-2.648,2.747,5.847c0,4.373-2.487,10.081-5.318,9.098c-1.079,2.379-5.792,12.779-5.729,13.981c0.061,1.274,1.014,11.414,1.446,11.652c1.563,2.186,22.377,11.167,27.512,11.474c9.347,1.263,18.534,8.458,23.739,41.048c0.768,5.401,4.122,38.448,7.822,48.226c3.362,8.515,11.034,51.051,10.241,56.809c-0.663,4.819,17.805,27.007-5.042,48.521c-5.986,4.412-2.72-9.323-3.78-13.391c-7.726-14.923-5.856-28.529-3.467-34.657c0.764-2.653-15.737-35.711-21.109-48.298c-3.452-14.465-10.711-30.404-3.206-57.209c0.765-7.096-0.987-15.306-1.137-15.374c-0.172-0.068,1.859,6.644,0.875,14.373c-2.454,9.982-8.864,23.43-8.864,37.954c0,4.618,1.631,20.929,3.444,25.248c1.018,2.426,2.68,9.118,3.013,10.33c1.019,3.704,2.538,6.923,4.735,15.114c2.01,7.495,2.89,36.255,1.015,48.577c-1.647,10.81-10.123,33.596-13.326,56.586c-0.683,4.258-1.461,11.577-1.173,17.356c0.289,6.388,1.43,18.6,1.147,22.952c-0.392,11.28-12.249,64.143-13.114,70.912c-0.561,4.388,3.589,18.896,5.314,22.355c-1.823,0-12.536,0-13.822,0c-1.496,0-5.984,0-5.984,0c0.262-3.507,0.085-7.223,0.117-10.507c0.038-3.717,0.03-5.334,1.053-11.849c1.908-19.224-6.264-57.138-5.599-71.39c0.175-3.261,0.7-16.116-0.111-22.476c-3.491-18.773-1.388-41.375-4.409-72.814c-0.212-4.095-1.259-21.302-1.259-21.302s-1.028,17.208-1.24,21.302c-3.021,31.439-0.918,54.041-4.409,72.814c-0.812,6.36-0.287,19.215-0.111,22.476c0.665,14.252-7.508,52.166-5.599,71.39c1.022,6.515,1.015,8.132,1.053,11.849c0.032,3.284-0.145,7,0.117,10.507c0,0-4.488,0-5.984,0c-1.286,0-11.999,0-13.822,0c1.725-3.46,5.875-17.968,5.314-22.355c-0.865-6.77-12.723-59.632-13.114-70.912c-0.282-4.352,0.858-16.563,1.147-22.952c0.288-5.779-0.49-13.098-1.173-17.356c-3.204-22.99-11.679-45.776-13.326-56.586c-1.875-12.321-0.995-41.082,1.015-48.577c2.196-8.191,3.716-11.411,4.735-15.114c0.333-1.211,1.995-7.904,3.013-10.33c1.813-4.318,3.444-20.629,3.444-25.248c0-14.524-6.41-27.972-8.864-37.954c-0.983-7.729,1.047-14.441,0.875-14.373c-0.149,0.068-1.901,8.278-1.137,15.374c7.505,26.806,0.246,42.744-3.205,57.209c-5.372,12.587-21.873,45.645-21.109,48.298c2.39,6.127,4.259,19.733-3.467,34.657c-1.06,4.067,2.206,17.803-3.78,13.391c-22.847-21.514-4.378-43.702-5.042-48.521c-0.793-5.758,6.879-48.294,10.241-56.809c3.7-9.777,7.054-42.824,7.822-48.226c5.204-32.59,14.392-39.785,23.739-41.048c5.134-0.307,25.949-9.288,27.512-11.474c0.432-0.238,1.385-10.378,1.445-11.652c0.063-1.202-4.649-11.602-5.729-13.981c-2.83,0.982-5.317-4.726-5.317-9.098c0-8.495,2.757-4.736,2.747-5.847c-0.007-0.827,0-6.261,0-10.049C69.441,5.761,83.319,0,91.258,0z";
        full_scr_mask = new Path(path_data);
        def_mask_height = full_scr_mask.bounds.height;
        adjusted_mask_height_px = (adjusted_mask_height_px * 100) / 450;
        full_scr_mask.scale(init_scale_ratio(),adjusted_mask_height_px/100);
        
        // toe shape 
        //toe shape for camera screen 
          
          full_scr_mask.segments[27].handleOut = new Point(7, 19);
          full_scr_mask.segments[28].point.y += toe_shape_px;
          full_scr_mask.segments[28].point.x += 4;
          full_scr_mask.segments[28].handleOut = new Point(0, 0);
          full_scr_mask.segments[29].handleOut = new Point(2.43237, -11);
          full_scr_mask.segments[29].handleIn = new Point(-5, 31);
          
          
          full_scr_mask.segments[43].handleIn = new Point(-7, 19);
          full_scr_mask.segments[42].point.y += toe_shape_px;
          full_scr_mask.segments[42].point.x -= 4;
          full_scr_mask.segments[42].handleIn = new Point(0, 0);
          full_scr_mask.segments[41].handleIn = new Point(-2.43237, -11);
          full_scr_mask.segments[41].handleOut = new Point(5,  31);      

        
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
        camera_scr_mask = camera_scr_mask.scale(0.750, 0.750);
        camera_scr_mask.selected = true;
       
        /// iPhone6 camera screen settings /// available screen for mask 475.125 in camera view.
        camera_scr_mask.position = new Point(187.5,500.25 - 25.125);
        camera_scr_mask.visible = false;
        
        full_scr_mask.selected = true;
        full_scr_mask.visible = true;

        $("#svg_path_data").attr("value", camera_scr_mask.pathData);
        $("#svg_path_data_full").attr("value", full_scr_mask.pathData);

        $("#side_svg_path_data").attr("value", camera_scr_mask.pathData);
        $("#side_svg_path_data_full").attr("value", full_scr_mask.pathData);

        $("#back_svg_path_data").attr("value", camera_scr_mask.pathData);
        $("#back_svg_path_data_full").attr("value", full_scr_mask.pathData);
        view.update();

    window.location.href = "svg_path_created";

    }
 
 }
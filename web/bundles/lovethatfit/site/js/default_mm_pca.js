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
        path_data = "M90.773,0c7.825,0,22.848,5.735,23.401,20.378c0,3.771,0.007,9.182,0,10.005c-0.01,1.106,2.735-0.645,2.735,7.813c0,4.352-2.476,11.031-5.294,10.053c-1.075,2.368-5.766,11.727-5.703,12.923c0.06,1.268,0.013,11.363,0.442,11.601c1.557,2.176,22.28,11.118,27.391,11.423c9.305,1.258,18.453,8.42,23.634,40.867c0.764,5.378,4.103,38.278,7.787,48.012c3.347,8.478,10.985,50.826,10.196,56.558c-0.661,4.798,17.726,26.888-5.02,48.306c-5.959,4.393-2.708-9.282-3.763-13.331c-7.691-14.858-5.831-28.404-3.452-34.504c0.76-2.641-15.667-35.553-21.016-48.084c-3.435-14.401-10.663-30.269-3.19-56.956c0.76-7.065-0.984-15.238-1.133-15.306c-0.171-0.068,1.85,8.606,0.871,16.301c-2.443,9.938-8.824,24.321-8.824,38.782c0,4.598,1.624,17.85,3.428,22.149c1.014,2.416,2.668,9.079,3,10.284c1.015,3.687,2.528,6.893,4.714,15.047c2.001,7.461,2.877,36.095,1.01,48.361c-1.639,10.762-10.077,33.448-13.267,56.335c-0.68,4.24-1.454,11.526-1.167,17.28c0.287,6.36,1.422,18.517,1.142,22.85c-0.389,11.23-10.212,58.857-11.074,65.597c-0.558,4.369,5.569,23.813,7.287,27.256c-1.815,0-16.458,0-17.739,0c-1.49,0-7.948,0-7.948,0c0.261-3.49,0.084-7.19,0.116-10.459c0.038-3.7,0.031-10.311,1.049-16.797c1.899-19.14-6.237-51.885-5.575-66.074c0.175-3.246,0.698-16.044-0.11-22.376c-3.476-18.69,0.609-41.192-2.398-72.492c-0.966-4.048-0.256-21.208-1.366-21.208c-1.068,0-0.516,17.159-1.482,21.208c-3.007,31.3,1.077,53.802-2.398,72.492c-0.808,6.332-0.285,19.13-0.11,22.376c0.662,14.189-7.475,46.935-5.575,66.074c1.018,6.486,1.011,13.097,1.049,16.797c0.031,3.269-0.145,6.969,0.116,10.459c0,0-6.458,0-7.948,0c-1.281,0-15.924,0-17.739,0c1.718-3.443,7.845-22.887,7.287-27.256c-0.861-6.74-10.685-54.367-11.074-65.597c-0.281-4.333,0.854-16.49,1.141-22.85c0.287-5.754-0.487-13.04-1.167-17.28c-3.19-22.888-11.627-45.573-13.266-56.335c-1.867-12.266-0.991-40.9,1.01-48.361c2.187-8.155,3.699-11.361,4.714-15.047c0.332-1.206,1.985-7.869,2.999-10.284c1.805-4.299,3.428-17.551,3.428-22.149c0-14.46-6.381-28.844-8.824-38.782c-0.979-7.695,1.042-16.369,0.871-16.301c-0.148,0.068-1.893,8.241-1.132,15.306c7.473,26.687,0.245,42.555-3.19,56.956c-5.348,12.531-21.776,45.443-21.016,48.084c2.379,6.1,4.24,19.646-3.451,34.504c-1.056,4.049,2.196,17.724-3.764,13.331c-22.746-21.418-4.358-43.508-5.02-48.306c-0.79-5.732,6.848-48.08,10.196-56.558c3.684-9.733,7.022-42.634,7.787-48.012c5.181-32.446,14.329-39.609,23.634-40.867c5.111-0.305,25.834-9.247,27.391-11.423c0.43-0.237,0.383-10.333,0.442-11.601c0.063-1.196-4.628-10.555-5.703-12.923c-2.817,0.978-5.294-5.701-5.294-10.053c0-8.458,2.745-6.707,2.735-7.813c-0.007-0.823,0-6.233,0-10.005C68.136,5.735,82.432,0,90.773,0z";
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
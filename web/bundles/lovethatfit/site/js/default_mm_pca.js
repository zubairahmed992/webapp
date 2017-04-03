paper.install(window);
$(document).ready(function() {
   paper.setup('canv_mask');  
   overall_mask();
});


function overall_mask(){
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
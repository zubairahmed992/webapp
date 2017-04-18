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
        //this mask wroking currently with flat toe shape
        //path_data = "M79.455,0c7.108,0,20.321,5.66,20.857,20.108c0,3.721,0.007,6.054,0,6.867c-0.011,1.091,2.608-2.602,2.608,5.743c0,4.296-2.366,9.903-5.054,8.937c-1.025,2.34-5.504,13.31-5.444,14.491c0.058,1.253,0.964,10.473,1.375,10.708c1.485,2.146,20.987,7.739,26.146,8.623c14.09,2.414,19.705,14.798,21.612,39.95c0.698,5.082,1.888,37.947,5.508,47.839c3.194,8.364,7.201,57.149,6.448,62.805c-0.632,4.735,15.809,25.89-5.905,47.022c-8.116,7.898-3.771-7.298-4.779-11.292c-7.34-14.66-3.574-28.72-1.302-34.74c0.724-2.605-14.161-41.228-17.126-54.275c-9.765-42.965-2.553-41.912,1.179-57.359c0.727-6.97-0.686-15.036-0.832-15.103c-0.164-0.067,1.768,6.526,0.832,14.117c-2.331,9.806-8.679,22.954-8.679,37.221c0,4.536,1.82,20.22,3.544,24.463c0.583,1.435,2.229,7.75,2.811,9.936c0.968,3.636,2.135,12.348,4.224,20.397c1.278,7.491,5.634,23.716,1.352,46.621c-1.565,10.619-10.74,31.778-13.684,57.761c-0.65,4.184-1.39,11.374-1.116,17.05c0.274,6.275,1.764,18.013,1.496,22.291c-0.372,11.08-12.044,57.033-12.867,63.681c-0.536,4.31,1.666,12.577,2.218,15.636c0.688,3.822,4.708,19.182,6.001,20.477c-0.603,0.059-10.042,0-11.797,0c-1.736,0-11.503,0-11.799,0c0.009-1.847,0.531-33.07,1.036-36.05c0.9-7.582-4.277-49.957-3.646-63.958c0.165-3.204,0.665-15.831-0.105-22.079c-3.317-18.44-3.884-34.317-5.089-74.925c-0.142-2.823,0.125-14.02-0.024-14.02c-0.151,0,0.092,11.197-0.05,14.02c-1.205,40.607-1.772,56.484-5.089,74.925c-0.77,6.248-0.271,18.875-0.105,22.079c0.631,14.001-4.546,56.376-3.646,63.958c0.505,2.98,1.027,34.204,1.036,36.05c-0.296,0-10.063,0-11.799,0c-1.756,0-11.194,0.059-11.797,0c1.293-1.295,5.313-16.654,6.001-20.477c0.552-3.059,2.754-11.326,2.218-15.636c-0.823-6.648-12.495-52.602-12.867-63.681c-0.267-4.278,1.222-16.016,1.496-22.291c0.274-5.676-0.466-12.866-1.115-17.05c-2.944-25.982-12.119-47.142-13.684-57.761c-4.283-22.904,0.074-39.129,1.352-46.621c2.089-8.049,3.256-16.761,4.224-20.397c0.582-2.187,2.228-8.501,2.812-9.936c1.724-4.243,3.544-19.927,3.544-24.463c0-14.267-6.349-27.415-8.68-37.221c-0.936-7.591,0.995-14.185,0.832-14.117c-0.146,0.067-1.559,8.133-0.832,15.103c3.732,15.447,10.943,14.394,1.179,57.359c-2.965,13.047-17.85,51.67-17.126,54.275c2.272,6.021,6.038,20.08-1.302,34.74c-1.008,3.994,3.337,19.19-4.779,11.292c-21.712-21.133-5.272-42.287-5.904-47.022c-0.753-5.656,3.253-54.441,6.447-62.805c3.621-9.892,4.811-42.757,5.509-47.839c1.907-25.151,7.523-37.536,21.613-39.95c5.158-0.884,24.66-6.477,26.145-8.623c0.411-0.235,1.316-9.455,1.375-10.708c0.061-1.181-4.418-12.151-5.444-14.491c-2.688,0.966-5.054-4.641-5.054-8.937c0-8.344,2.619-4.652,2.608-5.743c-0.007-0.813,0-3.146,0-6.867C59.107,5.66,72.463,0,79.455,0z";
        //path_data = "M87.41,0c7.603,0,20.554,5.537,21.09,19.673c0,3.641,0.006,5.923,0,6.718c-0.009,1.067,2.64-2.546,2.64,5.619c0,4.202-2.391,9.688-5.11,8.743c-1.037,2.287-5.567,13.021-5.506,14.177c0.058,1.224,0.974,10.246,1.389,10.475c1.502,2.1,21.507,7.775,26.441,8.071c8.983,1.214,17.814,8.128,22.815,39.45c0.738,5.191,3.961,36.951,7.518,46.35c3.23,8.184,10.604,57.149,9.843,62.684c-0.638,4.633,17.112,25.957-4.845,46.634c-5.753,4.239-2.614-8.962-3.634-12.871c-7.425-14.341-5.628-27.419-3.331-33.307c0.733-2.55-15.126-42.408-20.288-54.506c-3.317-13.9-10.295-29.22-3.081-54.981c0.735-6.82-0.948-14.712-1.093-14.776c-0.165-0.065,1.787,6.386,0.842,13.813c-2.359,9.595-8.519,22.519-8.519,36.477c0,4.438,1.566,20.114,3.31,24.266c0.978,2.331,2.575,8.763,2.895,9.927c0.979,3.561,2.44,12.535,4.551,20.408c1.932,7.202,2.777,34.845,0.976,46.686c-1.584,10.389-9.729,32.29-12.808,54.384c-0.656,4.093-1.404,11.127-1.128,16.681c0.277,6.139,1.374,17.875,1.104,22.06c-0.376,10.839-11.772,62.395-12.604,68.899c-0.539,4.216,3.449,17.342,5.106,20.667c1.803,3.618,9.966,15.615-13.283,17.583c-10.611,0-8.04-6.747-5.751-17.583c0.252-3.371,0.082-6.871,0.113-10.027c0.036-3.572,0.028-5.875,1.01-12.136c1.836-18.476-6.02-54.167-5.38-67.863c0.168-3.134,0.673-15.489-0.106-21.602c-3.355-18.042-1.334-39.765-4.239-69.979c-0.203-3.936-0.721-16.804-0.993-16.804s-0.79,12.868-0.993,16.804c-2.905,30.215-0.884,51.938-4.239,69.979c-0.779,6.112-0.273,18.468-0.105,21.602c0.639,13.696-7.217,49.388-5.381,67.863c0.981,6.261,0.974,8.563,1.01,12.136c0.031,3.156-0.139,6.656,0.113,10.027C80.044,443.253,82.616,450,72.004,450c-23.249-1.968-15.086-13.965-13.283-17.583c1.657-3.325,5.646-16.451,5.106-20.667c-0.832-6.505-12.229-58.061-12.604-68.899c-0.271-4.185,0.826-15.921,1.104-22.06c0.276-5.554-0.472-12.588-1.128-16.681c-3.078-22.094-11.224-43.995-12.808-54.384c-1.802-11.841-0.956-39.483,0.976-46.686c2.11-7.873,3.572-16.848,4.551-20.408c0.319-1.164,1.917-7.596,2.896-9.927c1.742-4.151,3.31-19.827,3.31-24.266c0-13.958-6.16-26.882-8.52-36.477c-0.945-7.427,1.007-13.878,0.842-13.813c-0.145,0.064-1.828,7.956-1.093,14.776c7.214,25.762,0.236,41.081-3.081,54.981c-5.162,12.098-21.021,51.956-20.288,54.506c2.298,5.888,4.094,18.966-3.331,33.307c-1.02,3.909,2.119,17.11-3.633,12.871c-21.957-20.677-4.208-42.001-4.846-46.634c-0.762-5.534,6.612-54.5,9.843-62.684c3.557-9.398,6.779-41.158,7.518-46.35c5.001-31.322,13.832-38.236,22.815-39.45c4.935-0.296,24.939-5.972,26.441-8.071c0.415-0.229,1.331-9.251,1.389-10.475c0.062-1.155-4.469-11.89-5.506-14.177c-2.72,0.944-5.11-4.541-5.11-8.743c0-8.165,2.648-4.552,2.641-5.619c-0.007-0.795,0-3.077,0-6.718C66.739,5.537,80.178,0,87.41,0z";
        //this version have flat toe shape
        path_data="M90.883,0c8.97,0,21.396,6.005,21.954,20.706c0,3.787,0.006,6.16,0,6.987c-0.009,1.109,2.745-2.648,2.745,5.843c0,4.37-2.486,10.076-5.315,9.092c-1.077,2.379-5.789,13.542-5.726,14.744c0.061,1.272,1.014,10.655,1.445,10.894c1.562,2.184,22.366,8.086,27.497,8.394c9.342,1.263,18.526,8.453,23.728,41.026c0.768,5.399,4.119,38.428,7.818,48.203c3.359,8.511,11.028,59.433,10.236,65.189c-0.663,4.818,17.796,26.995-5.039,48.498c-5.983,4.409-2.719-9.32-3.779-13.385c-7.722-14.915-5.853-28.516-3.464-34.639c0.763-2.651-15.73-44.103-21.099-56.684c-3.449-14.456-10.707-30.388-3.204-57.179c0.765-7.093-0.986-15.3-1.136-15.367c-0.172-0.067,1.858,6.642,0.875,14.365c-2.453,9.979-8.859,23.419-8.859,37.935c0,4.615,1.628,20.918,3.442,25.236c1.018,2.424,2.678,9.114,3.011,10.324c1.018,3.704,2.537,13.036,4.733,21.224c2.009,7.49,2.887,36.238,1.014,48.552c-1.647,10.805-10.117,33.581-13.319,56.558c-0.683,4.256-1.46,11.572-1.173,17.348c0.287,6.384,1.429,18.589,1.147,22.941c-0.391,11.272-12.242,64.11-13.107,70.875c-0.561,4.384,5.443,22.043,5.31,22.319c-3.649,0-12.099,0-13.723,0c-1.497,0-3.013,0-6.071,0c0.262-3.506,0.085-7.195,0.117-10.478c0.038-3.713,0.03-5.331,1.05-11.842c1.91-19.215-6.26-57.11-5.595-71.354c0.174-3.259,0.701-16.108-0.11-22.466c-3.49-18.763-1.387-41.354-4.409-72.776c-0.211-4.093-0.75-17.476-1.033-17.476c-0.283,0-0.822,13.383-1.033,17.476c-3.02,31.423-0.919,54.014-4.408,72.776c-0.81,6.357-0.284,19.207-0.109,22.466c0.665,14.243-7.505,52.14-5.596,71.354c1.021,6.511,1.013,8.127,1.05,11.842c0.033,3.282,0.118,10.241,0.118,10.478c-0.774,0-4.203,0-5.985,0c-2.183,0-12.647,0-13.814,0c-0.133-0.366,5.872-17.939,5.311-22.324c-0.865-6.765-12.718-59.604-13.108-70.875c-0.281-4.352,0.859-16.557,1.148-22.942c0.287-5.776-0.491-13.091-1.173-17.348c-3.201-22.977-11.673-45.753-13.32-56.558c-1.874-12.314-0.995-41.061,1.016-48.552c2.193-8.188,3.714-17.521,4.732-21.224c0.331-1.211,1.994-7.899,3.011-10.324c1.812-4.317,3.443-20.62,3.443-25.236c0-14.516-6.406-27.957-8.861-37.935c-0.982-7.724,1.047-14.433,0.875-14.365c-0.15,0.066-1.9,8.274-1.137,15.366c7.502,26.792,0.246,42.723-3.204,57.179c-5.369,12.582-21.861,54.033-21.099,56.685c2.391,6.123,4.258,19.724-3.464,34.638c-1.06,4.066,2.204,17.794-3.778,13.386c-22.834-21.503-4.376-43.68-5.04-48.498c-0.792-5.755,6.877-56.679,10.236-65.19c3.7-9.773,7.05-42.803,7.819-48.203c5.201-32.574,14.385-39.764,23.726-41.026c5.133-0.308,25.937-6.211,27.499-8.394c0.432-0.238,1.384-9.621,1.444-10.894c0.065-1.201-4.647-12.365-5.726-14.744c-2.829,0.981-5.315-4.723-5.315-9.093c0-8.491,2.754-4.733,2.747-5.844c-0.008-0.827,0-3.199,0-6.986C68.851,12.106,76.128,0,90.883,0z";
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

//        $("#side_svg_path_data").attr("value", camera_scr_mask.pathData);
//        $("#side_svg_path_data_full").attr("value", full_scr_mask.pathData);
//
//        $("#back_svg_path_data").attr("value", camera_scr_mask.pathData);
//        $("#back_svg_path_data_full").attr("value", full_scr_mask.pathData);
        view.update();

    window.location.href = "svg_path_created";

    }
 
 }
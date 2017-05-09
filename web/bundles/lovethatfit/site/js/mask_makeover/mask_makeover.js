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
var toe_shape_px=42;
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
   
  
    curr_path_prefix: $("#hdn_serverpath").attr("value")
}






/////////////////////////////// from mask generation pca file ///////////////////////////

function overall_mask(){
    pre_init();
    function pre_init(){
      var liquid_mask = {
            user_height: parseFloat($('#user_height_frm_3').attr('value')),
            //user_height: 72,
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
        //path_data = "M87.842,0c7.535,0,20.725,5.572,21.262,19.796c0,3.664,0.008,5.96,0,6.76c-0.008,1.074,2.658-2.562,2.658,5.655c0,4.229-2.407,9.748-5.143,8.798c-1.045,2.301-5.604,13.103-5.542,14.266c0.06,1.231,0.981,10.311,1.399,10.541c1.51,2.113,21.641,7.825,26.606,8.122c9.04,1.221,17.927,8.18,22.96,39.698c0.742,5.224,3.985,37.183,7.563,46.64c3.25,8.235,10.671,57.508,9.905,63.078c-0.643,4.662,17.22,26.12-4.876,46.926c-5.787,4.266-2.63-9.018-3.656-12.951c-7.471-14.432-5.662-27.591-3.352-33.517c0.738-2.566-15.221-42.674-20.415-54.848c-3.339-13.988-10.361-29.403-3.1-55.327c0.738-6.863-0.955-14.804-1.102-14.869c-0.166-0.066,1.8,6.426,0.847,13.899c-2.373,9.655-8.571,22.661-8.571,36.706c0,4.467,1.577,20.241,3.331,24.418c0.983,2.345,2.591,8.817,2.914,9.989c0.984,3.582,2.455,12.613,4.577,20.536c1.944,7.248,2.796,35.064,0.981,46.979c-1.592,10.454-9.789,32.492-12.886,54.725c-0.661,4.119-1.413,11.197-1.135,16.784c0.279,6.178,1.383,17.988,1.11,22.2c-0.378,10.906-11.847,62.785-12.684,69.332c-0.542,4.242,3.081,15.737,3.887,17.282c1.766,3.995,3.514,9.281,4.481,11.381c0.562,1.41-1.543,6.105-19.65,6.988c-6.449,0.315-4.438-4.95-4.362-6.988c3.921-13.785,0.1-14.941,2.759-30.168c1.847-18.592-6.057-54.508-5.413-68.289c0.169-3.154,0.678-15.586-0.108-21.739c-3.376-18.154-1.341-40.013-4.264-70.418c-0.206-3.96-0.73-16.91-0.924-16.91c-0.274,0-0.795,12.949-1,16.91c-2.923,30.405-0.888,52.264-4.265,70.418c-0.785,6.152-0.276,18.585-0.107,21.739c0.644,13.781-7.262,49.696-5.415,68.289c2.661,15.227-1.16,16.383,2.76,30.168c0.075,2.038,2.086,7.304-4.362,6.988c-18.107-0.883-20.213-5.578-19.65-6.988c0.966-2.1,2.715-7.386,4.481-11.381c0.805-1.545,4.429-13.04,3.887-17.282c-0.836-6.546-12.305-58.426-12.684-69.332c-0.272-4.211,0.832-16.021,1.11-22.2c0.278-5.587-0.474-12.666-1.135-16.784c-3.098-22.233-11.294-44.271-12.887-54.725c-1.813-11.916-0.963-39.731,0.98-46.979c2.124-7.923,3.595-16.954,4.579-20.536c0.322-1.172,1.931-7.644,2.915-9.989c1.752-4.177,3.33-19.952,3.33-24.418c0-14.045-6.198-27.051-8.571-36.706c-0.952-7.473,1.012-13.965,0.846-13.899c-0.146,0.065-1.84,8.006-1.1,14.869c7.26,25.924,0.238,41.339-3.101,55.327c-5.194,12.174-21.153,52.282-20.416,54.848c2.312,5.925,4.12,19.085-3.352,33.517c-1.025,3.934,2.132,17.217-3.656,12.951c-22.095-20.807-4.234-42.265-4.875-46.926c-0.767-5.569,6.653-54.843,9.905-63.078c3.578-9.457,6.822-41.417,7.564-46.64c5.033-31.519,13.919-38.477,22.959-39.698c4.965-0.297,25.097-6.009,26.607-8.122c0.418-0.23,1.339-9.309,1.398-10.541c0.063-1.163-4.496-11.964-5.541-14.266c-2.737,0.95-5.143-4.569-5.143-8.798c0-8.216,2.666-4.581,2.658-5.655c-0.007-0.8,0-3.097,0-6.76C67.158,5.572,80.648,0,87.842,0z";
        //path_data = "M87.41,0c7.603,0,20.554,5.537,21.09,19.673c0,3.641,0.006,5.923,0,6.718c-0.009,1.067,2.64-2.546,2.64,5.619c0,4.202-2.391,9.688-5.11,8.743c-1.036,2.287-5.566,13.021-5.506,14.177c0.059,1.224,0.975,10.246,1.39,10.475c1.502,2.1,21.507,7.775,26.44,8.071c8.983,1.214,17.814,8.128,22.815,39.45c0.738,5.191,3.961,36.951,7.518,46.35c3.23,8.184,10.604,57.149,9.843,62.684c-0.638,4.633,17.112,25.957-4.845,46.634c-5.753,4.239-2.614-8.962-3.634-12.871c-7.425-14.341-5.628-27.419-3.331-33.307c0.733-2.55-15.126-42.408-20.288-54.506c-3.316-13.9-10.295-29.22-3.081-54.981c0.735-6.82-0.948-14.712-1.093-14.776c-0.165-0.065,1.787,6.386,0.842,13.813c-2.358,9.595-8.519,22.519-8.519,36.477c0,4.438,1.565,20.114,3.31,24.266c0.979,2.331,2.575,8.763,2.896,9.927c0.979,3.562,2.439,12.535,4.551,20.408c1.932,7.202,2.776,34.845,0.976,46.686c-1.584,10.389-9.729,32.29-12.808,54.384c-0.656,4.093-1.404,11.127-1.128,16.681c0.276,6.139,1.374,17.875,1.104,22.06c-0.376,10.839-11.771,61.646-12.604,68.151c-0.539,4.216,3.449,18.136,5.105,21.462c0,0-11.883,0-13.195,0c-0.875,0-5.397,0-5.838,0c0.252-3.371,0.082-6.918,0.112-10.074c0.036-3.572,0.028-5.127,1.01-11.388c1.837-18.476-6.02-54.915-5.38-68.611c0.168-3.134,0.674-15.489-0.105-21.602c-3.355-18.042-1.334-39.765-4.239-69.979c-0.203-3.936-0.721-16.804-0.993-16.804c-0.271,0-0.79,12.868-0.993,16.804c-2.904,30.215-0.884,51.938-4.238,69.979c-0.779,6.112-0.273,18.468-0.105,21.602c0.64,13.696-7.217,50.136-5.381,68.611c0.981,6.261,0.974,7.815,1.01,11.388c0.031,3.156-0.139,6.703,0.113,10.074c0,0-5.354,0-5.755,0c-1.879,0-13.283,0-13.283,0c1.657-3.326,5.646-17.25,5.106-21.466c-0.832-6.505-12.229-57.313-12.604-68.151c-0.271-4.185,0.826-15.921,1.104-22.06c0.275-5.554-0.473-12.588-1.128-16.681c-3.078-22.094-11.225-43.995-12.809-54.384c-1.802-11.841-0.956-39.483,0.977-46.686c2.109-7.873,3.571-16.848,4.551-20.408c0.318-1.164,1.917-7.596,2.896-9.927c1.742-4.151,3.311-19.827,3.311-24.266c0-13.958-6.16-26.882-8.521-36.477c-0.944-7.427,1.007-13.878,0.842-13.813c-0.145,0.064-1.827,7.956-1.093,14.776c7.214,25.762,0.236,41.081-3.081,54.981c-5.162,12.098-21.021,51.956-20.288,54.506c2.299,5.888,4.095,18.966-3.331,33.307c-1.02,3.909,2.119,17.11-3.633,12.871c-21.957-20.677-4.208-42.001-4.846-46.634c-0.762-5.534,6.612-54.5,9.843-62.684c3.558-9.398,6.779-41.158,7.519-46.35C28.536,81.6,37.367,74.686,46.35,73.472c4.936-0.296,24.939-5.972,26.441-8.071c0.415-0.229,1.331-9.251,1.389-10.475c0.063-1.155-4.469-11.89-5.506-14.177c-2.72,0.944-5.11-4.541-5.11-8.743c0-8.165,2.648-4.552,2.642-5.619c-0.008-0.795,0-3.077,0-6.718C66.739,5.537,80.178,0,87.41,0z";
       //flat shape path data
        // path_data = "M90.883,0c8.97,0,21.396,6.005,21.954,20.706c0,3.787,0.006,6.16,0,6.987c-0.009,1.109,2.745-2.648,2.745,5.843c0,4.37-2.486,10.076-5.315,9.092c-1.077,2.379-5.789,13.542-5.726,14.744c0.061,1.272,1.014,10.655,1.445,10.894c1.562,2.184,22.366,8.086,27.497,8.394c9.342,1.263,18.526,8.453,23.728,41.026c0.768,5.399,4.119,38.428,7.818,48.203c3.359,8.511,11.028,59.433,10.236,65.189c-0.663,4.818,17.796,26.995-5.039,48.498c-5.983,4.409-2.719-9.32-3.779-13.385c-7.722-14.915-5.853-28.516-3.464-34.639c0.763-2.651-15.73-44.103-21.099-56.684c-3.449-14.456-10.707-30.388-3.204-57.179c0.765-7.093-0.986-15.3-1.136-15.367c-0.172-0.067,1.858,6.642,0.875,14.365c-2.453,9.979-8.859,23.419-8.859,37.935c0,4.615,1.628,20.918,3.442,25.236c1.018,2.424,2.678,9.114,3.011,10.324c1.018,3.704,2.537,13.036,4.733,21.224c2.009,7.49,2.887,36.238,1.014,48.552c-1.647,10.805-10.117,33.581-13.319,56.558c-0.683,4.256-1.46,11.572-1.173,17.348c0.287,6.384,1.429,18.589,1.147,22.941c-0.391,11.272-12.242,64.11-13.107,70.875c-0.561,4.384,5.443,22.043,5.31,22.319c-3.649,0-12.099,0-13.723,0c-1.497,0-3.013,0-6.071,0c0.262-3.506,0.085-7.195,0.117-10.478c0.038-3.713,0.03-5.331,1.05-11.842c1.91-19.215-6.26-57.11-5.595-71.354c0.174-3.259,0.701-16.108-0.11-22.466c-3.49-18.763-1.387-41.354-4.409-72.776c-0.211-4.093-0.75-17.476-1.033-17.476c-0.283,0-0.822,13.383-1.033,17.476c-3.02,31.423-0.919,54.014-4.408,72.776c-0.81,6.357-0.284,19.207-0.109,22.466c0.665,14.243-7.505,52.14-5.596,71.354c1.021,6.511,1.013,8.127,1.05,11.842c0.033,3.282,0.118,10.241,0.118,10.478c-0.774,0-4.203,0-5.985,0c-2.183,0-12.647,0-13.814,0c-0.133-0.366,5.872-17.939,5.311-22.324c-0.865-6.765-12.718-59.604-13.108-70.875c-0.281-4.352,0.859-16.557,1.148-22.942c0.287-5.776-0.491-13.091-1.173-17.348c-3.201-22.977-11.673-45.753-13.32-56.558c-1.874-12.314-0.995-41.061,1.016-48.552c2.193-8.188,3.714-17.521,4.732-21.224c0.331-1.211,1.994-7.899,3.011-10.324c1.812-4.317,3.443-20.62,3.443-25.236c0-14.516-6.406-27.957-8.861-37.935c-0.982-7.724,1.047-14.433,0.875-14.365c-0.15,0.066-1.9,8.274-1.137,15.366c7.502,26.792,0.246,42.723-3.204,57.179c-5.369,12.582-21.861,54.033-21.099,56.685c2.391,6.123,4.258,19.724-3.464,34.638c-1.06,4.066,2.204,17.794-3.778,13.386c-22.834-21.503-4.376-43.68-5.04-48.498c-0.792-5.755,6.877-56.679,10.236-65.19c3.7-9.773,7.05-42.803,7.819-48.203c5.201-32.574,14.385-39.764,23.726-41.026c5.133-0.308,25.937-6.211,27.499-8.394c0.432-0.238,1.384-9.621,1.444-10.894c0.065-1.201-4.647-12.365-5.726-14.744c-2.829,0.981-5.315-4.723-5.315-9.093c0-8.491,2.754-4.733,2.747-5.844c-0.008-0.827,0-3.199,0-6.986C68.851,12.106,76.128,0,90.883,0z";
        
        //srouce file : mask_final_v1.svg 
        //path_data = "M91.258,0c7.525,0,21.508,5.761,22.065,20.469c0,3.788,0.007,9.222,0,10.049c-0.01,1.111,2.747-2.648,2.747,5.847c0,4.373-2.487,10.081-5.318,9.098c-1.079,2.379-5.792,12.779-5.729,13.981c0.061,1.274,1.014,11.414,1.446,11.652c1.563,2.186,22.377,11.167,27.512,11.474c9.347,1.263,18.534,8.458,23.739,41.048c0.768,5.401,4.122,38.448,7.822,48.226c3.362,8.515,11.034,51.051,10.241,56.809c-0.663,4.819,17.805,27.007-5.042,48.521c-5.986,4.412-2.72-9.323-3.78-13.391c-7.726-14.923-5.856-28.529-3.467-34.657c0.764-2.653-15.737-35.711-21.109-48.298c-3.452-14.465-10.711-30.404-3.206-57.209c0.765-7.096-0.987-15.306-1.137-15.374c-0.172-0.068,1.859,6.644,0.875,14.373c-2.454,9.982-8.864,23.43-8.864,37.954c0,4.618,1.631,20.929,3.444,25.248c1.018,2.426,2.68,9.118,3.013,10.33c1.019,3.704,2.538,6.923,4.735,15.114c2.01,7.495,2.89,36.255,1.015,48.577c-1.647,10.81-10.123,33.596-13.326,56.586c-0.683,4.258-1.461,11.577-1.173,17.356c0.289,6.388,1.43,18.6,1.147,22.952c-0.392,11.28-12.249,64.143-13.114,70.912c-0.561,4.388,3.589,18.896,5.314,22.355c-1.823,0-12.536,0-13.822,0c-1.496,0-5.984,0-5.984,0c0.262-3.507,0.085-7.223,0.117-10.507c0.038-3.717,0.03-5.334,1.053-11.849c1.908-19.224-6.264-57.138-5.599-71.39c0.175-3.261,0.7-16.116-0.111-22.476c-3.491-18.773-1.388-41.375-4.409-72.814c-0.212-4.095-1.259-21.302-1.259-21.302s-1.028,17.208-1.24,21.302c-3.021,31.439-0.918,54.041-4.409,72.814c-0.812,6.36-0.287,19.215-0.111,22.476c0.665,14.252-7.508,52.166-5.599,71.39c1.022,6.515,1.015,8.132,1.053,11.849c0.032,3.284-0.145,7,0.117,10.507c0,0-4.488,0-5.984,0c-1.286,0-11.999,0-13.822,0c1.725-3.46,5.875-17.968,5.314-22.355c-0.865-6.77-12.723-59.632-13.114-70.912c-0.282-4.352,0.858-16.563,1.147-22.952c0.288-5.779-0.49-13.098-1.173-17.356c-3.204-22.99-11.679-45.776-13.326-56.586c-1.875-12.321-0.995-41.082,1.015-48.577c2.196-8.191,3.716-11.411,4.735-15.114c0.333-1.211,1.995-7.904,3.013-10.33c1.813-4.318,3.444-20.629,3.444-25.248c0-14.524-6.41-27.972-8.864-37.954c-0.983-7.729,1.047-14.441,0.875-14.373c-0.149,0.068-1.901,8.278-1.137,15.374c7.505,26.806,0.246,42.744-3.205,57.209c-5.372,12.587-21.873,45.645-21.109,48.298c2.39,6.127,4.259,19.733-3.467,34.657c-1.06,4.067,2.206,17.803-3.78,13.391c-22.847-21.514-4.378-43.702-5.042-48.521c-0.793-5.758,6.879-48.294,10.241-56.809c3.7-9.777,7.054-42.824,7.822-48.226c5.204-32.59,14.392-39.785,23.739-41.048c5.134-0.307,25.949-9.288,27.512-11.474c0.432-0.238,1.385-10.378,1.445-11.652c0.063-1.202-4.649-11.602-5.729-13.981c-2.83,0.982-5.317-4.726-5.317-9.098c0-8.495,2.757-4.736,2.747-5.847c-0.007-0.827,0-6.261,0-10.049C69.441,5.761,83.319,0,91.258,0z";
        
        //srouce file : mask_final_v2.svg 
        path_data="M89.515,0c14.962,0,23.395,10.368,23.395,19.515c0,3.797,0.007,8.21,0,9.038c-0.011,1.114,2.753-0.649,2.753,7.865c0,4.382-2.493,13.106-5.329,12.121c-0.399,3.479-0.815,6.923-5.742,15.023c0.06,1.276,0.014,9.788,0.445,10.026c3.286,5.789,25.403,8.759,30.574,10.5c9.019,3.02,16.548,9.686,21.794,40.914c0.77,5.414,1.13,36.415,4.839,46.214c3.372,8.534,11.06,51.167,10.264,56.938c-0.666,4.832,17.846,27.069-5.053,48.631c-6,4.421-2.726-9.344-3.79-13.422c-7.741-14.957-5.869-28.593-3.474-34.734c0.766-2.66-15.772-35.793-21.157-48.408c-3.458-14.498-8.734-29.472-1.211-56.339c0.767-7.112-0.991-18.367-1.14-18.436c-0.173-0.068,1.862,7.665,0.877,15.411c-2.459,10.005-8.883,28.512-8.883,43.069c0,4.629,1.634,16.97,3.45,21.298c1.021,2.43,2.685,9.139,3.02,10.353c1.021,3.711,2.543,6.938,4.745,15.147c2.015,7.513,2.896,36.338,1.017,48.687c-1.65,10.834-10.144,33.672-13.354,56.713c-0.684,4.27-1.464,11.604-1.175,17.396c0.289,6.405,1.431,18.641,1.148,23.001c-0.392,11.307-10.282,59.255-11.149,66.039c-0.561,4.399,5.607,23.972,7.337,27.438c-1.827,0-16.569,0-17.857,0c-1.501,0-8.001,0-8.001,0c0.261-3.514,0.085-7.236,0.116-10.529c0.039-3.723,0.031-10.379,1.056-16.909c1.912-19.267-6.279-52.233-5.612-66.517c0.176-3.268,0.703-16.15-0.111-22.525c-3.497-18.817,0.614-41.471-2.413-72.979c-0.973-4.076-0.743-21.351-1.4-21.351c-0.636,0-0.465,17.275-1.438,21.351c-3.027,31.509,1.084,54.163-2.413,72.979c-0.814,6.375-0.287,19.258-0.112,22.525c0.667,14.283-7.524,47.25-5.612,66.517c1.025,6.53,1.018,13.187,1.056,16.909c0.031,3.293-0.146,7.016,0.116,10.529c0,0-6.5,0-8.001,0c-1.288,0-16.03,0-17.857,0c1.729-3.467,7.897-23.039,7.337-27.438c-0.867-6.784-10.758-54.731-11.149-66.039c-0.283-4.361,0.859-16.597,1.148-23.001c0.289-5.793-0.491-13.126-1.175-17.396c-3.211-23.041-11.705-45.878-13.355-56.713c-1.879-12.349-0.998-41.174,1.017-48.687c2.202-8.209,3.724-11.437,4.745-15.147c0.334-1.214,1.999-7.922,3.02-10.353c1.816-4.328,3.45-16.669,3.45-21.298c0-14.557-6.424-33.064-8.883-43.069c-0.985-7.746,1.05-15.479,0.877-15.411c-0.148,0.069-1.906,11.324-1.14,18.436c7.523,26.867,2.247,41.841-1.211,56.339c-5.385,12.615-21.923,45.748-21.157,48.408c2.396,6.141,4.268,19.777-3.474,34.734c-1.064,4.078,2.209,17.843-3.791,13.422c-22.898-21.562-4.386-43.8-5.052-48.631c-0.796-5.771,6.892-48.403,10.264-56.938c3.708-9.799,4.069-40.8,4.839-46.214c5.246-31.228,12.775-37.894,21.793-40.914c5.172-1.741,27.609-4.874,30.575-10.5c0.431-0.239,0.385-8.75,0.445-10.026c-4.927-8.1-5.343-11.545-5.742-15.023c-2.835,0.985-5.329-7.739-5.329-12.121c0-8.514,2.764-6.751,2.753-7.865c-0.007-0.828,0-5.241,0-9.038C66.036,11.328,73.207,0,89.515,0z";
      
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
        to: [960, 1280],
        
    });
   
    
    canvas_area_hldr.style = {
        fillColor: '#eeeeee',
        opacity: 0.1
    };
    canvas_area_hldr.pivot = new Point(480,640);
    canvas_area_hldr.position = new Point(480,640);
}
function shape_resize(){
    console.log(canvas_area_hldr.size);
    
}
function canvas_ref_rect(){
    canv_ref_rect = new Path.Rectangle({
        from: [0, 0],
        to: [960, 1280]
    });
    canv_ref_rect.style = {
        fillColor: 'red',
        opacity: 0.1
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
    cir_out: false,
    h_line_available: "no"
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
         // alert( full_mask.segments[28]);
    if(maskConfig.dv_edit_type != "edit"){   
//        full_mask.segments[41].handleIn = 0;
//        full_mask.segments[41].handleOut = 0;
//
//        full_mask.segments[43].handleIn = 0;
//        full_mask.segments[43].handleOut = 0;
//
//        full_mask.segments[29].handleIn = 0;
//        full_mask.segments[29].handleOut = 0;
//
//        full_mask.segments[28].handleIn = 0;
//        full_mask.segments[28].handleOut = 0;
//
//        full_mask.segments[42].handleIn = 0;
//        full_mask.segments[42].handleOut = 0;
//
//        full_mask.segments[27].handleIn = 0;
//        full_mask.segments[27].handleOut = 0;
       
    
       full_mask.segments[41].point.y = full_mask.segments[43].point.y;
       full_mask.segments[27].point.y = full_mask.segments[29].point.y;
       
       
    //Setting Position    
        //full_mask.scale(required_mask_ratio);

        full_mask.pivot = new Point(full_mask.bounds.bottomCenter.x,full_mask.bounds.bottomCenter.y);
        full_mask.position = new Point(maskConfig.dv_scr_w/2 + 4,maskConfig.dv_scr_h - maskConfig.globle_pivot);

        $("#mask_x").attr("value", full_mask.pivot);
        $("#mask_y").attr("value", full_mask.position);

        full_mask.segments[28].point.y += toe_shape_px;
        full_mask.segments[28].handleIn = new Point((toe_shape_px * 1.4), -(toe_shape_px * 0.2));
        full_mask.segments[28].handleOut = new Point(-(toe_shape_px * 0.5), 0);
        
           //right foot
          full_mask.segments[42].point.y += toe_shape_px;
          full_mask.segments[42].handleOut = new Point(-(toe_shape_px * 1.3), -(toe_shape_px * 0.2));
          full_mask.segments[42].handleIn = new Point((toe_shape_px * 0.5), 0);
          
          
          
//          full_mask.segments[27].handleOut = new Point(18, 30);
//          full_mask.segments[28].point.y += maskConfig.toe_shape_px;
//          full_mask.segments[28].point.x += 4;
//          full_mask.segments[28].handleOut = new Point(0, 0);
//          full_mask.segments[29].handleOut = new Point(2.43237, -11);
//          full_mask.segments[29].handleIn = new Point(-10, 42);
//          
//          
//          full_mask.segments[43].handleIn = new Point(-18, 30);
//          full_mask.segments[42].point.y += maskConfig.toe_shape_px;
//          full_mask.segments[42].point.x -= 4;
//          full_mask.segments[42].handleIn = new Point(0, 0);
//          full_mask.segments[41].handleIn = new Point(-2.43237, -11);
//          full_mask.segments[41].handleOut = new Point(10, 42);


//        full_mask.segments[28].point.y += maskConfig.toe_shape_px;
//        full_mask.segments[27].handleIn = new Point(maskConfig.toe_shape_px,0);
//        full_mask.segments[29].handleIn = new Point(0,maskConfig.toe_shape_px);
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
    
    
    //var seg_array=[1,2,3,4,5,6];
    //shapeResize(seg_array, full_mask); 
  
    //var seg_arrayn=[68,67,66,65,64,2,3,4,5,6];
    //var dragPoints = 30;
   //shapeResize(seg_arrayn, full_mask, dragPoints); 
}

function shapeResize(seg_array, mask, percent){
    var currentPos = [];
    for(var i=0; i <  seg_array.length; i++){
        indexs = seg_array[i];
        mask.segments[indexs].point.y - mask.segments[0].point.y;
        lastIndex = seg_array.length -1;
        totalRange = mask.segments[lastIndex].point.y - mask.segments[0].point.y;
        totalprct = (totalRange / 100) + percent;
        currentPos[i]=(mask.segments[indexs].point.y - mask.segments[0].point.y) * 100 / totalRange; // + totalprct;
        mask.segments[indexs].point.y = (mask.segments[indexs].point.y + totalprct);
    }
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
        //mp_array.push([full_mask.segments[i].point.x * 1.0421875, full_mask.segments[i].point.y * 1.0421875]);
        mp_array.push([full_mask.segments[i].point.x, full_mask.segments[i].point.y]);
    };

    return JSON.stringify(mp_array);
}
function onKeyDown(event) {
    if(event.key === "n"){
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
      
            //horlinepath(event);
       
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
 function horlinepath(evt){
     if(Key.isDown('shift')){
     if(active_items.h_line_available == "no"){
    horline = new Path({
        segments:[[0, 0], [960, 0]],
        strokeColor:'red',
        strokeWidth:1,
        opacity:0.3,
        });
        horline.position.y = evt.point.y;
        horline.visible=true;
        active_items.h_line_available = "yes";
       return horline;
    }
    else{
        horline.position.y = evt.point.y;
        horline.visible=true;
       return horline;
   }  
     } 
 }     
function onMouseDrag(event) {
//    horline.position.y = event.point.y;
//    horline.visible=true;

    
 
    if(active_items.drag){
        active_items.segment.point = event.point;
        circle_in.position = new Point(active_items.segment.point.x + active_items.segment.handleIn.x, active_items.segment.point.y + active_items.segment.handleIn.y);
        circle_out.position = new Point(active_items.segment.point.x + active_items.segment.handleOut.x, active_items.segment.point.y + active_items.segment.handleOut.y);
        
            //horlinepath(event);
        
      
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
    //horline.visible=false;
}
function upload(){
    $('#image_actions').attr('value',JSON.stringify(image_actions_count));    
    $("#mask_x").attr("value", full_mask.pivot);
    $("#mask_y").attr("value", full_mask.position);    
    $('#img_path_paper').attr('value', full_mask.pathData);    
    
    
    var sholder_left =  full_mask.segments[7].point.y;
    var sholder_right = full_mask.segments[63].point.y;


    if(sholder_left <= sholder_right){
        $("#shoulder_height").attr("value", sholder_left);
    }else{
        $("#shoulder_height").attr("value", sholder_right);
    }

//// Remove value -66 in both lines////
    var bottom_left = full_mask.segments[21].point.y;
    var bottom_right = full_mask.segments[49].point.y;


    if(bottom_left <= bottom_right){
        $("#hip_height").attr("value", bottom_left);
    }else{
        $("#hip_height").attr("value", bottom_right);
    }
    
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
        shoulder_height: ($("#shoulder_height").attr("value") * 1.0421875) / 2,
        hip_height: ($("#hip_height").attr("value") * 1.0421875) / 2,
        svg_path:$('#img_path_paper').attr('value'),
        image_actions:$('#image_actions').attr('value')};
    
    $.ajax({
        type: "POST",
        url: $url,//"http://localhost/cs-ltf-webapp/web/app_dev.php/user/marker/save",
        data: value_ar,
        success: function(data){
            canv_settings_before_save();
            to_image();
            //act_btn();
        },
        failure: function(errMsg) {
            alert(errMsg);
        }
    });

//alert(JSON.stringify(value_ar));


}
////////////////// Shift image
function canv_settings_before_save(){
   // sub_can_area.visible=false;
   // canvas_raster.visible=false;
   circle_in.visible = false;
   circle_out.visible = false;
   full_mask.visible = false;
   view.update();
}
function canv_settings_after_save(){
    sub_can_area.remove();
    canvas_raster.remove();
    circle_in.visible = true;
    circle_out.visible = true;
    full_mask.visible = true;
    view.update();
    $("#page_wrap").fadeOut(160);
}
function to_image(){
   
    canvas_raster = new Raster();
    var canvas = document.getElementById("canv_mask_makeover");
    canvas_raster.setImage(canvas);
    var rect_area_pos = new Rectangle(120, 0, 720, 1280); 
   
    //var sub_can_area_raster = canvas.createImageData(rect_area_pos);
    sub_can_area = canvas_raster.getSubRaster(rect_area_pos);
    can_img_data = sub_can_area.toDataURL();
    
    // removeing captured image after save
    sub_can_area.visible=false;
    canvas_raster.visible=false;
    
    
    
//document.getElementById("updated_img").src = can_img_data;
    ////// Posting image
    //temporary hack: not accessing assetic value for the url, placed a hidden field, holds the server path in twig template.
    var entity_id = document.getElementById('hdn_entity_id').value;
    var img_update_url = document.getElementById('hdn_image_update_url').value;
    var archive_id = $('#hdn_archive_id').attr('value');
    $.post(img_update_url, {
        imageData : can_img_data,
        archive_id : archive_id,
        env: 'admin'
    }, function(can_img_data) {
        var obj_url = jQuery.parseJSON( can_img_data );
        // console.log("i am checked bhai");
        if(obj_url.status === "check"){ 
            
             canv_settings_after_save();
             
            alert("All Done! - Not Reloading...");
            
            
//            var curr_url = window.location + '';
////            curr_url_array = curr_url.split('/');
//            if(curr_url_array[curr_url_array.length - 1] == 'refresh'){
//                curr_url = curr_url.split('/refresh')[0];
//                window.location.assign(curr_url)
//            }else{
//                window.location.reload();
//            }
        }
    });
  
}


//main_layer = new Layer();
//main_layer.activate();    

// segments replacment as per percentage of top bottom 

//curr_range_1 = curr_range_2 = null;
//curr_ele_pos_per = false;
//mid_move_adj_per = [];
//function get_ele_pos(){
//    total_range = curr_range_2.position.y - curr_range_1.position.y;
//    for(var i = 0; i < big_move_adj.length; i++) {
//        curr_ele_pos = main_path.segments[big_move_adj[i]].point.y - curr_range_1.position.y;
//        curr_ele_pos_per = curr_ele_pos * 100 / total_range;
//        mid_move_adj_per[i] = curr_ele_pos_per;
//
//    }
//
//}
//
//function set_ele_pos_per(){
//    if(curr_range_1 != null){
//        total_range = curr_range_2.position.y - curr_range_1.position.y;
//        for(var i = 0; i < big_move_adj.length; i++) {
//
//            main_path.segments[big_move_adj[i]].point.y = curr_range_1.position.y + mid_move_adj_per[i] * total_range / 100;
//            def_path.segments[big_move_adj[i]].point.y = curr_range_1.position.y + mid_move_adj_per[i] * total_range / 100;
//        }
//    }
//}


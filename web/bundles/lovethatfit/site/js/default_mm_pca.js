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
        fixed_px_inch_ratio = 6.647154471544715;
        adj_btm_fix = 0; // Adjustment of iPhone5S
        //alert("iPhone5: " + liquid_mask.device_type + " - " + liquid_mask.device_model);
        //full_adj_btm_fix = 30.333; //30.333 for iPhone 5s // Temp, not in scope fo iphone5
        full_adj_btm_fix = 30.333; //30.333 for iPhone 5C
  }
  if(liquid_mask.device_model == "iphone5c"){
      //alert("iPhone5C: " + liquid_mask.device_type + " - " + liquid_mask.device_model);
        fixed_px_inch_ratio = 6.647154471544715;

        // adjusting 66.666% value of top empty area ----- 19.5/3*2 = 13
        //
        //
        // 3.83 is 1% value
        //adj_btm_fix = (13 + 3.83)-3;

        //adj_btm_fix = 13; (Old setting when move mask upside)
        adj_btm_fix = 0;
        
        full_adj_btm_fix = 30.333;
    }
    if(liquid_mask.device_model == "iphone5s"){
        //alert("iPhone5S: " + liquid_mask.device_type + " - " + liquid_mask.device_model);
        //fixed_px_inch_ratio = 6.4864;
        fixed_px_inch_ratio = 6.647154471544715;
        
        //adj_btm_fix = 0; // Adjustment of iPhone5S
        
        //New adjustment
        adj_btm_fix = 15.75; // Adjustment of iPhone5S
        
        //full_adj_btm_fix = 30.333;
        
        //new adjustment
        full_adj_btm_fix = 21;
        
    }
}
if(liquid_mask.device_type == "iphone6"){
    if(liquid_mask.device_model == "iphone6"){
         //alert("iPhone6: " + liquid_mask.device_type + " - " + liquid_mask.device_model);
        //fixed_px_inch_ratio = 8.094;

        fixed_px_inch_ratio = 6.647154471544715;

        // adjusting 66.666% value of top empty area ----- 23/3*2 = 15.333
        // 4.49 is 1% value
        //adj_btm_fix = 15.333 + 4.49;

        //fix adjustment for iphone6 camera view.

        adj_btm_fix = 33.5;
        
        6.647154471544715
        
    }
    if(liquid_mask.device_model == "iphone6s"){
        //alert("iPhone6S: " + liquid_mask.device_type + " - " + liquid_mask.device_model);
        fixed_px_inch_ratio = 6.647154471544715;

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

//path_data = $("#default_user_path").html();

// Regular shape
path_data = "M101.981,17.702c0,0,16.212-1.743,19.603,15.583c0.009,1.918,0.591,4.187-0.328,14.223c0.353-0.227,4.353-1.519,2.602,6.979c-0.386,1.092-1.204,7.784-3.599,7.163c-0.232,0.796-1.149,7.858-2.257,9.497c0.175,3.158-0.109,11.927,1.122,13.392c6.244,4.563,15.563,10.799,38.178,16.299c11.723,3.321,13.953,20.679,12.016,46.907c0.271,3.717-0.938,27.021,0.25,38.299c-0.091,20.908-2.881,36.976-5.639,58.339c2.09,12.8,6.088,28.382-9.13,37.033c-1.752,1.128-12.064,7.058-7.76-12.266c-0.649-2.253-2.682-19.773,6.588-24.794c-1.603-15.352-7.945-36.479-8.09-52.304c-0.131-9.101,1.919-34.793,2.253-40.301c-0.149-1.713-0.469-15.521-1.753-15.52c-0.64,0-1.658,3.007-2.253,7.509c-2.93,18.029-5.742,32.704-6.258,39.551c0.193,6.076,1.263,9.599,2.504,19.024c0.913,4.851,1.551,10.408,2.753,17.022c1.315,16.151,1.994,8.238,2.754,23.78c0.132,16.513,0.29,21.971-1.002,36.046c-2.335,26.526-2.323,37.381-4.005,52.066c0.397,7.333,0.281,9.988,0.751,15.27c1.1,16.243,1.602,23.199,1.502,29.788c-1.249,17.258-2.744,27.257-4.506,37.297c-0.716,6.73-1.191,11.331-2.002,18.523c-0.215,4.554,2.111,12.407,3.254,15.27c1.105,2.969,15.524,21.59,0.25,19.775c-8.555,0.745-17.71,1.656-19.828-19.66c-0.315-4.494-0.442-14.59,0.804-17.638c0.212-13.266-2.656-37.211-3.003-53.567c1.603-11.838,1.428-14.517,0-31.541c-2.07-8.744-6.422-41.502-8.261-65.583c-0.923-8.426-2.794-22.779-6.258-22.779c-3.316,0-5.085,14.539-5.758,22.779c-1.586,33.524-4.893,52.225-8.01,65.583c-3.362,11.495-0.975,21.611-0.5,31.541c0.637,14.314-2.502,34.992-3.254,53.567c2.302,5.388,1.266,12.054,1.001,17.522c-2.812,25.174-17.2,19.812-19.525,19.775c-14.499,0-2.601-11.707,0-19.775c1.867-4.351,3.266-10.718,3.504-15.27c-0.609-4.773-1.71-10.112-2.503-18.272c-1.473-10.711-3.62-20.224-4.005-37.548c-0.633-9.515-0.013-11.065,1.252-29.788c-0.082-4.662,0.744-10.585,1.001-15.27c-1.725-18.468-3.678-35.371-4.005-52.317c-1.537-19.225-0.578-23.548-1.252-36.045c1.071-10.49,2.398-18.042,3.004-23.53c1.027-4.149,1.374-10.431,2.253-17.022c1.486-6.384,2.25-10.417,2.753-19.024c-1.207-14.005-2.904-21.231-5.757-39.551c-0.342-4.334-0.957-7.451-1.752-7.509c-1.652,0-1.439,13.846-1.752,15.52c-0.909,7.543,3.824,21.077,1.502,40.301c-0.754,15.4-7.596,42.013-7.76,52.317c8.675,5.266,7.376,22.713,6.258,24.782c4.142,19.615-6.896,13.404-8.26,12.266c-10.879-4.833-13.711-24.156-8.761-37.047c-1.438-12.051-5.054-34.861-5.507-58.324c0.601-12.921,0.201-21.329-0.25-38.299c-0.103-23.176-1.944-41.611,12.266-47.06c19.647-3.392,32.891-11.322,38.549-16.021c1.006-1.289,0.901-10.224,0.751-13.517c-1.006-1.842-1.007-7.354-1.752-9.512c-0.433,0.554-2.352,0-4.005-7.259c-0.537-2.463,0.846-7.278,2.503-6.758c-0.599-1.657-0.975-9.218,0-14.519C85.942,31.674,89.597,17.794,101.981,17.702z";

side_path_data = "M14.7,15.3h450v450h-450V15.3z";

// Slim shape
//path_data = "M101.429,23.768c0,0,20.075-1.984,21.125,15.599c0.01,1.92,0.843,4.19-0.077,14.236c0.354-0.227,2.961-1.042,1.352,6.986c-0.481,2.193-2.458,7.791-4.855,7.17c-0.232,0.796-0.947,7.435-2.008,9.506c0.174,3.161-0.483,9.882,2.376,13.404c5.819,5.954,33.394,13.919,38.716,16.316c9.117,5.353,8.016,15.66,9.021,46.952c0.272,3.721,0.828,24.364,0.251,38.336c-1.728,43.505-5.612,49.118-4.643,58.396c2.092,12.813,3.883,28.672-10.893,37.069c-2.604,0.933-9.667,0.651-7.518-12.277c0.267-13.904,1.465-20.644,3.088-24.819c6.837-12.291,0.359-39.983-0.832-52.355c-3.207-15.916-2.339-34.827-2.005-40.341c-0.149-1.715,0.663-11.871,0.752-15.535c-0.641,0.654-1.082,2.617-2.005,7.517c-4.045,17.523-9.158,31.951-9.021,39.589c0.521,5.559,1.275,12.357,2.256,19.043c0.652,4.07,1.302,10.418,2.505,17.038c0.662,6.679,0.982,9.293,2.005,23.804c0.525,12.407,0.9,22.582,0,36.082c-0.767,12.024-1.772,31.462-1.754,52.117c0.005,4.265,1.163,9.605,1.503,15.284c0.97,9.584,1.375,12.883,0.752,29.817c-1.25,17.274-3.75,27.283-5.513,37.334c-0.585,5.035-2.471,13.044,0.251,18.542c0.57,4.099,5.121,12.418,6.264,15.284c1.107,2.972,10.92,19.794-3.257,19.794c-16.613,0-14.816,0-19.097-19.68c-1.297-4.694-2.371-13.884-1.449-17.654c3.091-12.951-1.123-29.591-2.256-53.62c-1.34-12.046,2.159-22.973,1.253-31.571c-4.648-17.844-5.818-44.354-8.269-65.647c-0.971-7.685-2.829-22.802-6.765-22.802c-3.319,0-4.905,10.906-6.765,22.802c-0.706,6.875-3.777,50.162-8.018,65.647c-1.528,9.543,2.699,17.08,1.253,31.571c0.512,13.828-6.681,40.08-1.754,53.62c0.383,4.981-1.489,12.065-1.754,17.54c-2.22,21.721-10.722,19.794-19.043,19.794c-12.271,0-7.164-11.811-3.508-19.794c1.869-4.355,5.774-10.729,6.013-15.284c1.678-4.642,1.41-10.947,0.251-18.292c-1.612-9.439-4.51-25.916-5.262-37.584c-0.633-8.837-0.743-14.05,0.752-29.817c-0.082-4.666,1.247-10.595,1.503-15.284c0.737-21.725-0.925-35.405-1.253-52.368c-0.669-16.453-0.513-25.676-0.501-36.081c0.568-8.762,1.648-18.06,2.255-23.553c0.716-3.867,1.713-12.599,2.255-17.038c0.967-5.9,1.751-12.143,2.255-19.043c-1.148-14.469-7.998-32.635-9.021-39.589c-0.639-3.458-1.527-6.13-1.754-7.517c-0.373,0,0.686,11.448,0.752,15.535c-0.009,8.832,0.064,29.189-2.005,40.341c-0.844,9.41-4.173,41.2-1.252,52.368c2.928,6.276,3.189,19.355,3.257,24.807c2.242,16.185-9.35,13.221-10.273,12.277c-11.234-10.395-11.266-26.786-8.77-37.084c0.832-13.349-4.033-35.728-4.26-58.381c-0.426-12.287,0.307-27.93,0.501-38.336c0.983-22.029-1.277-41.318,8.77-47.106c5.222-1.626,31.213-9.898,38.837-16.036c2.585-2.391,2.405-10.234,2.255-13.53c-0.816-2.131-1.343-7.314-1.754-9.521c-0.433,0.554-2.855,0-4.51-7.267c-0.537-2.466,0.096-7.285,1.754-6.765c-0.599-1.659-1.477-9.227-0.501-14.533C83.871,37.754,83.724,23.768,101.429,23.768z";

//New try mask
//path_data = "M176.667,63.032c0,0,1.112,8.699,4.158,12.583c-1.222,10.668-3.575,13.82-4.666,15.079c-5.614,6.476-16.901,8.604-23.127,10.307c-12.689,3.473-12.649,23.28-14.553,47.488c-1.064,13.533-4.349,31.038-6.456,42.564c-2.811,15.375-3.939,28.454-4.049,50.225c-2.514,10.188-7.164,27.887,3.124,35.27c4.188,3.005,10.158,4.56,4.208-11.088c2.517-6.784,3.83-14.061,1.969-24.291c9.3-34.738,7.794-27.386,10.614-49.786c1.571-12.474,5.105-20.551,6.894-38.517c0.593-5.954-0.438-16.544,0.766-24.073c-0.27,3.705-0.509,9.855,0.985,17.507c0.969,4.965,4.643,21.464,5.033,33.482c0.243,7.451-1.153,10.127-2.407,13.568c-1.872,5.137-3.558,8.214-7.112,18.9c-3.748,11.269-4.332,19.757-4.924,36.467c-0.487,13.745,0.368,17.291,1.422,23.635c2.506,15.077,9.451,51.067,9.629,61.713c0.132,7.931-0.221,12.308-0.875,18.711c-0.781,7.646-0.614,14.246,2.016,27.239c3.936,19.441,5.498,31.742,7.832,42.135c2.955,9.635,2.933,12.029,1.971,17.327c-0.83,4.566-6.308,15.841-7.661,18.452c-4.535,8.753,0.051,16.384,11.599,15.1c6.975-0.773,8.292-3.923,11.339-14.964c0.721-2.609,3.397-12.163,1.572-18.627c-0.622-10.032-1.256-42.293-0.984-59.337c0.248-15.516-1.133-23.821,0.765-32.577c3.069-14.152,3.729-45.833,5.362-73.968c0.662-11.411,0.137-13.927,0.875-13.927c0.726,0,0.086,2.587,0.984,13.816c2.622,32.8,1.257,52.724,5.252,74.049c2.175,11.61,0.328,19.586,0.767,32.606c0.458,13.607-0.219,48.613-0.876,59.337c-2.134,7.494,2.273,21.614,3.173,24.292c2.042,6.069,4.048,10.452,15.319,9.19c5.859-0.655,10.392-6.516,6.018-14.991c-1.713-3.319-6.816-13.599-7.659-18.303c-1.614-9.008,0.714-11.221,1.97-17.508c2.453-12.288,4.261-21.241,7.878-42.236c0.974-5.656,3.29-12.847,1.86-27.136c-0.513-5.127-1.205-10.779-0.766-19.259c0.472-9.122,5.699-38.361,9.739-61.245c0.885-5.016,1.426-10.937,1.203-23.665c-0.264-15.15-1.131-25.064-4.924-36.437c-3.102-9.302-5.296-13.315-7.112-18.82c-1.141-3.456-2.663-6.99-2.408-13.319c0.414-10.277,4.252-29.927,5.143-33.702c0.957-4.055,1.24-12.905,0.984-17.647c0.755,3.776-0.402,14.331,0.658,24.322c2.478,23.365,5.815,26.42,7.003,38.188c1.648,16.342,2.628,18.828,10.504,50.006c-0.766,5.143-2.39,12.068,2.079,24.37c-2.915,7.216-5.341,17.404,4.486,10.833s5.16-26.889,2.845-35.094c-0.217-27.449-1.067-32.167-4.095-50.007c-1.537-9.052-5.769-30.858-6.52-43c-1.813-29.35-2.91-44.41-14.99-47.41c-13.315-3.306-19.586-6.236-22.65-10.286c-2.876-3.801-4.596-10.723-4.596-15.1c0,0,2.371-1.332,4.158-12.474c1.02,1.261,2.517-3.392,3.283-5.69c0.766-2.298,1.531-7.878-0.767-7.222c1.75-4.486,2.408-7.331,1.423-11.708s-6.311-15.209-19.039-15.209c-14.118,0-18.979,11.512-19.696,15.677c-0.907,5.268,0.146,7.477,1.531,11.161c-1.86-0.037-1.969,2.845-0.656,7.221C174.807,61.609,175.245,63.141,176.667,63.032z";

mid_area_path = new Path(path_data);
mid_area_path.opacity = 0.6;

side_area_path = new Path(side_path_data);
side_area_path.opacity = 0.6;

var p_user_height = parseInt($('#user_height_frm_3').attr('value'));

var urs_height_inch = parseInt($('#user_height_frm_3').attr('value'));
var height_track_1 = urs_height_inch * 6.647154471544715;

//var p_user_height = 72;


//handleOut_41 = new Point(mid_area_path.segments[41].handleOut);
//handleOut_40 = new Point(mid_area_path.segments[40].handleOut);
//handleIn_29 = new Point(mid_area_path.segments[29].handleIn);
//handleIn_30 = new Point(mid_area_path.segments[30].handleIn);


handleOut_41 = new Point(mid_area_path.segments[41].handleOut);
handleOut_40 = new Point(mid_area_path.segments[40].handleOut);
handleIn_29 = new Point(mid_area_path.segments[29].handleIn);
handleIn_30 = new Point(mid_area_path.segments[30].handleIn);


mid_area_path.segments[41].handleIn = 0;
mid_area_path.segments[41].handleOut = 0;
mid_area_path.segments[40].handleIn = 0;
mid_area_path.segments[40].handleOut = 0;
mid_area_path.segments[29].handleIn = 0;
mid_area_path.segments[29].handleOut = 0;
mid_area_path.segments[30].handleIn = 0;
mid_area_path.segments[30].handleOut = 0;

mid_area_path.segments[42].handleIn = 0;
mid_area_path.segments[42].handleOut = 0;

mid_area_path.segments[28].handleIn = 0;
mid_area_path.segments[28].handleOut = 0;

mid_area_path.segments[41].point.y = mid_area_path.segments[40].point.y;
mid_area_path.segments[42].point.y = mid_area_path.segments[40].point.y;
mid_area_path.segments[29].point.y = mid_area_path.segments[40].point.y;
mid_area_path.segments[28].point.y = mid_area_path.segments[40].point.y;
mid_area_path.segments[30].point.y = mid_area_path.segments[40].point.y;



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


  //var user_shoulder_width = parseInt($("#user_back_frm_3").attr("value"));
  
  var user_shoulder_width = 16;

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

//mid_area_path.segments[11].point.x += 10;
//mid_area_path.segments[12].point.x += 10;

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
      //mid_area_path.scale(1, 1.01);
      
      //mask_retake_full.scale(1, 1.01);
      
      mid_area_path.pivot = new Point(mid_area_path.bounds.bottomCenter.x,mid_area_path.bounds.bottomCenter.y);
      mid_area_path.position = new Point(160,426 - adj_btm_fix);


        mask_retake_full.pivot = new Point(mask_retake_full.bounds.bottomCenter.x,mask_retake_full.bounds.bottomCenter.y);
        mask_retake_full.position = new Point(160,scr_height - full_adj_btm_fix);


        mid_area_path.segments[41].point.y += 9.375;
        mid_area_path.segments[41].handleOut = new Point(-10,0);
        mid_area_path.segments[40].handleOut = new Point(0,14);

        mid_area_path.segments[29].point.y += 9.375;
        mid_area_path.segments[29].handleIn = new Point(9,0);
        mid_area_path.segments[30].handleIn = new Point(0,9);



        mask_retake_full.segments[41].point.y += 12.5; 
        mask_retake_full.segments[41].handleOut = new Point(-12.5,0);
        mask_retake_full.segments[40].handleOut = new Point(0,12.5);

        mask_retake_full.segments[29].point.y += 12.5;
        mask_retake_full.segments[29].handleIn = new Point(12.5,0);;
        mask_retake_full.segments[30].handleIn = new Point(0,12.5);
        
    }
    
    
    if (liquid_mask.device_type == "iphone6"){

    
      //
      //New fix
      mid_area_path.scale(1.19725415851272,1.19725415851272);
      mask_retake_full.scale(1.19725415851272,1.19725415851272);
            
      mid_area_path.scale(0.750, 0.750);
      
      //side_area_path.pivot = new Point(side_area_path.bounds.bottomCenter.x,side_area_path.bounds.bottomCenter.y);
      //side_area_path.position = new Point(50,30);

      mid_area_path.pivot = new Point(mid_area_path.bounds.bottomCenter.x,mid_area_path.bounds.bottomCenter.y);
      mid_area_path.position = new Point(screen.width/2,500 - 25.125);
      
      mask_retake_full.pivot = new Point(mask_retake_full.bounds.bottomCenter.x,mask_retake_full.bounds.bottomCenter.y);
      mask_retake_full.position = new Point(screen.width/2,667 - adj_btm_fix); //Fixed for iphone 6 "621"
      


        mid_area_path.segments[41].point.y += 16.5;
        mid_area_path.segments[41].handleOut = new Point(-16.5,0);
        mid_area_path.segments[40].handleOut = new Point(0,16.5);

        mid_area_path.segments[29].point.y += 16.5;
        mid_area_path.segments[29].handleIn = new Point(16.5,0);
        mid_area_path.segments[30].handleIn = new Point(0,16.5);





        mask_retake_full.segments[41].point.y += 22; 
        mask_retake_full.segments[41].handleOut = new Point(-22,0);
        mask_retake_full.segments[40].handleOut = new Point(0,22);

        mask_retake_full.segments[29].point.y += 22;
        mask_retake_full.segments[29].handleIn = new Point(22,0);
        mask_retake_full.segments[30].handleIn = new Point(0,22);
        
        
        var final_height_point = 667 - ((height_track_1 * 1.19725415851272) + 33.5);
        var final_height = (height_track_1 * 1.19725415851272) + 22;
        
        
        var final_height_point_short = final_height_point * 0.75;
        var final_height_short = final_height * 0.75;
        

        var side_m_point_full = new Point(87.5, final_height_point);
        var side_m_size_full = new Size(200, final_height);
        var side_m_path_full = new Path.Rectangle(side_m_point_full, side_m_size_full);
        
        var side_m_point_short = new Point(87.5, final_height_point_short);
        var side_m_size_short = new Size(200, final_height_short);
        var side_m_path_short = new Path.Rectangle(side_m_point_short, side_m_size_short);


        
//        mid_area_path.segments[41].point.y += 19;
//        mid_area_path.segments[41].handleOut = handleOut_41;
//        mid_area_path.segments[40].handleOut = handleOut_40;
//
//        mid_area_path.segments[29].point.y += 19;
//        mid_area_path.segments[29].handleIn = handleIn_29;
//        mid_area_path.segments[30].handleIn = handleIn_30;
//        
//        mask_retake_full.segments[41].point.y += 24.5; 
//        mask_retake_full.segments[41].handleOut = handleOut_41;
//        mask_retake_full.segments[40].handleOut = handleOut_40;
// 
//        mask_retake_full.segments[29].point.y += 24.5;
//        mask_retake_full.segments[29].handleIn = handleIn_29;
//        mask_retake_full.segments[30].handleIn = handleIn_30;
        
    }








    mid_area_path.selected = true;
    mid_area_path.strokeWidth = 1;
    mid_area_path.strokeColor = new Color(1, 0, 0);
    mid_area_path.opacity = 0.85;
    
    mask_retake_full.selected = true;
    mask_retake_full.strokeWidth = 1;
    mask_retake_full.strokeColor = new Color(1, 0, 0);
    mask_retake_full.opacity = 0.85;

    //side_mask = new Path("M0,0h100v400h-100V-400z");
    //side_mask.pivot = new Point(50,400);
    //side_mask.position = new Point(160, 420);




    $("#svg_path_data").attr("value", mid_area_path.pathData);
    $("#svg_path_data_full").attr("value", mask_retake_full.pathData);
    
    $("#side_svg_path_data").attr("value", side_m_path_short.pathData);
    $("#side_svg_path_data_full").attr("value", side_m_path_full.pathData);
    
    $("#back_svg_path_data").attr("value", mid_area_path.pathData);
    $("#back_svg_path_data_full").attr("value", mask_retake_full.pathData);
    
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
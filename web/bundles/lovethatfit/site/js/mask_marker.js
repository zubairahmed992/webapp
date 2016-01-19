main_layer = new Layer();
main_layer.activate();

hitOptions = {
	segments: true,
	stroke: false,
	fill: false,
	tolerance: 22
};


change_x_pos_diff = 0;
change_y_pos_diff = 0;
inc_ratio = 1;
curr_screen_height = 505;
center_pos = 160;
def_pos_x = -500;
def_path_diff = 500;

gap_top_head = -20;

curr_view = "normal";
curr_crop = "normal";

hand_cursor = false;

dv_user_px_h = parseInt($("#dv_user_px_height").attr("value"));
dv_top_bar = parseInt($("#dv_top_bar").attr("value"));
dv_bottom_bar = parseInt($("#dv_bottom_bar").attr("value"));
dv_per_inch_px = parseInt($("#dv_per_inch_px").attr("value"));
dv_type = $("#dv_type").attr("value");
dv_scr_h = parseInt($("#dv_scr_h").attr("value"));
dv_edit_type = $("#dv_edit_type").attr("value");


dv_gap_top = 26;
dv_gap_bottom = 32;

//Total height of iPhone5 - gap from top and bottom, devide by max height decided (74)//
//fixed_px_inch_ratio = 6.891;

//iPhone5 and 6 px per inch

croped_img_path = $("#hdn_user_cropped_image_url").attr('value');


//true
//alert(croped_img_path);



//chk_no_img_path = true;

//////// From JS file --- End

$(document).ready(function() {
    //document.getElementById("canv_mask").setAttribute("height", window.screen.height);
    //document.getElementById("canv_mask").setAttribute("width", window.screen.width);
    createBlob();
});

function createBlob() {

    

/////////////////////////////////////////// Foot adjustment //////////////////////////////////////////

user_img_url = croped_img_path;
user_image = new Raster(user_img_url);
user_image.on('load', function() {
    user_image.position = new Point(160,568/2);
    user_image.pivot = new Point(0,(568/2) - dv_gap_bottom);
    //preset_user_image(move_up_down, move_left_right, img_rotate);
    
});


}
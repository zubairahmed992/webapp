$(document).ready(function() {
    load_user_masks();
});

function load_user_masks(){
    var json_data = jQuery.parseJSON($("#json_data_static").html());
    var p_extra_foot_area = 22;
    for(var i = 0; i < json_data.length; i++) {
      
      if(json_data[i].email == "050315.buf.056@ltf.com" || json_data[i].email == "050315.buf.103@ltf.com"){
        user_mask = new Path(json_data[i].svg_paths);
        user_mask.pivot = new Point(user_mask.bounds.bottomCenter.x,user_mask.bounds.bottomCenter.y - p_extra_foot_area); /// Setting pivot point before x, y setting, as it is done in registration///
        user_mask.position = new Point(json_data[i].mask_x, json_data[i].mask_y);
        user_mask.style = {
	stroke: 1,
	strokeColor: {
                hue: Math.random() * 360,
                saturation: 1,
                brightness: 1
            }
        }
        //user_mask.selected = true;
    }
    };
    //mid_area_path = new Path(pathData);
    //alert(json_data);
}

main_layer = new Layer();
main_layer.activate();
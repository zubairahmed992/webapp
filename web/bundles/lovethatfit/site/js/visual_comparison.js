$(document).ready(function() {
    alert("sd");
    //load_user_masks();
});

function load_user_masks(){
    var json_data = jQuery.parseJSON($("#user_data_placeholder").html());
    
    
    
    for(var i = 0; i < json_data.length; i++) {
      
      //if(json_data[i].email == "050315.buf.056@ltf.com" || json_data[i].email == "050315.buf.103@ltf.com" || json_data[i].email == "050315.buf.103@ltf.com"){
        user_mask = new Path(json_data[i].svg_paths);
        
        ////---- Align mask markers from bottom, same as in iPhone5////
        ////var p_extra_foot_area = 22; //For foot height adjustment
        ////user_mask.pivot = new Point(user_mask.bounds.bottomCenter.x,user_mask.bounds.bottomCenter.y - p_extra_foot_area); /// Setting pivot point before x, y setting, as it is done in registration///
        ////user_mask.position = new Point(json_data[i].mask_x, json_data[i].mask_y);
        ////----------------------------------/////
        
        ////---- Align mask markers from Top, head point////
        user_mask.pivot = new Point(user_mask.bounds.topCenter.x,user_mask.bounds.topCenter.y);
        user_mask.position = new Point(320/2, 0);
        
        
        user_mask.style = {
	stroke: 1,
	strokeColor: {
                hue: Math.random() * 360,
                saturation: 1,
                brightness: 1
            }
        }
        //user_mask.selected = true;
    
    //////------commenting if closing bras }
    
    };
    //mid_area_path = new Path(pathData);
    //alert(json_data);
}


//var but_getdata_url = curr_path_prefix + "bundles/lovethatfit/site/images/back_button_app.png";
//var but_back_top = new Raster(but_back_top_url);

//but_back_top.position = new Point(25, 22);

main_layer = new Layer();
main_layer.activate();
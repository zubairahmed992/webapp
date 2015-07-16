paper.install(window);
paper.setup('canv_compare');
$(document).ready(function() {
    
    load_user_masks();
});

function load_user_masks(){
    var json_data = jQuery.parseJSON($("#user_data_placeholder").html());    
    for(var i = 0; i < json_data.length; i++) {
        
        //if(json_data[i].email == "050315.buf.056@ltf.com" || json_data[i].email == "050315.buf.103@ltf.com" || json_data[i].email == "050315.buf.103@ltf.com"){
        user_mask = new Path(json_data[i].svg_paths);
        user_mask.name = 'usermask_'+json_data[i].id;
        ////---- Align mask markers from bottom, same as in iPhone5////
        ////var p_extra_foot_area = 22; //For foot height adjustment
        ////user_mask.pivot = new Point(user_mask.bounds.bottomCenter.x,user_mask.bounds.bottomCenter.y - p_extra_foot_area); /// Setting pivot point before x, y setting, as it is done in registration///
        ////user_mask.position = new Point(json_data[i].mask_x, json_data[i].mask_y);
        ////----------------------------------/////
        
        ////---- Align mask markers from Top, head point////
        user_mask.pivot = new Point(user_mask.bounds.topCenter.x,user_mask.bounds.topCenter.y);
        user_mask.position = new Point(320/2, 0);
        user_mask.style = {
	stroke: 1
        }
        
        
        reset_color = new Color(Math.random(1),Math.random(1),Math.random(1));
        
        //reset_color = new Color({
        //                hue: Math.round(Math.random() * 360),
	//		saturation: 1,
	//		brightness: 1});
        
        user_mask.strokeColor = reset_color;
        //var rgb_color = reset_color.convert('rgb');
                
                
        var masks_m_down = new Tool();
        masks_m_down.onMouseDown = onMouseDown;
        function onMouseDown(event) {
            console.log("QQQQQQQQQQQQ");
        }
                
        var css_color = reset_color.toCSS('rgb');
        var item_name = "'"+user_mask.name+"'";
        $("#mask_ids").append('<div class="mask_ref_id"><a onclick="interact_mask('+item_name+');">'+json_data[i].id+'</a><div class="mask_spot" style="background: ' + css_color + '"></div></div>');
       
       
       
       //////------commenting if closing bras }
       
       
       
       
    //user_mask.strokeColor.hue += hue_value;
    
    
    
    //user_mask.strokeColor.saturation = 0.5;
    //user_mask.strokeColor.brightness = 0;
    //user_mask.strokeColor.lightness 
    
    
    
    
    view.update([true]);
    };
    
    
}

function interact_mask(action_item){
    var masks_db = project.activeLayer.children;
    if(masks_db[action_item].selected){
        masks_db[action_item].selected = false;
    }else{
        masks_db[action_item].selected = true;
    };
    view.update([true]);
}

//main_layer = new Layer();
//main_layer.activate();
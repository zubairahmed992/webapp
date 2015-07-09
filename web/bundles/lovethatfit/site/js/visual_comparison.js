$(document).ready(function() {
    load_user_masks();
});

function load_user_masks(){
    var json_data = jQuery.parseJSON($("#json_data_static").html());
    
    for(var i = 0; i < json_data.length; i++) {
      
      if(json_data[i].email == "050315.buf.056@ltf.com" || json_data[i].email == "050315.buf.103@ltf.com"){
        user_mask = new Path(json_data[i].svg_paths);
        user_mask.style = {
	stroke: 1,
	strokeColor: '#ffcc00'
        }
    }
    };
    //mid_area_path = new Path(pathData);
    //alert(json_data);
}

main_layer = new Layer();
main_layer.activate();
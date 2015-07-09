$(document).ready(function() {
    load_json();
});

function load_json(){
    var json_data = jQuery.parseJSON($("#json_data_static").html());
    mid_area_path = new Path(pathData);
    alert(json_data);
}

main_layer = new Layer();
main_layer.activate();

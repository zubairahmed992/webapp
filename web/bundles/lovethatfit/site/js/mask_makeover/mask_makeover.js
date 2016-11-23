paper.install(window);
$(document).ready(function() {
   paper.setup('canv_mask_makeover');
   
//   lyr_user_img = new Layer();
//   project.addLayer(lyr_user_img);
      
   init();
   
   lyr_default_mask = new Layer();
   project.addLayer(lyr_default_mask);
   load_user_masks();
   
   
    lyr_cle = new Layer();
    project.addLayer(lyr_cle);
    lyr_cle.activate();
   
    full_mask.insertAbove(set_circle_in());
    full_mask.insertAbove(set_circle_out());
   
   lyr_action_bar = new Layer();
   project.addLayer(lyr_action_bar);
   lyr_action_bar.activate();
   
   set_action_bar();
   
   console.log(project.layers);
   //paper.project.view.scale(5,5);

});

function set_action_bar(){
    scr1_but_save_icon_url = "../../../../../bundles/lovethatfit/site/images/scr1_save_btn.png";
    scr1_but_save_icon = new Raster(scr1_but_save_icon_url);
    scr1_but_save_icon.position = new Point(60, 18);
}

function set_circle_in(){
    circle_in = new Path.Circle({
        center: new Point(10, 10),
        radius: 2,
        fillColor: 'red'
    });
    return circle_in;
}
function set_circle_out(){
    circle_out = new Path.Circle({
        center: new Point(10, 10),
        radius: 2,
        fillColor: 'blue'
    });
    return circle_out;
}

var maskConfig = {
    dv_scr_h: 1280,
    dv_scr_w: 960,
    dv_type: $("#dv_type").attr("value"),
    dv_model: $("#dv_model").attr("value"),
    dv_px_per_inch_ratio: 15.29166666666667,
    globle_pivot:64,
    user_gender: $('#user_gender').attr('value'),
    default_user_mask: function(){return "M500,1000c-2.38736,11.35776 -2.26905,16.3611 -1.54415,18.50617c-3.62453,-1.27388 -1.11636,8.25141 -0.74665,9.86215c2.14331,9.22043 3.38085,8.82214 3.6237,8.25683c0.83243,2.34254 5.2947,13.40318 6.51375,15.78702c0.18243,4.26174 1.30028,16.64583 -1.38671,19.04644c-6.85278,6.08027 -9.6741,10.29194 -32.15949,17.59704c-12.56263,4.56762 -14.44599,5.79914 -19.37536,39.35227c-0.26097,4.57537 -6.37872,23.93648 -9.2276,44.1107c-3.39256,31.76558 -11.66357,36.79734 -12.34619,96.76628c-5.99618,16.68041 5.20711,34.14104 20.40719,45.79435c1.65399,1.47393 18.40491,1.79477 4.1907,-26.28728c1.35437,-2.67553 -9.23922,-17.32562 -12.46989,-30.50836c0.50139,-1.77981 13.19708,-27.59342 24.21807,-88.22705c2.81264,-24.87607 7.05409,-17.80689 8.15474,-27.5681c0.37937,-2.16572 -5.67212,-21.09574 -3.67138,-21.09574c0.96292,0.07486 2.31106,8.95043 2.72546,14.55832c3.4566,23.70544 7.94507,32.24829 8.09368,51.23382c-0.61013,11.13834 -1.55372,10.27283 -3.96041,17.66957c-2.68215,7.34124 -4.52648,8.17862 -7.59042,25.42434c-1.84489,21.13833 -5.22165,14.2581 -6.01301,34.20258c1.52351,22.32702 -3.04196,17.09033 0.94261,49.31022c6.6631,34.99221 13.00829,36.12566 14.89547,66.39279c-0.00846,13.94423 1.49898,2.0413 0.18327,22.32651c-1.53197,24.22687 -3.85558,24.63368 -3.08959,36.94523c3.74535,30.02448 6.54301,36.59262 8.65974,53.47438c0.9605,10.55884 4.91049,12.31929 4.49487,31.22737c-1.05836,12.05213 -4.46645,14.47082 -6.72816,20.10194c-1.48485,5.51109 -3.90519,21.63114 18.91366,21.58765c2.81626,0.04646 9.46291,9.88861 11.97108,-15.60635c0.32138,-7.07666 -7.26873,-35.38836 -8.64726,-40.58066c2.06478,-33.89387 3.47161,-41.53116 5.64754,-67.7224c0.23439,-8.63835 3.71881,-30.64942 3.44456,-37.42535c3.77555,-17.28831 -0.7482,-45.82158 1.1728,-89.20175c0.00604,-9.4734 2.9687,-21.75362 5.94202,-21.75362c2.85372,0 4.43338,7.567 4.95531,20.69812c1.03541,43.41889 0.41295,71.18319 3.96258,88.07011c-0.1184,6.63267 4.02671,29.88509 4.0545,38.49117c2.51543,26.29708 2.77951,33.85435 5.23211,67.86567c-1.60446,5.02839 -5.60116,33.71228 -6.05181,40.91026c2.28708,25.45147 8.90185,14.8245 11.03549,14.74965c21.18298,0.07128 19.25092,-17.85405 17.07983,-23.0799c-2.12398,-5.68146 -3.66274,-7.30278 -4.05056,-19.12259c-0.68141,-18.79063 1.85583,-19.10271 2.90815,-29.68995c1.85455,-17.0418 2.40223,-17.06863 5.95185,-46.80917c0.85901,-12.57613 -0.13857,-20.02296 -1.74786,-44.20207c-1.58875,-19.7754 -0.4724,-6.56427 -0.76961,-20.10323c1.76031,-30.36264 9.67093,-31.75035 15.92084,-66.5709c4.52583,-31.94498 -0.08435,-25.80973 1.05013,-47.77021c-0.92063,-20.11226 -8.63569,-21.28753 -10.22927,-42.18838c-3.17509,-17.19538 -2.85028,-9.41221 -5.77527,-16.44499c-2.6145,-7.55291 -0.93708,-9.72482 -1.67648,-20.61149c-0.38662,-19.33271 4.52593,-23.43751 8.07555,-46.76737c0.72007,-5.82601 2.25288,-13.51573 3.02974,-13.51573c1.55492,-0.00129 -4.67017,24.78805 -4.49136,27.00539c1.01245,9.82704 0.91682,-4.50801 4.10762,20.54874c9.57601,60.64267 25.92811,77.09065 26.47662,78.78915c-3.55204,12.91816 -11.91348,38.0215 -11.12816,40.93709c-10.20789,27.67162 3.53271,15.32574 5.65427,13.86601c18.06467,-11.98501 20.67922,-20.64865 17.03777,-37.31099c-0.33104,-60.25289 -9.41998,-65.52988 -12.52258,-96.75209c-3.76347,-20.20777 -7.11143,-44.56759 -7.7433,-49.16103c-4.32407,-33.72221 -8.2013,-32.56472 -22.40101,-36.8626c-23.75519,-7.3335 -21.2397,-8.23747 -28.80289,-14.14221c-3.61366,-2.651 -2.57258,-16.24469 -2.78522,-20.33219c1.34229,-2.12054 6.27332,-15.93777 6.55362,-16.96771c2.29433,2.31414 3.89154,-9.23644 4.3591,-10.64971c3.23187,-10.02451 -0.29842,-9.17707 -0.72611,-8.8828c4.11868,-7.42771 -1.97389,-15.71732 -2.39071,-17.55134c-4.61041,-17.13214 -24.65407,-17.56709 -24.65407,-17.56709c-20.93409,0 -24.35565,16.36937 -24.73743,17.56709z"},
    default_user_mask_height_px: 430,
    adjusted_user_mask: function(){return},
    user_img_url: $("#hdn_user_cropped_image_url").attr('value'),
    user_height_inch: 72,
    user_height_px: 72 * 15.29166666666667, 
    head_percent: 12,
    neck_percent: 4,
    torso_percent: 42,
    inseam_percent:42,
    arm_percent:46,
    toe_shape_px: 42
}
var default_mask_ratio = maskConfig.default_user_mask_height_px / maskConfig.user_height_inch;
var required_mask_ratio = maskConfig.dv_px_per_inch_ratio / default_mask_ratio;

var active_items = {
    segment: -1,
    drag: false,
    cir_in: false,
    cir_out: false
}
var recent_items = {
    mask: []
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
        user_image.position = new Point(maskConfig.dv_scr_w/2,maskConfig.dv_scr_h/2);
        //preset_user_image(move_up_down, move_left_right, img_rotate);
//        if(old_account_img){}else{
//            preset_user_image(parseInt(image_actions_count.move_up_down),parseInt(image_actions_count.move_left_right),parseFloat(image_actions_count.img_rotate));
//        }
    });
//console.log(paper.project.layers[1]);
}
function load_user_masks(){    
    var full_mask_path = maskConfig.default_user_mask();    
    full_mask = new Path(full_mask_path);
    
    full_mask.segments[41].handleIn = 0;
    full_mask.segments[41].handleOut = 0;
    
    full_mask.segments[40].handleIn = 0;
    full_mask.segments[40].handleOut = 0;
    
    full_mask.segments[29].handleIn = 0;
    full_mask.segments[29].handleOut = 0;
    
    full_mask.segments[30].handleIn = 0;
    full_mask.segments[30].handleOut = 0;

    full_mask.segments[42].handleIn = 0;
    full_mask.segments[42].handleOut = 0;

    full_mask.segments[28].handleIn = 0;
    full_mask.segments[28].handleOut = 0;

    full_mask.segments[41].point.y = full_mask.segments[40].point.y;
    full_mask.segments[42].point.y = full_mask.segments[40].point.y;
    full_mask.segments[29].point.y = full_mask.segments[40].point.y;
    full_mask.segments[28].point.y = full_mask.segments[40].point.y;
    full_mask.segments[30].point.y = full_mask.segments[40].point.y;
    
    
    full_mask.style = {
        strokeColor: 'white',
        strokeWidth: 1
    };
    full_mask.opacity = 0.1;
    full_mask.selected = true;
    full_mask.scale(required_mask_ratio);
    full_mask.pivot = new Point(full_mask.bounds.bottomCenter.x,full_mask.bounds.bottomCenter.y);
    full_mask.position = new Point(maskConfig.dv_scr_w/2 + 4,maskConfig.dv_scr_h - maskConfig.globle_pivot);
    
    
    full_mask.segments[41].point.y += maskConfig.toe_shape_px; 
    full_mask.segments[41].handleOut = new Point(-maskConfig.toe_shape_px,0);
    full_mask.segments[40].handleOut = new Point(0,maskConfig.toe_shape_px);

    full_mask.segments[29].point.y += maskConfig.toe_shape_px;
    full_mask.segments[29].handleIn = new Point(maskConfig.toe_shape_px,0);
    full_mask.segments[30].handleIn = new Point(0,maskConfig.toe_shape_px);
   
    //view.update([true]);
    //console.log(paper.project.layers);
    
    
}

function getPathArrayJson(){
    var mp_array=[];
    for(var i = 0; i < full_mask.segments.length; i++) {
        mp_array.push([full_mask.segments[i].point.x * 2, full_mask.segments[i].point.y * 2]);

        console.log(full_mask.segments[i].point.x * 2 + " ::: "+full_mask.segments[i].point.y * 2);
    };

    return JSON.stringify(mp_array);
}


function onMouseDown(event) {
    var hitResult = paper.project.hitTest(event.point, hitOptions);
    
    console.log(hitResult);
    if(hitResult.type == "segment"){
        
        if (event.modifiers.shift) {
            
        }
        
        active_items.drag = true;
        
        if(hitResult.segment.point != active_items.segment.point){
            active_items.segment.selected = false;
        }
        for(i=0; i<full_mask.segments.length; i++){
            if(full_mask.segments[i].point == hitResult.segment.point){
                
                active_items.segment = full_mask.segments[i];
                //active_items.segment.selected = true;                
                circle_in.position = new Point(full_mask.segments[i].point + full_mask.segments[i].handleIn);
                circle_out.position = new Point(full_mask.segments[i].point + full_mask.segments[i].handleOut);
                console.log(active_items.segment);
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
            alert(hitResult.location);
            var hit_location = hitResult.location;
            full_mask.insert(hit_location.index + 1, event.point);}
        }
        
        
    if(hitResult.item == scr1_but_save_icon){
        upload();
    }    
    
}


function onMouseDrag(event) {
    if(active_items.drag){
        active_items.segment.point += event.delta;
        circle_in.position += event.delta;
        circle_out.position += event.delta;
    }
    if(active_items.cir_in){
        active_items.cir_out = false;
        circle_in.position += event.delta;
        active_items.segment.handleIn += new Point(event.delta);
    }
    if(active_items.cir_out){
        active_items.cir_in = false;
        circle_out.position += event.delta;
        active_items.segment.handleOut += new Point(event.delta);
    }
    
}
function onMouseUp(event) {
    active_items.drag = false;
    active_items.cir_in = false;
    active_items.cir_out = false;
}

function upload(){
    $("#img_path_json").attr("value",getPathArrayJson());
    
    var $url=$('#marker_update_url').attr('value');
    var value_ar = {
        auth_token:$('#user_auth_token').attr('value'),
        mask_x: $('#mask_x').attr('value'),
        mask_y: $('#mask_y').attr('value'),
        marker_json:$('#img_path_json').attr('value'),
        svg_path:$('#img_path_paper').attr('value')};

    $.ajax({
        type: "POST",
        url: "$url",//"http://localhost/cs-ltf-webapp/web/app_dev.php/user/marker/save",
        data: value_ar,
        success: function(data){
            console.log(data);
        },
        failure: function(errMsg) {
            alert(errMsg);
        }
    });

//alert(JSON.stringify(value_ar));


}

//main_layer = new Layer();
//main_layer.activate();

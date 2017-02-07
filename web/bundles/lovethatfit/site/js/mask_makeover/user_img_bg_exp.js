paper.install(window);
var dom_event_interaction_s1;
$(document).ready(function() {
   paper.setup('canv_mask_makeover');
   
//   dom_event_interaction_s1 = new Tool();
//   dom_event_interaction_s1.onMouseDown = onMouseDown;
//   dom_event_interaction_s1.onMouseDrag = onMouseDrag;
//   dom_event_interaction_s1.onMouseUp = onMouseUp;
//   dom_event_interaction_s1.onKeyDown = onKeyDown;
//   dom_event_interaction_s1.onKeyUp = onKeyUp;

init();
});

function init(){
    var user_img_path = "/bundles/lovethatfit/site/images/test_img_1.png";
    user_image_img = new Raster(user_img_path);
    user_image_img.on('load', function() {
        user_image_img.position = new Point(480,640);
    });
}
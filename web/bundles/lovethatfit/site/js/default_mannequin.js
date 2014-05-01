function set_default_canv(){
//    var context = document.getElementById("default_m_canv").getContext("2d");
//    var canv_part = new Image();
    
    var dmCanv = document.getElementById('default_m_canv');
    var dmCanvContext = dmCanv.getContext('2d');
    var mainPath = "http://localhost/webapp/web/bundles/lovethatfit/site/images/dm_parts/";
    var dmPart1 = mainPath + "part_1.png";
    var dmPart2 = mainPath + "part_2.png";
    var dmPart3 = mainPath + "part_3.png";
    var dmPart4 = mainPath + "part_4.png";
    var dmPart5 = mainPath + "part_5.png";
    var dmPart6 = mainPath + "part_6.png";
    var dmPart7 = mainPath + "part_7.png";
    var dmPart8 = mainPath + "part_8.png";
    
// load image from data url
        var mPart = new Image();
        mPart.onload = function() {
          dmCanvContext.drawImage(this, 0, 0);
        };

        mPart.src = dmPart1;


   // alert(mainPath);
}
$(document).ready(function() { 
   //set_default_canv();
   
   var dm_path = $("#hdn_url_1").html();
   
   //alert(dm_path);
   
   $.ajax({
                        url: dm_path,
                        cache: false,
                        success: function(dm_data){
                           //alert(dm_data);
                          var json = $.parseJSON(dm_data);  
                            
                            //alert(json.neck);
                            
                            //$(main_container_hldr + " #fitting_alerts_ul_"+new_prod_id).html(curr_prod_alerts);
                        }
                    }); 
   
   create_default_mannequin(
    );
   
   function create_default_mannequin() {
        var actual_user_height = parseInt($("#hdn_user_height").attr("value")) * 6;
   
        var mid_width_final = 64;
        var lft_width_final = 10;
        var rgt_width_final = 10;

        var full_mid_width_final = mid_width_final + lft_width_final + rgt_width_final;
        var arm_gap_adj = (lft_width_final + rgt_width_final)/5;

        var rgt_arm_width = mid_width_final / 1.82;
        var lft_arm_width = mid_width_final / 1.82;
        var full_body_width = rgt_arm_width + full_mid_width_final + lft_arm_width - (arm_gap_adj * 2);

        $("#dm_mid_body_hldr").width(full_mid_width_final);
        $("#dm_mid_body_hldr").css({marginLeft: -arm_gap_adj, marginRight: -arm_gap_adj});
        $("#dm_part_7 .dm_part_img").width(full_mid_width_final);
        $("#dm_part_2 .dm_part_img").width(full_mid_width_final);
        $("#dm_part_1 .dm_part_img").width(full_mid_width_final);

        $(".dm_part_mid img").width(mid_width_final);
        $(".dm_part_lft img").width(lft_width_final);
        $(".dm_part_rgt img").width(rgt_width_final);

        $("#dm_rgt_arm_hldr").width(rgt_arm_width);
        $("#dm_lft_arm_hldr").width(lft_arm_width);

        $("#dm_lft_arm_hldr #dm_lft_arm_part_1 .dm_part_img").width(lft_arm_width);
        $("#dm_lft_arm_hldr #dm_lft_arm_part_2 .dm_part_img").width(lft_arm_width);

        $("#dm_rgt_arm_hldr #dm_rgt_arm_part_1 .dm_part_img").width(rgt_arm_width);
        $("#dm_rgt_arm_hldr #dm_rgt_arm_part_2 .dm_part_img").width(rgt_arm_width);

        $("#default_dummy_hldr #default_dummy_inner").width(full_body_width);

        
        //var torso_inseam = (82.51*actual_user_height)/100
        
        var dm_torso = (40.73*actual_user_height)/100;
        var dm_inseam = (41.78*actual_user_height)/100;
        
        //console.log("torso: " + dm_torso);
        //console.log("actual_user_height " + actual_user_height);
        
        /*
        
        $("#dm_part_1 img").height((7*actual_user_height)/100);
        $("#dm_part_2 img").height((46.78*actual_user_height)/100);
        $("#dm_part_3 img").height((18.83*actual_user_height)/100);
        $("#dm_part_4 img").height((6.95*actual_user_height)/100);
        $("#dm_part_5 img").height((3.15*actual_user_height)/100);
        $("#dm_part_6 img").height(29);
        $("#dm_part_7 img").height((7.7*actual_user_height)/100);
        $("#dm_part_8").height((13.74*actual_user_height)/100);   
        $("#dm_part_8 img").height((13.74*actual_user_height)/100); */
        
        
        $("#dm_part_1 img").height((7*actual_user_height)/100);
        $("#dm_part_2 img").height(dm_inseam);
        $("#dm_part_3 img").height((52.69*dm_torso)/100);
        $("#dm_part_4 img").height((19.44*dm_torso)/100);
        $("#dm_part_5 img").height((8.81*dm_torso)/100);
        $("#dm_part_6 img").height((19*dm_torso)/100);
        $("#dm_part_7 img").height((7.7*actual_user_height)/100);
        $("#dm_part_8").height((13.74*actual_user_height)/100);   
        $("#dm_part_8 img").height((13.74*actual_user_height)/100);



        $("#dm_rgt_arm_hldr").css("top",(21.44*actual_user_height)/100);
        $("#dm_lft_arm_hldr").css("top",(21.44*actual_user_height)/100);

        $("#dm_rgt_arm_part_1 img").height($("#dm_part_6 img").height());
        $("#dm_lft_arm_part_1 img").height($("#dm_part_6 img").height());

        $("#dm_rgt_arm_part_2 img").height((35*actual_user_height)/100);
        $("#dm_lft_arm_part_2 img").height((35*actual_user_height)/100);
   }
   
   
});
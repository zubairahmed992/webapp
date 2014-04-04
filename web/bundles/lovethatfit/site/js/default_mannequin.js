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
   var actual_user_height = parseInt($("#hdn_user_height").attr("value")) * 6;
   $("#dm_part_1 img").height((7*actual_user_height)/100);
   $("#dm_part_2 img").height((46.78*actual_user_height)/100);
   $("#dm_part_3 img").height((18.83*actual_user_height)/100);
   $("#dm_part_4 img").height((6.95*actual_user_height)/100);
   $("#dm_part_5 img").height((3.15*actual_user_height)/100);
   $("#dm_part_6 img").height(29);
   $("#dm_part_7 img").height((7.7*actual_user_height)/100);
   $("#dm_part_8").height((13.74*actual_user_height)/100);   
   $("#dm_part_8 img").height((13.74*actual_user_height)/100);    
   
   $("#dm_rgt_arm_hldr").css("top",(21.44*actual_user_height)/100);
   $("#dm_lft_arm_hldr").css("top",(21.44*actual_user_height)/100);
   
   $("#dm_rgt_arm_part_1 img").height($("#dm_part_6 img").height());
   $("#dm_lft_arm_part_1 img").height($("#dm_part_6 img").height());
   
   $("#dm_rgt_arm_part_2 img").height((35*actual_user_height)/100);
   $("#dm_lft_arm_part_2 img").height((35*actual_user_height)/100);
   
   //$("#dm_lft_arm_part_1 img").height((35*actual_user_height)/100);
   
   
   //$("#dm_h_part_1").css("top",$("#dm_part_6 img").offset());
   
   //$("#dm_part_8 img").height((13.74*actual_user_height)/100);
});
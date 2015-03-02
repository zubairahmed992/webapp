 $(document).ready(function() {
       var $scrollingDiv = $("#ltf_fitting_room");
       $(window).scroll(function(){
              var top_check=window.scrollY;
              if(top_check>=55){
                                                    $scrollingDiv
                                    .stop()
                                    .animate({"top": ($(window).scrollTop()-72) + "px"}, "slow" );
                                            }
                                            else {$scrollingDiv
                                    .stop()
                                    .animate({"top": 0 + "px"}, "slow" );}

                    });  
                    
                    
 
              
            $(".fitting_alert_link").toggle(
                function () {
                  $("#fitting_alerts").fadeIn(300);
                },
                function () {
                  $("#fitting_alerts").fadeOut(300);
                }
   ); 
  });      
 
function product_drag_limit_check(prod_type){
        var prod_axis = $(prod_type).offset();

        if(prod_axis.left < $(prod_type).parent().offset().left - 30 || prod_axis.left > $(prod_type).parent().offset().left + 30 || prod_axis.top < $(prod_type).parent().offset().top - 30 || prod_axis.top > $(prod_type).parent().offset().top + 30){
            $( prod_type ).draggable( "option", "revert", false );
        }
        else{
            $( prod_type ).draggable( "option", "revert", true );
        }
    }
    
    function remove_alert_box(){
        if($(".fitting_alerts_cnt ul") != true)
            {
                $("#fitting_alerts").fadeOut();
            }
    }
    function remove_fitting_alerts(prod_type){
        if($( prod_type ).parent().attr('id') == "top_dropable")
            {
                $("#curr_top_alerts ul").fadeOut();
                $("#curr_top_alerts h3").fadeOut();
            }
        if($( prod_type ).parent().attr('id') == "bottom_dropable")
            {
                $("#curr_bottom_alerts ul").fadeOut();
                $("#curr_bottom_alerts h3").fadeOut();
            }
        if($( prod_type ).parent().attr('id') == "full_dropable")
            {
                $("#curr_dress_alerts ul").fadeOut();
                $("#curr_dress_alerts h3").fadeOut();
            }
            
    };
    
    
    function remove_prod_details (prod_type){       
        if($(prod_type).parent().attr("id") == "top_dropable" || $(prod_type).parent().parent().attr("id") == "prod_type_top_hlder"){
            $("#prod_type_top_hlder").fadeOut(300);
            $("#top_dropable").fadeOut(300);
            remove_prod_state(parseInt($("#remove_top_prod_id").attr('value')));
        }
        if($(prod_type).parent().attr("id") == "bottom_dropable" || $(prod_type).parent().parent().attr("id") == "prod_type_bottom_hlder"){
            $("#prod_type_bottom_hlder").fadeOut(300);
            $("#bottom_dropable").fadeOut(300);
            remove_prod_state(parseInt($("#remove_bottom_prod_id").attr('value')));
        }
        if($(prod_type).parent().attr("id") == "full_dropable" || $(prod_type).parent().parent().attr("id") == "prod_type_dress_hlder"){
            $("#prod_type_dress_hlder").fadeOut(300);
            $("#full_dropable").fadeOut(300);
            remove_prod_state(parseInt($("#remove_dress_prod_id").attr('value')));
        }
    }
    
    
    function remove_product_applied (prod_type){       
        if($( prod_type ).css("left") != "0px" || $( prod_type ).css("top") != "0px" ){
            $( prod_type).fadeOut(300, function (){$(prod_type).parent().addClass("hide").css("display","none");$(prod_type).remove();});
            remove_prod_details(prod_type);            
            remove_fitting_alerts(prod_type);
            
        }
    }
    
    
    

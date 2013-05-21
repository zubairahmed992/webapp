/* Elements Creator */
document.createElement("article");
document.createElement("footer");
document.createElement("header");
document.createElement("hgroup");
document.createElement("nav");
document.createElement("aside");
document.createElement("address");
document.createElement("section");



function show_tip(ele){
	
	var chk_offset = $(ele).position();
	//alert(chk_offset);
	
	
	
	if($(ele).find("div.tip").css("display") == "block")
	{
		//$(ele).find("div.tip").fadeIn(500)
	}
	else {
			
			$(ele).find("div.tip").removeClass("tip").addClass("show_my_tip");
			$(".tip").hide(300, function (){
				$("div.show_my_tip").fadeIn(300, function (){$(".action_cnt").find("div.show_my_tip").stop().animate({"left": "-250px"}).removeClass("show_my_tip").addClass("tip");});
			});			
		 }
		
}
/* When DOM Ready */
$(document).ready(function(){
	
//	$(".slider_wrap").click(
//	function (){
//		alert("sdfsd");
//		show_tip(this);
//	}
//	);	
	
        
///// Step Two New Working//////

$(".switch_button").click(function(){
    $(this).parent().parent().find(".show_hide").toggleClass("show_it");
});
        


///////Step 2 - Brand and Size///////
 var gender=$('#gender').text();
    $("#measurement_top_brand").change(function(){
 
        var checked_option = $(this).attr("value");
   
        $('#measurement_top_size').html('');
        $.ajax({
            type:'json',
            url: "ajax/size_chart_title/" + checked_option + "/" + gender + "/" + "Top",
            success: function (data) {
   
                var top_brand = JSON.parse(data);
                $('<option>').val(top_brand[0]['id']).text(top_brand[0]['title']).appendTo('#measurement_top_size');    
    
            }
        });
    });

// Bottom 

$("#measurement_bottom_brand").change(function(){
 
        var checked_option = $(this).attr("value");
   
        $('#measurement_bottom_size').html('');
        $.ajax({
            type:'json',
            url: "ajax/size_chart_title/" + checked_option + "/" + gender + "/" + "Bottom",
            success: function (data) {
   
                var bottom_brand = JSON.parse(data);
                $('<option>').val(bottom_brand[0]['id']).text(bottom_brand[0]['title']).appendTo('#measurement_bottom_size');    
    
            }
        });
    });

// Dress

$("#measurement_dress_brand").change(function(){
 
        var checked_option = $(this).attr("value");
   
        $('#measurement_dress_size').html('');
        $.ajax({
            type:'json',
            url: "ajax/size_chart_title/" + checked_option + "/" + gender + "/" + "Dress",
            success: function (data) {
   
                var bottom_brand = JSON.parse(data);
                $('<option>').val(bottom_brand[0]['id']).text(bottom_brand[0]['title']).appendTo('#measurement_dress_size');    
    
            }
        });
    });


///--Set left column bg--///
        $(".left_column").css("minHeight", $(".holder").height());
        
        
        //---Sliders active---//
	$(".slider_wrap").mousedown(function (){
		$(".slider_wrap").css('background','#fff');
		$(this).css('background','#fff');
		show_tip(this);
	});

	
	
	//$("#top_layer").draggable();
	$("#uploaded_photo").draggable({
		start: function() {
             $("#dummy_mark").addClass("put_me_top");
            },
            drag: function() {

            },
            stop: function() {
		$("#dummy_mark").removeClass("put_me_top");
            }
		
		});
	$("#uploaded_photo").mousedown(function (){$("#dummy_mark").addClass("put_me_top");});
        //$("#uploaded_photo").mouseup(function (){$("#dummy_mark").removeClass("put_me_top");});
        $(".play_area").click(function (){$("#dummy_mark").removeClass("put_me_top");});
        //$(".play_area").dblclick(function (){$("#dummy_mark").removeClass("put_me_top");});
        //$("#top_layer").click(function (){$("#dummy_mark").removeClass("put_me_top");});
        
	$(".play_area").hover(function (){
		
		$("#top_layer").addClass("hide");
		$("#uploaded_photo").removeClass("full_opacity");
		$("#uploaded_photo").addClass("low_opacity");
		},
		function (){
			$("#top_layer").removeClass("hide");
			$("#uploaded_photo").removeClass("low_opacity");
			$("#uploaded_photo").addClass("full_opacity");
		}
		
		
		);
});




(function($) {
$(function() {

	$('ul.tabs').each(function() {
		$(this).find('li').each(function(i) {
			$(this).click(function(){
				$(this).addClass('current').siblings().removeClass('current')
					.parents('div.sectionbox').find('div.box').eq($(this).index()).fadeIn(150).siblings('div.box').hide();
			});
		});
	});

})
})(jQuery)




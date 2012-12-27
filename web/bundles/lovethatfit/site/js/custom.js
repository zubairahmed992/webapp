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
	
	$(".slider_wrap").mousedown(function (){
		$(".slider_wrap").css('background','#4d4d4d');
		$(this).css('background','url(images/bg_focus_grad.gif)');
		show_tip(this);
	});

	//Photo Area
		$("#inp_file").change(function (){
			
		var photo_file_name = $("#inp_file").val();
//			alert(photo_file_name);
		$("#inp_txt_file_name").val(photo_file_name);
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
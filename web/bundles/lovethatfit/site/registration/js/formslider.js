$(document).ready(function(){
  $("form").submit(function(e) {
    e.preventDefault();
  });
  
  var csplash = $("#content-splash");
  var clogin = $("#content-login");
  var cregister = $("#content-register");
  
  /* display the register page */
  $("#showsplash").on("click", function(e){
    e.preventDefault();
    var newheight = csplash.height();
    $(clogin).css("display", "block");
    
    $(csplash).stop().animate({
	  "opacity": 0.25,
      "left": "-880px",
	  
    }, 800, function(){ /* callback */ });
    
    $(clogin).stop().animate({
      "left": "207px"
    }, 800, function(){ $(csplash).css("display", "none"); });
    
    $(".container").stop().animate({
      "height": newheight+"px"
    }, 550, function(){ /* callback */ });
  });
  
  
  /* display the register page */
  $("#showregister").on("click", function(e){
    e.preventDefault();
    var newheight = cregister.height();
    $(cregister).css("display", "block");
    
    $(clogin).stop().animate({
      "left": "-880px"
    }, 800, function(){ /* callback */ });
    
    $(cregister).stop().animate({
      "left": "207px"
    }, 800, function(){ $(clogin).css("display", "none"); });
	
	$(csplash).stop().animate({
	  "opacity": 0.25,
      "left": "-880px",	  
    }, 800, function(){ /* callback */ });
    
    $(".container").stop().animate({
      "height": newheight+"px"
    }, 550, function(){ /* callback */ });
  });
  
  /* display the login page */
  $("#showlogin").on("click", function(e){
    e.preventDefault();
    var newheight = clogin.height();
    $(clogin).css("display", "block");
    
    $(clogin).stop().animate({
      "left": "0px"
    }, 800, function() { /* callback */ });
    $(cregister).stop().animate({
      "left": "880px"
    }, 800, function() { $(cregister).css("display", "none"); });
	
	
	 $(csplash).stop().animate({
	  "opacity": 0.25,
      "left": "-880px",	  
    }, 800, function(){ /* callback */ });
    
    $(".container").stop().animate({
      "height": newheight+"px"
    }, 550, function(){ /* callback */ });
  });
});
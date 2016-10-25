$(document).ready(function(){
    $("form").submit(function(e) {
        // e.preventDefault();
    });



    var csplash = $("#content-splash");
    var cregister = $("#content-register");
    var cregister_2 = $("#content-register-step-2");
    var clogin = $("#content-login");
    var greetings = $("#greetBox");

    $("#register_btn").click(function(){
        $("#regform").fadeOut('slow');
        $("#greetBox").fadeIn().animate({
            "left": "-880px"
        }, 800, function(){ /* callback */ });
    });






    /* display the register page */
    $("#showsplash").on("click", function(e){

        e.preventDefault();
        $(cregister).css("display", "block");

        $(csplash).stop().animate({
            "opacity": 0.25,
            "left": "-880px"
        }, 800, function(){ /* callback */ });




        var winh = $(window).width();
        if(winh >400){
            var shor = "207px";
        }else{
            var shor = "-40px";
        }

        $(cregister).stop().animate({
            "left": shor
        }, 800, function(){ $(csplash).css("display", "none"); });


    });


    /* display the register page */
    $("#showregister").on("click", function(e){
        e.preventDefault();
        $(cregister_2).css("display", "block");

        $(cregister).stop().animate({
            "left": "-880px"
        }, 800, function(){ /* callback */ });



        var winh = $(window).width();
        if(winh >400){
            var shor = "207px";
        }else{
            var shor = "-40px";
        }
        $(cregister_2).stop().animate({
            "left": shor
        }, 800, function(){ $(cregister).css("display", "none"); });

        $(csplash).stop().animate({
            "opacity": 0.25,
            "left": "-880px",
        }, 800, function(){ /* callback */ });

    });



    /* display the login page */
    $("#showlogin").on("click", function(e){
        e.preventDefault();
        $(clogin).css("display", "block");
        $('.loginbtn').hide();
        $('#greetBox').hide();

        var winh = $(window).width();
        if(winh >400){
            var shor = "207px";
        }else{
            var shor = "-40px";
        }

        $(clogin).stop().animate({
            "left": shor
        }, 800, function() { /* callback */ });

        $(csplash).stop().animate({
            "opacity": 0.25,
            "left": "-880px",
        }, 800, function(){ $(csplash).css("display", "none"); });

        $(cregister).stop().animate({
            "left": "880px"
        }, 800, function() { $(cregister).css("display", "none"); });

        $(cregister_2).stop().animate({
            "left": "880px"
        }, 800, function() { $(cregister_2).css("display", "none"); });


    });
});
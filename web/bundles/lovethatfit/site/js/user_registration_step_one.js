$(document).ready(function() {
    
  
var hideTwitterAttempts = 0;
function hideTwitterBoxElements() {
    setTimeout( function() {
        if ( $('[id*=twitter]').length ) {
        $('[id*=twitter]').each( function(){
            var ibody = $(this).contents().find( 'body' );

            if ( ibody.find( '.timeline .stream .h-feed li.tweet' ).length ) {
            ibody.find( '.customisable-border' ).css( 'border', 0 );
            ibody.find( '.timeline' ).css( 'background', 'none' ); //theme: shell: background:
            ibody.find( 'ol.h-feed' ).css( 'background-color', 'none' ); //theme: tweets: background:
            ibody.find( 'ol.h-feed' ).css( 'border-radius', 'none' );
            ibody.find( 'li.tweet' ).css( 'border-bottom', '1px dotted #FFFFFF' ); //theme: tweets: color:
            ibody.find( 'li.tweet' ).css( 'color', '#444' ); //theme: tweets: color:
            ibody.find( '.customisable:link' ).css( 'color', '#000' ); //theme: tweets: links:
            ibody.find( '.footer' ).css( 'visibility', 'hidden' ); //hide reply, retweet, favorite images
            ibody.find( '.footer' ).css( 'min-height', 0 ); //hide reply, retweet, favorite images
            ibody.find( '.footer' ).css( 'height', 0 ); //hide reply, retweet, favorite images
            ibody.find( '.avatar' ).css( 'height', 0 ); //hide avatar
            ibody.find( '.timeline-header' ).css( 'padding', 0 ); //hide avatar
            ibody.find( '.twitter-follow-button' ).css( 'visibility', 'hidden' ); //hide avatar
            ibody.find( 'time' ).css( 'visibility', 'hidden' ); //hide avatar
            ibody.find( '.tweet' ).css( 'padding-bottom', '5px' ); //hide avatar
            ibody.find( '.stream' ).css( 'height', '85px' ); //hide avatar
            ibody.find( '.tweet-box-button' ).css( 'background', '#efefef' ); //hide avatar
            
            ibody.find( '.timeline-footer' ).css( 'background', 'none' ); //hide avatar
           
            ibody.find( '.avatar' ).css( 'width', 0 ); //hide avatar
            ibody.find( '.e-entry-title' ).css( 'font-size', '12px' ); //hide avatar
            ibody.find( '.p-nickname' ).css( 'font-size', 0 ); //hide @name of tweet
            ibody.find( '.p-nickname' ).css( 'visibility', 'hidden' ); //hide @name of tweet
            ibody.find( '.e-entry-content' ).css( 'margin', '-25px 0px 0px 0px' ); //move tweet up (over @name of tweet)
            ibody.find( '.dt-updated' ).css( 'color', '#000' ); //theme: tweets: links:
            ibody.find( '.full-name' ).css( 'margin', '0px 0px 0px -35px' ); //move name of tweet to left (over avatar)
            ibody.find( 'h1.summary' ).replaceWith( '<h1 class="summary"><a class="customisable-highlight" title="Tweets from fundSchedule" href="https://twitter.com/fundschedule" style="color: #FFFFFF; display:none;">fundSchedule</a></h1>' ); //replace Tweets text at top
            ibody.find( '.p-name' ).css( 'color', '#000' ); //theme: tweets: links:
            ibody.find( '.p-author' ).css( 'margin-bottom', '20px' ); //theme: tweets: links:
            
             
            }
            else {
                $(this).hide();
            }
        });
        }
        hideTwitterAttempts++;
        if ( hideTwitterAttempts < 3 ) {
            hideTwitterBoxElements();
        }
    }, 1500);
}

// somewhere in your code after html page load
hideTwitterBoxElements();




 var typingTimer;    //timer identifier
     var doneTypingInterval = 1000;  //time in ms, 5 second for example

     var checktime = 0;

     jQuery('form#registration').find(':input').each(function(){
        jQuery(this).keyup(function(){
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        });

        jQuery(this).keydown(function(){
            clearTimeout(typingTimer);
        });

    });



    function doneTyping () {
        checktime = typingTimer;
        return checktime;
    } 
    jQuery('form#registration').submit(function() {
        checktime = doneTyping ();
      
       $('#user_timeSpent').val(checktime);
        
    });




function formRegistrationValidates(){
 var password=document.getElementById('user_password_password').value;
 var zipcode=document.getElementById('user_zipcode').value;
 var password_filter=/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;
 if(!password_filter.test(password)) {
    document.getElementById('error_content2').style.display="block";
    return false;
    }
    if(/\D/.test(zipcode)){
    document.getElementById('error_content3').style.display="block";
    return false;
    }   
    if(zipcode.length<5){
    document.getElementById('error_content3').style.display="block";
    return false;
    }    
}




   function formValidates()
{
   
    var username=document.getElementById('email').value;
    var password=document.getElementById('password').value;
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    
    if(!filter.test(username))
    {
         document.getElementById('error_content1').style.display="block";
           username.focus;
           return false;
    }
    
    if((username=='' || username=='email'))
        {
           document.getElementById('error_content1').style.display="block";
           username.focus;
           return false;
        }else if(password=='' || password=='password')
            {
              document.getElementById('error_content1').style.display="block";
              return false;                
            }else
                {
                    return true;
                }
}


 function active_login_blk(){
        $(".main_log_box").removeClass('reg_active');
        $(".main_log_box").addClass('log_active');
    }
    function active_reg_blk(){
        $(".main_log_box").removeClass('log_active');
        $(".main_log_box").addClass('reg_active');
    }
    
    
    
    $('#lg_btn').click(function(){
            active_login_blk();        
       })
       
       $('#reg_btn').click(function(){
           active_reg_blk();
       })
              
        $('.login_section a').click(function(){
            active_login_blk();
       })
       
        $('#crt_reg a').click(function(){
            active_reg_blk();
       })
    
    
    
  
	$('.tweets').vTicker({ 
		speed: 500,
		pause: 6000,
		animation: 'fade',
		mousePause: true,
		showItems: 1,
		direction: 'down',
		height:70
	});
 
    
    

   //listen for the form beeing submitted
   $("#ppregistrationStepOneForm").submit(function(){
     alert("weweweee");
      return false;
   });


 $("#wwregistrationStepOneForm").submit(function(){
      //get the url for the form
      var url=$("#registrationStepOneForm").attr("action");
   
      //start send the post request
       $.post(url,{
           formName:$("#name_id").val(),
           other:"attributes"
       },function(data){
           //the response is in the data variable
   
            if(data.responseCode==200 ){           
                $('#output').html(data.greeting);
                $('#output').css("color","red");
            }
           else if(data.responseCode==400){//bad request
               $('#output').html(data.greeting);
               $('#output').css("color","red");
           }
           else{
              //if we got to this point we know that the controller
              //did not return a json_encoded array. We can assume that           
              //an unexpected PHP error occured
              alert("An unexpeded error occured.");

              //if you want to print the error:
              $('#output').html(data);
           }
       });//It is silly. But you should not write 'json' or any thing as the fourth parameter. It should be undefined. I'll explain it futher down

      //we dont what the browser to submit the form
      return false;
   });
});
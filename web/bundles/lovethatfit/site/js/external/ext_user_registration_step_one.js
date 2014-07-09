$(document).ready(function() {

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
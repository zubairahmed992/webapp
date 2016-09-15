// NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
// IT'S ALL JUST JUNK FOR OUR DOCS!
// ++++++++++++++++++++++++++++++++++++++++++


//---------------------------------------------------------------------------
    function splitStr(str){
  var myStr=str.split("_");   
  var flag="1";
   if((myStr[0]) && (typeof myStr[0] != 'undefined')){
       flag+="1";
   } if((myStr[1]) && (typeof myStr[1] != 'undefined')){
       flag+="1";
   }
    if((myStr[2]) && (typeof myStr[2] != 'undefined')){
       flag+="1";
   }
  return flag; 
 }
 // Multiple file uploading method for  producs Item--------------------------#
$(document).ready(function(){
$("#file").change(function(){
var src=$("#file").val();
var product_id=$('#product_id').val();
if(src!="")
{
formdata= new FormData();
var numfiles=this.files.length;
var i, file, progress, size;
for(i=0;i<numfiles;i++)
{
file = this.files[i];

if(splitStr(file.name)!="1111"){
    alert("Invalid Image Format");
    return false;
}
size = this.files[i].size;
name = this.files[i].name;
if (!!file.type.match(/image.*/))
{
if((Math.round(size))<=(1024*1024))
{
var reader = new FileReader();
reader.readAsDataURL(file);
$("#preview").show();
$('#preview').html("");
reader.onloadend = function(e){
var image = $('<img>').attr({'src':e.target.result,'width':'100px', 'height':'100px'});
$(image).appendTo('#preview');
};
formdata.append("file[]", file);
if(i==(numfiles-1))
{
    formdata.append("product_id", product_id);
$("#info").html("wait a moment to complete upload");
$.ajax({
	url: $('#multiple_uplaod_url').text(),
	type: "POST",
	data: formdata,
	processData: false,
	contentType: false,
	success: function(res){
	
	$("#info").html(res);
    
        
	}
	});
}
}
else
{
$("#info").html(name+"Size limit exceeded");
$("#preview").hide();
return;
}
}
else
{
$("#info").html(name+"Not image file");
$("#preview").hide();
return;
}
}
}
else
{
$("#info").html("Select an image file");
$("#preview").hide();
return;
}
return false;
});
});

function formatSecondsAsTime(secs, format) {
  var hr  = Math.floor(secs / 3600);
  var min = Math.floor((secs - (hr * 3600))/60);
  var sec = Math.floor(secs - (hr * 3600) -  (min * 60));

  if (hr < 10) { hr    = "0" + hr; }
  if (min < 10) { min = "0" + min; }
  if (sec < 10) { sec  = "0" + sec; }
  if (hr) { hr   = "00"; }

  if (format != null) {
    var formatted_time = format.replace('hh', hr);
    formatted_time = formatted_time.replace('h', hr*1+""); // check for single hour formatting
    formatted_time = formatted_time.replace('mm', min);
    formatted_time = formatted_time.replace('m', min*1+""); // check for single minute formatting
    formatted_time = formatted_time.replace('ss', sec);
    formatted_time = formatted_time.replace('s', sec*1+""); // check for single second formatting
    return formatted_time;
  } else {
    return hr + ':' + min + ':' + sec;
  }
}
//--------------------End of Multiple file uplaoding method---------------------#

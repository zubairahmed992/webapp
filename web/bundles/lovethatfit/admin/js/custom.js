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
	if(res!="0")
	$("#info").html("Successfully Uploaded");
	else
	$("#info").html("Error in upload. Retry");
    
        new_win = window.open("data:text/json," + res,
                       "_blank");
        new_win.focus();
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
//--------------------End of Multiple file uplaoding method---------------------#

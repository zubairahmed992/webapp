/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var url = url;
//alert(url);


//settings.applyMatrix = true;

	var values = {
	paths: 1,
	minPoints: 5,
	maxPoints: 15,
	minRadius: 30,
	maxRadius: 90
};

var hitOptions = {
	segments: true,
	stroke: false,
	fill: false,
	tolerance: 10
};

$(document).ready(function() {
    createPaths();
});
function createPaths() {
	var radiusDelta = values.maxRadius - values.minRadius;
	var pointsDelta = values.maxPoints - values.minPoints;
	for (var i = 0; i < values.paths; i++) {
		var radius = values.minRadius + Math.random() * radiusDelta;
		var points = values.minPoints + Math.floor(Math.random() * pointsDelta);
		var path = createBlob(view.size * Point.random(), radius, points);
		var lightness = (Math.random() - 0.5) * 0.4 + 0.4;
		var hue = Math.random() * 360;
		//path.fillColor = { hue: hue, saturation: 1, lightness: lightness };
		//path.fillColor = "red";
		path.strokeColor = '#666';
		
	};
        
        
        
}

function createBlob(center, maxRadius, points) {
	// Old path before setting foot and head marks//
        //var pathData = 'M41.571,229.813c-0.503-0.016-1.896,4.743-1.986,15.255c-0.182,21.305-0.934,55.532-5.251,76.514c-0.2,3.241,2.574,16.961,1.5,31.256c-2.333,31.059-4.038,37.798-5.251,66.012c-0.145,3.356,1.435,5.929,0.75,8.501c-1.275,4.791-3.228,7.806-4,13.504c-0.81,5.969-1.123,9.932-1.5,12.502c-6.021,8.176-8.606,6.501-12.752,6.501c-5.017,0-6.951-2.563-6.001-6.501c2.673-6.654,3.436-8.324,5.251-12.502c2.378-5.475,2.542-7.51,4-13.754c1.05-6.317,1.063-26.026,0.5-32.255c-10.131-54.678-5.501-67.667-5.501-71.263c2.639-17.797,0.775,1.458,1-15.253c-0.5-7.167-0.75-10.836-1.25-18.003c-4.74-19.523-9.961-58.019-10.502-60.761c-1.48-7.424,0.234-35.2,1.25-44.758c1.192-11.212,3.543-13.02,5.251-19.004c0.685-2.399,3.1-7.774,3-17.003c-0.043-4.041-1.088-11.012-2-15.253c-1.208-5.613-2.083-15.503-2-20.254c0.083-2.153-0.5-7.348-2.25-8.751c0-8.846,0-14.159-2-29.255c0.547-0.097,22.041-5.237,25.255-10.002c1.122-1.664,1.757-7.281,2.25-10.002c-0.027,0.15,0.055-2.198-1-5.751c-0.868-2.921-1.562-3.614-2.5-10.002c-0.68,0.002-1.149-0.01-1.5-0.25c-1.752-1.89-3.446-8.406-3.501-12.002c0.51-0.256,1.199-0.476,1.5-1c-1.186-1.288-1.028-2.899-1.5-5.001C18.748,12,23.911,5.844,29.083,2.773c8.895-5.281,25.883-2.694,31.006,5.001c1.776,2.667,3.64,10.436,2.25,15.253c-0.354,1.227-1.148,2.428-1.5,3.75c1.613,0.103,1.599,0.784,1.5,2.5c-0.312,2.222-1.698,6.535-3.5,10.252c-0.976-0.007-1.625,0.376-2,1c-1.026,5.523-2.13,8.696-2,9.001c-0.81,1.601-1.297,3.233-0.75,5.751c0,1.25,0,2.5,0,3.751c0.431,1.865,0.696,4.83,1.75,6.251c3.5,4.722,22.504,9.156,25.504,10.002c-1.838,2.544-1.496,28.794-2,29.255c-2.346,2.04-2.239,7.379-2.25,8.751c0,4.205-1.277,17.371-2,20.254c-1.278,5.097-1.969,12.621-2,15.253c-0.067,5.705,0.074,11.168,2.75,17.003c1.595,3.477,4.856,12.074,5.501,19.254c2.712,30.204,1,47.138,1.25,44.508c-0.627,6.602-8.085,48.397-10.502,60.761c-0.167,3.667-0.771,12.586-1,17.753c-0.1,7.301,0.133,9.453,0.75,15.503c3.129,30.686-2.504,47.345-5.501,71.263c-1.369,10.927,0,23.687-0.25,29.005c4.876,22.321,10.909,27.912,10.251,31.756c-1.035,3.527-3.232,4.251-7.501,4.251c-6.486,0-6.588-1.329-11.502-6.501c-0.67-4.559-1.25-13.09-1.25-12.502c-1.168-5.057-3.156-8.884-4.251-13.754c-0.6-2.668,1.152-6.176,1-8.251c-2.039-27.809-5.001-55.678-5.501-66.012c-1.281-13.919,3.152-25.277,1.75-31.256c-4.905-20.928-5.285-55.214-5.751-76.514C43.105,234.559,42.11,229.821,41.571,229.813z';
        
        //var pathData = 'M151.614,270.262c-6.62,4.646-8.099,4.489-4.747-10.494c-0.07-4.79-5.163-12.409-1.999-24.485c0.053-1.571-5.985-16.437-12.714-50.684c-2.128-10.832-6.178-62.905-8.273-61.248c-2.726,2.157-2.236,7.373-2.248,8.745c-0.001,4.201-1.526,17.356-2.249,20.237c-1.276,5.093-1.967,12.611-1.998,15.241c-0.067,5.701,0.677,11.159,2.998,16.99c1.413,3.551,4.748,12.075,5.496,19.238c2.862,27.388,1.294,43.984,1.249,44.723c-0.766,7.277-8.104,48.245-10.493,60.464c-0.167,3.664-0.771,12.576-0.999,17.739c-0.101,7.295-0.117,9.695,0.499,15.74c3.127,30.661-2.252,47.058-5.246,70.957c-0.837,6.674-0.327,16.709-0.5,23.985c-0.118,4.974,1.984,8.982,2.498,11.493c13.695,19.154,3.199,18.183-2.748,18.988c-15.116,2.045-9.75-7.721-14.241-18.988c-1.546-3.879,1.579-9.416,1.499-11.493c-1.072-27.787-4.997-55.634-5.497-65.96c-0.91-12.801,2.903-25.788,1.749-30.981c-4.307-19.38-3.932-42.688-5.496-83.449c-0.066-1.712,0.872-8.745-1.999-8.745c-2.71-0.063-1.956,7.194-1.999,8.745c-1.117,40.668-1.433,62.483-5.746,83.449c-0.78,3.238,2.805,16.614,1.999,30.981c-1.673,29.808-5.862,49.721-5.497,65.96c0.071,3.174,2.115,5.71,1.749,11.493c-0.763,12.052,2.375,21.236-14.741,18.738c-12.492-3.439-7.897-9.562-3.117-18.841c1.692-3.285,3.032-6.67,2.867-11.391c0.334-9.011,0.106-20.041-0.121-23.914c-9.755-54.432-5.811-61.274-5.375-71.278c0.898-6.056,0.668-9.235,0.75-15.241c-0.5-7.161-0.75-10.827-1.249-17.988c-4.736-19.508-9.953-57.974-10.494-60.714c-1.479-7.418,0.483-35.172,1.499-44.722c1.191-11.204,3.291-13.009,4.997-18.989c0.684-2.397,2.549-3.177,3.248-16.99c0.204-4.032-1.087-11.003-1.999-15.241c-1.207-5.608-2.332-15.49-2.249-20.237c0.083-2.152,0.233-6.398-2.499-8.745c-1.91-0.397-6.483,53.751-8.042,61.36c-8.189,39.967-12.969,50.416-12.508,50.628c2.855,11.759-1.935,19.697-2.005,24.489c3.362,14.985,1.878,15.142-4.762,10.495c-17.464-12.221-2.39-22.442-2.004-35.484c0.366-12.408,0.802-42.319,4.761-67.221c0.833-5.236,5.724-38.381,6.245-44.461c2.396-27.968,10.081-25.894,16.567-29.039c0.546-0.097,22.023-5.233,25.235-9.994c2.171-2.912,2.273-8.37,2.499-13.242c-0.457-1.17-2.579-5.746-3.498-12.493c-2.62,1.118-3.647-5.756-3.998-5.996c-2.605-8.126-0.082-6.169,0.25-6.746c-3.309-10.197-3.853-26.484,19.238-26.484c27.639,0,20.271,24.2,19.238,26.484c2.799-0.126,0.349,5.03,0.25,6.746c-2.335,8.929-3.287,4.813-3.998,5.996c-0.942,5.645-1.337,8.107-3.497,12.493c0.102,2.303-0.061,10.189,2.248,13.242c3.509,4.639,22.114,9.061,25.39,9.968c7.817,2.822,13.714,2.715,16.835,29.258c1.473,12.527,5.361,39.249,6.246,44.223c4.414,24.818,4.382,54.803,4.747,67.209C153.997,247.822,169.026,258.043,151.614,270.262z';
        var pathData = $("#img_path_paper").attr("value");
        if(chk_no_img_path == true){
            pathData = $("#user_path").html();
        }
        //var pathData = $("#user_path").attr("value");
        var left_arm_pathData = 'M346.574,265.37c0,0,0.352-30.387,2.001-29.255c2.917,2,14.959-0.022,16.753,29.255c0.772,12.6,5.366,39.28,6.252,44.258c4.416,24.838,4.385,54.847,4.75,67.263c0.385,13.05,15.426,23.278-2,35.507c-6.625,4.648-8.105,4.492-4.751-10.503c-0.07-4.794-5.167-12.419-2.001-24.504c0.461-0.212-4.066-9.134-12.729-50.66C352.596,315.927,349.075,263.182,346.574,265.37z';
        var right_arm_pathData = 'M262.56,326.731c-8.662,41.525-13.189,50.447-12.729,50.66c3.167,12.085-1.931,19.71-2.001,24.504c3.354,14.995,1.875,15.151-4.751,10.502c-17.426-12.229-2.385-22.457-2-35.506c0.366-12.417,0.334-42.425,4.751-67.263c0.885-4.979,5.479-31.658,6.251-44.258c1.794-29.278,13.836-27.255,16.753-29.255c1.65-1.131,2.001,29.255,2.001,29.255C268.333,263.182,264.813,315.927,262.56,326.731z';
        
        
mid_area_path = new Path(pathData);
left_arm_path = new Path(left_arm_pathData);
right_arm_path = new Path(right_arm_pathData);
trans_bg = new Path.Rectangle(new Point(-300, -300), new Size(1000, 1000));
trans_bg.style = {
	fillColor: '#666666',
	stroke: 2,
	strokeColor: '#ffcc00'
};

//console.log(parseInt($('#user_height_frm_3').attr('value'))+ 5);

var p_user_height = parseInt($('#user_height_frm_3').attr('value')) + 3.375;


p_user_height = p_user_height * 18;

var p_user_height_px = p_user_height;

p_user_height = p_user_height * 100 / 450;

p_user_height = p_user_height / 100;


console.log(p_user_height);

if(chk_no_img_path == true){
    //alert("in side");
    mid_area_path.scale(1,p_user_height*3);
    mid_area_path.position = new Point(181,p_user_height_px/2+18);
    left_arm_path.position = new Point(238,p_user_height_px/2-30);
    right_arm_path.position = new Point(125,p_user_height_px/2-30);
   }
    mid_area_path.position = new Point(181,p_user_height_px/2+18);
    left_arm_path.position = new Point(238,p_user_height_px/2-30);
    right_arm_path.position = new Point(125,p_user_height_px/2-30);
   

    
console.log(mid_area_path.height);
//Rectangle(x, y, width, height)

var path = new CompoundPath({
    children: [
		trans_bg,
                mid_area_path
    ],
    fillColor: '#666666',
    selected: true
	//strokeColor: '#ffcc00'
});


//var path = new Path(pathData);
//var start = new Point(600, 600);
//path.moveTo(start);
//path.strokeColor = '#ccc';
path.opacity = 0.6;
//path.scale(1, 0.9);
	return path;
}

var segment, path;
var movePath = false;
function onMouseDown(event) {


	segment = path = null;
	var hitResult = project.hitTest(event.point, hitOptions);
	if (!hitResult)
		return;

	if (event.modifiers.shift) {
		if (hitResult.type == 'segment') {
                
                
			//hitResult.segment.remove();
			
		};
		return;
	}

	if (hitResult) {
        
        
        
		path = hitResult.item;
		if (hitResult.type == 'segment') {
			segment = hitResult.segment;
		} else if (hitResult.type == 'stroke') {
			
		}
	}
	//movePath = hitResult.type == 'fill';
	//if (movePath)
		//project.activeLayer.addChild(hitResult.item);
}

function onMouseMove(event) {
            project.activeLayer.selected = false;
	if (event.item)
		event.item.selected = true;
	
}



function onMouseUp(event){

    if (segment4) {
    
    
        for(var i = 0; i < path.segments.length; i++) {
        $("#segments_details").prepend("<div class='sh ort_tip' style='left:" + parseInt(path.segments[i].point.x) +"px; top:" + parseInt(path.segments[i].point.y)+"px'>Segment" + i +": "+path.segments[i].point + "</div>");
        $("#play_area").prepend("<div class='short_tip' style='left:" + (parseInt(path.segments[i].point.x)+1) +"px; top:" + (parseInt(path.segments[i].point.y)+1)+"px'>" + i + "</div>");
        };
        
        
        
        //$("#play_area").prepend("<div class='short_tip'>Segment: "+ event + "</div>");
    }    
        
}


function onMouseDrag(event) {
	if (segment) {
        
        
        
        
        
        var export_path_full = path.exportSVG({asString: true});
        export_path_full.toString();
        var export_path_remove_start = export_path_full.substr(44);
        
        var export_path_final = export_path_remove_start.substr(0, export_path_remove_start.length - 29);
        
        //$("#user_path").html(export_path_final);
        
        $("#img_path_paper").attr("value", export_path_final);
       // alert(export_path_final);
        
       // var import_path_full = project.importSVG(export_path_full);
        
       // alert(import_path_full);
        
		segment.point += event.delta;
                
               
                var sholder_left = path.segments[3].point.y - 22;
                var sholder_right = path.segments[15].point.y - 22;
                
                //alert(sholder_right);
                
                if(sholder_left <= sholder_right){
                    $("#measurement_shoulder_height").attr("value", sholder_left);
                }else{
                    $("#measurement_shoulder_height").attr("value", sholder_right);
                }
                
                
                var bottom_left = path.segments[55].point.y - 66;
                var bottom_right = path.segments[29].point.y - 66;
                
                //alert(sholder_right);
                
                if(bottom_left <= bottom_right){
                    $("#measurement_hip_height").attr("value", bottom_left);
                }else{
                    $("#measurement_hip_height").attr("value", bottom_right);
                }
                
                
                
		//path.smooth();
		
		
		
		console.log("Me Hit!");
	} else if (path) {
		path.position += event.delta;
		
		
	}
	

	
}
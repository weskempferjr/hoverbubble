
jQuery(document).ready(function($) {

	$.ajax({
		url: 'http://localhost:8888/wordpress/wp-admin/admin-ajax.php',
		data:{
			'action':'tnotw_hoverbubble_ajax',
			'fn':'get_bubble_config',
			'bubble_id':1
 		},
		dataType: 'JSON',
		success:function(data){
		// save data for browser window events.
		saveBubbleConfig( data );
		displayBubbles(data);
          	},
		error: function(errorThrown){
			alert('error');
			console.log(errorThrown);
          	}


	});

	$(window).resize(function(){
		displayBubbles( retrieveBubbleConfig() );
	});
});


function displayBubbles(data)
{
	for ( i = 0 ; i <	data.length ; i++ )
	{
		displayBubble(data[i]);
	} 
}

function displayBubble(bubbleConfig){

	var bubbleMessage = bubbleConfig.bubbleMessage ;
	var bubbleFillColor = bubbleConfig.bubbleFillColor ;
	var bubbleFontColor = bubbleConfig.bubbleFontColor ;
	var bubbleFont = bubbleConfig.bubbleFont ;
	var bubbleTextAlign = bubbleConfig.bubbleTextAlign ;
	var bubbleTailLength = parseInt(bubbleConfig.bubbleTailLength);
	var bubblePadding = parseInt(bubbleConfig.bubblePadding );
	var bubbleCornerRadius = parseInt(bubbleConfig.bubbleCornerRadius );
	var bubbleOutlineColor = bubbleConfig.bubbleOutlineColor;
	var bubbleOutlineWidth = parseInt(bubbleConfig.bubbleOutlineWidth);
	var canvasTop = parseInt(bubbleConfig.canvasTop) ;
	var canvasLeft = parseInt(bubbleConfig.canvasLeft) ;
	var canvasHeight = parseInt(bubbleConfig.canvasHeight) ;
	var canvasWidth = parseInt(bubbleConfig.canvasWidth) ;
	var bubbleTailDirection = bubbleConfig.bubbleTailDirection ;
	var canvasBorderStyle = bubbleConfig.canvasBorderStyle ;
	var targetImageID = bubbleConfig.targetImageID ;
	var targetImageContainerID = bubbleConfig.targetImageContainerID ;
	var bubbleCanvasID = bubbleConfig.bubbleCanvasID ;

	var the_image = jQuery("img[src='" + targetImageID	+ "']");
	the_image.wrap('<div id="' + targetImageContainerID + '"/></div>');

	var img_div = jQuery("#the_image_div");
	var position = img_div.position();
	var bubble_top = position.top + canvasTop ;
	var bubble_left = position.left + canvasLeft ;
	var top = bubble_top + "px" ;
	var left = bubble_left + "px" ;

	
	// corner radius	
	var corner_r = bubbleCornerRadius ;

	// tail dimensions 
	var tail_length = bubbleTailLength ;
	var tail_angle = Math.PI / 4;
	var tail_offset_x = Math.round( tail_length *	Math.cos(tail_angle) );
	var tail_offset_y = Math.round( tail_length *	Math.sin(tail_angle) );
	// tail base width for side-based tail
	var tail_base_w = corner_r ;
	var tail_base_offset = corner_r /2 ;

	var canvas_w = canvasWidth;
	var canvas_h = canvasHeight; 
	var canvas_w_attr = canvas_w + "px" ; 
	var canvas_h_attr = canvas_h + "px" ;
	var canvas_border_style = canvasBorderStyle ;

	// height and width of bubble
	var bubble_pad = bubblePadding;
	var bubble_real_w = canvas_w - tail_length - bubble_pad ;
	var bubble_real_h = canvas_h - tail_length - bubble_pad ;

	var strokeColor = bubbleOutlineColor;
	var strokeWidth = bubbleOutlineWidth;
	var textFont = bubbleFont ;
	var textAlign = bubbleTextAlign
	var textColor = bubbleFontColor ;

	// remove previous instance if it exists.
	jQuery("#" +	bubbleCanvasID ).remove();

	img_div.append('<canvas id="' + bubbleCanvasID + '" width="' + canvas_w + '" height="' + canvas_h + '" > </canvas>');
	var canvasElement=document.getElementById( bubbleCanvasID );
	var ctx=canvasElement.getContext("2d");

	var scale_factor = backingScale(ctx) ;

	// center position of bubble rectangle relative canvas origin. 
	var center_x = ( canvas_w / 2 ) * scale_factor ;
	var center_y = ( canvas_h / 2 ) * scale_factor ;

	// center for text
	var text_x = center_x ; 
	var text_y = center_y ;

	// scaled dimensions
	var bubble_w = bubble_real_w * scale_factor;
	var bubble_h = bubble_real_h * scale_factor;


	// x and y values needed for start/end path positions
	var left_x = center_x - ( bubble_w / 2 );
	var right_x = center_x + ( bubble_w / 2 );
	var top_y = center_y - ( bubble_h / 2 );
	var bottom_y = center_y + ( bubble_h / 2 );

	// Determine start_point and tail style (corner or side) from bubbleTailDirection
	var tail_style = getBubbleTailStyle( bubbleTailDirection );
	var start_point = getBubbleDrawStartPoint( bubbleTailDirection );


	jQuery("#" + bubbleCanvasID ).css({"border": canvas_border_style,"position":"absolute","z-index":"2"});
	jQuery("#" + bubbleCanvasID ).css({"top":top,"left":left });


	ctx.save();



	if ( start_point == "nw" ) {
		var start_path_x = left_x + corner_r ;
		var start_path_y = top_y ;

		var first_arc_x1 = right_x ;
		var first_arc_y1 = top_y ;
		var first_arc_x2 = right_x ;
		var first_arc_y2 = top_y + corner_r ;

		var second_arc_x1 = right_x ;
		var second_arc_y1 = bottom_y ;
		var second_arc_x2 = right_x - corner_r ;
		var second_arc_y2 = bottom_y ;

		var third_arc_x1 = left_x ;
		var third_arc_y1 = bottom_y ;
		var third_arc_x2 = left_x ;
		var third_arc_y2 = bottom_y - corner_r ;

		var fourth_arc_x1 = left_x ;
		var fourth_arc_y1 = top_y ;
		var fourth_arc_x2 = left_x + corner_r ;
		var fourth_arc_y2 = top_y ;


		// for tail off of corner
		var tail_base_x = left_x ; 
		var tail_base_y = top_y + corner_r ;

		var tail_point_x = left_x - tail_offset_x ; 
		var tail_point_y = top_y - tail_offset_y ; 

	 // for tail off of side.
	 var tail_side_base_x1 = left_x ;
	 var tail_side_base_y1 = center_y + tail_base_offset ;
	 var tail_side_base_x2 = left_x ;
	 var tail_side_base_y2 = center_y - tail_base_offset ;

	 var tail_side_point_x = left_x - tail_length ;
	 var tail_side_point_y = center_y ;

		
	} 
	else if ( start_point == "ne" ) {
		var start_path_x = right_x ;
		var start_path_y = top_y + corner_r ;

		var first_arc_x1 = right_x ;
		var first_arc_y1 = bottom_y ;
		var first_arc_x2 = right_x - corner_r ;
		var first_arc_y2 = bottom_y ;

		var second_arc_x1 = left_x ;
		var second_arc_y1 = bottom_y ;
		var second_arc_x2 = left_x ;
		var second_arc_y2 = bottom_y - corner_r ;

		var third_arc_x1 = left_x ;
		var third_arc_y1 = top_y ;
		var third_arc_x2 = left_x + corner_r ;
		var third_arc_y2 = top_y ;

		var fourth_arc_x1 = right_x ;
		var fourth_arc_y1 = top_y ;
		var fourth_arc_x2 = right_x ;
		var fourth_arc_y2 = top_y + corner_r ;

	 // for tail off of corner.
		var tail_base_x = right_x - corner_r ; 
		var tail_base_y = top_y ;

		var tail_point_x = right_x + tail_offset_x ; 
		var tail_point_y = top_y - tail_offset_y ; 

	 // for tail off of side.
	 var tail_side_base_x1 = center_x - tail_base_offset ;
	 var tail_side_base_y1 = top_y ;
	 var tail_side_base_x2 = center_x + tail_base_offset ;
	 var tail_side_base_y2 = top_y ;

	 var tail_side_point_x = center_x ;
	 var tail_side_point_y = top_y - tail_length ;

	}
	else if ( start_point == "se" ) {

		var start_path_x = right_x - corner_r ;
		var start_path_y = bottom_y ;

		var first_arc_x1 = left_x ;
		var first_arc_y1 = bottom_y ;
		var first_arc_x2 = left_x ;
		var first_arc_y2 = bottom_y - corner_r ;

		var second_arc_x1 = left_x ;
		var second_arc_y1 = top_y ;
		var second_arc_x2 = left_x + corner_r ;
		var second_arc_y2 = top_y ;

		var third_arc_x1 = right_x ;
		var third_arc_y1 = top_y ;
		var third_arc_x2 = right_x ;
		var third_arc_y2 = top_y + corner_r ;

		var fourth_arc_x1 = right_x ;
		var fourth_arc_y1 = bottom_y ;
		var fourth_arc_x2 = right_x - corner_r ;
		var fourth_arc_y2 = bottom_y ;

		// for tail of of corner
		var tail_base_x = right_x ; 
		var tail_base_y = bottom_y - corner_r ;

		var tail_point_x = right_x + tail_offset_x ; 
		var tail_point_y = bottom_y + tail_offset_y ; 

	 // for tail off of side.
	 var tail_side_base_x1 = right_x ;
	 var tail_side_base_y1 = center_y - tail_base_offset ;
	 var tail_side_base_x2 = right_x ;
	 var tail_side_base_y2 = center_y + tail_base_offset ;

	 var tail_side_point_x = right_x + tail_length ;
	 var tail_side_point_y = center_y ;


	} 
	else if ( start_point == "sw" ) {

		var start_path_x = left_x ;
		var start_path_y = bottom_y - corner_r ;

		var first_arc_x1 = left_x ;
		var first_arc_y1 = top_y ;
		var first_arc_x2 = left_x + corner_r;
		var first_arc_y2 = top_y ;

		var second_arc_x1 = right_x ;
		var second_arc_y1 = top_y ;
		var second_arc_x2 = right_x ;
		var second_arc_y2 = top_y + corner_r;

		var third_arc_x1 = right_x ;
		var third_arc_y1 = bottom_y ;
		var third_arc_x2 = right_x - corner_r ;
		var third_arc_y2 = bottom_y ;

		var fourth_arc_x1 = left_x ;
		var fourth_arc_y1 = bottom_y ;
		var fourth_arc_x2 = left_x ;
		var fourth_arc_y2 = bottom_y - corner_r ;

		var tail_base_x = left_x + corner_r ; 
		var tail_base_y = bottom_y ;

		var tail_point_x = left_x - tail_offset_x ; 
		var tail_point_y = bottom_y + tail_offset_y ; 

	 // for tail off of side.
	 var tail_side_base_x1 = center_x + tail_base_offset ;
	 var tail_side_base_y1 = bottom_y ;
	 var tail_side_base_x2 = center_x - tail_base_offset ;
	 var tail_side_base_y2 = bottom_y ;

	 var tail_side_point_x = center_x ;
	 var tail_side_point_y = bottom_y + tail_length ;
	} 
		 
	ctx.strokeStyle = strokeColor ;
	ctx.lineWidth = strokeWidth ; 
	ctx.beginPath(); 
	ctx.moveTo( start_path_x, start_path_y);

	// Note that ctx.lineTo calls are not needed. 
	// See http://www.dbp-consulting.com/tutorials/canvas/CanvasArcTo.html. 

	//find start of	right side line and arc to it from end of top line
	ctx.arcTo( first_arc_x1, first_arc_y1, first_arc_x2, first_arc_y2, corner_r );
	
	// find end of right line, find start of bottom line and arc to it from end of right side line
	ctx.arcTo( second_arc_x1, second_arc_y1, second_arc_x2, second_arc_y2, corner_r );

	// find end of bottom line, find start of left line and arc to it from end of bottom side line
	ctx.arcTo( third_arc_x1, third_arc_y1, third_arc_x2, third_arc_y2, corner_r );

	if ( tail_style == "none" ) {
		// find end of left line, find start of	top line and arc to it from end of left side line
		ctx.arcTo( fourth_arc_x1, fourth_arc_y1, fourth_arc_x2, fourth_arc_y2, corner_r );
	} 
	else if ( tail_style == "corner" ) {
		ctx.lineTo( tail_base_x, tail_base_y );
		ctx.lineTo( tail_point_x, tail_point_y );
		ctx.closePath();
	}
	else if ( tail_style = "side" ) {
		ctx.lineTo( tail_side_base_x1, tail_side_base_y1 ); 
		ctx.lineTo( tail_side_point_x, tail_side_point_y );
		ctx.lineTo( tail_side_base_x2, tail_side_base_y2 ); 
		ctx.arcTo( fourth_arc_x1, fourth_arc_y1, fourth_arc_x2, fourth_arc_y2, corner_r );
	}

	// draw it
	ctx.stroke();

	ctx.fillStyle = bubbleFillColor ;
	ctx.fill(); 
	ctx.textAlign = textAlign ;
	ctx.fillStyle = textColor ;
	ctx.font = textFont ;
	ctx.fillText( bubbleMessage, text_x, text_y );
	ctx.restore();

};

// return tail style ("side" or "corner");
function getBubbleTailStyle( bubbleTailDirection ) {

	var tailStyle ;

	switch ( bubbleTailDirection ) {
		case "N":
		case "S":
		case "W":
		case "E":
			tailStyle = "side" ;
			break;

		case "NW":
		case "SW":
		case "NE":
		case "SE":
			tailStyle = "corner" ;
			break;

		case "NONE":
			tailStyle = "none" ;
			break ;

		default:
			tailStyle = "none" ;
			break ;
				 

	}
	return tailStyle ;
}


// return drawing start point based on bubble tail direction
function getBubbleDrawStartPoint( bubbleTailDirection ) {
	var startPoint ;

	switch ( bubbleTailDirection ) {

		case "W":
		case "NW":
			startPoint = "nw" ;
			break;

		case "N":
		case "NE":
			startPoint = "ne" ;
			break;

		case "E":
		case "SE":
			startPoint = "se" ;
			break;

		case "S":
		case "SW":
			startPoint = "sw" ;
			break;

		default:
			startPoint = "nw" ;
			break ;
				 

	}
	return startPoint ;
}


function backingScale(context) {
    if ('devicePixelRatio' in window) {
        if (window.devicePixelRatio > 1 && context.webkitBackingStorePixelRatio < 2) {
            return window.devicePixelRatio;
        }
    }
    return 1;
};

function saveBubbleConfig( data ) {
  localStorage.setItem( 'bubbleConfig', JSON.stringify( data ) );
} 

function retrieveBubbleConfig( data ) {
  return JSON.parse( localStorage.getItem( 'bubbleConfig' ) );
} 

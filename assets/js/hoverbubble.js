
jQuery(document).ready(function($) {


	var imageSourceArray = getImageList();
	var imageInfoData = JSON.stringify(imageSourceArray);
	
	$.ajax({
		url:  wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
		data:{
			'action':'tnotw_hoverbubble_ajax',
			'fn':'get_bubble_config',
			'bubble_id':1,
			'imageInfoData': imageSourceArray
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

function getImageList() {
	
	var imgSrcArray = new Array();
	
	jQuery("img").each(function() {
		var imgURLtmp = jQuery(this).attr('src');
		// Remove wordpress attachment size 
		// var imgURL = imgURLtmp.replace(/-[0-9]*x[0-9]*\./,'.');
		imgURL = imgURLtmp ;
		imgSrcArray.push( imgURL );
	    // imgSrcArray.push(jQuery(this).attr('src'));
	});
	
	return imgSrcArray ;
}

function restoreImageURL( imageURL ) {
	
	if ( jQuery("img[src='" + imageURL + "']").length > 0 ) {
		return imageURL;
	}
	
	var targetURL = "";
	jQuery("img").each(function() {
		var actualURL = jQuery(this).attr('src');
		// var matchURL = actualURL.replace(/-[0-9]*x[0-9]*\./,'.');
		matchURL = actualURL ;
		if ( imageURL == matchURL ) {
			targetURL = actualURL;
		}
		
	});
	
	if ( targetURL == "" ) {
		// TODO: console wrapper
		console.log("HB: restoreTargetURL: could not match target URL");
	}
	return targetURL;
	
}

function displayBubble(bubbleConfig){

	//var bubbleMessage = bubbleConfig.bubbleMessage ;
	var bubbleID = bubbleConfig.bubbleID ;
	var bubbleFillColor = bubbleConfig.bubbleFillColor ;
	var bubbleTailLength = parseInt(bubbleConfig.bubbleTailLength);
	var bubbleCornerRadius = parseInt(bubbleConfig.bubbleCornerRadius );
	var bubbleOutlineColor = bubbleConfig.bubbleOutlineColor;
	var bubbleOutlineWidth = parseInt(bubbleConfig.bubbleOutlineWidth);
	var bubbleTailX = parseInt(bubbleConfig.bubbleTailX) ;
	var bubbleTailY = parseInt(bubbleConfig.bubbleTailY) ;	var bubbleAspectRatio =  parseInt(bubbleConfig.bubbleAspectRatio) ;

	var contentAreaWidth = parseInt(bubbleConfig.contentAreaWidth);
	var contentAreaHeight = parseInt(bubbleConfig.contentAreaHeight);

	// Force tail direction to lower case. All code in this
	// script assumes lower case. 
	var bubbleTailDirection = bubbleConfig.bubbleTailDirection.toLowerCase(); ;
	var canvasBorderStyle = bubbleConfig.canvasBorderStyle ;
	var targetImageURL = bubbleConfig.targetImageURL  ;
	
	var targetImageContainerID = bubbleConfig.targetImageContainerID ;
	var bubbleCanvasID = bubbleConfig.bubbleCanvasID ;
	var contentDivID = bubbleConfig.contentDivID ;
	var contentEmbedID = bubbleConfig.contentEmbedID ;

	// wrap target image in div, call remove in case it previously exists. 
	var the_image = "";
	if ( jQuery("#" + targetImageContainerID ).length == 0 ) {
		var the_image = jQuery("img[src='" + targetImageURL	+ "']");
		the_image.wrap('<div id="' + targetImageContainerID + '"/></div>');
	}

	var img_div = jQuery("#" + targetImageContainerID); 
	var imageDivPosition = img_div.position();
	

	textAreaDimensions = new Object();
	textAreaDimensions.width = contentAreaWidth;
	textAreaDimensions.height = contentAreaHeight;

	
	
	// TODO: consider making textPadding a config parameter
	var textPadding = new Object();
	textPadding.x = 10;
	textPadding.y = 10;
	var bubbleAreaDimensions = calculateBubbleDimensions (	textAreaDimensions,
															textPadding);
	
	var bubbleTailCoordinates = new Object();
	bubbleTailCoordinates.x = bubbleTailX;
	bubbleTailCoordinates.y = bubbleTailY;
	var canvasAreaDimensions = calculateCanvasDimensions(	bubbleAreaDimensions,
															bubbleTailLength,
															bubbleTailDirection,
															bubbleTailCoordinates);
	// corner radius	
	var corner_r = bubbleCornerRadius ;
	

	

	var top = imageDivPosition.top + canvasAreaDimensions.top ;
	var left = imageDivPosition.left +   canvasAreaDimensions.left;
	

	// tail dimensions 
	var tail_length = bubbleTailLength ;
	var tail_angle = Math.PI / 4;
	var tail_offset_x = Math.round( tail_length *	Math.cos(tail_angle) );
	var tail_offset_y = Math.round( tail_length *	Math.sin(tail_angle) );
	// tail base width for side-based tail
	var tail_base_w = corner_r ;
	var tail_base_offset = corner_r /2 ;

	// var canvas_w = canvasWidth;
	// var canvas_h = canvasHeight; 
	var canvas_w = canvasAreaDimensions.width ;
	var canvas_h = canvasAreaDimensions.height ;
	var canvas_w_attr = canvas_w + "px" ; 
	var canvas_h_attr = canvas_h + "px" ;
	var canvas_border_style = canvasBorderStyle ;



	var strokeColor = bubbleOutlineColor;
	var strokeWidth = bubbleOutlineWidth;


	// remove previous instance if it exists.
	jQuery("#" + bubbleCanvasID ).remove();
	jQuery("#" + contentDivID ).remove();
	jQuery("#" + contentEmbedID ).remove();

	img_div.append('<canvas id="' + bubbleCanvasID + '" width="' + canvas_w + '" height="' + canvas_h + '" > </canvas>');
	var canvasElement=document.getElementById( bubbleCanvasID );
	var ctx=canvasElement.getContext("2d");

	var scale_factor = backingScale(ctx) ;

	// center position of bubble rectangle relative canvas origin. 
	var center_x = ( canvas_w / 2 ) * scale_factor ;
	var center_y = ( canvas_h / 2 ) * scale_factor ;
	
	

	// scaled dimensions
	var bubble_w = bubbleAreaDimensions.width * scale_factor;
	var bubble_h = bubbleAreaDimensions.height * scale_factor;

	// Start for for text
	//var text_x = center_x ;
	// var text_y = center_y ; used with center align
	// var text_padding_x = 5 ;
	// var text_padding_y = 15;	
	// var max_text_width = bubble_w - ( textPadding.x * 2 );
	// text_x = bubble_w - max_text_width / 2;
	// text_y = ( center_y - ( bubble_h / 2 ) ) + textPadding.y;
	
	// Calculate text origin. 
	var text_x =  Math.round(( canvasAreaDimensions.width  - textAreaDimensions.width ) / 2);
	var text_y =  Math.round(( canvasAreaDimensions.height  - textAreaDimensions.height ) / 2) + textPadding.y;
	
 
	// x and y values needed for start/end path positions
	var left_x = center_x - ( bubble_w / 2 );
	var right_x = center_x + ( bubble_w / 2 );
	var top_y = center_y - ( bubble_h / 2 );
	var bottom_y = center_y + ( bubble_h / 2 );

	// Determine start_point and tail style (corner or side) from bubbleTailDirection
	var tail_style = getBubbleTailStyle( bubbleTailDirection );
	var start_point = getBubbleDrawStartPoint( bubbleTailDirection );


	jQuery("#" + bubbleCanvasID ).css({"border": canvas_border_style,"position":"absolute","z-index":"10"});
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

		
		// canvas origin
		var tail_point_x = 0 + bubbleOutlineWidth;
		var tail_point_y = 0 + bubbleOutlineWidth;
		
		// for tail off of side.
		var tail_side_base_x1 = left_x ;
		var tail_side_base_y1 = center_y + tail_base_offset ;
		var tail_side_base_x2 = left_x ;
		var tail_side_base_y2 = center_y - tail_base_offset ;

		// 
		// var tail_side_point_x = left_x - tail_length ;
		// var tail_side_point_y = center_y ;
		// To the west side of the canvas.
		var tail_side_point_x = 0 + bubbleOutlineWidth ;
		var tail_side_point_y = Math.round( canvasAreaDimensions.height / 2 );
		
		
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
		var tail_base_x =  right_x - corner_r ; 
		var tail_base_y = top_y ;
		
		// To the northeast corner.
		var tail_point_x = canvasAreaDimensions.width - bubbleOutlineWidth; 
		var tail_point_y = 0 + bubbleOutlineWidth;

		// for tail off of side.
		var tail_side_base_x1 = center_x - tail_base_offset ;
		var tail_side_base_y1 = top_y ;
		var tail_side_base_x2 = center_x + tail_base_offset ;
		var tail_side_base_y2 = top_y ;

		// var tail_side_point_x = center_x ;
		// var tail_side_point_y = top_y - tail_length ;
		// To the North side of the canvas
		var tail_side_point_x = Math.round( canvasAreaDimensions.width / 2 );
		var tail_side_point_y = 0 + bubbleOutlineWidth;

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
		var tail_base_x = right_x  ; 
		var tail_base_y =  bottom_y - corner_r  ;

		//
		// var tail_point_x = right_x + tail_offset_x ; 
		// var tail_point_y = bottom_y + tail_offset_y ; 
		// To the southeast corner
		var tail_point_x = canvasAreaDimensions.width - bubbleOutlineWidth;
		var tail_point_y = canvasAreaDimensions.height - bubbleOutlineWidth;

		// for tail off of side.
		var tail_side_base_x1 = right_x ;
		var tail_side_base_y1 = center_y - tail_base_offset ;
		var tail_side_base_x2 = right_x ;
		var tail_side_base_y2 = center_y + tail_base_offset ;

		// var tail_side_point_x = right_x + tail_length ;
		// var tail_side_point_y = center_y ;
		// To the east side. 
		var tail_side_point_x = canvasAreaDimensions.width - bubbleOutlineWidth;
		var tail_side_point_y = Math.round( canvasAreaDimensions.height /2 );
		


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

		// var tail_point_x = left_x - tail_offset_x ; 
		// var tail_point_y = bottom_y + tail_offset_y ; 
		var tail_point_x  = 0 + bubbleOutlineWidth ;
		var tail_point_y = canvasAreaDimensions.height  - bubbleOutlineWidth ;

		// for tail off of side.
		var tail_side_base_x1 = center_x + tail_base_offset ;
		var tail_side_base_y1 = bottom_y ;
		var tail_side_base_x2 = center_x - tail_base_offset ;
		var tail_side_base_y2 = bottom_y ;

		// To the south
		var tail_side_point_x = Math.round(canvasAreaDimensions.width / 2 )  ;
		var tail_side_point_y = canvasAreaDimensions.height - bubbleOutlineWidth ;
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
	
	
	 var canvasPosition = jQuery("#" + bubbleCanvasID ).position();
	 var etop = canvasPosition.top + ( ( canvas_h - contentAreaHeight ) / 2 );
	 var eleft = canvasPosition.left + ( ( canvas_w - contentAreaWidth ) / 2 );

	 img_div.append('<div id="' + contentDivID + '"  style="z-index:20;position:absolute;top:' +  etop +  'px;left:' + eleft + 'px"></div>');
	 
	 //TODO: hardcoded URL
	 jQuery("#" + contentDivID ).append('<embed  type="text/html" width="' + contentAreaWidth + 'px" height="' + contentAreaHeight + 'px" src="' + wpsiteinfo.site_url + '/index.php?hb_bubble_id='+ bubbleID +'">');

	ctx.restore();

};

// return tail style ("side" or "corner");
function getBubbleTailStyle( bubbleTailDirection ) {

	var tailStyle ;

	switch ( bubbleTailDirection ) {
		case "n":
		case "s":
		case "w":
		case "e":
			tailStyle = "side" ;
			break;

		case "nw":
		case "sw":
		case "ne":
		case "se":
			tailStyle = "corner" ;
			break;

		case "none":
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

		case "w":
		case "nw":
			startPoint = "nw" ;
			break;

		case "n":
		case "ne":
			startPoint = "ne" ;
			break;

		case "e":
		case "se":
			startPoint = "se" ;
			break;

		case "s":
		case "sw":
			startPoint = "sw" ;
			break;

		default:
			startPoint = "sw" ;
			break ;
				 

	}
	return startPoint ;
}


function wrapText(context, text, x, y, maxWidth, bubbleFont) {
	var words = text.split(' ');
	var line = '';

	// determine line height from font
	var fontSizeStr = bubbleFont.split(' ') ;
	var fontHeightStrRaw = fontSizeStr[0] ;
	var fontHeightStr = fontHeightStrRaw.replace("px","");
	var fontHeight = parseInt(fontHeightStr);
	
	var lineHeight = fontHeight + 4 ;
	
	for(var n = 0; n < words.length; n++) {
		var testLine = line + words[n] + ' ';
		var metrics = context.measureText(testLine);
		var testWidth = metrics.width;
		if(testWidth > maxWidth) {
			context.fillText(line, x, y);
			line = words[n] + ' ';
			y += lineHeight;
		}
		else {
			line = testLine;
		}
	}
	context.fillText(line, x, y);
}


// calculate text area dimensions based on 
// a line of text and an w:h ratio.

function calculateTextDimensions(divObj, text, bubbleFont, aspectRatio, lineSpacing ) {
	
	var context = getTempContext(divObj);
	context.font = bubbleFont ;
	var metric = context.measureText(text);
	var lineLength = metric.width;
	
	// TODO collapse of clean this up. Turn in to function.
	var fontSizeStr = bubbleFont.split(' ') ;
	var fontHeightStrRaw = fontSizeStr[0] ;
	var fontHeightStr = fontHeightStrRaw.replace("px","");
	var fontHeight = parseInt(fontHeightStr);
	
	var lineHeight = fontHeight + lineSpacing ;
	var requiredArea = lineHeight * lineLength ;
	
	// calculate y 
	var textHeight = Math.sqrt( requiredArea * aspectRatio );
	var textWidth = textHeight * aspectRatio ;
	
	var textDimensions = new Object();
	textDimensions.height = Math.round( textHeight ) ;
	textDimensions.width = Math.round( textWidth );
	
	removeTempContext();
	
	return textDimensions ;
	
}

// calculate bubble dimensions - the area that does not include the tail
function calculateBubbleDimensions( textDimensions, textPadding ) {	
	var bubbleDimensions = new Object();
	bubbleDimensions.height = textDimensions.height + (textPadding.x * 2);
	bubbleDimensions.width = textDimensions.width + (textPadding.y * 2);
	return bubbleDimensions ;
	
}

// calculate canvas dimensions by adding tail length to bubble dimension
function calculateCanvasDimensions( bubbleDimensions, tailLength, tailDirection, tailCoordinates) {
	var canvasDimensions = new Object();
	var tailOffset = tailLength ;
	switch ( tailDirection ) {
		case "nw":
		case "ne":
		case "sw":
		case "se":
			var tailOffset = Math.round( tailLength * Math.sin(Math.PI / 4) );
			break;
		default:
			// 10 pixels are added to prevent tip of tail from 
			// being truncated. TODO: it may be necessary to 
			// consider outline width as a factor. 
			var tailOffset = tailLength ;
			break;
	}
	
	canvasDimensions.height = bubbleDimensions.height + tailOffset;
	canvasDimensions.width = bubbleDimensions.width + tailOffset;
	
	// Determine canvas top, left coordinates
	switch ( tailDirection ) {
		case "nw":
			canvasDimensions.left = tailCoordinates.x;
			canvasDimensions.top = tailCoordinates.y;
			break;
		case "ne":
			canvasDimensions.left = tailCoordinates.x - canvasDimensions.width;
			canvasDimensions.top = tailCoordinates.y;
			break;
		case "se":
			canvasDimensions.left = tailCoordinates.x - canvasDimensions.width;
			canvasDimensions.top = tailCoordinates.y - canvasDimensions.height;
			break;
		case "sw":
			canvasDimensions.left = tailCoordinates.x;
			canvasDimensions.top = tailCoordinates.y - canvasDimensions.height;
			break;
		case "n":
			canvasDimensions.left = tailCoordinates.x - ( canvasDimensions.width / 2 );
			canvasDimensions.top = tailCoordinates.y;
			break;
		case "e":
			canvasDimensions.left = tailCoordinates.x - canvasDimensions.width;
			canvasDimensions.top = tailCoordinates.y - ( canvasDimensions.height / 2 );
			break;
		case "s":
			canvasDimensions.left = tailCoordinates.x - ( canvasDimensions.width / 2 );
			canvasDimensions.top = tailCoordinates.y - canvasDimensions.height;
			break;
		case "w":
			canvasDimensions.left = tailCoordinates.x ;
			canvasDimensions.top = tailCoordinates.y - ( canvasDimensions.height / 2 );
			break;
		default:
		// TODO: error condition here
		break;
}
	
	
	return canvasDimensions ;
	

}

// Create temp canvas and return context for use in calculating
// text metrics. 
function getTempContext( divObject ) {
	
	// TODO: define temp canvas name as a constant
	divObject.append('<canvas id="HBTempCanvas" width="600" height="600" > </canvas>');
	var canvasElement=document.getElementById( "HBTempCanvas" );
	var ctx=canvasElement.getContext("2d");
	return ctx ;
}

function removeTempContext() {
	jQuery("#HBTempCanvas" ).remove();
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
	
	// restore URLs to match actuals on page.
	// for ( i = 0 ; i <	data.length ; i++ )
	// {
	//	data[i].targetImageURL = ( data[i].targetImageURL );
	// } 
	localStorage.setItem( 'bubbleConfig', JSON.stringify( data ) );
} 

function retrieveBubbleConfig( data ) {
  return JSON.parse( localStorage.getItem( 'bubbleConfig' ) );
} 

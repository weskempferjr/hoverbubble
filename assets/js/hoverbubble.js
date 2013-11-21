
jQuery(document).ready(function($) {


	var imageSourceArray = getImageList();

	
	if ( imageSourceArray.length > 0 ) {
		
		var imageInfoData = JSON.stringify(imageSourceArray);
		
		$.ajax({
			url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
			data:{
				'action':'tnotw_hoverbubble_ajax',
				'fn':'get_bubble_config',
				'bubble_id':1,
				'imageInfoData': imageSourceArray,
				'documentURL' : document.URL
	 		},
			dataType: 'JSON',
			success:function(data){
				if ( data.errorData != null && data.errorData == 'true' ) {
					reportError( data );
					return;
				}
				// save data for browser window events.
				saveBubbleConfig( data );
				displayBubbles(data);
	        },
			error: function(errorThrown){
				alert( objectl10n.retrieve_bubble_config_error + errorThrown.responseText.substring(0,500) );
				console.log(errorThrown);
	        }
	
		});
	}

	$(window).resize(function(){
		displayBubbles( retrieveBubbleConfig() );
	});
	
	function reportError( errorData ) {
		var errorString = objectl10n.server_error + errorData.errorMessage ;
		alert( errorString );
	}
});


function displayBubbles(data)
{
	
	jQuery('head').append(document.createElement('style'));
	var bubbleStyleSheet = document.styleSheets[document.styleSheets.length-1];
	 
	for ( i = 0 ; i <	data.length ; i++ )
	{
		displayBubble(data[i], bubbleStyleSheet );
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



function displayBubble(bubbleConfig, bubbleStyleSheet ){

	//var bubbleMessage = bubbleConfig.bubbleMessage ;
	var bubbleID = bubbleConfig.bubbleID ;
	var bubbleFillColor = bubbleConfig.bubbleFillColor ;
	var bubbleTailLength = parseInt(bubbleConfig.bubbleTailLength);
	var bubbleCornerRadius = parseInt(bubbleConfig.bubbleCornerRadius );
	var bubbleOutlineColor = bubbleConfig.bubbleOutlineColor;
	var bubbleOutlineWidth = parseInt(bubbleConfig.bubbleOutlineWidth);
	var bubbleTailX = parseInt(bubbleConfig.bubbleTailX) ;
	var bubbleTailY = parseInt(bubbleConfig.bubbleTailY) ;	
	var bubbleAspectRatio =  parseInt(bubbleConfig.bubbleAspectRatio) ;

	var contentAreaWidth = parseInt(bubbleConfig.contentAreaWidth);
	var contentAreaHeight = parseInt(bubbleConfig.contentAreaHeight);
	
	var bubbleDelay = parseInt( bubbleConfig.bubbleDelay );
	var bubbleDuration = parseInt( bubbleConfig.bubbleDuration );

	// Force tail direction to lower case. All code in this
	// script assumes lower case. 
	var bubbleTailDirection = bubbleConfig.bubbleTailDirection.toLowerCase(); ;
	
	var targetImageURL = bubbleConfig.targetImageURL  ;
	
	var targetImageContainerID = bubbleConfig.targetImageContainerID ;
	var bubbleCanvasID = bubbleConfig.bubbleCanvasID ;
	var contentDivID = bubbleConfig.contentDivID ;
	var contentEmbedID = bubbleConfig.contentEmbedID ;

	// wrap target image in div, call remove in case it previously exists. 
	var the_image = "";
	//         if ( jQuery("#" + targetImageContainerID ).length == 0 ) {
	var the_image = jQuery("img[src='" + targetImageURL	+ "']");
	var imageDivPosition = the_image.position();
	the_image.wrap('<div id="' + targetImageContainerID + '" style="top:' + imageDivPosition.top + 'px; left:' + imageDivPosition.left + 'px"/></div>');
	//          } 
	

	var img_div = jQuery("#" + targetImageContainerID); 
	// imageDivPosition = img_div.position();

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
	

	

	// var top = imageDivPosition.top + canvasAreaDimensions.top ;
	// var left = imageDivPosition.left +   canvasAreaDimensions.left;
	

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



	var strokeColor = bubbleOutlineColor;
	var strokeWidth = bubbleOutlineWidth;


	// remove previous instance if it exists.
	jQuery("#" + contentDivID ).remove();
	jQuery("#" + contentEmbedID ).remove();


	// center position of bubble rectangle relative canvas origin. 
	// var center_x = ( canvas_w / 2 ) * scale_factor ;
	// var center_y = ( canvas_h / 2 ) * scale_factor ;
	var center_x = ( canvas_w / 2 ) ;
	var center_y = ( canvas_h / 2 ) ;
	

	// scaled dimensions
	// var bubble_w = bubbleAreaDimensions.width * scale_factor;
	// var bubble_h = bubbleAreaDimensions.height * scale_factor;
	var bubble_w = bubbleAreaDimensions.width ;
	var bubble_h = bubbleAreaDimensions.height ;
	
	
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


	var contentDivPos = getContentDivPosition( bubbleConfig );
	var wrapperTop = imageDivPosition.top + contentDivPos.top ;
	var wrapperLeft = imageDivPosition.left +   contentDivPos.left;
	
	
	
	img_div.append('<div id="' + contentDivID + '"  style="visibility:hidden;z-index:20;position:absolute;top:' +  wrapperTop +  'px;left:' + wrapperLeft + 'px"></div>');
	 
	jQuery("#" + contentDivID ).append('<object  type="text/html" width="' + contentAreaWidth + 'px" height="' + contentAreaHeight + 'px" data="' + objectl10n.wpsiteinfo.site_url + '/index.php?hb_bubble_id='+ bubbleID +'">');

	 // set content div styles to create bubble
	jQuery("#" + contentDivID ).css("width", contentAreaWidth );
	jQuery("#" + contentDivID ).css("height", contentAreaHeight );
	jQuery("#" + contentDivID ).css("padding", "0px" );
	jQuery("#" + contentDivID ).css("background", bubbleFillColor );
	jQuery("#" + contentDivID ).css("border-width", bubbleOutlineWidth );
	jQuery("#" + contentDivID ).css("border-style", "solid");
	jQuery("#" + contentDivID ).css("border-color", bubbleOutlineColor );
	jQuery("#" + contentDivID ).css("border-radius", bubbleCornerRadius);
	jQuery("#" + contentDivID ).css("-webkit-border-radius", bubbleCornerRadius);
	jQuery("#" + contentDivID ).css("-moz-border-radius", bubbleCornerRadius);
	 
	if ( bubbleTailDirection != "none") {
		 var tailStyleSheetRules = getTailStyleSheetRules( bubbleConfig );
	
		 // Draw tail by adding rules to bubbleSytleSheet
		 var elementSelector = "#" + contentDivID + ":after" ;
		 bubbleStyleSheet.insertRule( elementSelector + " {content: ''}", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " {position: absolute}", 0);
		 
	
		 
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterVerticalPos, 0 );
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterHorizontalPos, 0 );
		 bubbleStyleSheet.insertRule( elementSelector + " {border-style: solid}", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterBorderWidth, 0 );
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterBorderColor, 0 );
		 
	
		 bubbleStyleSheet.insertRule( elementSelector + " {display: block}", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " {width: 0 }", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " {z-index: 1 }", 0);
		 
		 // draw tail outline
		 elementSelector = "#" + contentDivID + ":before" ;
		 bubbleStyleSheet.insertRule( elementSelector + " {content: ''}", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " {position: absolute}", 0);
		 
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeVerticalPos, 0 );
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeHorizontalPos, 0 );
		 bubbleStyleSheet.insertRule( elementSelector + " {border-style: solid}", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeBorderWidth, 0 );
		 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeBorderColor, 0 );
		 
		 bubbleStyleSheet.insertRule( elementSelector + " {display: block}", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " {width: 0 }", 0);
		 bubbleStyleSheet.insertRule( elementSelector + " {z-index: 0 }", 0);
	 }
	 
	 // Display bubble as indicated by delay and duration. 
	 setTimeout( function() {
		 	jQuery("#" + contentDivID ).css("visibility","visible").fadeIn("slow") ;
		 	
		 	if ( bubbleDuration > 0 ) {
			 	setTimeout( function() {
			 		jQuery("#" +  contentDivID ).fadeOut("slow");
			 	}, bubbleDuration );
	 		}
		}, bubbleDelay );
	 

};


function getContentDivPosition( bubbleConfig ) {
	
	var contentAreaWidth = parseInt(bubbleConfig.contentAreaWidth);
	var contentAreaHeight = parseInt(bubbleConfig.contentAreaHeight);
	var bubbleTailLength = parseInt(bubbleConfig.bubbleTailLength);
	var bubbleTailX = parseInt(bubbleConfig.bubbleTailX) ;
	var bubbleTailY = parseInt(bubbleConfig.bubbleTailY) ;	
	var bubbleTailDirection = bubbleConfig.bubbleTailDirection.toLowerCase();
	
	// TODO: config parameter for position
	var position = 0.5;
	
	var contentDivPos = new Object();
	
	// TODO: corner directions (nw, sw ) are to be removed.
	switch ( bubbleTailDirection ) {
	case "n":
	case "ne":
	case "nw":
		contentDivPos.left = bubbleTailX - ( contentAreaWidth * position );
		contentDivPos.top = bubbleTailY + bubbleTailLength ;				
		break; 

	case "se":
	case "sw":
	case "s":
		contentDivPos.left = bubbleTailX - ( contentAreaWidth * position );
		contentDivPos.top = bubbleTailY - bubbleTailLength - contentAreaHeight ;
		break;

	case "w":
		contentDivPos.left = bubbleTailX + bubbleTailLength ;
		contentDivPos.top = bubbleTailY - ( contentAreaHeight * position );
		break;
	
	case "e":
		contentDivPos.left = bubbleTailX - bubbleTailLength - contentAreaWidth;
		contentDivPos.top = bubbleTailY - ( contentAreaHeight * position );
		break;

	default:
		// TODO: error condition
		alert("unknown bubble tail direction");
		break ;
			 
	
	}
	
	return contentDivPos;	
	
}

// return object containing CSS rules for thoses properties dependent on tail and bubble geometry
function getTailStyleSheetRules( bubbleConfig )
{
	var contentAreaWidth = parseInt(bubbleConfig.contentAreaWidth);
	var contentAreaHeight = parseInt(bubbleConfig.contentAreaHeight);
	var bubbleFillColor = bubbleConfig.bubbleFillColor ;
	var bubbleTailLength = parseInt(bubbleConfig.bubbleTailLength);
	var bubbleCornerRadius = parseInt(bubbleConfig.bubbleCornerRadius );
	var bubbleOutlineColor = bubbleConfig.bubbleOutlineColor;
	var bubbleOutlineWidth = parseInt(bubbleConfig.bubbleOutlineWidth);
	var bubbleTailDirection = bubbleConfig.bubbleTailDirection.toLowerCase();
	
	//TODO need config vars for these: position, tail base width
	var position = 0.5;
	var tailBaseWidth = 10;
	
	var tailCSSRules = new Object();
	
	var outLineWidthOffset = bubbleOutlineWidth - 1;
	
	// position of the tail along side of bubble
	var horizontalTailOffset =  ( contentAreaWidth * position ) - tailBaseWidth ;
	var verticalTailOffset  = ( contentAreaHeight * position ) - tailBaseWidth ;
	
	var horizontalOutlineOffset = horizontalTailOffset - ( bubbleOutlineWidth - 1);
	var verticalOutlineOffset = verticalTailOffset - ( bubbleOutlineWidth - 1 ) ;
	
	var tailOutlineLength =  bubbleTailLength +  ( 2 * bubbleOutlineWidth ) - 1 ;
	
	var outlineTailBaseWidth = tailBaseWidth + bubbleOutlineWidth - 1;
	var outlineBubbleTailLength = bubbleTailLength + bubbleOutlineWidth - 1;
	
	var outlineHorizontalPos = contentAreaWidth + bubbleOutlineWidth ;
	var outlineVerticalPos = contentAreaHeight + bubbleOutlineWidth ;
	
	
	// Rules required:
	// after horizontal position (left, right)
	// after vertical position (top, bottom)
	// after border-width
	// after border-color
	// before horizontal position (left, right)
	// before vertical position (top, bottom)
	// before border-width
	// before border-color
	
	// TODO: corner directions (nw, sw ) are to be removed.
	switch ( bubbleTailDirection ) {
	case "n":
	case "ne":
	case "nw":
		
		tailCSSRules.afterVerticalPos = "{top: " +  bubbleTailLength * -1 + "px }" ;
		tailCSSRules.afterHorizontalPos = "{left: " + horizontalTailOffset + "px }" ;
		tailCSSRules.afterBorderWidth = "{border-width: " + 0 + " " + tailBaseWidth + "px " + bubbleTailLength + "px }" ;
		tailCSSRules.afterBorderColor = "{border-color: " + bubbleFillColor + " transparent }" ;
		
		tailCSSRules.beforeVerticalPos = "{top: "  + tailOutlineLength * -1 + "px }";
		tailCSSRules.beforeHorizontalPos = "{left: " + horizontalOutlineOffset + "px }";
		tailCSSRules.beforeBorderWidth = "{border-width: " + 0 + " " + outlineTailBaseWidth + "px " + outlineBubbleTailLength + "px }" ;
		tailCSSRules.beforeBorderColor = "{border-color: " + bubbleOutlineColor + " transparent }" ;
		
		break; 

	case "se":
	case "sw":
	case "s":
		tailCSSRules.afterVerticalPos = "{bottom: " +  bubbleTailLength * -1 + "px }" ;
		tailCSSRules.afterHorizontalPos = "{left: " + horizontalTailOffset + "px }" ;
		tailCSSRules.afterBorderWidth = "{border-width: " + bubbleTailLength + "px " + tailBaseWidth + "px 0}" ;
		tailCSSRules.afterBorderColor = "{border-color: " + bubbleFillColor + " transparent }" ;
		
		tailCSSRules.beforeVerticalPos = "{top: "  + outlineVerticalPos + "px }";
		tailCSSRules.beforeHorizontalPos = "{left: " + horizontalOutlineOffset + "px }";		
		tailCSSRules.beforeBorderWidth = "{border-width: " + outlineBubbleTailLength + "px " + outlineTailBaseWidth + "px 0}" ;
		tailCSSRules.beforeBorderColor = "{border-color: " + bubbleOutlineColor + " transparent }" ;
		break;

	case "w":
		tailCSSRules.afterVerticalPos = "{top: " +  verticalTailOffset + "px }" ;
		tailCSSRules.afterHorizontalPos = "{left: " + bubbleTailLength * -1 + "px }" ;
		tailCSSRules.afterBorderWidth = "{border-width: "  + tailBaseWidth + "px " + bubbleTailLength + "px " + tailBaseWidth + "px 0}" ;
		tailCSSRules.afterBorderColor = "{border-color: transparent " + bubbleFillColor + " }" ;
		
		tailCSSRules.beforeVerticalPos = "{top: "  + verticalOutlineOffset + "px }";
		tailCSSRules.beforeHorizontalPos = "{left: " + tailOutlineLength * -1 + "px }";	
		tailCSSRules.beforeBorderWidth = "{border-width: " + outlineTailBaseWidth + "px " + outlineBubbleTailLength + "px " + outlineTailBaseWidth + "px 0}" ;
		tailCSSRules.beforeBorderColor = "{border-color: transparent " + bubbleOutlineColor + " }" ;
		break;
	
	case "e":		
		tailCSSRules.afterVerticalPos = "{top: " +  verticalTailOffset + "px }" ;
		tailCSSRules.afterHorizontalPos = "{right: " + bubbleTailLength * -1 + "px }" ;
		tailCSSRules.afterBorderWidth = "{border-width: "  + tailBaseWidth + "px 0 " + tailBaseWidth + "px " + bubbleTailLength + "px }" ;
		tailCSSRules.afterBorderColor = "{border-color: transparent " + bubbleFillColor + " }" ;
		
		tailCSSRules.beforeVerticalPos = "{top: "  + verticalOutlineOffset + "px }";
		tailCSSRules.beforeHorizontalPos = "{left: " + outlineHorizontalPos + "px }";	
		tailCSSRules.beforeBorderWidth = "{border-width: " + outlineTailBaseWidth + "px 0 " + outlineTailBaseWidth + "px " + outlineBubbleTailLength + "px}" ;
		tailCSSRules.beforeBorderColor = "{border-color: transparent " + bubbleOutlineColor + " }" ;
		break;

	default:
		// TODO: error condition
		break ;
			 
	
	}
	
	return tailCSSRules;		
	
}

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
		case "none":
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


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
	var contentAreaTextPadding = parseInt(bubbleConfig.textPadding);
	var bubbleTailBaseWidth = parseInt(bubbleConfig.bubbleTailBaseWidth);
	var bubbleTailPosition = parseFloat( bubbleConfig.bubbleTailPosition );
	var bubbleTailType = bubbleConfig.bubbleTailType;
	
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

	var the_image = jQuery("img[src='" + targetImageURL	+ "']");
	var imageDivPosition = the_image.position();
	the_image.wrap('<div id="' + targetImageContainerID + '" style="top:' + imageDivPosition.top + 'px; left:' + imageDivPosition.left + 'px"/></div>');
	
	var img_div = jQuery("#" + targetImageContainerID); 


	textAreaDimensions = new Object();
	textAreaDimensions.width = contentAreaWidth;
	textAreaDimensions.height = contentAreaHeight;

	
	
	// TODO: consider making textPadding a config parameter
	var textPadding = new Object();
	textPadding.x = contentAreaTextPadding;
	textPadding.y = contentAreaTextPadding;
	var bubbleAreaDimensions = calculateBubbleDimensions (	textAreaDimensions,
															textPadding);
	
	var bubbleTailCoordinates = new Object();
	bubbleTailCoordinates.x = bubbleTailX;
	bubbleTailCoordinates.y = bubbleTailY;


	// remove previous instance if it exists.
	jQuery("#" + contentDivID ).remove();
	jQuery("#" + contentEmbedID ).remove();
	 

	var contentDivPos = getContentDivPosition( bubbleConfig );
	var wrapperTop = imageDivPosition.top + contentDivPos.top ;
	var wrapperLeft = imageDivPosition.left +   contentDivPos.left;
	
	
	
	img_div.append('<div id="' + contentDivID + '"  style="visibility:hidden;z-index:20;position:absolute;top:' +  wrapperTop +  'px;left:' + wrapperLeft + 'px"></div>');
	 
	jQuery("#" + contentDivID ).append('<object  type="text/html" width="' + contentAreaWidth + 'px" height="' + contentAreaHeight + 'px" data="' + objectl10n.wpsiteinfo.site_url + '/index.php?hb_bubble_id='+ bubbleID +'">');

	 // set content div styles to create bubble
	jQuery("#" + contentDivID ).css("width", contentAreaWidth );
	jQuery("#" + contentDivID ).css("height", contentAreaHeight );
	jQuery("#" + contentDivID ).css("padding", contentAreaTextPadding + "px" );
	jQuery("#" + contentDivID ).css("background", bubbleFillColor );
	jQuery("#" + contentDivID ).css("border-width", bubbleOutlineWidth );
	jQuery("#" + contentDivID ).css("border-style", "solid");
	jQuery("#" + contentDivID ).css("border-color", bubbleOutlineColor );
	jQuery("#" + contentDivID ).css("border-radius", bubbleCornerRadius);
	jQuery("#" + contentDivID ).css("-webkit-border-radius", bubbleCornerRadius);
	jQuery("#" + contentDivID ).css("-moz-border-radius", bubbleCornerRadius);
	 
	if ( bubbleTailDirection != "none") {
		
		
		
		 var tailStyleSheetRules = getTailStyleSheetRules( bubbleConfig, bubbleAreaDimensions );
		 
		 switch ( bubbleTailType ) { 
		 case "speech":
	
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
			 break;
			 
		 case "thought" :
			 // Draw tail by adding rules to bubbleSytleSheet
			 var elementSelector = "#" + contentDivID + ":after" ;
			 bubbleStyleSheet.insertRule( elementSelector + " {content: ''}", 0);
			 bubbleStyleSheet.insertRule( elementSelector + " {position: absolute}", 0);
			 
		
			 
			 // Draw tail by adding rules to bubbleSytleSheet
			 // little circle
			 var elementSelector = "#" + contentDivID + ":after" ;
			 bubbleStyleSheet.insertRule( elementSelector + " {content: ''}", 0);
			 bubbleStyleSheet.insertRule( elementSelector + " {position: absolute}", 0);
			 
		
			 
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterVerticalPos, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterHorizontalPos, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterWidth, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterHeight, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterWKBorderRadius, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterMozBorderRadius, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterBorderRadius, 0 );
			 
			 bubbleStyleSheet.insertRule( elementSelector + " {border-style: solid}", 0);
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterBorderWidth, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterBorderColor, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.afterFillColor, 0 );
			 
		
			 bubbleStyleSheet.insertRule( elementSelector + " {display: block}", 0);
			 // bubbleStyleSheet.insertRule( elementSelector + " {width: 0 }", 0);
			 bubbleStyleSheet.insertRule( elementSelector + " {z-index: 1 }", 0);
			 
			 // big cirle
			 elementSelector = "#" + contentDivID + ":before" ;
			 bubbleStyleSheet.insertRule( elementSelector + " {content: ''}", 0);
			 bubbleStyleSheet.insertRule( elementSelector + " {position: absolute}", 0);
			 
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeVerticalPos, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeHorizontalPos, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeWidth, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeHeight, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeWKBorderRadius, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeMozBorderRadius, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeBorderRadius, 0 );
			 
			 bubbleStyleSheet.insertRule( elementSelector + " {border-style: solid}", 0);
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeBorderWidth, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeBorderColor, 0 );
			 bubbleStyleSheet.insertRule( elementSelector + " " + tailStyleSheetRules.beforeFillColor, 0 );
			 
			 bubbleStyleSheet.insertRule( elementSelector + " {display: block}", 0);
			 // bubbleStyleSheet.insertRule( elementSelector + " {width: 0 }", 0);
			 bubbleStyleSheet.insertRule( elementSelector + " {z-index: 0 }", 0);
			 
			 
			 break;
			
			 
		 default:
			 break;
		 
		 }
		 
	 } // end if bubbleTailDirection
	 
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
function getTailStyleSheetRules( bubbleConfig, bubbleAreaDimensions )
{
	var contentAreaWidth = parseInt(bubbleConfig.contentAreaWidth);
	var contentAreaHeight = parseInt(bubbleConfig.contentAreaHeight);
	var bubbleFillColor = bubbleConfig.bubbleFillColor ;
	var bubbleTailLength = parseInt(bubbleConfig.bubbleTailLength);
	var bubbleCornerRadius = parseInt(bubbleConfig.bubbleCornerRadius );
	var bubbleOutlineColor = bubbleConfig.bubbleOutlineColor;
	var bubbleOutlineWidth = parseInt(bubbleConfig.bubbleOutlineWidth);
	var bubbleTailDirection = bubbleConfig.bubbleTailDirection.toLowerCase();
	var bubbleTailType = bubbleConfig.bubbleTailType;
	
	
	var position = parseFloat( bubbleConfig.bubbleTailPosition );
	var tailBaseWidth = parseInt( bubbleConfig.bubbleTailBaseWidth );
	
	var tailCSSRules = new Object();
	
	var outLineWidthOffset = bubbleOutlineWidth - 1;
	
	// position of the tail along side of bubble
	// removed - tailBaseWidth
	var tailBaseOffset = tailBaseWidth / 2 ;
	
	var horizontalTailOffset =  ( bubbleAreaDimensions.width * position ) - tailBaseOffset ;
	var verticalTailOffset  = ( bubbleAreaDimensions.height * position ) - tailBaseOffset ;
	
	if ( bubbleTailType == "speech") { 
		horizontalTailOffset =  ( bubbleAreaDimensions.width * position ) - tailBaseWidth ;
		verticalTailOffset  = ( bubbleAreaDimensions.height * position ) - tailBaseWidth ;
	}
	
	var horizontalOutlineOffset = horizontalTailOffset - ( bubbleOutlineWidth - 1);
	var verticalOutlineOffset = verticalTailOffset - ( bubbleOutlineWidth - 1 ) ;
	
	var tailOutlineLength =  bubbleTailLength +  ( 2 * bubbleOutlineWidth ) - 1 ;
	
	var outlineTailBaseWidth = tailBaseWidth + bubbleOutlineWidth - 1;
	var outlineBubbleTailLength = bubbleTailLength + bubbleOutlineWidth - 1;
	
	var outlineHorizontalPos = contentAreaWidth + bubbleOutlineWidth ;
	var outlineVerticalPos = contentAreaHeight + bubbleOutlineWidth ;
	

	// Rules required for "thought" tail:
	// for small circle
	// after horizontal position (left, right)
	// after vertical position (top, bottom)
	// after border-width
	// after border-color
	// after border width
	// after border color
	// after background color
	// after border radius
	
	// for large circle
	// before horizontal position (left, right)
	// before vertical position (top, bottom)
	// before width
	// before height
	// before border width
	// before border color
	// before background color
	// before border radius
	
		
	
	// Rules required for speech tail:
	// after horizontal position (left, right)
	// after vertical position (top, bottom)
	// after border-width
	// after border-color
	// before horizontal position (left, right)
	// before vertical position (top, bottom)
	// before border-width
	// before border-color
	
	
	switch ( bubbleTailType ) {
		
	case "speech":
	
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
			
			tailCSSRules.beforeVerticalPos = "{bottom: "  + tailOutlineLength * -1 + "px }";
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
			tailCSSRules.beforeHorizontalPos = "{right: " + tailOutlineLength * -1  + "px }";
			tailCSSRules.beforeBorderWidth = "{border-width: " + outlineTailBaseWidth + "px 0 " + outlineTailBaseWidth + "px " + outlineBubbleTailLength + "px}" ;
			tailCSSRules.beforeBorderColor = "{border-color: transparent " + bubbleOutlineColor + " }" ;
			break;
	
		default:
			// TODO: error condition
			break ;
				 		
		}
		break; // case speech
		
	case "thought":
		
		var radiusSmallCirle = tailBaseWidth / 2;
		var widthSmallCircle = tailBaseWidth / 2;
		
		var radiusLargeCircle = tailBaseWidth;
		var widthLargeCircle = tailBaseWidth;
		
		// offsett to center align small circle with large circle
		var smallCircleCenterOffset = Math.round( ( widthLargeCircle - widthSmallCircle ) / 2 );
		
		// TODO: about 80% of the statements in each case can be factored out. 
		switch ( bubbleTailDirection ) {
		case "n":
		case "ne":
		case "nw":
			
			tailCSSRules.afterVerticalPos = "{top: " +  bubbleTailLength * -1 + "px }" ;
			var offset =  horizontalTailOffset + smallCircleCenterOffset - ( bubbleOutlineWidth );
			tailCSSRules.afterHorizontalPos = "{left: " + offset + "px }" ;
			tailCSSRules.afterWidth = "{width: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterHeight = "{height: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterWKBorderRadius = "{-webkit-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterMozBorderRadius = "{-moz-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderRadius = "{border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.afterBorderColor = "{border-color: " + bubbleOutlineColor +   " }" ;
			tailCSSRules.afterFillColor = "{background: " + bubbleFillColor + "}" ;
			
			tailCSSRules.beforeVerticalPos = "{top: "  +  Math.round( -1 * widthLargeCircle ) + "px }";
			tailCSSRules.beforeHorizontalPos = "{left: " + horizontalOutlineOffset + "px }";
			tailCSSRules.beforeWidth = "{width: " + widthLargeCircle + "px}" ;
			tailCSSRules.beforeHeight = "{height: " + widthLargeCircle + "px}" ;			
			tailCSSRules.beforeWKBorderRadius = "{-webkit-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeMozBorderRadius = "{-moz-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderRadius = "{border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.beforeBorderColor = "{border-color: " + bubbleOutlineColor + " }" ;
			tailCSSRules.beforeFillColor = "{background: " + bubbleFillColor + "}" ;
			
			break; 
	
		case "se":
		case "sw":
		case "s":
			tailCSSRules.afterVerticalPos = "{bottom: " +  bubbleTailLength * -1 + "px }" ;
			var offset =  horizontalTailOffset + smallCircleCenterOffset - ( bubbleOutlineWidth );
			tailCSSRules.afterHorizontalPos = "{right: " + offset + "px }" ;
			tailCSSRules.afterWidth = "{width: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterHeight = "{height: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterWKBorderRadius = "{-webkit-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterMozBorderRadius = "{-moz-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderRadius = "{border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.afterBorderColor = "{border-color: " + bubbleOutlineColor +   " }" ;
			tailCSSRules.afterFillColor = "{background: " + bubbleFillColor + "}" ;
			
			tailCSSRules.beforeVerticalPos = "{bottom: "  +  Math.round( -1 * widthLargeCircle ) + "px }";
			tailCSSRules.beforeHorizontalPos = "{right: " + horizontalOutlineOffset + "px }";
			tailCSSRules.beforeWidth = "{width: " + widthLargeCircle + "px}" ;
			tailCSSRules.beforeHeight = "{height: " + widthLargeCircle + "px}" ;			
			tailCSSRules.beforeWKBorderRadius = "{-webkit-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeMozBorderRadius = "{-moz-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderRadius = "{border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.beforeBorderColor = "{border-color: " + bubbleOutlineColor + " }" ;
			tailCSSRules.beforeFillColor = "{background: " + bubbleFillColor + "}" ;
			break;
	
		case "w":

			tailCSSRules.afterHorizontalPos =  "{left: " +  bubbleTailLength * -1 + "px }" ;
			var offset =  verticalTailOffset + smallCircleCenterOffset - ( bubbleOutlineWidth );
			tailCSSRules.afterVerticalPos =  "{top: " + offset + "px }" ;
				
			tailCSSRules.afterWidth = "{width: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterHeight = "{height: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterWKBorderRadius = "{-webkit-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterMozBorderRadius = "{-moz-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderRadius = "{border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.afterBorderColor = "{border-color: " + bubbleOutlineColor +   " }" ;
			tailCSSRules.afterFillColor = "{background: " + bubbleFillColor + "}" ;
			
			tailCSSRules.beforeHorizontalPos = "{left: "  +  Math.round( -1 * widthLargeCircle ) + "px }";
			tailCSSRules.beforeVerticalPos = "{top: " + verticalOutlineOffset + "px }";				
			tailCSSRules.beforeWidth = "{width: " + widthLargeCircle + "px}" ;
			tailCSSRules.beforeHeight = "{height: " + widthLargeCircle + "px}" ;			
			tailCSSRules.beforeWKBorderRadius = "{-webkit-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeMozBorderRadius = "{-moz-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderRadius = "{border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.beforeBorderColor = "{border-color: " + bubbleOutlineColor + " }" ;
			tailCSSRules.beforeFillColor = "{background: " + bubbleFillColor + "}" ;
			break;
		
		case "e":		
			tailCSSRules.afterHorizontalPos =  "{right: " +  bubbleTailLength * -1 + "px }" ;
			var offset =  verticalTailOffset + smallCircleCenterOffset - ( bubbleOutlineWidth );
			tailCSSRules.afterVerticalPos =  "{top: " + offset + "px }" ;
				
			tailCSSRules.afterWidth = "{width: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterHeight = "{height: " + widthSmallCircle + "px}" ;
			tailCSSRules.afterWKBorderRadius = "{-webkit-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterMozBorderRadius = "{-moz-border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderRadius = "{border-radius: " + radiusSmallCirle + "px}" ;
			tailCSSRules.afterBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.afterBorderColor = "{border-color: " + bubbleOutlineColor +   " }" ;
			tailCSSRules.afterFillColor = "{background: " + bubbleFillColor + "}" ;
			
			tailCSSRules.beforeHorizontalPos = "{right: "  +  Math.round( -1 * widthLargeCircle ) + "px }";
			tailCSSRules.beforeVerticalPos = "{top: " + verticalOutlineOffset + "px }";				
			tailCSSRules.beforeWidth = "{width: " + widthLargeCircle + "px}" ;
			tailCSSRules.beforeHeight = "{height: " + widthLargeCircle + "px}" ;			
			tailCSSRules.beforeWKBorderRadius = "{-webkit-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeMozBorderRadius = "{-moz-border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderRadius = "{border-radius: " + radiusLargeCircle + "px}" ;
			tailCSSRules.beforeBorderWidth = "{border-width: " + bubbleOutlineWidth + "px }" ;
			tailCSSRules.beforeBorderColor = "{border-color: " + bubbleOutlineColor + " }" ;
			tailCSSRules.beforeFillColor = "{background: " + bubbleFillColor + "}" ;
			break;
	
		default:
			// TODO: error condition
			break ;
				 		
		}
		
		break;
		
	default:
		// TODO: error condition
		break ;
	}
	
	return tailCSSRules;		
	
}




// calculate bubble dimensions - the area that does not include the tail
function calculateBubbleDimensions( textDimensions, textPadding ) {	
	var bubbleDimensions = new Object();
	bubbleDimensions.height = textDimensions.height + (textPadding.x * 2);
	bubbleDimensions.width = textDimensions.width + (textPadding.y * 2);
	return bubbleDimensions ;
	
}




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

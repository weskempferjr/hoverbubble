<?php
class BubbleEditScrubber {
	
	public static function scrub( $inputData ) {
		
		$scrubbed = array();
		$scrubbed['bubble_fill_color'] = sanitize_text_field( $inputData['bubble_fill_color'] );
		$scrubbed['bubble_name'] = sanitize_text_field( $inputData['bubble_name'] ) ;		
		// TODO: Determin what might be necessary to secure bubble_message field. It base64 encoded immediately by the controller. 
		// Is that sufficient? 
		$scrubbed['bubble_message'] = $inputData['bubble_message'] ;
		$scrubbed['bubble_id'] = absint(  $inputData['bubble_id'] ) ;
		$scrubbed['bubble_tail_length'] = absint( $inputData['bubble_tail_length'] ) ;
		$scrubbed['bubble_corner_radius'] = absint( $inputData['bubble_corner_radius'] ) ;
		$scrubbed['bubble_outline_color'] = sanitize_text_field( $inputData['bubble_outline_color'] );
		$scrubbed['bubble_outline_width'] = absint( $inputData['bubble_outline_width'] ) ;
		$scrubbed['bubble_tail_direction'] = self::sanitizeBubbleTailDirection( $inputData['bubble_tail_direction'] ) ;
		$scrubbed['bubble_tail_x'] = absint( $inputData['bubble_tail_x'] ) ;
		$scrubbed['bubble_tail_y'] = absint( $inputData['bubble_tail_y'] ) ;
		$scrubbed['content_area_width'] = absint( $inputData['content_area_width'] ) ;
		$scrubbed['content_area_height'] = absint( $inputData['content_area_height'] ) ;
		$scrubbed['target_image_url'] = esc_url( $inputData['target_image_url'], array('http','https') );
		$scrubbed['bubble_description'] = sanitize_text_field( stripslashes_deep( $inputData['bubble_description'] ) );
		$scrubbed['bubble_delay'] = absint( $inputData['bubble_delay'] ) ;
		$scrubbed['bubble_duration'] = self::sanitizeBubbleDuration( $inputData['bubble_duration'] ) ;
		$scrubbed['bubble_pages'] = self::sanitizeBubblePages( $inputData['bubble_pages'] );
		$scrubbed['bubble_author'] = $inputData['bubble_author'];		
		$scrubbed['bubble_tail_type'] = $inputData['bubble_tail_type'];
		$scrubbed['text_padding'] = $inputData['text_padding'];
		$scrubbed['bubble_tail_base_width'] = $inputData['bubble_tail_base_width'];
		$scrubbed['bubble_tail_position'] = $inputData['bubble_tail_position'];
		
		if ( isset( $inputData['published'] ) ) {
			$scrubbed['published'] = TRUE ;
		}
		else {
			$scrubbed['published'] = FALSE ;
		}
				
		return $scrubbed ;
	} 
	
	private static function sanitizeBubbleDuration( $bubbleDuration ) {
		if ( is_numeric( $bubbleDuration )) {
			return $bubbleDuration ;
		}
		else {
			Logger::logError("BubbleEditScrubber: detected invalid input in bubble delay field, forcing to -1");
			return -1 ;
		}
	}
	
	private static function sanitizeBubblePages( $bubblePages ) {
		
		$validBubblePages = array();
		
		// If none selected, just return the array as is. 
		if ( $bubblePages[0] == "None" ) {
			array_push( $validBubblePages, "None" );
		}
		else {
			foreach ( $bubblePages as $bubblePage ) {
				$tmpBP = absint( $bubblePage );
				if ( $tmpBP == 0 ) {
					Logger::logError("BubbleEditScrubber: invalid bubble page ID in form input. Ignoring." );
				}
				else {
					array_push( $validBubblePages, $tmpBP );
				}
			}
		}
		return $validBubblePages ;
	}
	
	private static function sanitizeBubbleTailDirection( $direction ) {
		
		switch ($direction) {
			case 'N':
			case 'NW':
			case 'NE':
			case 'E':
			case 'W':
			case 'S':
			case 'SW':
			case 'SE':
			case 'NONE':
				$validDirection = $direction ;
				break;			
			default:
				Logger::logError("BubbleEditScrubber: detected invalid tail direction. Forcing direction to N");
				$validDirection = "N";
				break;
		}
		return $validDirection ;
	}
}
?>
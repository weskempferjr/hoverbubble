<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/WPResources.php");



// TODO: error handling in ajax controller.
class BubbleConfigAjaxController {
	
	public static function getBubbleConfigs() {
		switch($_REQUEST['fn']){
			case 'get_bubble_config':
				//$output = tnotw_get_bubble_configs();
				$output = BubbleConfigAjaxController::retrieveBubbleConfigs();
				break;
			default:
				$output = 'No function specified, check your jQuery.ajax() call';
			break;

		}

		// Convert $output to JSON and echo it to the browser 
	
		$output=json_encode($output);
		if(is_array($output)){
			print_r($output);	
 		}
		else {
			echo $output;
	     }
		die;
	}
	
	public static function getBubbleContent($wp) {
		
		if ( $wp->query_vars == null )
			return;

		if (array_key_exists('hb_bubble_id', $wp->query_vars) ) {
			$bubble_id = $wp->query_vars['hb_bubble_id'];
			// TODO: error handling here/run through prepare			
			// $bubble = $wpdb->get_row( "SELECT bubble_message FROM $wpdb->hoverbubbles WHERE bubble_id = " . $bubble_id , ARRAY_A );
		    // echo base64_decode($bubble['bubble_message']);
		    
			$bubble = new BubbleConfig();
			$bubble->restore($bubble_id);
			echo $bubble->getBubbleMessage();
			die;			
	    }
		
	}
	
	private static function retrieveBubbleConfigs() {

		// Clients sends a list of images on the 
		// current pages. Retrive bubbles associated
		// only with those images.
		$image_list = $_REQUEST['imageInfoData'];
		$where_clause = "target_image_url IN (";
	
		$img_count = count( $image_list );
		for ( $i = 0 ;  $i < $img_count ; $i++ ) {
			$where_clause .= '"' . $image_list[$i] . '"';
			if ( $i == ($img_count - 1)) {
				$where_clause .= ')';
			} 
			else {
				$where_clause .= ',';
			}
		}
		// TODO: error checking
		
		$bubbles = BubbleConfig::retrieveBubbles($where_clause);
		$configArray = array();
		foreach ( $bubbles as $bubble ) {
			// Message content is retrieved in a separate request.
			$bubble->setBubbleMessage(""); 
			$mappedConfig = BubbleConfigAjaxController::mapConfig( $bubble );
			array_push( $configArray, $mappedConfig );
		}
		return $configArray ;	
	
	}
	
	// TODO: move to BubbleConfig
	private static function mapConfig( $bubbleConfig ) {
		
		// $imageURL = WPResources::getImageURL( $bubbleConfig->getTargetImageID() );
		
		$mappedConfig = array(	
					'bubbleFillColor' => $bubbleConfig->getBubbleFillColor(),
					'bubbleName' => $bubbleConfig->getBubbleName(),
					'bubbleID' => $bubbleConfig->getBubbleID(),
					'bubbleTailLength' => $bubbleConfig->getBubbleTailLength(),
					'bubbleCornerRadius' => $bubbleConfig->getBubbleCornerRadius(),
					'bubbleOutlineColor' => $bubbleConfig->getBubbleOutlineColor(),
					'bubbleOutlineWidth' => $bubbleConfig->getBubbleOutlineWidth(),
					'bubbleTailDirection' => $bubbleConfig->getBubbleTailDirection(),
					'targetImageID' => $bubbleConfig->getTargetImageID(),
					'targetImageContainerID' => $bubbleConfig->getTargetImageContainerID(),
					'bubbleCanvasID' => $bubbleConfig->getBubbleCanvasID() ,
					'contentDivID' => $bubbleConfig->getContentDivID(),
					'embedID' => $bubbleConfig->getEmbedID(),
					'bubbleTailX' => $bubbleConfig->getBubbleTailX(),
					'bubbleTailY' => $bubbleConfig->getBubbleTailY(),
					'contentAreaHeight' => $bubbleConfig->getContentAreaHeight(),
					'contentAreaWidth' => $bubbleConfig->getContentAreaWidth(),
					'canvasBorderStyle' => $bubbleConfig->getCanvasBorderStyle(),
					'targetImageURL' => $bubbleConfig->getTargetImageURL()
		);
		return $mappedConfig ;
	}


}
?>
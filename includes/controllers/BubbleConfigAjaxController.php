<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubblePage.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/PageCandidate.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/ImageCandidate.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/WPResources.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/ImageListGenerator.php");




// TODO: error handling in ajax controller.
class BubbleConfigAjaxController {
	
	public static function getBubbleConfigs() {
		switch($_REQUEST['fn']){
			case 'get_bubble_config':
				$output = self::retrieveBubbleConfigs();
				break;
			case 'does_bubble_name_exist':
				$bubbleExists = self::doesBubbleNameExist();
				$output = array('bubbleExists' => $bubbleExists );
				break;
			case 'gen_site_image_list':
				// TODO: Return an indication of success or failure here.
				$output = self::genSiteImageList();
				break;
			case 'get_page_candidate_list':
				$output = self::retrievePageCandidates( $_REQUEST['target_image_url'], $_REQUEST['bubble_id'] );
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
		
		$currentPage = rtrim( $_SERVER['HTTP_REFERER'], "/" );
		
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
		
		// TODO: create a view to simplify this mess. 
		foreach ( $bubbles as $bubble ) {
			// Verify if bubble should appear on current page.
			// First get the image candidate ID for the target image URL.  
			$targetImageURL = $bubble->getTargetImageURL();
			$whereClause = "target_image_url = '" . $targetImageURL . "'";
			$imageCandidates = ImageCandidate::retrieveImageCandidates( $whereClause );
			$imageCandidate = $imageCandidates[0];
			$imageCandidateID = $imageCandidate->getImageCandidateID();
			
			// Get the page candidate list for image.
			$whereClause = "image_candidate_id = " . $imageCandidateID . " AND target_page_url = '" . $currentPage  ."'";
			$pageCandidates = PageCandidate::retrievePageCandidates( $whereClause );
			// If no page candidates found for the image, don't display. 
			// TODO: this is probably and error condition. It indicates the image tables are not up-to-date. 
			if ( count( $pageCandidates ) == 0 ) {
				continue ;
			}
			
			$pageCandidate = $pageCandidates[0];
			
			// See if there is bubble page record for this page and bubble. If no bubble page, don't display.
			$whereClause = "bubble_id = " . $bubble->getBubbleID() . " AND page_candidate_id = " . $pageCandidate->getPageCandidateID();
			$bubblePages = BubblePage::retrieveBubblePages( $whereClause ) ;
			if ( count( $bubblePages) == 0 ) {
				continue;
			}
			
			
			// Message content is retrieved in a separate request.
			$bubble->setBubbleMessage(""); 
			$mappedConfig = BubbleConfigAjaxController::mapConfig( $bubble );
			array_push( $configArray, $mappedConfig );
		}
		return $configArray ;	
	
	}
	
	private static function retrievePageCandidates( $targetImageURL, $bubbleID ) {
		
		$whereClause = "target_image_url = '" . trim($targetImageURL ) . "'";
		
		// TODO: This would be cleaner with a getRow call guaranteed to return 1 record. 
		$imageCandidateArray = ImageCandidate::retrieveImageCandidates( $whereClause ) ;
		$imageCandidate = $imageCandidateArray[0];
		
		$whereClause = "image_candidate_id = " . $imageCandidate->getImageCandidateID();
		$pageCandidates = PageCandidate::retrievePageCandidates( $whereClause );
		
		$mappedPageCandidates = array();
		
		foreach ( $pageCandidates as $pageCandidate ) {
			$mappedPageCandidate = self::mapPageCandidate( $pageCandidate );
			array_push( $mappedPageCandidates, $mappedPageCandidate );
		}
		
		// Get page candidates for bubble id if requeested.
		if ( $bubbleID != "" || $bubbleID != null ) {
			$whereClause = "bubble_id = " . $bubbleID ;
			$bubblePages = BubblePage::retrieveBubblePages( $whereClause ) ;
		}
		
		$displayPageIDs = array();
		
		foreach ( $bubblePages as $bubblePage ) {
			array_push( $displayPageIDs, $bubblePage->getPageCandidateID() );
		}
		
		
		$pageCandidateData = array(
			'pageCandidates' => $mappedPageCandidates,
			'displayPageIDs' => $displayPageIDs
		);
		
		return $pageCandidateData;	
		
	}
	
	private static function doesBubbleNameExist() {
		$bubble_name = $_REQUEST['bubble_name'];
		$where_clause = "bubble_name = '" . $bubble_name . "'" ;
		$bubbles = BubbleConfig::retrieveBubbles($where_clause);
		
		if ( count($bubbles) == 0 ) {
			return FALSE;
		}
		else {
			return TRUE;
		}
		
	}
	
	private static function genSiteImageList() {
		$imageList = ImageListGenerator::updateCandidateTables();
		return $imageList;
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
	
	// TODO: move to PageCandidate
	private static function mapPageCandidate( $pageCandidate ) {
		$mappedPageConfig = array(
			'pageCandidateID' => $pageCandidate->getPageCandidateID(),
			'imageCandidateID' => $pageCandidate->getImageCandidateID(),
			'targetPageURL' => $pageCandidate->getTargetPageURL()
		);
		return $mappedPageConfig ;
	}


}
?>
<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubblePage.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/PageCandidate.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/ImageCandidate.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/WPResources.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/ImageListGenerator.php");

/*
 * BubbleConfigAjaxController class
 * This class provides static functions that implement the 
 * ajax controler functions for the plugin. The following 
 * ajax request are supported:
 * 
 * Get Bubble Configs -- return the bubble configuration for 
 * each bubble eligible for display on the current page. 
 * 
 * Does Bubble Name Exist -- returns boolean based on whether a 
 * bubble exists with the specified name. 
 * 
 * Gen Site Image List -- this request initiates a site image 
 * list generation, a process which crawls the site to create a list
 * of potential target images. 
 * 
 * Get Page Candidate List -- for a specified image, this request 
 * returns a list of URLs on which a target image appears on the site.
 * 
 * Get Bubble Content -- returns the content for a specified bubble. 
 * 
 */


class BubbleConfigAjaxController {
	
	/*
	 * Function: getBubbleConfigs
	 * TODO: this function is misnamed as it actually does much
	 * more than just retrieve bubble configuations. Should be
	 * renamed to something like execRequest(). 
	 * 
	 * This function is registered as the ajax responder for the
	 * plugin in Wordpress. It calls subordinate functions in order
	 * to satisfy the request. The return string from the subordinate
	 * function is output as a client response directly in this function.
	 * 
	 * If an exception is caught by this function, data related to the
	 * exception are formated and sent as an error response to the
	 * client. 
	 * 
	 * Parameters: none directly. Reads $_REQUEST for 'fn' parameter. 
	 * 
	 */
	
	public static function getBubbleConfigs() {
		
		try {
			switch($_REQUEST['fn']){
				case 'get_bubble_config':
					// If these are not set, consider it an invalid request.
					if ( !isset( $_REQUEST['imageInfoData'] ) || !isset( $_REQUEST['documentURL'] ) ) {
						throw new Exception(_e('Invalid bubble config request.', TNOTW_HB_TEXTDOMAIN) );
					}
		
					$output = self::retrieveBubbleConfigs();
					break;
				case 'does_bubble_name_exist':
					$bubbleExists = self::doesBubbleNameExist();
					$output = array('bubbleExists' => $bubbleExists );
					break;
				case 'gen_site_image_list':
					ImageListGenerator::updateCandidateTables();
					$output = array(
						'updateTablesStatus' => 'success'
					);
					break;
				case 'get_page_candidate_list':
					$output = self::retrievePageCandidates( $_REQUEST['target_image_url'], $_REQUEST['bubble_id'] );
					break;
				default:
					$output = __('Unknown ajax request sent from client.', TNOTW_HB_TEXTDOMAIN );
				break;
	
			}
		} 
		catch ( Exception $e ) {
			$errorData = array(
				'errorData' => 'true',
				'errorMessage' => $e->getMessage(),
				'errorTrace' => $e->getTraceAsString()
			);
			$output = $errorData;
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
	
	/*
	 * Function: getBubbleContent
	 * This function is registered as a query responder. Once a 
	 * bubbles geometry is configured on the client side, a query 
	 * is sent by the client to retrieve the HTML contents that
	 * are to be displayed within the bubble. This function
	 * retrieves the content for the specified bubble and outputs 
	 * as as response. 
	 * 
	 * TODO: getBubbleContent should catch exceptions and report 
	 * error conditions to the client. 
	 * 
	 * Paramters: $wp in order to extact query var hb_bubble_id. 
	 * 
	 */
	public static function getBubbleContent($wp) {
		
		if ( $wp->query_vars == null )
			return;

		if (array_key_exists('hb_bubble_id', $wp->query_vars) ) {
			$bubble_id = $wp->query_vars['hb_bubble_id'];
			
			// Make sure query parameter is indeed something resembling a bubble id.
			$bubble_id = absint( $bubble_id );
		    
			$bubble = new BubbleConfig();
			$bubble->restore($bubble_id);
			echo $bubble->getBubbleMessage();
			die;			
	    }
		
	}
	
	/*
	 * Function: retrieveBubbleConfigs
	 * 
	 * This function retrieves the configuration for each bubble eligle
	 * to be displayed on the current page. The the curent page URL and
	 * lists of images on the page is sent via the $_REQUEST global. The database
	 * is queried to get a list of bubbles associated with 
	 * the one or more of the images on the page. Each bubble is
	 * then checked to see if has been published (ie, the published flag is set,
	 * and that the bubble is displayable on the current page. All bubble configs
	 * that meet these criteria are returned in a array to the caller. 
	 * 
	 *  Parameters: documentURL, imaageInfoData ( a list of image URLs) via $_REQUEST.
	 *  Returns: bubbleConfigArray
	 *  
	 *  Exceptions thrown: database-related exceptions may be thrown by this function. 
	 */
	
	private static function retrieveBubbleConfigs() {

		// Clients sends a list of images on the 
		// current pages. Retrive bubbles associated
		// only with those images.
						
		$currentPage = rtrim( $_REQUEST['documentURL'], '/' );
		$raw_image_list = $_REQUEST['imageInfoData'];
		
		// srcrub image list of suspect input
		$image_list = array();
		foreach ( $raw_image_list as $image_url ) {
			array_push( $image_list,  esc_url( $image_url, array('http', 'https') ) ) ;
		}
		
		
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

		
		$bubbles = BubbleConfig::retrieveBubbles($where_clause);
		$configArray = array();
		
		// TODO: create a view to simplify this mess. 
		foreach ( $bubbles as $bubble ) {
			
			// See if bubble has published flag set. If not
			// allow only author to view it. 
			if ( $bubble->getPublished() == FALSE ) {
				$currentUser = wp_get_current_user() ;
				if ( $currentUser->ID != $bubble->getBubbleAuthor() ) {
					continue;
				}	
			}
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
	
	
	/*
	 * Function: retrievePageCandidates
	 * 
	 * A page candidate in a page on which bubble appears, or can potentially
	 * appear. This function returns an array containing an array of 'mapped' page
	 * candidates (the page URLs on which an image appears) and optionally a second 
	 * array containing the pages IDs on which a specified bubble has been
	 * selected to appear. In the bubble editor UI, the array is used to 
	 * display the pages on which a selected target image appears. When editing
	 * and existing bubble, the array of page IDs are used to indicate on
	 * which pages the currently edited bubble has been selected to appear. 
	 * 
	 * The term 'mapped' in this context refers to a domain object that has been
	 * converted to an associative array ready for display in the UI. 
	 * 
	 * Parameters: targetImageURL, bubbleID
	 * Return: an array of two arrays -- mappedPageCandidates and display page IDs. 
	 */
	private static function retrievePageCandidates( $targetImageURL, $bubbleID ) {
		
		$targetImageURL = esc_url( $targetImageURL, array('http', 'https') );
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
	
	/*
	 * Function: doesBubbleNameExist
	 * Determine if bubble with specified name exists.
	 * 
	 * Parameters: none directly. $_REQUEST refereences for the parameter 'bubble_name'.
	 * Return: A boolean indicating whether or not a bubble with the specified name exists.
	 */
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
	
	/*
	 * Function: mapConfig
	 * 
	 * Take a bubbleConfig object and copy its contents to an associative array
	 * for use in the admin UI and on the the client side. 
	 * 
	 * Parameters: bubbleConfig -- bubbleConfig ojbect.
	 * Return: an associative array containing bubble config data. 
	 * TODO: move to BubbleConfig
	 */
	
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
					'targetImageURL' => $bubbleConfig->getTargetImageURL(),
					'bubbleDelay' => $bubbleConfig->getBubbleDelay(),
					'bubbleDuration' => $bubbleConfig->getBubbleDuration(),
					'bubbleTailType' => $bubbleConfig->getBubbleTailType(),
					'textPadding' => $bubbleConfig->getTextPadding(),
					'bubbleTailBaseWidth' => $bubbleConfig->getBubbleTailBaseWidth(),
					'bubbleTailPosition' => $bubbleConfig->getBubbleTailPosition()
		);
		return $mappedConfig ;
	}
	
	/*
	 * Function: mapPageCandidate
	 * 
	 * Return an associative array containing page Candidate data
	 * Paramters: a pageCandidate object
	 * Return: an associative array for use in the admin UI. 
	 * TODO: move to PageCandidate
	 */
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
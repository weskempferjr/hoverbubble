<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/AdminEditView.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubblePage.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/WPRegistrar.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/BubbleSettingsController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/BubbleEditScrubber.php" );






class BubbleEditActionController {
	
	public static function routeRequest( $statusMessage ) {
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', TNOTW_HB_TEXTDOMAIN ) );
		}

		// Render the HTML for the Settings page or include a file that does
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
			BubbleEditActionController::dispatchEditAction();
		}
		else {
			WPRegistrar::registerAdminAssets();
			BubbleEditActionController::displayEditActionView($statusMessage);
		}
			
	}
	
	public static function displayEditActionView($statusMessage){
		
		$edit_action = $_GET['action'] ;		
		
		try {
			switch ( $edit_action ) {
			case "add":				
				
				$bubble = new BubbleConfig();
				self::setViewImageCandidateList();
				AdminEditView::displayBubbleEditPage($bubble,"add", $statusMessage);	
				break;
			case "edit":
				$bubble_id = $_GET['bubble_id'];								
				$bubble = new BubbleConfig();
				$bubble->restore($bubble_id);
				self::setViewImageCandidateList();
				AdminEditView::displayBubbleEditPage($bubble,"edit", $statusMessage);
				break;
				
			case "delete":
				// Don't generate page but delete immediately. Assumes confirmation
				// on the client side (i.e, the action is not cancelled).
				$bubble_id = $_GET['bubble_id'];
				
				// verify nonce
				$nonce_action = 'bubble_delete_bubble_id' . $bubble_id ;
				check_admin_referer( $nonce_action );
				
				BubbleConfig::delete( $bubble_id ) ;
				WPRegistrar::registerAdminAssets();
				$statusMessge = sprintf(__('Delete of %d succeeded.', TNOTW_HB_TEXTDOMAIN ), $bubble_id );
				BubbleSettingsController::displaySettingsView($statusMessge);								
				break;
				
			default:				
				throw new Exception( __('Error: Unknown view edit page action', TNOTW_HB_TEXTDOMAIN) );
				break;
			}
		} 
		catch( Exception $e ) {
			ErrorController::displayErrorPage($e, "BubbleEditActionController error in displayActionEditActionView");
		}
	}
	
	public static function dispatchEditAction() {
		
		$edit_action =  $_POST['edit_action'];
		
		// scrub input before attempting to process it. 
		$scrubbed = BubbleEditScrubber::scrub( $_POST );
	
		try {
			switch ( $edit_action ) {
				case "add":					
					$nonce_action = 'bubble_add' ;
					check_admin_referer( $nonce_action );
					$bubble = new BubbleConfig();
					$bubble->columnsToObject($scrubbed, false);
					$bubble->insert();
					self::insertBubblePages( $bubble, $scrubbed['bubble_pages'] );
					$status = __('Add succeeded.', TNOTW_HB_TEXTDOMAIN );
					break;
				case "edit":
					$bubble = new BubbleConfig();
					$bubble->columnsToObject($scrubbed, false);
					// verify nonce
					$nonce_action = 'bubble_edit_bubble_id' . $bubble->getBubbleID() ;
					check_admin_referer( $nonce_action );
					$bubble->update();
					self::updateBubblePages( $bubble, $scrubbed['bubble_pages'] );
					$status = __('Edit succeeded.', TNOTW_HB_TEXTDOMAIN );
					break;
				default:
					throw new Exception( __('Error: Unknown edit action', TNOTW_HB_TEXTDOMAIN) );
					break;
			}
	
			WPRegistrar::registerAdminAssets();
			BubbleSettingsController::routeRequest( $status );					
			
		}
		catch (Exception $e ) {
			ErrorController::displayErrorPage($e, __( 'BubbleEditActionController error in dispatchEditAction', TNOTW_HB_TEXTDOMAIN ));			
		}
	}
	
	private static function setViewImageCandidateList() {
		$whereClause = "" ; // Get all images displayed on site.
		$imageCandidates = ImageCandidate::retrieveImageCandidates( $whereClause );
		AdminEditView::setImageCandidateList( $imageCandidates );
	}
	
	private static function insertBubblePages( $bubble, $pageCandidateIDs ) {
		// If "None" was selected, don't create new page list--effectively: 
		// do not display the bubble. 
		if ( $pageCandidateIDs[0] == "None"  || ( count( $pageCandidateIDs ) == 0 )) {
			return;
		}
		else {
			$bubbleID = $bubble->getBubbleID();
			foreach ( $pageCandidateIDs as $pageCandidateID ) {
				$bubblePage = new BubblePage();
				$bubblePage->setBubbleID( $bubbleID );
				$bubblePage->setPageCandidateID( $pageCandidateID );
				$bubblePage->insert();			
			}
		}
	}
	
	private static function updateBubblePages( $bubble, $pageCandidateIDs ) {
		
		$bubbleID = $bubble->getBubbleID();
		
		// Get existing bubble pages for this bubble. 
		$whereClause = "bubble_id = " . $bubbleID ;
		$bubblePages = BubblePage::retrieveBubblePages( $whereClause) ;

		// If "None", delete all bubble pages for this bubble.   
		if ( $pageCandidateIDs[0] == "None"  || ( count( $pageCandidateIDs ) == 0 ) ) {							
			foreach ( $bubblePages as $bubblePage )	{
				 BubblePage::delete( $bubblePage->getBubblePageID() );
			}
			return ;
		} 
		else {
			// For each select pageCandidateID, see if there is a BubblePage record for it. If not
			// add it.							
			foreach ( $pageCandidateIDs as $pageCandidateID ) {
				$whereClause = "bubble_id = " . $bubbleID . " and page_candidate_id = " . $pageCandidateID ;
				$bubblePages = BubblePage::retrieveBubblePages( $whereClause) ;
				if ( count( $bubblePages ) == 0 ) {
					$bubblePage = new BubblePage();
					$bubblePage->setBubbleID( $bubbleID );
					$bubblePage->setPageCandidateID( $pageCandidateID );
					$bubblePage->insert();	
				}
			}
		}

		// Delete any pages not selected. 
		$whereClause = "bubble_id = " . $bubbleID . " and page_candidate_id NOT IN ( ";
		$pcCount = count( $pageCandidateIDs );
		for ( $i = 0 ;  $i < $pcCount ; $i++ ) {
			$whereClause .= ' ' . $pageCandidateIDs[$i] ;
			if ( $i == ($pcCount - 1)) {
				$whereClause .= ')';
			} 
			else {
				$whereClause .= ',';
			}
		}
		
		$bubblePages = BubblePage::retrieveBubblePages( $whereClause) ;
		foreach ( $bubblePages as $bubblePage )	{
				BubblePage::delete( $bubblePage->getBubblePageID() );
		}
	}
	
			
}

?>
<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/AdminEditView.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubblePage.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/WPRegistrar.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/BubbleSettingsController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/BubbleEditScrubber.php" );
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/CMSInfoFactory.php");


/*
 * BubbleEditActionController class
 * 
 * This class provides static method to handle requests 
 * related to add, edit, and delete operations on bubbles. 
 */


class BubbleEditActionController {
	
	/*
	 * Function: routeRequest
	 * 
	 * This function is the main entry point into the controller. It determines
	 * whether the request is a post, and if so, calls the dispatchEditAction
	 * method to handle the post request. Otherwise, it will
	 * call dipslayEditActionView in order to display the add/edit form.
	 * 
	 *  This function also verifies whether or not the user has privs to
	 *  perform the requested action.
	 *  
	 *   Parameters: $statusMessage - message that is passed to subordinate functions for display in the UI.
	 *   Return: none
	 */
	
	public static function routeRequest( $statusMessage ) {
		
		//TODO: current_user_can/wp_die are wordpress depedencies that should be hidden behind an interface
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', TNOTW_HB_TEXTDOMAIN ) );
		}

		// Render the HTML for the Settings page or include a file that does
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
			BubbleEditActionController::dispatchEditAction();
		}
		else {	
			// TODO; Wordpress dependency not hidden
			WPRegistrar::registerAdminAssets();
			BubbleEditActionController::displayEditActionView($statusMessage);
		}
			
	}
	
	/*
	 * Function: displayEditActionView
	 * 
	 * This function determines from the $_GET variable 
	 * which action is being requested and calls the AdminEditView
	 * static methods to display the bubble edit form.
	 * 
	 * Parameters: statusMessage to be optionally displayed in the UI.
	 * Return: none
	 * 
	 * Exception handling: all exceptions thrown in the backend are
	 * caught here. If an execption is caught, the ErrorController is called
	 * to display information regarding the error in the UI. 
	 */
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
	
	/*
	 * Function: dispatchEditAction
	 * 
	 * This static function called the necessary subordinate functions
	 * in order to process a posted edit action (add, update, delete).
	 * 
	 * Parameters: none directly, references $_POST. 
	 * Return: none
	 * 
	 * Exception handling: all exceptions thrown in the backend are
	 * caught here. If an execption is caught, the ErrorController is called
	 * to display information regarding the error in the UI. 
	 */
	public static function dispatchEditAction() {
		
		$edit_action =  $_POST['edit_action'];
		
		// scrub input before attempting to process it. 
		$scrubbed = BubbleEditScrubber::scrub( $_POST );
	
		try {
			switch ( $edit_action ) {
				case "add":					
					$nonce_action = 'bubble_add' ;
					// TODO: CMS/WP dependency 
					check_admin_referer( $nonce_action );
					
					$bubble = new BubbleConfig();									
					$bubble->columnsToObject($scrubbed, false);
					
					// Set author based on current user. 
					$bubbleAuthor = self::getAuthorID() ;					
					$bubble->setBubbleAuthor($bubbleAuthor) ;
					
					$bubble->insert();
					self::insertBubblePages( $bubble, $scrubbed['bubble_pages'] );
					$status = __('Add succeeded.', TNOTW_HB_TEXTDOMAIN );
					break;
				case "edit":
					$bubble = new BubbleConfig();
					$bubble->columnsToObject($scrubbed, false);
					// verify nonce
					// TODO: This is wordpress dependency that needs to be hidden.
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
	
	/*
	 * Function: setViewImageCandidateList
	 * Retrieve the image candidates from the database and pass them
	 * to the UI via the AdminEditView class. 
	 * 
	 */
	private static function setViewImageCandidateList() {
		$whereClause = "" ; // Get all images displayed on site.
		$imageCandidates = ImageCandidate::retrieveImageCandidates( $whereClause );
		AdminEditView::setImageCandidateList( $imageCandidates );
	}
	
	/*
	 * Function: insertBubblePages
	 * 
	 * This function is called when adding a new bubble. It records the
	 * pages on which the user has selected a new bubble to appear. 
	 * 
	 * Parameters: 	bubble -- a bubble object
	 * 				pageCandidateIDs -- an array of page candidate ID
	 * Return: none
	 * 
	 * Exceptions from the database/model class methods may
	 * be thrown by this method. 
	 */
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
	/*
	 * Function: updateBubblePages
	 * 
	 * This function is called when editing bubble. It records the
	 * pages on which the user has selected a bubble to appear. 
	 * 
	 * Parameters: 	bubble -- a bubble object
	 * 				pageCandidateIDs -- an array of page candidate ID
	 * Return: none
	 * 
	 * Exceptions from the database/model class methods may
	 * be thrown by this method. 
	 */
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
	
	
	/*
	 * Function: getAuthorID
	 * 
	 * Return the author ID for the current user. 
	 * 
	 * Parameters: none
	 * Return: author ID
	 */
	private static function getAuthorID() {
		$cmsInfo = CMSInfoFactory::getCMSInfo();
		return $cmsInfo->getAuthorID();
	}
	

	
			
}

?>
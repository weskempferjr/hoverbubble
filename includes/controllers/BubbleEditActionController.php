<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/AdminEditView.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/WPRegistrar.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/BubbleSettingsController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");






class BubbleEditActionController {
	
	public static function routeRequest( $statusMessage ) {
		if (!current_user_can('manage_options')) {
			wp_die('YOU do not have sufficient permissions to access this page.');
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
				AdminEditView::displayBubbleEditPage($bubble,"add", $statusMessage);	
				break;
			case "edit":
				$bubble_id = $_GET['bubble_id'];
				$bubble = new BubbleConfig();
				$bubble->restore($bubble_id);
				AdminEditView::displayBubbleEditPage($bubble,"edit", $statusMessage);
				break;
			case "delete":
				// Don't generate page but delete immediately. Assumes confirmation
				// on the client side (i.e, the action is not cancelled).
				$bubble_id = $_GET['bubble_id'];
				
				BubbleConfig::delete( $bubble_id ) ;
				WPRegistrar::registerAdminAssets();
				$statusMessge = "Delete of $bubble_id succeeded.";
				BubbleSettingsController::displaySettingsView($statusMessge);								
				break;
				
			default:				
				ErrorController::displayErrorPage( new Exception(""), "Error: this is a bug. Unknown edit action");
				break;
			}
		} 
		catch( Exception $e ) {
			ErrorController::displayErrorPage($e, "BubbleEditActionController error in displayActionEditActionView");
		}
	}
	
	public static function dispatchEditAction() {
		
		$edit_action =  $_POST['edit_action'];
	
		try {
			switch ( $edit_action ) {
				case "add":
					$bubble = new BubbleConfig();
					$bubble->columnsToObject($_POST, false);
					$bubble->insert();
					break;
				case "edit":
					$bubble = new BubbleConfig();
					$bubble->columnsToObject($_POST, false);
					$bubble->update();
					break;
				default:
					throw new Exception("Error: this is a bug. Unknown edit action", -1);
					break;
			}
	
			WPRegistrar::registerAdminAssets();
			$status = "Update (" . $edit_action .  ")succeeded." ;
			BubbleSettingsController::routeRequest( $status );					
			
		}
		catch (Exception $e ) {
			ErrorController::displayErrorPage($e, "BubbleEditActionController error in dispatchEditAction");			
		}
	}
			
}

?>
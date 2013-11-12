<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/AdminSettingsView.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/SettingsFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/Settings.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/SettingsScrubber.php" );
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/Logger.php" );

/*
 * BubbleSettingsController
 * 
 * This class provides static functions that are 
 * called in response to user actions in the Bubble Settings UI. 
 */

class BubbleSettingsController {
	
	private static $settings ;
	
	/*
	 * Function: routeRequest
	 * 
	 * This function is the main entry point into the controller. It determines
	 * whether the request is a post, and if so, calls the dispatchEditAction
	 * method to handle the post request. Otherwise, it will
	 * call dipslaySettingsView in order to display the bubble setting UI.
	 * 
	 *  This function also verifies whether or not the user has privs to
	 *  perform the requested action.
	 *  
	 *   Parameters: $statusMessage - message that is passed to subordinate functions for display in the UI.
	 *   Return: none
	 */
	
	public static function routeRequest( $statusMessage ){
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', TNOTW_HB_TEXTDOMAIN ) );
		}
		
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
			BubbleSettingsController::dispatchEditAction();
		}

		BubbleSettingsController::displaySettingsView( $statusMessage );
	}
	
	/*
	 * Function: displaySettingView
	 * 
	 * Call necessary functions in model and setting and pass
	 * to the AdminSettingsView class for display in the UI.
	 * 
	 * Parameters: statusMessage -- a message which may optionally be display in the UI.
	 * 
	 * Exception handling: all exceptions thrown in the backend are
	 * caught here. If an execption is caught, the ErrorController is called
	 * to display information regarding the error in the UI. 
	 */
	public static function displaySettingsView( $statusMessage ){
		try {
			if ( ! isset( self::$settings ) ) {
				self::$settings = SettingsFactory::getSettings();
			}
			self::$settings->load();
			$bubbles = BubbleConfig::retrieveBubbles("");
			AdminSettingsView::displayBubbleSettingsPage($bubbles, self::$settings , $statusMessage);
		}
		catch ( Exception $e ) {
			ErrorController::displayErrorPage($e, __('BubbleSettingsController error in displayActionEditActionView', TNOTW_HB_TEXTDOMAIN));
		}
	}
	
	/*
	 * Function: dispatchEditAction
	 * 
	 * This function is called in response to user request to change
	 * settings values. 
	 * 
	 * Paramters: none directly. The $_POST variable is referenced for settings values.
	 * Return: none
	 * 
	 * Exception handling: all exceptions thrown in the backend are
	 * caught here. If an execption is caught, the ErrorController is called
	 * to display information regarding the error in the UI. 
	 */
	public static function dispatchEditAction() {
		
		
		try {
			if ( isset( $_POST['hb_settings'] ) ) {
				if ( ! isset( self::$settings ) ) {
					self::$settings = SettingsFactory::getSettings();	
				}
				$scrubbed = SettingsScrubber::scrub( $_POST ) ;
				self::$settings->setCrawlPath( $scrubbed['crawlpath'] );
				self::$settings->setExclusionList( $scrubbed['exclusionlist'] ) ;
				self::$settings->store();
			}
		}
		catch (Exception $e ) {
			ErrorController::displayErrorPage($e, __( 'BubbleSettingsController error in dispatchEditAction', TNOTW_HB_TEXTDOMAIN ) );			
		}
	}
}
?>
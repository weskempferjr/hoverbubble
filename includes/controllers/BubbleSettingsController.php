<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/AdminSettingsView.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/SettingsFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/Settings.php");



class BubbleSettingsController {
	
	private static $settings ;
	
	public static function routeRequest( $statusMessage ){
		if (!current_user_can('manage_options')) {
			wp_die('You do not have sufficient permissions to access this page.');
		}
		
		if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
			BubbleSettingsController::dispatchEditAction();
		}

		BubbleSettingsController::displaySettingsView( $statusMessage );
	}
	
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
			ErrorController::displayErrorPage($e, "BubbleSettingsController error in displayActionEditActionView");
		}
	}
	
public static function dispatchEditAction() {
		
	
		try {
			if ( isset( $_POST['hb_settings'] ) ) {
				if ( ! isset( self::$settings ) ) {
					self::$settings = SettingsFactory::getSettings();	
				}
				self::$settings->setCrawlPath( $_POST['crawlpath'] );
				self::$settings->setExclusionList( $_POST['exclusionlist'] ) ;
				self::$settings->store();
			}
		}
		catch (Exception $e ) {
			ErrorController::displayErrorPage($e, "BubbleSettingsController error in dispatchEditAction");			
		}
	}
}
?>
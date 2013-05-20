<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/AdminSettingsView.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/SettingsFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/Settings.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/SettingsScrubber.php" );
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/util/Logger.php" );



class BubbleSettingsController {
	
	private static $settings ;
	
	public static function routeRequest( $statusMessage ){
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', TNOTW_HB_TEXTDOMAIN ) );
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
			ErrorController::displayErrorPage($e, __('BubbleSettingsController error in displayActionEditActionView', TNOTW_HB_TEXTDOMAIN));
		}
	}
	
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
<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/BubbleConfig.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/AdminSettingsView.php");


class BubbleSettingsController {
	
	public static function routeRequest( $statusMessage ){
		if (!current_user_can('manage_options')) {
			wp_die('You do not have sufficient permissions to access this page.');
		}

		BubbleSettingsController::displaySettingsView( $statusMessage );
	}
	
	public static function displaySettingsView( $statusMessage ){
		try {
			$bubbles = BubbleConfig::retrieveBubbles("");
			AdminSettingsView::displayBubbleSettingsPage($bubbles, $statusMessage);
		}
		catch ( Exception $e ) {
			ErrorController::displayErrorPage($e, "BubbleSettingsController error in displayActionEditActionView");
		}
	}
}
?>
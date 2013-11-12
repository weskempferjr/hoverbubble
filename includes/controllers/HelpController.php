<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/HelpView.php");

/*
 * HelpController class
 * 
 * This class provides static methods which are used
 * to display pages in the help UI. 
 */

class HelpController {
	
	public static function displayHelpPage($statusMessage) {
		HelpView::displayHelpPage($statusMessage );
	}
}
?>
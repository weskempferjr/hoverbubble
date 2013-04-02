<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/HelpView.php");

class HelpController {
	
	public static function displayHelpPage($statusMessage) {
		HelpView::displayHelpPage($statusMessage );
	}
}
?>
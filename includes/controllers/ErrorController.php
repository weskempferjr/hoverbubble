<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/ErrorView.php");

class ErrorController {
	
	public static function handleError( $exception, $statusMessage ) {
		displayErrorView( $exception, $statusMessage);
	}
	
	public static function displayErrorPage($exception, $statusMessage) {
		ErrorView::displayErrorPage($exception, $statusMessage );
	}
}
?>
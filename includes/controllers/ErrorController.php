<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/views/ErrorView.php");


/*
 * ErrorController class
 * 
 * Static functions in this class are called in response to
 * exceptions. 
 */
class ErrorController {
	
	public static function handleError( $exception, $statusMessage ) {
		displayErrorView( $exception, $statusMessage);
	}
	
	public static function displayErrorPage($exception, $statusMessage) {
		ErrorView::displayErrorPage($exception, $statusMessage );
	}
}
?>
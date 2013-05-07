<?php
class Logger {
	
	public static function logError( $message ) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }

	}
	
	public static function logDebug( $message ) {
		if (WP_DEBUG === true) {
			self::logError( $message ) ;
		}
	}
}
?>
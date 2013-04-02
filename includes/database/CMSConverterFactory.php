<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/BubbleConfigConverter.php");

class CMSConverterFactory {
	public static function geBubbleConfigConverter() {
		return new BubbleConfigConverter();
	}
}

?>
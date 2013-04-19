<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/BubbleConfigConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/ImageCandidateConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/PageCandidateConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/BubblePageConverter.php");


class CMSConverterFactory {
	public static function getBubbleConfigConverter() {
		return new BubbleConfigConverter();
	}
	
	public static function getImageCandidateConverter() {
		return new ImageCandidateConverter();
	}
	
	public static function getPageCandidateConverter() {
		return new PageCandidateConverter();
	}
	
	public static function getBubblePageConverter() {
		return new BubblePageConverter();
	}
}

?>
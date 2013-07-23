<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/WPInfo.php");

class CMSInfoFactory {
	public static function getCMSInfo() {
		return new WPInfo();
	}	
}

?>
<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/WPSettings.php");

class SettingsFactory {
	public static function getSettings() {
		return new WPSettings();
	}
}
?>
<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/WPSettings.php");

/*
 * SettingsFactory class
 * This class is called to instantiate an implementation of the Settings
 * interface. 
 * TODO: Need runtime configuration to specify which implmentation to instantiate.
 */
class SettingsFactory {
	public static function getSettings() {
		return new WPSettings();
	}
}
?>
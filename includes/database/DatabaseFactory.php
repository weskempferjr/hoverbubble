<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/WPDatabase.php");

class DatabaseFactory {
	public static function getDatabase() {
		return new WPDatabase();
	}
}
?>
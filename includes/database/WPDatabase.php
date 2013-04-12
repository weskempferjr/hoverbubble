<?php

// require_once("../constants.php");
// require_once( TNOTW_HOVERBUBBLE_DIR . "includes/constants.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/Database.php");





class WPDatabase implements Database {

	private static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hoverbubbles)) {
			$wpdb->hoverbubbles = $wpdb->prefix . 'hoverbubbles';
		}	
	}

	
	public function getRow( $cmsConverter ) {
		global $wpdb ;
		WPDatabase::setAlias();
		$args = $cmsConverter->getCMSSelectRowArgs();
		
		$columnValues  = $wpdb->get_row( "SELECT * FROM " .  $wpdb->$args['wpTableName'] . " WHERE " . $args['wpPrepareFormat'] .  " = " . $args['wpPrepareValues'] , ARRAY_A );
		if ( $columnValues == null )
			throw new Exception("WPDatabase::getRow: Could not retrieve row from ". $wpdb->$args['wpTableName'], -1);
		return $columnValues ;
	}
	
	public function getRows( $cmsConverter ) {
		global $wpdb ;
		WPDatabase::setAlias();
		$args = $cmsConverter->getCMSSelectRowsArgs();
		
		$whereClause = "";
		if ( $args['wpWhereClause'] != "" ){
			$whereClause = " WHERE " . $args['wpWhereClause'];
		}
		
		$columnValuesArray = $wpdb->get_results( "SELECT " . $args['wpPrepareFormat'] .  " FROM " . $wpdb->$args['wpTableName'] . $whereClause , ARRAY_A );
		
		if ( empty($columnValuesArray) ) {
			return $columnValuesArray;
		}
		else if ( $columnValuesArray == NULL )
			throw new Exception("WPDatabase::getRows: Could not retrieve rows from ". $wpdb->$args['wpTableName'], -1);
		return $columnValuesArray ;
	}

	
	public function updateRows( $cmsConverter ){
		global $wpdb ;
		WPDatabase::setAlias();
		$args = $cmsConverter->getCMSUpdateArgs();
		$sql = $wpdb->prepare( 
			"UPDATE " . $wpdb->$args['wpTableName'] . "  set " . $args['wpPrepareFormat'],
			$args['wpPrepareValues']
		);
		$ret_val = $wpdb->query( $sql );
		if ( $ret_val === false )
			throw new Exception("WPDatabase::updateRows: Update failed in table ". $args['wpTableName'], -1);
	}
	
	public function insertRow( $cmsConverter ) {
		global $wpdb ;
		WPDatabase::setAlias();
		$args = $cmsConverter->getCMSInsertArgs();
		$sql = $wpdb->prepare( 
			"INSERT INTO " . $wpdb->$args['wpTableName'] . " " . $args['wpPrepareFormat'],
			$args['wpPrepareValues']
		);
		
		$ret_val = $wpdb->query( $sql );
		if ( $ret_val == false )
			throw new Exception("WPDatabase::insertRow: Insert failed in table ". $wpdb->$args['wpTableName'], -1);
	}
	
	public function deleteRow( $cmsConverter ) {
		global $wpdb ;
		WPDatabase::setAlias();
		$args = $cmsConverter->getCMSDeleteArgs();
		$sql = $wpdb->prepare( 
			"DELETE FROM " . $wpdb->$args['wpTableName'] . " WHERE " . $args['wpPrepareFormat'],
			$args['wpPrepareValues']
		);
		
		$ret_val = $wpdb->query( $sql );
		if ( $ret_val == false )
			throw new Exception("WPDatabase::deleteRow: Delete failed in table ". $args['wpTableName'], -1);
	}
	
	/*
	 * In wordpress this will be called from an activation function
	 * like so:
	 * 
	 * 	WPDatabase::createTable ( 	BubbleConfig::generateDDL(), 
	 * 								BubbleConfig::getTableName() );
	 */
	public function createTable($ddl, $tableName ) {
		global $wpdb;
		WPDatabase::setAlias();
		$hbtable = $wpdb->prefix . $tableName ;
 
		//verify if table already exists
		if($wpdb->get_var("SHOW TABLES LINK '$hbtable'") !== $hbtable ) {
			$sql = $ddl ;
		}
 
		//include the wordpress db functions
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);

		update_option(TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY, TNOTW_HOVERBUBBLE_VERSION);
	}
	
}

?>
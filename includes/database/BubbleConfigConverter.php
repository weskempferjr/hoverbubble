<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");

/*
 * BubbleConfigConverter class
 * 
 * This class provides that functions that map bubble config 
 * relation data to a bubbleConfig object.
 * 
 * TODO: references to $wpdb should be hidden
 */
class BubbleConfigConverter implements CMSConverter {
	
	private $insertArgs ;
	private $deleteArgs;
	private $updateArgs;
	private $selectRowArgs;
	private $selectRowsArgs ;
	private $bubbleConfig ;
	
	/*
	 * Function: setAlias
	 * 
	 * Sets the alias for the bubble config table in the $wpdp
	 * variable. 
	 * TODO: wordpress dependency that should be hidden behind
	 * an interface. 
	 * Parameters: none
	 * Return: none
	 */
	public static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hoverbubbles)) {
			$wpdb->hoverbubbles = $wpdb->prefix . 'hoverbubbles';
		}	
	}
	
	
	/*
	 * Function: generateDDL
	 * 
	 * This function stores the DDL necessary to create
	 * the table associated with this object. 
	 * 
	 * Parameters: none
	 * Return: a string containing the DDL for the object.
	 */
	
	public static final function generateDDL() {
		global $wpdb ;
		
		self::setAlias() ;
		
       $sql = "CREATE TABLE " . $wpdb->prefix . 'hoverbubbles' . " ( 
					bubble_id mediumint(12) NOT NULL AUTO_INCREMENT,
					bubble_name varchar(50) NOT NULL DEFAULT '',
					bubble_description varchar(1000) DEFAULT NULL,
					bubble_message varchar(4000) NOT NULL DEFAULT '',
					bubble_fill_color varchar(50) DEFAULT NULL,
					bubble_tail_length smallint(4) unsigned DEFAULT NULL,
					bubble_tail_direction char(2) DEFAULT NULL,
					bubble_tail_x smallint(4) unsigned DEFAULT NULL,
					bubble_tail_y smallint(4) unsigned DEFAULT NULL,
					bubble_corner_radius smallint(4) unsigned DEFAULT NULL,
					bubble_outline_color varchar(50) DEFAULT NULL,
					bubble_outline_width smallint(4) unsigned DEFAULT NULL,
					canvas_border_style varchar(100) DEFAULT NULL,
					content_area_width smallint(4) unsigned DEFAULT NULL,
					content_area_height smallint(4) unsigned DEFAULT NULL,
					target_image_id bigint(20) unsigned DEFAULT NULL,
					target_image_url varchar(1000) DEFAULT NULL,
					bubble_delay int(11) DEFAULT '0',
					bubble_duration int(11) DEFAULT '-1',
					bubble_author bigint(20) unsigned NOT NULL DEFAULT '0',
					published tinyint(3) unsigned NOT NULL DEFAULT '0',
					bubble_tail_type char(40) NOT NULL DEFAULT 'speech',
					text_padding smallint(6) NOT NULL DEFAULT '10',
  					bubble_tail_base_width smallint(6) NOT NULL DEFAULT '10',
  					bubble_tail_position float unsigned NOT NULL DEFAULT '0.5'
					PRIMARY KEY  bubble_id (bubble_id),
					UNIQUE KEY  bubble_name (bubble_name)
				) ;";
		
			return $sql;	
	}
	
	/*
	 * Function: tableExists
	 * 
	 * Returns a boolean indicated whether the table for the object exists.
	 * 
	 * Paramters: none
	 * Return: boolean indicating whether or not the table exists
	 */
	public static function tableExists() {
		
		global $wpdb ;
		$tablename = $wpdb->prefix . 'hoverbubbles';
		if( $wpdb->get_var( "SHOW TABLES LIKE '$tablename'" ) != $tablename ) { 
			return false ;
		}
		return true ;
	}
	
	/*
	 * Function: generateUpgradeDLL
	 * 
	 * This function is intended to be called when upgrading
	 * to new versions. 
	 * 
	 * Parameters: none
	 * Return: a string containing the DDL for the object.
	 */
	public static final function generateUpgradeDDL() {
		global $wpdb ;
		
		self::setAlias() ;
		
       $sql = "CREATE TABLE " . $wpdb->prefix . 'hoverbubbles' . " ( 
					bubble_id mediumint(12) NOT NULL AUTO_INCREMENT,
					bubble_name varchar(50) NOT NULL DEFAULT '',
					bubble_description varchar(1000) DEFAULT NULL,
					bubble_message varchar(4000) NOT NULL DEFAULT '',
					bubble_fill_color varchar(50) DEFAULT NULL,
					bubble_tail_length smallint(4) unsigned DEFAULT NULL,
					bubble_tail_direction char(2) DEFAULT NULL,
					bubble_tail_x smallint(4) unsigned DEFAULT NULL,
					bubble_tail_y smallint(4) unsigned DEFAULT NULL,
					bubble_corner_radius smallint(4) unsigned DEFAULT NULL,
					bubble_outline_color varchar(50) DEFAULT NULL,
					bubble_outline_width smallint(4) unsigned DEFAULT NULL,
					canvas_border_style varchar(100) DEFAULT NULL,
					content_area_width smallint(4) unsigned DEFAULT NULL,
					content_area_height smallint(4) unsigned DEFAULT NULL,
					target_image_id bigint(20) unsigned DEFAULT NULL,
					target_image_url varchar(1000) DEFAULT NULL,
					bubble_delay int(11) DEFAULT '0',
					bubble_duration int(11) DEFAULT '-1',
					bubble_author bigint(20) unsigned NOT NULL DEFAULT '0',
					published tinyint(3) unsigned NOT NULL DEFAULT '0',
					bubble_tail_type char(40) NOT NULL DEFAULT 'speech',
					text_padding smallint(6) NOT NULL DEFAULT '10',
  					bubble_tail_base_width smallint(6) NOT NULL DEFAULT '10',
  					bubble_tail_position float unsigned NOT NULL DEFAULT '0.5'
				) ;";
		
			return $sql;	
	}
	
	/*
	 * Function: getCMSSelectRowArgs
	 * 
	 * Returns the select row args for the oject. Called by
	 * the database object when retrieving an instance 
	 * of this object from the database.
	 * 
	 * Parameters: none
	 * Returns: selectRowArgs array. See setCMSSelectRowArgs
	 */
	public function getCMSSelectRowArgs() {
		return $this->selectRowArgs ;
	}
	
	/* 
	 * Function: setCMSSelectRowArgs
	 * 
	 * Set the select row args for the object.
	 * 
	 * Parameters: $object, and instance of the object with the primary
	 * key field set.
	 * Returns: none
	 */
	public function setCMSSelectRowArgs( $object ) {
		global $wpdb;
		
		self::setAlias();
		
		$this->selectRowArgs = array(
			'wpPrepareFormat' => "bubble_id",
			'wpPrepareValues' => $object->getBubbleID(),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	/*
	 * Function: getCMSSelectRowsArgs
	 * 
	 * Returns the select *rows* (plural) args for the object.
	 * 
	 * Parameters: none
	 * Returns: selectRowsArgs array. See setCMSSelectRowsArgs
	 * 
	 */
	public function getCMSSelectRowsArgs() {
		return $this->selectRowsArgs ;
	}
	
	
	/* 
	 * Function: setCMSSelectRowsArgs
	 * 
	 * Set the select rows (plural) args for the object.
	 * 
	 * Parameters: $whereClause -- a string containing the whereClause for retrieving multiple rows
	 * Returns: none
	 */
	public function setCMSSelectRowsArgs( $whereClause ) {
		global $wpdb;
		
		self::setAlias();
		
		$this->selectRowsArgs = array(
			'wpPrepareFormat' => "*",
			'wpWhereClause' => $whereClause,
			'wpTableName' => 'hoverbubbles'
		);
	}
	/*
	 * Function: getCMSInsertArgs
	 * 
	 * Return CMS insert args. Usually called byt the Database object. 
	 */
	public function getCMSInsertArgs() {
		return $this->insertArgs;
	}
	
	/*
	 * Function: setCMSInsertArgs
	 * 
	 * This function is called when doing an insert. The object to be
	 * inserted creates its respective converter, and then calls 
	 * this method in the converter to stort the insert args (which is basically
	 * just a string containing DML appropriate for the objects 
	 * respective storage table. 
	 */
	public function setCMSInsertArgs( $object) {
		global $wpdb;

		self::setAlias();
		
		$this->bubbleConfig = $object ;
		
		// Get WP author information and set in bubble config option.
		global $current_user ;
		get_currentuserinfo();
		$object->setBubbleAuthor( $current_user->ID );
		
		
		$this->insertArgs = array(
			'wpPrepareFormat' => "(	
				bubble_id,
				bubble_name,
				bubble_message,
				bubble_fill_color,			
				bubble_tail_length,				
				bubble_corner_radius,
				bubble_outline_color,								
				bubble_outline_width,
				bubble_tail_direction,				
				bubble_tail_x,
				bubble_tail_y,
				content_area_height,
				content_area_width,				
				target_image_id,
				target_image_url,
				bubble_description,
				bubble_delay,
				bubble_duration,
				bubble_author,
				published,
				bubble_tail_type,
				text_padding,
				bubble_tail_base_width,
				bubble_tail_position
			) VALUES ( %d, %s, %s, %s, %d, %d, %s, %d, %s, %d, %d, %d, %d, %d, %s, %s, %d, %d, %d, %d, %s, %d, %d, %f )",
			'wpPrepareValues' => $object->objectToColumns( true ),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	/*
	 * Function: getCMSUpdateArgs
	 * 
	 * This function is called by the Dabase object to get the 
	 * update SQL for the object. 
	 */
	public function getCMSUpdateArgs() {
		return $this->updateArgs;
	}
	
	
	/*
	 * Function: setCMSUpdateArgs
	 * 
	 * This function is called when doing an update. The object to be
	 * updated creates its respective converter, and then calls 
	 * this method in the converter to stort the insert args (which is basically
	 * just a string containing DML appropriate for the objects 
	 * respective storage table. 
	 */
	public function setCMSUpdateArgs( $object) {
		
		global $wpdb;
		
		self::setAlias();
		
		
		// Note that bubble_author is not updated. This based on the
		// assumption that authorship will not be modifiable. 
		$this->updateArgs = array( 
			'wpPrepareFormat' => "
				bubble_name = %s,
				bubble_message = %s,
				bubble_fill_color = %s,
				bubble_tail_length = %d,
				bubble_corner_radius = %d,
				bubble_outline_color = %s,
				bubble_outline_width = %d,
				bubble_tail_direction = %s,
				bubble_tail_x = %d,
				bubble_tail_y = %d,
				content_area_height = %d,
				content_area_width = %d,
				target_image_id = %d,
				target_image_url = %s,
				bubble_description = %s,
				bubble_delay = %d,
				bubble_duration = %d,
				bubble_author = %d,
				published = %d,
				bubble_tail_type = %s,
				text_padding = %d,
				bubble_tail_base_width = %d,
				bubble_tail_position = %f
			WHERE bubble_id = %d
			",
			'wpPrepareValues' => $object->objectToColumnsForUpdate( true ),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	/*
	 * Function: getCMSUpdateArgs
	 * 
	 * This function is called by the Dabase object to get the 
	 * delete SQL for the object. 
	 */
	public function getCMSDeleteArgs() {
		return $this->deleteArgs;
	}

	/*
	 * Function: setCMSDeleteArgs
	 * 
	 * This function is called when doing an delete. The object to be
	 * delete creates its respective converter, and then calls 
	 * this method in the converter to store the delete args (which is basically
	 * just a string containing DML appropriate for the objects 
	 * respective storage table. 
	 */
	public function setCMSDeleteArgs( $object) {
		
		global $wpdb;
		
		self::setAlias();
		
		$this->deleteArgs = array(
			'wpPrepareFormat' => "bubble_id = %d",
			'wpPrepareValues' => array( $object->getBubbleID() ),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	
	/* 
	 * Function: setImageURLFromCMS
	 * 
	 * TODO: document. 
	 */
	public function setImageURLFromCMS( $object ) {
		$imageURL = WPResources::getImageURL( $object->getTargetImageID() );
		$object->setTargetImageURL( $imageURL );
	}
	
	/*
	 * Function setUID
	 * 
	 * TODO: wordpress-specific requirement for assiging authorhip
	 * when bubble is created. 
	 */
	
	public function setUID( $uid ) {
		$this->bubbleConfig->setBubbleID( $uid );
	}
	
	/*
	 * Function setUID
	 * 
	 * TODO: wordpress-specific requirement for assiging authorhip
	 * when bubble is created. 
	 */
	public function setAuthor( $author ) {
		$this->bubbleConfig->setBubbleAuthor( $author );
	}
}

?>

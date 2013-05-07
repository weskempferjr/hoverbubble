<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");


class BubbleConfigConverter implements CMSConverter {
	
	private $insertArgs ;
	private $deleteArgs;
	private $updateArgs;
	private $selectRowArgs;
	private $selectRowsArgs ;
	private $bubbleConfig ;
	
	
	public static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hoverbubbles)) {
			$wpdb->hoverbubbles = $wpdb->prefix . 'hoverbubbles';
		}	
	}
	
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
					PRIMARY KEY  bubble_id (bubble_id),
					UNIQUE KEY  bubble_name (bubble_name)
				) ;";
		
			return $sql;	
	}
	
	public static function tableExists() {
		
		global $wpdb ;
		$tablename = $wpdb->prefix . 'hoverbubbles';
		if( $wpdb->get_var( "SHOW TABLES LIKE '$tablename'" ) != $tablename ) { 
			return false ;
		}
		return true ;
	}
	
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
					bubble_duration int(11) DEFAULT '-1'
				) ;";
		
			return $sql;	
	}
	
	
	public function getCMSSelectRowArgs() {
		return $this->selectRowArgs ;
	}
	
	public function setCMSSelectRowArgs( $object ) {
		global $wpdb;
		
		self::setAlias();
		
		$this->selectRowArgs = array(
			'wpPrepareFormat' => "bubble_id",
			'wpPrepareValues' => $object->getBubbleID(),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	public function getCMSSelectRowsArgs() {
		return $this->selectRowsArgs ;
	}
	
	public function setCMSSelectRowsArgs( $whereClause ) {
		global $wpdb;
		
		self::setAlias();
		
		$this->selectRowsArgs = array(
			'wpPrepareFormat' => "*",
			'wpWhereClause' => $whereClause,
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	public function getCMSInsertArgs() {
		return $this->insertArgs;
	}
	
	public function setCMSInsertArgs( $object) {
		global $wpdb;

		self::setAlias();
		
		$this->bubbleConfig = $object ;
		
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
				bubble_duration
			) VALUES ( %d, %s, %s, %s, %d, %d, %s, %d, %s, %d, %d, %d, %d, %d, %s, %s, %d, %d )",
			'wpPrepareValues' => $object->objectToColumns( true ),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	public function getCMSUpdateArgs() {
		return $this->updateArgs;
	}
	
	public function setCMSUpdateArgs( $object) {
		
		global $wpdb;
		
		self::setAlias();
		
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
				bubble_duration = %d
			WHERE bubble_id = %d
			",
			'wpPrepareValues' => $object->objectToColumnsForUpdate( true ),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	public function getCMSDeleteArgs() {
		return $this->deleteArgs;
	}
	
	public function setCMSDeleteArgs( $object) {
		
		global $wpdb;
		
		self::setAlias();
		
		$this->deleteArgs = array(
			'wpPrepareFormat' => "bubble_id = %d",
			'wpPrepareValues' => array( $object->getBubbleID() ),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	public function setImageURLFromCMS( $object ) {
		$imageURL = WPResources::getImageURL( $object->getTargetImageID() );
		$object->setTargetImageURL( $imageURL );
	}
	
	public function setUID( $uid ) {
		$this->bubbleConfig->setBubbleID( $uid );
	}
}

?>

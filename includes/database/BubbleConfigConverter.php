<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");


class BubbleConfigConverter implements CMSConverter {
	
	private $insertArgs ;
	private $deleteArgs;
	private $updateArgs;
	private $selectRowArgs;
	private $selectRowsArgs ;
	
	
	private static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hoverbubbles)) {
			$wpdb->hoverbubbles = $wpdb->prefix . 'hoverbubbles';
		}	
	}
	public function getCMSSelectRowArgs() {
		return $this->selectRowArgs ;
	}
	
	public function setCMSSelectRowArgs( $object ) {
		global $wpdb;
		
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
		
		BubbleConfigConverter::setAlias();
		
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
				canvas_border_style,
				content_area_height,
				content_area_width,				
				target_image_id,
				target_image_url
			) VALUES ( %d, %s, %s, %s, %d, %d, %s, %d, %s, %d, %d, %s, %d, %d, %d, %s )",
			'wpPrepareValues' => $object->objectToColumns( true ),
			'wpTableName' => 'hoverbubbles'
		);
	}
	
	public function getCMSUpdateArgs() {
		return $this->updateArgs;
	}
	
	public function setCMSUpdateArgs( $object) {
		
		global $wpdb;
		
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
				canvas_border_style = %s,
				content_area_height = %d,
				content_area_width = %d,
				target_image_id = %d,
				target_image_url = %s
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
}

?>

<?php
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");


class BubblePageConverter implements CMSConverter {
	
	private $insertArgs ;
	private $deleteArgs;
	private $updateArgs;
	private $selectRowArgs;
	private $selectRowsArgs ;
	private $bubblePage ;
	
	
	private static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hbbubblepages)) {
			$wpdb->hbbubblepages = $wpdb->prefix . 'hbbubblepages';
		}	
	}
	public function getCMSSelectRowArgs() {
		return $this->selectRowArgs ;
	}
	
	public function setCMSSelectRowArgs( $object ) {
		global $wpdb;
		
		self::setAlias();
		
		$this->selectRowArgs = array(
			'wpPrepareFormat' => "page_candidate_id",
			'wpPrepareValues' => $object->getPageCandidateID(),
			'wpTableName' => 'hbbubblepages'
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
			'wpTableName' => 'hbbubblepages'
		);
	}
	
	public function getCMSInsertArgs() {
		return $this->insertArgs;
	}
	
	public function setCMSInsertArgs( $object) {
		global $wpdb;
		
		self::setAlias();
		
		$this->bubblePage = $object;
		
		$this->insertArgs = array(
			'wpPrepareFormat' => "(
				bubble_page_id,	
				page_candidate_id,
				bubble_id
			) VALUES ( %d, %d, %d )",
			'wpPrepareValues' => $object->objectToColumns( true ),
			'wpTableName' => 'hbbubblepages'
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
				page_candidate_id = %d,
				bubble_id = %d
			WHERE bubble_page_id = %d
			",
			'wpPrepareValues' => $object->objectToColumnsForUpdate( true ),
			'wpTableName' => 'hbbubblepages'
		);
	}
	
	public function getCMSDeleteArgs() {
		return $this->deleteArgs;
	}
	
	public function setCMSDeleteArgs( $object) {
		
		global $wpdb;
		
		self::setAlias();
		
		$this->deleteArgs = array(
			'wpPrepareFormat' => "bubble_page_id = %d",
			'wpPrepareValues' => array( $object->getBubblePageID() ),
			'wpTableName' => 'hbbubblepages'
		);
	}

	public function setUID( $uid ) {
		$this->bubblePage->setBubblePageID( $uid );
	}
	// TODO: delete this if it turns out is not needed. For now, comment.
	// public function setImageURLFromCMS( $object ) {
	//	$imageURL = WPResources::getImageURL( $object->getTargetImageID() );
	//	$object->setTargetImageURL( $imageURL );
	// }
}

?>
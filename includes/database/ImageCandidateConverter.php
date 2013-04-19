<?php
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");


class ImageCandidateConverter implements CMSConverter {
	
	private $insertArgs ;
	private $deleteArgs;
	private $updateArgs;
	private $selectRowArgs;
	private $selectRowsArgs ;
	private $imageCandidate ;
	
	
	private static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hbimagecandidates)) {
			$wpdb->hbimagecandidates = $wpdb->prefix . 'hbimagecandidates';
		}	
	}
	public function getCMSSelectRowArgs() {
		return $this->selectRowArgs ;
	}
	
	public function setCMSSelectRowArgs( $object ) {
		global $wpdb;
		
		self::setAlias();
		
		$this->selectRowArgs = array(
			'wpPrepareFormat' => "image_candidate_id",
			'wpPrepareValues' => $object->getImageCandidateID(),
			'wpTableName' => 'hbimagecandidates'
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
			'wpTableName' => 'hbimagecandidates'
		);
	}
	
	public function getCMSInsertArgs() {
		return $this->insertArgs;
	}
	
	public function setCMSInsertArgs( $object) {
		global $wpdb;
		
		self::setAlias();
		
		$this->imageCandidate = $object ;
		
		$this->insertArgs = array(
			'wpPrepareFormat' => "(	
				image_candidate_id,
				target_image_url
			) VALUES ( %d, %s )",
			'wpPrepareValues' => $object->objectToColumns( true ),
			'wpTableName' => 'hbimagecandidates'
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
				target_image_url = %s
			WHERE image_candidate_id = %d
			",
			'wpPrepareValues' => $object->objectToColumnsForUpdate( true ),
			'wpTableName' => 'hbimagecandidates'
		);
	}
	
	public function getCMSDeleteArgs() {
		return $this->deleteArgs;
	}
	
	public function setCMSDeleteArgs( $object) {
		
		global $wpdb;
		
		self::setAlias();
		
		$this->deleteArgs = array(
			'wpPrepareFormat' => "image_candidate_id = %d",
			'wpPrepareValues' => array( $object->getImageCandidateID() ),
			'wpTableName' => 'hbimagecandidates'
		);
	}
	
	public function setUID( $uid ) {
		$this->imageCandidate->setImageCandidateID( $uid );
	}

	// TODO: delete this if it turns out is not needed. For now, comment.
	// public function setImageURLFromCMS( $object ) {
	//	$imageURL = WPResources::getImageURL( $object->getTargetImageID() );
	//	$object->setTargetImageURL( $imageURL );
	// }
}

?>

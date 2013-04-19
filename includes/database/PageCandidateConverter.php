<?php
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");


class PageCandidateConverter implements CMSConverter {
	
	private $insertArgs ;
	private $deleteArgs;
	private $updateArgs;
	private $selectRowArgs;
	private $selectRowsArgs ;
	private $pageCandidate ;
	
	
	private static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hbpagecandidates)) {
			$wpdb->hbpagecandidates = $wpdb->prefix . 'hbpagecandidates';
		}	
	}
	public function getCMSSelectRowArgs() {
		return $this->selectRowArgs ;
	}
	
	public function setCMSSelectRowArgs( $object ) {
		global $wpdb;
		
		$this->selectRowArgs = array(
			'wpPrepareFormat' => "page_candidate_id",
			'wpPrepareValues' => $object->getPageCandidateID(),
			'wpTableName' => 'hbpagecandidates'
		);
	}
	
	public function getCMSSelectRowsArgs() {
		return $this->selectRowsArgs ;
	}
	
	public function setCMSSelectRowsArgs( $whereClause ) {
		global $wpdb;
		
		PageCandidateConverter::setAlias();
		
		$this->selectRowsArgs = array(
			'wpPrepareFormat' => "*",
			'wpWhereClause' => $whereClause,
			'wpTableName' => 'hbpagecandidates'
		);
	}
	
	public function getCMSInsertArgs() {
		return $this->insertArgs;
	}
	
	public function setCMSInsertArgs( $object) {
		global $wpdb;
		
		$this->pageCandidate = $object;
		
		$this->insertArgs = array(
			'wpPrepareFormat' => "(	
				page_candidate_id,
				image_candidate_id,
				target_page_url,
				display_bubble
			) VALUES ( %d, %d, %s, %d )",
			'wpPrepareValues' => $object->objectToColumns( true ),
			'wpTableName' => 'hbpagecandidates'
		);
	}
	
	public function getCMSUpdateArgs() {
		return $this->updateArgs;
	}
	
	public function setCMSUpdateArgs( $object) {
		
		global $wpdb;
		
		$this->updateArgs = array( 
			'wpPrepareFormat' => "
				image_candidate_id = %d,
				target_page_url = %s,
				display_bubble = %d
			WHERE page_candidate_id = %d
			",
			'wpPrepareValues' => $object->objectToColumnsForUpdate( true ),
			'wpTableName' => 'hbpagecandidates'
		);
	}
	
	public function getCMSDeleteArgs() {
		return $this->deleteArgs;
	}
	
	public function setCMSDeleteArgs( $object) {
		
		global $wpdb;
		
		$this->deleteArgs = array(
			'wpPrepareFormat' => "page_candidate_id = %d",
			'wpPrepareValues' => array( $object->getPageCandidateID() ),
			'wpTableName' => 'hbpagecandidates'
		);
	}

	public function setUID( $uid ) {
		$this->pageCandidate->setPageCandidateID( $uid );
	}
	// TODO: delete this if it turns out is not needed. For now, comment.
	// public function setImageURLFromCMS( $object ) {
	//	$imageURL = WPResources::getImageURL( $object->getTargetImageID() );
	//	$object->setTargetImageURL( $imageURL );
	// }
}

?>

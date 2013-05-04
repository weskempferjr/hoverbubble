<?php
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");


class PageCandidateConverter implements CMSConverter {
	
	private $insertArgs ;
	private $deleteArgs;
	private $updateArgs;
	private $selectRowArgs;
	private $selectRowsArgs ;
	private $pageCandidate ;
	
	
	public static function setAlias() {
		global $wpdb ;
		if (!isset($wpdb->hbpagecandidates)) {
			$wpdb->hbpagecandidates = $wpdb->prefix . 'hbpagecandidates';
		}	
	}
	
	public static final function generateDDL() {
		
		global $wpdb ;
		
		self::setAlias();
		
       $sql =  "CREATE TABLE " . $wpdb->prefix . 'hbpagecandidates' . " ( 
			page_candidate_id int(11) NOT NULL AUTO_INCREMENT,
			image_candidate_id int(11) NOT NULL DEFAULT '0',
			target_page_url varchar(1000) DEFAULT NULL,
			display_bubble tinyint(3) NOT NULL DEFAULT '0',
			PRIMARY KEY  page_candidate_id (page_candidate_id),
			KEY  image_candidate_id (image_candidate_id),
			CONSTRAINT wp_hbpagecandidates_ibfk_1 FOREIGN KEY (image_candidate_id) REFERENCES wp_hbimagecandidates (image_candidate_id) ON DELETE CASCADE ON UPDATE CASCADE 
		);";
		return $sql;	
	}
	
	public static function tableExists() {
		
		global $wpdb ;
		$tablename = $wpdb->prefix . 'hbpagecandidates';
		if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename ) { 
			return false ;
		}
		return true ;
	}
	public static final function generateUpgradeDDL() {
		
		global $wpdb ;
		
		self::setAlias();
		
       $sql =  "CREATE TABLE " . $wpdb->prefix . 'hbpagecandidates' . " ( 
			page_candidate_id int(11) NOT NULL AUTO_INCREMENT,
			image_candidate_id int(11) NOT NULL DEFAULT '0',
			display_bubble tinyint(3) NOT NULL DEFAULT '0',
			target_page_url varchar(1000) DEFAULT NULL
		);";
		return $sql;	
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

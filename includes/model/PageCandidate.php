<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DBMap.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverterFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DatabaseFactory.php");

class PageCandidate implements DBMap {
	
	private static $databaseConnector = null;
	private static $tableName = "hbpagecandidates";
	
	private $pageCandidateID = 0;
	private $targetPageURL = "";
	private $imageCandidateID = 0;
	private $displayBubble = false;
	
	private $whereClause = "";
	
	
	public function restore( $pageCandidateID ) {
		$converter = CMSConverterFactory::getPageCandidateConverter();
		$this->setPageCandidateID( $pageCandidateID );
		$converter->setCMSSelectRowArgs($this);
		$database = PageCandidate::getDatabase();
		$columnValues = $database->getRow( $converter );
		$this->columnsToObject( $columnValues, true );
	}
	
	public static function delete( $pageCandidateID ) {
		
		$converter = CMSConverterFactory::getPageCandidateConverter();
		$pageCandidate = new PageCandidate();
		$pageCandidate->setPageCandidateID( $pageCandidateID );
		$converter->setCMSDeleteArgs($pageCandidate);
		$database = PageCandidate::getDatabase();
		$database->deleteRow( $converter );

	}
	
	public static function retrievePageCandidates( $whereClause ) {
		$converter = CMSConverterFactory::getPageCandidateConverter();
		$converter->setCMSSelectRowsArgs($whereClause);
		$database = PageCandidate::getDatabase();
		$pageCandidateColumnValueArray = $database->getRows( $converter );

		$pageCandidates = array();
		
		foreach ( $pageCandidateColumnValueArray as $pageCandidateColumnValues ) {
			$pageCandidate = new PageCandidate();
			$pageCandidate->columnsToObject(  $pageCandidateColumnValues, true );
			array_push( $pageCandidates, $pageCandidate );
		}
		return $pageCandidates ;
		
	}
	
	public function update() {
		$converter = CMSConverterFactory::getPageCandidateConverter();
		$converter->setCMSUpdateArgs($this);
		$database = PageCandidate::getDatabase();
		$database->updateRows( $converter );
	}
	
	public function insert() {
		$converter = CMSConverterFactory::getPageCandidateConverter();
		$converter->setCMSInsertArgs($this);
		$database = PageCandidate::getDatabase();
		$database->insertRow( $converter );
	}
		
	public function setPageCandidateID ( $pageCandidateID ) {
		$this->pageCandidateID = $pageCandidateID;
	}
	
	public function getPageCandidateID() {
		return $this->pageCandidateID;
	}	
	
	public function setImageCandidateID ( $imageCandidateID ) {
		$this->imageCandidateID = $imageCandidateID;
	}
	
	public function getImageCandidateID() {
		return $this->imageCandidateID;
	}
	
	public function setTargetPageURL( $pageURL ) {
		$this->targetPageURL = rtrim( $pageURL, "/");
	}
	
	public function getTargetPageURL() {
		return $this->targetPageURL;
	}
	
	public function setDisplayBubble( $displayBubble ) {
		$this->displayBubble = $displayBubble;
	}
	
	public function getDisplayBubble() {
		return $this->displayBubble;
	}
	
	public function setWhereClause ( $whereClause ) {
		$this->whereClause = $whereClause ;
	}
	
	public function getWhereClause() {
		return $this->whereClause ;
	}
	
	public function columnsToObject( $columnValues, $dbread ) {		

		$this->pageCandidateID = $columnValues['page_candidate_id'];
		$this->targetPageURL = $columnValues['target_page_url'];
		$this->imageCandidateID = $columnValues['image_candidate_id'];
		$this->displayBubble = $columnValues['display_bubble'];
	}
	
	public function objectToColumns( $dbwrite ) {
		
		$columnValues = array (
			'page_candidate_id' => $this->pageCandidateID,	
			'image_candidate_id' => $this->imageCandidateID,			
			'target_page_url' => $this->targetPageURL,
			'display_bubble' => $this->displayBubble
		);
		return $columnValues ;
		
	}
	
	// TODO: Come up with another way to handle the prepare values for 
	// update versus insert. Put conditional code perhaps in objectToColumns. 
	public function objectToColumnsForUpdate( $dbwrite ) {
		$columnValues = array (
			'page_candidate_id' => $this->pageCandidateID,	
			'image_candidate_id' => $this->imageCandidateID,			
			'target_image_url' => $this->targetImageURL,
			'display_bubble' => $this->displayBubble
		);
		return $columnValues ;
		
	}
	

	
	
	private static function getDatabase() {
		if ( self::$databaseConnector == null ) {
			self::$databaseConnector = DatabaseFactory::getDatabase();
		}
		return self::$databaseConnector ;
	}
	
}
?>
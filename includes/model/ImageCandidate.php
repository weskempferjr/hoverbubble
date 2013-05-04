<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DBMap.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverterFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DatabaseFactory.php");

class ImageCandidate implements DBMap {
	
	private static $databaseConnector = null;
	private static $tableName = "hbimagecandidates";
	
	private $imageCandidateID = 0;
	private $targetImageURL = "";
	
	private $whereClause = "";
	
	public function restore( $imageCandidateID ) {
		$converter = CMSConverterFactory::getImageCandidateConverter();
		$this->setImageCandidateID( $imageCandidateID );
		$converter->setCMSSelectRowArgs($this);
		$database = ImageCandidate::getDatabase();
		$columnValues = $database->getRow( $converter );
		$this->columnsToObject( $columnValues, true );
	}
	
	public static function delete( $imageCandidateID ) {
		
		$converter = CMSConverterFactory::getImageCandidateConverter();
		$imageCandidate = new ImageCandidate();
		$imageCandidate->setImageCandidateID( $imageCandidateID );
		$converter->setCMSDeleteArgs($imageCandidate);
		$database = ImageCandidate::getDatabase();
		$database->deleteRow( $converter );

	}
	
	public static function retrieveImageCandidates( $whereClause ) {
		$converter = CMSConverterFactory::getImageCandidateConverter();
		$converter->setCMSSelectRowsArgs($whereClause);
		$database = ImageCandidate::getDatabase();
		$imageCandidateColumnValueArray = $database->getRows( $converter );

		$imageCandidates = array();
		
		foreach ( $imageCandidateColumnValueArray as $imageCandidateColumnValues ) {
			$imageCandidate = new ImageCandidate();
			$imageCandidate->columnsToObject( $imageCandidateColumnValues, true );
			array_push( $imageCandidates, $imageCandidate );
		}
		return $imageCandidates ;
		
	}
	
	public function update() {
		$converter = CMSConverterFactory::getImageCandidateConverter();
		$converter->setCMSUpdateArgs($this);
		$database = ImageCandidate::getDatabase();
		$database->updateRows( $converter );
	}
	
	public function insert() {
		$converter = CMSConverterFactory::getImageCandidateConverter();
		$converter->setCMSInsertArgs($this);
		$database = ImageCandidate::getDatabase();
		$database->insertRow( $converter );
	}
		
	
	public function setImageCandidateID ( $imageCandidateID ) {
		$this->imageCandidateID = $imageCandidateID;
	}
	
	public function getImageCandidateID() {
		return $this->imageCandidateID;
	}
	
	public function setTargetImageURL( $imageURL ) {
		$this->targetImageURL = $imageURL;
	}
	
	public function getTargetImageURL() {
		return $this->targetImageURL;
	}
	
	
	public function setWhereClause ( $whereClause ) {
		$this->whereClause = $whereClause ;
	}
	
	public function getWhereClause() {
		return $this->whereClause ;
	}
	
	public function columnsToObject( $columnValues, $dbread ) {		
		$this->imageCandidateID = $columnValues['image_candidate_id'];
		$this->targetImageURL = $columnValues['target_image_url'];
	}
	
	public function objectToColumns( $dbwrite ) {

		
		$columnValues = array (
			'image_candidate_id' => $this->imageCandidateID,			
			'target_image_url' => $this->targetImageURL	
		);
		return $columnValues ;
		
	}
	
	// TODO: Come up with another way to handle the prepare values for 
	// update versus insert. Put conditional code perhaps in objectToColumns. 
	public function objectToColumnsForUpdate( $dbwrite ) {
		$columnValues = array (
			'image_candidate_id' => $this->imageCandidateID,			
			'target_image_url' => $this->targetImageURL	
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
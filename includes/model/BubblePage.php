<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DBMap.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverterFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DatabaseFactory.php");

class BubblePage implements DBMap {
	
	private static $databaseConnector = null;
	private $tableName = "hbbubblepages";
	
	private $bubblePageID = 0;
	private $pageCandidateID = 0;
	private $bubbleID = 0;
	
	private $whereClause = "";
	
	
	public function restore( $bubblePageID ) {
		$converter = CMSConverterFactory::getBubblePageConverter();
		$this->setBubblePageID( $bubblePageID );
		$converter->setCMSSelectRowArgs($this);
		$database = self::getDatabase();
		$columnValues = $database->getRow( $converter );
		$this->columnsToObject( $columnValues, true );
	}
	
	public static function delete( $bubblePageID ) {
		
		$converter = CMSConverterFactory::getBubblePageConverter();
		$bubblePage = new BubblePage();
		$bubblePage->setBubblePageID( $bubblePageID );
		$converter->setCMSDeleteArgs( $bubblePage );
		$database = self::getDatabase();
		$database->deleteRow( $converter );

	}
	
	public static function retrieveBubblePages( $whereClause ) {
		$converter = CMSConverterFactory::getBubblePageConverter();
		$converter->setCMSSelectRowsArgs($whereClause);
		$database = self::getDatabase();
		$bubblePageColumnValueArray = $database->getRows( $converter );

		$bubblePages = array();
		
		foreach ( $bubblePageColumnValueArray as $bubblePageColumnValues ) {
			$bubblePage = new BubblePage();
			$bubblePage->columnsToObject(  $bubblePageColumnValues, true );
			array_push( $bubblePages, $bubblePage);
		}
		return $bubblePages ;
		
	}
	
	public function update() {
		$converter = CMSConverterFactory::getBubblePageConverter();
		$converter->setCMSUpdateArgs($this);
		$database = self::getDatabase();
		$database->updateRows( $converter );
	}
	
	public function insert() {
		$converter = CMSConverterFactory::getBubblePageConverter();
		$converter->setCMSInsertArgs($this);
		$database = self::getDatabase();
		$database->insertRow( $converter );
	}

	public function setBubblePageID ( $bubblePageID ) {
		$this->bubblePageID = $bubblePageID ;
	}
	
	public function getBubblePageID() {
		return $this->bubblePageID ;
	}
	
	public function setPageCandidateID ( $pageCandidateID ) {
		$this->pageCandidateID = $pageCandidateID;
	}
	
	public function getPageCandidateID() {
		return $this->pageCandidateID;
	}	
	

	public function setBubbleID( $id ) {
		$this->bubbleID = $id;
	}
	
	public function getBubbleID() {
		return $this->bubbleID ;
	}
	
	public function setWhereClause ( $whereClause ) {
		$this->whereClause = $whereClause ;
	}
	
	public function getWhereClause() {
		return $this->whereClause ;
	}
	
	public function columnsToObject( $columnValues, $dbread ) {		

		$this->bubblePageID = $columnValues['bubble_page_id'];
		$this->pageCandidateID = $columnValues['page_candidate_id'];
		$this->bubbleID = $columnValues['bubble_id'];
	}
	
	public function objectToColumns( $dbwrite ) {
		
		$columnValues = array (
			'bubble_page_id' => $this->bubblePageID,
			'page_candidate_id' => $this->pageCandidateID,
			'bubble_id' => $this->bubbleID		
		);
		return $columnValues ;
		
	}
	
	// TODO: Come up with another way to handle the prepare values for 
	// update versus insert. Put conditional code perhaps in objectToColumns. 
	public function objectToColumnsForUpdate( $dbwrite ) {
		
		$columnValues = array (
			'bubble_page_id' => $this->bubblePageID,
			'page_candidate_id' => $this->pageCandidateID,
			'bubble_id' => $this->bubbleID		
		);
		return $columnValues ;
		
	}
	
	public static final function generateDDL() {
		$sql =  "CREATE TABLE IF NOT EXISTS ". $hbtable . " (
		 	bubble_page_id int(11) unsigned NOT NULL AUTO_INCREMENT,
 		 	bubble_id` mediumint(12) NOT NULL,
 		 	page_candidate_id int(11) NOT NULL,
 			PRIMARY KEY (bubble_page_id),
  			KEY page_candidate_id (page_candidate_id),
  			KEY bubble_id (bubble_id),
  			CONSTRAINT wp_hbbubblepages_ibfk_2 FOREIGN KEY (bubble_id) REFERENCES `wp_hoverbubbles` (`bubble_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  			CONSTRAINT wp_hbbubblepages_ibfk_1 FOREIGN KEY `page_candidate_id) REFERENCES wp_hbpagecandidates (page_candidate_id) ON DELETE CASCADE ON UPDATE CASCADE";
		return $sql;	
	}
	
	public static final function getTableName(){
		return $this->tableName;
	}
	
	
	private static function getDatabase() {
		if ( self::$databaseConnector == null ) {
			self::$databaseConnector = DatabaseFactory::getDatabase();
		}
		return self::$databaseConnector ;
	}
	
}
?>
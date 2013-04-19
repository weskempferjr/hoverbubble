<?php



require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DBMap.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/CMSConverterFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DatabaseFactory.php");





class BubbleConfig implements DBMap {
	
	private static $databaseConnector = null;
	// private static $databaseConnector = DatabaseFactory::getDatabase();
	private $tableName = "hoverbubbles";
	private $bubbleID = 0;
	private $bubbleName = "";
	private $bubbleMessage = "";
	private $bubbleTailLength = 0;	
	private $bubbleCornerRadius = 0;	
	private $bubbleOutlineColor = "";
	private $bubbleFillColor = "";
	private $bubbleOutlineWidth = 0;
	private $bubbleTailDirection = "";
	private $targetImageID = 0 ;
	private $targetImageContainerID = "";
	private $bubbleCanvasID = "";
	private $contentDivID = "";
	private $embedID = "";
	private $bubbleTailX = 0;
	private $bubbleTailY = 0;
	private $contentAreaHeight = 0;
	private $contentAreaWidth = 0;
	private $canvasBorderStyle = "";
	private $targetImageURL = "";
	
	private $whereClause = "";
	
	public function __construct() {
		$this->bubbleName =  "";
		$this->bubbleMessage = "";
		$this->bubbleFillColor = "white";
		$this->bubbleTailLength = 30;
		$this->bubbleTailDirection = "";
		$this->bubbleCornerRadius = 15 ;
		$this->bubbleOutlineColor = "black";
		$this->bubbleTailDirection = "S";
		$this->bubbleOutlineWidth = 4;
		$this->bubbleTailX = 0;
		$this->bubbleTailY = 0;
		$this->contentAreaWidth = 150;
		$this->contentAreaHeight = 150;
		$this->canvasBorderStyle =  "0px solid #000000";
		$this->targetImageID = "" ;

	}
	
	public function restore( $bubbleID ) {
		$converter = CMSConverterFactory::getBubbleConfigConverter();
		$this->setBubbleID( $bubbleID );
		$converter->setCMSSelectRowArgs($this);
		$database = BubbleConfig::getDatabase();
		$columnValues = $database->getRow( $converter );
		$this->columnsToObject( $columnValues, true );
	}
	
	public static function delete( $bubbleID ) {
		
		$converter = CMSConverterFactory::getBubbleConfigConverter();
		$bubble = new BubbleConfig();
		$bubble->setBubbleID( $bubbleID );
		$converter->setCMSDeleteArgs($bubble);
		$database = BubbleConfig::getDatabase();
		$database->deleteRow( $converter );

	}
	
	public static function retrieveBubbles( $whereClause ) {
		$converter = CMSConverterFactory::getBubbleConfigConverter();
		$converter->setCMSSelectRowsArgs($whereClause);
		$database = BubbleConfig::getDatabase();
		$bubbleColumnValueArray = $database->getRows( $converter );

		$bubbleConfigs = array();
		
		foreach ( $bubbleColumnValueArray as $bubbleColumnValues ) {
			$bubbleConfig = new BubbleConfig();
			$bubbleConfig->columnsToObject( $bubbleColumnValues, true );
			array_push( $bubbleConfigs, $bubbleConfig );
		}
		return $bubbleConfigs ;
		
	}
	
	public function update() {
		$converter = CMSConverterFactory::getBubbleConfigConverter();
		// $converter->setImageURLFromCMS($this);		
		$converter->setCMSUpdateArgs($this);
		$database = BubbleConfig::getDatabase();
		$database->updateRows( $converter );
	}
	
	public function insert() {
		$converter = CMSConverterFactory::getBubbleConfigConverter();
		// $converter->setImageURLFromCMS($this);
		$converter->setCMSInsertArgs($this);
		$database = BubbleConfig::getDatabase();
		$database->insertRow( $converter );
	}
	
	public function setBubbleID( $id ) {
		$this->bubbleID = $id;
	}
	
	public function getBubbleID() {
		return $this->bubbleID ;
	}
	
	
	public function setBubbleName( $name) {
		$this->bubbleName = $name;
	}
	
	public function getBubbleName() {
		return $this->bubbleName ;
	}
			
	public function setBubbleMessage( $message) {
		$this->bubbleMessage = $message;
	}
	
	public function getBubbleMessage() {
		return $this->bubbleMessage ;
	}
	
	public function setBubbleTailLength( $length ) {
		$this->bubbleTailLength = $length;
	}
	
	public function getBubbleTailLength() {
		return $this->bubbleTailLength ;
	}
	
	public function setBubbleCornerRadius( $radius ) {
		$this->bubbleCornerRadius = $radius;
	}
	
	public function getBubbleCornerRadius() {
		return $this->bubbleCornerRadius;
	}
	
	public function setBubbleOutlineColor( $color ) {
		$this->bubbleOutlineColor = $color;
	}
	
	public function getBubbleOutlineColor() {
		return $this->bubbleOutlineColor;
	}
	
	public function setBubbleFillColor( $color ) {
		$this->bubbleFillColor = $color;
	}
	
	public function getBubbleFillColor() {
		return $this->bubbleFillColor;
	}
	
	public function setBubbleOutlineWidth( $width ) {
		$this->bubbleOutlineWidth = $width;
	}
	
	public function getBubbleOutlineWidth() {
		return $this->bubbleOutlineWidth;
	}
	
	public function setBubbleTailDirection( $direction ) {
		$this->bubbleTailDirection = $direction;
	}
	
	public function getBubbleTailDirection() {
		return $this->bubbleTailDirection;
	}
	
	public function setTargetImageID( $imageID ) {
		$this->targetImageID = $imageID;
	}
	
	public function getTargetImageID() {
		return $this->targetImageID;
	}
	
	public function setTargetImageContainerID( $imageContainerID ) {
		$this->targetImageContainerID = $imageContainerID;
	}
	
	public function getTargetImageContainerID() {
		return $this->targetImageContainerID;
	}
	
	public function setBubbleCanvasID( $id ) {
		$this->bubbleCanvasID = $id;
	}
	
	public function getBubbleCanvasID() {
		return $this->bubbleCanvasID;
	}

	public function setContentDivID( $divID ) {
		$this->contentDivID = $divID;
	}
	
	public function getContentDivID() {
		return $this->contentDivID;
	}
	
	public function setEmbedID( $embedID ) {
		$this->embedID = $embedID;
	}
	
	public function getEmbedID() {
		return $this->embedID;
	}

	public function setBubbleTailX( $xcoord ) {
		$this->bubbleTailX = $xcoord;
	}
	
	public function getBubbleTailX() {
		return $this->bubbleTailX;
	}	
	
	public function setBubbleTailY( $ycoord ) {
		$this->bubbleTailX = $ycoord;
	}
	
	public function getBubbleTailY() {
		return $this->bubbleTailY;
	}

	public function setContentAreaWidth( $width ) {
		$this->contentAreaWidth = $width;
	}
	
	public function getContentAreaWidth() {
		return $this->contentAreaWidth;
	}	
	
	public function setContentAreaHeight( $height ) {
		$this->contentAreaHeight = $height;
	}
	
	public function getContentAreaHeight() {
		return $this->contentAreaHeight;
	}

	public function setCanvasBorderStyle( $style ) {
		$this->canvasBorderStyle = $style;
	}
	
	public function getCanvasBorderStyle() {
		return $this->canvasBorderStyle;
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
		
		$bubble_message = "";
		if ( $dbread == true ) {
			$bubble_message = base64_decode ( $columnValues['bubble_message'] );
		}
		else {
			$bubble_message =  stripslashes( $columnValues['bubble_message']);
		}
		
		$this->bubbleFillColor = $columnValues['bubble_fill_color'];
		$this->bubbleName = $columnValues['bubble_name'];
		$this->bubbleMessage = $bubble_message ;
		$this->bubbleID = $columnValues['bubble_id'];
		$this->bubbleTailLength = $columnValues['bubble_tail_length'];
		$this->bubbleCornerRadius = $columnValues['bubble_corner_radius'];
		$this->bubbleOutlineColor = $columnValues['bubble_outline_color'];
		$this->bubbleOutlineWidth = $columnValues['bubble_outline_width'];
		$this->bubbleTailDirection = $columnValues['bubble_tail_direction'];
		// $this->targetImageID = $columnValues['target_image_id'];
		$this->targetImageContainerID = $this->bubbleNameToContainerID($columnValues['bubble_name'] );
		$this->bubbleCanvasID = "bubblecanvas" . $columnValues['bubble_id'] ;
		$this->contentDivID = "hbcontentdiv" . $columnValues['bubble_id'];
		$this->embedID = "hbembeddiv" . $columnValues['bubble_id'];
		$this->bubbleTailX = $columnValues['bubble_tail_x'];
		$this->bubbleTailY = $columnValues['bubble_tail_y'];
		$this->contentAreaHeight = $columnValues['content_area_height'];
		$this->contentAreaWidth = $columnValues['content_area_width'];
		$this->canvasBorderStyle = $columnValues['canvas_border_style'];
		$this->targetImageURL = $columnValues['target_image_url'];
	}
	
	public function objectToColumns( $dbwrite ) {
		$bubble_message = "";
		if ( $dbwrite == true ) {
			$bubble_message = base64_encode($this->bubbleMessage) ;
		}
		else {
			$bubble_message = $this->bubbleMessage;
		}
		
		$columnValues = array (
			'bubble_id' => $this->bubbleID,			
			'bubble_name' => $this->bubbleName,
			'bubble_message' => $bubble_message,
			'bubble_fill_color' => $this->bubbleFillColor,
			'bubble_tail_length' => $this->bubbleTailLength,
			'bubble_corner_radius' => $this->bubbleCornerRadius,
			'bubble_outline_color' => $this->bubbleOutlineColor,
			'bubble_outline_width' => $this->bubbleOutlineWidth,
			'bubble_tail_direction' => $this->bubbleTailDirection,
			'bubble_tail_x' => $this->bubbleTailX,
			'bubble_tail_y' => $this->bubbleTailY,
			'canvas_border_style' => $this->canvasBorderStyle,
			'content_area_height' => $this->contentAreaHeight,
			'content_area_width' => $this->contentAreaWidth,
			'target_image_id' => $this->targetImageID,
			'target_image_url' => $this->targetImageURL	
		);
		return $columnValues ;
		
	}
	
	// TODO: Come up with another way to handle the prepare values for 
	// update verse insert. Put conditional code perhaps in objectToColumns. 
	public function objectToColumnsForUpdate( $dbwrite ) {
		$bubble_message = "";
		if ( $dbwrite == true ) {
			$bubble_message = base64_encode($this->bubbleMessage) ;
		}
		else {
			$bubble_message = $this->bubbleMessage;
		}
		
		$columnValues = array (		
			'bubble_name' => $this->bubbleName,
			'bubble_message' => $bubble_message,
			'bubble_fill_color' => $this->bubbleFillColor,
			'bubble_tail_length' => $this->bubbleTailLength,
			'bubble_corner_radius' => $this->bubbleCornerRadius,
			'bubble_outline_color' => $this->bubbleOutlineColor,
			'bubble_outline_width' => $this->bubbleOutlineWidth,
			'bubble_tail_direction' => $this->bubbleTailDirection,
			'bubble_tail_x' => $this->bubbleTailX,
			'bubble_tail_y' => $this->bubbleTailY,
			'canvas_border_style' => $this->canvasBorderStyle,
			'content_area_height' => $this->contentAreaHeight,
			'content_area_width' => $this->contentAreaWidth,
			'target_image_id' => $this->targetImageID,
			'target_image_url' => $this->targetImageURL,
			'bubble_id' => $this->bubbleID,		
		);
		return $columnValues ;
		
	}
	public static final function generateDDL() {
		$sql =  "CREATE TABLE ". $hbtable . " (		
			bubble_id mediumint(12) NOT NULL AUTO_INCREMENT,
			bubble_name varchar(50) DEFAULT NULL,
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
			UNIQUE KEY bubble_id (bubble_id),
			UNIQUE KEY `bubble_name` (`bubble_name`);";
		return $sql;	
	}
	
	public static final function getTableName(){
		return $this->tableName;
	}
	
	private function bubbleNameToContainerID ( $name ) {
		$cid = strtolower($name);
		$cid = str_replace(' ','-', $cid) . "div";
		return $cid ;
	}
	
	private static function getDatabase() {
		if ( BubbleConfig::$databaseConnector == null ) {
			BubbleConfig::$databaseConnector = DatabaseFactory::getDatabase();
		}
		return BubbleConfig::$databaseConnector ;
	}
}
?>
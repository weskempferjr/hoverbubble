<?php
/**
 * database related functions
 **/


/**
 * Set up alias for bubble table access.
 */
global $wpdb ;
if (!isset($wpdb->hoverbubbles)) {
	$wpdb->hoverbubbles = $wpdb->prefix . 'hoverbubbles';
}


/**
 * Retrieve bubble configurations from database.  
 * TODO: modify to target page, or image title. the_image_div is hardcoded.
 */
function tnotw_get_bubble_configs(){
	global $wpdb ;
	
	// The client sends a list image source URLs (those images 
	// that appear on the curent page). Select only thoses hb entries
	// that are associated with on more of those images. 
	$image_list = $_REQUEST['imageInfoData'];
	$where_clause = "target_image_url IN (";
	
	$img_count = count( $image_list );
	for ( $i = 0 ;  $i < $img_count ; $i++ ) {
		$where_clause .= '"' . $image_list[$i] . '"';
		if ( $i == ($img_count - 1)) {
			$where_clause .= ')';
		} 
		else {
			$where_clause .= ',';
		}
		
	}
	
	$bubble_configs = $wpdb->get_results(
		"
		SELECT * FROM $wpdb->hoverbubbles WHERE $where_clause 
		",
		ARRAY_A
	);

	$configArray = array();
	foreach ( $bubble_configs as $bubble_config ) {
		$mapped_config = map_config( $bubble_config );
		array_push( $configArray, $mapped_config);
	}
	return $configArray ;	
	
}

/**
 * Map database columns to variable names as they are known in the UI.Note
 * some config fields are dirived or composed of more than datanbase one field. 
 */
function map_config( $bubbleConfig ) {
	$mappedConfig = array(	'bubbleFillColor' => $bubbleConfig['bubble_fill_color'],
				'bubbleName' => $bubbleConfig['bubble_name'],
				'bubbleMessage' => base64_decode($bubbleConfig['bubble_message']),
				'bubbleID' => $bubbleConfig['bubble_id'],
				'bubbleTailLength' => $bubbleConfig['bubble_tail_length'],
				'bubbleCornerRadius' => $bubbleConfig['bubble_corner_radius'],
				'bubbleOutlineColor' => $bubbleConfig['bubble_outline_color'],
				'bubbleOutlineWidth' => $bubbleConfig['bubble_outline_width'],
				'bubbleTailDirection' => $bubbleConfig['bubble_tail_direction'],
				'targetImageID' => $bubbleConfig['target_image_id'],
				'targetImageContainerID' => bubble_name_to_container_id($bubbleConfig['bubble_name'] ),
				'bubbleCanvasID' => "bubblecanvas" . $bubbleConfig['bubble_id'] ,
				'contentDivID' => "hbcontentdiv" . $bubbleConfig['bubble_id'],
				'embedID' => "hbembeddiv" . $bubbleConfig['bubble_id'],
				'bubbleTailX' => $bubbleConfig['bubble_tail_x'],
				'bubbleTailY' => $bubbleConfig['bubble_tail_y'],
				'contentAreaHeight' => $bubbleConfig['content_area_height'],
				'contentAreaWidth' => $bubbleConfig['content_area_width'],
				'canvasBorderStyle' => $bubbleConfig['canvas_border_style'],
				'targetImageURL' => $bubbleConfig['target_image_url']	
	);
	return $mappedConfig ;
;	

} 

function bubble_name_to_container_ID ( $name ) {
	$cid = strtolower($name);
	$cid = str_replace(' ','-', $cid) . "div";
	return $cid ;
}
 
function tnotw_create_bubble_tables() {

	global $wpdb;
	$hbtable = $wpdb->prefix . "hoverbubbles";
 
	//verify if table already exists
	if($wpdb->get_var("SHOW TABLES LIKK '$hbtable'") !== $hbtable ) {
		$sql = get_bubble_table_DDL() ;
	}
 
	//include the wordpress db functions
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);

	update_option(TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY, TNOTW_HOVERBUBBLE_VERSION);
 
}


/**
 * Update tables if necesary on upgrade. Version option entry is updated also.
 */
function tnotw_check_update_bubble_tables(){

	global $wpdb;
	$hbtable = $wpdb->prefix . "hoverbubbles";

	$installed_ver = get_option( TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY );

	if( $installed_ver != TNOTW_HOVERBUBBLE_VERSION ) {
		$sql = get_bubble_table_DDL() ;
		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);
		update_option(TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY, TNOTW_HOVERBUBBLE_VERSION);
	}
}

// register to run 
add_action('plugins_loaded', 'tnotw_check_update_bubble_tables');

// TODO: add unique constraint to bubble_name
function get_bubble_table_DDL() {

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
		UNIQUE KEY bubble_id (bubble_id));";
	return $sql;
}
 


?>

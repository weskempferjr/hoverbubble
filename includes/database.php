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
 */
function tnotw_get_bubble_configs(){
	global $wpdb ;
	$bubble_configs = $wpdb->get_results(
		"
		SELECT * FROM $wpdb->hoverbubbles WHERE target_image_cntnr_id = 'the_image_div'
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
				'bubbleMessage' => $bubbleConfig['bubble_message'],
				'bubbleFont' => $bubbleConfig['bubble_font'],
				'bubbleFontColor' => $bubbleConfig['bubble_font_color'],
				'bubbleTextAlign' => $bubbleConfig['bubble_text_align'],
				'bubbleTailLength' => $bubbleConfig['bubble_tail_length'],
				'bubbleCornerRadius' => $bubbleConfig['bubble_corner_radius'],
				'bubbleOutlineColor' => $bubbleConfig['bubble_outline_color'],
				'bubbleOutlineWidth' => $bubbleConfig['bubble_outline_width'],
				'bubbleTailDirection' => $bubbleConfig['bubble_tail_direction'],
				'targetImageID' => $bubbleConfig['target_image_id'],
				'targetImageContainerID' => $bubbleConfig['target_image_cntnr_id'],
				'bubbleCanvasID' => "bubble_canvas" . $bubbleConfig['bubble_id'] ,
				'bubbleTailX' => $bubbleConfig['bubble_tail_x'],
				'bubbleTailY' => $bubbleConfig['bubble_tail_y'],
				'bubbleAspectRatio' => $bubbleConfig['bubble_aspect_ratio'],
				'textLineSpacing' => $bubbleConfig['text_line_spacing'],
				'canvasBorderStyle' => $bubbleConfig['canvas_border_style']
	
	);
	return $mappedConfig ;
;	

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


function get_bubble_table_DDL() {

	$sql =  "CREATE TABLE ". $hbtable . " (
		bubble_id mediumint(12) NOT NULL AUTO_INCREMENT,
		bubble_message varchar(1000) NOT NULL,
		bubble_fill_color varchar(50) DEFAULT NULL,
		bubble_font_color varchar(50) DEFAULT NULL,
		bubble_font varchar(50) DEFAULT NULL,
		bubble_text_align varchar(50) DEFAULT NULL,
		text_line_spacing int(4) unsigned DEFAULT NULL,
		bubble_aspect_ratio float unsigned DEFAULT NULL,
		bubble_tail_length smallint(4) unsigned DEFAULT NULL,
		bubble_tail_direction char(2) DEFAULT NULL,
		bubble_tail_x int(4) unsigned DEFAULT NULL,
		bubble_tail_y int(4) unsigned DEFAULT NULL,
		bubble_corner_radius smallint(4) unsigned DEFAULT NULL,
		bubble_outline_color varchar(50) DEFAULT NULL,
		bubble_outline_width smallint(4) unsigned DEFAULT NULL,
		canvas_border_style varchar(100) DEFAULT NULL,
		target_image_id varchar(500) DEFAULT NULL,
		target_image_cntnr_id varchar(50) DEFAULT NULL,
		UNIQUE KEY bubble_id (bubble_id));";
	return $sql;
}
 


?>

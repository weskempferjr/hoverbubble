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
 * Map database columns to variable names as they are known in the UI.
 */
function map_config( $bubbleConfig ) {
	$mappedConfig = array(	'bubbleFillColor' => $bubbleConfig['bubble_fill_color'],
				'bubbleMessage' => $bubbleConfig['bubble_message'],
				'bubbleFont' => $bubbleConfig['bubble_font'],
				'bubbleFontColor' => $bubbleConfig['bubble_font_color'],
				'bubbleTextAlign' => $bubbleConfig['bubble_text_align'],
				'bubbleTailLength' => $bubbleConfig['bubble_tail_length'],
				'bubblePadding' => $bubbleConfig['bubble_padding'],
				'bubbleCornerRadius' => $bubbleConfig['bubble_corner_radius'],
				'bubbleOutlineColor' => $bubbleConfig['bubble_outline_color'],
				'bubbleOutlineWidth' => $bubbleConfig['bubble_outline_width'],
				'bubbleTailDirection' => $bubbleConfig['bubble_tail_direction'],
				'targetImageID' => $bubbleConfig['target_image_id'],
				'targetImageContainerID' => $bubbleConfig['target_image_cntnr_id'],
				'bubbleCanvasID' => $bubbleConfig['bubble_canvas_id'],
				'canvasBorderStyle' => $bubbleConfig['canvas_border_style'],
				'canvasTop' => $bubbleConfig['canvas_top_offset'],
				'canvasLeft' => $bubbleConfig['canvas_left_offset'],
				'canvasHeight' => $bubbleConfig['canvas_height'],
				'canvasWidth' => $bubbleConfig['canvas_width']
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
		bubble_id MEDIUMINT(12) NOT NULL AUTO_INCREMENT,
		bubble_message VARCHAR(1000) NOT NULL,
		bubble_fill_color VARCHAR(50),
		bubble_font_color VARCHAR(50),
		bubble_font VARCHAR(50),
		bubble_text_align VARCHAR(50),
		bubble_tail_length SMALLINT(4) UNSIGNED,	
		bubble_padding SMALLINT(4) UNSIGNED,	
		bubble_corner_radius SMALLINT(4) UNSIGNED,	
		bubble_outline_color VARCHAR(50),
		bubble_outline_width SMALLINT(4) UNSIGNED,	
		bubble_tail_direction CHAR(2),
		canvas_top_offset SMALLINT(4),
		canvas_left_offset SMALLINT(4),
		canvas_width MEDIUMINT(5),
		canvas_height MEDIUMINT(5),
		canvas_border_style VARCHAR(100),
		target_image_id VARCHAR(500),
		target_image_cntnr_id VARCHAR(50),
		bubble_canvas_id VARCHAR(50) UNIQUE,
		UNIQUE KEY bubble_id (bubble_id));";

	return $sql;
}
 


?>

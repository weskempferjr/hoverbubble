<?php
/*
Plugin Name: Hover Bubble
Plugin URI: http://tnotw.com/hoverbubble
Description: This plugin enables the creation of comic book bubble captions over images.
Version: 0.1
Author: Wes Kempfer
Author URI: http://tnotw.com/about-me
License: GPL2
/*  Copyright 2013  Wes Kempfer Jr  (email : wkempferjr@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Plugin constants
 **/
define('TNOTW_HOVERBUBBLE_VERSION', '0.1');
define('TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY', 'tnotw_hoverbubble_version');
define('TNOTW_HOVERBUBBLE_DIR', plugin_dir_path(__FILE__));
define('TNOTW_HOVERBBUBLE_URL', plugin_dir_url(__FILE__));



/**
 * Load files
 * 
 **/
function tnotw_hoverbubble_load(){
		
	if(is_admin()) //load admin files only in admin
		require_once(TNOTW_HOVERBUBBLE_DIR .'includes/admin.php');
		
	require_once(TNOTW_HOVERBUBBLE_DIR .'includes/core.php');
	require_once(TNOTW_HOVERBUBBLE_DIR .'includes/database.php');
}

tnotw_hoverbubble_load();


/**
 * Activation, Deactivation and Uninstall Functions
 * 
 **/
register_activation_hook(__FILE__, 'tnotw_hoverbubble_activation');
register_deactivation_hook(__FILE__, 'tnotw_hoverbubble_deactivation');


function tnotw_hoverbubble_activation() {
    
	//actions to perform once on plugin activation go here    
	require_once(TNOTW_HOVERBUBBLE_DIR .'includes/database.php');
	tnotw_create_bubble_tables();
	
	//register uninstaller
	register_uninstall_hook(__FILE__, 'tnotw_hoverbubble_uninstall');
}

function tnotw_hoverbubble_deactivation() {
    
	// actions to perform once on plugin deactivation go here
	    
}

function tnotw_hoverbubble_uninstall(){
    
    //actions to perform once on plugin uninstall go here
	    
}

/*
 * Register assets (javascript, css)
 */


function tnotw_hoverbubble_register_js()  
{  
	wp_register_script(	'hoverbubble-js', 
				plugins_url(	'assets/js/hoverbubble.js', __FILE__ ), 
						array( 'jquery' ) 
	);  
	wp_enqueue_script( 'hoverbubble-js' );  
}  

add_action( 'wp_enqueue_scripts', 'tnotw_hoverbubble_register_js' );  

/*
 * AJAX request handler
 */

function tnotw_hoverbubble_ajax(){

	// the first part is a SWTICHBOARD that fires specific functions
	// according to the value of Query Var 'fn'

	switch($_REQUEST['fn']){
		case 'get_bubble_config':
			$output = tnotw_get_bubble_configs();
			break;
		default:
		$output = 'No function specified, check your jQuery.ajax() call';
		break;

	}

	// Convert $output to JSON and echo it to the browser 

	$output=json_encode($output);
		if(is_array($output)){
			print_r($output);	
 		}
		else {
			echo $output;
	     	}
	die;

}

add_action('wp_ajax_nopriv_tnotw_hoverbubble_ajax', 'tnotw_hoverbubble_ajax');
add_action('wp_ajax_tnotw_hoverbubble_ajax', 'tnotw_hoverbubble_ajax');

?>

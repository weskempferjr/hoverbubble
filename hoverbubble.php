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

// require_once( plugin_dir_path(__FILE__) . "constants.php");

define('TNOTW_HOVERBUBBLE_VERSION', '0.5');
define('TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY', 'tnotw_hoverbubble_version');
define('TNOTW_HOVERBUBBLE_DIR', plugin_dir_path(__FILE__));
define('TNOTW_HOVERBBUBLE_URL', plugin_dir_url(__FILE__));


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/WPRegistrar.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/DatabaseFactory.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/BubbleConfigConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/ImageCandidateConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/PageCandidateConverter.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/BubblePageConverter.php");



require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/BubbleSettingsController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/BubbleConfigAjaxController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/BubbleEditActionController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/ErrorController.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/controllers/HelpController.php");




// TODO: make sure admin related resources are loaded only 
// with is_admin()

class HoverBubblePlugin {
	
	public function __construct() {
		// TODO: add localization code
		// add_action( 'init', array( $this, 'plugin_textdomain' ) );
		
		// check table configuration after upgrade.
		add_action('plugins_loaded', array($this,'check_table_update'));
		
		// Register admin scripts only for settings page/admin privs required 
		if ( is_admin() ) {
			add_action('admin_menu', array($this, 'admin_menu_pages'));
			add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );
			add_action( 'wpmu_new_blog', array( $this, 'new_blog') ) ;   
		}
		
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		// register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );
		// register_uninstall_hook( __FILE__, 'HoverBubblePlugin::uninstall'  );
		
		
		// register ajax function
		add_action('wp_ajax_nopriv_tnotw_hoverbubble_ajax', array( $this, 'tnotw_hoverbubble_ajax'));
		add_action('wp_ajax_tnotw_hoverbubble_ajax', array( $this,'tnotw_hoverbubble_ajax'));
		add_action('parse_request', array( $this,'tnotw_get_bubble_content'));
		add_filter('query_vars', array($this, 'query_vars') );
		
		// For jquery UI dialogs
		add_action('admin_footer', array($this, 'load_jquery_elements'));
		
		
	}
	
	
	public function register_assets() {		
		WPRegistrar::registerAssets();		
	}
	
	public function register_admin_assets($hook) {
		// Load assets only for plugin-related pages
		if ( ! strpos( $hook, "hoverbubble-settings"))
			return;

		WPRegistrar::registerAdminAssets();
	}
	
	// TODO: handle wpmu 
	
	public function activate ( $networkwide ) {
		
		global $wpdb ;
		if (function_exists('is_multisite') && is_multisite()) {
			 if ($networkwide) {
				$old_blog = $wpdb->blogid;
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ($blogids as $blog_id) {
                	switch_to_blog( $blog_id );
                	$this->site_activate( $blog_id );
            	}
            	switch_to_blog( $old_blog );
            	return;
			}
		
		}
		
		$this->site_activate( $wpdb->blogid );
		
	}
	
	
	     
 
	public function new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
   		 global $wpdb;
	 
	    if (is_plugin_active_for_network('hoverbubbles/hoverbubbles.php')) {
	        $old_blog = $wpdb->blogid;
	        switch_to_blog($blog_id);
	        $this->site_activate( $blog_id );
	        switch_to_blog($old_blog);
   	 	}
	}
	
	public function site_activate( $blog_id ) {
		
	
		$settings = SettingsFactory::getSettings();			
		$settings->initialize( $blog_id );
		
		$database = DatabaseFactory::getDatabase();

		if ( ! BubbleConfigConverter::tableExists() ) {
			$database->createTable( BubbleConfigConverter::generateDDL() );	
		}

		if ( ! ImageCandidateConverter::tableExists() ) {
 			$database->createTable( ImageCandidateConverter::generateDDL() );
		}

		if ( ! PageCandidateConverter::tableExists() ) {
 			$database->createTable( PageCandidateConverter::generateDDL() );
		}

		if ( ! BubblePageConverter::tableExists() ) {
 			$database->createTable(	BubblePageConverter::generateDDL() );
		}
 									
 		update_option(TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY, TNOTW_HOVERBUBBLE_VERSION);
 		
	}
	
	
	public function deactivate( $network_wide ) {
		
	}
	
	
	
	public function tnotw_hoverbubble_ajax() {
		BubbleConfigAjaxController::getBubbleConfigs();
	}
	
	public function tnotw_get_bubble_content($wp) {
		BubbleConfigAjaxController::getBubbleContent($wp);
	}
	
	public function query_vars( $vars ) {
		 $vars[] = 'hb_bubble_id';
    	return $vars;
	}
	
	public function admin_menu_pages(){
			// Add the top-level admin menu
		$page_title = 'Hover Bubble Plugin Setings';
		$menu_title = 'Hover Bubble';
		$capability = 'manage_options';
		$menu_slug = 'hoverbubble-settings';
		$function = 'hoverbubble_settings';
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this, $function)) ;
	
		// Add submenu page with same slug as parent to ensure no duplicates
		$sub_menu_title = 'Settings';
		add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, array($this, $function));
		
		// add_submenu_page(NULL, "Hover Bubble Edit", "Hover Bubble Edit", $capability, "hoverbubble-edit", "hoverbubble_edit");
	
		// Now add the submenu page for Help
		$submenu_page_title = 'Hover Bubble Plugin Help';
		$submenu_title = 'Help';
		$submenu_slug = 'hoverbubble-help';
		$submenu_function = 'hoverbubble_help';
		add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, array($this,$submenu_function));
	
		// Add bubble edit page, parent slug set to NULL so that page does not show up menu.
		$edit_page_title = "Hover Bubble Edit";
		$edit_menu_title = "Hover Bubble Edit";
		$edit_slug = "hoverbubble-edit";
		$edit_function = "hoverbubble_edit";
	
		add_submenu_page(NULL, $edit_page_title, $edit_menu_title, $capability, $edit_slug, array($this,$edit_function));
		
	}
	
	public function hoverbubble_settings() {
		$statusMessge = "";
		BubbleSettingsController::routeRequest( $statusMessge );
	}
	
	public function hoverbubble_edit() {
		BubbleEditActionController::routeRequest("");		
	}
	
	// TODO: complete hoverbubble help
	public function hoverbubble_help() {
		$statusMessage = "";
		HelpController::displayHelpPage($statusMessage);
		
	}
	
	public function check_table_update() {
		
		global $wpdb;
	
		$installed_ver = get_option( TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY );
	
		if( $installed_ver != TNOTW_HOVERBUBBLE_VERSION ) {
			
			$database = DatabaseFactory::getDatabase();
			
			$database->createTable( BubbleConfigConverter::generateUpgradeDDL() );									
 			$database->createTable( ImageCandidateConverter::generateUpgradeDDL() );									
 			$database->createTable( PageCandidateConverter::generateUpgradeDDL() );									
 			$database->createTable(	BubblePageConverter::generateUpgradeDDL() );
 									
			update_option(TNOTW_HOVERBUBBLE_VERSION_OPTION_KEY, TNOTW_HOVERBUBBLE_VERSION);
		}
	}
	
	public function load_jquery_elements() {
		// TODO: complete jquery delete confirmer
		?>
		<div id="dialog" style="display: none" title="Basic dialog">
		<p>Delete this bubble?</p>
		</div>
		<?php
	}
}

$hoverbubble_plugin = new HoverBubblePlugin();

?>
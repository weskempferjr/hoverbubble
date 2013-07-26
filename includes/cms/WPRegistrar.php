<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/lib/Mobile_Detect.php");

class WPRegistrar {
	
	public static function registerAssets() {
		
		// Don't bother with old versions of IE, or user agent not set. 
		if ( self::isUnsupportedBrowser() ) {
			return; 
		}
		
		// TODO: mobile stategy. Until then...
		$detect = new TNOTW_Mobile_Detect();
		if ($detect->isMobile()) {
 			return ;
		}
		
		wp_register_script(	'hoverbubble-js', 
							plugins_url() . '/hoverbubble/assets/js/hoverbubble.js', 
		 					array( 'jquery' ) 
			);
		wp_enqueue_script( 'hoverbubble-js' ); 
			
		$wp_js_info = array('site_url' => __(site_url()));
		// wp_localize_script('hoverbubble-js', 'wpsiteinfo', $wp_js_info );
		wp_localize_script('hoverbubble-js', 'objectl10n', array(
			'wpsiteinfo' => $wp_js_info,
			'retrieve_bubble_config_error' => __('Error retrieving bubble configurations:',  TNOTW_HB_TEXTDOMAIN ),
			'server_error' => __('Server error:', TNOTW_HB_TEXTDOMAIN )
		));
	}
	
	public static function registerAdminAssets() {
		//TODO: definition constant for path of assets/js
	
		wp_enqueue_script( 'hoverbubble-form-js',
			 plugins_url() . '/hoverbubble/assets/js/hoverbubble_form.js',  array( 'wp-color-picker' ) );

		$wp_js_info = array('site_url' => __(site_url()));
		
		wp_localize_script('hoverbubble-form-js', 'objectl10n', array(
			'wpsiteinfo' => $wp_js_info,
			'genimagestatus' => __('Gen image table status = ', TNOTW_HB_TEXTDOMAIN ),
			'gen_image_status_retrieve_error' => __('Error retrieving gen image table status from server:', TNOTW_HB_TEXTDOMAIN),
			'page_candidate_retrieve_error' => __('Error retrieving page candidate list from server:', TNOTW_HB_TEXTDOMAIN ),
			'outline_width_label' => __('Outline Width:', TNOTW_HB_TEXTDOMAIN ),
			'tail_length_label' => __('Bubble Tail Length:', TNOTW_HB_TEXTDOMAIN ),
			'corner_radius_label' => __('Bubble Corner Radius:', TNOTW_HB_TEXTDOMAIN ),
			'tail_tip_x_label' => __('Bubble Tail Tip X Coordinate:', TNOTW_HB_TEXTDOMAIN ),
			'tail_tip_y_label' => __('Bubble Tail Tip Y Coordinate:', TNOTW_HB_TEXTDOMAIN ),
			'content_area_height_label' => __('Content Area Heigth:', TNOTW_HB_TEXTDOMAIN ),
			'content_area_width_label' => __('Content Area Width:', TNOTW_HB_TEXTDOMAIN ),
			'delay_label' => __('Delay (in ms):', TNOTW_HB_TEXTDOMAIN ),
			'duration_label' => __('Duration (in ms):', TNOTW_HB_TEXTDOMAIN ),
			'bubble_name_req_label' => __('Bubble Name required:', TNOTW_HB_TEXTDOMAIN ),
			'bubble_name_label' => __('Bubble Name:', TNOTW_HB_TEXTDOMAIN ),
			'dup_bubble_name_label' => __('Duplicate bubble name!:', TNOTW_HB_TEXTDOMAIN ),
			'check_bubble_avail_error' => __('Error checking bubble name availability:', TNOTW_HB_TEXTDOMAIN ),
			'ok' => __('OK', TNOTW_HB_TEXTDOMAIN ),
			'cancel' => __('Cancel', TNOTW_HB_TEXTDOMAIN ),
			'must_be_num' => __('must be a number within the range', TNOTW_HB_TEXTDOMAIN ),
			'server_error' => __('Server error:', TNOTW_HB_TEXTDOMAIN ),
			'none' => __('None', TNOTW_HB_TEXTDOMAIN ),
			'confirm_delete_title' => __('Confirm Bubble Delete', TNOTW_HB_TEXTDOMAIN )	
			));
			 
		$plugins_url = plugins_url() ;	
			 
		wp_enqueue_script( 'jquery-ui-widget',
			$plugins_url . '/hoverbubble/assets/js/jquery.ui.widget.min.js' );
			 
		wp_enqueue_script( 'jquery-ui-button',
			$plugins_url . '/hoverbubble/assets/js/jquery.ui.button.min.js' );
	
		wp_enqueue_script( 'jquery-ui-position',
			$plugins_url . '/hoverbubble/assets/js/jquery.ui.position.min.js' );
			 
			 
		wp_enqueue_script( 'jquery-ui-dialog',
			$plugins_url . '/hoverbubble/assets/js/jquery.ui.dialog.min.js' );
	
				
		wp_enqueue_style('jquery.ui.theme', 
			$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery-ui.min.css');
			
		wp_enqueue_style('jquery.ui.core.theme', 
			$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.core.min.css');
	
		wp_enqueue_style('jquery.ui.theme.theme', 
			$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.theme.min.css');
		
		wp_enqueue_style('jquery.ui.button.theme', 
			$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.button.min.css');
			
			
		wp_enqueue_style('jquery.ui.dialog.theme', 
			$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.dialog.min.css');
		
		// color picker
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		
		// tiny mce
		wp_enqueue_script( 'hb.tiny.mce',
			$plugins_url . '/hoverbubble/assets/js/tiny_mce/tiny_mce.js' );
		
		// jquery tools 
		wp_enqueue_script( 'jquery.tools',
			$plugins_url . '/hoverbubble/assets/js/jquery.tools.min.js' );
		
		wp_enqueue_style('hb.admin.form', 
			$plugins_url . '/hoverbubble/assets/css/admin_form.css');
	}
	
	private static function isUnsupportedBrowser() {
		
		if ( !isset( $_SERVER['HTTP_USER_AGENT']  ) ) {
			return true ;
		}
		
		$ua_array = preg_split("/;/", $_SERVER['HTTP_USER_AGENT'] );
		foreach ( $ua_array as $item ) {
			if ( 	strpos( $item, "MSIE 6", 0) !== FALSE || 
					strpos( $item, "MSIE 7", 0) !== FALSE || 
					strpos( $item, "MSIE 8", 0) !== FALSE ) {
				return TRUE ;
			}
		}

		return FALSE ;
	}
	
}
?>
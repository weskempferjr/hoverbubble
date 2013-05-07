<?php

class WPRegistrar {
	
	public static function registerAssets() {
		
		// Don't bother with old versions of IE 
		if ( self::isUnsupportedBrowser() ) {
			return; 
		}
		
		wp_register_script(	'hoverbubble-js', 
							plugins_url() . '/hoverbubble/assets/js/hoverbubble.js', 
		 					array( 'jquery' ) 
			);
		wp_enqueue_script( 'hoverbubble-js' ); 
			
		$wp_js_info = array('site_url' => __(site_url()));
		wp_localize_script('hoverbubble-js', 'wpsiteinfo', $wp_js_info );
	}
	
	public static function registerAdminAssets() {
		//TODO: definition constant for path of assets/js
	
		wp_enqueue_script( 'hoverbubble-form-js',
			 plugins_url() . '/hoverbubble/assets/js/hoverbubble_form.js',  array( 'wp-color-picker' ) );

		$wp_js_info = array('site_url' => __(site_url()));
		wp_localize_script('hoverbubble-form-js', 'wpsiteinfo', $wp_js_info );
			 
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
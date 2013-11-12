<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/Settings.php");
define( 'TNOTW_DEFAULT_EXCLUSION_LIST', '.jpg .png .gif .pdf .mp3' );
define( 'TNOTW_WP_OPTIONS_NAME', 'tnotw_hbsettings' );

/*
 * WPSettings class
 * 
 * Implments Setting interface for Wordpress. 
 */

class WPSettings implements Settings {
	
	private $crawlPath ;
	private $exclusionList ;
	
	private $options ;
	private $crawlPathArray;
	private $exclusionListArray ;
	
	/*
	 * Function: setCrawlPath. The crawlpath is a list of the top-level URLs at which 
	 * the Generate Image List function begins its scan of the site
	 * for target images. 
	 */
	public function setCrawlPath( $crawlPath ) {
		$this->crawlPath = $crawlPath ;
	}
	
	/*
	 * Function: getCrawlPath. Returns the crawl path URL. 
	 */
	public function getCrawlPath() {
		return $this->crawlPath ;
	}
	
	/*
	 * Function: setExclusion list. Set the exclution list. The exclusion
	 * list is a list of filename patterns (e.g. *.pdf) that should
	 * not be considered as potential target objects. 
	 */
	public function setExclusionList( $exclusionList ) {
		$this->exclusionList = $exclusionList;
	}
	
	/*
	 * Function: getExclusionList, returns the current exclusion list setting. 
	 */
	public function getExclusionList() {
		return $this->exclusionList ;
	}
	
	/*
	 * Function: getExclusionListArray, returns exclusion list as 
	 * an array. 
	 */
	public function getExclusionListArray() {
		return $this->exclusionListArray;
	}
	
	/*
	 * Function: getCrawlPathArray: returns crawlpath as an array.
	 */
	public function getCrawlPathArray() {
		return $this->crawlPathArray ;
	}
	
	/*
	 * Function: initialize.
	 * This function is intended to be called when the plugin is installed
	 * and activated. It records the site-specific defaults. 
	 */ 
	public function initialize( $blogID ) {
		
		// set up a default options array
		if (function_exists('is_multisite') && is_multisite()) {
			$this->setCrawlPath( get_home_url( $blogID, '', 'http' ) );
		} 
		else {
			$this->setCrawlPath( home_url('', 'http') );
		}
		
		$this->setExclusionList( TNOTW_DEFAULT_EXCLUSION_LIST ) ;
		$this->formatForStorage();
		
		$this->options = get_option( TNOTW_WP_OPTIONS_NAME, $this->options );
		$this->formatForDisplay() ;
		$this->store();
		
	}
	
	/*
	 * Function: store
	 * This function writes the current settings to the WP options table. 
	 */
	public function store() {
		$this->formatForStorage();
		update_option( 'tnotw_hbsettings', $this->options ) ;
	}
	
	/*
	 * Function: load
	 * This function reads the currently stored setting from the WP
	 * options table. 
	 */
	public function load() {
		
		$this->options = get_option( TNOTW_WP_OPTIONS_NAME ) ;
		$this->crawlPathArray = $this->options['crawlPathArray'];
		$this->exclusionListArray = $this->options['exclusionListArray'];
		
		$this->formatForDisplay();
		
	}
	
	/*
	 * Function: formatForStorage
	 * This function formats the current setting for storage in the 
	 * WP Options table. It is called by store. 
	 */
	private function formatForStorage() {
		
		$this->crawlPathArray = preg_split("/[\s]+/", $this->crawlPath, -1, PREG_SPLIT_NO_EMPTY );
		$this->exclusionListArray = preg_split("/[\s]+/",  $this->exclusionList , -1, PREG_SPLIT_NO_EMPTY );
				
		$this->options = array(
			'crawlPathArray' => $this->crawlPathArray,
			'exclusionListArray' => $this->exclusionListArray
		);
	}
	
	/*
	 * Function: formatForDisplay
	 * This function formats the currently stored setting for UI display.
	 * It is called by load(). 
	 */
	private function formatForDisplay() {
		
		$crawlPath = "";		
		foreach ( $this->crawlPathArray as $item ) {
			$crawlPath .= $item . ' ';
		}
		$this->crawlPath = rtrim( $crawlPath, ' ');
		
		$exclusionList = "";		
		foreach ( $this->exclusionListArray as $item ) {
			$exclusionList .= $item . ' ';
		}
		$this->exclusionList = rtrim( $exclusionList, ' ');
	}
	
	
	
}
?>
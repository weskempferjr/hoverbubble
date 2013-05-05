<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/config/Settings.php");
define( 'TNOTW_DEFAULT_EXCLUSION_LIST', '.jpg .png .gif .pdf .mp3' );
define( 'TNOTW_WP_OPTIONS_NAME', 'tnotw_hbsettings' );


class WPSettings implements Settings {
	
	private $crawlPath ;
	private $exclusionList ;
	
	private $options ;
	private $crawlPathArray;
	private $exclusionListArray ;
	
	public function setCrawlPath( $crawlPath ) {
		$this->crawlPath = $crawlPath ;
	}
	
	public function getCrawlPath() {
		return $this->crawlPath ;
	}
	
	public function setExclusionList( $exclusionList ) {
		$this->exclusionList = $exclusionList;
	}
	
	public function getExclusionList() {
		return $this->exclusionList ;
	}
	
	public function getExclusionListArray() {
		return $this->exclusionListArray;
	}
	
	public function getCrawlPathArray() {
		return $this->crawlPathArray ;
	}
	
	// Used for activation. 
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
	
	public function store() {
		$this->formatForStorage();
		update_option( 'tnotw_hbsettings', $this->options ) ;
	}
	
	public function load() {
		
		$this->options = get_option( TNOTW_WP_OPTIONS_NAME ) ;
		$this->crawlPathArray = $this->options['crawlPathArray'];
		$this->exclusionListArray = $this->options['exclusionListArray'];
		
		$this->formatForDisplay();
		
	}
	
	private function formatForStorage() {
		
		$this->crawlPathArray = preg_split("/[\s]+/", $this->crawlPath, -1, PREG_SPLIT_NO_EMPTY );
		$this->exclusionListArray = preg_split("/[\s]+/",  $this->exclusionList , -1, PREG_SPLIT_NO_EMPTY );
				
		$this->options = array(
			'crawlPathArray' => $this->crawlPathArray,
			'exclusionListArray' => $this->exclusionListArray
		);
	}
	
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
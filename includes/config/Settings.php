<?php

/*
 *  Settings interface
 *  
 *  This interface is used to hide the CMS/runtime env specific details 
 *  around the storage and retrieval of configuration settings. 
 */
interface Settings {
	public function setCrawlPath( $crawlPath );
	public function getCrawlPath();
	public function setExclusionList( $exclusionList );
	public function getExclusionList();
	public function load();
	public function store();
	public function initialize( $blogID );
}

?>
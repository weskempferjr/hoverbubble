<?php

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
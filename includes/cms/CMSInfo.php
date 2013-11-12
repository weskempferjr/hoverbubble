<?php

/* 
 * CMSInfo interface
 * Use this interface to hide CMS-specific details regarding how to retrieve
 * varous pieces of information which are probably common to all CMSs stored 
 * accessed in CMS-specific ways. 
 * 
 */
interface CMSInfo {
	public function getAuthorID();
	public function getAuthorLogin( $authorID );
}

?>
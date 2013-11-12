<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/CMSInfo.php");
/*
 * WPInfo class
 * 	Implements CMSInfo interface
 */
class WPInfo implements CMSInfo {
	/*
	 * Function: getAuthorID
	 * 	Parameters: none
	 * 	Return: the author ID for the current user. 
	 */
	public function getAuthorID() {
		$currentUser = wp_get_current_user() ;
		return $currentUser->ID ;
	}
	
	/*
	 * Function: getAuthorLogin
	 * 	Parameters: $author (one returned by getAuthorID, for example)
	 * 	Returns:the login name for the specified author ID. 
	 */
	public function getAuthorLogin( $authorID ) {
		$user = get_userdata( $authorID );
		return $user->user_login ;		
	}
}
?>
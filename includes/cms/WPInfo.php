<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/CMSInfo.php");

class WPInfo implements CMSInfo {
	public function getAuthorID() {
		$currentUser = wp_get_current_user() ;
		return $currentUser->ID ;
	}
	
	public function getAuthorLogin( $authorID ) {
		$user = get_userdata( $authorID );
		return $user->user_login ;		
	}
}
?>
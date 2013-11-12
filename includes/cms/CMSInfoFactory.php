<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/cms/WPInfo.php");

/*
 *  CMSInfoFactory class
 *  	This is a factory class that returns CMS-specific implementation
 *  	of the CMSInfo interface. 
 *  	
 *  	TODO: Need to add runtime configuration variable or property that
 *  	indicates which implementation to instantiate. 
 *  
 */
class CMSInfoFactory {
	/*
	 * Function: getCMSInfo
	 * 		Parameters: none
	 * 		Return: an instance of the class that implements 
	 * 		the CMSInfo interface, appropriate for the CMS runtime 
	 * 		environment.
	 */
	public static function getCMSInfo() {
		return new WPInfo();
	}	
}

?>
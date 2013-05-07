<?php

class SettingsScrubber {
	
	public static function scrub( $inputData ) {
		$scrubbed = array();
		
		$cpArray = preg_split("/[\s]+/", $inputData['crawlpath'], -1, PREG_SPLIT_NO_EMPTY );
		$scrubbedCPArray = array();
		foreach ( $cpArray  as $cp ) {
			array_push( $scrubbedCPArray, esc_url( $cp ) ) ;
		}
		$crawlPath = "";		
		foreach ( $scrubbedCPArray as $item ) {
			$crawlPath .= $item . ' ';
		}
		$scrubbed['crawlpath'] = rtrim( $crawlPath, ' ');
		
		$scrubbed['exclusionlist'] = sanitize_text_field( $inputData['exclusionlist'] );
		
		return $scrubbed ;		
		
	}
}
?>
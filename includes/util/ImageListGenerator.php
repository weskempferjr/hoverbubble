<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/ImageCandidate.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/PageCandidate.php");

class ImageListGenerator {
	
	private static $imageInfoList ;
	private static $searched ;
	
	// TODO: exception handling
	public static function updateCandidateTables() {
		// Crawl site to find images and the pages
		// on which they appear. 
		self::getSiteImageList();
		
		// Build unique list of images from image info list.
		$imageURLList = array();
		foreach ( self::$imageInfoList as $imageInfo ) {
			array_push($imageURLList, $imageInfo->imageURL );
		}
		array_unique(imageURLList);
		
		
		// For each unique image URL, 
		//  -add to database if it does does not exit.
		//  -get list of pages on which it appears. 
		//  -add pages to pages candidates table (if they don't exist).
		foreach ( $imageURLList as $imageURL ) {
			
			// TODO: collapse into a single call/shoudl be getRow call with a unique column value
			if ( self::doesImageCandidateExist( $imageURL ) ) {
				$whereClause = "target_image_url = '" . $imageURL . "'" ;
				$imageCandidates = ImageCandidate::retrieveImageCandidates($whereClause);
				$imageCandidate = $imageCandidates[0];
			} 
			else {
				$imageCandidate = new ImageCandidate();
				$imageCandidate->setTargetImageURL($imageURL);
				$imageCandidate->insert();
			}
			
			$imageCandidateID = $imageCandidate->getImageCandidateID();
			
			foreach ( self::$imageInfoList as $imageInfo ) {
				 if ( $imageURL === $imageInfo->imageURL ) {
				 	if ( self::doesPageCandidateExist( $imageInfo->parentPage , $imageCandidateID ) ) {
				 		continue;
				 	}
				 	else {
				 		$pageCandidate = new PageCandidate();
				 		$pageCandidate->setImageCandidateID( $imageCandidateID );
				 		$pageCandidate->setDisplayBubble( false );
				 		$pageCandidate->setTargetPageURL( $imageInfo->parentPage ); 
				 		$pageCandidate->insert();
				 	}
				 }
			}
			
		}
				
		
	}
	
	public static function getSiteImageList() {
		
		self::$imageInfoList = array();
		self::$searched = array();
		// TODO: use interface to hide CMS depedencies. 
		$siteURL = get_site_url();
		$searchComplete = false;
		$currentURL = $siteURL;
		
		ImageListGenerator::findPageImages($siteURL, $siteURL);
		return self::$imageInfoList;
	}
	
	private static function findPageImages( $siteURL, $pageURL ) {
		$doc = new DOMDocument();
		$doc->loadHTMLFile($pageURL);
		
		// get list of images on this page. 
		foreach($doc->getElementsByTagName("img") as $tag) {
			$srcAttr = $tag->getAttribute("src");
			if ( strpos($srcAttr, $siteURL, 0) === 0) {
				$imageInfo = new ImageInfo();
				$imageInfo->parentPage = $pageURL;
				$imageInfo->imageURL = $srcAttr ;
				array_push(self::$imageInfoList, $imageInfo);
			}			
		}
		// add page to already searched list
		array_push( self::$searched, $pageURL );
		
		foreach ($doc->getElementsByTagName("a") as $tag ) {
			$hrefAttr = $tag->getAttribute("href");	

			if ( strpos( $hrefAttr, "#") !== false ) {
				continue;
			}
			
			// TODO: create a filter option to enable configuration of links to skip
			if ( substr( $hrefAttr, -4 ) === ".jpg"  || substr( $hrefAttr, -4 ) === ".png") {
				continue;
			}
			
			if ( strpos($hrefAttr, $siteURL, 0) === 0) {
				$hrefAttrTrimmed = rtrim( $hrefAttr, "/");
				$pageURLTrimmed = rtrim( $pageURL, "/");
				
				if ( $hrefAttrTrimmed != $pageURLTrimmed && !(in_array( $hrefAttr, self::$searched) || in_array( $hrefAttrTrimmed, self::$searched) ) ) {
					ImageListGenerator::findPageImages($siteURL, $hrefAttr);
				}
			}	
		}
		
	}
	
	
	// TODO: move this kind of stuff to model.
	private static function doesImageCandidateExist( $imageURL ) {
		
		$whereClause = "target_image_url = '" . $imageURL . "'" ;
		$imageCandidates = ImageCandidate::retrieveImageCandidates($whereClause);
		
		if ( count($imageCandidates) == 0 ) {
			return FALSE;
		}
		else {
			return TRUE;
		}
		
	}
	
	private static function doesPageCandidateExist( $pageURL, $imageCandidateID ) {
		
		$pageURL = rtrim( $pageURL, "/");
		
		$where_clause = "target_page_url = '" . $pageURL . "' and image_candidate_id = " . $imageCandidateID ;
		$pageCandidates = PageCandidate::retrievePageCandidates($where_clause);
		
		if ( count($pageCandidates) == 0 ) {
			return FALSE;
		}
		else {
			return TRUE;
		}
		
	}
}

class ImageInfo {
	public $parentPage;
	public $imageURL ;
}
?>
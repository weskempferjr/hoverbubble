<?php


require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/ImageCandidate.php");
require_once( TNOTW_HOVERBUBBLE_DIR . "includes/model/PageCandidate.php");

class ImageListGenerator {
	
	private static $imageInfoList ;
	private static $imagePageMap ;
	private static $searched ;
	private static $settings ;
	
	// Update the page and image candidate tables by crawling site and updating
	// the image and page candidate tables to match what is found on the site.
	// Exceptions thrown by calls in this function should be caught somewhere up the
	// call stack. 
	public static function updateCandidateTables() {
		
		self::$settings = SettingsFactory::getSettings();
		self::$settings->load();
		
		// Crawl site to find images and the pages
		// on which they appear. 
		self::getSiteImageList();
		
		// Build unique list of images from image info list.
		$imageURLList = array();
		foreach ( self::$imageInfoList as $imageInfo ) {
			array_push($imageURLList, $imageInfo->imageURL );
		}
		array_unique( $imageURLList );
		
		
		// For each unique image URL, 
		//  -add to database if it does does not exit.
		//  -get list of pages on which it appears. 
		//  -add pages to pages candidates table (if they don't exist).
		foreach ( $imageURLList as $imageURL ) {
			
			// TODO: collapse into a single call/shoudl be getRow call with a unique column value
			if ( self::doesImageCandidateExist( $imageURL ) ) {
				$whereClause = "target_image_url = '" . $imageURL . "'" ;
				$imageCandidates = ImageCandidate::retrieveImageCandidates( $whereClause );
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
		// Remove any images from the database that
		// no are no longer published. 
		self::cleanImageTables();
		
	}
	
	// Called from updateImageTables in order to remove image/page candidate records 
	// no longer reference on the site (i.e. images no longer used, still existing images that have been
	// removed from some pages. 
	public static function cleanImageTables() {
				
		// Delete image candidates not on current list		
		$whereClause = "";				
		$imageCandidates = ImageCandidate::retrieveImageCandidates( $whereClause ) ;
		
		foreach ( $imageCandidates as $imageCandidate ) {
			$targetImageURL = $imageCandidate->getTargetImageURL();
			if ( self::isImageCandidateDisplayed( $targetImageURL ) ) {
				continue;
			}
			$imageCandidateID = $imageCandidate->getImageCandidateID();
			ImageCandidate::delete( $imageCandidateID );
		}
		
		// For each active image candidate, delete any invalid page candidates. 
		foreach ( array_keys( self::$imagePageMap ) as $imageURL ) {
			// TODO: collapse into a single call/should be getRow call with a unique column value
			if ( self::doesImageCandidateExist( $imageURL ) ) {
				$whereClause = "target_image_url = '" . $imageURL . "'" ;
				$imageCandidates = ImageCandidate::retrieveImageCandidates( $whereClause );
				$imageCandidate = $imageCandidates[0];
				$imageCandidateID = $imageCandidate->getImageCandidateID();
				
				
				$pageList = self::$imagePageMap[ $imageURL ];
				$whereClause = "image_candidate_id = " . $imageCandidateID .  " AND target_page_url NOT IN ( ";
				$pageCount = count( $pageList );
				for ( $i = 0 ;  $i < $pageCount ; $i++ ) {
					$whereClause .= ' ' . "'". $pageList[$i] . "'";
					if ( $i == ($pageCount - 1)) {
						$whereClause .= ')';
					} 
					else {
						$whereClause .= ',';
					}
				}
				
				$pageCandidates = PageCandidate::retrievePageCandidates( $whereClause ) ;
				foreach ( $pageCandidates as $pageCandidate ) {
					$pageCandidateID = $pageCandidate->getPageCandidateID();
					PageCandidate::delete( $pageCandidateID ); 
				}
				
			} 
			
		}
		
		
	}
	
	public static function getImagePageList( $imageURL ) {
		
		$pageList = array();
		foreach ( self::$imageInfoList as $imageInfo ) {
			if ( $imageURL == $imageInfo->imageURL ) {
				array_push( $pageList, $imageInfo->parentPage );
			}
		}	
		return $pageList ;
	}
	
	private static function isImageCandidateDisplayed(  $imageCandidateURL )  {
		
		$imageURLList = array_keys( self::$imagePageMap );
		
		if ( array_search( $imageCandidateURL, $imageURLList ) === false ) {
			return false; 
		} 
		else {
			return true;
		}
	}
	
	
	public static function getSiteImageList() {
		
		self::$imageInfoList = array();
		self::$searched = array();
		// TODO: use interface to hide CMS depedencies. 
		$siteURL = get_site_url();

		$crawlPathArray = self::$settings->getCrawlPathArray() ;
		
		foreach ( $crawlPathArray as $crawlURL ) {
			self::findPageImages($siteURL, $crawlURL );
		}
		
		self::setImagePageMap();
		return self::$imageInfoList;
	}
	
	// 
	// Crawl the specified page to find images. This method
	// is called recursively as links to other pages 
	// on the site are encountered. Links to external sites
	// are ingored. 
	// TODO: External links are detected by trying to match link URLs to
	// the site URL. Hence, relative links may be missed. This appears
	// not to be a problem with Wordpress since it seems to generate
	// only abslolute URLs.  
	private static function findPageImages( $siteURL, $pageURL ) {
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		
		// Suppressing error output deliberately (with "@") in order to catch
		// exception and send brief error message from caller exception
		// handler. 
		if ( @$doc->loadHTMLFile($pageURL) === false ) {
			libxml_clear_errors();
			if ( self::isCrawlPathURL( $pageURL ) ) {
			 	throw new Exception('ImageListGenerator could not load crawl path URL:' . $pageURL );
			}
			else {
				Logger::logError( 'ImageListGenertator could not page URL: ' . $pageURL ) ;
			}
		}
		
		// get list of images on this page. 
		foreach($doc->getElementsByTagName("img") as $tag) {
			$srcAttr = $tag->getAttribute("src");
			if ( strpos($srcAttr, $siteURL, 0) === 0) {
				$imageInfo = new ImageInfo();
				$imageInfo->parentPage = rtrim( $pageURL, "/" );
				$imageInfo->imageURL = $srcAttr ;
				array_push(self::$imageInfoList, $imageInfo);
			}			
		}
		// add page to already searched list
		array_push( self::$searched, $pageURL );
		
		foreach ($doc->getElementsByTagName("a") as $tag ) {
			$hrefAttr = $tag->getAttribute("href");	

			// Ignore anchors to same page
			if ( strpos( $hrefAttr, "#") !== false ) {
				continue;
			}
			
			// Don't try to crawl this URL if it is a media file. 
			if ( self::isExcluded( $hrefAttr ) ) {
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
	
	private static function isCrawlPathURL( $pageURL ) {
		
		$pageURL = rtrim( $pageURL, '/' );
		foreach ( self::$settings->getCrawlPathArray() as $crawlPathURL  ) {
			if ( $pageURL === $crawlPathURL ) {
				return true;
			}
		}
		return false ;
	}
	
	
	//
	// Compare URL to list of media type exlucsions. If URL points to a jpeg file, for
	// example, ignore it. 
	private static function isExcluded( $url ) {
		
		$exclusionListArray = self::$settings->getExclusionListArray();
		
		foreach ( $exclusionListArray as $exclusion ) {
			$endPosition = strlen( $exclusion ) * -1;
			if ( strpos( $url, $endPosition ) === $exclusion ) {
				return true ;
			}			
		}
		
		return false ;		
	}
	
	private static function setImagePageMap() {
		$imageURLList = array();
		foreach ( self::$imageInfoList as $imageInfo ) {
			array_push($imageURLList, $imageInfo->imageURL );
		}
		array_unique($imageURLList);
		
		self::$imagePageMap = array();
		foreach ( $imageURLList as $imageURL ) {
			$pageList = self::getImagePageList( $imageURL );
			self::$imagePageMap[ $imageURL ] =  $pageList ;	
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
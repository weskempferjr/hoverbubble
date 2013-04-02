<?php

class WPResources {
	
	public static final function getImageURL( $imageID ) {
			// $imageURL = wp_get_attachment_url($imageID);
			$srcArray = wp_get_attachment_image_src( $imageID );
			if ( $srcArray == null )
				throw new Exception("WPResources::getImageURL: Could not find attachment source for image ID = " . $imageID, -1);
			$imageURL = $srcArray[0];
			if ( $imageURL == null )
				throw new Exception("WPResources::getImageURL: Could not find image URL for image ID = " . $imageID, -1);
			return $imageURL ;
	}
	
	public static final function getPostedImages() {
		
		$query_images_args = array(
	    	'post_type' => 'attachment', 'post_mime_type' =>'image', 'post_status' => 'inherit', 'posts_per_page' => -1,
		);

		$query_images = new WP_Query( $query_images_args );
		$images = array();
		foreach ( $query_images->posts as $image) {
			$image_info = array();
			$post_parent = get_post( $image->post_parent);
			//if ( $post_parent == null )
			//	continue;
			$image_info['image_ID'] = $image->ID ;
			$image_info['image_title'] = $image->post_title ;
			
			if ( $post_parent != null ) {
				$image_info['image_parent_id'] = $image->post_parent ;
				$image_info['image_parent_post_title'] = get_the_title($image->post_parent );	
				$image_info['image_parent_post_type'] = $post_parent->post_type ;
					
				if ($image_info['image_parent_post_title'] == '') {
					$image_info['image_parent_post_title'] = $post_parent->post_name ;
			}		
			}
			else {
				$image_info['image_parent_id'] = 0;
				$image_info['image_parent_post_title'] = "no parent";
				$image_info['image_parent_post_type'] = "no parent";
				$image_info['image_parent_post_title'] = "no parent";
			}
			
			$image_info['image_thumb_url '] = wp_get_attachment_thumb_url($image->ID);
			
	
	   		$images[] = $image_info ;
		}
		
		return $images ;
	}
}
?>
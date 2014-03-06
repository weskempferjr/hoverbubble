<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/WPResources.php");


class AdminEditView {
	
	private static $imageCandiditeList ;
	
	public static function setImageCandidateList( $imageCandidates) {
		self::$imageCandiditeList = $imageCandidates;
	}
	
	public static function displayBubbleEditPage( $bubble, $action, $statusMessage ) {
	?>
	<div class="wrap">
		<?php if ( $action == "edit") { ?>
			<p><?php printf( __( 'Here is the hoverbubble edit page for bubble_id = %d', TNOTW_HB_TEXTDOMAIN ), $bubble->getBubbleID() ); ?></p>
		 <?php } ?>
		<form id="hbedit" action="" method="POST">
			<table style="width: 100%">
			<col  style="width: 20%">
			<col  style="width: 80%">
			<?php if ( $action == "add") { ?>
				<?php  wp_nonce_field('bubble_add') ;?>
				<tr>
				<td><p id="bubblenamelabel"><?php echo __('Bubble Name:', TNOTW_HB_TEXTDOMAIN ); ?><p></td> 
	 			<td><input id="bubblename" type="text" name="bubble_name" value="<?php echo $bubble->getBubbleName(); ?>" autofocus required /> </td>
				</tr>
			<?php } 
			else { ?>
				<?php  wp_nonce_field('bubble_edit_bubble_id' . $bubble->getBubbleID() ) ;?>				
				<tr>
				<td><?php echo __('Bubble Name:', TNOTW_HB_TEXTDOMAIN ); ?></td> 
	 			<td><input type="hidden" name="bubble_name" value="<?php echo $bubble->getBubbleName(); ?>" /> <?php echo $bubble->getBubbleName(); ?></td>
				</tr>
			<?php  } ?>
			<tr>
			<td><?php echo __('Bubble Message:', TNOTW_HB_TEXTDOMAIN ); ?></td>
			<td>
			<textarea class="theEditor" rows="10" cols="40" name="bubble_message" form="hbedit" ><?php echo $bubble->getBubbleMessage(); ?> </textarea> 		
			</td> 
			</tr>
			<tr>
			<td><?php echo __('Bubble Description:', TNOTW_HB_TEXTDOMAIN ); ?></td>
			<td>
			<textarea rows="3" cols="40" name="bubble_description" form="hbedit" ><?php echo $bubble->getBubbleDescription(); ?> </textarea> 		
			</td> 
			</tr>
			<tr>
			<td><label for="published"><?php echo __('Published:', TNOTW_HB_TEXTDOMAIN) ;?></label></td>
			<td><input name="published" id="pubcb" type="checkbox" value="published" <?php if ( $bubble->getPublished() != FALSE ) echo " checked" ; ?>/></td>
			<tr>
			<tr>
			<td><?php echo __('Bubble Fill Color:', TNOTW_HB_TEXTDOMAIN ); ?></td> 
	 		<td><input class="colorfield" type="text" name="bubble_fill_color" value="<?php echo $bubble->getBubbleFillColor(); ?>" /> </td>
			</tr>
			<tr>
			<td><?php echo __('Bubble Outline Color:', TNOTW_HB_TEXTDOMAIN ); ?></td>
			<td><input class="colorfield" type="text" name="bubble_outline_color" value="<?php echo $bubble->getBubbleOutlineColor(); ?>" />  </td>
			</tr>
			<tr>
			<td><p id="outlinewidthlabel"><?php echo __('Bubble Outline Width:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td><input id="outlinewidth" type="number" step="1" min="0" max="15" name="bubble_outline_width" value="<?php echo $bubble->getBubbleOutlineWidth(); ?>" />  </td>
			</tr>
			<tr>
			<td><p id="taillengthlabel"><?php echo __('Bubble Tail Length:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td><input id="taillength" type="number" step="1" min="0" max="1000" name="bubble_tail_length" value="<?php echo $bubble->getBubbleTailLength(); ?>" />  </td>
			</tr>
			<tr>
			<td><?php echo __('Bubble Tail Direction:', TNOTW_HB_TEXTDOMAIN ); ?></td>
			<td>
			<table>
			<tr>
			<td ><?php _e('N')?> <input type="radio" name="bubble_tail_direction" value ="N"  <?php if ($bubble->getBubbleTailDirection() == "N") echo 'checked' ; ?>/>&nbsp </td>
			<td><?php _e('E')?> <input type="radio" name="bubble_tail_direction" value ="E"  <?php if ($bubble->getBubbleTailDirection() == "E") echo 'checked' ; ?> />&nbsp </td>
			<td><?php _e('S')?> <input type="radio" name="bubble_tail_direction" value ="S"  <?php if ($bubble->getBubbleTailDirection() == "S") echo 'checked' ; ?>/>&nbsp </td>
			<td><?php _e('W')?> <input type="radio" name="bubble_tail_direction" value ="W"  <?php if ($bubble->getBubbleTailDirection() == "W") echo 'checked' ; ?> />&nbsp </td>
			<td><?php _e('NONE')?> <input type="radio" name="bubble_tail_direction" value ="NONE" <?php if ($bubble->getBubbleTailDirection() == "NONE") echo 'checked' ; ?> />&nbsp </td>
			
			</tr>
			</table>
			</td>
			</tr>
			<tr>
			<td><p id="cornerradiuslabel"><?php echo __('Bubble Corner Radius:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td><input id="cornerradius" type="number" step="1" min="1" max="150" name="bubble_corner_radius" value="<?php echo $bubble->getBubbleCornerRadius(); ?>" />  </td>
			</tr>
			
			<tr>
			<td><?php echo __('Bubble Tail Type:', TNOTW_HB_TEXTDOMAIN ); ?></td>
			<td>
			<table>
			<tr>
			<td ><?php _e('speech')?> <input type="radio" name="bubble_tail_type" value ="speech"  <?php if ($bubble->getBubbleTailType() == "speech") echo 'checked' ; ?>/>&nbsp </td>
			<td><?php _e('thought')?> <input type="radio" name="bubble_tail_type" value ="thought" <?php if ($bubble->getBubbleTailType() == "thought") echo 'checked' ; ?> />&nbsp </td>
			</tr>
			</table>
			</td>
			</tr>		
			
			
			<tr>
			<td><p id="tailbasewidthlabel"><?php echo __('Bubble Tail Base Width:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td><input id="tailbasewidth" type="number" step="1" min="0" max="10000" name="bubble_tail_base_width" value="<?php echo $bubble->getBubbleTailBaseWidth(); ?>" />  </td>
			</tr>
			
			<tr>
			<td><p id="tailposlabel"><?php echo __('Bubble Tail Position:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td><input id="tailposlabel" type="number" step="0.1" min="0.1" max="1" name="bubble_tail_position" value="<?php echo $bubble->getBubbleTailPosition(); ?>" />  </td>
			</tr>
			
			
			<tr>
			<td><p id="tailtipxlabel"><?php echo __('Bubble Tail Tip X Coordinate:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td><input id="tailtipx" type="number" step="1" min="0" max="2000" name="bubble_tail_x" value="<?php echo $bubble->getBubbleTailX(); ?>" />  </td>
			</tr>
			<tr>
			<td><p id="tailtipylabel"><?php echo __('Bubble Tail Tip Y Coordinate:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td><input id ="tailtipy" type="number" step="1" min="0" max="2000" name="bubble_tail_y" value="<?php echo $bubble->getBubbleTailY(); ?>" />  </td>
			</tr>
			<tr>
			<td><p id="caheightlabel"><?php echo __('Content Area Height:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td> <input id="caheight" type="number" step="1" min="10" max="2000" name="content_area_height" value="<?php echo $bubble->getContentAreaHeight(); ?>" />  </td>
			</tr>
			<tr>
			<td><p id="cawidthlabel"><?php echo __('Content Area Width:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td> <input id="cawidth" type="number" step="1" min="10" max="2000" name="content_area_width" value="<?php echo $bubble->getContentAreaWidth(); ?>" />  </td>
			</tr>
			<tr>	
			<td><p id="tpaddinglabel"><?php echo __('Content Area Text Padding:', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td> <input id="tpadding" type="number" step="1" min="1" max="2000" name="text_padding" value="<?php echo $bubble->getTextPadding(); ?>" />  </td>
			</tr>			
			<tr>
			<td><p id="delaylabel"><?php echo __('Bubble Delay (in ms):', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td> <input id="bubbledelay" type="number" step="1" min="0" max="31536000000" name="bubble_delay" value="<?php echo $bubble->getBubbleDelay(); ?>" />  </td>
			</tr>
			
			<tr>
			<td><p id="durationlabel"><?php echo __('Bubble Duration (in ms):', TNOTW_HB_TEXTDOMAIN ); ?></p></td>
			<td> <input id="bubbleduration" type="number" step="1" min="-1" max="31536000000"  name="bubble_duration" value="<?php echo $bubble->getBubbleDuration(); ?>" />  </td>
			</tr>
			
			<tr>
			<td><?php echo __('Target Image URL  :', TNOTW_HB_TEXTDOMAIN ); ?><img id="getpagelistind" alt="" style="visibility: hidden" src="<?php echo plugins_url('hoverbubble/assets/img/ajax-loader.gif') ;?>"/></td>		
			<td>
				<select id="imageurl" style="width: 80%"  name="target_image_url" >
				<option <?php if ( $action == 'edit' ) echo 'select'?>></option>
				<?php
					foreach ( self::$imageCandiditeList as $imageCandiate ) {
						$caTargetImageURL = $imageCandiate->getTargetImageURL();
						$bubbleTargetImageURL = $bubble->getTargetImageURL();						
						?>
						<option value="<?php echo $imageCandiate->getTargetImageURL() ;?>"<?php if ( $caTargetImageURL == $bubbleTargetImageURL ) echo 'selected' ?> > <?php echo $caTargetImageURL ;?> </option>
						<?php
					}
				?>
				</select>  
			</td>						
			</tr>
			<tr id="pageurlph" hidden><td><?php echo __('Page Display List:', TNOTW_HB_TEXTDOMAIN ); ?></td><td id="pageurlinput"></td></tr>
			<tr>
			<td><input type="submit" name="edit_bubble" value="<?php _e( 'Submit', TNOTW_HB_TEXTDOMAIN ); ?>" class="button-primary" />   <input type="reset" value="<?php _e('Reset', TNOTW_HB_TEXTDOMAIN ) ; ?>" class="button-primary" /></td>
			<td></td>
			</tr>
			</table>
			<input  id="bubbleidhid" type="hidden" name="bubble_id" value="<?php echo $bubble->getBubbleID(); ?>" />
			<input  id="bubbleauthorhid" type="hidden" name="bubble_author" value="<?php echo $bubble->getBubbleAuthor(); ?>" />	
			<input type="hidden" name="edit_action" value="<?php echo $action ?>" />
		</form>
	</div>
        <?php
		
	}
}

?>
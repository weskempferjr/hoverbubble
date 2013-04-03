<?php

require_once( TNOTW_HOVERBUBBLE_DIR . "includes/database/WPResources.php");


class AdminEditView {
	
	public static function displayBubbleEditPage( $bubble, $action, $statusMessage ) {
	?>
	<div class="wrap">
		<?php if ( $action == "edit") { ?>
			<p>Here is the hoverbubble edit page for bubble_id = <?php echo $bubble->getBubbleID(); ?></p>
		 <?php } ?>
		<form id="hbedit" action="" method="POST">
			<table>
			<tr>
			<td>Bubble Name:</td> 
	 		<td><input type="text" name="bubble_name" value="<?php echo $bubble->getBubbleName(); ?>" /> </td>
			</tr>
			<tr>
			<td>Bubble Message:</td>
			<td>
			<textarea class="theEditor" rows="10" cols="40" name="bubble_message" form="hbedit" ><?php echo $bubble->getBubbleMessage(); ?> </textarea> 		
			</td> 
			</tr>
			<tr>
			<td>Bubble Fill Color:</td> 
	 		<td><input class="colorfield" type="text" name="bubble_fill_color" value="<?php echo $bubble->getBubbleFillColor(); ?>" /> </td>
			</tr>
			<tr>
			<td>Bubble Tail Length:</td>
			<td><input type="text" name="bubble_tail_length" value="<?php echo $bubble->getBubbleTailLength(); ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Tail Direction:</td>
			<td>
			<table>
			<tr>
			<td >N <input type="radio" name="bubble_tail_direction" value ="N"  <?php if ($bubble->getBubbleTailDirection() == "N") echo 'checked' ; ?>/>&nbsp </td>
			<td>NE <input type="radio" name="bubble_tail_direction" value ="NE" <?php if ($bubble->getBubbleTailDirection() == "NE") echo 'checked' ; ?> />&nbsp </td>
			<td>E <input type="radio" name="bubble_tail_direction" value ="E"  <?php if ($bubble->getBubbleTailDirection() == "E") echo 'checked' ; ?> />&nbsp </td>
			<td>SE <input type="radio" name="bubble_tail_direction" value ="SE" <?php if ($bubble->getBubbleTailDirection() == "SE") echo 'checked' ; ?> /> &nbsp</td>
			<td>S <input type="radio" name="bubble_tail_direction" value ="S"  <?php if ($bubble->getBubbleTailDirection() == "S") echo 'checked' ; ?>/>&nbsp </td>
			<td>SW <input type="radio" name="bubble_tail_direction" value ="SW" <?php if ($bubble->getBubbleTailDirection() == "SW") echo 'checked' ; ?>/>&nbsp </td>
			<td>W <input type="radio" name="bubble_tail_direction" value ="W"  <?php if ($bubble->getBubbleTailDirection() == "W") echo 'checked' ; ?> />&nbsp </td>
			<td>NW <input type="radio" name="bubble_tail_direction" value ="NW" <?php if ($bubble->getBubbleTailDirection() == "NW") echo 'checked' ; ?> />&nbsp </td>
			</tr>
			</table>
			</td>
			</tr>
			<tr>
			<td>Bubble Outline Color:</td>
			<td><input class="colorfield" type="text" name="bubble_outline_color" value="<?php echo $bubble->getBubbleOutlineColor(); ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Outline Width:</td>
			<td><input type="text" name="bubble_outline_width" value="<?php echo $bubble->getBubbleOutlineWidth(); ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Corner Radius:</td>
			<td><input type="text" name="bubble_corner_radius" value="<?php echo $bubble->getBubbleCornerRadius(); ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Tail Tip X Coordinate:</td>
			<td><input type="text" name="bubble_tail_x" value="<?php echo $bubble->getBubbleTailX(); ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Tail Tip Y Coordinate:</td>
			<td><input type="text" name="bubble_tail_y" value="<?php echo $bubble->getBubbleTailY(); ?>" />  </td>
			</tr>
			<tr>
			<td>Canvas Border Style :</td>
			<td> <input type="text" name="canvas_border_style" value="<?php echo $bubble->getCanvasBorderStyle(); ?>" />  </td>
			</tr>
			<tr>
			<td>Content Area Height :</td>
			<td> <input type="text" name="content_area_height" value="<?php echo $bubble->getContentAreaHeight(); ?>" />  </td>
			</tr>
			<tr>
			<td>Content Area Width :</td>
			<td> <input type="text" name="content_area_width" value="<?php echo $bubble->getContentAreaWidth(); ?>" />  </td>
			</tr>
			<tr>
			<td>Target Image URL  :</td>
			<td><input type="text" name="target_image_url" value="<?php echo $bubble->getTargetImageURL(); ?>" />  </td>			
			</tr>
			<tr>
			<td><input type="submit" name="edit_bubble" value="Submit" class="button-primary" />
			</tr>
			</table>
			<input type="hidden" name="bubble_id" value="<?php echo $bubble->getBubbleID(); ?>" />
			<input type="hidden" name="edit_action" value="<?php echo $action ?>" />
		</form>
	</div>
        <?php
		
	}
}

?>
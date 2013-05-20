<?php

class AdminSettingsView {
	public static function displayBubbleSettingsPage( $bubbles, $settings, $statusMessage ) {
		
		$add_page_url = admin_url('admin.php?page=hoverbubble-edit');
		$add_page_url = add_query_arg(	"action", 
										"add", 
										$add_page_url);

		?>
		<div class="wrap">
			<h2><?php _e('Hover Bubbles', TNOTW_HB_TEXTDOMAIN ); ?> <a href="<?php echo $add_page_url; ?>" class="add-new-h2"><?php _e('Add New',TNOTW_HB_TEXTDOMAIN )?></a> </h2>
			<br>
			<?php
			if ( $statusMessage != "" ) {
			?>
				<p> <?php echo $statusMessage ; ?></p>			 
			<?php 
			}
			?>
	
			<br>
			<table class="widefat">
				<thead>
				<tr>
				<th><?php _e('Bubble ID', TNOTW_HB_TEXTDOMAIN )?></th>
				<th><?php _e('Name', TNOTW_HB_TEXTDOMAIN )?></th>
				<th><?php _e('Description', TNOTW_HB_TEXTDOMAIN) ?></th>
				</tr>
				</thead>
				<tfoot>
				<tr>
				<th><?php _e('Bubble ID', TNOTW_HB_TEXTDOMAIN )?></th>
				<th><?php _e('Name', TNOTW_HB_TEXTDOMAIN )?></th>
				<th><?php _e('Description', TNOTW_HB_TEXTDOMAIN) ?></th>
				</tr>
				</tfoot>
				<tbody>			
				<?php	
					foreach ( $bubbles as $bubble ) {
						$edit_page_url = admin_url('admin.php?page=hoverbubble-edit');
						
						$delete_page_url = add_query_arg(	"action", 
										"delete", 
										$edit_page_url);
	
						$edit_page_url = add_query_arg(	"action", 
										"edit", 
										$edit_page_url);
	
						$edit_page_url = add_query_arg(	"bubble_id", 
										$bubble->getBubbleID(), 
										$edit_page_url);
										
						$delete_page_url = add_query_arg(	"bubble_id", 
										$bubble->getBubbleID(), 
										$delete_page_url);
										
						$delete_page_url = wp_nonce_url( $delete_page_url, 'bubble_delete_bubble_id' . $bubble->getBubbleID() );
	
						?>
						<tr>
						<td><?php echo $bubble->getBubbleID(); ?> </td>
						<td><?php echo $bubble->getBubbleName(); ?> </td>
						<td><?php echo $bubble->getBubbleDescription(); ?> </td>
						<td><a href="<?php echo $edit_page_url?>"> <?php _e('Edit', TNOTW_HB_TEXTDOMAIN) ;?></a> | <a class="hbdelete" href="<?php echo $delete_page_url?>"> <?php _e('Delete', TNOTW_HB_TEXTDOMAIN) ;?></a></td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>			
			<br>
			<h3><?php _e('General Settings', TNOTW_HB_TEXTDOMAIN )?></h3>
			<h4><?php _e('Target Image List Generator', TNOTW_HB_TEXTDOMAIN )?></h4>
			<form id="hbsettings" action="" method="POST">
			<table style="width: 100%">
			<col  style="width: 15%">
			<col  style="width: 85%">
			<tr>
			<td><?php _e('Crawl Path:', TNOTW_HB_TEXTDOMAIN ); ?></td><td><input id="crawlpathinput" style="width: 80%" name="crawlpath" type="text" value="<?php echo $settings->getCrawlPath(); ?>"/></td>
			</tr>
			<tr>
			<td><?php _e('Exclusion List:', TNOTW_HB_TEXTDOMAIN); ?></td><td><input id="exclistinput" style="width: 80%" name="exclusionlist" type="text" value="<?php echo $settings->getExclusionList(); ?>"/></td>			
			</tr>
			<tr>
			<td><input type="submit" name="hb_settings" value="Submit" class="button-primary" />   <input type="reset" class="button-primary" /></td>
			</tr>
			</table>
			</form>
			<p><?php _e('Press this button to update the image table after installation, if the above settings are changed, or after adding new content.', TNOTW_HB_TEXTDOMAIN ); ?></p>
			<table>
			<tr>
			<td><button id="genimagetab" class="button-primary"><?php _e('Generate Image Table', TNOTW_HB_TEXTDOMAIN ) ?></button></td>
			<td><img id="genimagetabind" alt="" style="visibility: hidden" src="<?php echo plugins_url('hoverbubble/assets/img/ajax-loader.gif') ;?>"></td>
			</tr>
			</table>
		</div>
		<?php
		}
}
?>
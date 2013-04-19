<?php

class AdminSettingsView {
	public static function displayBubbleSettingsPage( $bubbles, $statusMessage ) {
		
		$add_page_url = admin_url('admin.php?page=hoverbubble-edit');
		$add_page_url = add_query_arg(	"action", 
										"add", 
										$add_page_url);

		?>
		<div class="wrap">
			<h2>Hover Bubbles <a href="<?php echo $add_page_url; ?>" class="add-new-h2">Add New</a> </h2>
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
				<th>Bubble ID</th>
				<th>Name</th>
				<th>Target Image</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
				<th>Bubble ID</th>
				<th>Name</th>
				<th>Target Image</th>
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
	
						?>
						<tr>
						<td><?php echo $bubble->getBubbleID(); ?> </td>
						<td><?php echo $bubble->getBubbleName(); ?> </td>
						<td><?php echo $bubble->getTargetImageID(); ?> </td>
						<td><a href="<?php echo $edit_page_url?>"> Edit</a> | <a class="hbdelete" href="<?php echo $delete_page_url?>"> Delete</a></td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>
			<br>
			<h3>General Settings</h3>
			<button id="genimagetab" class="button-secondary"/>Generate Image Table</button>
		</div>
		<?php
		}
}
?>
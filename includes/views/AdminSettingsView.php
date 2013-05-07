<?php

class AdminSettingsView {
	public static function displayBubbleSettingsPage( $bubbles, $settings, $statusMessage ) {
		
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
				<th>Description</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
				<th>Bubble ID</th>
				<th>Name</th>
				<th>Description</th>
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
						<td><a href="<?php echo $edit_page_url?>"> Edit</a> | <a class="hbdelete" href="<?php echo $delete_page_url?>"> Delete</a></td>
						</tr>
						<?php
					}
				?>
				</tbody>
			</table>			
			<br>
			<h3>General Settings</h3>
			<h4>Target Image List Generator</h4>
			<p>Before creating bubbles, generate a target image list. The target image list enables you to select 
			which image, and on which pages, a bubble will appear. The image list generator
			needs to know two things:
			</p>
			<ol>
			<li>The top level URLs to crawl for target images, as specified in the <i>Crawl Path</i> field. The crawl path should contain
			your site's top URL. If there is a page on which you want to target images and it is not linked anywhere within the link 
			heiarchy accessible from your site's home page, then it will need to be added to the crawl path.</li>
			<li>Which which types of content to skip when crawling your site as specified in the <i>Exclusion List</i> field. 
			This will typically be media files. To exclude a content type, add its file extension to the list, including the dot (".")</li>
			</ol>
			<p>Entries in both fields must be seperated by spaces. Hit the submit button to save your changes. <i>These settings should
			be set automically to reasonable defaults by the installation. Change them only as necessary.</i></p>
			<form id="hbsettings" action="" method="POST">
			<table style="width: 100%">
			<col  style="width: 15%">
			<col  style="width: 85%">
			<tr>
			<td>Crawl Path:</td><td><input id="crawlpathinput" style="width: 80%" name="crawlpath" type="text" value="<?php echo $settings->getCrawlPath(); ?>"/></td>
			</tr>
			<tr>
			<td>Exclusion List:</td><td><input id="exclistinput" style="width: 80%" name="exclusionlist" type="text" value="<?php echo $settings->getExclusionList(); ?>"/></td>			
			</tr>
			<tr>
			<td><input type="submit" name="hb_settings" value="Submit" class="button-primary" />   <input type="reset" class="button-primary" /></td>
			</tr>
			</table>
			</form>
			<p>Once the Crawl Path and Exclusion list are specified, press the Generate Image Button to generate the target image table. <i>The
			Generator must be run at least once immediately after installation.</i></p>
			<table>
			<tr>
			<td><button id="genimagetab" class="button-primary">Generate Image Table</button></td>
			<td><img id="genimagetabind" alt="" style="visibility: hidden" src="<?php echo plugins_url('hoverbubble/assets/img/ajax-loader.gif') ;?>"></td>
			</tr>
			</table>
		</div>
		<?php
		}
}
?>
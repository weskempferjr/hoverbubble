<?php
/**
 * Admin functionality
 **/

add_action('admin_menu', 'myplugin_menu_pages');

function myplugin_menu_pages() {
	// Add the top-level admin menu
	$page_title = 'Hover Bubble Plugin Setings';
	$menu_title = 'Hover Bubble';
	$capability = 'manage_options';
	$menu_slug = 'hoverbubble-settings';
	$function = 'hoverbubble_settings';
	add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function);

	// Add submenu page with same slug as parent to ensure no duplicates
	$sub_menu_title = 'Settings';
	add_submenu_page($menu_slug, $page_title, $sub_menu_title, $capability, $menu_slug, $function);
	
	// add_submenu_page(NULL, "Hover Bubble Edit", "Hover Bubble Edit", $capability, "hoverbubble-edit", "hoverbubble_edit");

	// Now add the submenu page for Help
	$submenu_page_title = 'Hover Bubble Plugin Help';
	$submenu_title = 'Help';
	$submenu_slug = 'hoverbubble-help';
	$submenu_function = 'hoverbubble_help';
	add_submenu_page($menu_slug, $submenu_page_title, $submenu_title, $capability, $submenu_slug, $submenu_function);

	// Add bubble edit page, parent slug set to NULL so that page does not show up menu.
	$edit_page_title = "Hover Bubble Edit";
	$edit_menu_title = "Hover Bubble Edit";
	$edit_slug = "hoverbubble-edit";
	$edit_function = "hoverbubble_edit";

	add_submenu_page(NULL, $edit_page_title, $edit_menu_title, $capability, $edit_slug, $edit_function);
}

function hoverbubble_edit() {
	if (!current_user_can('manage_options')) {
		wp_die('YOU do not have sufficient permissions to access this page.');
	}

	// Render the HTML for the Settings page or include a file that does
	generate_hoverbubble_edit_page();
}


function generate_hoverbubble_edit_page() {

	$bubble_id = $_GET['bubble_id'];
	echo '<div class="wrap"><p>Here is the hoverbubble edit page for bubble_id = ' . $bubble_id . '.</p></div>';

	global $wpdb ;
	$bubble = $wpdb->get_row( "SELECT * FROM $wpdb->hoverbubbles WHERE bubble_id = " . $bubble_id , ARRAY_A );
	if ( $bubble != NULL ){
		echo '<p>bubble_id = ' . $bubble['bubble_id'] . '</p>' ;
		echo '<p>bubble_canvas_id = ' . $bubble['bubble_canvas_id'] . '</p>' ;
		echo '<p>bubble_fill_color = ' . $bubble['bubble_fill_color'] . '</p>' ;
		echo '<p>bubble_font_color = ' . $bubble['bubble_font_color'] . '</p>' ;
		echo '<p>bubble_font = ' . $bubble['bubble_font'] . '</p>' ;
		echo '<p>bubble_text_align = ' . $bubble['bubble_text_align'] . '</p>' ;
		echo '<p>bubble_tail_length = ' . $bubble['bubble_tail_length'] . '</p>' ;
		echo '<p>bubble_padding = ' . $bubble['bubble_padding'] . '</p>' ;
		echo '<p>bubble_corner_radius = ' . $bubble['bubble_corner_radius'] . '</p>' ;
		echo '<p>bubble_outline_color = ' . $bubble['bubble_outline_color'] . '</p>' ;
		echo '<p>bubble_outline_width = ' . $bubble['bubble_outline_width'] . '</p>' ;
		echo '<p>bubble_tail_direction = ' . $bubble['bubble_tail_direction'] . '</p>' ;
		echo '<p>canvas_top_offset = ' . $bubble['canvas_top_offset'] . '</p>' ;
		echo '<p>canvas_left_offset = ' . $bubble['canvas_left_offset'] . '</p>' ;
		echo '<p>canvas_height = ' . $bubble['canvas_height'] . '</p>' ;
		echo '<p>canvas_width = ' . $bubble['canvas_width'] . '</p>' ;
		echo '<p>canvas_border_style= ' . $bubble['canvas_border_style'] . '</p>' ;
		echo '<p>bubble_message = ' . $bubble['bubble_message'] . '</p>' ;
		echo '<p>target_image_cntnr_id = ' . $bubble['target_image_cntnr_id'] . '</p>' ;
		echo '<p>target_image_id = ' . $bubble['target_image_id'] . '</p>' ;
	}
	else {
		echo '<p> Error: hoverbubble query returned NULL.</p>';
	}
	

}

function hoverbubble_settings() {
	if (!current_user_can('manage_options')) {
		wp_die('You do not have sufficient permissions to access this page.');
	}

	// Render the HTML for the Settings page or include a file that does
	generate_hoverbubble_settings_page();
}


function generate_hoverbubble_settings_page() {
	
	?>
	<div class="wrap">
		<h2>Hover Bubble Administration</h2>
		<h3>Click the search button below to search, view, edit, add, or delete bubbles.</h3>
		<br>
		<form action="" method="POST">
			<input type="submit" name="list_bubbles" value="Search" class="button-primary" />
		</form>
		<br>
		<table class="widefat">
			<thead>
			<tr>
			<th>Bubble ID</th>
			<th>Bubble Canvas ID</th>
			<th>Message</th>
			<th>Target Image</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
			<th>Bubble ID</th>
			<th>Bubble Canvas ID</th>
			<th>Message</th>
			<th>Target Image</th>
			</tr>
			</tfoot>
			<tbody>
			<?php			
				global $wpdb ;
				$bubbles = array();
				if (isset($_POST['list_bubbles'])) {
					$bubbles = $wpdb->get_results(
						"
						SELECT 	bubble_id, 
							bubble_canvas_id, 
							bubble_message, 
							target_image_id
						FROM $wpdb->hoverbubbles
						"
					);
					update_option('hoverbubbles_listing', $bubbles );
				}
				else if ( get_option('hoverbubbles_listing') ) {
					$bubbles = get_option('hoverbubbles_listing');
				}
				?>
				<?php	
				foreach ( $bubbles as $bubble ) {
					$edit_page_url = admin_url('admin.php?page=hoverbubble-edit');
					$edit_page_url = add_query_arg(	"bubble_id", 
									$bubble->bubble_id, 
									$edit_page_url);

					?>
					<tr>
					<?php
					echo "<td>" . $bubble->bubble_id . "</td>" ;
					echo "<td>" . $bubble->bubble_canvas_id . "</td>" ;
					echo "<td>" . $bubble->bubble_message . "</td>" ;
					echo "<td>" . $bubble->target_image_id . "</td>" ;
					echo "<td>" . "<a href='" . $edit_page_url . "'>Edit</a>" . "</td>" ;
					?>
					</tr>
					<?php
				}
			?>
			</tbody>
		</table>
	<div>
	<?php
}

function hoverbubble_help() {
	if (!current_user_can('manage_options')) {
		wp_die('You do not have sufficient permissions to access this page.');
	}

	// Render the HTML for the Help page or include a file that does
	generate_hoverbubble_help_page();
}

function generate_hoverbubble_help_page() {
	?>
	<div class="wrap"><p>Here is the hoverbubble help page.</p></div>
	<?php
}

?>

<?php
/**
 * Admin functionality
 **/

add_action('admin_menu', 'tnotw_hoverbubble_menu_pages');

function tnotw_hoverbubble_menu_pages() {
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
	
	// Add javascript
	add_action( 'admin_enqueue_scripts', 'tnotw_hoverbubble_admin_enqueue_scripts' );
}

function hoverbubble_edit() {
	if (!current_user_can('manage_options')) {
		wp_die('YOU do not have sufficient permissions to access this page.');
	}

	// Render the HTML for the Settings page or include a file that does
	if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
		handle_hoverbubble_edit();
	}
	else {
		tnontw_enqueue_scripts_internally();
		generate_hoverbubble_edit_page();
	}
}


function handle_hoverbubble_edit() {
	
	$edit_action =  $_POST['edit_action'];
	
	switch ( $edit_action ) {
		case "add":
			$ret_val = add_bubble();	
			break;
		case "edit":
			$ret_val = update_bubble();
			break;
		default:
			generate_error_page("Error: this is a bug. Unknown edit action");
			break;
	}
	if ( $ret_val != TRUE ) {		
		generate_error_page();
	}
	else {
		tnontw_enqueue_scripts_internally();
		generate_hoverbubble_settings_page("Update (" . $edit_action .  ")succeeded." );
		
		
	}
	
}

function generate_hoverbubble_edit_page() {

	$edit_action = $_GET['action'] ;
	
	switch ( $edit_action ) {
		case "add":
			$bubble = get_add_form_defaults();	
			break;
		case "edit":
			$bubble_id = $_GET['bubble_id'];
			// TODO: add error checking here
			$bubble = get_bubble_values( $bubble_id );
			break;
		case "delete":
			// Don't generate page but delete immediately. Assumes confirmation
			// on the client side.
			$bubble_id = $_GET['bubble_id'];
			if ( delete_bubble( $bubble_id ) ) {
				tnontw_enqueue_scripts_internally();
				generate_hoverbubble_settings_page("Delete of $bubble_id succeeded.");
				return ;
			} 
			else {
				generate_error_page("Delete operation on $bubble_id failed.");
				return;	
			}
			break;
		default:
			generate_error_page("Error: this is a bug. Unknown edit action");
			break;
	}

	
	if ( $bubble != NULL ){
	?>
	<div class="wrap">
		<p>Here is the hoverbubble edit page for bubble_id = <?php echo $bubble_id ?></p>
		<form id="hbedit" action="" method="POST">
			<table>
			<tr>
			<td>Bubble Message:</td>
			<td><input type="text" name="bubble_message" value="<?php echo $bubble['bubble_message'] ?>" /></td> 
			</tr>
			<tr>
			<td>Bubble Fill Color:</td> 
	 		<td><input class="colorfield" type="text" name="bubble_fill_color" value="<?php echo $bubble['bubble_fill_color'] ?>" /> </td>
			</tr>
			<tr>
			<td>Bubble Font:</td> 
			<td><input type="text" name="bubble_font" value="<?php echo $bubble['bubble_font'] ?>" /> </td>
			</tr>
			<tr>
			<td>Bubble Font Color:</td>
			<td> <input class="colorfield" type="text" name="bubble_font_color" value="<?php echo $bubble['bubble_font_color'] ?>" /> </td>
			</tr>
			<tr>
			<td>Bubble Text Align:</td>
			<td><input type="text" name="bubble_text_align" value="<?php echo $bubble['bubble_text_align'] ?>" /> </td>
			</tr>
			<tr>
			<td>Bubble Tail Length:</td>
			<td><input type="text" name="bubble_tail_length" value="<?php echo $bubble['bubble_tail_length'] ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Tail Direction:</td>
			<td> <input type="text" name="bubble_tail_direction" value="<?php echo $bubble['bubble_tail_direction'] ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Outline Color:</td>
			<td><input class="colorfield" type="text" name="bubble_outline_color" value="<?php echo $bubble['bubble_outline_color'] ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Outline Width:</td>
			<td><input type="text" name="bubble_outline_width" value="<?php echo $bubble['bubble_outline_width'] ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Corner Radius:</td>
			<td><input type="text" name="bubble_corner_radius" value="<?php echo $bubble['bubble_corner_radius'] ?>" />  </td>
			</tr>
			<tr>
			<td>Bubble Padding:</td>
			<td><input type="text" name="bubble_padding" value="<?php echo $bubble['bubble_padding'] ?>" />  </td>
			</tr>
			<tr>
			<td>Canvas Height:</td>
			<td><input type="text" name="canvas_height" value="<?php echo $bubble['canvas_height'] ?>" />  </td>
			</tr>
			<tr>
			<td>Canvas Width:</td>
			<td><input type="text" name="canvas_width" value="<?php echo $bubble['canvas_width'] ?>" />  </td>
			</tr>
			<tr>
			<td>Canvas Top Offset:</td>
			<td><input type="text" name="canvas_top_offset" value="<?php echo $bubble['canvas_top_offset'] ?>" />  </td>
			</tr>
			<tr>
			<td>Canvas Left Offset:</td>
			<td> <input type="text" name="canvas_left_offset" value="<?php echo $bubble['canvas_left_offset'] ?>" />  </td>
			</tr>
			<tr>
			<td>Canvas Border Style :</td>
			<td> <input type="text" name="canvas_border_style" value="<?php echo $bubble['canvas_border_style'] ?>" />  </td>
			</tr>
			<tr>
			<td>Target Image ID  :</td>
			<td><input type="text" name="target_image_id" value="<?php echo $bubble['target_image_id'] ?>" />  </td>
			</tr>
			<tr>
			<td>Target Image Container ID  :</td>
			<td><input type="text" name="target_image_cntnr_id" value="<?php echo $bubble['target_image_cntnr_id'] ?>" />  </td>
			</tr>
			<tr>
			<td><input type="submit" name="edit_bubble" value="Submit" class="button-primary" />
			</tr>
			<table>
			<input type="hidden" name="bubble_id" value="<?php echo $bubble_id ?>">
			<input type="hidden" name="edit_action" value="<?php echo $edit_action ?>">
		<form>
	</div>
        <?php
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


function generate_hoverbubble_settings_page( $status_message ) {
	
	$add_page_url = admin_url('admin.php?page=hoverbubble-edit');
	$add_page_url = add_query_arg(	"action", 
					"add", 
					$add_page_url);

	?>
	<div class="wrap">
		<h2>Hover Bubbles <a href="<?php echo $add_page_url; ?>" class="add-new-h2">Add New</a> </h2>
		<br>
		<?php
		if ( $status_message != "" ) {
		?>
			<p> <?php echo $status_message ; ?></p>			 
		<?php 
		}
		?>

		<br>
		<table class="widefat">
			<thead>
			<tr>
			<th>Bubble ID</th>
			<th>Message</th>
			<th>Target Image</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
			<th>Bubble ID</th>
			<th>Message</th>
			<th>Target Image</th>
			</tr>
			</tfoot>
			<tbody>
			<?php			
				global $wpdb ;
				$bubbles = array();
				$bubbles = $wpdb->get_results(
					"
					SELECT 	bubble_id, 
						bubble_message, 
						target_image_id
					FROM $wpdb->hoverbubbles
					"
				);
				
				?>
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
									$bubble->bubble_id, 
									$edit_page_url);
									
					$delete_page_url = add_query_arg(	"bubble_id", 
									$bubble->bubble_id, 
									$delete_page_url);

					?>
					<tr>
					<td><?php echo $bubble->bubble_id ?> </td>
					<td><?php echo $bubble->bubble_message ?> </td>
					<td><?php echo $bubble->target_image_id ?> </td>
					<td><a href="<?php echo $edit_page_url?>"> Edit</a> | <a class="hbdelete" href="<?php echo $delete_page_url?>"> Delete</a></td>
					</tr>
					<?php
				}
			?>
			</tbody>
		</table>
	</div>
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


function get_bubble_values ( $bubble_id ) {
	global $wpdb ;

	// TODO: error handling here
	$bubble = $wpdb->get_row( "SELECT * FROM $wpdb->hoverbubbles WHERE bubble_id = " . $bubble_id , ARRAY_A );
	return $bubble ;
}


function get_add_form_defaults() {
	$bubble = array(
		"bubble_message" => "",
		"bubble_fill_color" => "white",
		"bubble_font_color" => "black",
		"bubble_font" => "12x Arial",
		"bubble_text_align" => "center",
		"bubble_tail_length" => "30",
		"bubble_tail_direction" => "",
		"bubble_corner_radius" => "40",
		"bubble_padding" => "40",
		"bubble_outline_color" => "black",
		"bubble_outline_width" => "4",
		"canvas_top_offest" => "",
		"canvas_left_offset" => "",
		"canvas_width" => "150",
		"canvas_height" => "100",
		"canvas_border_style" => "0px solid #000000",
		"target_image_id" => "",
		"target_image_cntr_id" => ""
	);
	return $bubble;
}

function add_bubble() {
	global $wpdb ;
	$sql = $wpdb->prepare(
		"INSERT INTO $wpdb->hoverbubbles
			(	bubble_id,
				bubble_message,
				bubble_fill_color,
				bubble_font_color,
				bubble_font,
				bubble_text_align,
				bubble_tail_length,
				bubble_padding,
				bubble_corner_radius,
				bubble_outline_color,				
				bubble_outline_width,
				bubble_tail_direction,
				canvas_top_offset,
				canvas_left_offset,
				canvas_width,				
				canvas_height,
				canvas_border_style,
				target_image_id,
				target_image_cntnr_id
			) VALUES ( %d, %s, %s, %s, %s, %s, %d, %d, %d, %s, %d, %s, %d, %d, %d, %d, %s, %s, %s )
		",
		array(
			$_POST['bubble_id'],
			$_POST['bubble_message'],
			$_POST['bubble_fill_color'],
			$_POST['bubble_font_color'],
			$_POST['bubble_font'],
			$_POST['bubble_text_align'],
			$_POST['bubble_tail_length'],
			$_POST['bubble_padding'],
			$_POST['bubble_corner_radius'],
			$_POST['bubble_outline_color'],
			$_POST['bubble_outline_width'],
			$_POST['bubble_tail_direction'],
			$_POST['canvas_top_offset'],
			$_POST['canvas_left_offset'],
			$_POST['canvas_width'],
			$_POST['canvas_height'],
			$_POST['canvas_border_style'],
			$_POST['target_image_id'],
			$_POST['target_image_cntnr_id']
		)
	);
	$ret_val = $wpdb->query( $sql );
	return $ret_val;
		
}

function update_bubble() {
	global $wpdb ;
	$sql = $wpdb->prepare(
		"
			UPDATE $wpdb->hoverbubbles set
				bubble_message = %s,
				bubble_fill_color = %s,
				bubble_font_color = %s,
				bubble_font = %s,
				bubble_text_align = %s,
				bubble_tail_length = %d,
				bubble_padding = %d,
				bubble_corner_radius = %d,
				bubble_outline_color = %s,
				bubble_outline_width = %d,
				bubble_tail_direction = %s,
				canvas_top_offset = %d,
				canvas_left_offset = %d,
				canvas_width = %d,
				canvas_height = %d,
				canvas_border_style = %s,
				target_image_id = %s,
				target_image_cntnr_id = %s
			WHERE bubble_id = %d
		",
		array(		
			$_POST['bubble_message'],
			$_POST['bubble_fill_color'],
			$_POST['bubble_font_color'],
			$_POST['bubble_font'],
			$_POST['bubble_text_align'],
			$_POST['bubble_tail_length'],
			$_POST['bubble_padding'],
			$_POST['bubble_corner_radius'],
			$_POST['bubble_outline_color'],
			$_POST['bubble_outline_width'],
			$_POST['bubble_tail_direction'],
			$_POST['canvas_top_offset'],
			$_POST['canvas_left_offset'],
			$_POST['canvas_width'],
			$_POST['canvas_height'],
			$_POST['canvas_border_style'],
			$_POST['target_image_id'],
			$_POST['target_image_cntnr_id'],
			$_POST['bubble_id']
		)
	
	);
	$ret_val = $wpdb->query( $sql );
	return $ret_val ;
	
}

function delete_bubble() {
	global $wpdb ;
	
	$sql = $wpdb->prepare( 
				"DELETE FROM $wpdb->hoverbubbles WHERE bubble_id = %d", 
				array ($_GET['bubble_id'] ) 
			);
			
	$ret_val = $wpdb->query( $sql );
	return $ret_val ;
	
}

function generate_error_page( $message ) {
	global $wpdb ;
	
	?>
		<div class="wrap">
			<h2>Hover Bubble Database Error</h2>
			<p> <?php echo 'edit_action = ' . $_POST['edit_action']; ?></p>
			<p> <?php $wpdb->print_error() ;?></p>
		</div>	
	<?php
}

function tnotw_hoverbubble_admin_enqueue_scripts($hook) {

	if ( ! strpos( $hook, "hoverbubble-settings"))
		return;
	
	tnontw_enqueue_scripts_internally();
	//TODO: definition constant for path of assets/js
	// wp_enqueue_script( 'hoverbubble-form-js', 
	//	plugins_url('hoverbubble/assets/js/hoverbubble_form.js') , 
	//	array('jquery'));
}

function tnontw_enqueue_scripts_internally() {
	
	wp_enqueue_script( 'hoverbubble-form-js',
		 plugins_url() . '/hoverbubble/assets/js/hoverbubble_form.js',  array( 'wp-color-picker' ) );
		 // array('jquery'));

	//wp_enqueue_script( 'jquery',
	//	 plugins_url('hoverbubble/assets/js/jquery.1.9.1.js') );
		 
	//wp_enqueue_script( 'jquery-ui-core',
		 // plugins_url() . '/hoverbubble/assets/js/jquery.ui.core.min.js');
		 
	$plugins_url = plugins_url() ;	
		 
	wp_enqueue_script( 'jquery-ui-widget',
		$plugins_url . '/hoverbubble/assets/js/jquery.ui.widget.min.js' );
		 
	wp_enqueue_script( 'jquery-ui-button',
		$plugins_url . '/hoverbubble/assets/js/jquery.ui.button.min.js' );

	wp_enqueue_script( 'jquery-ui-position',
		$plugins_url . '/hoverbubble/assets/js/jquery.ui.position.min.js' );
		 
		 
	wp_enqueue_script( 'jquery-ui-dialog',
		$plugins_url . '/hoverbubble/assets/js/jquery.ui.dialog.min.js' );

			
	wp_enqueue_style('jquery.ui.theme', 
		$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery-ui.min.css');
		
	wp_enqueue_style('jquery.ui.core.theme', 
		$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.core.min.css');

	wp_enqueue_style('jquery.ui.theme.theme', 
		$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.theme.min.css');
	
	wp_enqueue_style('jquery.ui.button.theme', 
		$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.button.min.css');
		
		
	wp_enqueue_style('jquery.ui.dialog.theme', 
		$plugins_url . '/hoverbubble/assets/css/ui-lightness/minified/jquery.ui.dialog.min.css');
		
	// color picker
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker' );
	
		 
}

function tnotw_add_jquery_footer() {
	?>
	<div id="dialog" style="display: none" title="Basic dialog">
		<p>Delete this bubble?</p>
	</div>
	<?php
	
}

add_action('admin_footer', 'tnotw_add_jquery_footer');

?>

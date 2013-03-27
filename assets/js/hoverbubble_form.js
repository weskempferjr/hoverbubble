

jQuery(document).ready(function($) {
	$("#dialog").dialog({
		autoOpen: false,
		modal: true,
		open: function() {
				$(this).siblings('.ui-dialog-buttonpane').find('button:eq(1)').focus();
			},
		buttons: [ 
			{ text: "OK", click: function() { 
				$( this ).dialog( "close" );
				window.location = $(this).data('delete_target').href ;				
			}},
			{ text: "Cancel", click: function() { $( this ).dialog( "close" ); }} 
		]
	});

	$(".hbdelete").click(function(e) {
		e.preventDefault();	
		$("#dialog").data('delete_target', this );
		$("#dialog").dialog("open");			
	});
	
	// color picker
	$('.colorfield').wpColorPicker({
		change: function(event, ui) {
			var field_color = $('.colorfield').wpColorPicker("color");
			event.target.value = field_color ;
		},
		clear: function(event,ui) {

		 }
	      
	});
	
	tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        theme_advanced_buttons3_add : "fontselect,fontsizeselect",
        relative_urls : false,
        convert_urls : false,
        encoding: "raw"
	});
	
	// active image select list
	$("input[rel]").overlay();
	$("#target_image").overlay();

});

//function confirm_before_submit( form, message ) {
//	if ( confirm ( message )) {
//		form.submit();
//	}
//}
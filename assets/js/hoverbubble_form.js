

jQuery(document).ready(function($) {
	$("#dialog").dialog({
		autoOpen: false,
		modal: true,
		open: function() {
				$(this).siblings('.ui-dialog-buttonpane').find('button:eq(1)').focus();
			},
		buttons: [ 
			{ text: "Ok", click: function() { 
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
			// alert("Here we are.");
			// var rsp = confirm("Delete bubble?");
			//if ( rsp == true ) {
				// alert("yes");
				//window.location = jQuery(this).attr('href');
				// jQuery(function() {
				//	    jQuery( "#dialog" ).dialog({
				//	    	width: 600,
				//			height: 400
				//	    });
				// });
			//} 
			// else {
			//	alert("no");
			//	e.preventDefault();				
			//}
			
	});

});

//function confirm_before_submit( form, message ) {
//	if ( confirm ( message )) {
//		form.submit();
//	}
//}
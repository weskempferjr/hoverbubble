

jQuery(document).ready(function($) {
	
	
	$("#outlinewidth").blur( function(event) {
		validateNumberField("Outline Width:","outlinewidthlabel", "outlinewidth");
	});
	
	$("#taillength").blur( function(event) {
		validateNumberField("Bubble Tail Length:","taillengthlabel", "taillength");
	});
	
	$("#cornerradius").blur( function(event) {
		validateNumberField("Bubble Corner Radius:","cornerradiuslabel", "cornerradius");
	});

	$("#tailtipx").blur( function(event) {
		validateNumberField("Bubble Tail Tip X Coordinate:","tailtipxlabel", "tailtipx");
	});

	$("#tailtipy").blur( function(event) {
		validateNumberField("Bubble Tail Tip Y Coordinate:","tailtipylabel", "tailtipy");
	});
	
	$("#caheight").blur( function(event) {
		validateNumberField("Content Area Heigth:","caheightlabel", "caheight");
	});
	
	$("#cawidth").blur( function(event) {
		validateNumberField("Content Area Width:","cawidthlabel", "cawidth");
	});
	
	$("#bubblename").blur( function(event) {
		var bubble_name = $("#bubblename").val();
		if (bubble_name.length == 0) {
			$("#bubblenamelabel").html("Bubble Name required:");
			$("#bubblenamelabel").css("color","red");
			return;
		}
		else {
			$("#bubblenamelabel").html("Bubble Name:");
			$("#bubblenamelabel").css("color","black");
		}
		
		$.ajax({
			url:  wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
			data:{
				'action':'tnotw_hoverbubble_ajax',
				'fn':'does_bubble_name_exist',
				'bubble_name': bubble_name
	 		},
			dataType: 'JSON',
			success:function(data){
				if ( data.bubbleExists == true ) {
					// alert("A bubble with the name " + bubble_name + " already exists.");
					// $("#bubblename").focus();
					$("#bubblenamelabel").html("Duplicate bubble name!:");
					$("#bubblenamelabel").css("color","red");
					return;
				}
	        },
			error: function(errorThrown){
				alert('error');
				console.log(errorThrown);
	        }


		});
		
	
	});
	
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
        theme_advanced_buttons3_add : "fontselect,fontsizeselect,forecolor,backcolor",
        relative_urls : false,
        convert_urls : false,
        encoding: "raw"
	});
	
	// active image select list
	$("input[rel]").overlay();
	$("#target_image").overlay();
	
	
	function validateNumberField( labelText, labelID, inputID ) {
		var value = $("#" + inputID ).val();
		value = parseInt(value);
		var min = $("#" + inputID ).attr("min");
		var max = $("#" + inputID ).attr("max");
		if ( isNaN( value ) || (value < min || value > max ) ) {
			$("#" + labelID ).html( labelText + " must be a number from " + min + " to " + max + ":");
			$("#" + labelID ).css("color","red");
			return;
		}
		else {
			$("#" + labelID ).html( labelText );
			$("#" + labelID ).css("color","black");
		}	
	}
});





jQuery(document).ready(function($) {
	
	

	$("#genimagetab").click( function(e) {
		e.preventDefault();
		$("#genimagetabind").css({"visibility":"visible"});
		$.ajax({
			url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
			data:{
				'action':'tnotw_hoverbubble_ajax',
				'fn':'gen_site_image_list'
	 		},
			dataType: 'JSON',
			success:function(data){
				$("#genimagetabind").css({"visibility":"hidden"});
				if ( data.errorData != null && data.errorData == 'true' ) {
					reportError( data );
					return;
				}
				alert( objectl10n.genimagestatus + data.updateTablesStatus );
	        },
			error: function(errorThrown){
				$("#genimagetabind").css({"visibility":"hidden"});
				alert( objectl10n.gen_image_status_retrieve_error + errorThrown.responseText.substring(0,500) );
				console.log(errorThrown);
	        }
		});

	});
	
	$("#pageurlinput").change( function( e ) {
		var selElems = $("#pageurlinput :selected").toArray();
		if ( selElems.length > 1) {
			$("#pageurlinput option[value='None']").prop('selected',false);
		}
		
	});
	
	$("#imageurl").change( function(e) {
		
		// get selection
		var targetImageURL = $("#imageurl :selected").text();
		if ( targetImageURL == "" || targetImageURL == null ) {
			return ;
		}
		
		var bubbleID = $("#bubbleidhid").val();
		
		$.ajax({
			url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
			data:{
				'action':'tnotw_hoverbubble_ajax',
				'fn':'get_page_candidate_list',
				'target_image_url': targetImageURL,
				'bubble_id' : bubbleID
	 		},
			dataType: 'JSON',
			success:function(data){
				if ( data.errorData != null && data.errorData == 'true' ) {
					reportError( data );
					return;
				}
				displayPageCandidateSelect( data );
	        },
			error: function(errorThrown){
				alert( objectl10n.page_candidate_retrieve_error + errorThrown.responseText.substring(0,500) );
				console.log(errorThrown);
	        }


		});
		$("#pageurlph").show();
	});
	
	$("#outlinewidth").blur( function(event) {
		validateNumberField( objectl10n.outline_width_label ,"outlinewidthlabel", "outlinewidth");
	});
	
	$("#taillength").blur( function(event) {
		validateNumberField( objectl10n.tail_length_label ,"taillengthlabel", "taillength");
	});
	
	$("#cornerradius").blur( function(event) {
		validateNumberField( objectl10n.corner_radius_label,"cornerradiuslabel", "cornerradius");
	});

	$("#tailtipx").blur( function(event) {
		validateNumberField( objectl10n.tail_tip_x_label ,"tailtipxlabel", "tailtipx");
	});

	$("#tailtipy").blur( function(event) {
		validateNumberField( objectl10n.tail_tip_y_label ,"tailtipylabel", "tailtipy");
	});
	
	$("#caheight").blur( function(event) {
		validateNumberField( objectl10n.content_area_height_label ,"caheightlabel", "caheight");
	});
	
	$("#cawidth").blur( function(event) {
		validateNumberField( objectl10n.content_area_width_label, "cawidthlabel", "cawidth");
	});
	
	$("#bubbledelay").blur( function(event) {
		validateNumberField( objectl10n.delay_label, "delaylabel", "bubbledelay");
	});
	
	$("#bubbleduration").blur( function(event) {
		validateNumberField( objectl10n.duration_label, "durationlabel", "bubbleduration");
	});
	

	
	$("#bubblename").blur( function(event) {
		var bubble_name = $("#bubblename").val();
		if (bubble_name.length == 0) {
			// l10n
			$("#bubblenamelabel").html( objectl10n.bubble_name_req_label );
			$("#bubblenamelabel").css("color","red");
			return;
		}
		else {
			// l10n
			$("#bubblenamelabel").html( objectl10n.bubble_name_label );
			$("#bubblenamelabel").css("color","black");
		}
		
		$.ajax({
			url:  objectl10n.wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
			data:{
				'action':'tnotw_hoverbubble_ajax',
				'fn':'does_bubble_name_exist',
				'bubble_name': bubble_name
	 		},
			dataType: 'JSON',
			success:function(data){
				
				if ( data.errorData != null && data.errorData == 'true' ) {
					reportError( data );
					return;
				}
				
				if ( data.bubbleExists == true ) {
					$("#bubblenamelabel").html( objectl10n.dup_bubble_name_label );
					$("#bubblenamelabel").css("color","red");
					return;
				}
	        },
			error: function(errorThrown){
				alert( objectl10n.check_bubble_avail_error + errorThrown.responseText.substring(0,500) );
				console.log(errorThrown);
	        }


		});
		
	
	});
	// l10n text
	$("#dialog").dialog({
		autoOpen: false,
		modal: true,
		title: objectl10n.confirm_delete_title,
		open: function() {
				$(this).siblings('.ui-dialog-buttonpane').find('button:eq(1)').focus();
			},
		buttons: [ 
			{ text: objectl10n.ok , click: function() { 
				$( this ).dialog( "close" );
				window.location = $(this).data('delete_target').href ;				
			}},
			{ text: objectl10n.cancel, click: function() { $( this ).dialog( "close" ); }} 
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
        editor_selector : "theEditor",
        theme_advanced_buttons3_add : "forecolor,backcolor,fontselect,fontsizeselect",
        relative_urls : false,
        convert_urls : false,
        encoding: "raw"
	});
	
	// Trigger change on load to display page candidate
	// list when in edit mode. 
	$("#imageurl").trigger("change");
	
	
	// l10n
	function validateNumberField( labelText, labelID, inputID ) {
		var value = $("#" + inputID ).val();
		value = parseInt(value);
		var min = $("#" + inputID ).attr("min");
		var max = $("#" + inputID ).attr("max");
		if ( isNaN( value ) || (value < min || value > max ) ) {
			$("#" + labelID ).html( labelText + " " + objectl10n.must_be_num + " " + min + " - " + max + ":");
			$("#" + labelID ).css("color","red");
			return;
		}
		else {
			$("#" + labelID ).html( labelText );
			$("#" + labelID ).css("color","black");
		}	
	}
	
	function displayPageCandidateSelect( data ) {
		
		// remove old list 
		$(".pcseloptions").remove();
		$("#pcselect").remove();
		
		$("#pageurlinput").append('<select id="pcselect" name="bubble_pages[]" multiple></select>');
		$("#pcselect").append( '<option class="pcseloptions" value="None">None</option>' );
		
		var pageCandidates = data.pageCandidates ;
		var displayPageIDs = data.displayPageIDs ;
		
		var somethingSelected = false ;
		
		for ( i = 0 ; i <	pageCandidates.length ; i++ )
		{
			var pageCandidate = pageCandidates[i];
			var optionValue = pageCandidate.pageCandidateID ;
			var displayValue = pageCandidate.targetPageURL ;
			
			var selectMe = "";
			if ( displayPageIDs.indexOf( optionValue ) != -1 ) {
				selectMe = " selected " ;
				somethingSelected = true; 
			}
			var optionStr = '<option class="pcseloptions" value="' + optionValue  + '"' +  selectMe +  '>' + displayValue + '</option>';
			$("#pcselect").append( optionStr );
		} 
		
		// If no displayed pages, force selection to none.
		if ( somethingSelected == false ) {
			$("#pageurlinput option[value='" + objectl10n.none + "']").prop('selected',true);
		}
		
	}
	
	function reportError( errorData ) {
		var errorString = objectl10n.server_error + errorData.errorMessage ;
		alert( errorString );
	}
});



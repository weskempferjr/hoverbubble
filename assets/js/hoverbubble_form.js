

jQuery(document).ready(function($) {
	
	

	$("#genimagetab").click( function(e) {
		e.preventDefault();
		$.ajax({
			url:  wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
			data:{
				'action':'tnotw_hoverbubble_ajax',
				'fn':'gen_site_image_list'
	 		},
			dataType: 'JSON',
			success:function(data){
				if ( data.errorData != null && data.errorData == 'true' ) {
					reportError( data );
					return;
				}

				alert("Gen image table status = " + data.updateTablesStatus );
	        },
			error: function(errorThrown){
				alert('Error retrieving gen image table status from server:' + errorThrown.responseText.substring(0,500) );
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
		// alert("ImageURL select callback executed");
		
		// get selection
		var targetImageURL = $("#imageurl :selected").text();
		if ( targetImageURL == "" || targetImageURL == null ) {
			return ;
		}
		
		var bubbleID = $("#bubbleidhid").val();
		
		$.ajax({
			url:  wpsiteinfo.site_url + '/wp-admin/admin-ajax.php',
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
				alert('Error retrieving page candidate list from server:' + errorThrown.responseText.substring(0,500) );
				console.log(errorThrown);
	        }


		});
		$("#pageurlph").show();
	});
	
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
				
				if ( data.errorData != null && data.errorData == 'true' ) {
					reportError( data );
					return;
				}
				
				if ( data.bubbleExists == true ) {

					$("#bubblenamelabel").html("Duplicate bubble name!:");
					$("#bubblenamelabel").css("color","red");
					return;
				}
	        },
			error: function(errorThrown){
				alert('Error checking bubble name availability:' + errorThrown.responseText.substring(0,500) );
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
        theme_advanced_buttons3_add : "forecolor,backcolor,fontselect,fontsizeselect",
        relative_urls : false,
        convert_urls : false,
        encoding: "raw"
	});
	
	// Trigger change on load to display page candidate
	// list when in edit mode. 
	$("#imageurl").trigger("change");
	
	
	
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
			$("#pageurlinput option[value='None']").prop('selected',true);
		}
		
	}
	
	function reportError( errorData ) {
		var errorString = "Server error:" + errorData.errorMessage ;
		alert( errorString );
	}
});



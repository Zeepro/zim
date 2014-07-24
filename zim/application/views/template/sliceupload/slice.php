<div data-role="page" data-url="/sliceupload/slice">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="#" onclick="javascript:window.location.href='/';" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div id="preview_zone" style="clear: both; text-align: center;">{wait_preview}</div>
			<div id="detail_zone" style="clear: both; text-align: center;">
				<h3><label for="preset_menu">{select_hint}</label></h3>
				<div data-role="fieldcontain">
					<select name="preset_menu" id="preset_menu">
					{preset_list}
						<option value="{id}">{name}</option>
					{/preset_list}
						<option value="_GOTO_PRESET_LIST_">{goto_preset}</option>
					</select>
				</div>
				<a href="#" id="slice_button" class="ui-disabled" data-role="button" onclick="javascript: startSlice();">{slice_button}</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
<!--
var var_stage = "{current_stage}";
var var_preview;
var var_slice;
var var_slice_status;
var var_slice_status_lock = false;
var var_slice_interval;
var var_model_change;
var var_current_rho = {value_rho};
var var_current_delta = {value_delta};
var var_current_theta = {value_theta};
var var_interval_rho = 100;

var var_model_scale = 100;
var var_model_zrot = 0;
var var_interval_scale = 50;
var var_interval_zrot = 30;


$(document).ready(prepareDisplay());

function prepareDisplay() {
	if (var_stage == "wait_slice") {
		getPreview();
		// set goto preset listener
		$("select#preset_menu").change(function() {
			if ($(this).val() == "_GOTO_PRESET_LIST_") {
				var user_input = confirm("{goto_hint}");
				if (user_input == true) {
					window.location.href="/preset";
				}
			}
		});
	}
	else if (var_stage == "wait_print") {
		// try to get sliced info
		$("#detail_zone").html("");
		getPreview(false);
		getSlice();
	}
	else {
		// treat error
		alert("unable to reach here");
	}

	return;
}

function getPreview(var_control) {
	if (var_slice_status_lock == true) {
		return;
	}
	else {
		var_slice_status_lock = true;
	}
	
	var_control = typeof var_control !== 'undefined' ? var_control : true;
	
	$("a#slice_button").addClass("ui-disabled");
	var_preview = $.ajax({
		url: "/sliceupload/preview_ajax",
		type: "GET",
		data: {
			rho: var_current_rho,
			delta: var_current_delta,
			theta: var_current_theta,
			},
		cache: false,
		beforeSend: function()
		{
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
        },
        complete: function()
        {	
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
        },
		timeout: 1000*60*10,
	})
	.done(function(html) {
		// html => link to image
		var var_html = '<img src="' + html + '"><br>';
		var_html = var_html
		+ '<div id="control_general_group">'
			+ '<button id="preview_near_button" data-inline="true" data-icon="minus" data-iconpos="left"'
			+ ' onclick="javascript: getPreviewNear(' + var_control + ');" class="ui-btn-hidden" data-disabled="false">{near_button}</button>'
			+ '<button id="preview_far_button" data-inline="true" data-icon="plus" data-iconpos="left"'
			+ ' onclick="javascript: getPreviewFar(' + var_control + ');" class="ui-btn-hidden" data-disabled="false">{far_button}</button>'
		+ '</div>';
		if (var_control == true) {
			var_html = var_html + '<div class="ui-grid-b" id="control_grid">'
			+ '<div class="ui-block-a"><div class="ui-bar ui-bar-f" id="xrot_grid">'
				+ '<button id="model_xrotminus_button" data-inline="true" data-icon="minus" data-iconpos="notext"'
				+ ' onclick="javascript: changeModel(\'xrot-\');" class="ui-btn-hidden" data-disabled="false">xrot add</button>'
				+ '<br><br>X<br><br>'
				+ '<button id="model_xrotadd_button" data-inline="true" data-icon="plus" data-iconpos="notext"'
				+ ' onclick="javascript: changeModel(\'xrot+\');" class="ui-btn-hidden" data-disabled="false">xrot minus</button>'
			+ '</div></div>'
			+ '<div class="ui-block-b"><div class="ui-bar ui-bar-f" id="yrot_grid">'
				+ '<button id="model_yrotminus_button" data-inline="true" data-icon="minus" data-iconpos="notext"'
				+ ' onclick="javascript: changeModel(\'yrot-\');" class="ui-btn-hidden" data-disabled="false">yrot add</button>'
				+ '<br><br>Y<br><br>'
				+ '<button id="model_yrotadd_button" data-inline="true" data-icon="plus" data-iconpos="notext"'
				+ ' onclick="javascript: changeModel(\'yrot+\');" class="ui-btn-hidden" data-disabled="false">yrot minus</button>'
			+ '</div></div>'
			+ '<div class="ui-block-c"><div class="ui-bar ui-bar-f" id="zrot_grid">'
				+ '<button id="model_zrotminus_button" data-inline="true" data-icon="minus" data-iconpos="notext"'
				+ ' onclick="javascript: changeModel(\'zrot-\');" class="ui-btn-hidden" data-disabled="false">zrot add</button>'
				+ '<br><br>Z<br><br>'
				+ '<button id="model_zrotadd_button" data-inline="true" data-icon="plus" data-iconpos="notext"'
				+ ' onclick="javascript: changeModel(\'zrot+\');" class="ui-btn-hidden" data-disabled="false">zrot minus</button>'
			+ '</div></div>'
			+ '</div>';
		}
		$("#preview_zone").html(var_html);
		$("a#slice_button").removeClass("ui-disabled");
		$('button#preview_near_button').button().button('refresh');
		$('button#preview_far_button').button().button('refresh');
		if (var_control == true) {
			$('#control_general_group').append(
					'<button id="model_small_button" data-inline="true" data-icon="minus" data-iconpos="left"'
					+ ' onclick="javascript: changeModel(\'s-\');" class="ui-btn-hidden" data-disabled="false">{small_button}</button>'
					+ '<button id="model_big_button" data-inline="true" data-icon="plus" data-iconpos="left"'
					+ ' onclick="javascript: changeModel(\'s+\');" class="ui-btn-hidden" data-disabled="false">{big_button}</button>'
					);
			$('button#model_small_button').button().button('refresh');
			$('button#model_big_button').button().button('refresh');
			$('button#model_xrotminus_button').button().button('refresh');
			$('button#model_xrotadd_button').button().button('refresh');
			$('button#model_yrotminus_button').button().button('refresh');
			$('button#model_yrotadd_button').button().button('refresh');
			$('button#model_zrotminus_button').button().button('refresh');
			$('button#model_zrotadd_button').button().button('refresh');
			
// 			$('<button>').appendTo('#cartridge_detail_info')
// 			.attr({'id': 'prime_button', 'onclick': 'javascript: window.location.href="/printdetail/printprime?v={abb_cartridge}";'})
// 			.html('{prime_button}').button().button('refresh');
		}
	})
	.fail(function() { // not allowed
		alert("failed preview");
		$("#preview_zone").html("failed");
	})
	.always(function() {
		var_slice_status_lock = false;
	});

	return;
}

function getPreviewNear(var_control) {
	var_control = typeof var_control !== 'undefined' ? var_control : true;
	var_current_rho = var_current_rho - var_interval_rho;
	if (var_current_rho < 0) {
		var_current_rho = 0;
	}
	getPreview(var_control);

	return;
}

function getPreviewFar(var_control) {
	var_control = typeof var_control !== 'undefined' ? var_control : true;
	var_current_rho = var_current_rho + var_interval_rho;
	if (var_current_rho > 5000) {
		var_current_rho = 5000;
	}
	getPreview(var_control);

	return;
}

function changeModel(var_action) {
	var var_model_id = 0;
	var var_ajax_data = {};
	
	var_action = typeof var_action !== 'undefined' ? var_action : 'error';
	
	if (var_slice_status_lock == true) {
		return;
	}
	else {
		var_slice_status_lock = true;
	}
	
	switch (var_action) {
		case 'zrot+':
			var_model_zrot += var_interval_zrot;
			var_ajax_data['zrot'] = var_model_zrot;
			break;
			
		case 'zrot-':
			var_model_zrot -= var_interval_zrot;
			var_ajax_data['zrot'] = var_model_zrot;
			break;
			
		case 's+':
			var_model_scale += var_interval_scale;
			var_ajax_data['s'] = var_model_scale;
			break;
			
		case 's-':
			var_model_scale -= var_interval_scale;
			var_ajax_data['s'] = var_model_scale;
			break;
			
		case 'error':
		default:
			return; 
	}
	var_ajax_data['id'] = var_model_id;
	
	var_model_change = $.ajax({
		url: "/sliceupload/preview_change_ajax",
		type: "GET",
		cache: false,
		data: var_ajax_data,
	})
	.done(function(html) {
		var_slice_status_lock = false;
		getPreview();
	})
	.fail(function() { // not allowed
		alert('failed');
		
		// reverse the original state
		switch (var_action) {
			case 'zrot+':
				var_model_zrot -= var_interval_zrot;
				break;
				
			case 'zrot-':
				var_model_zrot += var_interval_zrot;
				break;
				
			case 's+':
				var_model_scale -= var_interval_scale;
				break;
				
			case 's-':
				var_model_scale += var_interval_scale;
				break;
				
			case 'error':
			default:
				return; 
		}
	})
	.always(function() {
		var_slice_status_lock = false;
	});
	
	return;
}

function startSlice() {
	if (var_slice_status_lock == true) {
		return;
	}
	else {
		var_slice_status_lock = true;
	}
	
	var var_id_preset = $("select#preset_menu").val();
	
	// disable slice button
	$("a#slice_button").addClass("ui-disabled");
	$("#preview_zone").hide();
	
	$("#detail_zone").html("{wait_slice}");
	var_slice = $.ajax({
		url: "/sliceupload/slice_model_ajax",
		type: "GET",
		data: {
			id: var_id_preset,
		},
		cache: false,
	})
	.done(function(html) {
		// html => link to image
		var_slice_status_lock = false;
		window.location.href='/sliceupload/slicestatus';
// 		checkSlice(); // launch checking directly
// 		var_slice_interval = setInterval(checkSlice, 3000);
	})
	.fail(function() { // not allowed
		alert("failed slice");
		$("#detail_zone").html("failed");
	})
	.always(function() {
		var_slice_status_lock = false;
	});
}

function checkSlice() {
	if (var_slice_status_lock == true) {
		return;
	}
	else {
		var_slice_status_lock = true;
	}
	
	var_slice_status = $.ajax({
		url: "/sliceupload/slice_status_ajax",
		type: "GET",
		cache: false,
	})
	.done(function(html) {
		if (var_slice_status.status == 202) { // finished checking, wait user to input
			clearInterval(var_slice_interval);
			$("#preview_zone").show();
			getPreview(false);
			$("#detail_zone").html(html);
		}
		else if (var_slice_status.status == 200) { // in checking
			// html => percentage
			$("#detail_zone").html("{wait_in_slice} " + html);
		}
	})
	.fail(function() { // not allowed
		window.location.replace("/");
//			clearInterval(var_refreshChangeStatus);
//			$("#print_detail_info").html('<p>{finish_info}</p>');
//			$('button#print_action').click(function(){window.location.href='/'; return false;});
//			$('button#print_action').parent().find('span.ui-btn-text').text('{return_button}');
//			$('button#print_action').html('{return_button}');
//			alert("failed");
	})
	.always(function() {
		var_slice_status_lock = false;
	});
	
	return;
}

function getSlice() {
	var_slice_status = $.ajax({
		url: "/sliceupload/slice_status_ajax",
		type: "GET",
		data: {
			callback: 1,
		},
		cache: false,
	})
	.done(function(html) {
		if (var_slice_status.status == 202) { // finished checking, wait user to input
			$("#detail_zone").html(html);
		}
		else if (var_slice_status.status == 200) { // in checking
			$("#detail_zone").html("{wait_in_slice} " + html);
			getPreview(false); // redo previewing for changing color to cartridge's one
		}
	})
	.fail(function() { // not allowed
		window.location.replace("/");
//			clearInterval(var_refreshChangeStatus);
//			$("#print_detail_info").html('<p>{finish_info}</p>');
//			$('button#print_action').click(function(){window.location.href='/'; return false;});
//			$('button#print_action').parent().find('span.ui-btn-text').text('{return_button}');
//			$('button#print_action').html('{return_button}');
//			alert("failed");
	})
	.always(function() {
		var_slice_status_lock = false;
	});
}

-->
</script>
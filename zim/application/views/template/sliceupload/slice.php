<div data-role="page" data-url="sliceupload/slice">
	<header data-role="header" class="page-header"></header>
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
	getPreview(var_current_rho, var_current_delta, var_current_theta);
	if (var_stage == "wait_slice") {
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
		getSlice();
	}
	else {
		// treat error
		alert("unable to reach here");
	}

	return;
}

function getPreview() {
	if (var_slice_status_lock == true) {
		return;
	}
	else {
		var_slice_status_lock = true;
	}
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
		timeout: 1000*60*10,
	})
	.done(function(html) {
		// html => link to image
		$("#preview_zone").html('<img src="' + html + '"><br>');
		$("a#slice_button").removeClass("ui-disabled");
		$('<button>').appendTo('#preview_zone')
		.attr({'id': 'prime_button', 'data-inline': 'true', 'data-icon': 'minus', 'data-iconpos': 'notext',
			'onclick': 'javascript: getPreviewNear();'}).html('Near')
		.button().button('refresh');
		$('<button>').appendTo('#preview_zone')
		.attr({'id': 'preview_near_button', 'data-inline': 'true', 'data-icon': 'minus', 'data-iconpos': 'left',
			'onclick': 'javascript: getPreviewNear();'}).html('{near_button}')
		.button().button('refresh');
		$('<button>').appendTo('#preview_zone')
		.attr({'id': 'preview_far_button', 'data-inline': 'true', 'data-icon': 'plus', 'data-iconpos': 'left',
			'onclick': 'javascript: getPreviewFar();'}).html('{far_button}')
		.button().button('refresh');
// 		$('<button>').appendTo('#cartridge_detail_info')
// 		.attr({'id': 'prime_button', 'onclick': 'javascript: window.location.href="/printdetail/printprime?v={abb_cartridge}";'}).html('{prime_button}')
// 		
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

function getPreviewNear() {
	var_current_rho = var_current_rho - var_interval_rho;
	if (var_current_rho < 0) {
		var_current_rho = 0;
	}
	getPreview();

	return;
}

function getPreviewFar() {
	var_current_rho = var_current_rho + var_interval_rho;
	if (var_current_rho > 5000) {
		var_current_rho = 5000;
	}
	getPreview();

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
		checkSlice(); // launch checking directly
		var_slice_interval = setInterval(checkSlice, 3000);
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
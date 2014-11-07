<div data-role="page" data-url="/pronterface">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>Position</h4>
				<table style="text-align:center;margin:0 auto;">
				<tr>
					<td><a href="#" data-role="button" data-icon="home" data-iconpos="left" data-inline="true" onclick="home();">XYZ</a></td>
					<td colspan="2"></td>
					<td><a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="move('Y', 50);">50</a></td>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="move('Z', -50);">50</a></td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="move('Y', 10);">10</a></td>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="move('Z', -10);">10</a></td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="move('Y', 1);">1</a></td>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="move('Z', -1);">1</a></td>
				</tr>
				<tr>
					<td><a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" data-inline="true" onclick="move('X', -50);">50</a></td>
					<td><a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" data-inline="true" onclick="move('X', -10);">10</a></td>
					<td><a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" data-inline="true" onclick="move('X', -1);">1</a></td>
					<td><input type="number" style="text-align:right;" data-clear-btn="false" name="xy_speed" id="xy_speed" value="2000"></td>
					<td><a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" data-inline="true" onclick="move('X', 1);">1</a></td>
					<td><a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" data-inline="true" onclick="move('X', 10);">10</a></td>
					<td><a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" data-inline="true" onclick="move('X', 50);">50</a></td>
					<td><input type="number" style="text-align:right;" data-clear-btn="false" name="z_speed" id="z_speed" value="500"></td>
				</tr>
				<tr>
					<td><a href="#" data-role="button" data-icon="home" data-iconpos="left" data-inline="true" onclick="home('X');">X</a></td>
					<td colspan="2"></td>
					<td><a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="move('Y', -1);">1</a></td>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="move('Z', 1);">1</a></td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="move('Y', -10);">10</a></td>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="move('Z', 10);">10</a></td>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td><a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="move('Y', -50);">50</a></td>
					<td><a href="#" data-role="button" data-icon="home" data-iconpos="left" data-inline="true" onclick="home('Y');">Y</a></td>
					<td></td>
					<td><a href="#" data-role="button" data-icon="home" data-iconpos="left" data-inline="true" onclick="home('Z');">Z</a></td>
					<td><a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="move('Z', 50);">50</a></td>
				</tr>
				</table>
				<div data-role="collapsible" data-collapsed="true" style="align: center;">
					<h4>Leveling</h4>
					<table style="text-align:center;margin:0 auto;">
					<tr>
						<td><a href="#" data-role="button" data-icon="grid" data-iconpos="left" data-inline="true" onclick="level('xmin_ymax');">Move</a></td>
						<td></td>
						<td><a href="#" data-role="button" data-icon="grid" data-iconpos="left" data-inline="true" onclick="level('xmax_ymax');">Move</a></td>
					</tr>
					<tr>
						<td></td>
						<td><a href="#" data-role="button" data-icon="grid" data-iconpos="left" data-inline="true" onclick="level('center');">Move</a></td>
						<td></td>
					</tr>
					<tr>
						<td><a href="#" data-role="button" data-icon="grid" data-iconpos="left" data-inline="true" onclick="level('xmin_ymin');">Move</a></td>
						<td></td>
						<td><a href="#" data-role="button" data-icon="grid" data-iconpos="left" data-inline="true" onclick="level('xmax_ymin');">Move</a></td>
					</tr>
					</table>
				</div>
			</div>
			<div data-role="collapsible" data-collapsed="true" style="align: center;" id="temper_collaspible">
				<h4>Temperature</h4>
				<div class="ui-grid-a">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f">
							<label for="temper_l">Left</label>
							<div style="width:40px;float:left;margin-right:10px;">
								<input type="text" data-clear-btn="false" name="temper_l_current" id="temper_l_current" value="0" disabled>
							</div>
							<div style="width:60px;float:left;margin-right:10px;">
								<input type="number" style="text-align:right;" data-clear-btn="false" name="temper_l" id="temper_l" value="200">
							</div>
							<a href="#" data-role="button" data-icon="check" data-iconpos="left" data-inline="true" onclick="heat('l');">Set</a>
							<a href="#" data-role="button" data-icon="delete" data-iconpos="left" data-inline="true" onclick="heat('l', 0);">Stop</a>
							<a href="#" data-role="button" data-icon="refresh" data-iconpos="left" data-inline="true" onclick="refreshCheckTemper();">Refresh</a>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f">
							<label for="temper_r">Right</label>
							<div style="width:40px;float:left;margin-right:10px;">
								<input type="text" data-clear-btn="false" name="temper_r_current" id="temper_r_current" value="0" disabled>
							</div>
							<div style="width:60px;float:left;margin-right:10px;">
								<input type="number" style="text-align:right;" data-clear-btn="false" name="temper_r" id="temper_r" value="200">
							</div>
							<a href="#" data-role="button" data-icon="check" data-iconpos="left" data-inline="true" onclick="heat('r');">Set</a>
							<a href="#" data-role="button" data-icon="delete" data-iconpos="left" data-inline="true" onclick="heat('r', 0);">Stop</a>
							<a href="#" data-role="button" data-icon="refresh" data-iconpos="left" data-inline="true" onclick="refreshCheckTemper();">Refresh</a>
						</div>
					</div>
				</div>
			</div>
			<div data-role="collapsible" data-collapsed="true" style="align: center;">
				<h4>Filament</h4>
				<div class="ui-grid-a">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f">
							<label for="temper_l">Left</label>
							<div style="width:350px;float:left;margin-right:10px;">
								<input type="text" data-clear-btn="false" name="rfid_l_current" id="rfid_l_current" value="NEED REFRESH" disabled>
							</div>
							<a href="#" data-role="button" data-icon="refresh" data-iconpos="left" data-inline="true" onclick="refreshRfid();">Refresh</a>
							<br style="clear:left;" />
							<div style="width:60px;float:left;margin-right:10px;">
								<input type="number" style="text-align:right;" data-clear-btn="false" name="extrude_l" id="extrude_l" value="20"> mm
							</div>
							<div style="width:60px;float:left;margin-right:10px;">
								<input type="number" style="text-align:right;" data-clear-btn="false" name="speed_l" id="speed_l" value="100"> mm/min
							</div>
							<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="extrude('l', '-');">Reverse</a>
							<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="extrude('l', '+');">Extrude</a>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f">
							<label for="temper_r">Right</label>
							<div style="width:350px;float:left;margin-right:10px;">
								<input type="text" data-clear-btn="false" name="rfid_r_current" id="rfid_r_current" value="NEED REFRESH" disabled>
							</div>
							<a href="#" data-role="button" data-icon="refresh" data-iconpos="left" data-inline="true" onclick="refreshRfid();">Refresh</a>
							<br style="clear:left;" />
							<div style="width:60px;float:left;margin-right:10px;">
								<input type="number" style="text-align:right;" data-clear-btn="false" name="extrude_r" id="extrude_r" value="20"> mm
							</div>
							<div style="width:60px;float:left;margin-right:10px;">
								<input type="number" style="text-align:right;" data-clear-btn="false" name="speed_r" id="speed_r" value="100"> mm/min
							</div>
							<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" data-inline="true" onclick="extrude('r', '-');">Reverse</a>
							<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" data-inline="true" onclick="extrude('r', '+');">Extrude</a>
						</div>
					</div>
				</div>
			</div>
			<div data-role="collapsible" data-collapsed="true" style="align: center;">
				<h4>G-code</h4>
				<label for="oneline_v">Verbatim one line G-code</label>
				<input type="text" name="oneline_v" id="oneline_v" value="">
				<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="right" onclick="runGcodeGet();">Send</a>
				<label for="severalline_v">Verbatim multiline G-code</label>
				<textarea name="severalline_v" id="severalline_v" style="max-height:200px;"></textarea>
				<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="right" onclick="runGcodePOST('#severalline_v', 'verbatim');">Send</a>
				<label for="severalline_n">Multiline G-code</label>
				<textarea name="severalline_n" id="severalline_n" style="max-height:200px;"></textarea>
				<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="right" onclick="runGcodePOST('#severalline_n');">Send</a>
				<form id="fileupload_v" action="/rest/gcodefile" method="post" enctype="multipart/form-data" data-ajax="false">
					<label for="fileupload_v">Verbatim file upload</label>
					<input type="file" data-clear-btn="true" name="f" id="file_v">
					<input type="hidden" name="mode" value="verbatim">
					<input type="button" value="Send" data-icon="arrow-r" data-iconpos="right" onclick="startSubmit('form#fileupload_v');">
				</form>
<!-- 				<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="right">Send</a> -->
				<form id="fileupload_n" action="/rest/gcodefile" method="post" enctype="multipart/form-data" data-ajax="false">
					<label for="fileupload_n">File upload</label>
					<input type="file" data-clear-btn="true" name="f" id="file_n">
					<input type="button" value="Send" data-icon="arrow-r" data-iconpos="right" onclick="startSubmit('form#fileupload_n');">
				</form>
<!-- 				<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="right">Send</a> -->
<!-- 				<form action="/pronterface/emulator" method="post" enctype="multipart/form-data" data-ajax="false">
					<label for="severalline_v">File upload emulator</label>
					<input type="file" data-clear-btn="true" name="f" id="file_n_e">
					<input type="submit" value="Send" data-icon="arrow-r" data-iconpos="right">
				</form> -->
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-delete" onclick="javascript: window.location.href='/pronterface/stop';">Stop</button>
			</div>
			<label for="gcode_detail_info">Output</label>
			<textarea name="gcode_detail_info" id="gcode_detail_info" style="height: 300px !important;" disabled></textarea>
<!-- 			<div id="gcode_detail_info"></div> -->
		</div>
	</div>

<script type="text/javascript">
var var_ajax;
var var_refreshCheckTemper = 0;
var var_ajax_lock = false;
var var_checked_rfid = false;
var var_verifyRFIDInterval = 0;

$(document).ready(checkTemper());

// $("form#fileupload_v, form#fileupload_n").submit(verifyRfid(event));


/* 
function checkTemper() {
//	refreshCheckTemper();
	$("#temper_collaspible").bind('expand', function () {
		if (var_refreshCheckTemper == 0) {
//			checkTemper();
			var_refreshCheckTemper = setInterval(refreshCheckTemper, 10000);
		}
	}).bind('collapse', function() {
		clearInterval(var_refreshCheckTemper);
		var_refreshCheckTemper = 0;
	});
}
 */
function checkTemper() {
//		refreshCheckTemper();
		$("#temper_collaspible").collapsible({
			expand: function(event, ui) {
				if (var_refreshCheckTemper == 0) {
					refreshCheckTemper();
					var_refreshCheckTemper = setInterval(refreshCheckTemper, 10000);
				}
			},
			collapse: function(event, ui) {
				if (var_refreshCheckTemper != 0) {
					clearInterval(var_refreshCheckTemper);
					var_refreshCheckTemper = 0;
				}
			},
		});
	}

function refreshCheckTemper() {
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}
	
	var_ajax = $.ajax({
		url: "/pronterface/temper_ajax",
		type: "GET",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		var response = JSON.parse(html);
		$("#temper_l_current").val(response.left);
		$("#temper_r_current").val(response.right);
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});
}

function home(var_axis) {
	var var_url;
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}

	var_axis = typeof var_axis !== 'undefined' ? var_axis : 'all';
	if (var_axis == 'all') {
		var_url = "/pronterface/home";
	}
	else {
		var_url = "/pronterface/home/" + var_axis;
	}
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function move(var_axis, var_value) {
	var var_url;
	var var_speed;
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}

	var_axis = typeof var_axis !== 'undefined' ? var_axis : 'error';
	if (var_axis == 'error') {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	else {
		if (var_axis == 'Z') {
			var_speed = $("#z_speed").val();
		}
		else {
			var_speed = $("#xy_speed").val();
		}
		var_url = "/pronterface/move/" + var_axis + '/' + var_value + '/' + var_speed;
	}
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function level(var_point) {
	var var_url;
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}

	var_point = typeof var_point !== 'undefined' ? var_point : 'error';
	if (var_point == 'error') {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	else {
		var_url = "/pronterface/level/" + var_point;
	}
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function heat(var_extruder, var_value) {
	var var_url;
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}

	var_extruder = typeof var_extruder !== 'undefined' ? var_extruder : 'error';
	var_value = typeof var_value !== 'undefined' ? var_value : 'get';

	if (var_extruder == 'error') {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	if (var_value == 'get') {
		if (var_extruder == 'l') {
			var_value = $("#temper_l").val();
		}
		else {
			var_value = $("#temper_r").val();
		}
	}

	var_url = "/pronterface/heat/" + var_extruder + '/' + var_value;
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function extrude(var_extruder, var_direction) {
	var var_url, var_value, var_speed;
	var id_extrude, id_speed;
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}

	var_extruder = typeof var_extruder !== 'undefined' ? var_extruder : 'error';
	var_direction = typeof var_direction !== 'undefined' ? var_direction : 'error';

	if (var_extruder == 'error' || var_direction == 'error') {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	if (var_extruder == 'l') {
		id_extrude = "#extrude_l";
		id_speed = "#speed_l";
	} else if (var_extruder == 'r') {
		id_extrude = "#extrude_r";
		id_speed = "#speed_r";
	} else {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	if (var_direction == '+') {
		var_value = $(id_extrude).val();
	} else if (var_direction == '-') {
		var_value = -parseFloat($(id_extrude).val());
	} else {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	var_speed = $(id_speed).val();

	var_url = "/pronterface/extrude/" + var_extruder + '/' + var_value + '/' + var_speed;
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function refreshRfid() {
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}
	
	var_ajax = $.ajax({
		url: "/pronterface/rfid_ajax",
		type: "GET",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		var response = JSON.parse(html);
		$("#rfid_l_current").val(response.left);
		$("#rfid_r_current").val(response.right);
		var_checked_rfid = true;
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function verifyRfid() {
	if (var_checked_rfid == false) {
// 		var var_option = confirm("Haven't launched checking RFID detected, check it now before start printing?");
// 		if (var_option == true) {
			refreshRfid();
			return true;
// 		}
// 		else {
// 			alert("You have cancelled printing because of RFID");
// 			return false;
// 		}
	}
	else {
		return true;
	}
}

function runGcodeGet() {
	var var_gcode = $('#oneline_v').val();
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}

	if (var_gcode.indexOf('M109') != -1
			|| var_gcode.indexOf('M1606') != -1
			|| var_gcode.indexOf('M1607') != -1) {
		alert("M109 / M1606 / M1607 include!");
		return false;
	}
	var_ajax = $.ajax({
		url: "/rest/gcode",
		type: "GET",
		data: {
			v: var_gcode,
		},
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		$("#gcode_detail_info").html(html);
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function runGcodePOST(id_code, var_mode) {
	var var_gcode;
	var var_verify;

	id_code = typeof id_code !== 'undefined' ? id_code : 'error';
	var_mode = typeof var_mode !== 'undefined' ? var_mode : 'normal';
	if (id_code == 'error') {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	
	var_verify = verifyRfid();
	if (var_verify == false) {
		return;
	}
	
	var_gcode = $(id_code).val();
	
	var_verifyRFIDInterval = setInterval(function(var_gcode, var_mode) {
		if (var_ajax_lock == false) {
			spinnerStart();
			postGcodeAjax(var_gcode, var_mode);
			clearInterval(var_verifyRFIDInterval);
		}
	}, 100);
	
	function postGcodeAjax(var_gcode, var_mode) {
		if (var_ajax_lock == true) {
			return;
		}
		else {
			var_ajax_lock = true;
		}
		
		var_ajax = $.ajax({
			url: "/rest/gcode",
			type: "POST",
			data: {
				v:		var_gcode,
				mode:	var_mode,
			},
			cache: false,
			beforeSend: function() {
				$("#overlay").addClass("gray-overlay");
				$(".ui-loader").css("display", "block");
			},
			complete: function() {
				$("#overlay").removeClass("gray-overlay");
				$(".ui-loader").css("display", "none");
			},
		})
		.done(function(html) {
			$("#gcode_detail_info").html('OK');
		})
		.fail(function() {
			$("#gcode_detail_info").html('ERROR');
		})
		.always(function() {
			var_ajax_lock = false;
		});
	}
}

function spinnerStart() {
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
}

function startSubmit(formid) {
	var var_verify;
	
	spinnerStart();
	var_verify = verifyRfid();
	if (var_verify == false) {
		$("#overlay").removeClass("gray-overlay");
		$(".ui-loader").css("display", "none");
		return false;
	}
	else {
		var_verifyRFIDInterval = setInterval(function() {
			if (var_ajax_lock == false) {
				spinnerStart();
				$(formid).submit();
				clearInterval(var_verifyRFIDInterval);
			}
		}, 100);
		return true;
	}
}

</script>

</div>

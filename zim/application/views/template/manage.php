<div data-role="page" data-url="/manage">
	<style>
		.round-button
		{
			width: 15% !important;
			height: 42% !important;
			border-radius: 50% !important;
		}
	</style>
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible">
				<h4>{platform_view_title}</h4>
				<div class="container_16">
					<script type="text/javascript" src="/assets/jwplayer/jwplayer.js"></script>
	 				<script type="text/javascript">jwplayer.key="Jh6aqwb1m2vKLCoBtS7BJxRWHnF/Qs3LMjnt13P9D6A=";</script>
	 				<style type="text/css">div#myVideo_wrapper {margin: 0 auto;}</style>
					<div id="myVideo">Loading the player...</div>
				</div>
			</div>
			<div data-role="collapsible">
				<h4>{lighting_title}</h4>
				<div class="container_16">
					<div class="ui-grid-a">
					<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
						<label for="slider"><h2>{strip_led}</h2></label>
					</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-f" style="height:3em;">
						<select name="strip_led" id="strip_led" data-role="slider" data-track-theme="a" data-theme="a">
							<option value="off" id="strip_off">{led_off}</option>
							<option value="on" id="strip_on" {strip_led_on}>{led_on}</option>
						</select>
					</div></div>
					<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
						<label for="slider"><h2>{head_led}</h2></label>
					</div></div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<select name="head_led" id="head_led" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="off" id="head_off">{led_off}</option>
								<option value="on" id="head_on"{head_led_on}>{led_on}</option>
							</select>
						</div>
					</div>
				</div>
				</div>
			</div>
			<div data-role="collapsible" style="align: center;">
				<h4>{reset}</h4>
				<div class="container_16">
					<div class="grid_6 prefix_5 suffix_5">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home();">XYZ</a>
					</div>
					<div class="grid_6 prefix_5 suffix_5">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home('X');">X</a>
					</div>
					<div class="grid_6 prefix_5 suffix_5">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home('Y');">Y</a>
					</div>
					<div class="grid_6 prefix_5 suffix_5">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home('Z');">Z</a>
					</div>
				</div>
			</div>
			<div data-role="collapsible" style="align: center;">
				<h4>{head}</h4>
				<div class="container_16">
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Y', 1);">1</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Y', 10);">10</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Y', 50);">50</a>
					</div>
					<div class="grid_4 prefix_1 suffix_3" style="margin-bottom:13px;">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" onclick="move('X', -1);">1</a>
					</div>
					<div class="grid_4 prefix_3 suffix_1" style="margin-bottom:13px;">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" onclick="move('X', 1);">1</a>
					</div>
					<div class="grid_4 prefix_1 suffix_1">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" onclick="move('X', -10);">10</a>
					</div>
					<div class="grid_4">
						<input type="number" style="text-align:right;" data-clear-btn="false" name="xy_speed" id="xy_speed" value="30" min="10" max="35"/><center style="padding-left:22px">mm/s</center>
					</div>
					<div class="grid_4 prefix_1 suffix_1">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" onclick="move('X', 10);">10</a>
					</div>
					<div class="grid_4 prefix_1 suffix_3">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" onclick="move('X', -50);">50</a>
					</div>
					<div class="grid_4 prefix_3 suffix_1">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" onclick="move('X', 50);">50</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Y', -1);">1</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Y', -10);">10</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Y', -50);">50</a>
					</div>
				</div>
			</div>
			<div data-role="collapsible" style="align: center;">
				<h4>{platform}</h4>
				<div class="container_16">
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Z', -1);">1</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Z', -10);">10</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Z', -50);">50</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<input type="number" style="text-align:right;" data-clear-btn="false" name="z_speed" id="z_speed" value="5" min="1" max="10"/><center style="padding-left:22px">mm/s</center>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Z', 1);">1</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Z', 10);">10</a>
					</div>
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Z', 50);">50</a>
					</div>
				</div>
			</div>
			<div data-role="collapsible" style="align: center;">
				<h4>{filament}</h4>
				
				<ul data-role="listview" id="listview" data-inset="true">
					<li><a href="#" onclick="javascript: window.location.href='/printerstate/changecartridge?v=l&f=0';">
						<h2>{manage_left}</h2></a>
					</li>
					<li><a href="#" onclick="javascript: window.location.href='/printerstate/changecartridge?v=r&f=0';">
						<h2>{manage_right}</h2></a>
					</li>
				</ul>
			</div>
			<div data-role="collapsible" data-collapsed="false">
				<h4>{bed_title}</h4>
				<div id="bed_container" class="container_16">
					<table style="border: 2px solid;height: 100%;width: 100%;background-color:silver;margin:0 auto;max-width:400px;max-height:400px">
						<tr>
							<td style="text-align: center">
								<a onclick="level('step2')" data-role="button" data-inline="true" class="round-button" style="margin-left:22%">2</a>
							</td>
							<td></td>
							<td style="text-align: center">
								<a onclick="level('step3')" data-role="button" data-inline="true" class="round-button">3</a>
							</td>
						</tr>
						<tr>
							<td></td>
							<td style="text-align: center">
								<a onclick="level('step1')" data-role="button" data-inline="true" class="round-button" style="margin-left: 15%">1</a>
							</td>
							<td></td>
						</tr>
						<tr></tr>
						<tr></tr>
					</table>
				</div>
				<!--<div class="container_16" style="height:430px">
					<div id="bed_interface" style="height:100%;background-color:silver;margin:0 auto;max-width:400px;max-height:400px">
						<a onclick="level('step2')" data-role="button" data-inline="true" class="round-button" style="margin-left: 15%;margin-top: 15%;">2</a>
						<a onclick="level('step3')" data-role="button" data-inline="true" class="round-button" style="right: -35%;margin-top: 15%;">3</a>
						<a onclick="level('step1')" data-role="button" data-inline="true" class="round-button" style="margin-left: 41%;margin-top: 30%;">1</a>
					</div>
				</div>-->
			</div>
		</div>
	</div>

<script type="text/javascript">

$(document).ready(function()
{
	$("table").css("height", $("table").css("width"));
	$("#bed_container").css("height", $("table").css("height")+2);
});

function load_jwplayer_video() {
	var player = jwplayer("myVideo").setup({
							file: "{video_url}",
							width: "100%",
							autostart: true,
							fallback: false,
							androidhls: true
						});
	player.onSetupError(function()
	{
		$("#myVideo").empty().append('<img src=/images/error.png" height="280" width="280" />' +
									"<p>{video_error}</p>");
	});
}

setTimeout(load_jwplayer_video, 7000);
var var_ajax;
var var_ajax_lock = false;

$("#head_led").change(function()
{
	setTimeout(function()
	{
		if (var_ajax_lock == false)
		{
			var_ajax_lock = true;
			var var_state = $("#head_led").val().toString();
			var_ajax = $.ajax(
			{
				url: "/rest/set",
				cache: false,
				data:
				{
					p: "headlight",
					v: var_state,
				},
				type: "GET",
			})
			.always(function()
			{
				var_ajax_lock = false;
			});
		}
	}, 1000);
});

$("#strip_led").change(function()
{
	setTimeout(function()
	{
		if (var_ajax_lock == false)
		{
			var_ajax_lock = true;
			var var_state = $("#strip_led").val().toString();
			var_ajax = $.ajax(
			{
				url: "/rest/set",
				cache: false,
				data:
				{
					p: "stripled",
					v: var_state,
				},
				type: "GET",
			})
			.always(function()
			{
				var_ajax_lock = false;
			});
		}
	}, 1000);
});

function home(var_axis) {
	var var_url;

	var_axis = typeof var_axis !== 'undefined' ? var_axis : 'all';
	if (var_axis == 'all') {
		var_url = "/manage/home";
	}
	else {
		var_url = "/manage/home/" + var_axis;
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
	});

	return false;
}

function move(var_axis, var_value) {
	var var_url;
	var var_speed;

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
		var_url = "/manage/move/" + var_axis + '/' + var_value + '/' + var_speed;
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
	});

	return false;
}

function level(var_point) {
	var var_url;
	if (var_ajax_lock == true)
	{
		return;
	}
	else
	{
		var_ajax_lock = true;
	}

	var_point = typeof var_point !== 'undefined' ? var_point : 'error';
	if (var_point == 'error')
	{	
		return false;
	}
	else
	{
		var_url = "/manage/level/" + var_point;
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
	
	})
	.fail(function() {
		
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}
</script>
</div>

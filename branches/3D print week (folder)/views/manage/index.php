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
		<a href="javascript:history.back();" data-icon="back" data-ajax="false" class="back-button">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div id="threeD_print_week" data-role="popup" class="ui-content">
		<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
		{non_accessible}
	</div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible">
				<h4>{platform_view_title}</h4>
				<div class="container_16">
					<script type="text/javascript" src="/assets/jwplayer/jwplayer.js"></script>
	 				<script type="text/javascript">jwplayer.key="Jh6aqwb1m2vKLCoBtS7BJxRWHnF/Qs3LMjnt13P9D6A=";</script>
	 				<style type="text/css">div#myVideo_wrapper {margin: 0 auto;}</style>
					<div id="myVideo">{loading_player}</div>
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
			<div id="homing_disable_collapsible" data-role="collapsible" style="align: center;">
				<h4>{reset}</h4>
				<div class="container_16">
					
						<a href="#home_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">{what}</a>
						<div id="home_popup" data-role="popup" class="ui-content">
							<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
							{home_text}
						</div>
					
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
			<div id="head_disable_collapsible" data-role="collapsible" style="align: center;">
				<h4>{head}</h4>
				<div class="container_16">
					
						<a href="#head_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">{what}</a>
						<div id="head_popup" data-role="popup" class="ui-content">
							<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
							{head_text}
						</div>
					
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
			<div id="platform_disable_collapsible" data-role="collapsible" style="align: center;">
				<h4>{platform}</h4>
				<div class="container_16">
					
						<a href="#platform_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">{what}</a>
						<div id="platform_popup" data-role="popup" class="ui-content">
							<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
							{platform_text}
						</div>
					
					<div class="grid_4 prefix_6 suffix_6">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Z', -0.1);">0.1</a>
					</div>
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
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Z', 0.1);">0.1</a>
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
			<div id="filament_disable_collapsible" data-role="collapsible">
				<h4>{filament}</h4>
				<div class="container_16" style="text-align:center">
					
						<a href="#filament_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">{what}</a>
						<div id="filament_popup" data-role="popup" class="ui-content">
							<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
							{filament_text}
						</div>
					
					<div style="text-align:center;display:inline-block;width:45%;margin-left:2%">
						{left}
					</div>
					<div style="text-align:center;display:inline-block;width:45%;margin-left:2%">
						{right}
					</div>
				</div>
				<div class="container_16" style="text-align:center">
					<div id="cartridge_ajax" style="background-color:#f6f6f6;text-align:center;border: 2px solid;border-radius: 53px;display:inline-block;width:45%;margin-left:2%;">
						<img src="styles/images/ajax-loader.gif" style="opacity: 0.18;" />
					</div>
					<div id="cartridge_ajax2" style="background-color:#f6f6f6;text-align:center;border: 2px solid;border-radius: 53px;display:inline-block;width:45%;margin-left:2%;">
						<img src="styles/images/ajax-loader.gif" style="opacity: 0.18;" />
					</div>
				</div>
				<br />
			</div>
			<div id="bed_disable_collapsible" data-role="collapsible">
				<h4>{bed_title}</h4>
				<div id="bed_container" class="container_16" style="height:480px; text-align: center">
					
						<a href="#bed_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">{what}</a>
						<div id="bed_popup" data-role="popup" class="ui-content">
							<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
							{bed_text}
						</div>
					
					<br />
					<table style="border: 2px solid;width:100%;background-color:silver;margin:0 auto;max-width:400px;max-height:400px">
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
			</div>
			<a href="#threeD_print_week" data-rel="popup" data-transition="pop" data-position-to="origin" data-role="button">{reboot}</a>
			<a href="#threeD_print_week" data-rel="popup" data-transition="pop" data-position-to="origin" data-role="button">{shutdown}</a>
		</div>
	</div>

<script type="text/javascript">

//**************
//	CARTRIDGE JS
//**************

var var_manage_filament_l = $.ajax(
{
	url: "/manage/filament_ajax/l",
	cache: false,
	type: "GET",
	async: "true",
})
.done(function(html)
{
	if (var_manage_filament_l.status == 202) {
		$("#cartridge_ajax").html(html);
		$("#cartridge_ajax").css('cursor', 'pointer');
		$("#cartridge_ajax").on('click', function()
		{
			window.location.href = '/printerstate/changecartridge?v=l&f=0';
		});
	}
});

var var_manage_filament_r = $.ajax(
{
	url: "/manage/filament_ajax/r",
	cache: false,
	type: "GET",
	async: "true"
})
.done(function(html)
{
	if (var_manage_filament_r.status == 202) {
		$("#cartridge_ajax2").html(html);
		$("#cartridge_ajax2").css('cursor', 'pointer');
		$("#cartridge_ajax2").on('click', function()
		{
			window.location.href = '/printerstate/changecartridge?v=r&f=0';
		});
	}
});

$(document).ready(function()
{
	$("table").css("height", $("table").css("width"));
	$("#bed_container").css("height", $("table").css("width")+1);
	$("div[id$='_disable_collapsible']").each(function() {
		$(this).collapsible("disable");
		$(this).on('click', function() {
			$("#threeD_print_week").popup( "option", "positionTo", $(this));
			$("#threeD_print_week").popup( "option", "transition", "pop" );
			$("#threeD_print_week").popup("open");
		});
	});
});

//**********
//	VIDEO JS
//**********

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
var video_check = setInterval(function()
{
	var req = $.ajax(
	{
		url: "{video_url}",
		type: "HEAD",
		success: function()
		{
			load_jwplayer_video();
			clearInterval(video_check);
		}
	});
}, 1000);

//********
//	LED JS
//********

var var_ajax;
var var_ajax_lock = false;
var var_home_before_level = false;

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

//********
//	HOMING
//********

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
		return false;
	}
	else {
		if (var_axis == 'Z') {
			var_speed = $("#z_speed").val();
		}
		else {
			var_speed = $("#xy_speed").val();
		}
		var_url = "/manage/move";
	}
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
		data: {
				'axis': var_axis,
				'value': var_value,
				'speed': var_speed
		},
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function level(var_point) {
	var var_url;
	if (var_home_before_level == false) {
		home_before_level(var_point);
		return;
	}
	if (var_ajax_lock == true) {
		return;
	}
	else {
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
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}

function home_before_level(var_point) {
	if (var_ajax_lock == true) {
		return;
	}
	else {
		var_ajax_lock = true;
	}
	var_ajax = $.ajax({
		url: "/manage/home",
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
	.done(function() {
		var_ajax_lock = false;
		var_home_before_level = true;
		level(var_point);
	})
	.always(function() {
		var_ajax_lock = false;
	});

	return false;
}
</script>
</div>

<div id="overlay"></div>
<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
		<form action="/printdetail/printuserlib_temp?id={id}" method="POST" data-ajax="false" id="form_userlib_printlib">
			<h2 style="text-align: center;">{title}</h2>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{photo_title}</h4>
				<img src="{photo_link}" style="max-width: 100%;"><br>
			</div>
			<div data-role="collapsible" id="userlib_printvideo" style="text-align: center; display: none;">
				<h4>{video_title}</h4>
				<script type="text/javascript" src="/assets/jwplayer/jwplayer.js"></script>
	 			<script type="text/javascript">jwplayer.key="Jh6aqwb1m2vKLCoBtS7BJxRWHnF/Qs3LMjnt13P9D6A=";</script>
	 			<style type="text/css">div#myVideo_wrapper {margin: 0 auto;}</style>
				<div id="myVideo_container"><div id="myVideo"></div></div>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{title_current}</h4>
				<div style="display: none;" class="widget_monocolor slider-show-value-container">
					<div style="width: 75px; height: 75px; background-color: {state_c_r}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
					<p id="state_right">{state_f_r}</p>
					<a href="/printerstate/changecartridge?v=r&f={need_filament_r}&id=userlib{id}" data-role="button" data-ajax="false" data-iconpos="none" class="ui-shadow ui-corner-all">{change_filament_r}</a>
					<div>{temp_adjustments}</div>
					<input type="range" id="slider-mono" value="{temper_filament_r}" min="{temper_min}" max="{temper_max}" data-show-value="true">
				</div>
				<div style="height:265px; display: none;" class="widget_bicolor">
					<div class="ui-grid-a">
						<div class="ui-block-a">
							<div style="width: 75px; height: 75px; background-color: {state_c_l}; margin: 0 auto;">
								<img src="/images/cartridge.png" style="width: 100%">
							</div>
						</div>
						<div class="ui-block-b">
							<div style="width: 75px; height: 75px; background-color: {state_c_r}; margin: 0 auto;">
								<img src="/images/cartridge.png" style="width: 100%">
							</div>
						</div>
						<div class="ui-block-a">
							<p id="state_left">{state_f_l}</p>
						</div>
						<div class="ui-block-b">
							<p id="state_right">{state_f_r}</p>
						</div>
						<div class="ui-block-a" style="padding-left:0px">
							<a href="/printerstate/changecartridge?v=l&f={need_filament_l}&id=userlib{id}" data-role="button" data-ajax="false" data-iconpos="none" class="ui-shadow ui-corner-all">{change_filament_l}</a>
						</div>
						<div class="ui-block-b">
							<a href="/printerstate/changecartridge?v=r&f={need_filament_r}&id=userlib{id}" data-role="button" data-ajax="false" data-iconpos="none" class="ui-shadow ui-corner-all">{change_filament_r}</a>
						</div>
					</div>
					<div class="ui-grid-a slider-show-value-container">
						<div class="ui-block-a">{temp_adjustments_l}</div>
						<div class="ui-block-b">{temp_adjustments_r}</div>
						<div class="ui-block-a">
							<input type="range" name="l" id="slider-l" value="{temper_filament_l}" min="{temper_min}" max="{temper_max}" data-show-value="true">
						</div>
						<div class="ui-block-b">
							<input type="range" name="r" id="slider-r" value="{temper_filament_r}" min="{temper_min}" max="{temper_max}" data-show-value="true">
						</div>
					</div>
				</div>
			</div>
			<div data-role="collapsible" style="clear: both; text-align: center;">
				<h4>{advanced}</h4>
				<div class="ui-grid-a">
					<div class="ui-block-a">
						<a data-role="button" href="#" onclick="javascript: do_requestGcodeAnalyser(2);">{gcode_link}</a>
					</div>
					<div class="ui-block-b">
						<a data-role="button" href="#" onclick="javascript: do_requestGcodeAnalyser(3);">{2drender_link}</a>
					</div>
				</div>
				<p style="font-weight: bold;">{extrud_multiply}</p>
				<div class="widget_monocolor slider-show-value-container" style="display: none;">
					<input type="range" id="slider_mono_em" value="{extrud_r}" min="{extrud_min}" max="{extrud_max}" data-show-value="true" />
				</div>
				<div class="widget_bicolor ui-grid-a slider-show-value-container" style="display: none;">
					<div class="ui-block-a">
						<label>{left_extrud_mult}</label>
						<div id="extrud_l">
							<input type="range" id="slider_left_em" name="e_l" value="{extrud_l}" min="{extrud_min}" max="{extrud_max}" data-show-value="true" />
						</div>
					</div>
					<div class="ui-block-b">
						<label>{right_extrud_mult}</label>
						<div id="extrud_r">
							<input type="range" id="slider_right_em" name="e_r" value="{extrud_r}" min="{extrud_min}" max="{extrud_max}" data-show-value="true" />
						</div>
					</div>
				</div>
			</div>
			<div data-role="collapsible" class="heat_bed_widget slider-show-value-container" data-collapsed="false" style="clear: both; display: none;">
				<h4>{title_heatbed}</h4>
				<div class="heat_bed_pos_widget" style="display: none;">
					<label>
						<input type="checkbox" id="enable_heatbed" data-mini="true" value="1" {checked_heatbed}>{enable_heatbed}
					</label>
				</div>
				<div data-role="navbar" class="heat_bed_neg_widget" style="margin-bottom: 1em; display: none;">
					<ul>
						<li><a href="#" onclick="javascript: onBedSliderSelected();" class="ui-btn-active">{button_bed_off}</a></li>
						<li><a href="#" onclick="javascript: onBedSliderSelected('PLA');">PLA</a></li>
						<li><a href="#" onclick="javascript: onBedSliderSelected('ABS');">ABS</a></li>
					</ul>
				</div>
				<input type="range" name="b" id="bed_temper_control" value="{value_heatbed}" min="0" max="{bed_temper_max}" data-show-value="true" />
			</div>
			<p id="userPrintDetail_stateInfo" style="text-align: center; font-weight: bold;"></p>
			<div style="clear: both;">
				<input type="hidden" name="exchange" id="exchange_extruder_hidden" value="0">
				<input type="submit" value="{print_button}" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" />
			</div>
		</form>
			<p id="userPrintDetail_error" class="zim-error">{error}</p>
		</div>
	</div>

<script>
var var_enable_print = {enable_print};
var var_suggest_temper_right = {temper_suggest_r};
var var_suggest_temper_left = {temper_suggest_l};
var limit_min_tmp = {temper_min};
var limit_max_tmp = {temper_max};
var delta_tmp = {temper_delta};
var error_checked = false;
var var_bicolor = {bicolor};
var slider_l = "input#slider-l";
var slider_r = var_bicolor ? "input#slider-r" : "input#slider-mono";
var var_cache_ready = false;
var var_typeAction_cached;
var var_ajax;
var var_show_video = {show_video};
var var_video_initialized = false;
var var_have_heatbed = {heat_bed};
var var_contain_heatbed = {contain_heat_bed};
var limit_bed_max_tmp = {bed_temper_max};

var handlerUserPrintSubmit = function submitUserPrint(event) {
	event.preventDefault();
	
	if (var_cache_ready) {
		$("form#form_userlib_printlib").unbind("submit", handlerUserPrintSubmit).submit();
		startSubmit_wait();
		
		return;
	}
	
	var_typeAction_cached = 1;
	startCachePrint();
	
	return;
}

function startCachePrint() {
	console.log("startCachePrint");
	$("p#userPrintDetail_stateInfo").html("{msg_gcode_download}");
	
	if (typeof(var_typeAction_cached) == "undefined") {
		console.log("var_typeAction_cached undefined");
		return;
	}
	
	var_ajax = $.ajax({
		url: "/userlib/prepareprint_ajax",
		cache: false,
		type: "POST",
		data: { id: '{id}' },
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			if (var_ajax.status != 403) {
				$("#overlay").removeClass("gray-overlay");
				$(".ui-loader").css("display", "none");
			}
		},
	})
	.done(function() {
		var_cache_ready = true;
		$("p#userPrintDetail_stateInfo").empty();
		
		switch (var_typeAction_cached) {
		case 1: // start print
			$("form#form_userlib_printlib").unbind("submit", handlerUserPrintSubmit).submit();
			startSubmit_wait();
			break;
			
		case 2: // gcode display
			window.location.href="/gcode/userlib/display?id={id}";
			break;
			
		case 3: // gcode render
			window.location.href="/gcode/userlib/render?id={id}";
			break;
			
		default:
			console.log("unknown var_typeAction_cached");
			break;
		}
	})
	.fail(function() {
		if (var_ajax.status == 403) {
			setTimeout(startCachePrint, 5000);
		}
		else {
			console.log("unexpected error case: " + var_ajax.status);
			$("p#userPrintDetail_stateInfo").empty();
			$("p#userPrintDetail_error").html("{msg_download_fail}");
		}
	});
	
	return;
}

function startSubmit_wait() {
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
}

function do_requestGcodeAnalyser(var_typeId) {
	var_typeAction_cached = var_typeId;
	startCachePrint();
	
	return;
}

function load_jwplayer_video() {
	var player = jwplayer("myVideo").setup({
		file: "{video_url}",
		width: "100%",
		autostart: false,
		fallback: false,
		androidhls: true
	}).onSetupError(function() {
		console.log('jwplayer onSetupError');
	}).onError(function() {
		console.log('jwplayer onError');
	});
}

function onBedSliderSelected(var_matType) {
	var var_value2switch = 0;
	var var_bed_slider_selector = "input#bed_temper_control";
	
	if (typeof(var_matType) != "undefined") {
		switch (var_matType) {
			case "PLA":
				var_value2switch = {bed_temper_pla};
				break;
				
			case "ABS":
				var_value2switch = {bed_temper_abs};
				break;
				
			default:
				console.log("unknown material in onBedSliderSelected");
				break;
		}
	}
	$(var_bed_slider_selector).val(var_value2switch);
	if (var_value2switch == 0) {
		$(var_bed_slider_selector).slider("disable");
	}
	else {
		$(var_bed_slider_selector).slider("enable");
	}
	$(var_bed_slider_selector).slider("refresh");
	
	return;
}

function onBedSliderSwitched(var_enable) {
	var var_value2switch = var_minToChange = 0;
	var var_maxToChange = limit_bed_max_tmp;
	var var_bed_slider_selector = "input#bed_temper_control";
	
	if (typeof(var_enable) != "undefined") {
		if (var_enable) {
			var_value2switch = {value_heatbed};
			var_maxToChange = var_value2switch + delta_tmp;
			var_minToChange = var_value2switch - delta_tmp;
			if (var_maxToChange > limit_bed_max_tmp) {
				var_maxToChange = limit_bed_max_tmp;
			}
		}
	}
	$(var_bed_slider_selector).val(var_value2switch).attr("max", var_maxToChange).attr("min", var_minToChange);
	if (var_value2switch == 0) {
		$(var_bed_slider_selector).slider("disable");
	}
	else {
		$(var_bed_slider_selector).slider("enable");
	}
	$(var_bed_slider_selector).slider("refresh");
	
	return;
}

$(document).on("pagecreate",function() {
	if (var_bicolor == true) {
		$(".widget_bicolor").show();
	}
	else {
		$(".widget_monocolor").show();
		$(slider_r).change(function() {
			$("input#slider-r").val($(slider_r).val());
		});
		$("input#slider_mono_em").change(function() {
			$("input#slider_right_em").val($("input#slider_mono_em").val());
		});
	}
	
	if (var_enable_print == false) {
		$("input[type=submit]").button("disable");
	}
	
	var tmp = $(slider_r).val();
	var min_tmp = tmp - delta_tmp;
	var max_tmp = parseInt(tmp) + delta_tmp;
	
	$(slider_r).attr('min', (min_tmp < limit_min_tmp) ? limit_min_tmp : min_tmp);
	$(slider_r).attr('max', (max_tmp > limit_max_tmp) ? limit_max_tmp : max_tmp);
	if (var_suggest_temper_right != $(slider_r).val()
			&& var_suggest_temper_right >= $(slider_r).attr('min')
			&& var_suggest_temper_right <= $(slider_r).attr('max')) {
		$(slider_r).val(var_suggest_temper_right);
	}
	$(slider_r).slider("refresh");
	
	tmp = $(slider_l).val();
	min_tmp = tmp - delta_tmp;
	max_tmp = parseInt(tmp) + delta_tmp;
	
	$(slider_l).attr('min', (min_tmp < limit_min_tmp) ? limit_min_tmp : min_tmp);
	$(slider_l).attr('max', (max_tmp > limit_max_tmp) ? limit_max_tmp : max_tmp);
	if (var_suggest_temper_left != $(slider_l).val()
			&& var_suggest_temper_left >= $(slider_l).attr('min')
			&& var_suggest_temper_left <= $(slider_l).attr('max')) {
		$(slider_l).val(var_suggest_temper_left);
	}
	$(slider_l).slider("refresh");
	
	$("input[type=submit]").on('click', function()
	{
		startSubmit_wait();
	});
	
	$("form#form_userlib_printlib").bind("submit", handlerUserPrintSubmit);
	
	if ("{state_f_r}" != "{msg_ok}") {
		$(slider_r).slider({disabled: true});
		error_checked = true;
	}
	if ("{state_f_l}" != "{msg_ok}") {
		$(slider_l).slider({disabled: true});
		error_checked = true;
	}
	if (error_checked == false) {
		if ({need_filament_l} <= 0) {
			$(slider_l).slider({disabled: true});
		}
		if ({need_filament_r} <= 0) {
			$(slider_r).slider({disabled: true});
		}
	}
	
	if (var_show_video == true) {
		$("div#userlib_printvideo").show();
		
		$("div#userlib_printvideo").on("collapsibleexpand", function() {
			if (var_video_initialized == false) {
				load_jwplayer_video();
				var_video_initialized = true;
			}
		});
	}
	
	if (var_have_heatbed == true) {
		$("div.heat_bed_widget").show();
		
		if (var_contain_heatbed == true) {
			onBedSliderSwitched(true);
			$("div.heat_bed_pos_widget").show();
			
			$("input#enable_heatbed").change(function() {
				$("input#bed_temper_control").slider(($("input#enable_heatbed").is(":checked") ? "enable" : "disable"));
			});
		}
		else {
			onBedSliderSelected();
			$("div.heat_bed_neg_widget").show();
		}
	}
	else {
		onBedSliderSwitched(false);
	}
});
</script>
</div>

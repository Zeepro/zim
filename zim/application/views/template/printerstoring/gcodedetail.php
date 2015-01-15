<div id="overlay"></div>
<div data-role="page">
	<style>
		input[type=number] { display : none !important; }
	</style>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
		<form action="/printdetail/printgcode_temp?id={id}" method="POST" data-ajax="false">
			<h2 style="text-align: center;">{title}</h2>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{photo_title}</h4>
				<img src="/printerstoring/getpicture?type=gcode&id={id}" style="max-width: 100%;"><br>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{title_current}</h4>
				<div style="height:265px">
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
							<a href="/printerstate/changecartridge?v=l&f={need_filament_l}&id={model_id}" data-role="button" data-ajax="false" data-iconpos="none" class="ui-shadow ui-corner-all">{change_filament_l}</a>
						</div>
						<div class="ui-block-b">
							<a href="/printerstate/changecartridge?v=r&f={need_filament_r}&id={model_id}" data-role="button" data-ajax="false" data-iconpos="none" class="ui-shadow ui-corner-all">{change_filament_r}</a>
						</div>
					</div>
					<div class="ui-grid-a">
						<div class="ui-block-a">
							{temp_adjustments_l} <span id="temperature_text_1">{temper_filament_l}°C</span>
						</div>
						<div class="ui-block-b">
							{temp_adjustments_r} <span id="temperature_text_2">{temper_filament_r}°C</span>
						</div>
						<div class="ui-block-a" id="div-slider1">
							<input type="range" name="l" id="slider-1" value="{temper_filament_l}" min="160" max="260" data-show-value="true">
						</div>
						<div class="ui-block-b" id="div-slider2">
							<input type="range" name="r" id="slider-2" value="{temper_filament_r}" min="160" max="260" data-show-value="true">
						</div>
					</div>
				</div>
			</div>
			<div style="clear: both;">
				<input type="hidden" name="exchange" id="exchange_extruder_hidden" value="0">
				<input type="submit" value="{print_button}" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" />
			</div>
			</form>
		</div>
	</div>

<script>
var var_enable_print = {enable_print};
var limit_min_tmp = {temper_min};
var limit_max_tmp = {temper_max};
var delta_tmp = {temper_delta};
var tmp = $("#slider-2").val();
var min_tmp = tmp - delta_tmp;
var max_tmp = parseInt(tmp) + delta_tmp;

$("#slider-2").attr('min', (min_tmp < limit_min_tmp) ? limit_min_tmp : min_tmp);
$("#slider-2").attr('max', (max_tmp > limit_max_tmp) ? limit_max_tmp : max_tmp);

tmp = $("#slider-1").val();
min_tmp = tmp - delta_tmp;
max_tmp = parseInt(tmp) + delta_tmp;

$("#slider-1").attr('min', (min_tmp < limit_min_tmp) ? limit_min_tmp : min_tmp);
$("#slider-1").attr('max', (max_tmp > limit_max_tmp) ? limit_max_tmp : max_tmp);

$("input[type=submit]").on('click', function()
{
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
});

if ("{state_f_l}" == "{error}" || "{state_f_r}" == "{error}")
{
	$("#slider-" + ("{state_f_l}" == "{error}" ? "1" : "2")).attr("disabled", "disabled");
}

// if ("{state_f_l}" != "ok" || "{state_f_r}" != "ok")
// {
// 	$("input[type=submit]").attr("disabled", "disabled");
// }
$(document).on("pagecreate",function() {
	if (var_enable_print == false) {
// 		$("input[type=submit]").attr("disabled", "disabled");
		$("input[type=submit]").button("disable");
	}
});

$("#div-slider1").on("change", function()
{
	$("#temperature_text_1").html($("#slider-1").val() + "°C");
});

$("#div-slider2").on("change", function()
{
	$("#temperature_text_2").html($("#slider-2").val() + "°C");
});
</script>
</div>

<?php //TODO exchange also filament quantity in change cartridge link - need to create new javascript function instead of a fixed link ?>

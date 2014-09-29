<div id="overlay"></div>
<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
		<form action="/printdetail/printmodel_temp?id={model_id}" method="POST" data-ajax="false">
			<h2 style="text-align: center;">{title}</h2>
			<div data-role="collapsible" data-collapsed="false">
				<h4>{desp_title}</h4>
				<p>{desp}</p>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{preview_title}</h4>
				<img src="{image}" style="max-width: 100%;"><br>
				<p>{color_suggestion}</p>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 75px; height: 75px; background-color: {model_c_l}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
				</div>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 75px; height: 75px; background-color: {model_c_r}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
				</div><br>
				<p>{time}</p>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{title_current}</h4>
				<div class="ui-grid-a">
					<div id="exchange_container" class="ui-bar ui-bar-f" style="height:3em;">
						<label>
							<input type="checkbox" name="exchange_interface" id="exchange_extruder" value="1" {enable_exchange}>{exchange_extruder}
						</label>
					</div>
				</div>
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
					<div>{temp_adjustments}</div>
					<div class="ui-grid-a">
						<div class="ui-block-a">
							<input type="range" name="l" id="slider-1" value="{temper_filament_l}" min="160" max="260">
						</div>
						<div class="ui-block-b">
							<input type="range" name="r" id="slider-2" value="{temper_filament_r}" min="160" max="260">
						</div>
					</div>
				</div>
			</div>
			<div style="clear: both;">
				<input type="hidden" name="exchange" id="exchange_extruder_hidden" value="0">
				<input type="submit" value="{print_model}" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" />
			</div>
			</form>
		</div>
	</div>

<script>
var var_enable_print = {enable_print};
var tmp = $("#slider-2").val();
var min_tmp = tmp - 10;

$("#slider-2").attr('min', (min_tmp < 165) ? 165 : min_tmp); 
$("#slider-2").attr('max', parseInt(tmp) + 10);

tmp = $("#slider-1").val();
min_tmp = tmp - 10;

$("#slider-1").attr('min', (min_tmp < 165) ? 165 : min_tmp); 
$("#slider-1").attr('max', parseInt(tmp) + 10);

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

//assign trigger for exchange extruder
$("input#exchange_extruder").change(function() {
	// switch print on and exchange off in some special cases
	if (var_enable_print == false) {
		$("input#exchange_extruder").slider({disabled: true});
		$("input#exchange_extruder").slider("refresh");
		$("input[type=submit]").button("refresh");
		$("input[type=submit]").button("enable");
	}
	
	$("p#state_f_r").html('{filament_ok}');
	$("p#state_f_l").html('{filament_ok}');
	
	if ($("input#exchange_extruder").is(":checked")) {
		$("input#exchange_extruder_hidden").val("1");
	}
	else {
		$("input#exchange_extruder_hidden").val("0");
	}
});

</script>
</div>

<?php //TODO exchange also filament quantity in change cartridge link - need to create new javascript function instead of a fixed link ?>

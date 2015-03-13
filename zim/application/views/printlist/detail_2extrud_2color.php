<div id="overlay"></div>
<div id="{random_prefix}printModel_detailPage" data-role="page">
	<style> input[type=number] { display : none !important; } </style>
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
					<div class="ui-grid-a">
						<div class="ui-block-a">
							{temp_adjustments_l}
						</div>
						<div class="ui-block-b">
							{temp_adjustments_r}
						</div>
						<div class="ui-block-a" id="div-slider1">
							<input type="range" name="l" id="slider-l" value="{temper_filament_l}" min="160" max="260" data-show-value="true">
						</div>
						<div class="ui-block-b" id="div-slider2">
							<input type="range" name="r" id="slider-r" value="{temper_filament_r}" min="160" max="260" data-show-value="true">
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
var var_suggest_temper_right = {temper_suggest_r};
var var_suggest_temper_left = {temper_suggest_l};
var limit_min_tmp = {temper_min};
var limit_max_tmp = {temper_max};
var delta_tmp = {temper_delta};
var slider_l = "input#slider-l";
var slider_r = "input#slider-r";

$("#{random_prefix}printModel_detailPage").on("pagecreate",function() {
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
	$("input#slider-r").slider("refresh");
	
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
	$("input#slider-l").slider("refresh");
	
	$("input[type=submit]").on('click', function()
	{
		$("#overlay").addClass("gray-overlay");
		$(".ui-loader").css("display", "block");
	});
	
	if ("{state_f_r}" == "{error}") {
		$(slider_r).slider({disabled: true});
	}
	if ("{state_f_l}" == "{error}") {
		$(slider_l).slider({disabled: true});
	}
	
	if (var_enable_print == false) {
		$("input[type=submit]").button("disable");
	}
	
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
});
</script>
</div>

<?php //TODO exchange also filament quantity in change cartridge link - need to create new javascript function instead of a fixed link ?>

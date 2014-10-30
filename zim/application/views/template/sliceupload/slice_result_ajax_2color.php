
				<form action="/printdetail/printslice_temp" method="POST" data-ajax="false">
					<div id="exchange_container" class="ui-bar ui-bar-f" style="height:3em;">
						<label>
							<input type="checkbox" name="exchange_interface" id="exchange_extruder" value="1" {enable_exchange}>{exchange_extruder}
						</label>
					</div>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div id="left_cartridge" class="ui-bar ui-bar-f">
							<div style="width: 75px; height: 75px; background-color: {cartridge_c_l}; margin: 0 auto;">
								<img src="/images/cartridge.png" style="width: 100%">
							</div>
							<p id="state_f_l">{state_f_l}</p>
							<p>{temper_l} °C</p>
						</div></div>
						<div class="ui-block-b"><div id="right_cartridge" class="ui-bar ui-bar-f">
							<div style="width: 75px; height: 75px; background-color: {cartridge_c_r}; margin: 0 auto;">
								<img src="/images/cartridge.png" style="width: 100%">
							</div>
							<p id="state_f_r">{state_f_r}</p>
							<p>{temper_r} °C</p>
						</div></div>
					</div>
					<p style="text-align: left;">{error_msg}</p>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div id="left_cartridge" class="ui-bar ui-bar-f">
							<div id="temper_l">
								<label>Left temperature</label>
								<input type="range" id="slider_left" name="l" value="{temper_l}" min="160" max="260" />
							</div>
						</div></div>
						<div class="ui-block-b"><div id="right_cartridge" class="ui-bar ui-bar-f">
							<div id="temper_r">
								<label>Right temperature</label>
								<input type="range" id="slider_right" name="r" value="{temper_r}" min="160" max="260" />
							</div>
						</div></div>
<!-- 						<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;"> -->
<!-- 							<label for="exchange_extruder"><h2>{exchange_extruder}</h2></label> -->
<!-- 						</div></div> -->
<!-- 						<div class="ui-block-b"> -->
<!-- 						</div> -->
						<input type="hidden" name="exchange" id="exchange_extruder_hidden" value="0">
						<input type="submit" id="print_slice" value="{print_button}">
					</div>
				</form>

<script type="text/javascript">
var var_enable_print = {enable_print};
var var_reslice = {enable_reslice};
var var_need_refresh_preview = false;
var var_need_print_right = {needprint_right};
var var_need_print_left = {needprint_left};
var var_bicolor_model = {bicolor_model};

$("input[type=submit]").on('click', function()
{
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
});
$('<div>').appendTo('#left_cartridge')
.attr({'id': 'change_left', 'data-icon': 'refresh', 'data-iconpos':'right', 'onclick': 'javascript: window.location.href="/printerstate/changecartridge?v=l&f={need_filament_l}&id=slice";'}).html('{change_left}')
.button().button('refresh');
$('<div>').appendTo('#right_cartridge')
.attr({'id': 'change_right', 'data-icon': 'refresh', 'data-iconpos':'right', 'onclick': 'javascript: window.location.href="/printerstate/changecartridge?v=r&f={need_filament_r}&id=slice";'}).html('{change_right}')
.button().button('refresh');
// $('<button>').appendTo('#detail_zone')
// .attr({'id': 'print_slice', 'onclick': 'javascript: window.location.href="/printdetail/printslice";'}).html('{print_button}')
// .button().button('refresh');

var limit_min_tmp = {temper_min};
var limit_max_tmp = {temper_max};
var delta_tmp = {temper_delta};
var tmp = $("#slider_right").val();
var min_tmp = tmp - delta_tmp;
var max_tmp = parseInt(tmp) + delta_tmp;

$("#slider_right").attr('min', (min_tmp < limit_min_tmp) ? limit_min_tmp : min_tmp); 
$("#slider_right").attr('max', (max_tmp > limit_max_tmp) ? limit_max_tmp : max_tmp);

tmp = $("#slider_left").val();
min_tmp = tmp - delta_tmp;
max_tmp = parseInt(tmp) + delta_tmp;

$("#slider_left").attr('min', (min_tmp < limit_min_tmp) ? limit_min_tmp : min_tmp); 
$("#slider_left").attr('max', (max_tmp > limit_max_tmp) ? limit_max_tmp : max_tmp);

$('#detail_zone').trigger("create");

if (var_need_print_right == false) {
	$('input#slider_right').slider({disabled: true});
}
if (var_need_print_left == false) {
	$('input#slider_left').slider({disabled: true});
}
	
if (var_enable_print == false) {
	$("#print_slice").button("disable");
}
if (var_reslice == true) {
	$('<div>').appendTo('#detail_zone')
	.attr({'id': 'reslice_button', 'onclick': 'javascript: startSlice(true);'}).html('{reslice_button}')
	.button().button('refresh');
}

if (var_bicolor_model == false) {
	$('#exchange_container').addClass("switch-larger");
}

// assign new preview color
var_color_right = '{cartridge_c_r}';
var_color_left = '{cartridge_c_l}';

// if (var_color_right != '{cartridge_c_r}') {
// 	var_color_right = '{cartridge_c_r}';
// 	var_need_refresh_preview = true;
// }
// if (var_color_left != '{cartridge_c_l}') {
// 	var_color_left = '{cartridge_c_l}';
// 	var_need_refresh_preview = true;
// }

$("#preview_zone").show();
// if (var_need_refresh_preview) {
	getPreview(false);
// }

// assign trigger for exchange extruder
$("input#exchange_extruder").change(function() {
	// switch print on and exchange off in some special cases
	if (var_enable_print == false) {
		$("input#exchange_extruder").slider({disabled: true});
		$("input#exchange_extruder").slider('refresh');
		$("#print_slice").button("enable");
	}
	
	// switch temperature slider and state message if it's mono-color model
	if (var_bicolor_model == false) {
		if (var_need_print_right) {
			$('input#slider_right').slider({disabled: true});
			$('input#slider_left').slider({disabled: false});
			$("p#state_f_l").html('{filament_ok}');
			$("p#state_f_r").html('{filament_not_need}');
			var_need_print_right = false;
			var_need_print_left = true;
		}
		else { // var_need_print_left
			$('input#slider_right').slider({disabled: false});
			$('input#slider_left').slider({disabled: true});
			$("p#state_f_r").html('{filament_ok}');
			$("p#state_f_l").html('{filament_not_need}');
			var_need_print_left = false;
			var_need_print_right = true;
		}
	}
	else {
		$("p#state_f_r").html('{filament_ok}');
		$("p#state_f_l").html('{filament_ok}');
	}
	
	// inverse color and get new preview image if necessary
	if (var_color_right != var_color_left) {
		var temp_color = var_color_right;
		
		var_color_right = var_color_left;
		var_color_left = temp_color;
		getPreview(false);
	}
	
	if ($("input#exchange_extruder").is(":checked")) {
		$("input#exchange_extruder_hidden").val("1");
	}
	else {
		$("input#exchange_extruder_hidden").val("0");
	}
});

</script>

				<div class="ui-grid-a">
					<div class="ui-block-a"><div id="left_cartridge" class="ui-bar ui-bar-f">
						<div style="width: 75px; height: 75px; background-color: {cartridge_c_l}; margin: 0 auto;">
							<img src="/images/cartridge.png" style="width: 100%">
						</div>
						<p>{state_f_l}</p>
						<p>{temper_l} °C</p>
					</div></div>
					<div class="ui-block-b"><div id="right_cartridge" class="ui-bar ui-bar-f">
						<div style="width: 75px; height: 75px; background-color: {cartridge_c_r}; margin: 0 auto;">
							<img src="/images/cartridge.png" style="width: 100%">
						</div>
						<p>{state_f_r}</p>
						<p>{temper_r} °C</p>
					</div></div>
				</div>
				<p style="text-align: left;">{error_msg}</p>
				<form action="/printdetail/printslice_temp" method="POST" data-ajax="false">
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
							<label for="exchange_extruder"><h2>{exchange_extruder}</h2></label>
						</div></div>
						<div class="ui-block-b">
							<div class="ui-bar ui-bar-f" style="height:3em;">
								<select name="exchange" id="exchange_extruder" data-role="slider" data-track-theme="a" data-theme="a">
									<option value="{exchange_o1_val}">{exchange_o1}</option>
									<option value="{exchange_o2_val}" {exchange_o2_sel}>{exchange_o2}</option>
								</select>
							</div>
						</div>
						<div id="temper_l">
							<label>Left temperature</label>
							<input type="range" id="slider_left" name="l" value="{temper_l}" min="160" max="260" />
						</div>
						<div id="temper_r">
							<label>Right temperature</label>
							<input type="range" id="slider_right" name="r" value="{temper_r}" min="160" max="260" />
						</div>
						<input type="submit" id="print_slice" value="{print_button}">
					</div>
				</form>

<script type="text/javascript">
var var_enable_print = {enable_print};
var var_reslice = {enable_reslice};
var var_need_refresh_preview = false;

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

if ($("#slider_left").attr('value') == "---")
	$("#temper_l").css('display', 'none');
if ($("#slider_right").attr('value') == "---")
	$("#temper_r").css('display', 'none');

var tmp = $("#slider_right").val();
var min_tmp = tmp - 10;

$("#slider_right").attr('min', (min_tmp < 165) ? 165 : min_tmp); 
$("#slider_right").attr('max', parseInt(tmp) + 10);

tmp = $("#slider_left").val();
min_tmp = tmp - 10;

$("#slider_left").attr('min', (min_tmp < 165) ? 165 : min_tmp); 
$("#slider_left").attr('max', parseInt(tmp) + 10);

$('#detail_zone').trigger("create");
	
if (var_enable_print == false) {
	$("#print_slice").button("disable");
}
if (var_reslice == true) {
	$('<div>').appendTo('#detail_zone')
	.attr({'id': 'reslice_button', 'onclick': 'javascript: startSlice(true);'}).html('{reslice_button}')
	.button().button('refresh');
}

// assign new preview color
if (var_color_right != '{cartridge_c_r}') {
	var_color_right = '{cartridge_c_r}';
	var_need_refresh_preview = true;
}
if (var_color_left != '{cartridge_c_l}') {
	var_color_left = '{cartridge_c_l}';
	var_need_refresh_preview = true;
}

$("#preview_zone").show();
if (var_need_refresh_preview) {
	getPreview(false);
}

</script>
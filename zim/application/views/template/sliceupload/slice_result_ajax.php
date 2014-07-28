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
				<div id="temper_l">
					<label>Left temperature</label>
					<input type="range" id="slider_left" value="{temper_l}" min="160" max="260" />
				</div>
				<div id="temper_r">
					<label>Right temperature</label>
					<input type="range" id="slider_right" value="{temper_r}" min="160" max="260" />
				</div>
				<p style="text-align: left;">{error_msg}</p>

<script type="text/javascript">
<!--
var var_enable_print = {enable_print};
var var_reslice = {enable_reslice};

$('<button>').appendTo('#left_cartridge')
.attr({'id': 'change_left', 'data-icon': 'refresh', 'data-iconpos':'right', 'onclick': 'javascript: window.location.href="/printerstate/changecartridge?v=l&f={need_filament_l}&id=slice";'}).html('{change_left}')
.button().button('refresh');
$('<button>').appendTo('#right_cartridge')
.attr({'id': 'change_right', 'data-icon': 'refresh', 'data-iconpos':'right', 'onclick': 'javascript: window.location.href="/printerstate/changecartridge?v=r&f={need_filament_r}&id=slice";'}).html('{change_right}')
.button().button('refresh');
$('<button>').appendTo('#detail_zone')
.attr({'id': 'print_slice', 'onclick': 'javascript: window.location.href="/printdetail/printslice";'}).html('{print_button}')
.button().button('refresh');

if ($("#slider_left").attr('value') == "---")
	$("#temper_l").css('display', 'none');
if ($("#slider_right").attr('value') == "---")
	$("#temper_r").css('display', 'none');

var tmp = $("#slider_right").val();

$("#slider_right").attr('min', tmp - 10); 
$("#slider_right").attr('max', parseInt(tmp) + 10);

tmp = $("#slider_left").val();

$("#slider_left").attr('min', tmp - 10); 
$("#slider_left").attr('max', parseInt(tmp) + 10);

$('#detail_zone').trigger("create");
	
if (var_enable_print == false) {
	$("button#print_slice").button("disable");
}
if (var_reslice == true) {
	$('<button>').appendTo('#detail_zone')
	.attr({'id': 'reslice_button', 'onclick': 'javascript: startSlice();'}).html('{reslice_button}')
	.button().button('refresh');
}
-->
</script>
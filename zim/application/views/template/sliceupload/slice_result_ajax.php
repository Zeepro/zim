				<div class="ui-grid-a">
					<div class="ui-block-a"><div id="left_cartridge" class="ui-bar ui-bar-f">
						<div style="width: 75px; height: 75px; background-color: {cartridge_c_l}; margin: 0 auto;">
							<img src="/images/cartridge.png" style="width: 100%">
						</div>
						<p>{state_f_l}</p>
						<p>{temper_l} °C</p>
						<input type="range" value="{temper_l}" />
					</div></div>
					<div class="ui-block-b"><div id="right_cartridge" class="ui-bar ui-bar-f">
						<div style="width: 75px; height: 75px; background-color: {cartridge_c_r}; margin: 0 auto;">
							<img src="/images/cartridge.png" style="width: 100%">
						</div>
						<p>{state_f_r}</p>
						<p>{temper_r} °C</p>
						<input type="range" value="{temper_r}" />
					</div></div>
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
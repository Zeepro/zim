<button id="unload_button" onclick='javascript: inputUserChoice("unload");'>{unload_button}</button>
<br />
<button id="prime_button" onclick='prime()'>{prime_button}</button>

<script type="text/javascript">
<!--
var_next_phase = '{next_phase}';
var_enable_unload = {enable_unload};

function prime() {
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
	window.location.href="/printdetail/printprime?v={abb_cartridge}&cb={id_model}";
}
	
$('#cartridge_detail_info').trigger("create");

if (var_enable_unload == true) {
// 	$('#unload_button').button("enable");
	$('#unload_button').attr('enable', 'enable');
}
else {
// 	$('#unload_button').button().disable();
	$('#unload_button').attr('disabled', 'disabled');
	<?php
	//TODO add an ajax to check temperature here
	/*  /printerstate/changecartridge_temper?v={abb_cartridge}
	 *  200 => maintain disable lock; 202 => release disable lock
	 *  403 => invalid abb cartridge; 500 => internal error
	 */
	?>
}
-->
</script>

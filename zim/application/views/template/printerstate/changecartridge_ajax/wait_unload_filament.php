<button id="unload_button" onclick='javascript: inputUserChoice("unload");'>{unload_button}</button>
<br />
<button id="prime_button" onclick='prime()'>{prime_button}</button>

<script type="text/javascript">
var_next_phase = '{next_phase}';

function prime()
{
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
	window.location.href="/printdetail/printprime?v={abb_cartridge}&cb={id_model}";
}
	
$('#cartridge_detail_info').trigger("create");
</script>

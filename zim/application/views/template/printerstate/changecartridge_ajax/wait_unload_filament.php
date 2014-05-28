
<script type="text/javascript">
var_next_phase = '{next_phase}';
$('<button>').appendTo('#cartridge_detail_info')
.attr({'id': 'unload_button', 'onclick': 'javascript: inputUserChoice("unload");'}).html('{unload_button}')
.button().button('refresh');
$('<button>').appendTo('#cartridge_detail_info')
.attr({'id': 'prime_button', 'onclick': 'javascript:  window.location.href="/printdetail/printprime?v={abb_cartridge}&cb={id_model}";'}).html('{prime_button}')
.button().button('refresh');
</script>

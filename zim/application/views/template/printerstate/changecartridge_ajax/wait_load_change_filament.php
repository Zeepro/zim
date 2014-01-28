
<script type="text/javascript">
var_next_phase = '{next_phase}';
$('<button>').appendTo('#cartridge_detail_info')
.attr({'id': 'load_button', 'onclick': 'javascript: inputUserChoice("load");'}).html('{load_button}')
.button().button('refresh');
$('<button>').appendTo('#cartridge_detail_info')
.attr({'id': 'change_button', 'onclick': 'javascript: inputUserChoice("change");'}).html('{change_button}')
.button().button('refresh');
</script>

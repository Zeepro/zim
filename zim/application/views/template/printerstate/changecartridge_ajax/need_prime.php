				<p>{question}</p>

<script type="text/javascript">
var_next_phase = '{next_phase}';
$('<div>').appendTo('#cartridge_detail_info')
.attr({'id': 'yes_button', 'onclick': 'javascript: window.location.href="{yes_url}";'}).html('{yes_button}')
.button().button('refresh');
$('<div>').appendTo('#cartridge_detail_info')
.attr({'id': 'no_button', 'onclick': 'javascript: window.location.href="{no_url}";'}).html('{no_button}')
.button().button('refresh');
</script>

				<p>{question}</p>

<script type="text/javascript">
var_next_phase = '{next_phase}';

function yes_func()
{
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
	window.location.href="{yes_url}";
}

$('<div>').appendTo('#cartridge_detail_info')
.attr({'id': 'yes_button', 'onclick': 'yes_func()'}).html('{yes_button}')
.button().button('refresh');
$('#cartridge_detail_info').append("<br />");
$('<div>').appendTo('#cartridge_detail_info')
.attr({'id': 'no_button', 'onclick': 'javascript: window.location.href="{no_url}";'}).html('{no_button}')
.button().button('refresh');
</script>

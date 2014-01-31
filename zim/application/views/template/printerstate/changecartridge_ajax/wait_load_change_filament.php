				<div id="cartridge_color_info">{wait_info}</div><br/><br/><br/><br/><br/><br/>

<script type="text/javascript">
var_next_phase = '{next_phase}';

$.ajax({
	url: "/printerstate/changecartridge_action/detail",
	type: "GET",
	data: {
		v: "{abb_cartridge}",
		id: "{id_model}",
		},
	cache: false,
})
.done(function(html) {
	$("#cartridge_color_info").html(html);
})
.fail(function() { // not allowed
// 	window.location.replace("/");
	alert('failed');
});

$('<button>').appendTo('#cartridge_detail_info')
.attr({'id': 'load_button', 'onclick': 'javascript: inputUserChoice("load");'}).html('{load_button}')
.button().button('refresh');
$('<button>').appendTo('#cartridge_detail_info')
.attr({'id': 'change_button', 'onclick': 'javascript: inputUserChoice("change");'}).html('{change_button}')
.button().button('refresh');
</script>

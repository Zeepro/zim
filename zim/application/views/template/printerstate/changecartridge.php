<div data-role="page" data-url="/printerstate/changecartridge">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2 style="text-align: center;">{title}</h2>
			<div id="cartridge_detail_info" style="text-align: center;">
				<p>{wait_info}</p>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var var_refreshChangeStatus;
var var_ajax;
var var_next_phase = '{first_status}';
$(document).ready(checkChangeStatus());

function checkChangeStatus() {
 	var_refreshChangeStatus = setInterval(refreshChangeStatus, 1000);
	function refreshChangeStatus() {
		var_ajax = $.ajax({
			url: "/printerstate/changecartridge_ajax",
			type: "POST",
			data: {
				abb_cartridge: "{abb_cartridge}",
				need_filament: "{need_filament}",
				mid: "{id_model}",
				next_phase: var_next_phase,
				},
			cache: false,
		})
		.done(function(html) {
			if (var_ajax.status == 202) { // finished checking, wait user to input
				clearInterval(var_refreshChangeStatus);
				$("#cartridge_detail_info").html(html);
			}
			else if (var_ajax.status == 200) { // in checking
				$("#cartridge_detail_info").html(html);
			}
		})
		.fail(function() { // not allowed
			window.location.replace("/");
// 			clearInterval(var_refreshChangeStatus);
// 			$("#print_detail_info").html('<p>{finish_info}</p>');
// 			$('button#print_action').click(function(){window.location.href='/'; return false;});
// 			$('button#print_action').parent().find('span.ui-btn-text').text('{return_button}');
// 			$('button#print_action').html('{return_button}');
// 			alert("failed");
		});
	}
}

function inputUserChoice(action) {
	var var_action = null;

	switch (action) {
		case 'load':
			var_action = $.ajax({
				url: "/printerstate/changecartridge_action/load",
				type: "GET",
				data: {v: "{abb_cartridge}"},
				cache: false,
			});
			break;

		case 'unload':
			var_action = $.ajax({
				url: "/printerstate/changecartridge_action/unload",
				type: "GET",
				data: {v: "{abb_cartridge}"},
				cache: false,
			});
			break;

		case 'change':
			var_next_phase = '{insert_status}';
			checkChangeStatus();
			break;

		default:
// 			alert("unknown action");
			return false;
			break;
	}

	if (var_action) {
		var_action.done(function() {
// 			alert("done choice");
			checkChangeStatus();
		}).fail(function() {
// 			alert("failed choice");
		});
	}

	return false;
}

</script>

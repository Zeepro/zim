<div data-role="page" data-url="/sliceupload/upload">
	<header data-role="header" class="page-header"></header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div id="wait_message">{wait_message}</div>
		</div>
	</div>
</div>

<script type="text/javascript">
<!--
var var_ajax;

$(document).ready(add_model());

function add_model() {
	var_ajax = $.ajax({
		url: "/sliceupload/add_model_ajax",
		type: "POST",
		data: {
			file: '{model_name}',
			},
		cache: false,
		timeout: 1000*60*10,
	})
	.done(function(html) {
		$('#wait_message').html("{fin_message}");
		setTimeout(function(){
			window.location.href="/sliceupload/slice";
			}, 3000);
	})
	.fail(function() { // not in printing
		$('#wait_message').html("{fail_message}");
		$('<div>').appendTo('#container')
		.attr({'id': 'return_button', 'onclick': 'javascript: window.location.href="/sliceupload/upload";'}).html('{return_button}')
		.button().button('refresh');
	});
}
-->
</script>
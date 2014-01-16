<div data-role="page" data-url="/printdetail/status">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			<h2>{title}</h2>
			<video width="320" height="240" autoplay controls>
				<source src="/zim.m3u8" type="application/x-mpegURL" />
				Your browser does not support HTML5 streaming!
			</video>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{print_detail}</h4>
				<div id="print_detail_info">
					<p>{wait_info}</p>
				</div>
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-delete">{print_stop}</button>
			</div>
		</div>
	</div>
</div>

<script>
// $(document).ready(refreshPrintStatus());
$(document).ready(checkPrintStatus());

function checkPrintStatus() {
	function refreshPrintStatus() {
		$.ajax({
			url: "/printdetail/status_ajax",
			cache: false,
		})
		.done(function(html) {
			$("#print_detail_info").html(html);
		})
		.fail(function() {
//			alert("done");
			window.location.replace("/menu_home");
		});
	}
 	var var_refreshPrintStatus = setInterval(refreshPrintStatus, 1000);
}
</script>


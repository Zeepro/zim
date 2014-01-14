<div data-role="page" data-url="/printdetail/status">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			<h2>{title}</h2>
			<canvas id="videoCanvas" width="640" height="480">
				<p>
					Please use a browser that supports the Canvas Element, like
					<a href="http://www.google.com/chrome">Chrome</a>,
					<a href="http://www.mozilla.com/firefox/">Firefox</a>,
					<a href="http://www.apple.com/safari/">Safari</a> or Internet Explorer 10
				</p>
			</canvas>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{print_detail}</h4><br>
				<div id="print_detail_info">
					<p>{print_percent}</p>
					<p>{print_remain}</p>
					<p>{print_temperL}</p>
					<p>{print_temperR}</p>
				</div>
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-delete">{print_stop}</button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	function refreshPrintStatus() {
		$.ajax({
			url: "/printdetail/status_ajax",
			cache: false,
		})
		.done(function ( html ) {
			$( "#print_detail_info" ).html( html );
		})
		.fail(function () {
// 			alert("done");
			window.location.replace("/menu_home");
		});
	}
	setInterval(refreshPrintStatus, 1000);
});
</script>


<div data-role="page" data-url="/connection/in_progress">
	<header data-role="header" class="page-header"></header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align:center;">
			{config_printer}
		</div>
	</div>

<script>
setTimeout(function() {
	var interval;
	
	interval = setInterval(function() {
		$.ajax(
		{
			url: "http://{hostname}.local",
			type: "GET",
			statusCode: {
				200: function() {
					clearInterval(interval);
					window.location.href="http://{hostname}.local";
				}
			}
		});
	}, 1000);
}, 30000);
</script>

</div>
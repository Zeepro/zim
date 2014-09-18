<div data-role="page" data-url="/connection/in_progress">
	<header data-role="header" class="page-header"></header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align:center;">
			{config_printer}
		</div>
	</div>

<script>

setTimeout(function()
{
	var interval;
	
	interval = setInterval(function()
	{
		var image = new Image();

		image.src = "http://{hostname}.local/assets/images/pixel.png";
		setTimeout(function()
		{
			if (image.height != 0)
			{
				clearInterval(interval);
				window.location.href="http://{hostname}.local";
			}
		}, 1000);
	}, 2000);
}, 30000);
</script>

</div>
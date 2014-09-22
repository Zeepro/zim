<div data-role="page">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>

			<form action="/connection/wifipswd" method="post" data-ajax="false"
				accept-charset="utf-8">
				<label for="password">{label}</label>
				<input type="hidden" name="ssid" id="ssid" value="{ssid}">
				<input type="hidden" name="mode" id="mode" value="{mode}">
				<input type="password" name="password" id="password" value="" />
				<label for="password_confirm">{confirm_password}</label>
				<input type="password" name="password_confirm" />
				<div>
					<input type="submit" value="{submit}" />
				</div>

			</form>
		</div>
	</div>
	<script>
		$("input[type=submit]").on('click', function()
		{
			$(".ui-loader").css('display', 'block');
		});
	</script>
</div>
<div data-role="page">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>
			<div class="zim-error">{err_msg}</div>
			<form method="post"
				accept-charset="utf-8">
				<label for="password">{label}</label>
				<input type="hidden" name="ssid" id="ssid" value="{ssid}">
				<input type="hidden" name="mode" id="mode" value="{mode}">
				<input type="password" name="password" id="password" value="" />
				<label for="password_confirm">{confirm_password}</label>
				<input type="password" name="password_confirm" />
				<br />
				<input type="submit" value="{submit}" />
			</form>
		</div>
	</div>
	<script>
	$(document).on("pagebeforehide", function() {
		$(".ui-loader").css("display", "none");
		$("#overlay").removeClass("gray-overlay");
	});
		$("input[type=submit]").on('click', function()
		{
			$(".ui-loader").css('display', 'block');
		});
	</script>
</div>
<div data-role="page" data-url="/connection/wifip2p">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>
			<form method="post" accept-charset="utf-8">
				<p>{ssid_title}</p>
				<input type="text" name="ssid" id="ssid" value=""  data-clear-btn="true"/>
				<p>{pwd_title}</p>
				<input type="password" name="pwd" id="pwd" value=""  data-clear-btn="true" autocomplete="off"/>
				{error}
				<div>
					<input type="submit" value="{ok}" />
				</div>
			</form>
		</div>
	</div>
</div>
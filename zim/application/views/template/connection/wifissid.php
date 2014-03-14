<div data-role="page" data-url="/connection/wifissid">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>
			<ul data-role="listview" data-inset="true" id="listview"
				class="shadowBox">
				{list_ssid}
				<li><a href="/connection/wifipswd?ssid={link}">{name}</a></li>
				{/list_ssid}
				<li><a href="/connection/wifinotvisiblessid" data-prefetch>{no_visable}</a></li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

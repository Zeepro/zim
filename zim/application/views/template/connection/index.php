<div data-role="page" data-url="/connection">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>
			<a href="/printerstate/sethostname" data-role="button">{set_hostname}</a><br>
			{hint}<br><br>
			<ul data-role="listview" data-inset="true" id="listview"
				class="shadowBox">
				<li><a href="/connection/wifissid" data-prefetch>{wifissid}</a></li>
				<li><a href="/connection/wifip2p" data-prefetch>{wifip2p}</a></li>
				<li><a href="/connection/wired" data-prefetch>{wired}</a></li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

<div data-role="page" data-url="/printerstate">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true">
				<li><a href="/printerstate/resetnetwork">
					<h2>{reset_network}</h2></a>
				</li>
				<li><a href="#" onclick="javascript: window.location.href='/printerstate/changecartridge?v=l&f=0';">
					<h2>{change_left}</h2></a>
				</li>
				<li><a href="#" onclick="javascript: window.location.href='/printerstate/changecartridge?v=r&f=0';">
					<h2>{change_right}</h2></a>
				</li>
				<li><a href="/printerstate/printerinfo">
					<h2>{printer_info}</h2></a>
				</li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

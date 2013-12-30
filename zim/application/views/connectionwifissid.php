<div data-role="page" data-url="/connection/wifissid">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back"><?= t('Back') ?></a>
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2><?= t('WiFi network connected to the Internet') ?></h2>

			<ul data-role="listview" data-inset="true" id="listview"
				class="shadowBox">
		<?php
		
foreach ( $listSSID as $ssid ) {
			echo '<li><a href="/connection/wifipswd?ssid=' . htmlspecialchars ( rawurlencode ( $ssid ) ) . '">' . htmlspecialchars ( $ssid ) . '</a></li>';
		}
		?>
		<li><a href="/connection/wifinotvisiblessid" data-prefetch><?= htmlspecialchars(t("Not visible...")) ?></a></li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

<div data-role="page" data-url="connection">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back"><?= t('Back') ?></a>
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2><?= t('Connection configuration') ?></h2>
    		<?= t("Welcome...") ?><br> <br>
			<ul data-role="listview" data-inset="true" id="listview"
				class="shadowBox">
				<li><a href="/connection/wifissid" data-prefetch><?= htmlspecialchars(t("Option 1")) ?></a></li>
				<li><a href="/connection/wifip2p" data-prefetch><?= htmlspecialchars(t("Option 3")) ?></a></li>
				<li><a href="/connection/wired" data-prefetch><?= htmlspecialchars(t("Option 2")) ?></a></li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

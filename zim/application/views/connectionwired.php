<div data-role="page" data-url="/connection/wired">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back"><?= t('Back') ?></a>
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2><?= t('Wired network connection') ?></h2>

			<?= t("Text")?> 
			<ul data-role="listview" data-inset="true" id="listview"
				class="shadowBox">
				<li><a href="/connection/wiredauto" data-prefetch><?= htmlspecialchars(t("OK")) ?></a></li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
			<?= t("Text2")?>
			<ul data-role="listview" data-inset="true" id="listview"
				class="shadowBox">
				<li><a href="/connection/wiredadvanced" data-prefetch><?= htmlspecialchars(t("Advanced")) ?></a></li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

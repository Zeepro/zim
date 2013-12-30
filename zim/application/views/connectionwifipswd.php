<div data-role="page" data-url="/connection/wifipswd">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back"><?= t('Back') ?></a>
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2><?= htmlspecialchars(t("network", $ssid)) ?></h2>

			<form action="/connection/wifipswd" method="post"
				accept-charset="utf-8">

				<label for="ip"><?= htmlspecialchars(t("network password")) ?></label>
				<input type="password" name="password" id="password" value=""/>

				<div>
					<input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" />
				</div>

			</form>
		</div>
	</div>
</div>

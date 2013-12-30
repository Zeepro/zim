<div data-role="page" data-url="/connection/wifinotvisiblessid">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back"><?= t('Back') ?></a>
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">

			<h2><?= t('WiFi network connected to the Internet') ?></h2>

			<form action="/connection/wifinotvisiblessid" method="post"
				accept-charset="utf-8">

				<input type="text" name="ssid" id="ssid" value=""  data-clear-btn="true"/>
				<?php echo form_error('ssid'); ?>
				<div>
					<input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" />
				</div>
			</form>
		</div>
	</div>
</div>
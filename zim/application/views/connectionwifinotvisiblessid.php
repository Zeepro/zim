<div id="container">

    <h2><?= t('WiFi network connected to the Internet') ?></h2>

	<form action="/connection/wifinotvisiblessid" method="post" accept-charset="utf-8">	
	
	<label for="ssid"><?= htmlspecialchars(t("Please enter your SSID")) ?></label>
	<input type="text" name="ssid" id="ssid" value=""  />
	<?php echo form_error('ssid'); ?>
	
	<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
	</form>
</div>

<div id="container">
    <h2><?= t('WiFi network connected to the Internet') ?></h2>

	<form action="/connection/wifipswd" method="post" accept-charset="utf-8">	
	
	<label for="password"><?= htmlspecialchars(t("network password", $ssid)) ?></label>
	<input type="text" name="password" id="password" value=""  />
	    
	<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
	
	</form>
</div>

<div style="height:30px">
    <div style="position:absolute;right:50px">
        <a href ="/en/connection/wifinetwork"><img src="/images/en.png" border="0"></a>
        <a href ="/fr/connection/wifinetwork"><img src="/images/fr.png" border="0"></a>
    </div>
</div>

<div id="container">
    <h2><?= t('WiFi network connected to the Internet') ?></h2>

	<?php echo validation_errors(); ?>
	
	<?php echo form_open('connection/wifinetwork'); ?>
	
	<label for="ssid" class="select"><?= htmlspecialchars(t("SSID")) ?></label>
	<select name="ssid" id="ssid" data-native-menu="false">
		<option value="choose" data-placeholder="true"><?= htmlspecialchars(t("Choose one...")) ?></option>
		<?php foreach ($listSSID as $ssid) {
		    echo '<option value="' . htmlspecialchars($ssid) . '">' . htmlspecialchars($ssid) . '</option>';
		}
		?>
	</select>
	
	<label for="password"><?= htmlspecialchars(t("Password")) ?></label>
	<input type="text" name="password" id="password" value=""  />
	    
	<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
	
	</form>
</div>

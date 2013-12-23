<div id="container">
    <h2><?= t('WiFi network connected to the Internet') ?></h2>

	<?php echo validation_errors(); ?>
	
	<?php echo form_open('connection/wifissid'); ?>
	
	<label for="ssid" class="select"><?= htmlspecialchars(t("SSID")) ?></label>
	<select name="ssid" id="ssid" data-native-menu="false">
		<option value="choose" data-placeholder="true"><?= htmlspecialchars(t("Choose one...")) ?></option>
		<?php foreach ($listSSID as $ssid) {
		    echo '<option value="' . htmlspecialchars($ssid) . '">' . htmlspecialchars($ssid) . '</option>';
		}
		?>
	</select>
	
	<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
	
	</form>
</div>

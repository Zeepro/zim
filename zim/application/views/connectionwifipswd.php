<div id="container">
    <h2><?= t('WiFi network connected to the Internet') ?></h2>

	<?php echo validation_errors(); ?>
	
	<?php echo form_open('connection/wifipswd'); ?>
	
	<label for="password"><?= htmlspecialchars(t("Password")) ?></label>
	<input type="text" name="password" id="password" value=""  />
	    
	<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
	
	</form>
</div>

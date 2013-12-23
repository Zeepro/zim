<div id="container">
    <h2><?= t('Advanced wired network connection') ?></h2>

	<?php echo validation_errors(); ?>
	
	<?php echo form_open('connection/wiredadvanced'); ?>
	
		<label for="ip"><?= htmlspecialchars(t("ip")) ?></label>
		<input type="text" name="ip" id="ip" value="" style="width:20%" />
		<br/>
		<label for="mask"><?= htmlspecialchars(t("mask")) ?></label>
		<input type="text" name="mask" id="mask" value=""  />
		<br/>
		<label for="gateway"><?= htmlspecialchars(t("gateway")) ?></label>
		<input type="text" name="gateway" id="gateway" value=""  />
		<br/>
		<label for="dns"><?= htmlspecialchars(t("dns")) ?></label>
		<input type="text" name="dns" id="dns" value=""  />
		<br/>
		<?= t("Text") ?>
		<br/>
		<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
	</form>
</div>

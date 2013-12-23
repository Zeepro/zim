<div id="container">
    <h2><?= t('Wired network connection') ?></h2>

	<?php echo validation_errors(); ?>
	
	<?php echo form_open('connection/wired'); ?>
	
		<?= t("Text") ?>
		<br/>
		<br/>
		<input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" />
		<br/>
		<?= t("Text2") ?>
		<br/>
		<br/>
		<ul data-role="listview" data-inset="true" id="listview" class="shadowBox">
			<li><a href="/connection/wiredadvanced"><?= htmlspecialchars(t("Advanced")) ?></a></li>
	    </ul>
	    <img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
	</form>
</div>

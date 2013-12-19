<div style="height:30px">
    <div style="position:absolute;right:50px">
        <a href ="/en/connection/wired"><img src="/images/en.png" border="0"></a>
        <a href ="/fr/connection/wired"><img src="/images/fr.png" border="0"></a>
    </div>
</div>

<div id="container">
    <h2><?= t('Wired network connection') ?></h2>

	<?php echo validation_errors(); ?>
	
	<?php echo form_open('connection/wired'); ?>
	
		<?= t("Text") ?>
		<br/>
		<br/>
				<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
		<br/>
		<?= t("Text2") ?>
		<br/>
		<br/>
		<div><a href="/connection/wiredadvanced" data-role="button"><?= htmlspecialchars(t("Advanced")) ?></a></div>
		
	</form>
</div>

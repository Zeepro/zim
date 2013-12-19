<div style="height:30px">
    <div style="position:absolute;right:50px">
        <a href ="/en/connection/wiredadvanced"><img src="/images/en.png" border="0"></a>
        <a href ="/fr/connection/wiredadvanced"><img src="/images/fr.png" border="0"></a>
    </div>
</div>

<div id="container">
    <h2><?= t('Advanced wired network connection') ?></h2>

	<?php echo validation_errors(); ?>
	
	<?php echo form_open('connection/wiredadvanced'); ?>
	
		<?= t("Text") ?>
		<br/>
<label for="limit">Limit length of 5</label>
        <input type="text" maxlength="5" id="limit" style="width:75px"/>
        
        		<label for="ipa"><?= htmlspecialchars(t("ip")) ?></label>
		<input type="text" name="ipa" id="ipa" value="" style="width:20%" />
		<input type="text" name="ipb" id="ipb" value="" style="width:20%" />
		<input type="text" name="ipc" id="ipc" value="" style="width:20%" />
		<input type="text" name="ipd" id="ipd" value="" style="width:20%" />
		<br/>
		<br/>
		<label for="maska"><?= htmlspecialchars(t("mask")) ?></label>
		<input type="text" name="maska" id="maska" value=""  />
		<input type="text" name="maskb" id="maskb" value=""  />
		<input type="text" name="maskc" id="maskc" value=""  />
		<input type="text" name="maskd" id="maskd" value=""  />
		<br/>
		<br/>
		<label for="gatewaya"><?= htmlspecialchars(t("gateway")) ?></label>
		<input type="text" name="gatewaya" id="gatewaya" value=""  />
		<input type="text" name="gatewayb" id="gatewayb" value=""  />
		<input type="text" name="gatewayc" id="gatewayc" value=""  />
		<input type="text" name="gatewayd" id="gatewayd" value=""  />
		<br/>
		<br/>
		<label for="dnsa"><?= htmlspecialchars(t("dns")) ?></label>
		<input type="text" name="dnsa" id="dnsa" value=""  />
		<input type="text" name="dnsb" id="dnsb" value=""  />
		<input type="text" name="dnsc" id="dnsc" value=""  />
		<input type="text" name="dnsd" id="dnsd" value=""  />
		<br/>
		<div><input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" /></div>
	</form>
</div>

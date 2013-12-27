<div data-role="page" data-url="connectionwiredadvanced">
	<header data-role="header" class="page-header">
		<a data-icon="arrow-l" data-role="button" data-direction="reverse"
			data-rel="back"><?= t('Back') ?></a>
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2><?= t('Advanced wired network connection') ?></h2>

			<form action="/connection/wiredadvanced" method="post" accept-charset="utf-8" data-ajax="false">

				<label for="ip"><?= htmlspecialchars(t("ip")) ?></label>
				<input type="text" name="ip" id="ip" value="<?php echo set_value('ip'); ?>" />
				<?php echo form_error('ip'); ?>
				<br />
				<label for="mask"><?= htmlspecialchars(t("mask")) ?></label>
				<input type="text" name="mask" id="mask" value="<?php echo set_value('mask'); ?>" />
				<?php echo form_error('mask'); ?>
				<br />
				<label for="gateway"><?= htmlspecialchars(t("gateway")) ?></label>
				<input type="text" name="gateway" id="gateway" value="<?php echo set_value('gateway'); ?>" />
				<?php echo form_error('gateway'); ?>
				<br />
				<label for="dns"><?= htmlspecialchars(t("dns")) ?></label>
				<input type="text" name="dns" id="dns" value="<?php echo set_value('dns'); ?>" />
				<?php echo form_error('dns'); ?>
				<br />
				<?= t("Text")?>
				<br />
				<div>
					<input type="submit" value="<?= htmlspecialchars(t("OK")) ?>" />
				</div>
			</form>
		</div>
	</div>
</div>

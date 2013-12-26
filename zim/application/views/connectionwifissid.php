<div id="container">
    <h2><?= t('WiFi network connected to the Internet') ?></h2>

	<label for="ssid" class="select"><?= htmlspecialchars(t("Choose your network:")) ?></label>
	<ul data-role="listview" data-inset="true" id="listview" class="shadowBox">
		<?php foreach ($listSSID as $ssid) {
		    echo '<li><a href="/connection/wifipswd?ssid=' . htmlspecialchars($ssid) . '">' . htmlspecialchars($ssid) . '</a></li>';
		}
		?>
		<li><a href="/connection/wired" data-theme="c"><?= htmlspecialchars(t("Not visible...")) ?></a></li>
	</ul>
    <img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
</div>

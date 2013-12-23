<div id="container">
    <h2><?= t('Connection configuration') ?></h2>
    <?= t("Welcome...") ?><br>
    <br>
	<ul data-role="listview" data-inset="true" id="listview" class="shadowBox">
		<li><a href="/connection/wifissid"><?= htmlspecialchars(t("Option 1")) ?></a></li>
        <li><a href="/connection/wifip2p"><?= htmlspecialchars(t("Option 3")) ?></a></li>
		<li><a href="/connection/wired"><?= htmlspecialchars(t("Option 2")) ?></a></li>
    </ul>
    <img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
</div>
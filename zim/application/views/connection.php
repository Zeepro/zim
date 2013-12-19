<div style="height:30px">
    <div style="position:absolute;right:50px">
        <a href ="/en/connection"><img src="/images/en.png" border="0"></a>
        <a href ="/fr/connection"><img src="/images/fr.png" border="0"></a>
    </div>
</div>
<div id="container">
    <h2><?= t('Connection configuration') ?></h2>
    <?= t("Welcome...") ?><br>
    <br>
    <table width="100%">
        <tr>
            <td width="100%" align="center">
            	<a href="/connection/wifinetwork" data-role="button"><?= htmlspecialchars(t("Option 1")) ?></a>
            </td>
        </tr>
        <tr>
            <td width="100%" align="center">
	            <a href="/connection/wifip2p" data-role="button"><?= htmlspecialchars(t("Option 3")) ?></a>
            </td>
        </tr>
        <tr>
	        <td width="100%" align="center">
	            <a href="/connection/wired" data-role="button"><?= htmlspecialchars(t("Option 2")) ?></a>
            </td>
        </tr>
    </table>
</div>
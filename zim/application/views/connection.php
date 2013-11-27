<div style="height:30px">
    <div style="position:absolute;right:50px">
        <a href ="/en/connection"> <img src="/images/en.png" border="0"></a>
        <a href ="/fr/connection" ><img src="/images/fr.png" border="0"></a>
    </div>
</div>
<div id="container">
    <h2><?= t('Connection configuration') ?></h2>
    <?= t("Welcome...") ?><br>
    <br>
    <table width="100%">
        <tr height="100px">
            <td width="50%" align="center">
                <form action="connection/wifinetwork" method="GET">
                    <input type="submit" value="<?= htmlspecialchars(t("Option 1")) ?>" style="height:100px;width:450px;white-space:normal">
                </form>
            </td>
            <td width="50%" align="center">
                <form action="connection/wirednetwork" method="GET">
                    <input type="submit" value="<?= htmlspecialchars(t("Option 2")) ?>" style="height:100px;width:450px;white-space:normal">
                </form>
            </td>
        </tr>
        <tr height="100px" align="center">
            <td width="50%">
                <form action="connection/wifip2p" method="GET">
                    <input type="submit" value="<?= htmlspecialchars(t("Option 3")) ?>" style="height:100px;width:450px;white-space:normal">
                </form>
            </td>
            <td width="50%" align="center">
                <form action="connection/wiredp2p" method="GET">
                    <input type="submit" value="<?= htmlspecialchars(t("Option 4")) ?>" style="height:100px;width:450px;white-space:normal">
                </form>
            </td>
        </tr>
    </table>
</div>
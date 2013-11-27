<div>
    <ul id="menu1">
        <li><a href="./"><?= t('Print model') ?></a></li>
        <li><a href="param"><?= t('Setup') ?></a></li>
        <li><a href="#"><?= t('Test menu') ?></a>
            <ul>
                <li>
                    <a href="#"><?= t('Test option 1') ?></a>
                </li>
                <li>
                    <a href="#"><?= t('Test option 2') ?></a>
                </li>
            </ul>
        </li>

    </ul>
    <div id="flag">
        <a href ="/en/"> <img src="/images/en.png" border="0"></a>
        <a href ="/fr/" ><img src="/images/fr.png" border="0"></a>
    </div>
</div>
<div id="container">
    <?= $message ?>
</div>

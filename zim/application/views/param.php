
<?
/**
 * ZeePro PP21
 *
 * Parameters page
 */
$config = & get_config();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $arr = array('fill_option' => $_POST['fill_option']);
        $fh = fopen($config['base_data'] . 'test.config', 'w') or die("can't open file");
        fwrite($fh, json_encode($arr));
        fclose($fh);
        break;
    default: // GET
        $arr = json_decode(file_get_contents($config['base_data'] . 'test.config'), true);
        break;
}
?>

<div>
    <ul id="menu1">
        <li><a href="./"><?= t('Print model') ?></a></li>
        <li><a href="param"><b><?= t('Setup') ?></b></a></li>
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
    <h2><?= t('Setup') ?></h2>

    <div id="body">
        <h3><?= t('Filling') ?></h3>
        <form name="fill_option" action="#" method="POST">
            <input type="radio" name="fill_option" value="hollow" onClick="this.form.submit();"
            <? if ($arr['fill_option'] == 'hollow') echo "checked"; ?>
                   ><?= t('hollow') ?><br>
            <input type="radio" name="fill_option" value="strong" onClick="this.form.submit();"
            <? if ($arr['fill_option'] == 'strong') echo "checked"; ?>
                   ><?= t('strong') ?><br>
            <input type="radio" name="fill_option" value="solid" onClick="this.form.submit();"
            <? if ($arr['fill_option'] == 'solid') echo "checked"; ?>
                   ><?= t('solid') ?>
        </form>
    </div>
</div>
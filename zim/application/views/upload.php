<!DOCTYPE html>
<html lang="<?= $lang ?>">
      <head>
        <meta charset="utf-8">
        <title><?= t('ZeePro Personal Printer 21 - Select model') ?></title>
        <link rel="stylesheet" href="/styles/jquery.mobile-1.3.0.min.css" />
        <script src="/scripts/jquery-1.9.1.min.js"></script>
        <script>
            $(document).bind("mobileinit", function() {
                $.mobile.defaultPageTransition = 'none';
            });
        </script>
        <script src="/scripts/jquery.mobile-1.3.0.min.js"></script>
    </head>
    <body>
        <div data-role="page">
            <div data-role="header" data-theme="d" data-position="fixed">
                <a href="/" data-icon="home"><?= t("Home") ?></a>
                <h1><?= t("Select a model") ?></h1>
            </div>
            <form action="/index.php/upload" method="post" accept-charset="utf-8" enctype="multipart/form-data" data-ajax="false">
                <br>
                <br>
                <br>
                <label for="userfile"><?= t("File") ?></label>
                <input type="file" name="userfile" size="20" />
                <br>
                <INPUT TYPE="hidden" NAME="champ_de_test" VALUE="valeur de test">
                <input type="submit" value="<?= t("Upload") ?>" />
            </form>
            <?php echo $error; ?>
        </div>
	</body> 
</html>



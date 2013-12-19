<!DOCTYPE html>
<html lang="<?= $lang ?>">
      <head>
        <meta charset="utf-8">
        <title><?= t("ZeePro Personal Printer 21 - Language select") ?></title>
        <link rel="stylesheet" href="/styles/flag.css" />
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
            <div data-role="header" data-theme="d">
                <h1><?= t("Select your language") ?></h1>
            </div>     
            <div data-role="content">
                <ul data-role="listview" data-inset="true" data-theme="d">
                    <li data-icon="false"><a href="/en/menu" data-ajax="false"><img src="/images/en.png" class="ui-li-icon ui-corner-none"><?= t('english') ?></a></li>
                    <li data-icon="false"><a href="/fr/menu" data-ajax="false"><img src="/images/fr.png" class="ui-li-icon ui-corner-none"><?= t('french') ?></a></li>
                </ul>
            </div>
        </div>
	</body> 
</html>
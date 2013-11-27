<!DOCTYPE html>
<html lang="<?= $lang ?>">
	<head>
        <meta charset="utf-8">
        <?= $header ?>
        <link rel="stylesheet" href="/styles/jquery.mobile-1.3.0.min.css" />
        <script src="/scripts/jquery-1.9.1.min.js"></script>
        <script>
            $(document).bind("mobileinit", function() {
                $.mobile.defaultPageTransition = 'none';
            });
        </script>
        <script src="/scripts/jquery.mobile-1.3.0.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, target-densitydpi=medium-dpi, user-scalable=0" />
	</head>
    <body>
        <div data-role="page">
            <div data-role="header" data-theme="b" data-position="fixed">
                <h1><?= t("ZeePro Personal Printer 21") ?></h1>
                <a href="menu" data-icon="bars" class="ui-btn-right"><?= t("Menu") ?></a>
            </div>
            <div data-role="content">
                <?= $contents ?>
            </div>
        </div>
	</body> 
</html>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
      <head>
        <meta charset="utf-8">
        <title><?= t("ZeePro Personal Printer 21 - Operation in progress...") ?></title>
        <meta http-equiv="Refresh" content="2" />
        <link rel="stylesheet" href="/styles/flag.css" />
        <link rel="stylesheet" href="/styles/jquery.mobile-1.3.0.min.css" />
        <script src="/scripts/jquery-1.9.1.min.js"></script>
        <script>
            $(document).bind("mobileinit", function() {
                $.mobile.defaultPageTransition = 'none';
                $.mobile.defaultDialogTransition = "none";
            });
        </script>
        <script src="/scripts/jquery.mobile-1.3.0.min.js"></script>
    </head>
    <body>
        <div data-role="dialog" data-close-btn="none" data-theme="e">
            <div data-role="content">
                <?= t("Operation in progress...") ?>
            </div>
        </div>
	</body> 
</html>
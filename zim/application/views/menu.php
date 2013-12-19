<!DOCTYPE html>
<html lang="<?= $lang ?>">
      <head>
        <meta charset="utf-8">
        <title><?= t("ZeePro Personal Printer 21 - Main menu") ?></title>
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
            <div data-role="header" data-theme="d" data-position="fixed">
                <a href="/" data-icon="home" data-ajax="false"><?= t("Home") ?></a>
                <h1><?= t("Main menu") ?></h1>
            </div>
            <div data-role="content">
                <form action="/menu" method="post">
                    <fieldset data-role="controlgroup" data-mini="true">
                        <legend><?= t('Filling') ?></legend>
                        <input name="fill_option" id="radio-choice-v-6a" value="hollow" type="radio" <? if ($fill_option == 'hollow') echo "checked"; ?> onClick="this.form.submit();">
                        <label for="radio-choice-v-6a"><?= t('hollow') ?></label>
                        <input name="fill_option" id="radio-choice-v-6b" value="strong" type="radio" <? if ($fill_option == 'strong') echo "checked"; ?> onClick="this.form.submit();">
                        <label for="radio-choice-v-6b"><?= t('strong') ?></label>
                        <input name="fill_option" id="radio-choice-v-6c" value="solid" type="radio" <? if ($fill_option == 'solid') echo "checked"; ?> onClick="this.form.submit();">
                        <label for="radio-choice-v-6c"><?= t('solid') ?></label>
                    </fieldset>
                    <div data-role="fieldcontain">
                        <legend><?= t('Language') ?></legend>
                        <a data-role="button" data-icon="arrow-d" data-transition="none" data-rel="dialog" href="language"><img src="<?= $flag_url ?>">&nbsp;&nbsp;&nbsp;<?= $language ?></a>
                    </div>
                </form>
            </div>
        </div>
	</body> 
</html>
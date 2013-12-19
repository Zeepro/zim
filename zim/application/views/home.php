<!DOCTYPE html>
<html lang="<?= $lang ?>">
      <head>
        <meta charset="utf-8">
        <title><?= t('ZeePro Personal Printer 21') ?></title>
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
                <h1><?= t("ZeePro Personal Printer 21") ?></h1>
                <a href="menu" data-icon="bars" class="ui-btn-right"><?= t("Menu") ?></a>
            </div>
            <div data-role="content">
                <? if ($img) { ?>
                    <div id="model">
                        <div id="view" style="text-align:center;">
                            <img src="<?= $img ?>" width="300px" height="400px">
                        </div>
                        <div id="controls">
                            <form method="post" data-ajax="false">
                                <fieldset data-role="controlgroup" data-type="horizontal" style="text-align:center;">
                                    <input type="submit" data-role="button" data-inline="true" data-theme="d" name="submitbutton" value="&lt;&lt;" />
                                    <input type="submit" data-role="button" data-inline="true" data-theme="d" name="submitbutton" value="&lt;" />
                                    <select name="action" id="select-native-11" data-native-menu="false" data-inline="true">
                                        <option value="width"><?= t("width") ?></option>
                                        <option value="depth"<? if ($action == 'depth') echo " selected"; ?>><?= t("depth") ?></option>
                                        <option value="height"<? if ($action == 'height') echo " selected"; ?>><?= t("height") ?></option>
                                        <option value="X axis"<? if ($action == 'X axis') echo " selected"; ?>><?= t("X axis") ?></option>
                                        <option value="Y axis"<? if ($action == 'Y axis') echo " selected"; ?>><?= t("Y axis") ?></option>
                                        <option value="Z axis"<? if ($action == 'Z axis') echo " selected"; ?>><?= t("Z axis") ?></option>
                                        <option value="Size"<? if ($action == 'Size') echo " selected"; ?>><?= t("Size") ?></option>
                                    </select>
                                    <input type="submit" data-role="button" data-inline="true" data-theme="d" name="submitbutton" value="&gt;" />
                                    <input type="submit" data-role="button" data-inline="true" data-theme="d" name="submitbutton" value="&gt;&gt;" />
                                </fieldset>
                            </form>
                            <center>
                                <form method="post" data-ajax="false"><input type="hidden" name="action" value="reset" /><input type="submit" name="submitbutton" value="<?= t("Reset") ?>" data-inline="true"/></form>
                            </center>                                
                        </div>
                    </div>
                <? }
                ?> 
            </div>
            <div data-role="footer" data-theme="d" data-position="fixed">
                <div data-role="navbar">
                    <ul>

                        <? if ($img) { ?><li><form method="post" data-ajax="false">
                                    <input type="hidden" name="action" value="clear" />
                                    <input data-icon="delete" type="submit" name="submitbutton" value="<?= t("Clear model") ?>" />
                                </form></li><li>
                                <form method="post" data-ajax="false">
                                    <input type="hidden" name="action" value="print" />
                                    <input data-icon="edit" type="submit" name="submitbutton" value="<?= t("Print model") ?>" />
                                </form></li><? } else { ?>
                            <li><form action="upload" method="get">
                                    <input data-icon="plus" type="submit" name="submitbutton" value="<?= t("Upload model") ?>" />
                                </form></li>            <? } ?>
                    </ul>
                </div><!-- /navbar -->
            </div><!-- /footer -->
        </div>
	</body> 
</html>





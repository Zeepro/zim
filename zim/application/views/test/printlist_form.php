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
                <h1>Test - Peng</h1>
            </div>
            <?php $attributes = array('accept-charset' => 'utf-8', 'data-ajax' => 'false'); ?>
            <?php
//				echo form_open_multipart('t_printlist/send', $attributes);
              ?>
            <form action="/store" method="post" accept-charset="utf-8" enctype="multipart/form-data" data-ajax="false">
            <?php echo form_label('Name', 'L_Name'); ?>
            <?php echo form_input('n', 'Name'); ?>
            <?php echo form_label('Desp', 'L_Desp');?>
            <?php echo form_textarea('d', 'Desp'); ?>
            <?php echo form_label('Time', 'L_Time'); ?>
            <?php echo form_input('t', '3'); ?>
            <?php echo form_label('Length1', 'L_Leng1'); ?>
            <?php echo form_input('l1', '1'); ?>
            <?php echo form_label('Length2', 'L_Leng2'); ?>
            <?php echo form_input('l2', '2'); ?>
            <?php echo form_label('Gcode', 'L_Gcode'); ?>
            <?php echo form_upload('f'); ?>
            <?php echo form_label('Pic1', 'L_Pic1'); ?>
            <?php echo form_upload('p1'); ?>
            <?php echo form_label('Pic2', 'L_Pic2'); ?>
            <?php echo form_upload('p2'); ?>
            <?php echo form_label('Pic3', 'L_Pic3'); ?>
            <?php echo form_upload('p3'); ?>
            <?php echo form_label('Pic4', 'L_Pic4'); ?>
            <?php echo form_upload('p4'); ?>
            <?php echo form_label('Pic5', 'L_Pic5'); ?>
            <?php echo form_upload('p5'); ?>
            <?php echo form_submit('submit', 'submit'); ?>
            <?php echo form_close('<br />'); ?>
            <?php echo isset($error)?$error:''; ?>
        </div>
	</body> 
</html>



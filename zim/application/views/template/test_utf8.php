<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Test UTF8</title>
</head>
<body>
	<form action="/test_utf8" method="post" accept-charset="utf-8" enctype="multipart/form-data">
	<?php echo form_label('Chars', 'L_chars'); ?><br />
	<?php echo form_textarea('chars'); ?><br />
	<?php echo form_submit('submit', 'submit'); ?><br />
	<?php echo form_close('<br />'); ?>
	<p>page:</p>
	<pre>{display}</pre>
	<p>file:</p>
	<pre>{display_f}</pre>
	<p>id:</p>
	<pre>{display_id}</pre>
</body>
</html>
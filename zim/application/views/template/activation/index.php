<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>
			<br />
			<div id="error"><?php $this->load->helper('form'); echo validation_errors(); ?></div>
			<?php
				echo form_open('/account/signin/{returnUrl}', array('data-ajax' => "false"));
				echo form_label('Email', 'email');
				echo form_input('email') . '<br />' . '<br />';
				echo form_label('{password}', 'password');
				echo form_password('password') . '<br />';
				echo '<div>';
				echo '<label><input type="checkbox" name="show_pass" data-mini=true>{show_password}</label>';
				echo "</div><br />";
				echo form_submit('submit', '{sign_in}');
				echo form_close();
			?>
		</div>
	</div>
	<script>
	$(document).ready(function()
	{
		$(".ui-loader").css('display', 'none');
	});
	$("input[type=submit]").on('click', function()
	{
		$(".ui-loader").css('display', 'block');
	});
	$("input[name=show_pass]").on("click", function()
	{
		if ($("input[name=show_pass]").is(':checked'))
			$("input[name=password]").attr("type", "text");
		else
			$("input[name=password]").attr("type", "password");
	});
	</script>
</div>
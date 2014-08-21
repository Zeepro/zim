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
			<div id="error"><?php $this->load->helper('form'); echo validation_errors(); ?></div>
			<?php
				echo form_open('/account/signin/{returnUrl}', array('data-ajax' => "false"));
				echo form_label('Email', 'email') . '<br />';
				echo form_input('email') . '<br />' . '<br />';
				echo form_label('{password}', 'password') . '<br />';
				echo form_password('password') . '<br />' . '<br />';
				echo form_submit('submit', '{sign_in}');
				echo form_close();
			?>
		</div>
		<h2>{create_account}</h2>
		<a href="/account/signup/{returnUrl}" data-role="button" data-ajax="false">{sign_up}</a>
	</div>
</div>
<div data-role="page">
	<header data-role="header" class="page-header"></header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h1>My Zeepro</h1>
			<h2>Your Zeepro account</h2>
			<div id="error"><?php $this->load->helper('form'); echo validation_errors(); ?></div>
			<?php
				echo form_open('/account/signin', array('data-ajax' => "false"));
				echo form_label('Email', 'email') . '<br />';
				echo form_input('email') . '<br />' . '<br />';
				echo form_label('Password', 'password') . '<br />';
				echo form_password('password') . '<br />' . '<br />';
				echo form_submit('submit', 'Sign in');
				echo form_close();
			?>
		</div>
		<h2>Create a Zeepro account</h2>
		<a href="/account/signup" data-role="button" data-ajax="false" data-theme="b">Sign up</a>
	</div>
</div>
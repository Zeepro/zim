<div data-role="page">
	<header data-role="header" class="page-header"></header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h1>Sign Up</h1>
				<h3>Create your Zeepro account</h3>
		<?php
			$this->load->helper('form');
			
			echo form_open('/account/signup', array('data-ajax' => 'false'));
			echo form_label('Email', 'email');
			echo form_input('email').'<br />';
			echo form_label('Password', 'password');
			echo form_password('password').'<br />';
			echo form_label('Confirm Password', 'confirm');
			echo form_password('confirm').'<br />';
			echo '<br />';
			echo form_submit('submit', 'Sign up');
			echo form_close();
		?>
	<div>
		<p>An email with a confirmation code will be sent to this address</p>
	</div>
		<a href="/" data-role="button">Back</a>
		</div>
	</div>
</div>
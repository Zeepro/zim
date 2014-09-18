<div data-role="page">
	<header data-role="header" class="page-header"></header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h1>{signup_title}Sign Up</h1>
				<h3>{signup_text}Create your Zeepro account</h3>
		<?php
			$this->load->helper('form');
			
			echo form_open('/account/signup', array('data-ajax' => 'false'));
			echo form_label('Email', 'email');
			echo form_input('email').'<br />';
			echo form_label(t('Password'), 'password');
			echo form_password('password').'<br />';
			echo form_label(t('Confirm Password'), 'confirm');
			echo form_password('confirm').'<br />';
			echo '<br />';
			echo form_submit('submit', t('signup_title'));
			echo form_close();
		?>
	<div>
		<p>{confcode_hint}An email with a confirmation code will be sent to this address</p>
	</div>
		<a href="{btn_url}" data-role="button">{back_or_arleady}</a>
		<a href="/activation/wizard_confirm/skip" data-role="button" style="display:{has_skip}">{skip_title}</a>
		</div>
	</div>
</div>
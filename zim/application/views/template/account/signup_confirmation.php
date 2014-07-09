<div data-role="page">
	<header data-role="header" class="page-header"></header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h3>Confirm your Zeepro account</h3>
			<?php
				$this->load->helper('form');
			
				echo form_open('/account/signup_confirmation', array('data-ajax' => 'false'));
				echo '<p>Enter the confirmation code you got by email here :</p>';
				echo form_input('code');
				echo '<br />';
				echo form_submit('submit', 'Send code');
				echo form_close();
			?>
		</div>
	</div>
</div>
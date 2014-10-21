<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="{btn_url}" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h1>{signup_title}</h1>
				<h3>{signup_text}</h3>
		<?php
			$this->load->helper('form');
			
			echo form_open('/account/signup', array('data-ajax' => 'false'));
			echo form_label('Email', 'email');
			echo form_input('email').'<br />';
			echo form_label(t('Password'), 'password');
			echo form_password('password').'<br />';
			echo form_label(t('Confirm Password'), 'confirm');
			echo form_password('confirm') . '<br />';
			echo '<br /><div>';
			echo '<label><input type="checkbox" name="show_pass" data-mini=true>{show_password}</label>';
			echo "</div><br />";
			echo form_submit('submit', t('signup_title'));
			echo form_close();
		?>
	<div>
		<p>{confcode_hint}</p>
	</div>
		<a href="{btn_url}" data-role="button">{back_or_already}</a>
		<a href="/activation/wizard_confirm/skip" data-role="button" style="display:{has_skip}">{skip_title}</a>
		</div>
	</div>
	<script>
		$("form").on("submit", function()
		{
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		});
		$("input[name=show_pass]").on("click", function()
		{
			if ($("input[name=show_pass]").is(':checked'))
				$("input[type=password]").attr("type", "text");
			else
			{
				$("input[name=password]").attr("type", "password");
				$("input[name=confirm]").attr("type", "password");
			}
		});
	</script>
</div>
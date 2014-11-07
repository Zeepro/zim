<div data-role="page">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="skip_confirmation" style="display:none">
			<p>{confirm_skip_text}</p>
			<a href="/activation/wizard_confirm/skip" data-role="button">{still_skip}</a>
			<a class="lol" href="#" data-role="button">{back}</a>
		</div>
		<div id="container">
			<h1>{signup_title}</h1>
			<h3>{signup_text}</h3>
			<div class="zim-error">{error}</div>
			<br />	
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
		<a href="{btn_url}" data-role="button">{back_or_already}</a>
		<a class="lol" href="#" data-role="button" style="display:{has_skip}">{skip_title}</a>
		</div>
	</div>
	<script>
		$("a.lol").on("click", function()
		{
			$("div#container").toggle();
			$("div#skip_confirmation").toggle();
		});
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
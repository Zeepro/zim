<div data-role="page">
	<header data-role="header" class="page-header"></header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h1>Activation</h1>
			<div id="error"><?php $this->load->helper('form'); echo validation_errors(); ?></div>
			<form action="/activation/activation_form" data-ajax="false" method="POST">
				<input type="hidden" name="email" value='{email}' />
				<input type="hidden" name="password" value='{password}' />
				<label for="printer_name">Give a name to your printer : </label>
				<input type="text" name="printer_name" value="" />
				<input type="submit" name="submit" value="Activate my printer" />
			</form>
		</div>
	</div>
</div>
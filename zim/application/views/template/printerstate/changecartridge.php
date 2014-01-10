<div data-role="page" data-url="/printerstate/changecartridge">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2 style="text-align: center;">{title}</h2>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{step1_title}</h4><br>
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh">{step1_action}</button>
				<p id="step1-message"></p>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{step2_title}</h4><br>
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh">{step2_action}</button>
				<p id="step2-message"></p>
			</div>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

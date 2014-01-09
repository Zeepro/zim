<div data-role="page">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{title}</h4><br>
				<img src="{image}"><br>
				<div style="width: 16px; height: 16px; background-color: {model_c1}"></div>
				<div style="width: 16px; height: 16px; background-color: {model_c2}; margin-left: 30px;"></div><br>
				<p>{time}</p>
<!-- 				<h4>{title_current}</h4><br> -->
				<div style="width: 16px; height: 16px; background-color: {state_c1}"></div>
				<div style="width: 16px; height: 16px; background-color: {state_c2}; margin-left: 30px;"></div><br>
				<p>{state_f1}</p><p style="margin-left: 50px;">{state_f2}</p><br>
				<a href="#" class="ui-btn ui-btn-inline ui-icon-refresh ui-btn-icon-left">{change_filament}</a>
				<a href="#" class="ui-btn ui-btn-inline ui-icon-refresh ui-btn-icon-left">{change_filament}</a><br>
				<a href="/print?id={model_id}" class="ui-btn ui-btn-inline ui-icon-action ui-btn-icon-left">{print_model}</a>
			</div>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

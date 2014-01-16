<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2 style="text-align: center;">{title}</h2>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{preview_title}</h4><br>
				<img src="{image}" style="max-width: 100%;"><br>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 32px; height: 32px; background-color: {model_c1}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
				</div>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 32px; height: 32px; background-color: {model_c2}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
				</div><br>
				<p>{time}</p>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{title_current}</h4><br>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 32px; height: 32px; background-color: {state_c1}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
					<p>{state_f1}</p>
					<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" onclick="window.location.href='/printerstate/changecartridge?v=l'">{change_filament}</button>
				</div>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 32px; height: 32px; background-color: {state_c2}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
					<p>{state_f2}</p>
					<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" onclick="window.location.href='/printerstate/changecartridge?v=r'">{change_filament}</button>
				</div><br>
				<br><br><br><br>
<!-- 				<a href="/print?id={model_id}" class="ui-btn ui-btn-inline ui-icon-action ui-btn-icon-left">{print_model}</a> -->
			</div>
			<div style="clear: both;">
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" onclick="window.location.href='/printdetail/printmodel?id={model_id}'">{print_model}</button>
			</div>
		</div>
	</div>
</div>

<div id="detailPage" data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">Home</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
		<form action="/printdetail/printmodel_temp?id={model_id}" method="POST" data-ajax="false">
			<h2 style="text-align: center;">{title}</h2>
			<div data-role="collapsible" data-collapsed="false">
				<h4>{desp_title}</h4>
				<p>{desp}</p>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{preview_title}</h4>
				<img src="{image}" style="max-width: 100%;"><br>
				<div style="text-align: center;">
					<div style="width: 75px; height: 75px; background-color: {model_c_r}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
				</div><br>
				<p>{time}</p>
			</div>
<!-- 			<div data-role="collapsible" data-collapsed="false"> -->
<!-- 				<h4>Gestion des températures</h4> -->
<!-- 						<label>Température tête droite</label> -->
<!-- 			</div> -->
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{title_current}</h4>
				<div style="height:210px">
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 75px; height: 75px; background-color: {state_c_l}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
					<p>{state_f_l}</p>
<!-- 					<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" onclick="window.location.href='/printerstate/changecartridge?v=l&f={need_filament_l}&id={model_id}'">{change_filament_l}</button> -->
					<a href="/printerstate/changecartridge?v=l&f={need_filament_l}&id={model_id}" data-role="button" data-ajax="false" data-icon="refresh" class="ui-shadow ui-corner-all">{change_filament_l}</a>
				</div>
				<div style="width: 50%; float: right; text-align: center;">
					<div style="width: 75px; height: 75px; background-color: {state_c_r}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
					<p>{state_f_r}</p>
<!-- 					<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" onclick="window.location.href='/printerstate/changecartridge?v=r&f={need_filament_r}&id={model_id}'">{change_filament_r}</button> -->
					<a href="/printerstate/changecartridge?v=r&f={need_filament_r}&id={model_id}" data-role="button" data-ajax="false" data-icon="refresh" class="ui-shadow ui-corner-all">{change_filament_r}</a>
					<input type="range" name="r" id="slider-2" value="{temper_filament_r}" min="160" max="260">
				</div>
				</div>
<!-- 				<a href="/print?id={model_id}" class="ui-btn ui-btn-inline ui-icon-action ui-btn-icon-left">{print_model}</a> -->
			</div>
			<div style="clear: both;">
				<!-- <button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" onclick="window.location.href='/printdetail/printmodel?id={model_id}'">{print_model}</button>-->
				<input type="submit" value="{print_model}" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" />
			</div>
			</form>
		</div>
	</div>
</div>

<script>
	var tmp = $("#slider-2").val();

	$("#slider-2").attr('min', tmp - 10); 
	$("#slider-2").attr('max', parseInt(tmp) + 10);
</script>
<div id="overlay"></div>
<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
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
				<p>{color_suggestion}</p>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 75px; height: 75px; background-color: {model_c_l}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
				</div>
				<div style="width: 50%; float: left; text-align: center;">
					<div style="width: 75px; height: 75px; background-color: {model_c_r}; margin: 0 auto;">
						<img src="/images/cartridge.png" style="width: 100%">
					</div>
				</div><br>
				<p>{time}</p>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="text-align: center;">
				<h4>{title_current}</h4>
				<div style="height:265px">
					<div class="ui-grid-a">
						<div class="ui-block-a">
							<div style="width: 75px; height: 75px; background-color: {state_c_l}; margin: 0 auto;">
								<img src="/images/cartridge.png" style="width: 100%">
							</div>
						</div>
						<div class="ui-block-b">
							<div style="width: 75px; height: 75px; background-color: {state_c_r}; margin: 0 auto;">
								<img src="/images/cartridge.png" style="width: 100%">
							</div>
						</div>
						<div class="ui-block-a">
							<p>{state_f_l}</p>
						</div>
						<div class="ui-block-b">
							<p>{state_f_r}</p>
						</div>
						<div class="ui-block-a" style="padding-left:0px">
							<a href="/printerstate/changecartridge?v=l&f={need_filament_l}&id={model_id}" data-role="button" data-ajax="false" data-iconpos="none" class="ui-shadow ui-corner-all">{change_filament_l}</a>
						</div>
						<div class="ui-block-b">
							<a href="/printerstate/changecartridge?v=r&f={need_filament_r}&id={model_id}" data-role="button" data-ajax="false" data-iconpos="none" class="ui-shadow ui-corner-all">{change_filament_r}</a>
						</div>
					</div>
					<div>{temp_adjustments}</div>
					<div class="ui-grid-a">
						<div class="ui-block-a">
							<input type="range" name="l" id="slider-1" value="{temper_filament_l}" min="160" max="260">
						</div>
						<div class="ui-block-b">
							<input type="range" name="r" id="slider-2" value="{temper_filament_r}" min="160" max="260">
						</div>
					</div>
				</div>
			</div>
			<div style="clear: both;">
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

tmp = $("#slider-1").val();

$("#slider-1").attr('min', tmp - 10); 
$("#slider-1").attr('max', parseInt(tmp) + 10);

$("input[type=submit]").on('click', function()
{
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
});

if ("{state_f_l}" == "{error}" || "{state_f_r}" == "{error}")
{
	$("#slider-" + ("{state_f_l}" == "{error}" ? "1" : "2")).attr("disabled", "disabled");
}
</script>
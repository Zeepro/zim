<div data-role="page" data-url="/test_endstop">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="#" data-icon="back" data-ajax="false" style="visibility:hidden">{back}</a>
		<a href="#" onclick="javascript:window.location.href='/';" data-icon="home" data-ajax="false" style="float:right">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="background-color:white;">
			<div style="display:{error}">
				<span class="zim-error">Couldn't get endstop information</span>
			</div>
			<div>
				<div class="ui-grid-a" style="text-align:center;">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<label for="slider"><h2>Back</h2></label>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<select name="strip_led" id="ymax" data-role="slider" disabled>
								<option value="off" id="strip_off">Not touched</option>
								<option value="on" id="strip_on" {yback}>Touched</option>
							</select>
						</div>
					</div>
				</div>
				<div style="text-align:center;"><b>Y axis</b></div>
				<div class="ui-grid-a" style="text-align:center;">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<label for="slider"><h2>Front</h2></label>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<select name="strip_led" id="ymin" data-role="slider" disabled>
								<option value="off" id="strip_off">Not touched</option>
								<option value="on" id="strip_on" {yfront}>Touched</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<br />
			<br />
			<div>
				<div style="text-align:center;"><b>X axis</b></div>
				<div class="ui-grid-a" style="text-align:center;">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f">
							<label for="slider"><h2>Left</h2></label>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f">
							<label for="slider"><h2>Right</h2></label>
						</div>
					</div>
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f">
							<select name="strip_led" id="xmin" data-role="slider" disabled>
								<option value="off" id="strip_off">Not touched</option>
								<option value="on" id="strip_on" {xleft}>Touched</option>
							</select>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f">
							<select name="strip_led" id="xmax" data-role="slider" disabled>
								<option value="off" id="strip_off">Not touched</option>
								<option value="on" id="strip_on" {xright}>Touched</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<br />
			<br />
			<div>
				<div class="ui-grid-a" style="text-align:center;">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<label for="slider"><h2>Top</h2></label>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<select name="strip_led" id="zmin" data-role="slider" disabled>
								<option value="off" id="strip_off">Not touched</option>
								<option value="on" id="strip_on" {ztop}>Touched</option>
							</select>
						</div>
					</div>
				</div>
				<div style="text-align:center;"><b>Z axis</b></div>
				<div class="ui-grid-a" style="text-align:center;">
					<div class="ui-block-a">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<label for="slider"><h2>Bottom</h2></label>
						</div>
					</div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<select name="strip_led" id="zmax" data-role="slider" disabled>
								<option value="off" id="strip_off">Not touched</option>
								<option value="on" id="strip_on" {zbottom}>Touched</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<br />
			<br />
			<div class="ui-grid-a" style="text-align:center;">
				<div class="ui-block-a">
					<b>Left filament guide</b>
				</div>
				<div class="ui-block-b">
					<b>Right filament guide</b>
				</div>
				<div class="ui-block-a">
					<select name="strip_led" id="E1" data-role="slider" disabled>
						<option value="off" id="strip_off">Not touched</option>
						<option value="on" id="strip_on">Touched</option>
					</select>
				</div>
				<div class="ui-block-b">
					<select name="strip_led" id="E0" data-role="slider" disabled>
						<option value="off" id="strip_off">Not touched</option>
						<option value="on" id="strip_on">Touched</option>
					</select>				
				</div>
			</div>
		</div>
	</div>
	<script>
$(document).on('pageinit', function()
{
	$(".ui-slider").removeClass("ui-state-disabled");
})

setInterval(function()
{
	$.ajax({
		url:'/test_endstop/endstop_ajax'
	})
	.done(function(jsontab)
	{
		var endstop = JSON.parse(jsontab);

		$("#xmin").val(endstop["xmin"] ? "on" : "off");
		$("#xmax").val(endstop["xmax"] ? "on" : "off");
		$("#ymin").val(endstop["ymin"] ? "on" : "off");
		$("#ymax").val(endstop["ymax"] ? "on" : "off");
		$("#zmin").val(endstop["zmin"] ? "on" : "off");
		$("#zmax").val(endstop["zmax"] ? "on" : "off");
		$("#E0").val(endstop["E0"] ? "on" : "off");
		$("#E1").val(endstop["E1"] ? "on" : "off");

		$("select[data-role=slider]").slider("refresh");
		$(".ui-slider").removeClass("ui-state-disabled");
	});
}, 1000);
	</script>
</div>
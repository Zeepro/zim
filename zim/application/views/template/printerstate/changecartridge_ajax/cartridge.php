<script type="text/javascript" src="/scripts/spectrum.js"></script>
<link rel="stylesheet" type="text/css" href="/styles/spectrum.css" />
<link rel="stylesheet" type="text/css" href="/assets/jquery-mobile-fluid960.min.css" />

<div class="container_16">
	<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 2em;">
		<label for="showPaletteOnly">{color_label}</label>
	</div></div>
	<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 2em;">
		<input name="c" id="showPaletteOnly" value="black" data-role="none" />
	</div></div>
	<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 3em;">
		<label for="temper_input">{temper_label}</label>
	</div></div>
	<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
		<input type='range' name="t" id="temper_input" value="200" min="160" max="260" />
	</div></div>
	<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 3em;">
		<label for="length_input">{length_label}</label>
	</div></div>
	<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
		<input type='range' name="l" id="length_input" value="10" min="{length_min}" max="200" />
	</div></div>
</div>

<button onclick="javascript: inputUserChoice('write');">{write_button}</button>


<script type="text/javascript">
var_next_phase = '{next_phase}';

$("#showPaletteOnly").spectrum(
{
	showPaletteOnly: true,
	showPalette:true,
	preferredFormat:"name",
	palette:
	[
		['black', 'white', 'silver', 'cyan', 'orange', 'brown'],
		['red', 'yellow', 'blue', 'green', 'purple', 'pink']
	]
});

$("#home").trigger('create');
</script>
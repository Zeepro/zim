<center>	
	<label>Color</label>
	<input name="color" id="showPaletteOnly" data-role="none" />	
	<label>Temperature</label>
	<input type='range' name="slider_temp" id="slider-1" min="160" max="260"/>
	<label>Length</label>
	<input type='range' name="slider_length" id="slider-2" min="2" max="200"/>
</center>

<script>
	$("#showPaletteOnly").spectrum(
	{
		showPaletteOnly: true,
		showPalette:true,
		palette:
		[
			['black', 'white', 'silver', 'cyan', 'orange', 'brown'],
			['red', 'yellow', 'blue', 'green', 'purple', 'pink']
		]
	});
</script>
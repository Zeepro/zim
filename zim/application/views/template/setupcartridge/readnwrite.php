<div data-role="page" id="home">
	<script type="text/javascript" src="/scripts/spectrum.js"></script>
	<link rel="stylesheet" type="text/css" href="/styles/spectrum.css" />
	<link rel="stylesheet" type="text/css" href="/assets/jquery-mobile-fluid960.min.css" />
	
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a id="back_button" href="javascript:history.back();" data-icon="back" data-ajax="false" style="display:none">{back}</a>
		<a id="home_button" href="/" data-icon="home" data-ajax="false" style="display:none">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2 style="text-align: center;">Read 'n' Write - {side_cartridge} Cartridge</h2>
			
			<div class="container_16">
				<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 2em;">
					<label for="showPaletteOnly">Color</label>
				</div></div>
				<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 2em;">
					<input name="c" id="showPaletteOnly" value="{rfid_color}" data-role="none" />
				</div></div>
				<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 3em;">
					<label for="material_input">Material</label>
				</div></div>
				<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
					<select name="m" id="material_input">
						{material_array}
							<option value="{value}" {on}>{name}</option>
						{/material_array}
					</select>
				</div></div>
				<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 3em;">
					<label for="temper_input">Temperature</label>
				</div></div>
				<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
					<input type='range' name="t" id="temper_input" value="{temper_value}" min="160" max="260" />
				</div></div>
				<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 3em;">
					<label for="temper_first_input">Temperature (first layer)</label>
				</div></div>
				<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
					<input type='range' name="tf" id="temper_first_input" value="{temper_f_value}" min="160" max="260" />
				</div></div>
				<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 3em;">
					<label for="length_input">Length</label>
				</div></div>
				<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
					<input type='range' name="l" id="length_input" value="{length_value}" min="10" max="200" />
				</div></div>
			</div>
			
			<button onclick="javascript: inputUserChoice(flag);">Write</button>
			<div id="hint_message" class="zim-error">{error}</div>
		</div>
	</div>

<script type="text/javascript">
var_next_phase = '{next_phase}';
var flag = false;

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

$("input#showPaletteOnly").on('change', function() { flag = true; });
$("input#temper_input").on('change', function() { flag = true; });
$("input#temper_first_input").on('change', function() { flag = true; });
$("input#length_input").on('change', function() { flag = true; });
$("select#material_input").on('change', function() { flag = true; });

function inputUserChoice(flag) {
	if (flag == true) {
		var_action = $.ajax({
			url: "/setupcartridge/readnwrite_ajax",
			type: "GET",
			data: {
					c: $("#showPaletteOnly").val(),
					t: $("#temper_input").val(),
					l: $("#length_input").val(),
					m: $("#material_input").val(),
					v: "{abb_cartridge}",
					tf: $("#temper_first_input").val()
				},
			cache: false,
			beforeSend: function() {
				$("#hint_message").html('');
				$("#overlay").addClass("gray-overlay");
				$(".ui-loader").css("display", "block");
			},
			complete: function() {	
				$("#overlay").removeClass("gray-overlay");
				$(".ui-loader").css("display", "none");
			},
		});
	}
	else {
		$("#hint_message").html('Information is not changed');
	}
	
	if (var_action) {
		var_action.done(function() {
			$("#hint_message").html('Writing successed');
		}).fail(function() {
			$("#hint_message").html('Error in writing');
		});
	}

	return false;
}

</script>

</div>
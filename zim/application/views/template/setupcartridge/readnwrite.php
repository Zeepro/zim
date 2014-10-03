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
			<h2 style="text-align: center;">{side_cartridge}</h2>
			Initial code : <span id="code">{cartridge_code}</span>
			<table border="1">
				<tr>
					<td id="case_1"></td>
					<td id="case_2"></td>
					<td id="case_21"></td>
					<td id="case_3"></td>
					<td id="case_4"></td>
					<td id="case_5"></td>
					<td id="case_6"></td>
					<td id="case_7"></td>
					<td id="case_8"></td>
					<td id="case_9"></td>
					<td id="case_10"></td>
					<td id="case_11"></td>
					</tr>
				<tr>
					<td>Magic number</td>
					<td>Cartridge type</td>
					<td>Material</td>
					<td>Red</td>
					<td>Green</td>
					<td>Blue</td>
					<td>Initial length</td>
					<td>Used length</td>
					<td>Temp</td>
					<td>1st layer temp</td>
					<td>Packing date</td>
					<td>Checksum</td>
				</tr>
			</table>
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
					<label for="length_input">Initial length</label>
				</div></div>
				<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
					<input type='range' name="l" id="length_input" value="{initial_length_value}" min="10" max="200" />
				</div></div>
				<div class="grid_5"><div class="ui-bar ui-bar-d" style="height: 3em;">
					<label for="length_input">Used length</label>
				</div></div>
				<div class="grid_11"><div class="ui-bar ui-bar-c" style="height: 3em;">
					<input type='range' name="l" id="length_input" value="{used_length_value}" min="0" max="200" />
				</div></div>
			</div>
			
			<button onclick="javascript: inputUserChoice(flag);">Write</button>
			<div id="hint_message" class="zim-error">{error}</div>
		</div>
	</div>

<script type="text/javascript">
var_next_phase = '{next_phase}';
var flag = false;

var code = $("#code").html();

$("#case_1").html(code.substr(0, 4));
$("#case_2").html(code[4]);
$("#case_21").html(code[5]);
$("#case_3").html(code.substr(6, 2));
$("#case_4").html(code.substr(8, 2));
$("#case_5").html(code.substr(10, 2));
$("#case_6").html(code.substr(12, 5));
$("#case_7").html(code.substr(17, 5));
$("#case_8").html(code.substr(22, 2));
$("#case_9").html(code.substr(24, 2));
$("#case_10").html(code.substr(26, 4));
$("#case_11").html(code.substr(30, 2));

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

	return (false);
}

</script>

</div>
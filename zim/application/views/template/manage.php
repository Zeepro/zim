<div data-role="page" data-url="/manage">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible" style="align: center;">
				<h4>{head}</h4>
				<div class="container_16">
					<div class="grid_2 suffix_5">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home();">XYZ</a>
					</div>
					<div class="grid_2 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Y', 50);">50</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Y', 10);">10</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Y', 1);">1</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" onclick="move('X', -50);">50</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" onclick="move('X', -10);">10</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" onclick="move('X', -1);">1</a>
					</div>
					<div class="grid_3">
						<input type="number" data-clear-btn="false" name="xy_speed" id="xy_speed" value="30" min="10" max="35" />
					</div>
					<div class="grid_1" style="">mm/s</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" onclick="move('X', 1);">1</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" onclick="move('X', 10);">10</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left" onclick="move('X', 50);">50</a>
					</div>
					<div class="grid_2 suffix_5">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home('X');">X</a>
					</div>
					<div class="grid_2 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Y', -1);">1</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Y', -10);">10</a>
					</div>
					<div class="grid_2 prefix_7 suffix_1">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Y', -50);">50</a>
					</div>
					<div class="grid_2 suffix_4">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home('Y');">Y</a>
					</div>
				</div>
			</div>
			<div data-role="collapsible" style="align: center;">
				<h4>{platform}</h4>
				<div class="container_16">
					<div class="grid_2 prefix_7 suffix_5">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Z', -50);">50</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left" onclick="home('Z');">Z</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Z', -10);">10</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left" onclick="move('Z', -1);">1</a>
					</div>
					<div class="grid_3 prefix_6">
						<input type="number" data-clear-btn="false" name="z_speed" id="z_speed" value="10" min="1" max="10" />
					</div>
					<div class="grid_1 suffix_6">
						mm/s
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Z', 1);">1</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Z', 10);">10</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left" onclick="move('Z', 50);">50</a>
					</div>
				</div>
			</div>
			<div data-role="collapsible" style="align: center;">
				<h4>{filament}</h4>
				
				<ul data-role="listview" id="listview" data-inset="true">
					<li><a href="#" onclick="javascript: window.location.href='/printerstate/changecartridge?v=l&f=0';">
						<h2>{manage_left}</h2></a>
					</li>
					<li><a href="#" onclick="javascript: window.location.href='/printerstate/changecartridge?v=r&f=0';">
						<h2>{manage_right}</h2></a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var var_ajax;

function home(var_axis) {
	var var_url;

	var_axis = typeof var_axis !== 'undefined' ? var_axis : 'all';
	if (var_axis == 'all') {
		var_url = "/pronterface/home";
	}
	else {
		var_url = "/pronterface/home/" + var_axis;
	}
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	});

	return false;
}

function move(var_axis, var_value) {
	var var_url;
	var var_speed;

	var_axis = typeof var_axis !== 'undefined' ? var_axis : 'error';
	if (var_axis == 'error') {
		$("#gcode_detail_info").html('ERROR');
		return false;
	}
	else {
		if (var_axis == 'Z') {
			var_speed = $("#z_speed").val();
		}
		else {
			var_speed = $("#xy_speed").val();
		}
		var_url = "/pronterface/move/" + var_axis + '/' + var_value + '/' + var_speed;
	}
	var_ajax = $.ajax({
		url: var_url,
		type: "GET",
		cache: false,
		beforeSend: function()
		{
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
        },
        complete: function()
        {	
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
        },
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	});

	return false;
}

</script>

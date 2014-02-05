<div data-role="page" data-url="/gcode">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{button_get}</h4>
				<input type="text" name="get_gcode" id="get_gcode" value="">
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-delete" onclick="javascript: runGcodeGet();">{button_get}</button>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{button_post}</h4>
				<textarea cols="40" rows="8" name="post_gcode" id="post_gcode"></textarea>
				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-delete" onclick="javascript: runGcodePOST();">{button_post}</button>
			</div>
			<div id="gcode_detail_info"></div>
		</div>
	</div>
</div>

<script type="text/javascript">
var var_ajax;
var var_gcode;

function runGcodeGet() {
	var_gcode = $('#get_gcode').val();
	var_ajax = $.ajax({
		url: "/rest/gcode",
		type: "GET",
		data: {
			v: var_gcode,
		},
		cache: false,
	})
	.done(function(html) {
		$("#gcode_detail_info").html('<pre>' + html + '</pre>');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	});
}

function runGcodePOST() {
	var_gcode = $('#post_gcode').val();
	var_ajax = $.ajax({
		url: "/rest/gcode",
		type: "POST",
		data: {
			v: var_gcode,
		},
		cache: false,
	})
	.done(function(html) {
		$("#gcode_detail_info").html('OK');
	})
	.fail(function() {
		$("#gcode_detail_info").html('ERROR');
	});
}

</script>

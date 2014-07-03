<div data-role="page" data-url="/printerstate">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<style>
				div.ui-slider-switch div.ui-slider-inneroffset a.ui-slider-handle-snapping {display: none;}
/* 				div.ui-slider-switch {width: 150px;} */
			</style>
			<div class="ui-grid-a">
				<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
					<label for="slider"><h2>{strip_led}</h2></label>
				</div></div>
				<div class="ui-block-b"><div class="ui-bar ui-bar-f" style="height:3em;">
					<select name="strip_led" id="strip_led" data-role="slider" data-track-theme="a" data-theme="a">
						<option value="off">{led_off}</option>
						<option value="on" {strip_led_on}>{led_on}</option>
					</select>
				</div></div>
				<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
					<label for="slider"><h2>{head_led}</h2></label>
				</div></div>
				<div class="ui-block-b"><div class="ui-bar ui-bar-f" style="height:3em;">
					<select name="head_led" id="head_led" data-role="slider" data-track-theme="a" data-theme="a">
						<option value="off">{led_off}</option>
						<option value="on" {head_led_on}>{led_on}</option>
					</select>
				</div></div>
			</div>
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true">
				<li><a href="/preset/listpreset">
					<h2>{set_preset}</h2></a>
				</li>
				<li><a href="/printerstate/resetnetwork">
					<h2>{reset_network}</h2></a>
				</li>
				<li><a href="/printerstate/sethostname">
					<h2>{set_hostname}</h2></a>
				</li>
				<li><a href="/printerstate/printerinfo">
					<h2>{printer_info}</h2></a>
				</li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

<script type="text/javascript">
<!--
var var_ajax;

$("#strip_led").change(function() {
	var var_state = $("#strip_led").val().toString();
// 	if (var_state == "on") {
// 		alert("on");
// 	}
// 	else {
// 		alert("off");
// 	}
	var_ajax = $.ajax({
		url: "/rest/set",
		cache: false,
		data: {
			p: "stripled",
			v: var_state,
			},
		type: "GET",
	});
// 	var_ajax.done(function(html) {
// 		alert("done");
// 	})
// 	.fail(function() {
// 		alert("failed");
// 	});
});
$("#head_led").change(function() {
	var var_state = $("#head_led").val().toString();
	var_ajax = $.ajax({
		url: "/rest/set",
		cache: false,
		data: {
			p: "headlight",
			v: var_state,
			},
		type: "GET",
	});
});
//-->
</script>

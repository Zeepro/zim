<div data-role="page" data-url="/printdetail/status">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{title}</h4>
				<script type="text/javascript" src="/assets/jwplayer/jwplayer.js"></script>
	 			<script type="text/javascript">jwplayer.key="Jh6aqwb1m2vKLCoBtS7BJxRWHnF/Qs3LMjnt13P9D6A=";</script>
	 			<style type="text/css">div#myVideo_wrapper {margin: 0 auto;}</style>
				<div id="myVideo">{loading_player}</div>
			</div>
			<div data-role="collapsible">
				<h4>{lighting}</h4>
				<div class="ui-grid-a">
					<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
						<label for="slider"><h2>{strip_led}</h2></label>
					</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-f" style="height:3em;">
						<select name="strip_led" id="strip_led" data-role="slider" data-track-theme="a" data-theme="a">
							<option value="off" id="strip_off">{led_off}</option>
							<option value="on" id="strip_on" {initial_strip}>{led_on}</option>
						</select>
					</div></div>
					<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
						<label for="slider"><h2>{head_led}</h2></label>
					</div></div>
					<div class="ui-block-b">
						<div class="ui-bar ui-bar-f" style="height:3em;">
							<select name="head_led" id="head_led" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="off" id="head_off">{led_off}</option>
								<option value="on" id="head_on" {initial_head}>{led_on}</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{print_detail}</h4>
				<div id="print_detail_info">
					<p>{wait_info}</p>
				</div>
<!-- 				<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-delete">{print_stop}</button> -->
			</div>
			<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-delete" id="print_action" onclick='javascript: stopPrint();'>{print_stop}</button>
		</div>
	</div>
</div>

<script>
function load_jwplayer_video() {
	var player = jwplayer("myVideo").setup({
							file: "{video_url}",
							width: "100%",
							autostart: true,
							fallback: false,
							androidhls: true
						});
	player.onSetupError(function()
	{
		$("#myVideo").empty().append('<img src=/images/error.png" height="280" width="280" />' +
									"<p>{video_error}</p>");
	});
}

setTimeout(load_jwplayer_video, 10000);

</script>

<script type="text/javascript">
var var_refreshPrintStatus;
var var_refreshVideoURL = 0;
var var_firstRun = true;
var var_onPlay = false;
var var_ajax;
var var_prime = {var_prime};
// var var_finish = false;
var var_ajax_lock = false;
var var_finish = false;
var var_temper_holder;
var var_temper_l = null;
var var_temper_r = null;
// var timeout_head;
// var timeout_strip;

// timeout_strip = setInterval(function()
// {
// 	if (var_ajax_lock == false)
// 	{
// 		var_ajax_lock = true;
// 		var var_state = $("#head_led").val().toString();
// 		var_ajax = $.ajax(
// 		{
// 			url: "/rest/get",
// 			cache: false,
// 			data:
// 			{
// 				p: "headlight",
// 			},
// 			type: "GET",
// 		})
// 		.done(function(led_status)
// 		{
// 			if (led_status == "on")
// 			{
// 				$("#strip_led option:nth-child(2)").attr("selected", "selected");
// 				$("#strip_led").refresh();
// 			}
// 			clearInterval(timeout_strip);
// 		})
// 		.always(function()
// 		{
// 			console.log("realease");
// 			var_ajax_lock = false;
// 		});
// 	}
// }, 1000);

// function get_head_led()
// {
// 	if (var_ajax_lock == false)
// 	{
// 		var_ajax_lock = true;
// 		var var_state = $("#head_led").val().toString();
// 		var_ajax = $.ajax(
// 		{
// 			url: "/rest/get",
// 			cache: false,
// 			data:
// 			{
// 				p: "headlight",
// 			},
// 			type: "GET",
// 		})
// 		.done(function(msg)
// 		{
// 			$("#head_led option:nth-child(2)").attr("selected", "selected");
// 			$("#head_led").refresh();
// 			console.log('head_done : ' + timeout_head);
// 			clearInterval(timeout_head);
// 		})
// 		.always(function()
// 		{
// 			var_ajax_lock = false;
// 		});
// 	}
// 	else
// 	{
// 		console.log("locked");
// 	}
// }

// timeout_head = setInterval(get_head_led, 1000);

$(document).ready(checkPrintStatus());

function again()
{
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
	window.location.href="{restart_url}";
};

$("#head_led").change(function()
{
	var timeout;

	timeout = setInterval(function()
	{
		if (var_ajax_lock == false)
		{
			var_ajax_lock = true;
			var var_state = $("#head_led").val().toString();
			var_ajax = $.ajax(
			{
				url: "/rest/set",
				cache: false,
				data:
				{
					p: "headlight",
					v: var_state,
				},
				type: "GET",
			})
			.done(function()
			{
				clearInterval(timeout);
			})
			.always(function()
			{
				var_ajax_lock = false;
			});
		}
	}, 1000);
});

$("#strip_led").change(function()
{
	var interval;

	interval = setInterval(function()
	{
		if (var_ajax_lock == false)
		{
			var_ajax_lock = true;
			var var_state = $("#strip_led").val().toString();
			var_ajax = $.ajax(
			{
				url: "/rest/set",
				cache: false,
				data:
				{
					p: "stripled",
					v: var_state,
				},
				type: "GET",
			})
			.done(function()
			{
				clearInterval(interval);
			})
			.always(function()
			{
				var_ajax_lock = false;
			});
		}
	}, 1000);
});
	
function checkPrintStatus() {
	refreshPrintStatus();
	var_refreshPrintStatus = setInterval(refreshPrintStatus, 5000);
	function refreshPrintStatus() {
		if (var_ajax_lock == true) {
			return;
		}
		else {
			var_ajax_lock = true;
			//refreshVideoURL();
		}
		
		var_ajax = $.ajax({
			url: "/printdetail/status_ajax",
			cache: false,
		})
		.done(function(html) {
			if (var_ajax.status == 202) { // finished printing
				finishAction();
			}
			else if (var_ajax.status == 200) { // in printing
				$("#print_detail_info").html(html);
			}
		})
		.fail(function() { // not in printing
			window.location.replace("/");
// 			finishAction();
// 			finishLoop();
// 			alert("no connection");
<!--	//	<?php //FIXME just disable redirection and do same as finished for simulation ?> -->
		})
		.always(function() {
			var_ajax_lock = false;
		});
	}

// 	jwplayer("myVideo").onPlay(function() {
// 		var_onPlay = true;
// 	})

	return;
}

function refreshVideoURL() {
// 	if (var_refreshVideoURL == 0) {
// 		var_refreshVideoURL = setInterval(refreshVideo, 1000 * 5);
// 	}
// 	else if (var_firstRun == true) {
// 		clearInterval(var_refreshVideoURL);
// 		var_refreshVideoURL = setInterval(refreshVideo, 1000 * 30 * 4);
// 		var_firstRun = false;
// 	}
	if (var_onPlay == false) {
		var_refreshVideoURL = setInterval(refreshVideo, 1000 * 5);
	}
	else if (var_firstRun == true) {
		clearInterval(var_refreshVideoURL);
		var_refreshVideoURL = setInterval(refreshVideo, 1000 * 30 * 4);
		var_firstRun = false;
	}
	
	function refreshVideo() {
		jwplayer('myVideo').load({file:'{video_url}'});
	}

	return;
}

function finishLoop() {
	clearInterval(var_refreshPrintStatus);
	clearInterval(var_refreshVideoURL);

	return;
}

function stopPrint()
{
	var cancel = confirm("{cancel_confirm}");

	if (cancel == true)
	{
		if (var_finish == true)
			return;
		finishLoop();
		setTimeout(redirect_cancel, 1000);

		function redirect_cancel()
		{
 			//window.location.href="/printdetail/cancel?_=" + Math.round(+new Date / 1e3);
			window.location.href="/printdetail/cancel";
			return;
		}
	}
	return;
}

function finishAction() {
	finishLoop();
	// display info
	$("#print_detail_info").html('<p>{finish_info}</p>');
	// change return button
	$('button#print_action').attr('onclick','').unbind('click');
	$('button#print_action').click(function(){window.location.href='{return_url}'; return false;});
	$('button#print_action').parent().find('span.ui-btn-text').text('{return_button}');
	$('button#print_action').html('{return_button}');
	var_finish = true;

	// add restart button for print again
	$('<div>').appendTo('#container')
	.attr({'id': 'again_button', 'onclick' : 'again()', 'data-icon' : 'refresh'}).html('{again_button}')
	.button().button('refresh');

	return;
}
</script>


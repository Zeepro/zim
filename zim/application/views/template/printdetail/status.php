<div data-role="page" data-url="/printdetail/status">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{title}</h4>
<!--				<video width="320" height="240" autoplay controls>
					<source src="http://88.175.62.75/zim.m3u8" type="application/x-mpegURL" />
					Your browser does not support HTML5 streaming!
				</video> -->
				<script type="text/javascript" src="/assets/jwplayer/jwplayer.js"></script>
	 			<script type="text/javascript">jwplayer.key="Jh6aqwb1m2vKLCoBtS7BJxRWHnF/Qs3LMjnt13P9D6A=";</script>
	 			<style type="text/css">div#myVideo_wrapper {margin: 0 auto;}</style>
				<div id="myVideo">Loading the player...</div>
				<script type="text/javascript">
					jwplayer("myVideo").setup({
						file: "{video_url}",
						autostart: true,
					});
				</script>
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

<script type="text/javascript">
var var_refreshPrintStatus;
var var_refreshVideoURL = 0;
var var_firstRun = true;
var var_onPlay = false;
var var_ajax;
var var_prime = {var_prime};
// var var_finish = false;
var var_ajax_lock = false;

$(document).ready(checkPrintStatus());

function checkPrintStatus() {
	refreshPrintStatus();
	var_refreshPrintStatus = setInterval(refreshPrintStatus, 5000);
	function refreshPrintStatus() {
		if (var_ajax_lock == true) {
			return;
		}
		else {
			var_ajax_lock = true;
			refreshVideoURL();
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

	jwplayer("myVideo").onPlay(function() {
		var_onPlay = true;
	})

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

function stopPrint() {
	finishLoop();
	setTimeout(redirect_cancel, 1000);

	function redirect_cancel() {
// 		window.location.href="/printdetail/cancel?_=" + Math.round(+new Date / 1e3);
		window.location.href="/printdetail/cancel";

		return;
	}

	return;
}

function finishAction() {
	finishLoop();
	// display info
	$("#print_detail_info").html('<p>{finish_info}</p>');
	// change return button
	$('button#print_action').click(function(){window.location.href='{return_url}'; return false;});
	$('button#print_action').parent().find('span.ui-btn-text').text('{return_button}');
	$('button#print_action').html('{return_button}');

	// add restart button for print again
	$('<div>').appendTo('#container')
	.attr({'id': 'again_button', 'onclick': 'javascript: window.location.href="{restart_url}";', 'data-icon' : 'refresh'}).html('{again_button}')
	.button().button('refresh');

	return;
}
</script>


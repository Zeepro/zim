<div data-role="page" data-url="/printdetail/cancel">
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
				<div id="cancel_detail_info">
					<p>{wait_info}</p>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var var_refreshCancelStatus;
var var_refreshVideoURL;
var var_ajax;
$(document).ready(checkCancelStatus());

function checkCancelStatus() {
 	var_refreshCancelStatus = setInterval(refreshCancelStatus, 1000);
	refreshVideoURL();
	function refreshCancelStatus() {
		var_ajax = $.ajax({
			url: "/printdetail/cancel_ajax",
			cache: false,
		})
		.done(function(html) {
			if (var_ajax.status == 202) { // finished printing
				finishAction();
			}
			else if (var_ajax.status == 200) { // in printing
				$("#cancel_detail_info").html(html);
			}
		})
		.fail(function() { // not in printing
// 			window.location.replace("/");
			finishAction();
<!--	//	<?php //FIXME just disable redirection and do same as finished for simulation ?> -->
		});
	}

	return;
}

function refreshVideoURL() {
	var_refreshVideoURL = setInterval(refreshVideo, 1000 * 30 * 4);
	function refreshVideo() {
		jwplayer('myVideo').load({file:'{video_url}'});
	}

	return;
}

function finishAction() {
	clearInterval(var_refreshCancelStatus);
	// display info
	$("#cancel_detail_info").html('<p>{finish_info}</p>');
	// add return button for Home
	$('<button>').appendTo('#container')
	.attr({'id': 'print_action', 'onclick': 'javascript: window.location.href="{return_url}";'}).html('{return_button}')
	.button().button('refresh');

	return;
}
</script>


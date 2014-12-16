<div data-role="page" data-url="/printdetail/timelapse">
	<style>
		.ui-icon-myicon:after
		{
			background-image: url("/images/youtube_icon.png");
		}
	</style>
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="#home_popup" data-rel="popup" class="ui-btn ui-icon-home ui-btn-icon-left ui-corner-all ui-shadow" data-transition="pop">{home_button}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			<p>{finish_info}</p>
			<div id="home_popup" data-role="popup" data-dismissible="false" class="ui-content" style="max-width:250px;">
				{home_popup_text}
		        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back" data-transition="flow" onclick="javascript: finish_timelapse();">{yes}</a>
		        <a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back">{no}</a>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{timelapse_title}</h4>
				<script type="text/javascript" src="/assets/jwplayer/jwplayer.js"></script>
	 			<script type="text/javascript">jwplayer.key="Jh6aqwb1m2vKLCoBtS7BJxRWHnF/Qs3LMjnt13P9D6A=";</script>
	 			<style type="text/css">div#myVideo_wrapper {margin: 0 auto;}</style>
				<div id="myVideo">{loading_player}</div>
<!-- 				<a href="#" id="timelapse_button" data-ajax="false" data-role="button" class="ui-link ui-btn ui-shadow ui-corner-all">{timelapse_button}</a> -->
				<a id="send_email_button" href="#timelapse_right_panel" data-role="button" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-mail" style="display:none;">{send_email_button}</a>
				<a id="send_yt_button" href="/printdetail/connect_google" data-ajax="false" data-role="button" class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-myicon" style="display:none;">{send_yt_button}</a>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>{timelapse_info_title}</h4>
				<style type="text/css"> .ui-table-columntoggle-btn { display: none !important; } </style>
				<table data-role="table" data-mode="columntoggle" id="test-table" class="ui-shadow table-stroke" style="background-color:#e7e7e7">
					<tbody>
						{timelapse_info}
						<tr>
							<th>{title}</th>
							<td>{value}</td>
						</tr>
						{/timelapse_info}
					</tbody>
				</table>
			</div>
			<a href="#home_popup" data-rel="popup" data-role="button" class="ui-btn ui-icon-home ui-btn-icon-left ui-corner-all ui-shadow" data-transition="pop">{home_button}</a>
			<!--<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-home" id="print_action" onclick='javascript: finish_timelapse();'>{home_button}</button>-->
			<button class="ui-btn ui-shadow ui-corner-all ui-btn-icon-left ui-icon-refresh" id="print_action" onclick='javascript: restart_print();'>{again_button}</button>
		</div>
	</div>
	<div data-role="panel" id="timelapse_right_panel" data-display="overlay" data-position="right">
		<h3>{send_email_hint}</h3>
		<input type="text" name="name" id="email_timelapse" value="" data-clear-btn="true">
		<p style="">{send_email_multi}</p>
		<span class="zim-error" id="email_timelapse_wrong" style="display: none;">{send_email_wrong}</span>
		<span class="zim-error" id="sending_timelapse_error" style="display: none;">{send_email_error}</span>
		<button class="ui-btn ui-shadow ui-corner-all" id="email_timelapse_submit" onclick='javascript: send_email();'>{send_email_action}</button>
    </div>
</div>

<script>

var timelapse = "timelapse";

$("div#link_logo").on('click', function()
{
	$("div#home_popup").popup("open");
});

var var_interval_video_check;
var var_ajax_video_check;
var var_ajax_timelapse_end;
var var_ajax_send_email;

function load_jwplayer_video() {
	var player = jwplayer("myVideo").setup({
			file: "{video_url}",
			width: "100%",
			autostart: true,
			fallback: false,
			androidhls: true
	});
	player.onSetupError(function() {
		$("#myVideo").empty().append('<img src="/images/error.png" height="280" width="280" />' +
				"<p>{video_error}</p>");
	});
}

function finish_timelapse() {
	var_ajax_timelapse_end = $.ajax({
		url: "/printdetail/timelapse_end_ajax",
		cache: false,
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
// 		window.location.replace="/";
		window.location.href="/";
	})
	.fail(function() {
		alert('end timelapse failed');
	});
}

// function restart_print() {
// 	var_ajax_timelapse_end = $.ajax({
// 		url: "/printdetail/timelapse_end_ajax",
// 		cache: false,
// 		beforeSend: function() {
// 			$("#overlay").addClass("gray-overlay");
// 			$(".ui-loader").css("display", "block");
// 		},
// // 		complete: function() {
// // 			$("#overlay").removeClass("gray-overlay");
// // 			$(".ui-loader").css("display", "none");
// // 		},
// 	})
// 	.done(function(html) {
// 		window.location.replace="{restart_url}";
// 	})
// 	.fail(function() {
// 		alert('end timelapse failed');
// 	});
// }

function restart_print() {
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
	window.location.replace("{restart_url}");
}

function send_email() {
// 	var email_reg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	
// 	if(!email_reg.test($.trim($("#email_timelapse").val()))) {
// 		$("#email_timelapse_error").css('display','block');
// 		return;
// 	}
	
	var_ajax_send_email = $.ajax({
		url: "/printdetail/sendemail_ajax",
		cache: false,
		type: "POST",
		data: {
				email: $('#email_timelapse').val(),
				model: "{send_email_modelname}",
		},
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(html) {
		$("#timelapse_right_panel").panel("close");
	})
	.fail(function() {
		switch(var_ajax_send_email.status) {
			case 432:
			case 433:
				$("#email_timelapse_error").css('display','block');
				break;
				
			default:
				$("#sending_timelapse_error").css('display','block');
				break;
		}
	});
}

// start of script
var_interval_video_check = setInterval(function() {
	var_ajax_video_check = $.ajax({
		url: "/printdetail/timelapse_ready_ajax",
		cache: false,
	})
	.done(function(html) {
		if (var_ajax_video_check.status == 202) {
			$('a#timelapse_button').attr('href', "{video_url}");
			load_jwplayer_video();
			$('a#send_email_button').show();
			$('a#send_yt_button').show();
			clearInterval(var_interval_video_check);
			var_interval_video_check = 0;
		}
		// do nothing when status code is 200
	})
	.fail(function() {
		console.log('check video failed');
	});
}, 3000);

// reset previously set border colors and hide all message on .keyup()
$("#email_timelapse").keyup(function() { 
	$("#email_timelapse_error").css('display','none');
	$("#sending_timelapse_error").css('display','none');
});

$('a#send_email_button').click(function(event) {
	if (var_interval_video_check != 0) {
		event.preventDefault();
		event.stopPropagation();
	}
});

// // open new tab / window for video
// $('a#timelapse_button').click(function(event) {
// 	event.preventDefault();
// 	event.stopPropagation();
// 	window.open(this.href, '_blank');
// });

// $.ajax({
// 	type:"POST",
// 	dataType: "json",
// 	contentType: "application/json",
// 	url:"https://mandrillapp.com/api/1.0/messages/send.json",
// 	data:JSON.stringify({"key":"2Zgc9PkAhAoJH8oNBt2q8A","message":{"html":"Example HTML content","subject":"Your zim-motion ajax","from_email":"zim-motion@zeepro.com","from_name":"Zim","to":[{"email":"peng.ni@zeepro.fr"}],"important":false,"track_opens":null,"track_clicks":null,"auto_text":null,"auto_html":null,"inline_css":null,"url_strip_qs":null,"preserve_recipients":null,"view_content_link":null,"tracking_domain":null,"signing_domain":null,"return_path_domain":null,"merge":false},"async":false,"ip_pool":"Main Pool"}),
// }).done(function(response) {
// 	console.log(response);
// });

</script>

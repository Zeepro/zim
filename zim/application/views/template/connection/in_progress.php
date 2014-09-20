<div data-role="page" data-url="/connection/in_progress">
	<header data-role="header" class="page-header"></header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align:center;">
			<p id="hint_box">{config_printer}</p>
		</div>
	</div>

<script>
$(document).ready(function()
{
	$(".ui-loader").css("display", "block");
});

var ua = navigator.userAgent;
var isAndroid = ua.indexOf("android") > -1;
var version = "N/A";

var suffix = '.local';

if (isAndroid)
{
	var match = ua.match(/Android\s([0-9\.]*)/);
	if (match[1][0] < '4' || (match[1][0] == '4' && match[1][2] < '3'))
		suffix = '';
}

if (ua.search('Windows') != -1)
{
	suffix = '';
}


setTimeout(function()
{
	var interval;
	var counter = 0;
	
	interval = setInterval(function()
	{
		var image = new Image();
		
		if (counter >= 90) {
			clearInterval(interval);
			$("p#hint_box").html('{connect_error_msg}');
			$(".ui-loader").css("display", "none");
		}
		else {
			counter += 1;
		}
		
		image.src = "http://{hostname}"+ suffix +"/assets/images/pixel.png?_=" + (new Date()).getTime();
		setTimeout(function()
		{
			if (image.height != 0)
			{
				clearInterval(interval);
				window.location.href="http://{hostname}.local";
			}
		}, 1000);
	}, 2000);
}, 30000);



</script>

</div>
<div data-role="page" data-url="/connection/in_progress">
	<header data-role="header" class="page-header"></header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align:center;">
			<p id="hint_box">{config_printer}</p>
			<div id="error_box" style="display:none;">
				{connect_error_msg}
			</div>
		</div>
	</div>

<script>

$(document).ready(function()
{
	$(".ui-loader").css("display", "block");
});

/*
// Start magicDatabase=zsso;User ID=zssologin;Password=V8lu7hb1
*/

setTimeout(function()
{
	var interval;
	var counter = 0;
	var localip = "";
	
	interval = setInterval(function()
	{
		var image = new Image();
		var image2 = new Image();
		var image3 = new Image();

		if (localip.length == 0)
		{
			$.ajax({
			cache: false,
			type: "POST",
			url: "https://sso.zeepro.com/getlocalip.ashx",
			data: { "printersn": "{printersn}"},
			dataType: "json",
			success: function (data) {
				if (data.state == "ok")
				{
					localip = data.localIP;
				}
			},
//			error:function (xhr, ajaxOptions, thrownError){
//			}
		});
		}

		if (counter >= 90)
		{
			clearInterval(interval);
			$("p#hint_box").css('display', 'none');
			$("div#error_box").css('display', 'block');
			$(".ui-loader").css("display", "none");
		}
		else
		{
			counter += 1;
		}
		
		if (localip.length > 0)
		{
			image3.src = "http://" + localip + "/assets/images/pixel.png?_=" + (new Date()).getTime();
		}
		image.src = "http://{hostname}/assets/images/pixel.png?_=" + (new Date()).getTime();
		image2.src = "http://{hostname}.local/assets/images/pixel.png?_=" + (new Date()).getTime();
		setTimeout(function()
		{
			if (image3.height != 0)
			{
				clearInterval(interval);
				window.location.href = "http://" + localip + "/account/first_signup/";
			}
			if (image.height != 0)
			{
				clearInterval(interval);
				window.location.href = "http://{hostname}/account/first_signup/";
			}
			if (image2.height != 0)
			{
				clearInterval(interval);
				window.location.href = "http://{hostname}.local/account/first_signup/";
			}
		}, 1000);
	}, 2000);
}, 30000);
</script>

</div>
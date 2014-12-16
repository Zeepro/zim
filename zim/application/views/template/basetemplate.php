<!DOCTYPE html>
<html lang="{lang}">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
		<meta http-equiv="X-UA-Compatible" content="IE=9" />
		<meta charset="utf-8" />
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />
		{headers}
		<link rel="stylesheet" href="/styles/jquery.mobile-1.4.0.min.css" />
		<link rel="stylesheet" href="/styles/Custom-zim.min.css" />
		<script src="/scripts/jquery-1.9.1.min.js"></script>
		<script>
			$(document).bind("mobileinit", function() {
				$.mobile.defaultPageTransition = 'slide';
			});
		</script>
		<script src="/scripts/jquery.mobile-1.4.0.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, target-densitydpi=medium-dpi, user-scalable=0" />
		<!-- <link rel="stylesheet" href="/assets/css/4.css"> -->
		<!-- <link rel="stylesheet" href="/assets/css/style.css"> -->
		<!-- <link rel="stylesheet" href="/styles/flag.css" /> -->
		<style type="text/css">
			div.logo {
				width:100%; 
				height:110px;
				background:url("/assets/images/logo-white.png") top center no-repeat;
				background-size: 212px 59px;
			}
			div#link_logo {
				margin: 0 auto;
				width: 248px;
				height: 72px;
				cursor: pointer;
			}
			.ui-page-theme-a{
				color:#575749;
				background:url("/assets/images/page-backgrounds/back-4.jpg") 0 -100px repeat-x  #f9f7f3;
				background-size: 79px 245px; 
			}
			.page-header { 
				background:url("/assets/images/headerBack.png") bottom repeat-x !important;
				border:0px;!important
				color:#333;
				height:38px;
				text-shadow: 0 -1px 1px #fff;
			}
			.shadow {
				width: 100%;
				position: relative;
				top: -17px;
				z-index: 5;
			}
			.zim-error
			{
				color:red;
				font-weight: bold;
			}
			.ui-grid-a .ui-block-b .ui-slider-switch,
			.switch-larger .ui-slider-switch {
				width: 12em;
				max-width: 100%;
			}
			.ui-grid-a .ui-block-a .ui-slider-switch,
			.switch-larger .ui-slider-switch {
				width: 12em;
				max-width: 100%;
			}
		</style>
		<script type="text/javascript">
			$(document).on("pageinit", function()
			{
				if (typeof timelapse == 'undefined')
					$('div#link_logo').click(function(){window.location.href='/'; return false;});
			});
		</script>
	</head>
	<body>
		<noscript>
			<p style="text-align: center">Your navigator has disabled Javascript support, please enable it to use zim.</p>
		</noscript>
		<div id="page_body" style="display:none">
			{contents}
		</div>
	</body>
	<script>
		$("#page_body").css("display", "block");
	</script>
</html>
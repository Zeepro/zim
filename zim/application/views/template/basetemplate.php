<!DOCTYPE html>
<html lang="{lang}">
<head>
<meta charset="utf-8">
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
		background:url("/assets/images/logo-1.png") top center no-repeat;
		background-size: 119px 96px;
	}
	div#link_logo {
		margin: 0 auto;
		width: 97px;
		height: 100px;
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
</style>
<script type="text/javascript">
	$(document).on("pageinit", function() {
		$('div#link_logo').click(function(){window.location.href='/'; return false;});
	});
</script>

</head>
<body>
{contents}
</body>
</html>
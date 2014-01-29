<!DOCTYPE html>
<html lang="{lang}">
<head>
<meta charset="utf-8">
	{headers}
	<link rel="stylesheet" href="/styles/jquery.mobile-1.3.0.min.css" />
	<script src="/scripts/jquery-1.9.1.min.js"></script>
	<script>
		$(document).bind("mobileinit", function() {
			$.mobile.defaultPageTransition = 'slide';
		});
	</script>
<script src="/scripts/jquery.mobile-1.3.0.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, target-densitydpi=medium-dpi, user-scalable=0" />

<link rel="stylesheet" href="/assets/css/4.css">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/styles/flag.css" />
<style type="text/css">
	div#link_logo {
		margin: 0 auto;
		width: 97px;
		height: 100px;
		cursor: pointer;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('#link_logo').click(function(){window.location.href='/'; return false;});
	});
</script>

</head>
<body>
{contents}
</body>
</html>
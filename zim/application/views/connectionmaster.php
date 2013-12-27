<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="utf-8">
    <?= $header ?>
    <link rel="stylesheet" href="/styles/jquery.mobile-1.3.0.min.css" />
	<script src="/scripts/jquery-1.9.1.min.js"></script>
	<script>
		$(document).bind("mobileinit", function() {
			$.mobile.defaultPageTransition = 'pop';
		});
	</script>
<script src="/scripts/jquery.mobile-1.3.0.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, target-densitydpi=medium-dpi, user-scalable=0" />

<link rel="stylesheet" href="/assets/css/4.css">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="stylesheet" href="/styles/flag.css" />

</head>
<body>
	<div data-role="page">
		<header data-role ="header" class="page-header"> 
			<a data-icon="arrow-l" data-role="button" data-direction="reverse" data-rel="back">Back</a>
<!-- 		    <div class="ui-btn-right" data-role="controlgroup" data-type="horizontal"> -->
<!-- 				<a href="javascript:$.mobile.changePage('/en' + $.mobile.path.parseUrl(window.location.href).pathname)" data-role="button" data-iconpos="notext" data-icon="en-flag">en</a> -->
<!-- 				<a href="javascript:$.mobile.changePage('/fr' + $.mobile.path.parseUrl(window.location.href).pathname)" data-role="button" data-iconpos="notext" data-icon="fr-flag">fr</a> -->
<!-- 		    </div> -->
		</header>
		<div class="logo"></div>
		<div data-role="content">
             <?= $contents?>
        </div>
	</div>
</body>
</html>

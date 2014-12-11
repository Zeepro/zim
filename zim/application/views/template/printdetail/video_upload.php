<div data-role="page" data-url="/printdetail/video_upload">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			<p>The video is uploading...</p>
			<div class="ui-loader ui-corner-all ui-body-a ui-loader-default" style="display:block">
				<span class="ui-icon-loading"></span>
			</div>
		</div>
	</div>
	<script>
		$.get('/printdetail/connect_google/true?state={state}&code={code}')
		.done(function( data )
		{
			window.location.href = "/printdetails/timelapse";
		});
	</script>
</div>	

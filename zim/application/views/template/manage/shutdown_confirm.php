<div data-role="page">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<div style="text-align:center">
				<p>{confirm_message}</p>
				<div class="ui-grid-a" style="width:50%; margin: 0 auto">
					<div class="ui-block-a">
						<a id="yes_btn" href="#" data-role="button">{yes}</a>
					</div>
					<div class="ui-block-b">
						<a href="/manage" data-role="button">{no}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$("a#yes_btn").on("click", function()
		{
			$.ajax(
			{
				url: '/manage/shutdown_ajax'
			})
			.complete(function()
			{
				alert('{shutdown_confirm}');
			});
		});
	</script>
</div>
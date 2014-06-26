<div data-role="page" data-url="/setupcartridge/write">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>Write tag 写入标签</h4>
				<img src="{image}"><br>
				{hint}
				<form method="post" action="/setupcartridge/wait" data-ajax="false">
					<input type="hidden" name="type" value="{type}">
					<input type="hidden" name="year" value="{year}">
					<input type="hidden" name="month" value="{month}">
					<input type="hidden" name="day" value="{day}">
					<input type="hidden" name="times" value="{times}">
					<input type="submit" value="ok 确认">
				</form>
			</div>
		</div>
	</div>
</div>


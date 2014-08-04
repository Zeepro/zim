<div data-role="page" data-url="/printerstate/sethostname">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home_button}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2 style="text-align: center;">{hint}</h2>
			<form method="post" accept-charset="utf-8">
				<input type="text" name="hostname" id="hostname" value="" data-clear-btn="true"/>
				<div>
					<input type="submit" value="{set_button}" />
				</div>
				{error}
			</form>
		</div>
	</div>
</div>

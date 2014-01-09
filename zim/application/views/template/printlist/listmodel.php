<div data-role="page" data-url="/printmodel/listmodel">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"></div>
	<div data-role="content">
		<div id="container">
			<h2>{title}</h2>
			<ul data-role="listview" id="listview"
				class="shadowBox">
				{model_lists}
				<li><a href="{baseurl_detail}?id={id}">
					<img src="{image}">
					<h2 style="margin-left:100px;">{name}</h2></a>
				</li>
				{/model_lists}
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

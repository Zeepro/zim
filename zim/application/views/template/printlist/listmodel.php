<div data-role="page" data-url="/printmodel/listmodel">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
<!-- 			<h2>{title}</h2> -->
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true" data-filter="true" data-filter-placeholder="{search_hint}" data-filter-theme="d">
				{model_lists}
				<li><a href="{baseurl_detail}?id={id}" data-ajax=false>
					<img src="{image}">
					<h2>{name}</h2></a>
				</li>
				{/model_lists}
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

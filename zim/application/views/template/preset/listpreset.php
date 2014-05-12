<div data-role="page" data-url="/preset/listpreset">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
<!-- 			<h2>{title}</h2> -->
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true" data-filter="true" data-filter-placeholder="{search_hint}" data-filter-theme="d">
				{model_lists}
				<li>
					<a href="{baseurl_detail}?id={id}"><h2>{name}</h2></a>
				</li>
				{/model_lists}
			</ul>
			<div style="height:50px;"></div>
			<form action="/preset/detail" method="get">
				<div data-role="fieldcontain">
<!-- 					<legend>Vertical controlgroup:</legend> -->
					<label for="new_preset_select">{new_preset_label}</label>
					<select name="id" id="new_preset_select">
						{newmodel_lists}
						<option value="{id}">{name}</option>
						{/newmodel_lists}
					</select>
				</div>
				<input type="hidden" name="new" id="new_preset_hidden">
				<div id="submit_container"><input type="submit" value="{submit_button}" data-ajax="false"></div>
			</form>
		</div>
	</div>
</div>

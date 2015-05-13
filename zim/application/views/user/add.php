<div data-role="page" data-url="/user/add">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<form method="POST">
			<h2 style="text-align: center;">{title_add_form}</h2>
			<div class="ui-grid-a">
				<div class="ui-block-a"><div class="ui-bar ui-bar-f">
					<label for="user_name"><h2>{title_name}</h2></label>
				</div></div>
				<div class="ui-block-b"><div class="ui-bar ui-bar-f">
					<input type="text" name="user_name" required />
				</div></div>
				<div class="ui-block-a"><div class="ui-bar ui-bar-f">
					<label for="user_email"><h2>{title_email}</h2></label>
				</div></div>
				<div class="ui-block-b"><div class="ui-bar ui-bar-f">
					<input type="email" name="user_email" required />
				</div></div>
			</div>
			<div data-role="collapsible" data-collapsed="false">
				<h4>{title_access}</h4>
				<a href="#user_access_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">?</a>
				<div id="user_access_popup" data-role="popup" class="ui-content">
					<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
					{hint_access}
				</div>
				<fieldset data-role="controlgroup">
					<input type="radio" name="user_access" id="user_access_v" value="1" checked>
					<label for="user_access_v">{title_p_view}</label>
					<input type="radio" name="user_access" id="user_access_m" value="2">
					<label for="user_access_m">{title_p_manage}</label>
					<input type="radio" name="user_access" id="user_access_a" value="3">
					<label for="user_access_a">{title_p_account}</label>
				</fieldset>
			</div>
			<input type="submit" value="{button_confirm}" />
			</form>
			<div class="zim-error">{error}</div>
		</div>
	</div>
</div>
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
				<div class="ui-grid-a">
					<div class="ui-block-a"><div class="ui-bar ui-bar-f">
						<label for="access_view">{title_p_view}</label>
					</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-f">
						<select name="access_view" data-role="slider" data-track-theme="a" data-theme="a">
							<option value="0">{function_off}</option>
							<option value="1" selected>{function_on}</option>
						</select>
					</div></div>
					<div class="ui-block-a"><div class="ui-bar ui-bar-f">
						<label for="access_manage">{title_p_manage}</label>
					</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-f">
						<select name="access_manage" data-role="slider" data-track-theme="a" data-theme="a">
							<option value="0">{function_off}</option>
							<option value="1">{function_on}</option>
						</select>
					</div></div>
					<div class="ui-block-a"><div class="ui-bar ui-bar-f">
						<label for="access_account">{title_p_account}</label>
					</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-f">
						<select name="access_account" data-role="slider" data-track-theme="a" data-theme="a">
							<option value="0">{function_off}</option>
							<option value="1">{function_on}</option>
						</select>
					</div></div>
				</div>
			</div>
			<input type="submit" value="{button_confirm}" />
			</form>
			<div class="zim-error">{error}</div>
		</div>
	</div>
</div>
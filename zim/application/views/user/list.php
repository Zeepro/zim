<div data-role="page" data-url="/user/userlist" style="overflow:hidden;">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible-set" id="list_printeruser">
			{userlist}
				<div data-role="collapsible" id="printeruser_{user_name}_{random_id}">
					<h3>{user_name}</h3>
					<a href="#delete_popup" data-role="button" data-rel="popup" onclick='javascript: pre_deleteUser("printeruser_{user_name}_{random_id}");'>{button_delete}</a>
					<form method="POST" action="/user/add" data-ajax="false">
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="user_name"><h2>{title_name}</h2></label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" name="user_name" value="{user_name}" required />
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="user_email"><h2>{title_email}</h2></label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="email" name="user_email" value="{user_email}" required />
						</div></div>
					</div>
					<div data-role="collapsible" data-collapsed="false">
						<h4>{title_access}</h4>
						<a href="#user_access_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">{popup_what}</a>
						<fieldset data-role="controlgroup">
							<input type="radio" name="user_access" id="user_access_{user_name}_{random_id}_v" value="1" {user_p_view}>
							<label for="user_access_{user_name}_{random_id}_v">{title_p_view}</label>
							<input type="radio" name="user_access" id="user_access_{user_name}_{random_id}_m" value="2" {user_p_manage}>
							<label for="user_access_{user_name}_{random_id}_m">{title_p_manage}</label>
							<input type="radio" name="user_access" id="user_access_{user_name}_{random_id}_a" value="3" {user_p_account}>
							<label for="user_access_{user_name}_{random_id}_a">{title_p_account}</label>
						</fieldset>
					</div>
					<input type="hidden" name="user_ori_email" value="{user_email}" />
					<input type="submit" name="edit_user" value="{button_confirm}" />
					</form>
				</div>
			{/userlist}
			</div>
			<div id="user_access_popup" data-role="popup" class="ui-content">
				<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
				{hint_access}
			</div>
			<div class="zim-error">{error_get_list}</div>
		</div>
		<div id="delete_popup" data-role="popup" data-dismissible="false" class="ui-content" style="max-width: 250px; text-align: center;">
			{message_delete}
			<br /><br />
			<div class="ui-grid-a">
				<div class="ui-block-a">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back" data-transition="flow" onclick="javascript: do_deleteUser();">{button_delete_ok}</a>
				</div>
				<div class="ui-block-b">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back">{button_delete_no}</a>
				</div>
			</div>
		</div>
	</div>

<script>
var var_id_toDelete = null;

function pre_deleteUser(div_id) {
	if (typeof(div_id) != "undefined" && $("div#" + div_id)) {
		var_id_toDelete = div_id;
	}
	
	return;
}

function do_deleteUser() {
	if (var_id_toDelete != null) {
		var var_user_email = $("div#" + var_id_toDelete + " input[type=hidden][name=user_ori_email]").val();
		
		$.ajax({
				cache: false,
				type: "POST",
				url: "/user/delete_ajax",
				data: { "user_email": var_user_email },
				beforeSend: function() {
					$("#overlay").addClass("gray-overlay");
					$(".ui-loader").css("display", "block");
				},
				complete: function() {
					$(".ui-loader").css("display", "none");
					$("#overlay").removeClass("gray-overlay");
				},
				success: function() {
					$("div#" + var_id_toDelete).remove();
					$("div#list_printeruser").collapsibleset("refresh");
				},
				error: function (data, textStatus, xhr) {
					alert("{msg_delete_error}");
					console.log(xhr);
				},
		});
	}
	
	return;
}
</script>
</div>
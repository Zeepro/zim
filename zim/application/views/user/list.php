<div data-role="page" data-url="/user/userlist" style="overflow-y: hidden;">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<a href="/user/add" data-role="button" data-ajax="false">{button_add_user}</a>
			<div data-role="collapsible-set" id="list_printeruser">
			{userlist}
				<div data-role="collapsible" id="printeruser_{user_name}_{random_id}">
					<h3>{user_name}</h3>
					<a href="#delete_popup" data-role="button" data-rel="popup" onclick='javascript: pre_deleteUser("printeruser_{user_name}_{random_id}");'>{button_delete}</a>
					<form method="POST" class="user_manage_edit" action="/user/add" data-ajax="false" data-fillmsg="false">
					<div class="ui-field-contain">
						<label for="user_name">{title_name}</label>
						<input type="text" name="user_name" value="{user_name}" pattern="[A-Za-z0-9_-]*" required />
						<input type="hidden" name="user_oldname" value="{user_name}" required />
					</div>
					<div class="ui-field-contain">
						<label for="user_email">{title_email}</label>
						<input type="email" name="user_email_display" value="{user_email}" disabled />
						<input type="hidden" name="user_email" value="{user_email}" required />
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
<!-- 					<div data-role="collapsible" style="margin-top: 8px;"> -->
<!-- 						<h4>{title_message}</h4> -->
<!-- 						<textarea cols="40" rows="8" name="user_message" placeholder="{hint_message}" maxlength="2048"></textarea> -->
<!-- 					</div> -->
					<input type="hidden" name="user_ori_email" value="{user_email}" />
					<input type="hidden" name="user_message" />
					<input type="hidden" name="edit_user" value="edit" />
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
		<div id="list_exist_popup" data-role="popup" class="ui-content" style="max-width: 250px; text-align: center;">
			<p class="zim-error">{msg_add_exist}</p>
			<a href="#" class="ui-btn ui-corner-all ui-shadow" data-rel="back">{button_ok}</a>
		</div>
		<div id="user_edit_message_popup" data-role="popup" class="ui-content">
			<textarea cols="40" rows="8" name="user_message" id="user_edit_message_popup_text" placeholder="{hint_message}" maxlength="2048"></textarea>
			<a href="#" data-role="button" onclick="javascript: startSubmitUser();">{button_confirm}</a>
		</div>
	</div>

<script>
var var_id_toDelete = null;
var var_formsubmit = null;
var var_ajax;

var handlerUserEditSubmit = function submitUserEdit(event) {
	event.preventDefault();
	
	if (false == $(this).data("fillmsg")) {
		var_formsubmit = $(this);
		
		if (3 == $(this).find("input[name=user_access]:checked").val()) {
			alert("{msg_grant_manage}");
		}
		$("div#user_edit_message_popup").popup("open");
		
		return;
	}
	
	var_ajax = $.ajax({
		url: "/user/check_exist_ajax",
		cache: false,
		type: "POST",
		data: {
			action: "edit",
			email: $(this).find("input[name=user_email]").val(),
			name: $(this).find("input[name=user_name]").val(),
			oldname: $(this).find("input[name=user_oldname]").val(),
		},
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			if (var_ajax.status != 200) {
				$("#overlay").removeClass("gray-overlay");
				$(".ui-loader").css("display", "none");
			}
		},
	})
	.done(function() {
		if (var_ajax.status == 202) {
			$("div#list_exist_popup").popup("open");
		}
		else if (var_formsubmit != null) {
			var_formsubmit.unbind("submit", handlerUserEditSubmit).submit();
		}
	})
	.fail(function() {
		console.log("unexpected error case: " + var_ajax.status);
	});
}

function startSubmitUser() {
	if (var_formsubmit != null) {
		var_formsubmit.find("input[name=user_message]").val($("textarea#user_edit_message_popup_text").val());
		var_formsubmit.data("fillmsg", true);
		$("div#user_edit_message_popup").popup("close");
		
		$("#overlay").addClass("gray-overlay");
		$(".ui-loader").css("display", "block");
		
		setTimeout(function() {
			var_formsubmit.submit();
		}, 500);
	}
	else {
		console.log("var_formsubmit is null in StartSubmitUser");
	}
	
	return;
}

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

$("form.user_manage_edit").bind("submit", handlerUserEditSubmit);
</script>
</div>
<div data-role="page" data-url="/user/add" style="overflow-y: hidden;">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<form id="user_manage_add" method="POST" data-fillmsg="false" data-ajax="false">
			<h2 style="text-align: center;">{title_add_form}</h2>
			<div class="ui-field-contain">
				<label for="user_name">{title_name}</label>
				<input type="text" name="user_name" pattern="[A-Za-z0-9\.@ _-]*" title="{hint_name_pattern}" required />
			</div>
			<div class="ui-field-contain">
				<label for="user_email">{title_email}</label>
				<input type="email" name="user_email" required />
			</div>
			<div data-role="collapsible" data-collapsed="false">
				<h4>{title_access}</h4>
				<a href="#user_access_popup" data-rel="popup" class="ui-btn ui-icon-info ui-btn-icon-right ui-corner-all ui-shadow" data-transition="pop">{popup_what}</a>
				<div id="user_access_popup" data-role="popup" class="ui-content">
					<a href="#" data-rel="back" class="ui-btn ui-corner-all ui-shadow ui-btn-a ui-icon-delete ui-btn-icon-notext ui-btn-right"></a>
					{hint_access}
				</div>
				<fieldset data-role="controlgroup" id="user_access">
					<input type="radio" name="user_access" id="user_access_v" value="1" required checked>
					<label for="user_access_v">{title_p_view}</label>
					<input type="radio" name="user_access" id="user_access_m" value="2" required>
					<label for="user_access_m">{title_p_manage}</label>
					<input type="radio" name="user_access" id="user_access_a" value="3" required>
					<label for="user_access_a">{title_p_account}</label>
				</fieldset>
			</div>
<!-- 			<div data-role="collapsible"> -->
<!-- 				<h4>{title_message}</h4> -->
<!-- 				<textarea cols="40" rows="8" name="user_message" id="user_message" placeholder="{hint_message}" maxlength="2048"></textarea> -->
<!-- 			</div> -->
			<input type="hidden" name="user_message" id="user_message" />
			<input type="submit" value="{button_confirm}" />
			</form>
			<div class="zim-error">{error}</div>
		</div>
		<div id="add_exist_popup" data-role="popup" class="ui-content" style="max-width: 250px; text-align: center;">
			<p class="zim-error">{msg_add_exist}</p>
			<a href="#" class="ui-btn ui-corner-all ui-shadow" data-rel="back">{button_ok}</a>
		</div>
		<div id="user_add_message_popup" data-role="popup" class="ui-content">
			<textarea cols="40" rows="8" name="user_message" id="user_add_message_popup_text" placeholder="{hint_message}" maxlength="2048"></textarea>
			<a href="#" data-role="button" onclick="javascript: startSubmitUser();">{button_confirm}</a>
		</div>
	</div>

<script>
var var_ajax;

var handlerUserAddSubmit = function submitUserAdd(event) {
	event.preventDefault();
	
	if (false == $("form#user_manage_add").data("fillmsg")) {
		if (3 == $("fieldset#user_access :radio:checked").val()) {
			alert("{msg_grant_manage}");
		}
		
		$("div#user_add_message_popup").popup("open");
		
		return;
	}
	
	var_ajax = $.ajax({
		url: "/user/check_exist_ajax",
		cache: false,
		type: "POST",
		data: {
			action: "add",
			email: $(this).find("input[name=user_email]").val(),
			name: $(this).find("input[name=user_name]").val(),
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
			$("div#add_exist_popup").popup("open");
		}
		else {
			$("form#user_manage_add").unbind("submit", handlerUserAddSubmit).submit();
		}
	})
	.fail(function() {
		console.log("unexpected error case: " + var_ajax.status);
	});
}

function startSubmitUser() {
	$("input#user_message").val($("textarea#user_add_message_popup_text").val());
	$("form#user_manage_add").data("fillmsg", true);
	$("div#user_add_message_popup").popup("close");
	
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
	
	setTimeout(function() {
		$("form#user_manage_add").submit();
	}, 500);
	
	return;
}

$("form#user_manage_add").bind("submit", handlerUserAddSubmit);
</script>
</div>
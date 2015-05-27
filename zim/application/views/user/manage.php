<div data-role="page" data-url="/user/manage">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<p id="user_manage_add_ok" class="zim-error" style="display: none; text-align: center;">{msg_add_ok}</p>
			<a href="/user/add" data-role="button">
				{button_add_user}
			</a>
			<a href="/user/userlist" data-ajax="false" data-role="button">
				{button_list_user}
			</a>
		</div>
	</div>

<script>
var var_display_add_ok = {display_add_ok};

if (var_display_add_ok) {
	$("p#user_manage_add_ok").show().delay(5000).fadeOut("slow");
}
</script>
</div>
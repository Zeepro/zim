<div data-role="page"> <!-- data-url="/printerstate/upgradenote" -->
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h2>{note_title}</h2>
			<p id="upgradenote_hint" style="display: none;"><!-- perhaps we pass this part from controller to be multi-language -->
				Preliminary Note:<br/>If you wish to check previous release notes, please visit our support website.
			</p>
			<div id="upgradenote_body">{note_body}</div>
			<div id="go_reboot_part" style="display: none;">
<!-- 				<a href="/printerstate/upgradenote?ui&reboot" data-role="button">Mobile UI mode</a> -->
				<a href="/manage/rebooting" data-role="button">{reboot_button}</a>
<!-- 				<a href="#" data-role="button" data-icon="arrow-u" onclick='javascript: $("html, body").animate({ scrollTop: 0 });'>Top</a> -->
			</div>
<!-- 			<a href="javascript:history.back();" data-role="button" data-ajax="false">{back}</a> -->
		</div>
	</div>

<script>
var var_reboot = {reboot_display};

if (var_reboot == true) {
	$("div#go_reboot_part").show();
}
else {
	$("p#upgradenote_hint").show();
}
</script>
</div>
<div data-role="page"> <!-- data-url="/printerstate/upgradenote" -->
	<header data-role="header" class="page-header">
	</header>
	<div class="logo">
		<div id="link_logo"></div>
	</div>
	<div data-role="content">
		<div id="container">
			<h2 style="text-align:center">{note_title}</h2>
			<div id="upgradenote_body">{note_body}</div>
			<a href="/printerstate/upgradenote?ui&reboot" data-role="button" style="display: {reboot_display};">Mobile UI mode</a>
			<a href="/manage/rebooting" id="go_reboot" data-role="button" style="display: {reboot_display};">{reboot_button}</a>
<!-- 			<div style="display: {reboot_display};"> -->
<!-- 				<a href="#" data-role="button" data-icon="arrow-u" onclick='javascript: $("html, body").animate({ scrollTop: 0 });'>Top</a> -->
<!-- 			</div> -->
		</div>
	</div>
</div>
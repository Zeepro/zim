<div data-role="page" data-url="/menu/m_print">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<ul data-role="listview" id="listview_menu_config_manageUser" class="shadowBox" data-inset="true" style="display: none;">
				<li><a href="/user/manage">
					<h2>{link_manage_user}</h2></a>
				</li>
			</ul>
			<ul data-role="listview" id="listview_menu_config_controls" class="shadowBox" data-inset="true" style="display: none;">
				<li><a href="/manage">
					<h2>{link_control}</h2></a>
				</li>
			</ul>
			<ul data-role="listview" id="listview_menu_config_general" class="shadowBox" data-inset="true">
				<li><a href="/printerstate">
					<h2>{link_config}</h2></a>
				</li>
				<li><a href="/printerstate/printerinfo">
					<h2>{link_about}</h2></a>
				</li>
			</ul>
			<img src="/images/listShadow.png" class="shadow" alt="shadow">
		</div>
	</div>

<script>
var var_show_manageUser = {show_mange_user};
var var_show_control = {show_control};

if (var_show_manageUser) {
	$("ul#listview_menu_config_manageUser").show();
}

if (var_show_control) {
	$("ul#listview_menu_config_controls").show();
}

</script>
</div>

<div data-role="page" data-url="/menu/m_total">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/menu/home" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<ul data-role="listview" id="listview_menu_print" data-inset="true">
				<li id="print_import_once"><a href="/sliceupload/upload">
					<h2>{link_import_once}</h2></a>
				</li>
				<li><a href="/printmodel/listmodel">
					<h2>{link_printlist}</h2></a>
				</li>
				<li style="display: {library_visible};"><a href="/userlib">
					<h2>{link_userlib}</h2></a>
				</li>
				<li><a href="/preset">
					<h2>{link_preset}</h2></a>
				</li>
			</ul>
			<div style="clear: both; margin-top: 50px;"></div>
			<ul data-role="listview" id="listview_menu_config_manageUser" data-inset="true" style="display: none;">
				<li><a href="/user/manage">
					<h2>{link_manage_user}</h2></a>
				</li>
			</ul>
			<ul data-role="listview" id="listview_menu_config_controls" data-inset="true" style="display: none;">
				<li><a href="/manage" data-ajax="false">
					<h2>{link_control}</h2></a>
				</li>
			</ul>
			<ul data-role="listview" id="listview_menu_config_general" class="shadowBox" data-inset="true" style="margin-bottom: 0;">
				<li><a href="/printerstate">
					<h2>{link_config}</h2></a>
				</li>
				<li><a href="/printerstate/printerinfo">
					<h2>{link_about}</h2></a>
				</li>
			</ul>
			<div class="shadowContainer"><img src="/images/listShadow.png" class="shadow" alt="shadow"></div>
		</div>
	</div>

<script>
var iOS = (navigator.userAgent.match(/(iPad|iPhone|iPod)/g));
var var_show_manageUser = {show_mange_user};
var var_show_control = {show_control};

if (var_show_manageUser) {
	$("ul#listview_menu_config_manageUser").show();
}

if (var_show_control) {
	$("ul#listview_menu_config_controls").show();
}

$(document).ready(function() {
	if (iOS) {
		$("li#print_import_once").remove();
		$("ul#listview_menu_print").listview("refresh");
	}
});

</script>
</div>

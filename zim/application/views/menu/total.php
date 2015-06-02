<div data-role="page" data-url="/menu/m_total" id="page_menu_home_v2">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/menu/home" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<a href="/printerstate/upgradenote?reboot">
				<span id="upgrade_notification" style="color: red; font-weight: bold; font-size: larger; text-shadow: 0 1px #FFF;">{update_available}</span>
			</a>
			<ul data-role="listview" id="listview_menu_print" data-inset="true">
				<li id="print_import_once"><a href="/sliceupload/upload">
					<h2>{link_import_once}</h2></a>
				</li>
				<li><a href="/printmodel/listmodel">
					<h2>{link_printlist}</h2></a>
				</li>
				<li style="display: {library_visible};"><a href="/userlib" data-ajax="false">
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
var var_interval_checkUpgrade;
var var_ajax_checkUpgrade;

function index_checkUpgrade() {
	var currentPage = $.mobile.activePage.attr('id');
	
	if (typeof(currentPage) == 'undefined' || currentPage != 'page_menu_home_v2') {
		// disable any checking when jqm variable is not ready or changing page
		console.log('disable check upgrade ajax in this interval');
		return;
	}
	
	var_ajax_checkUpgrade = $.ajax({
		cache: false,
		type: "GET",
		url: "/printerstate/checkupgrade",
		success: function () {
			if (var_ajax_checkUpgrade.status == 202) {
				$('span#upgrade_notification').html("{update_hint}");
				clearInterval(var_interval_checkUpgrade);
			}
		},
		error: function (data) {
			console.log('check upgrade failed: ' + data);
		},
	});
}

if (var_show_manageUser) {
	$("ul#listview_menu_config_manageUser").show();
}

if (var_show_control) {
	$("ul#listview_menu_config_controls").show();
}


$(document).ready(function() {
	var_interval_checkUpgrade = setInterval(index_checkUpgrade, 30000);
	
	if (iOS) {
		$("li#print_import_once").remove();
		$("ul#listview_menu_print").listview("refresh");
	}
});

</script>
</div>

<div data-role="page" data-url="/menu/m_print">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<ul data-role="listview" id="listview_menu_print" class="shadowBox" data-inset="true">
				<li id="print_import_once"><a href="/sliceupload/upload">
					<h2>{link_import_once}</h2></a>
				</li>
				<li style="display: {library_visible};"><a href="/userlib">
					<h2>{link_userlib}</h2></a>
				</li>
				<li><a href="/printmodel/listmodel">
					<h2>{link_printlist}</h2></a>
				</li>
				<li><a href="/preset">
					<h2>{link_preset}</h2></a>
				</li>
			</ul>
			<div class="shadowContainer"><img src="/images/listShadow.png" class="shadow" alt="shadow"></div>
		</div>
	</div>

<script>
var iOS = (navigator.userAgent.match(/(iPad|iPhone|iPod)/g));

$(document).ready(function() {
	if (iOS) {
		$("li#print_import_once").remove();
		$("ul#listview_menu_print").listview("refresh");
	}
});

</script>
</div>

<div data-role="page" data-url="/menu_home">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<b>{update_available}<b>
<!-- 			<h2>{title}</h2> -->
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true">
				<li><a href="/printmodel/listmodel">
					<h2>{menu_printlist}</h2></a>
				</li>
				<li id="upload_li"><a href="/sliceupload/upload">
					<h2>{upload}</h2></a>
				</li>
			</ul>
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true">
				<li>
					<a href="#"><h2>{my_library}</h2></a>
				</li>
				<li>
					<a href="#"><h2>{my_zim_shop}</h2></a>
				</li>
			</ul>
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true">
				<li><a href="/manage" data-ajax="false">
					<h2>{manage}</h2></a>
				</li>
			</ul>
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true">
				<li><a href="/printerstate">
					<h2>{menu_printerstate}</h2></a>
				</li>
				<li><a href="/printerstate/printerinfo">
					<h2>{about}</h2></a>
				</li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
	<script>
		var iOS = (navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false);

		if (iOS)
			$("#upload_li").remove();
	</script>
</div>

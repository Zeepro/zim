<div data-role="page" data-url="/user">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<ul data-role="listview" class="shadowBox" data-inset="true">
				<li><a href="/user/info">
					<h2>{button_user_info}</h2></a>
				</li>
				<li><a href="https://zeeproshare.com/user/newsletter" target="_blank">
					<h2>{button_newsletter}</h2></a>
				</li>
				<li><a href="https://zeeproshare.com/user/change_password" target="_blank">
					<h2>{button_edit_password}</h2></a>
				</li>
				<li><a href="https://zeeproshare.com/user/delete_user" target="_blank">
					<h2>{button_delete_user}</h2></a>
				</li>
			</ul>
			<img src="/images/listShadow.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>
<div data-role="page" data-url="sliceupload/upload">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<form action="/sliceupload/upload" method="post" enctype="multipart/form-data" data-ajax="false">
				<label for="file">{select_hint}</label>
				<input type="file" data-clear-btn="true" name="file" id="file_upload">
				<input type="submit" value="{upload_button}" data-icon="arrow-r" data-iconpos="right">
			</form>
			<span id="upload_error">{error}</span>
		</div>
	</div>
</div>
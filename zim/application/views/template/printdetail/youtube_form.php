<div data-role="page" data-url="/printdetail/youtube_form">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container" style="text-align: center;">
			<form action="/printdetail/youtube_form" method="POST" id="yt_form" data-ajax="false">
				<div class="ui-grid-a">
					<div class="ui-block-a" style="width:20%">
						{title_label}
					</div>
					<div class="ui-block-b" style="width:80%">
						<input name="yt_title" value="{yt_title}" />
					</div>
					<div class="ui-block-a" style="width:20%">
						{desc_label}
					</div>
					<div class="ui-block-b" style="width:80%">
						<textarea name="yt_description" form="yt_form" placeholder="Enter description here">{yt_desc}</textarea>
					</div>
					<div class="ui-block-a" style="width:20%">
						{tags_label}
					</div>
					<div class="ui-block-b" style="width:80%">
						<input name="yt_tags" value="{yt_tags}" />
					</div>
					<div class="ui-block-a" style="width:20%">
						{privacy_label}
					</div>
					<div class="ui-block-b" style="width:80%">
						<select name="yt_privacy">
							<option value="public">{yt_privacy_public}</option>
							<option value="unlisted">{yt_privacy_unlisted}</option>
							<option value="private">{yt_privacy_private}</option>
						</select>
					</div>
				</div>
				<input type="submit" value="{upload_to_yt}" />
			</form>
		</div>
	</div>
</div>

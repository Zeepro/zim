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
						Title
					</div>
					<div class="ui-block-b" style="width:80%">
						<input name="yt_title" value="3D printing by zim" />
					</div>
					<div class="ui-block-a" style="width:20%">
						Description
					</div>
					<div class="ui-block-b" style="width:80%">
						<textarea name="yt_description" form="yt_form" placeholder="Enter description here">Time-lapse video powered by zim 3D printer, the reference in personal 3D printing. Visit zeepro.com to join the zim experience !</textarea>
					</div>
					<div class="ui-block-a" style="width:20%">
						Tags
					</div>
					<div class="ui-block-b" style="width:80%">
						<input name="yt_tags" value="zim, zeepro" />
					</div>
					<div class="ui-block-a" style="width:20%">
						Privacy
					</div>
					<div class="ui-block-b" style="width:80%">
						<select name="yt_privacy">
							<option value="public">Public</option>
							<option value="unlisted">Unlisted</option>
							<option value="private">Private</option>
						</select>
					</div>
				</div>
				<input type="submit" value="Upload my video to Youtube" />
			</form>
		</div>
	</div>
</div>

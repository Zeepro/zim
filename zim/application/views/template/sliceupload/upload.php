<div data-role="page" data-url="/sliceupload/upload">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
		<form action="/sliceupload/upload" method="post" enctype="multipart/form-data" data-ajax="false">
		<div id="set" data-role="collapsible-set" data-inset="false">
			<div id="tab1" data-role="collapsible" data-collapsed="false">
				<h3> {header_single} </h3>
				<label for="file">{select_hint}</label>
				<input type="file" data-clear-btn="true" name="file" id="file_upload1" />
			</div>
			<div id="tab2" data-role="collapsible">
				<h3> {header_multi} </h3>
				<label for="file_c1">{select_hint_multi}</label>
				<input type="file" data-clear-btn="true" name="file_c1" id="file_upload2" />
				<br />
				<input type="file" data-clear-btn="true" name="file_c2" id="file_upload3" />
			</div>
			<input type="submit" value="{upload_button}" data-icon="arrow-r" data-iconpos="right" />
		</div>
		</form>
			{goto_slice}
			<span id="upload_error">{error}</span>
		</div>
	</div>
</div>

<script>
/*
 
 SCRIPT FOR JQUERY MOBILE 1.3
 
	$("#tab1").bind("expand", function(event)
	{
		$("#file_upload2").val("");
		$("#file_upload3").val("");
	});
	$("#tab2").bind("expand", function()
	{
		$("#file_upload1").val("");
	});
*/

$("#tab1").on("collapsibleexpand", function(event)
{
	$("#file_upload2").val("");
	$("#file_upload3").val("");
}); 

$("#tab2").on("collapsibleexpand", function(event)
{
	$("#file_upload1").val("");
});

</script>
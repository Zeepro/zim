<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<form method="post" id="form_userlib_storemodel" enctype="multipart/form-data" data-ajax="false">
			<input type="hidden" id="usermodel_id" name="id" value="{model_id}">
			<label for="name"><h2>{title_name}</h2></label>
			<input type="text" name="name" id="usermodel_name" value="" data-clear-btn="true" required />
			<br />
			<div id="set" data-role="collapsible-set" data-inset="true">
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
				<input type="submit" value="{upload_button}" data-icon="arrow-r" data-iconpos="right" /> <!-- onclick='javascript: uploadfile_wait();' -->
			</div>
			</form>
			<span class="zim-error" id="upload_error">{error}</span>
		</div>
		<div id="overwrite_popup" data-role="popup" data-dismissible="false" class="ui-content" style="max-width: 250px; text-align: center;">
			{save_overwrite}
			<br /><br />
			<div class="ui-grid-a">
				<div class="ui-block-a">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" onclick="javascript: do_overwriteModel();">{button_save_ok}</a>
				</div>
				<div class="ui-block-b">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back">{button_save_no}</a>
				</div>
			</div>
		</div>
	</div>

<script>
var var_submitModel = false;

var handlerUserModelSubmit = function submitUserModel(event) {
	event.preventDefault();
	
	if (parseInt($("input#usermodel_id").val()) > 0 && var_submitModel) {
		$("form#form_userlib_storemodel").unbind("submit", handlerUserModelSubmit).submit();
		uploadfile_wait();
		
		return;
	}
	
	var_ajax = $.ajax({
		url: "/userlib/addmodel_ajax",
		cache: false,
		type: "POST",
		dataType: "json",
		data: { name: $("input#usermodel_name").val() },
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function(data) {
		if (typeof(data.id) == "undefined" || typeof(data.exist) == "undefined") {
			return;
		}
		
		$("input#usermodel_id").val(data.id);
		if (data.exist == false) {
			var_submitModel = true;
			$("form#form_userlib_storemodel").unbind("submit", handlerUserModelSubmit).submit();
			uploadfile_wait();
		}
		else {
			$("div#overwrite_popup").popup("open");
		}
	})
	.fail(function() {
		console.log("unexpected error case: " + var_ajax.status);
	});
	
	return;
}

function do_overwriteModel() {
	var_submitModel = true;
// 	$("div#overwrite_popup").popup("close");
	$("form#form_userlib_storemodel").unbind("submit", handlerUserModelSubmit).submit();
	uploadfile_wait();
	
	return;
}

function uploadfile_wait() {
	// this create a blocked spinner when we return to this page by back button
	if ($("#usermodel_name").val() == "") {
		return;
	}
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
}

$("#form_userlib_storemodel").bind("submit", handlerUserModelSubmit);

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
</div>

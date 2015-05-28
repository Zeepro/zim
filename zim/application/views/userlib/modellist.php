<div data-role="page" style="overflow:hidden;" id="userlib_modelList_{random_nb}">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<a href="/userlib/storemodel" data-role="button">{button_add_model}</a>
			<ul data-role="listview" id="listview_usermodel" data-inset="true" data-filter="true" data-filter-placeholder="{search_hint}" data-filter-theme="d" data-split-icon="delete" data-split-theme="b">
				{model_list}
				<li id="usermodel_{id}">
					<a href="{link}" data-rel="popup" data-callpopup="{popup}" id="usermodel_link_{id}" onclick="javascript: clickModelbyState('usermodel_link_{id}');">
						<img alt="Model image" src="{image}">{name}
					</a>
					<a href='#delete_popup' data-rel="popup" onclick="javascript: pre_deleteModel('{id}');">Delete model</a>
				</li>
				{/model_list}
			</ul>
			<div class="zim-error">{error_get_list}</div>
		</div>
		<div id="delete_popup" data-role="popup" data-dismissible="false" class="ui-content" style="max-width: 250px; text-align: center;">
			{message_delete}
			<br /><br />
			<div class="ui-grid-a">
				<div class="ui-block-a">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back" data-transition="flow" onclick="javascript: do_deleteModel();">{button_delete_ok}</a>
				</div>
				<div class="ui-block-b">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back">{button_delete_no}</a>
				</div>
			</div>
		</div>
		<div id=upload_model_popup data-role="popup" class="ui-content" style="max-width: 250px; text-align: center;">
			{msg_upload_model}
			<br /><br />
			<a href="#" class="ui-btn ui-corner-all ui-shadow" data-rel="back">{button_ok}</a>
		</div>
		<div id=prepare_model_popup data-role="popup" class="ui-content" style="max-width: 250px; text-align: center;">
			{msg_prepare_model}
			<br /><br />
			<a href="#" class="ui-btn ui-corner-all ui-shadow" data-rel="back">{button_ok}</a>
		</div>
		<div id=error_model_popup data-role="popup" class="ui-content" style="max-width: 250px; text-align: center;">
			{msg_error_model}
			<br /><br />
			<a href="#" class="ui-btn ui-corner-all ui-shadow" data-rel="back">{button_ok}</a>
		</div>
	</div>

<script>
var var_id_toDelete = null;

function clickModelbyState(var_id) {
	if (typeof(var_id) != "undefined") {
		if (false == $("a#" + var_id).data("callpopup")) {
			window.location.href = $("a#" + var_id).attr("href");
		}
	}
	
	return;
}

function pre_deleteModel(var_id) {
	if (typeof(var_id) != "undefined" && $("li#usermodel_" + var_id)) {
		var_id_toDelete = var_id;
	}
	
	return;
}

function do_deleteModel() {
	if (var_id_toDelete != null) {
		$.ajax({
				cache: false,
				type: "POST",
				url: "/userlib/deletemodel_ajax",
				data: { "id": var_id_toDelete },
				beforeSend: function() {
					$("#overlay").addClass("gray-overlay");
					$(".ui-loader").css("display", "block");
				},
				complete: function() {
					$(".ui-loader").css("display", "none");
					$("#overlay").removeClass("gray-overlay");
				},
				success: function() {
					$("li#usermodel_" + var_id_toDelete).remove();
					$("ul#listview_usermodel").listview("refresh");
				},
				error: function (data, textStatus, xhr) {
					alert("{msg_delete_error}");
					console.log(xhr);
				},
		});
	}
	
	return;
}
</script>
</div>
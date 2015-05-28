<div data-role="page" style="overflow:hidden;">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<ul data-role="listview" id="listview_userprint" data-inset="true" data-filter="true" data-filter-placeholder="{search_hint}" data-filter-theme="d" data-split-icon="delete" data-split-theme="b">
				{print_list}
				<li id="userprint_{model_id}_{timestamp}">
					<a href="{link}"><img alt="Print image" src="{image}"><h2>{date}</h2><p>{preset_name_title}{preset}</p></a>
					<a href='#delete_popup' data-rel="popup" onclick="javascript: pre_deletePrint('{model_id}', '{timestamp}');">Delete print</a>
				</li>
				{/print_list}
			</ul>
		</div>
		<div id="delete_popup" data-role="popup" data-dismissible="false" class="ui-content" style="max-width: 250px; text-align: center;">
			{message_delete}
			<br /><br />
			<div class="ui-grid-a">
				<div class="ui-block-a">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back" data-transition="flow" onclick="javascript: do_deletePrint();">{button_delete_ok}</a>
				</div>
				<div class="ui-block-b">
					<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" data-rel="back">{button_delete_no}</a>
				</div>
			</div>
		</div>
		<div id=upload_print_popup data-role="popup" class="ui-content" style="max-width: 250px; text-align: center;">
			{msg_upload_print}
			<br /><br />
			<a href="#" class="ui-btn ui-corner-all ui-shadow" data-rel="back">{button_ok}</a>
		</div>
		<div id=error_print_popup data-role="popup" class="ui-content" style="max-width: 250px; text-align: center;">
			{msg_error_print}
			<br /><br />
			<a href="#" class="ui-btn ui-corner-all ui-shadow" data-rel="back">{button_ok}</a>
		</div>
	</div>

<script>
var var_id_toDelete = null;
var var_timestamp_toDelete = null;

function pre_deletePrint(var_id, var_timestamp) {
	if (typeof(var_id) != "undefined" && typeof(var_timestamp) != "undefined"
			&& $("li#userprint_" + var_id + "_" + var_timestamp)) {
		var_id_toDelete = var_id;
		var_timestamp_toDelete = var_timestamp;
	}
	
	return;
}

function do_deletePrint() {
	if (var_id_toDelete != null && var_timestamp_toDelete != null) {
		$.ajax({
				cache: false,
				type: "POST",
				url: "/userlib/deleteprint_ajax",
				data: {
					"id":	var_id_toDelete,
					"time":	var_timestamp_toDelete,
				},
				beforeSend: function() {
					$("#overlay").addClass("gray-overlay");
					$(".ui-loader").css("display", "block");
				},
				complete: function() {
					$(".ui-loader").css("display", "none");
					$("#overlay").removeClass("gray-overlay");
				},
				success: function() {
					$("li#userprint_" + var_id_toDelete + "_" + var_timestamp_toDelete).remove();
					$("ul#listview_userprint").listview("refresh");
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
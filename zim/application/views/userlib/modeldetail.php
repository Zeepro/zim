<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h1>{model_name}</h1>
			<p id="modelDetail_waitMessage">{msg_wait_prepare}</p>
			<p id="modelDetail_3dfileNotReady" style="display: none;">{msg_3dfile_n_rdy}</p>
			<div id="modelDetail_3dfileReady" style="display: none;">
				<a href="#" data-ajax="false" data-role="button" onclick="javascript: start_importModel();">{button_3dfile}</a>
			</div>
			<div id="modelDetail_havePrints" style="display: none;">
				<a href="/userlib/modelgcodes?id={model_id}" data-ajax="false" data-role="button">{button_prints}</a>
			</div>
			<p id="modelDetail_stateInfo" style="text-align: center;"></p>
			<p id="modelDetail_error" class="zim-error"></p>
		</div>
	</div>

<script>
var var_show_3dfile = {show_3dfile};
var var_show_prints = {show_prints};
var var_ajax_download;
var var_ajax_import;
var var_usermodel_id = '{model_id}';

function start_importModel() {
	console.log("start_importModel");
	$("p#modelDetail_stateInfo").html("{msg_3dfile_download}");
	
	var_ajax_download = $.ajax({
		url: "/userlib/preparemodel_ajax",
		cache: false,
		type: "POST",
		data: { id: var_usermodel_id },
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			if (var_ajax_download.status != 403) {
				$("#overlay").removeClass("gray-overlay");
				$(".ui-loader").css("display", "none");
			}
		},
	})
	.done(function() {
		do_importModel();
	})
	.fail(function() {
		if (var_ajax_download.status == 403) {
			setTimeout(start_importModel, 5000);
		}
		else {
			console.log("unexpected error case: " + var_ajax_download.status);
			$("p#modelDetail_stateInfo").empty();
			$("p#modelDetail_error").html("{msg_download_fail}");
		}
	});
	
	return;
}

function do_importModel() {
	console.log("do_importModel");
	$("p#modelDetail_stateInfo").html("{msg_3dfile_import}");
	
	var_ajax_import = $.ajax({
		url: "/userlib/importmodel_ajax",
		cache: false,
		type: "POST",
		data: { id: var_usermodel_id },
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
	})
	.done(function() {
		window.location.href="/sliceupload/slice";
	})
	.fail(function() {
		console.log("unexpected error case: " + var_ajax_import.status);
		$("p#modelDetail_stateInfo").empty();
		$("p#modelDetail_error").html("{msg_import_fail}");
	});
	
	return;
}

$("p#modelDetail_waitMessage").hide();
if (var_show_3dfile == true) {
	$("div#modelDetail_3dfileReady").show();
}
else {
	$("p#modelDetail_3dfileNotReady").show();
}
if (var_show_prints == true) {
	$("div#modelDetail_havePrints").show();
}

</script>
</div>
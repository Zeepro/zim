<div data-role="page" data-url="/printerstoring/liststl">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<p>{uploaded}</p>
		<p>{list_info}</p>
		<div id="container">
<!-- 			<h2>{title}</h2> -->
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true" data-filter="true" data-filter-placeholder="" data-filter-theme="d">
				{list}
				<li>
					<img src="{image}" style="vertical-align:middle">
					<b style="position:absolute; top:39%;">{name}</b>
					<span data-inline="true" id="buttons-{id}" style="position:absolute; right:0;">
						<a data-inline="true" href='#' id="printmodel-{id}" data-role="button" onclick="printmodel(this.id);">{print-model}</a>
						<a data-inline="true" href='#' id="deletemodel-{id}" data-role="button" onclick="deletemodel(this.id);">{delete-model}</a>
					</span>
				</li>
				{/list}
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
	
<script type="text/javascript">
function printmodel(clicked_id){
//	console.log(clicked_id);
	var tmp = clicked_id.split('-');
	var id = tmp[1];

	$.ajax({
			cache: false,
			type: "GET",
			url: "/rest/libprintstl",
			data: { "id": id},
//			dataType: "json",
			success: function (data, textStatus, xhr) {
				window.location.href = "/sliceupload/slice";
			},
			error: function (data, textStatus, xhr) {
				alert("{print_error}");
				console.log(data);
			},
	});
}

function deletemodel(clicked_id){
//	console.log(clicked_id);
	var tmp = clicked_id.split('-');
	var id = tmp[1];

	$.ajax({
			cache: false,
			type: "GET",
			url: "/rest/libdeletestl",
			data: { "id": id},
//			dataType: "json",
			success: function (data, textStatus, xhr) {
				$('#' + clicked_id).closest('li').remove();
			},
			error: function (data, textStatus, xhr) {
				console.log(data);
				console.log(textStatus);
				console.log(xhr);
				alert('{delete_error}');
			},
	});
}

<!--
function load_wait() {
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
}

$(document).on("pagebeforehide", function() {
	$(".ui-loader").css("display", "none");
	$("#overlay").removeClass("gray-overlay");
});
-->
</script>

</div>

<div data-role="page" data-url="/printerstoring/listgcode">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<p>{list_info}</p>
		<div id="container">
			<div data-role="fieldcontain">
				<select name="select_sort" id="select_sort">
					<option value="alphabetical">{select_alphabetical}</option>
					<option value="mostrecent" selected="selected">{select_mostrecent}</option>
				</select>
			</div>
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true" data-filter="true" data-filter-placeholder="" data-filter-theme="d">
			</ul>
<!-- 			<h2>{title}</h2> -->
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
	
<script type="text/javascript">
var modellist = {encoded_list};
modellist = $.map(modellist, function(value, index) {
    return [value];
});
function compare_date(a,b) {
  if (a.creation_date < b.creation_date)
     return 1;
  if (a.creation_date > b.creation_date)
    return -1;
  return 0;
}

function compare_name(a,b) {
  if (a.modelname < b.modelname)
     return -1;
  if (a.modelname > b.modelname)
    return 1;
  return 0;
}

function displaylist() {
	var listelement = $('#listview');
	listelement.empty();

//	modellist.sort(compare_date);

	for(k in modellist) {
		var model = modellist[k];
		listelement.append("<li><img src=\""+ model.image +"\"> \
					<span style=\"position:absolute; top:39%;\">[" + model.creation_datestr +"] <b>"+ model.modelname +"</b></span> \
					<span data-inline=\"true\" id=\"buttons-" + model.mid + "\" style=\"position:absolute; right:0;\"> \
						<a data-inline=\"true\" href='#' id=\"printmodel-" + model.mid + "\" class=\"ui-link ui-btn ui-btn-inline ui-shadow ui-corner-all\" data-role=\"button\" onclick=\"printgcode(this.id);\">{print-model}</a> \
						<a data-inline=\"true\" href='#' id=\"deletemodel-" + model.mid + "\" class=\"ui-link ui-btn ui-btn-inline ui-shadow ui-corner-all\" data-role=\"button\" onclick=\"deletegcode(this.id);\">{delete-model}</a> \
					</span></li>");
	}
	listelement.listview("refresh");
}

$(document).ready(function() {
	modellist.sort(compare_date);
	displaylist();

	$('#select_sort').change(function () {
		console.log($(this).find(":selected").val());
		if ($(this).find(":selected").val() == 'alphabetical') {
			modellist.sort(compare_name);
			displaylist();
		}
		else if ($(this).find(":selected").val() == 'mostrecent') {
			modellist.sort(compare_date);
			displaylist();
		}
  })
  .change();
});

function printgcode(clicked_id){
//	console.log(clicked_id);
	var tmp = clicked_id.split('-');
	var id = tmp[1];

	$.ajax({
			cache: false,
			type: "GET",
			url: "/rest/libprintgcode",
			data: { "id": id},
//			dataType: "json",
			success: function (data, textStatus, xhr) {
				window.location.href = "/printdetail/status?id=" + id;
			},
			error: function (data, textStatus, xhr) {
				alert("{print_error}");
				console.log(data);
			},
	});
}

function deletegcode(clicked_id){
//	console.log(clicked_id);
	var tmp = clicked_id.split('-');
	var id = tmp[1];

	$.ajax({
			cache: false,
			type: "GET",
			url: "/rest/libdeletegcode",
			data: { "id": id},
//			dataType: "json",
			success: function (data, textStatus, xhr) {
				$('#' + clicked_id).closest('li').remove();
			},
			error: function (data, textStatus, xhr) {
				alert('{delete_error}');
				console.log(xhr);
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

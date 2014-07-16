<div data-role="page" data-url="/sliceupload/slicestatus">
	<header data-role="header" class="page-header"></header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div id="detail_zone" style="clear: both; text-align: center;">
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
<!--
var var_slice_status;
var var_slice_status_lock = false;
var var_slice_interval;


$(document).ready(prepareDisplay());

function prepareDisplay() {
	checkSlice(); // launch checking directly
	var_slice_interval = setInterval(checkSlice, 3000);

	return;
}

function checkSlice() {
	if (var_slice_status_lock == true) {
		return;
	}
	else {
		var_slice_status_lock = true;
	}
	
	var_slice_status = $.ajax({
		url: "/sliceupload/slice_status_ajax",
		type: "GET",
		cache: false,
	})
	.done(function(html) {
		if (var_slice_status.status == 202) { // finished checking, wait user to input
			clearInterval(var_slice_interval);
			window.location.href='/sliceupload/slice?callback';
		}
		else if (var_slice_status.status == 200) { // in checking
			// html => percentage
			$("#detail_zone").html("{wait_in_slice} " + html);
		}
	})
	.fail(function() { // not allowed
		window.location.replace("/");
//			clearInterval(var_refreshChangeStatus);
//			$("#print_detail_info").html('<p>{finish_info}</p>');
//			$('button#print_action').click(function(){window.location.href='/'; return false;});
//			$('button#print_action').parent().find('span.ui-btn-text').text('{return_button}');
//			$('button#print_action').html('{return_button}');
//			alert("failed");
	})
	.always(function() {
		var_slice_status_lock = false;
	});
	
	return;
}

-->
</script>
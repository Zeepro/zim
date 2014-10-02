<div id="overlay"></div>
<div data-role="page" data-url="/sliceupload/reducesize">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<h2>{reduce_size_title}</h2>
			<label>{reduce_size_text}</label>
			<br />
			<div>{reduce_size_graduation}</div>
			<form action="" method="post">
					<input type="range" name="sizepercentage" id="sizepercentage" value="" min="1" max="100">
					<div id="dimension"><center>{reduced_size}: <span id="x_size"></span>mm x <span id="y_size"></span>mm x <span id="z_size"></span>mm</center></div>
					<div id="submit_container"><input type="submit" value="{submit_button}" data-ajax="false"></div>
					<div id="submit_container"><input type="button" value="{cancel_button}" data-ajax="false"></div>
			</form>
		</div>
	</div>

<script>
var var_xsize = {xsize};
var var_ysize = {ysize};
var var_zsize = {zsize};
var var_max_percent = {max_percent};

$(document).ready(function () {

	$(".ui-slider-handle").attr('style', "left: 100%;");
	$("#sizepercentage").attr('max', var_max_percent * 100);
	$('#sizepercentage').val(var_max_percent * 100);
 	$("#x_size").text(var_xsize * var_max_percent);
 	$("#y_size").text(var_ysize * var_max_percent);
 	$("#z_size").text(var_zsize * var_max_percent);

   $('#sizepercentage').on('change', function () { 
   	$("#x_size").text(var_xsize * $('#sizepercentage').val() / 100);
   	$("#y_size").text(var_ysize * $('#sizepercentage').val() / 100);
   	$("#z_size").text(var_zsize * $('#sizepercentage').val() / 100);
   });
});

$("input[type=submit]").on('click', function()
{
	$("#overlay").addClass("gray-overlay");
	$(".ui-loader").css("display", "block");
});
</script>

</div>

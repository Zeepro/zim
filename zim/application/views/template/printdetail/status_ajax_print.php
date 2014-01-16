					<p>{print_percent}</p>
					<p>{print_remain}</p>
					<div id="print_detail_info_temper_l">{print_temperL}<br></div><br>
					<div id="print_detail_info_temper_r">{print_temperR}<br></div><br>
					
<script type="text/javascript">
<!--
$('<input>').appendTo('#print_detail_info_temper_l').attr({'name':'slider','id':'sliderL','data-highlight':'true','min':'0','max':'260','value':'{value_temperL}','type':'range'}).slider({
	create: function( event, ui ) {
		$(this).parent().find('input').hide();
		$(this).parent().find('input').css('margin-left','-9999px'); // Fix for some FF versions
		$(this).parent().find('.ui-slider-track').css('margin-left','0px');
		$(this).parent().find('.ui-slider-track').css('margin-right','0px');
		$(this).parent().find('.ui-slider-handle').hide();
	}
});
$('<input>').appendTo('#print_detail_info_temper_r').attr({'name':'slider','id':'sliderR','data-highlight':'true','min':'0','max':'260','value':'{value_temperR}','type':'range'}).slider({
	create: function( event, ui ) {
		$(this).parent().find('input').hide();
		$(this).parent().find('input').css('margin-left','-9999px'); // Fix for some FF versions
		$(this).parent().find('.ui-slider-track').css('margin-left','0px');
		$(this).parent().find('.ui-slider-track').css('margin-right','0px');
		$(this).parent().find('.ui-slider-handle').hide();
	}
});

//-->
</script>

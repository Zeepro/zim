				<p>{unload_info}</p>

<script type="text/javascript">
var_next_phase = '{next_phase}';

$('<input>').appendTo('#cartridge_detail_info').attr({'name':'slider','id':'sliderL','data-highlight':'true','min':'0','max':'260','value':'{value_temper}','type':'range'}).slider({
	create: function( event, ui ) {
		$(this).parent().find('input').hide();
		$(this).parent().find('input').css('margin-left','-9999px'); // Fix for some FF versions
		$(this).parent().find('.ui-slider-track').css('margin-left','0px');
		$(this).parent().find('.ui-slider-track').css('margin-right','0px');
		$(this).parent().find('.ui-slider-handle').hide();
	}
});
</script>

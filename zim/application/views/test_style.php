<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">back</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible" style="align: center;">
				<h4>move</h4>
				<div class="container_16">
					<div class="grid_2 suffix_5">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left">XYZ</a>
					</div>
					<div class="grid_2 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left">50</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left">10</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="left">1</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left">50</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left">10</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left">1</a>
					</div>
					<div class="grid_3">
						<input type="number" style="text-align:right;" data-clear-btn="false" name="xy_speed" id="xy_speed" value="3000">
					</div>
					<div class="grid_1" style="">mm/s</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left">1</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left">10</a>
					</div>
					<div class="grid_2">
						<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="left">50</a>
					</div>
					<div class="grid_2 suffix_5"">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left">X</a>
					</div>
					<div class="grid_2 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left">1</a>
					</div>
					<div class="grid_2 prefix_7 suffix_7">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left">10</a>
					</div>
					<div class="grid_2 prefix_7 suffix_1">
						<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="left">50</a>
					</div>
					<div class="grid_2 suffix_4">
						<a href="#" data-role="button" data-icon="home" data-iconpos="left">Y</a>
					</div>
				</div>
			</div>
			<div data-role="collapsible" data-collapsed="false" style="align: center;">
				<h4>led</h4>
				<div class="ui-grid-a">
					<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
						<label for="slider"><h2>string led</h2></label>
					</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-f" style="height:3em;">
						<select name="strip_led" id="strip_led" data-role="slider" data-track-theme="a" data-theme="a">
							<option value="off">off</option>
							<option value="on">on</option>
						</select>
					</div></div>
					<div class="ui-block-a"><div class="ui-bar ui-bar-f" style="height:3em;">
						<label for="slider"><h2>head led</h2></label>
					</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-f" style="height:3em;">
						<select name="head_led" id="head_led" data-role="slider" data-track-theme="a" data-theme="a">
							<option value="off">off</option>
							<option value="on">on</option>
						</select>
					</div></div>
				</div>
			</div>
			
			<div class="ui-grid-a">
				<div class="ui-block-a"><div class="ui-bar ui-bar-d" style="height:2em;">title</div></div>
				<div class="ui-block-b"><div class="ui-bar ui-bar-c" style="height:2em;">value</div></div>
				<div class="ui-block-a"><div class="ui-bar ui-bar-d" style="height:2em;">title</div></div>
				<div class="ui-block-b"><div class="ui-bar ui-bar-c" style="height:2em;">value</div></div>
			</div>
			<p>
				<a href="#" data-role="button" data-inline="true">True</a>
				<a href="#" data-role="button" data-inline="true">False</a>
			</p>
			<a href="#" data-role="button" data-icon="plus" data-iconpos="notext" data-theme="c" data-inline="true">Plus</a>
			<a href="#" data-role="button" data-inline="true">Text only</a>
			<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="left" data-inline="true">Left</a>
			<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="right" data-inline="true">Right</a>
			<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="top" data-inline="true">Top</a>
			<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="bottom" data-inline="true">Bottom</a>
			<a href="#" data-role="button" data-icon="delete" data-iconpos="notext" data-inline="true">Icon only</a>
			<a href="#" data-role="button" class="ui-disabled">Disabled anchor via class</a>
			<a href="#" data-role="button">Anchor</a>
			<form>
				<button>Button</button>
				<input type="button" value="Input">
				<input type="submit" value="Submit">
				<input type="reset" value="Reset">
				<button disabled="disabled">Button with disabled attribute</button>
				<input type="button" value="Input with disabled attribute" disabled="disabled">
				<div data-role="fieldcontain">
					<label for="select-native-1">Basic:</label>
					<select name="select-native-1" id="select-native-1">
						<option value="1">The 1st Option</option>
						<option value="2">The 2nd Option</option>
						<option value="3">The 3rd Option</option>
						<option value="4">The 4th Option</option>
					</select>
				</div>
				<label for="file-1">File: </label>
				<input type="file" data-clear-btn="true" name="file-1" id="file-1" value="">
				<label for="text-12">Text normal:</label>
				<input type="text" name="text-12" id="text-12" value="">
				<label for="text-6">Text hint:</label>
				<input type="text" name="text-6" id="text-6" value="" placeholder="hint here">
				<label for="number-1">Number:</label>
				<input type="number" style="text-align:right;" data-clear-btn="false" name="number-1" id="number-1" value="">
				<label for="number-2">Number with clear:</label>
				<input type="number" style="text-align:right;" data-clear-btn="true" name="number-2" id="number-2" value="">
			</form>
			<a href="#" data-role="button" data-icon="plus" data-iconpos="notext" data-theme="c" data-inline="true">Plus</a>
			<a href="#" data-role="button" data-icon="minus" data-iconpos="notext" data-theme="c" data-inline="true">Minus</a>
			<a href="#" data-role="button" data-icon="delete" data-iconpos="notext" data-theme="c" data-inline="true">Delete</a>
			<a href="#" data-role="button" data-icon="arrow-l" data-iconpos="notext" data-theme="c" data-inline="true">Arrow left</a>
			<a href="#" data-role="button" data-icon="arrow-r" data-iconpos="notext" data-theme="c" data-inline="true">Arrow right</a>
			<a href="#" data-role="button" data-icon="arrow-u" data-iconpos="notext" data-theme="c" data-inline="true">Arrow up</a>
			<a href="#" data-role="button" data-icon="arrow-d" data-iconpos="notext" data-theme="c" data-inline="true">Arrow down</a>
			<a href="#" data-role="button" data-icon="check" data-iconpos="notext" data-theme="c" data-inline="true">Check</a>
			<a href="#" data-role="button" data-icon="gear" data-iconpos="notext" data-theme="c" data-inline="true">Gear</a>
			<a href="#" data-role="button" data-icon="refresh" data-iconpos="notext" data-theme="c" data-inline="true">Refresh</a>
			<a href="#" data-role="button" data-icon="forward" data-iconpos="notext" data-theme="c" data-inline="true">Forward</a>
			<a href="#" data-role="button" data-icon="back" data-iconpos="notext" data-theme="c" data-inline="true">Back</a>
			<a href="#" data-role="button" data-icon="grid" data-iconpos="notext" data-theme="c" data-inline="true">Grid</a>
			<a href="#" data-role="button" data-icon="star" data-iconpos="notext" data-theme="c" data-inline="true">Star</a>
			<a href="#" data-role="button" data-icon="alert" data-iconpos="notext" data-theme="c" data-inline="true">Alert</a>
			<a href="#" data-role="button" data-icon="info" data-iconpos="notext" data-theme="c" data-inline="true">Info</a>
			<a href="#" data-role="button" data-icon="home" data-iconpos="notext" data-theme="c" data-inline="true">Home</a>
			<a href="#" data-role="button" data-icon="search" data-iconpos="notext" data-theme="c" data-inline="true">Search</a>
			<a href="#" data-role="button" data-icon="bars" data-iconpos="notext" data-theme="c" data-inline="true">Bars</a>
			<a href="#" data-role="button" data-icon="edit" data-iconpos="notext" data-theme="c" data-inline="true">Edit</a>
			<div id="print_detail_info_temper_l">Temperature: 200<br></div><br><br>
			<ul data-role="listview" id="listview" class="shadowBox" data-inset="true" data-filter="true" data-filter-placeholder="search" data-filter-theme="d">
				<li><a href="#">
					<img src="/rest/getpicture?id=45f4ce6c3306644b1efe333f4f8d6929&p=1">
					<h2 style="margin-left:100px;">name</h2></a>
				</li>
				<li><a href="#">
					<img src="/rest/getpicture?id=45f4ce6c3306644b1efe333f4f8d6929&p=1">
					<h2 style="margin-left:100px;">test</h2></a>
				</li>
			</ul>
			<img src="/assets/images/shadow2.png" class="shadow" alt="shadow">
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('<input>').appendTo('#print_detail_info_temper_l')
	.attr({'name':'slider','id':'sliderL','data-highlight':'true','min':'0','max':'260','value':'200','type':'range'}).slider({
		create: function( event, ui ) {
			$(this).parent().find('input').hide();
			$(this).parent().find('input').css('margin-left','-9999px'); // Fix for some FF versions
			$(this).parent().find('.ui-slider-track').css('margin-left','0px');
			$(this).parent().find('.ui-slider-track').css('margin-right','0px');
			$(this).parent().find('.ui-slider-handle').hide();
		}
	});
});
</script>

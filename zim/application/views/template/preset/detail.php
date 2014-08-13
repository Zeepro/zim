<div data-role="page">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-role="button" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<style>
				div.ui-slider-switch div.ui-slider-inneroffset a.ui-slider-handle-snapping {display: none;}
/* 				div.ui-slider-switch {width: 150px;} */
			</style>
			<div style="clear: both;">
				<h2>{preset_title}</h2>
				<div style="float: right; margin-top: -4.5em; {hide_delete}" id="delete_container">
					<a href="/preset/delete?id={preset_id}" data-role="button" data-icon="delete" data-ajax="false" data-inline="true">Delete</a> <!-- class="ui-disabled" -->
				</div>
			</div>
			<form action="/preset/detail?id={preset_id}{preset_newurl}" method="post">
			<div data-role="collapsible">
				<h4>{layer_perimeter_title}</h4>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{layer_perimeter_subtitle1}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="layer_height">{layer_height}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" step="0.01" min="0" data-clear-btn="false" name="layer_height" id="layer_height" value="{layer_height_value}" min="0.025" max="0.2">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="first_layer_height">{first_layer_height}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="first_layer_height" id="first_layer_height" value="{first_layer_height_value}" min="0.05" max="0.4">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{layer_perimeter_subtitle2}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="perimeters">{perimeters}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="perimeters" id="perimeters" value="{perimeters_value}" min="1" max="10">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="spiral_vase">{spiral_vase}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="spiral_vase" id="spiral_vase" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {spiral_vase_value}>{switch_on}</option>
							</select>
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{layer_perimeter_subtitle3}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="slider">Solid layers:</label>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="top_solid_layers" style="padding-left: 2em;">{top_solid_layers}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="top_solid_layers" id="top_solid_layers" value="{top_solid_layers_value}" min="1" max="20">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="bottom_solid_layers" style="padding-left: 2em;">{bottom_solid_layers}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="bottom_solid_layers" id="bottom_solid_layers" value="{bottom_solid_layers_value}" min="1" max="20">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{layer_perimeter_subtitle4}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="extra_perimeters">{extra_perimeters}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="extra_perimeters" id="extra_perimeters" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {extra_perimeters_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="avoid_crossing_perimeters">{avoid_crossing_perimeters}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="avoid_crossing_perimeters" id="avoid_crossing_perimeters" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {avoid_crossing_perimeters_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="slider">Start perimeters at:</label>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="start_perimeters_at_concave_points" style="padding-left: 2em;">{start_perimeters_at_concave_points}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="start_perimeters_at_concave_points" id="start_perimeters_at_concave_points" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {start_perimeters_at_concave_points_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="start_perimeters_at_non_overhang" style="padding-left: 2em;">{start_perimeters_at_non_overhang}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="start_perimeters_at_non_overhang" id="start_perimeters_at_non_overhang" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {start_perimeters_at_non_overhang_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="thin_walls">{thin_walls}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="thin_walls" id="thin_walls" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {thin_walls_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="overhangs">{overhangs}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="overhangs" id="overhangs" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {overhangs_value}>{switch_on}</option>
							</select>
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{layer_perimeter_subtitle5}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="randomize_start">{randomize_start}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="randomize_start" id="randomize_start" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {randomize_start_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="external_perimeters_first">{external_perimeters_first}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="external_perimeters_first" id="external_perimeters_first" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {external_perimeters_first_value}>{switch_on}</option>
							</select>
						</div></div>
					</div>
				</div>
			</div> <!-- layers and perimeters -->
			<div data-role="collapsible">
				<h4>{infill_title}</h4>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{infill_subtitle1}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="fill_density">{fill_density}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="fill_density" id="fill_density" value="{fill_density_value}" min="0" max="100" step="0.01">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="fill_pattern">{fill_pattern}</label>
						</div></div>
						<div class="ui-block-b" style="min-width:200px"><div class="ui-bar ui-bar-f">
							<div data-role="fieldcontain">
								<select name="fill_pattern" id="fill_pattern">
									<option value="rectilinear" {fill_pattern_value1}>{fill_pattern1}</option>
									<option value="line" {fill_pattern_value2}>{fill_pattern2}</option>
									<option value="concentric" {fill_pattern_value3}>{fill_pattern3}</option>
									<option value="honeycomb" {fill_pattern_value4}>{fill_pattern4}</option>
									<option value="hilbertcurve" {fill_pattern_value5}>{fill_pattern5}</option>
									<option value="archimedeanchords" {fill_pattern_value6}>{fill_pattern6}</option>
									<option value="octagramspiral" {fill_pattern_value7}>{fill_pattern7}</option>
								</select>
							</div>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="solid_fill_pattern">{solid_fill_pattern}</label>
						</div></div>
						<div class="ui-block-b" style="min-width:200px"><div class="ui-bar ui-bar-f">
							<div data-role="fieldcontain">
								<select name="solid_fill_pattern" id="solid_fill_pattern">
									<option value="rectilinear" {solid_fill_pattern_value1}>{solid_fill_pattern1}</option>
									<option value="concentric" {solid_fill_pattern_value2}>{solid_fill_pattern2}</option>
									<option value="hilbertcurve" {solid_fill_pattern_value3}>{solid_fill_pattern3}</option>
									<option value="archimedeanchords" {solid_fill_pattern_value4}>{solid_fill_pattern4}</option>
									<option value="octagramspiral" {solid_fill_pattern_value5}>{solid_fill_pattern5}</option>
								</select>
							</div>
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{infill_subtitle2}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="infill_every_layers">{infill_every_layers}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="infill_every_layers" id="infill_every_layers" value="{infill_every_layers_value}" min="0" max="20">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="infill_only_where_needed">{infill_only_where_needed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="infill_only_where_needed" id="infill_only_where_needed" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {infill_only_where_needed_value}>{switch_on}</option>
							</select>
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{infill_subtitle3}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="solid_infill_every_layers">{solid_infill_every_layers}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="solid_infill_every_layers" id="solid_infill_every_layers" value="{solid_infill_every_layers_value}" min="0" max="100">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="fill_angle">{fill_angle}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="fill_angle" id="fill_angle" value="{fill_angle_value}" min="0" max="90">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="solid_infill_below_area">{solid_infill_below_area}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="solid_infill_below_area" id="solid_infill_below_area" value="{solid_infill_below_area_value}" min="0" max="100">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="only_retract_when_crossing_perimeters">{only_retract_when_crossing_perimeters}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="only_retract_when_crossing_perimeters" id="only_retract_when_crossing_perimeters" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {only_retract_when_crossing_perimeters_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="infill_first">{infill_first}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="infill_first" id="infill_first" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {infill_first_value}>{switch_on}</option>
							</select>
						</div></div>
					</div>
				</div>
			</div> <!-- infill -->
			<div data-role="collapsible">
				<h4>{speed_title}</h4>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{speed_subtitle1}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="perimeter_speed">{perimeter_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="perimeter_speed" id="perimeter_speed" value="{perimeter_speed_value}" min="10" max="200">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="small_perimeter_speed">{small_perimeter_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="small_perimeter_speed" id="small_perimeter_speed" value="{small_perimeter_speed_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="external_perimeter_speed">{external_perimeter_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="external_perimeter_speed" id="external_perimeter_speed" value="{external_perimeter_speed_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="infill_speed">{infill_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="infill_speed" id="infill_speed" value="{infill_speed_value}" min="10" max="200">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="solid_infill_speed">{solid_infill_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="solid_infill_speed" id="solid_infill_speed" value="{solid_infill_speed_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="top_solid_infill_speed">{top_solid_infill_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="top_solid_infill_speed" id="top_solid_infill_speed" value="{top_solid_infill_speed_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_speed">{support_material_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_speed" id="support_material_speed" value="{support_material_speed_value}" min="10" max="200">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="bridge_speed">{bridge_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="bridge_speed" id="bridge_speed" value="{bridge_speed_value}" min="10" max="200">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="gap_fill_speed">{gap_fill_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="gap_fill_speed" id="gap_fill_speed" value="{gap_fill_speed_value}" min="10" max="200">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{speed_subtitle2}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="travel_speed">{travel_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="travel_speed" id="travel_speed" value="{travel_speed_value}" min="10" max="300">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{speed_subtitle3}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="first_layer_speed">{first_layer_speed}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="first_layer_speed" id="first_layer_speed" value="{first_layer_speed_value}">
						</div></div>
					</div>
				</div>
			</div> <!-- speed -->
			<div data-role="collapsible">
				<h4>{skirt_brim_title}</h4>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{skirt_brim_subtitle1}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="skirts">{skirts}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="skirts" id="skirts" value="{skirts_value}" min="0" max="10">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="skirt_distance">{skirt_distance}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="skirt_distance" id="skirt_distance" value="{skirt_distance_value}" min="1" max="20">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="skirt_height">{skirt_height}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="skirt_height" id="skirt_height" value="{skirt_height_value}" min="0" max="1000">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="min_skirt_length">{min_skirt_length}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="min_skirt_length" id="min_skirt_length" value="{min_skirt_length_value}" min="0" max="100">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{skirt_brim_subtitle2}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="brim_width">{brim_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="brim_width" id="brim_width" value="{brim_width_value}" min="0" max="20">
						</div></div>
					</div>
				</div>
			</div> <!-- skirt and brim -->
			<div data-role="collapsible">
				<h4>{support_material_title}</h4>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{support_material_subtitle1}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material">{support_material}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="support_material" id="support_material" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {support_material_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_threshold">{support_material_threshold}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_threshold" id="support_material_threshold" value="{support_material_threshold_value}" min="0" max="90">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_enforce_layers">{support_material_enforce_layers}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_enforce_layers" id="support_material_enforce_layers" value="{support_material_enforce_layers_value}" min="0" max="1000">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{support_material_subtitle2}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="raft_layers">{raft_layers}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="raft_layers" id="raft_layers" value="{raft_layers_value}" min="0" max="10">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{support_material_subtitle3}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_pattern">{support_material_pattern}</label>
						</div></div>
						<div class="ui-block-b" style="min-width:200px"><div class="ui-bar ui-bar-f">
							<div data-role="fieldcontain">
								<select name="support_material_pattern" id="support_material_pattern">
									<option value="rectilinear" {support_material_pattern_value1}>{support_material_pattern1}</option>
									<option value="rectilinear-grid" {support_material_pattern_value2}>{support_material_pattern2}</option>
									<option value="honeycomb" {support_material_pattern_value3}>{support_material_pattern3}</option>
								</select>
							</div>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_spacing">{support_material_spacing}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_spacing" id="support_material_spacing" value="{support_material_spacing_value}" min="1" max="10" step="0.01">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_angle">{support_material_angle}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_angle" id="support_material_angle" value="{support_material_angle_value}" min="0" max="90">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_interface_layers">{support_material_interface_layers}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_interface_layers" id="support_material_interface_layers" value="{support_material_interface_layers_value}" min="0" max="10">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_interface_spacing">{support_material_interface_spacing}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_interface_spacing" id="support_material_interface_spacing" value="{support_material_interface_spacing_value}" min="0" max="10">
						</div></div>
					</div>
				</div>
			</div> <!-- support material -->
			<div data-role="collapsible">
				<h4>{mutiple_extruder_title}</h4>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{mutiple_extruder_subtitle1}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="perimeter_extruder">{perimeter_extruder}</label>
						</div></div>
						<div class="ui-block-b" style="min-width:200px"><div class="ui-bar ui-bar-f">
						<!--<input type="number" style="text-align:right;" data-clear-btn="false" name="perimeter_extruder" id="perimeter_extruder" value="{perimeter_extruder_value}">-->
							<select name="perimeter_extruder" id="perimeter_extruder">
								<option value="{extruder_left_val}" {perimeter_extruder_value_left}>{extruder_left}</option>
								<option value="{extruder_right_val}" {perimeter_extruder_value_right}>{extruder_right}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="infill_extruder">{infill_extruder}</label>
						</div></div>
						<div class="ui-block-b" style="min-width:200px"><div class="ui-bar ui-bar-f">
<!-- 							<input type="number" style="text-align:right;" data-clear-btn="false" name="infill_extruder" id="infill_extruder" value="{infill_extruder_value}"> -->
							<select name="infill_extruder" id="infill_extruder">
								<option value="{extruder_left_val}" {infill_extruder_value_left}>{extruder_left}</option>
								<option value="{extruder_right_val}" {infill_extruder_value_right}>{extruder_right}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_extruder">{support_material_extruder}</label>
						</div></div>
						<div class="ui-block-b" style="min-width:200px"><div class="ui-bar ui-bar-f">
<!-- 							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_extruder" id="support_material_extruder" value="{support_material_extruder_value}"> -->
						<select name="support_material_extruder" id="support_material_extruder">
								<option value="{extruder_left_val}" {support_material_extruder_value_left}>{extruder_left}</option>
								<option value="{extruder_right_val}" {support_material_extruder_value_right}>{extruder_right}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_interface_extruder">{support_material_interface_extruder}</label>
						</div></div>
						<div class="ui-block-b" style="min-width:200px"><div class="ui-bar ui-bar-f">
<!-- 							<input type="number" style="text-align:right;" data-clear-btn="false" name="support_material_interface_extruder" id="support_material_interface_extruder" value="{support_material_interface_extruder_value}"> -->
						<select name="support_material_interface_extruder" id="support_material_interface_extruder">
								<option value="{extruder_left_val}" {support_material_interface_extruder_value_left}>{extruder_left}</option>
								<option value="{extruder_right_val}" {support_material_interface_extruder_value_right}>{extruder_right}</option>
							</select>
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{mutiple_extruder_subtitle2}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="ooze_prevention">{ooze_prevention}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<select name="ooze_prevention" id="ooze_prevention" data-role="slider" data-track-theme="a" data-theme="a">
								<option value="0">{switch_off}</option>
								<option value="1" {ooze_prevention_value}>{switch_on}</option>
							</select>
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="standby_temperature_delta">{standby_temperature_delta}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="standby_temperature_delta" id="standby_temperature_delta" value="{standby_temperature_delta_value}" min="-20" max="0">
						</div></div>
					</div>
				</div>
			</div> <!-- multiple extruder -->
			<div data-role="collapsible">
				<h4>{advanced_title}</h4>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{advanced_subtitle1}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="extrusion_width">{extrusion_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="extrusion_width" id="extrusion_width" value="{extrusion_width_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="first_layer_extrusion_width">{first_layer_extrusion_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="first_layer_extrusion_width" id="first_layer_extrusion_width" value="{first_layer_extrusion_width_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="perimeter_extrusion_width">{perimeter_extrusion_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="perimeter_extrusion_width" id="perimeter_extrusion_width" value="{perimeter_extrusion_width_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="infill_extrusion_width">{infill_extrusion_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="infill_extrusion_width" id="infill_extrusion_width" value="{infill_extrusion_width_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="solid_infill_extrusion_width">{solid_infill_extrusion_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="solid_infill_extrusion_width" id="solid_infill_extrusion_width" value="{solid_infill_extrusion_width_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="top_infill_extrusion_width">{top_infill_extrusion_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="top_infill_extrusion_width" id="top_infill_extrusion_width" value="{top_infill_extrusion_width_value}">
						</div></div>
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="support_material_extrusion_width">{support_material_extrusion_width}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="text" style="text-align:right;" data-clear-btn="false" name="support_material_extrusion_width" id="support_material_extrusion_width" value="{support_material_extrusion_width_value}">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{advanced_subtitle2}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="bridge_flow_ratio">{bridge_flow_ratio}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="bridge_flow_ratio" id="bridge_flow_ratio" value="{bridge_flow_ratio_value}" min="0.75" max="1.5" step="0.01">
						</div></div>
					</div>
				</div>
				<div data-role="collapsible" data-collapsed="false" data-theme="d">
					<h4>{advanced_subtitle3}</h4>
					<div class="ui-grid-a">
						<div class="ui-block-a"><div class="ui-bar ui-bar-f">
							<label for="resolution">{resolution}</label>
						</div></div>
						<div class="ui-block-b"><div class="ui-bar ui-bar-f">
							<input type="number" style="text-align:right;" data-clear-btn="false" name="resolution" id="resolution" value="{resolution_value}" min="0" max="1" step="0.01">
						</div></div>
					</div>
				</div>
			</div> <!-- advanced -->
			<div id="save_as_container" style="{hide_save_as}">
				<h3><label for="save_as">{save_as_title}</label></h3>
				<input type="text" data-clear-btn="true" name="save_as" id="save_as">
			</div>
			<div id="submit_container" style="{hide_submit}"><input type="submit" value="{submit_button}" data-ajax="false"></div>
			</form>
			<div>{error}</div>
		</div>
	</div>
</div>

<div data-role="page" data-url="/user/info" id="page_user_info">
	<div id="overlay"></div>
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
		<a href="/" data-icon="home" data-ajax="false">{home}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<form method="POST" data-ajax="false">
				<p>{msg_head_hint}</p>
				<label for="user_country">{title_location}</label>
				<select name="user_country" id="user_country" data-oldvalue="{value_country}">
					<option value="">{hint_country}</option>
				</select>
				<select name="user_city" id="user_city" data-oldvalue="{value_city}">
					<option value="">{hint_city}</option>
				</select>
				<div class="ui-field-contain">
					<label for="user_birth">{title_birth}</label>
					<input type="date" name="user_birth" id="user_birth" data-clear-btn="true" value="{value_birth}" />
				</div>
				<label for="user_why">{label_why}</label>
				<textarea cols="40" rows="8" name="user_why" id="user_why" placeholder="{hint_why}">{value_why}</textarea>
				<label for="user_what">{label_what}</label>
				<textarea cols="40" rows="8" name="user_what" id="user_what" placeholder="{hint_what}">{value_what}</textarea>
				<input type="submit" value="{button_confirm}" />
			</form>
			<div class="zim-error">{msg_error}</div>
		</div>
	</div>

<script>
var var_location_list = null;

// function sortArrayByName(a, b){
// 	var aName = a.name.toLowerCase();
// 	var bName = b.name.toLowerCase(); 
// 	return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
// }

function onCountryChanged() {
	if (var_location_list != null) {
		var var_disable_city = ($("select#user_country").val().length == 0);
		
		$("select#user_city").textinput({disabled: var_disable_city});
		$("select#user_city").empty();
		$("select#user_city").append(new Option("{hint_city}", ""));
		if (var_disable_city == false) {
			var var_city_list = null;
			var var_country_value = $("select#user_country").data("oldvalue");
			var var_country_current = $("select#user_country").val();
			var var_city_value = $("select#user_city").data("oldvalue");
			
			$.each(var_location_list, function(var_countryName, var_cityList) {
				if (var_countryName == $("select#user_country").val()) {
					var_city_list = var_cityList.sort();
					return false;
				}
			});
			$.each(var_city_list, function(var_i, var_cityName) {
				if (var_cityName.length > 0) {
					var var_selected = (var_country_value == var_country_current && var_cityName == var_city_value);
					
					$("select#user_city").append(new Option(var_cityName, var_cityName, var_selected));
					if (var_selected == true) {
						$("select#user_city").val(var_cityName);
					}
				}
			});
		}
		$("select#user_city").trigger("change");
	}
	else {
		console.log("error case");
		load_locationList();
	}
	
	return;
}

function load_locationList() {
	$.ajax({
		url: "/assets/countriesToCities.json",
		cache: true,
		dataType: "json",
		beforeSend: function() {
			$("#overlay").addClass("gray-overlay");
			$(".ui-loader").css("display", "block");
		},
		complete: function() {
			$("#overlay").removeClass("gray-overlay");
			$(".ui-loader").css("display", "none");
		},
		success: function(data) {
			var var_country_list = [];
			var var_country_value = $("select#user_country").data("oldvalue");
			
			var_location_list = data;
			
			// add country into list
			$.each(var_location_list, function(var_country, var_cityList) {
				if (var_country.length > 0) {
					var_country_list.push(var_country);
				}
// 				$.each(var_cityList, function(var_i, var_cityName) {
// 					;
// 				});
			});
			var_country_list.sort();
			$.each(var_country_list, function(var_i, var_countryName) {
				var var_selected = (var_country_value == var_countryName);
				
				$("select#user_country").append(new Option(var_countryName, var_countryName, var_selected));
				if (var_selected == true) {
					$("select#user_country").val(var_countryName);
				}
			});
			
			$("select#user_country").change(onCountryChanged);
			$("select#user_city").change(function() {
				$("#user_city").parent().children("span").removeClass();
			});
			$("select#user_country").trigger("change");
		},
		error: function() {
			var_location_list = null;
		},
	});
	
	return;
}

$("#page_user_info").on("pagecreate",function() {
	$("select#user_city").textinput({disabled: true});
	load_locationList();
});
</script>
</div>
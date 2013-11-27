<div id="container">
	<form action="/test" method="post">
		<label for="gcode">G-code:</label> <input name="gcode" id="gcode"
			value="" type="text"> <label for="textarea1">Arduino response:</label>
		<textarea cols="40" rows="8" name="textarea1" id="textarea1"><?= $response ?></textarea>
		<input value="Send G-code" type="submit">
	</form>
</div>
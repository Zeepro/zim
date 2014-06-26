<div data-role="page" data-url="/setupcartridge/input">
	<header data-role="header" class="page-header">
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<div data-role="collapsible" data-collapsed="false">
				<h4>Please input the information blow 请输入以下信息</h4>
				<form method="post" action="/setupcartridge/wait" data-ajax="false">
					<div data-role="fieldcontain">
						<div>UTC Date UTC日期</div>
						<label for="year">Year 年</label>
						<input type="number" id="year" name="year" value="">
						<label for="month">Month 月</label>
						<input type="number" id="month" name="month" value="">
						<label for="day">Day 日</label>
						<input type="number" id="day" name="day" value="">
					</div>
					<div data-role="fieldcontain">
						<label for="type">Filament type 打印丝种类</label>
						<select name="type" id="type">
							{types}
							<option value="{code}">{name}</option>
							{/types}
						</select>
						<label for="times">Number of tags to be printed 标签写入次数</label>
						<input type="number" id="times" name="times" value="1">
					</div>
					<a href="#" onclick="javascript: autoInput();" data-role="button">auto input 自动填写</a>
					<input type="submit" value="ok 确认">
				</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).on('pageinit', autoInput());

function autoInput() {
	var var_today = new Date();
	$("#year").val(var_today.getUTCFullYear());
	$("#month").val(var_today.getUTCMonth() + 1);
	$("#day").val(var_today.getUTCDate());
}
</script>

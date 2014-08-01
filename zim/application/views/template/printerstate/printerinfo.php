<style type="text/css">
	.ui-table-columntoggle-btn
	{
    	display: none !important;
	}
</style>

<div data-role="page" data-url="/printerstate/printerinfo">
	<header data-role="header" class="page-header">
		<a href="javascript:history.back();" data-icon="back" data-ajax="false">{back}</a>
	</header>
	<div class="logo"><div id="link_logo"></div></div>
	<div data-role="content">
		<div id="container">
			<table data-role="table" data-mode="columntoggle" id="test-table" class="ui-shadow table-stroke" style="background-color:#e7e7e7">
  				<tbody>
  					{array_info}
    				<tr>
      					<th>{title}</th>
      					<td>{value}</td>
    				</tr>
   					{/array_info}
  				</tbody>
			</table>
			<a href="/activation" data-role="button">{button_sso}</a>
		</div>
	</div>
</div>

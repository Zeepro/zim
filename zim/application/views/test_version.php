<div data-role="page" data-url="/test_version">
	<style type="text/css">
	.ui-table-columntoggle-btn
	{
    	display: none !important;
	}
	</style>
	<header data-role="header" class="page-header"></header>
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
		</div>
	</div>
</div>

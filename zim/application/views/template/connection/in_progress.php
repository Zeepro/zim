<div data-role="page" data-url="/connection/in_progress">
     <header data-role="header" class="page-header"></header>
     <div data-role="logo"><div id="link_logo"></div></div>
     <div data-role="content">
     	<div id="container" text-align="center">
	       {config_printer}
	</div>
     </div>
     <script>
	setTimeout(function()
	{
	   var interval;

	   interval = setInterval(function()
	   {
	      $.ajax(
	      {
		url: "http://{hostname}.local",
	      	type: "GET",
	      	statusCode:
	      	{
		   200: function()
		   {
		      clearInterval(interval);
		   }
	      	}
	      });
	   }, 1000);
	}, 30000);
     </script>
</div>
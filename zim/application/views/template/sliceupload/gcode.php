<div data-role="page" data-url="/sliceupload/gcode">
    <link rel="stylesheet" type="text/css" href="/assets/gcode/css/cupertino/jquery-ui-1.9.0.custom.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/assets/gcode/lib/codemirror.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/assets/gcode/css/style.css" media="screen" />
<!--     <script type="text/javascript" src="/assets/gcode/lib/jquery-1.8.2.min.js"></script> -->
    <script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script> <!-- pass this file into local folder -->
    <script type="text/javascript" src="/assets/gcode/lib/jquery-ui-1.9.0.custom.js"></script>

    <script type="text/javascript" src="/assets/gcode/lib/codemirror.js"></script>
    <script type="text/javascript" src="/assets/gcode/lib/mode_gcode/gcode_mode.js"></script>
<!--     <script type="text/javascript" src="/assets/gcode/lib/three.js"></script> -->
    <script type="text/javascript" src="/assets/gcode/lib/bootstrap.js"></script>
    <script type="text/javascript" src="/assets/gcode/lib/modernizr.custom.09684.js"></script>
<!--     <script type="text/javascript" src="/assets/gcode/lib/TrackballControls.js"></script> -->
    <script type="text/javascript" src="/assets/gcode/lib/zlib.min.js"></script>
    <script type="text/javascript" src="/assets/gcode/js/ui.js"></script>
    <script type="text/javascript" src="/assets/gcode/js/gCodeReader.js"></script>
    <script type="text/javascript" src="/assets/gcode/js/renderer.js"></script>
    <script type="text/javascript" src="/assets/gcode/js/analyzer.js"></script>
<!--     <script type="text/javascript" src="/assets/gcode/js/renderer3d.js"></script> -->
    
    <style> #slider-horizontal { position: relative !important; } </style>
    
	<header data-role="header" class="page-header"></header>
	<div class="logo"><div id="link_logo"></div></div>
		<div data-role="content">
			<div id="container">
				<div id="gc_analyser_container" style="margin: 0 auto; width: 700px;">
				<button id="gc_analyser_view_changer" onclick="javascript: $('#rendering').toggle(); $('#gCodeContainer').toggle();">Change view</button>
					<div id="rendering" style="display: none;">
						<canvas id="canvas" width="650" height="620"></canvas>
	                    <div id="slider-vertical"></div>
	                    <div id="slider-horizontal"></div>
					</div>
					<div id="gCodeContainer"></div>
				</div>
			</div>
		</div>
	</div>
	<div id="errorList"></div>
<script>
    GCODE.ui.initHandlers();
    $(document).ready(function()
   	{
    	$.get("http://localhost:81/images/test.gcode").done(function(data)
    	{
    		console.log("done");
    		GCODE.ui.functest(data);
    	});
   	})
</script>
</div>
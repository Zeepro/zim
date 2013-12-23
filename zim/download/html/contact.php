<?php include_once('assets/contact/assets/config.php'); ?>
<?php include_once('assets/contact/assets/language.php'); ?>
<!DOCTYPE html> 
<html> 

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0, initial-scale=1.0">   
	<title>emdot - Mobile Website Theme</title>
    
	<!-- scripts -->
	<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script> 
    <script src="/assets/contact/js/contact.js"></script>
    
    
    <!-- css / theme -->
   	<link rel="stylesheet" href="/assets/css/1.css"><!-- change the number of the css file to switch themes -->
    <link rel="stylesheet" href="/assets/css/style.css">

</head> 

<body><div data-role="page" id="main">
	
    <!-- header -->
	<div data-role="header" class="page-header">
    	<a  data-icon="arrow-l" data-iconpos="left" data-direction="reverse"  rel="external" data-rel="back" href="m.yourdomain.com">Back</a>
		<h1>Contact</h1>
        <a href="index.html" data-icon="home" data-iconpos="notext" data-direction="reverse"  rel="external" >Home</a>
	</div>
    <!--/header -->
    
    
	<div data-role="content">
    
    <!-- banner image -->
    <div class="box contact-page"></div><!-- change these images in the style.css file -->
    <img src="/assets/images/shadow.png" class="shadow" alt="shadow">
    <!-- /banner image -->
    
    <p>Fill out the form to contact us today!</p></p>
    
    
		<div id="errors" class="hide ui-corner-all"></div>
		<p align="right">* <?php echo $language->field_required?></p>
		<form name="form1" id="form1">
			<div data-role="fieldcontain">
				<label for="name"><?php echo $language->name?> *</label>
				<input type="text" name="name" id="name" autofocus />
			</div>
			<div data-role="fieldcontain">
				<label for="lastname"><?php echo $language->lastname?> *</label>
				<input type="text" name="lastname" id="lastname" />
			</div>
			<div data-role="fieldcontain">
				<label for="email"><?php echo $language->email?> *</label>
				<input type="text" name="email" id="email" />
			</div>
            <div data-role="fieldcontain">
				<label for="phone"><?php echo $language->phone?> *</label>
				<input type="text" name="phone" id="phone" />
			</div>
			<div data-role="fieldcontain">
				<label for="subject"><?php echo $language->subject?> *</label>
				<input type="text" name="subject" id="subject" />
			</div>
			<div data-role="fieldcontain">
				<label for="message"><?php echo $language->message?> *</label>
				<textarea name="message" id="message" rows="8" cols="25"></textarea>
			</div>
            <?php if($mailing_list) : ?>
			<div data-role="fieldcontain">
				<fieldset data-role="controlgroup" data-type="horizontal">
					<legend><?php echo $language->join_mailing_list?></legend>
					<input type="radio" name="mailing_list" id="mailing_list-1" value="yes" checked="checked" />
					<label for="mailing_list-1"><?php echo $language->yes_please?></label>
					<input type="radio" name="mailing_list" id="mailing_list-0" value="no" />
					<label for="mailing_list-0"><?php echo $language->no_thanks?></label>
				</fieldset>
			</div>
            <?php endif ?>
            <?php if($how_you_found_us) : ?>
			<div data-role="fieldcontain">
				<label for="how_you_found_us"><?php echo $language->how_did_you_find_us?></label>
				<select id="how_you_found_us" name="how_you_found_us">
					<?php	if(is_array($how_you_found_us_options)) : 
								foreach($how_you_found_us_options as $value) :
									echo '<option value="'.$value.'">'.$value.'</option>';
								endforeach;
							endif
					?>
				</select>
			</div>
            <?php endif ?>
			<div data-role="fieldcontain">
				<label for="captcha"><?php echo $language->are_you_human?> *</label>
				<span id="are_you_human"></span><input type="text" name="captcha" id="captcha" />
			</div>
			<div data-role="fieldcontain">
				<fieldset data-role="controlgroup">
					<label for="copy"><?php echo $language->send_yourself?></label>
					<input name="copy" type="checkbox" id="copy" value="y" />
				</fieldset>
			</div>
			<fieldset class="ui-grid-a">
				<div class="ui-block-a">
					<button type="reset" data-theme="d"><?php echo $language->cancel?></button>
				</div>
				<div class="ui-block-b">
					<button type="submit" id="sbm" data-theme="a"><?php echo $language->submit?></button>
				</div>
			</fieldset>
		</form>
	</div>
    
    <!-- footer -->
	<div data-role="footer">
		<div data-role="navbar" class="custom-icons" data-grid="c"  data-position="fixed">
			<ul>
				<li><a href="http://bit.ly/yx2mXZ" id="map-marker" data-icon="custom">Directions</a></li>
				<li><a href="contact.php" rel="external" id="mail" data-icon="custom">Contact</a></li>
				<li><a href="portfolio.html" rel="external" id="camera" data-icon="custom">Portfolio</a></li>
				<li><a href="icons.html" id="star" data-icon="custom">Icons!</a></li>
			</ul>
		</div>
	</div>
	<!-- /footer -->
    
</div>





<!-- after submit -->
<div data-role="page" id="done">
	
    <!-- header -->
	<div data-role="header" class="page-header"	>
    
		<a class="ui-btn-left ui-btn ui-btn-icon-left ui-btn-corner-all ui-shadow ui-btn-up-a" data-icon="arrow-l" data-direction="reverse" data-rel="back" href="m.yourdomain.com" data-theme="a">
			<span class="ui-btn-inner ui-btn-corner-all">Back</span>
			<span class="ui-icon ui-icon-arrow-l ui-icon-shadow"></span>
		</a>
		
        <h1>Success!</h1>
		
        <a href="index.html" data-icon="home" data-iconpos="notext" data-direction="reverse"  rel="external" >Home</a>

	</div>
    <!--/header -->
	
	<div data-role="content">
    
    <!-- banner image -->
    <div class="box contact-page"></div><!-- change these images in the style.css file -->
    <img src="/assets/images/shadow.png" class="shadow" alt="shadow">
    <!-- /banner image -->
		
        <h4>Your email has been sent successfully!</h4>
        <p>We will be in touch soon.</p>
        
	</div>
    
    <!-- footer -->
	<div data-role="footer">
		<div data-role="navbar" class="custom-icons" data-grid="c"  data-position="fixed">
			<ul>
				<li><a href="http://bit.ly/yx2mXZ" id="map-marker" data-icon="custom">Directions</a></li>
				<li><a href="contact.php" id="mail" data-icon="custom" rel="external">Contact</a></li>
				<li><a href="portfolio.html" rel="external"  id="camera" data-icon="custom">Portfolio</a></li>
				<li><a href="icons.html" id="star" data-icon="custom">Icons!</a></li>
			</ul>
		</div>
	</div>
	<!-- /footer -->
    
    
</div>
<!-- /after submit -->

</body>
</html>
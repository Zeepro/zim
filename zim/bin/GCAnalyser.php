<?php
// Version: 1.0

define('RC_ERROR_OK',			0);
define('RC_ERROR_NO_PRM',		1);
define('RC_ERROR_NO_FILENAME',	2);
define('RC_ERROR_NO_FILE',		3);

$parameter = '';
$filepath = '';
$array_line = array();

// treat input data
if (empty($_GET) && !empty($argv)) {
	$parameter = $argv[1];
	$filepath = $argv[2];
}
else if (!empty($_GET)) {
	$parameter = $_GET['p'];
	$filepath = $_GET['v'];
}
else {
	exit(RC_ERROR_NO_PRM);
}

// check filename
if (empty($filepath)) {
	exit(RC_ERROR_NO_FILENAME);
}
else if (!file_exists($filepath)) {
	exit(RC_ERROR_NO_FILE);
}
else {
	// main function
	// read file into array
	$array_line = file($filepath);
	
	foreach($array_line as $line) {
		$line = trim(" \t\n\r\0\x0B", $line);
		
		// do not count comment and empty line
		if (empty($line) || strpos($line, ';')) {
			
		}
	}
}

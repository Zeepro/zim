<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->helper(array('detectos', 'errorcode'));

if (!defined('SLICER_URL_ADD_MODEL')) {
	define('SLICER_URL_ADD_MODEL',		'add?file=');
	define('SLICER_URL_GET_MODELFILE',	'getmodel?id=');
	define('SLICER_URL_RELOAD_PRESET',	'reload');
	define('SLICER_URL_LISTMODEL',		'listmodel');
	define('SLICER_URL_REMOVE_MODEL',	'removemodel?id=');
	
	define('SLICER_RESPONSE_OK',		200);
	define('SLICER_RESPONSE_MISS_PRM',	432);
	define('SLICER_RESPONSE_ADD_ERROR',	433);
	define('SLICER_RESPONSE_WRONG_PRM',	433);
	
	define('SLICER_FILENAME_ZIPMODEL',	'_model_slicer.zip');
}

function Slicer_addModel($model_path) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_ADD_MODEL . $model_path, $response);
	
	switch ($ret_val) {
		case SLICER_RESPONSE_OK:
			$cr = ERROR_OK;
			break;
			
		case SLICER_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case SLICER_RESPONSE_ADD_ERROR:
			$cr = ERROR_WRONG_FORMAT;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			break;
	}
// 	var_dump($http_response_header);
// 	var_dump($response);
	
	return $cr;
}

function Slicer_removeModel($model_id) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_REMOVE_MODEL . $model_id, $response);
	
	switch ($ret_val) {
		case SLICER_RESPONSE_OK:
			$cr = ERROR_OK;
			break;
			
		case SLICER_RESPONSE_MISS_PRM:
			$cr = ERROR_MISS_PRM;
			break;
			
		case SLICER_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			break;
	}
	
	return $cr;
}

function Slicer_getModelFile($model_id) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_GET_MODELFILE . $model_id, $response);
	
	switch ($ret_val) {
		case SLICER_RESPONSE_OK:
			if (file_exists($response)) {
				//TODO zip it and return file to user
			}
			else {
				$cr = ERROR_INTERNAL;
			}
			break;
			
		case SLICER_RESPONSE_WRONG_PRM:
			$cr = ERROR_WRONG_PRM;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			break;
	}
	
	return $cr;
}

function Slicer_slice() {
	
}

function Slicer_listmodel(&$response) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_LISTMODEL, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		$cr = ERROR_OK;
	}
	else {
		$cr = ERROR_INTERNAL;
		$response = "[]";
	}
	
	return $cr;
}

function Slicer_reset() {
	
}

function Slicer_reloadPreset() {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_RELOAD_PRESET, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		$cr = ERROR_OK;
	}
	else {
		$cr = ERROR_INTERNAL;
	}
	
	return $cr;
}

//internal function
function Slicer__getHTTPCode($http_response_header) {
	$matches = array();
	preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);
	return (int)$matches[1];
}

function Slicer__requestSlicer($suffix_url, &$response) {
	global $CFG;
	$context = stream_context_create(
			array('http' => array('ignore_errors' => TRUE))
	);
	$url = $CFG['slicer_url'] . $suffix_url;
	$response = file_get_contents($url, FALSE, $context);
	
	return Slicer__getHTTPCode($http_response_header);
}

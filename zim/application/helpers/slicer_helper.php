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
	define('SLICER_URL_SET_MODEL',		'setmodel?');
	define('SLICER_URL_SLICE',			'slice');
	define('SLICER_URL_SLICE_STATUS',	'slicestatus');
	define('SLICER_URL_SLICE_HALT',		'slicehalt');
	
	define('SLICER_PRM_ID',		'id');
	define('SLICER_PRM_XPOS',	'xpos');
	define('SLICER_PRM_YPOS',	'ypos');
	define('SLICER_PRM_ZPOS',	'zpos');
	define('SLICER_PRM_XROT',	'xrot');
	define('SLICER_PRM_YROT',	'yrot');
	define('SLICER_PRM_ZROT',	'zrot');
	define('SLICER_PRM_SCALE',	's');
	define('SLICER_PRM_COLOR',	'c');
	
	define('SLICER_TITLE_COLOR',	'color');
	define('SLICER_FILE_MODEL',		'/tmp/_sliced_model.gcode');
	
	define('SLICER_OFFSET_VALUE_COLOR2EXTRUDER',	-1);
	
	define('SLICER_RESPONSE_OK',		200);
	define('SLICER_RESPONSE_MISS_PRM',	432);
	define('SLICER_RESPONSE_ADD_ERROR',	433);
	define('SLICER_RESPONSE_WRONG_PRM',	433);
	define('SLICER_RESPONSE_ERROR',		499);
	
	define('SLICER_FILENAME_ZIPMODEL',	'_model_slicer.zip');
}

function Slicer_addModel($model_path) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_ADD_MODEL . $model_path);
	
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
			
		case 404:
			$cr = 404;
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
	$ret_val = Slicer__requestSlicer(SLICER_URL_REMOVE_MODEL . $model_id);
	
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
			
		case 404:
			$cr = 404;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			break;
	}
	
	return $cr;
}

function Slicer_getModelFile($model_id, &$path_model) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_GET_MODELFILE . $model_id, $response);
	
	switch ($ret_val) {
		case SLICER_RESPONSE_OK:
			if (file_exists($response)) {
				//TODO zip it and return file to user or compress it by lighttpd/php via gzip
				$path_model = $response;
				$cr = ERROR_OK;
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
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_SLICE, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		if (CoreStatus_setInSlicing()) {
			$cr = ERROR_OK;
		}
		else {
			$cr = ERROR_INTERNAL;
		}
	}
	else {
		$cr = ERROR_INTERNAL;
	}
	
	return $cr;
}

function Slicer_sliceHalt() {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_SLICE_HALT, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		$cr = ERROR_OK;
	}
	else {
		$cr = ERROR_NO_PRINT;
	}
	
	return $cr;
}

function Slicer_checkSlice(&$progress, &$array_extruder = array()) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_SLICE_STATUS, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		if ((int)$response < 0) {
			$cr = ERROR_NO_PRINT;
			$progress = -1;
		}
		else {
			$cr = ERROR_OK;
			$progress = (int)$response;
			if ($progress == 100) {
				$CI = &get_instance();
				$CI->load->helper('printerstate');
				
				$explode_array = explode("\n", $response);
				if (isset($explode_array[1])) {
					$explode_array = explode(';', $explode_array[1]);
					foreach ($explode_array as $key_value) {
						$tmp_array = explode(':', $key_value);
						$abb_filament = PrinterState_cartridgeNumber2Abbreviate((int)$tmp_array[0]);
						$array_extruder[$abb_filament] = ceil($tmp_array[1]);
					}
				}
				else {
					$cr = ERROR_INTERNAL;
				}
			}
		}
	}
	else if ($ret_val == SLICER_RESPONSE_ERROR) {
		if (strpos($response, "InitalError" !== FALSE)) {
			$cr = ERROR_WRONG_PRM;
		}
		else if (strpos($response, "ExportError" !== FALSE)) {
			$cr = ERROR_UNKNOWN_MODEL;
		}
		else {
			$cr = ERROR_INTERNAL;
		}
		$progress = -1;
		
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('slicer error: ' . $response);
	}
	else {
		$cr = ERROR_INTERNAL;
	}
	
	return $cr;
}

function Slicer_listModel(&$response) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_LISTMODEL, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		$cr = ERROR_OK;
	}
	else {
		$cr = $ret_val;
		$response = "[]";
	}
	
	return $cr;
}

function Slicer_checkPlatformColor() {
	$cr = 0;
	$array_platform = array();
	$array_color = array();
	
	Slicer_listModel($array_model);
	$array_platform = json_decode($array_model, TRUE);
	if (is_null($array_platform)) {
		$cr = ERROR_EMPTY_PLATFORM;
	}
	else {
		$CI = &get_instance();
		$CI->load->helper('printerstate');
		$cr = ERROR_OK;
		// get the extruder which we need
		foreach ($array_platform as $model) {
			$colors = $model[SLICER_TITLE_COLOR];
			foreach ($colors as $color) {
				$array_color[] = (int)$color + SLICER_OFFSET_VALUE_COLOR2EXTRUDER;
			}
		}
		$array_color = array_unique($array_color);
		foreach ($array_color as $number_color) {
			$abb_cartridge = PrinterState_cartridgeNumber2Abbreviate($number_color);
			if (PrinterState_getFilamentStatus($abb_cartridge)) {
				continue;
			}
			else if ($abb_cartridge == 'l') {
				$cr = ERROR_MISS_LEFT_FILA;
				break;
			}
			else {
				$cr = ERROR_MISS_RIGT_FILA;
				break;
			}
		}
	}
	
	return $cr;
}

function Slicer_setModel($array_data) {
	$cr = 0;
	$ret_val = 0;
	$url_request = SLICER_URL_SET_MODEL;
	
	if (!is_array($array_data)) {
		return ERROR_INTERNAL;
	}
	else if (!isset($array_data[SLICER_PRM_ID])
			|| !isset($array_data[SLICER_PRM_XPOS])
			|| !isset($array_data[SLICER_PRM_YPOS])
			|| !isset($array_data[SLICER_PRM_ZPOS])
			|| !isset($array_data[SLICER_PRM_XROT])
			|| !isset($array_data[SLICER_PRM_YROT])
			|| !isset($array_data[SLICER_PRM_ZROT])
			|| !isset($array_data[SLICER_PRM_SCALE])
			|| !isset($array_data[SLICER_PRM_COLOR])) {
		return ERROR_MISS_PRM;
	}
	
	$url_request .= SLICER_PRM_ID . '=' . $array_data[SLICER_PRM_ID]
			. '&' . SLICER_PRM_XPOS . '=' . $array_data[SLICER_PRM_XPOS]
			. '&' . SLICER_PRM_YPOS . '=' . $array_data[SLICER_PRM_YPOS]
			. '&' . SLICER_PRM_ZPOS . '=' . $array_data[SLICER_PRM_ZPOS]
			. '&' . SLICER_PRM_XROT . '=' . $array_data[SLICER_PRM_XROT]
			. '&' . SLICER_PRM_YROT . '=' . $array_data[SLICER_PRM_YROT]
			. '&' . SLICER_PRM_ZROT . '=' . $array_data[SLICER_PRM_ZROT]
			. '&' . SLICER_PRM_SCALE . '=' . $array_data[SLICER_PRM_SCALE]
			. '&' . SLICER_PRM_COLOR . '=' . $array_data[SLICER_PRM_COLOR];
	$ret_val = Slicer__requestSlicer($url_request, $response);
	
	switch ($ret_val) {
		case SLICER_RESPONSE_OK:
		case SLICER_RESPONSE_WRONG_PRM:
		case SLICER_RESPONSE_MISS_PRM:
			$cr = $ret_val;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			break;
	}
	
	return $cr;
}

function Slicer_reset() {
	
}

function Slicer_reloadPreset() {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_RELOAD_PRESET);
	
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

function Slicer__requestSlicer($suffix_url, &$response = NULL) {
	global $CFG;
	$context = stream_context_create(
			array('http' => array('ignore_errors' => TRUE, 'timeout' => 3))
	);
	$url = $CFG->config['slicer_url'] . $suffix_url;
	$response = @file_get_contents($url, FALSE, $context);
	
	if ($response === FALSE || is_null($http_response_header)) {
		return 404;
	}
	
	return Slicer__getHTTPCode($http_response_header);
}

<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

$CI = &get_instance();
$CI->load->helper(array('detectos', 'errorcode'));

if (!defined('SLICER_URL_ADD_MODEL')) {
	define('SLICER_URL_ADD_MODEL',		'add?file=');
	define('SLICER_URL_ADD_MODEL_ADV',	'add?noresize&file=');
	define('SLICER_URL_GET_MODELFILE',	'getmodel?id=');
	define('SLICER_URL_RELOAD_PRESET',	'reload');
	define('SLICER_URL_LISTMODEL',		'listmodel');
	define('SLICER_URL_REMOVE_MODEL',	'removemodel?id=');
	define('SLICER_URL_SET_MODEL',		'setmodel?');
	define('SLICER_URL_SLICE',			'slice');
	define('SLICER_URL_SLICE_STATUS',	'slicestatus');
	define('SLICER_URL_SLICE_HALT',		'slicehalt');
	define('SLICER_URL_RENDERING',		'preview?');
	define('SLICER_URL_ADD_STATUS',		'addstatus');
	define('SLICER_URL_SETPARAMETER',	'setparameter?');
	
	define('SLICER_PRM_ID',		'id');
	define('SLICER_PRM_XPOS',	'xpos');
	define('SLICER_PRM_YPOS',	'ypos');
	define('SLICER_PRM_ZPOS',	'zpos');
	define('SLICER_PRM_XROT',	'xrot');
	define('SLICER_PRM_YROT',	'yrot');
	define('SLICER_PRM_ZROT',	'zrot');
	define('SLICER_PRM_SCALE',	's');
	define('SLICER_PRM_COLOR',	'c');
	define('SLICER_PRM_RHO',	'rho');
	define('SLICER_PRM_THETA',	'theta');
	define('SLICER_PRM_DELTA',	'delta');
	define('SLICER_PRM_COLOR1',	'color1');
	define('SLICER_PRM_COLOR2',	'color2');
	define('SLICER_PRM_SDCARD',	'sdcard');
	
	define('SLICER_PRM_PRM',	'slicerparameter');
	
	define('SLICER_TITLE_COLOR',	'color');
	define('SLICER_TITLE_MAXSCALE',	'scalemax');
	define('SLICER_TITLE_MODELID',	'id');
	define('SLICER_TITLE_XSIZE',	'xsize');
	define('SLICER_TITLE_YSIZE',	'ysize');
	define('SLICER_TITLE_ZSIZE',	'zsize');
	
	define('SLICER_FILE_MODEL',		'_sliced_model.gcode');
// 	define('SLICER_FILE_RENDERING',	'preview.png');
	define('SLICER_FILE_TEMP_DATA',	'_sliced_info.json');
	define('SLICER_FILE_HTTP_PORT',	'Slic3rPort.txt');
	
	define('SLICER_FILE_SLICELOG',	'/var/log/slic3r');
	
	define('SLICER_OFFSET_VALUE_COLOR2EXTRUDER',	-1);
	
	define('SLICER_RESPONSE_OK',		200);
	define('SLICER_RESPONSE_MISS_PRM',	432);
	define('SLICER_RESPONSE_ADD_ERROR',	433);
	define('SLICER_RESPONSE_WRONG_PRM',	433);
	define('SLICER_RESPONSE_ERROR',		499);
	define('SLICER_RESPONSE_NO_MODEL',	453);
	
	define('SLICER_TIMEOUT_WITHLIMIT',	5);
	define('SLICER_TIMEOUT_NOLIMIT',	300);
	
	define('SLICER_VALUE_DEFAULT_TEMPER',		230);
	define('SLICER_VALUE_DEFAULT_FIRST_TEMPER',	235);
	
	define('SLICER_CMD_SLICER_PS_STATUS',	'ps -A | grep slic3r.bin');
	define('SLICER_CMD_RESTART_SLICER',		'sudo /etc/init.d/zeepro-slic3r restart &');
	define('SLICER_CMD_PRM_PREVIEW_FILE',	'preview.png');
	
// 	define('SLICER_FILENAME_ZIPMODEL',	'_model_slicer.zip');
}

function Slicer_addModel($models_path, $auto_resize = TRUE, &$array_return = array()) {
	$cr = 0;
	$CI = &get_instance();
	$ret_val = 0;
	
	if (!is_array($models_path)) {
		$CI->load->helper('printerlog');
		PrinterLog_logDebug("add slicer model api error");
		return ERROR_INTERNAL;
	}
	
	if ($auto_resize == TRUE) {
		$ret_val = Slicer__requestSlicer(SLICER_URL_ADD_MODEL . json_encode($models_path), FALSE);
	}
	else {
		$response = NULL;
		$tmp_array = array();
		
		$ret_val = Slicer__requestSlicer(SLICER_URL_ADD_MODEL_ADV . json_encode($models_path), FALSE, $response);
		$tmp_array = json_decode($response, TRUE);
		if ($tmp_array != NULL && is_array($tmp_array)) {
			$array_return = $tmp_array;
		}
		else {
			$ret_val = ERROR_INTERNAL;
			$CI->load->helper('printerlog');
			PrinterLog_logError("add slicer model api error", __FILE__, __LINE__);
		}
	}
	
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
	@unlink($CI->config->item('temp') . SLICER_FILE_MODEL);
	@unlink($CI->config->item('temp') . SLICER_FILE_TEMP_DATA);
	
	return $cr;
}

function Slicer_removeModel($model_id) {
	$cr = 0;
	$CI = &get_instance();
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
	
	@unlink($CI->config->item('temp') . SLICER_FILE_MODEL);
	@unlink($CI->config->item('temp') . SLICER_FILE_TEMP_DATA);
	
	return $cr;
}

function Slicer_getModelFile($model_id, &$path_model) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_GET_MODELFILE . $model_id, TRUE, $response);
	
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
	$ret_val = Slicer__requestSlicer(SLICER_URL_SLICE);
	
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
	$ret_val = Slicer__requestSlicer(SLICER_URL_SLICE_HALT);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		$CI = &get_instance();
		$CI->load->helper('corestatus');
		
		CoreStatus_setInIdle();
		
		$cr = ERROR_OK;
		Slicer_restart(); //FIXME remove me as soon as possible
	}
	else {
		$cr = ERROR_NO_SLICING;
	}
	
	return $cr;
}

function Slicer_checkSlice(&$progress, &$array_extruder = array()) {
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_SLICE_STATUS, TRUE, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		if ((int)$response < 0) {
			$cr = ERROR_NO_SLICING;
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
		if (strpos($response, "InitalError") !== FALSE) {
			$cr = ERROR_WRONG_PRM;
		}
		else if (strpos($response, "ExportError") !== FALSE) {
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

function Slicer_checkAdd(&$progress) {
	// do not in use if we have not resolved the share problem between threads in Perl
	$cr = 0;
	$ret_val = Slicer__requestSlicer(SLICER_URL_ADD_STATUS, TRUE, $response);

	if ($ret_val == SLICER_RESPONSE_OK) {
		if ((int)$response < 0) {
			$cr = ERROR_NO_SLICING;
			$progress = -1;
		}
		else {
			$cr = ERROR_OK;
			$progress = (int)$response;
		}
	}
	else if ($ret_val == SLICER_RESPONSE_ERROR) {
		if (strpos($response, "AddError" !== FALSE)) {
			$cr = ERROR_WRONG_PRM;
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
	$ret_val = Slicer__requestSlicer(SLICER_URL_LISTMODEL, TRUE, $response);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		$cr = ERROR_OK;
	}
	else {
		$cr = $ret_val;
		$response = "[]";
	}
	
	return $cr;
}

function Slicer_checkPlatformColor(&$array_cartridge = array()) {
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
			if ($abb_cartridge == 'error') {
				$cr = ERROR_WRONG_PRM;
				break;
			}
			$array_cartridge[] = $abb_cartridge;
			// we do not check filament status when starting slicing 20140807
// 			if (PrinterState_getFilamentStatus($abb_cartridge)) {
// 				continue;
// 			}
// 			else if ($abb_cartridge == 'l') {
// 				$cr = ERROR_MISS_LEFT_FILA;
// 				break;
// 			}
// 			else {
// 				$cr = ERROR_MISS_RIGT_FILA;
// 				break;
// 			}
		}
	}
	
	return $cr;
}

function Slicer_setModel($array_data) {
	$cr = 0;
	$ret_val = 0;
	$CI = &get_instance();
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
// 	else if (!isset($array_data[SLICER_PRM_ID])) {
// 		return ERROR_MISS_PRM;
// 	}
// 	else if (!isset($array_data[SLICER_PRM_XPOS])
// 			&& !isset($array_data[SLICER_PRM_YPOS])
// 			&& !isset($array_data[SLICER_PRM_ZPOS])
// 			&& !isset($array_data[SLICER_PRM_XROT])
// 			&& !isset($array_data[SLICER_PRM_YROT])
// 			&& !isset($array_data[SLICER_PRM_ZROT])
// 			&& !isset($array_data[SLICER_PRM_SCALE])
// 			&& !isset($array_data[SLICER_PRM_COLOR])) {
// 		return ERROR_MISS_PRM;
// 	}
	
	// prepare url
	$url_request .= SLICER_PRM_ID . '=' . $array_data[SLICER_PRM_ID];
	foreach (array(
			SLICER_PRM_XPOS,
			SLICER_PRM_YPOS,
			SLICER_PRM_ZPOS,
			SLICER_PRM_XROT,
			SLICER_PRM_YROT,
			SLICER_PRM_ZROT,
			SLICER_PRM_SCALE,
			SLICER_PRM_COLOR,
	) as $key) {
		if (isset($array_data[$key]) && $array_data[$key] !== FALSE) {
			$url_request .= '&' . $key . '=' . $array_data[$key];
		}
	}
	
	$ret_val = Slicer__requestSlicer($url_request);
	
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
	
	@unlink($CI->config->item('temp') . SLICER_FILE_MODEL);
	@unlink($CI->config->item('temp') . SLICER_FILE_TEMP_DATA);
	
	return $cr;
}

function Slicer_rendering($rho, $theta, $delta, &$path_image, $color1 = NULL, $color2 = NULL) {
	global $CFG;
	$cr = 0;
	$ret_val = 0;
	$response = NULL;
	$url_request = SLICER_URL_RENDERING;
	
	if (!isset($rho) || !isset($theta) || !isset($delta)) {
		return ERROR_MISS_PRM;
	}
	
	$url_request .= SLICER_PRM_RHO . '=' . $rho
			. '&' . SLICER_PRM_THETA . '=' . $theta . '&' . SLICER_PRM_DELTA . '=' . $delta;
	foreach (array(
			SLICER_PRM_COLOR1	=> $color1,
			SLICER_PRM_COLOR2	=> $color2,
	) as $key => $color) {
		if ($color) {
			$color_string = NULL;
			$cr = Slicer__getColorString($color, $color_string);
			if ($cr != ERROR_OK) {
				return $cr;
			}
			$url_request .= '&' . $key . '=' . $color_string;
		}
	}
	
	$ret_val = Slicer__requestSlicer($url_request, FALSE, $response);
	
	switch ($ret_val) {
		case SLICER_RESPONSE_OK:
		case SLICER_RESPONSE_WRONG_PRM:
		case SLICER_RESPONSE_MISS_PRM:
			$cr = $ret_val;
			break;
			
		case SLICER_RESPONSE_NO_MODEL:
			$cr = ERROR_EMPTY_PLATFORM;
			break;
			
		default:
			$cr = ERROR_INTERNAL;
			break;
	}
	
	if ($cr = ERROR_OK) {
		$explode_array = explode("\n", $response);
		if (isset($explode_array[1])) {
			$path_image = $explode_array[1];
			
			if (file_exists($path_image)) {
				$image_folder = dirname($path_image);
				$output = array();
				$ret_val = 0;
				$command = "convert $path_image $image_folder/" . SLICER_CMD_PRM_PREVIEW_FILE;
				
				exec($command, $output, $ret_val);
				$path_image = $image_folder . '/' . SLICER_CMD_PRM_PREVIEW_FILE;
				
				if ($ret_val != ERROR_NORMAL_RC_OK) {
					$CI = &get_instance();
					$CI->load->helper('printerlog');
					PrinterLog_logDebug('convert command error, cmd: ' . $command . '; ret: ' . $ret_val, __FILE__, __LINE__);
					return $cr; //TODO we do not change the return code of this error for this moment, but we will change it
				}
			}
		}
		else {
			$cr = ERROR_INTERNAL;
		}
// 		$path_image = $CFG->config['temp'] . SLICER_FILE_RENDERING;
// 		$path_image = $response;
	}
	else {
		$path_image = NULL;
	}
	
	return $cr;
}

function Slicer_changeParameter($array_setting) {
	$cr = 0;
	$ret_val = 0;
	$CI = &get_instance();
	$url_request = SLICER_URL_SETPARAMETER;
	
	if (!is_array($array_setting)) {
		$cr = ERROR_INTERNAL;
	}
	else if (count($array_setting) == 0) {
		$cr = ERROR_MISS_PRM;
	}
	else {
		$first_parameter = TRUE;
		
		foreach ($array_setting as $key => $value) {
			if ($first_parameter) {
				$url_request .= $key . '=' . $value;
				$first_parameter = FALSE;
			}
			else {
				$url_request .= '&' . $key . '=' . $value;
			}
		}
		
		$ret_val = Slicer__requestSlicer($url_request);
		
		switch ($ret_val) {
			case SLICER_RESPONSE_OK:
			case SLICER_RESPONSE_WRONG_PRM:
			case SLICER_RESPONSE_MISS_PRM:
			case 404:
				$cr = $ret_val;
				break;
				
			default:
				$cr = ERROR_INTERNAL;
				break;
		}
	}
	
	@unlink($CI->config->item('temp') . SLICER_FILE_MODEL);
	@unlink($CI->config->item('temp') . SLICER_FILE_TEMP_DATA);
	
	return $cr;
}

function Slicer_changeTemperByCartridge($array_cartridge) {
	$json_cartridge = array();
	$temperature = $first_temperature = NULL;
	$array_danger_return = array(
			ERROR_INTERNAL,
			ERROR_WRONG_PRM,
			ERROR_BUSY_PRINTER,
			ERROR_MISS_RIGT_CART,
			ERROR_MISS_LEFT_CART
	);
	
	$CI = &get_instance();
	$CI->load->helper('printerstate');
	
// 	foreach ($array_cartridge as $abb_cartridge) {
// 		switch ($abb_cartridge) {
// 			case 'r':
// 				$array_danger_return[] = ERROR_MISS_RIGT_CART;
// 				break;
				
// 			case 'l':
// 				$array_danger_return[] = ERROR_MISS_LEFT_CART;
// 				break;
				
// 			default:
// 				$CI->load->helper('printerlog');
// 				PrinterLog_logError('unknown cartridge abb value', __FILE__, __LINE__);
// 				return ERROR_INTERNAL;
// 				break; // never reach here
// 		}
// 	}
	
	foreach (array('r', 'l') as $abb_cartridge) {
		$ret_val = PrinterState_getCartridgeAsArray($json_cartridge, $abb_cartridge);
		//TODO think about if we need to set default temperature when cartridge is absent or not
		if (in_array($ret_val, $array_danger_return)) {
			//TODO notice the printerstate helper that slicer use default temperature instead of cartridge info (user can put cartridge during slicing)
			$json_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER] = SLICER_VALUE_DEFAULT_TEMPER;
			$json_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1] = SLICER_VALUE_DEFAULT_FIRST_TEMPER;
			
			$CI->load->helper('printerlog');
			PrinterLog_logMessage('dangerous return detected: ' . $ret_val
					 . ', slicer default temperature assigned for cartridge ' . $abb_cartridge, __FILE__, __LINE__);
		}
		
		if (is_null($temperature)) {
			$temperature = $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER];
		}
		else {
			$temperature .= ',' . $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER];
		}
		if (is_null($first_temperature)) {
			$first_temperature = $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1];
		}
		else {
			$first_temperature .= ',' . $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1];
		}
	}
		
	$array_setting = array(
			'temperature'				=> $temperature,
			'first_layer_temperature'	=> $first_temperature,
	);
	$cr = Slicer_changeParameter($array_setting);
	
	return $cr;
}

function Slicer_reset() {
	
}

function Slicer_reloadPreset() {
	$cr = 0;
	$CI = &get_instance();
	$url = SLICER_URL_RELOAD_PRESET;
	
	if ($CI->config->item('use_sdcard')) {
		$url .= '?' . SLICER_PRM_SDCARD . '=1';
	}
	$ret_val = Slicer__requestSlicer($url);
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		$cr = ERROR_OK;
	}
	else {
		$cr = ERROR_INTERNAL;
	}
	
	@unlink($CI->config->item('temp') . SLICER_FILE_MODEL);
	@unlink($CI->config->item('temp') . SLICER_FILE_TEMP_DATA);
	
	return $cr;
}

function Slicer_restart() {
	//exec(SLICER_CMD_RESTART_SLICER);
	pclose(popen(SLICER_CMD_RESTART_SLICER, 'r'));
	
	return;
}

function Slicer_checkAlive($restart = TRUE) {
	$ret_val = 0;
	$output = array();
	
	exec(SLICER_CMD_SLICER_PS_STATUS, $output, $ret_val);
	if ($ret_val == ERROR_NORMAL_RC_OK) {
		return TRUE;
	}
	
	if ($restart == TRUE) {
		Slicer_restart();
	}
	
	return FALSE;
}

function Slicer_checkOnline($restart = TRUE) {
	$CI = &get_instance();
	$ret_val = Slicer__requestSlicer('');
	
	if ($ret_val == SLICER_RESPONSE_OK) {
		return TRUE;
	}
	
	$CI->load->helper('printerlog');
	PrinterLog_logDebug('slicer return code: ' . $ret_val, __FILE__, __LINE__);
	
	if ($restart == TRUE) {
		Slicer_restart();
	}
	
	return FALSE;
}

//internal function
function Slicer__getHTTPCode($http_response_header) {
	$matches = array();
	preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);
	return (int)$matches[1];
}

function Slicer__requestSlicer($suffix_url, $time_limit = TRUE, &$response = NULL) {
	global $CFG;
	Slicer__changeURLPort();
	
	$context = stream_context_create(
			array('http' => array(
					'ignore_errors' => TRUE,
					'timeout' => ($time_limit) ? SLICER_TIMEOUT_WITHLIMIT : SLICER_TIMEOUT_NOLIMIT,
					)
			)
	);
	$url = $CFG->config['slicer_url'] . $suffix_url;
	$response = @file_get_contents($url, FALSE, $context);
	
	if ($response === FALSE || is_null($http_response_header)) {
		return 404;
	}
	
	return Slicer__getHTTPCode($http_response_header);
}

function Slicer__changeURLPort() {
	$CI = &get_instance();
	$slicer_url = $CI->config->item('slicer_url');
	$port_filepath = $CI->config->item('temp') . SLICER_FILE_HTTP_PORT;
	
	if (FALSE === strpos($slicer_url, '8080/')) {
		return;
	}
	if (file_exists($port_filepath)) {
		$port_slicer = @file_get_contents($port_filepath);
		
		if (strlen($port_slicer)) {
			$slicer_url = str_replace('8080/', '', $slicer_url);
			$slicer_url .= $port_slicer . '/';
		}
		$CI->config->set_item('slicer_url', $slicer_url);
		$CI->load->helper('printerlog');
		PrinterLog_logDebug('found slic3r port file, url: ' . $slicer_url, __FILE__, __LINE__);
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logMessage('can not find port file of slic3r, try to use original one, 8080', __FILE__, __LINE__);
	}
	
	return;
}

function Slicer__getColorString($color, &$color_string) {
	$array_color = array();
	
	if (strlen($color) != 7) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('color code error', __FILE__, __LINE__);
		
		return ERROR_INTERNAL;
	}
	
	$offset = 5;
	do {
		$array_color[] = hexdec(substr($color, $offset, 2)) / 255;
		$offset = $offset - 2;
	} while($offset >= 1);
	
	$color_string = json_encode($array_color);
	
	return ERROR_OK;
}

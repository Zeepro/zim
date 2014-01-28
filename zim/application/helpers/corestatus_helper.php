<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!defined('CORESTATUS_FILENAME_WORK')) {
	define('CORESTATUS_FILENAME_WORK',		'Work.json');
	define('CORESTATUS_FILENAME_INIT',		'Boot.json');
	define('CORESTATUS_FILENAME_CONNECT',	'Connection.json');
	
	define('CORESTATUS_TITLE_VERSION',		'Version');
// 	define('CORESTATUS_TITLE_CMD',			'CommandLine');
	define('CORESTATUS_TITLE_STATUS',		'State');
// 	define('CORESTATUS_TITLE_CMD_CANCEL',	'Cancel');
// 	define('CORESTATUS_TITLE_CMD_PAUSE',	'PauseOrResume');
// 	define('CORESTATUS_TITLE_URL_REDIRECT',	'CallBackURL');
// 	define('CORESTATUS_TITLE_URL_REDIRECT',	'RedirectURL');
	define('CORESTATUS_TITLE_MESSAGE',		'Message');
	define('CORESTATUS_TITLE_STARTTIME',	'StartDate');
	
	define('CORESTATUS_VALUE_IDLE',				'idle');
	define('CORESTATUS_VALUE_PRINT',			'printing');
	define('CORESTATUS_VALUE_LOAD_FILA_L',		'loading_left');
	define('CORESTATUS_VALUE_LOAD_FILA_R',		'loading_right');
	define('CORESTATUS_VALUE_UNLOAD_FILA_L',	'unloading_left');
	define('CORESTATUS_VALUE_UNLOAD_FILA_R',	'unloading_right');
// 	define('CORESTATUS_VALUE_UPGRADE',			'upgrading');

	define('CORESTATUS_PRM_CAMERA_START',
			' -v verbose -r 10 -s 320x240 -f video4linux2 -i /dev/video0 -c:v libx264 -crf 18 -profile:v baseline -b:v 1024k -pix_fmt yuv420p -flags -global_header -hls_time 1 -hls_wrap 5 /var/www/tmp/zim.m3u8');
	define('CORESTATUS_PRM_CAMERA_STOP',	' stop ');
}

function CoreStatus_checkInIdle(&$status_current = '') {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_WORK;
	$tmp_array = array();
	
	$CI = &get_instance();
	$CI->load->helper('json');
	
	// read json file
	try {
		$tmp_array = json_read($state_file);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('read work json error');
		return FALSE;
	}
	
	// check status
	if ($tmp_array['json'][CORESTATUS_TITLE_STATUS] == CORESTATUS_VALUE_IDLE) {
		return TRUE;
	}
	$status_current = $tmp_array['json'][CORESTATUS_TITLE_STATUS];
	
	return FALSE;
}

function CoreStatus_checkInInitialization() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_INIT;
	
	// we have the json file when in init
	if (file_exists($state_file)) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

function CoreStatus_checkInConnection() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_CONNECT;
	
	// we have the json file when having finished connection config
	if (file_exists($state_file)) {
		return FALSE;
	}
	else {
		return TRUE;
	}
}

function CoreStatus_checkCallREST() {
	return CoreStatus__checkCallController('rest');
}

function CoreStatus_checkCallInitialization(&$url_redirect = '') {
	$url_redirect = '/initialization';
	
	return CoreStatus__checkCallController('initialization');
}

function CoreStatus_checkCallConnection(&$url_redirect = '') {
	$url_redirect = '/connection';
	
	return CoreStatus__checkCallController('connection');
}

function CoreStatus_checkCallPrinting(&$url_redirect = '') {
	$url_redirect = '/printdetail/status';
	
	return CoreStatus__checkCallURI(array(
			'/printdetail/status'		=> NULL,
			'/printdetail/status_ajax'	=> NULL,
	));
}

function CoreStatus_checkCallPrintingAjax() {
	$url_redirect = '/printdetail/status';
	
	return CoreStatus__checkCallURI(array(
			'/printdetail/status_ajax'	=> NULL,
	));
}

function CoreStatus_checkCallNoBlockREST() {
	$CI = &get_instance();
	$CI->load->helper('printerstate');
	
	return CoreStatus__checkCallURI(array(
			'/rest/status'	=> NULL,
			'/rest/get'		=> array(
					'p'	=> array(PRINTERSTATE_PRM_TEMPER, PRINTERSTATE_PRM_INFO),
			),
	));
}

function CoreStatus_setInIdle() {
	$status_previous = '';
	$ret_val = CoreStatus_checkInIdle($status_previous);
	if ($ret_val == TRUE) {
		return TRUE; // we are already in idle
	}
	else if ($status_previous == CORESTATUS_VALUE_PRINT) {
		// stop camera http live streaming
		global $CFG;
		$output = NULL;
		$ret_val = 0;
		$command = $CFG->config['camera'] . CORESTATUS_PRM_CAMERA_STOP;
		
		exec($command, $output, $ret_val);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('camera stop command error');
			return FALSE;
		}
	}
	
	return CoreStatus__setInStatus(CORESTATUS_VALUE_IDLE,
			array(CORESTATUS_TITLE_STARTTIME => NULL)
	);
}

function CoreStatus_setInPrinting() {
	// start camera http live streaming
	global $CFG;
	$output = NULL;
	$ret_val = 0;
	$command = $CFG->config['camera'] . CORESTATUS_PRM_CAMERA_START;
	
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('camera start command error');
		return FALSE;
	}
	
	return CoreStatus__setInStatus(CORESTATUS_VALUE_PRINT,
			array(CORESTATUS_TITLE_STARTTIME => time())
	);
}

function CoreStatus_setInLoading($abb_filament) {
	if (!in_array($abb_filament, array('l', 'r'))) {
		return FALSE;
	}
	$value_status = ($abb_filament == 'r')
			? CORESTATUS_VALUE_LOAD_FILA_R : CORESTATUS_VALUE_LOAD_FILA_L;
	
	return CoreStatus__setInStatus($value_status, array());
}

function CoreStatus_setInUnloading($abb_filament) {
	if (!in_array($abb_filament, array('l', 'r'))) {
		return FALSE;
	}
	$value_status = ($abb_filament == 'r')
			? CORESTATUS_VALUE_UNLOAD_FILA_R : CORESTATUS_VALUE_UNLOAD_FILA_L;
	
	return CoreStatus__setInStatus($value_status, array());
}

function CoreStatus_getStartPrintTime(&$time_start) {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_WORK;
	$tmp_array = array();
	$data_json = array();
	$time_start = NULL;
	
	$CI = &get_instance();
	$CI->load->helper('json');
	
	// read json file
	try {
		$tmp_array = json_read($state_file);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		return FALSE;
	}
	$data_json = $tmp_array['json'];
	
	// check status
	if ($data_json[CORESTATUS_TITLE_STATUS] == CORESTATUS_VALUE_PRINT) {
		if (!isset($data_json[CORESTATUS_TITLE_STARTTIME])) {
			return FALSE;
		}
		$time_start = $data_json[CORESTATUS_TITLE_STARTTIME];
	}
	
	return TRUE;
}

function CoreStatus_wantConnection() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_CONNECT;
	
	if (file_exists($state_file)) {
		$ret_val = unlink($state_file);
		return $ret_val;
	}
	else {
		return FALSE;
	}
	
	return FALSE;
}

function CoreStatus_finishConnection($data_json = array()) {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_CONNECT;
	
	$fp = fopen($state_file, 'w');
	if ($fp) {
		fwrite($fp, json_encode($data_json));
		fclose($fp);
	}
	else {
		return FALSE;
	}
	
	return TRUE;
}

// internal function
function CoreStatus__checkCallController($name_controller) {
	$CI = &get_instance();
	if ($CI->router->class == $name_controller) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

// function CoreStatus__checkCallURI($array_URI) {
// 	$CI = &get_instance();
// 	if (in_array($CI->router->uri->uri_string, $array_URI)) {
// 		return TRUE;
// 	}
// 	else {
// 		return FALSE;
// 	}
// }

function CoreStatus__checkCallURI($array_URI) {
	$CI = &get_instance();
	if (array_key_exists($CI->router->uri->uri_string, $array_URI)) {
		if (is_null($array_URI[$CI->router->uri->uri_string])) {
			return TRUE;
		}
		else if (!is_array($array_URI[$CI->router->uri->uri_string])) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('check call URI internal API error');
			return FALSE;
		}
		else {
			foreach ($array_URI[$CI->router->uri->uri_string] as $key => $value) {
				$real_value = $CI->input->get($key);
				if (is_array($value) && in_array($real_value, $value)) {
					continue; // compare with a data array
				} else if ($real_value == $value) {
					continue; // compare with an alone data
				}
				else {
					return FALSE;
					break; // never reach here
				}
			}
		}
	}
	else {
		return FALSE;
	}
	
	return TRUE;
}

function CoreStatus__setInStatus($value_status, $data_array = array()) {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_WORK;
	$tmp_array = array();
	$data_json = array();
	$fp = NULL;
	
	$CI = &get_instance();
	$CI->load->helper('json');
	
	// read json file
	try {
		$tmp_array = json_read($state_file);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		return FALSE;
	}
	$data_json = $tmp_array['json'];
	
	// change status
	$data_json[CORESTATUS_TITLE_STATUS] = $value_status;
	foreach ($data_array as $key => $value) {
		$data_json[$key] = $value;
	}
	
	// write json file
	$fp = fopen($CFG->config['conf'] . CORESTATUS_FILENAME_WORK, 'w');
	if ($fp) {
		fwrite($fp, json_encode($data_json));
		fclose($fp);
	}
	else {
		return FALSE;
	}
	
	return TRUE;
}

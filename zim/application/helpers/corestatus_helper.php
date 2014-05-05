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
// 	define('CORESTATUS_TITLE_LASTERROR',	'LastError');
	
	define('CORESTATUS_VALUE_IDLE',				'idle');
	define('CORESTATUS_VALUE_PRINT',			'printing');
	define('CORESTATUS_VALUE_LOAD_FILA_L',		'loading_left');
	define('CORESTATUS_VALUE_LOAD_FILA_R',		'loading_right');
	define('CORESTATUS_VALUE_UNLOAD_FILA_L',	'unloading_left');
	define('CORESTATUS_VALUE_UNLOAD_FILA_R',	'unloading_right');
	define('CORESTATUS_VALUE_CANCEL',			'canceling');
	define('CORESTATUS_VALUE_WAIT_CONNECT',		'to_be_connected');
	define('CORESTATUS_VALUE_SLICE',			'slicing');
// 	define('CORESTATUS_VALUE_UPGRADE',			'upgrading');
}

function CoreStatus_initialFile() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_WORK;
	
	if (file_exists($state_file)) {
		return TRUE;
	}
	else {
		// prepare data array
		$data_json = array(
				CORESTATUS_TITLE_VERSION	=> '1.0',
				CORESTATUS_TITLE_STATUS		=> CORESTATUS_VALUE_IDLE,
				CORESTATUS_TITLE_MESSAGE	=> NULL,
		);
		
		// write json file
		$fp = fopen($state_file, 'w');
		if ($fp) {
			fwrite($fp, json_encode($data_json));
			fclose($fp);
		}
		else {
			return FALSE;
		}
	}
	
	return TRUE;
}

function CoreStatus_checkInIdle(&$status_current = '', &$array_status = array()) {
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
		PrinterLog_logError('read work json error', __FILE__, __LINE__);
		return FALSE;
	}
	
	// check status
	$array_status = $tmp_array['json'];
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
			'/printdetail/cancel'		=> NULL, // for canceling printing
			'/printdetail/cancel_ajax'	=> NULL, // for canceling printing
	));
}

function CoreStatus_checkCallCanceling(&$url_redirect = '') {
	$url_redirect = '/printdetail/cancel';
	
	return CoreStatus__checkCallURI(array(
			'/printdetail/cancel'		=> NULL,
			'/printdetail/cancel_ajax'	=> NULL,
	));
}

function CoreStatus_checkCallUnloading(&$url_redirect = '') {
	$status_current = '';
	$abb_filament = '';
	CoreStatus_checkInIdle($status_current);
	if ($status_current == CORESTATUS_VALUE_UNLOAD_FILA_L) {
		$url_redirect = '/printerstate/changecartridge?v=l&f=0';
		$abb_filament = 'l';
	}
	else { // CORESTATUS_VALUE_UNLOAD_FILA_R
		$url_redirect = '/printerstate/changecartridge?v=r&f=0';
		$abb_filament = 'r';
	}
	
	return CoreStatus__checkCallURI(array(
			'/printerstate/changecartridge'			=> array(
					'v'	=> $abb_filament,
			),
			'/printerstate/changecartridge_ajax'	=> NULL,
			'/printerstate/changecartridge_action'	=> NULL,
	));
}

function CoreStatus_checkCallPrintingAjax() {
// 	$url_redirect = '/printdetail/status';
	
	return CoreStatus__checkCallURI(array(
			'/printdetail/status_ajax'	=> NULL,
	));
}

function CoreStatus_checkCallCancelingAjax() {
// 	$url_redirect = '/printdetail/status';
	
	return CoreStatus__checkCallURI(array(
			'/printdetail/cancel_ajax'	=> NULL,
	));
}

function CoreStatus_checkCallSlicing(&$url_redirect = '') {
	$url_redirect = '/printdetail/slice';
	
	return CoreStatus__checkCallURI(array(
			'/printdetail/slice'		=> NULL,
			'/printdetail/slice_ajax'	=> NULL,
			'/printdetail/slice_action'	=> NULL,
	));
}

function CoreStatus_checkCallDebug() {
	// test_log & test_video & test_cartridge controller is not in My_controller's control
	return (CoreStatus__checkCallController('gcode')
			|| CoreStatus__checkCallController('pronterface'));
}

function CoreStatus_checkCallNoBlockREST() {
	$CI = &get_instance();
	$CI->load->helper('printerstate');
	
	return CoreStatus__checkCallURI(array(
			'/rest/status'		=> NULL,
			'/rest/get'			=> array(
					'p'	=> PRINTERSTATE_PRM_INFO,
			),
			'/rest/gcode'		=> NULL,
			'/rest/gcodefile'	=> NULL,
	));
}

function CoreStatus_checkCallNoBlockRESTInConnection() {
	return CoreStatus__checkCallURI(array(
			'/rest/status'		=> NULL,
			'/rest/setnetwork'	=> NULL,
	));
}

function CoreStatus_checkCallNoBlockRESTInPrint() {
	return CoreStatus__checkCallURI(array(
			'/rest/status'		=> NULL,
			'/rest/cancel'		=> NULL,
			'/rest/suspend'		=> NULL,
			'/rest/resume'		=> NULL,
			'/rest/get'			=> array(
					'p'	=> PRINTERSTATE_PRM_TEMPER,
			),
	));
}

function CoreStatus_checkCallNoBlockRESTInSlice() {
	return CoreStatus__checkCallURI(array(
			'/rest/status'		=> NULL,
			'/rest/cancel'		=> NULL,
	));
}

function CoreStatus_checkCallNoBlockPage() {
	return CoreStatus__checkCallURI(array(
			'/printerstate/sethostname'	=> NULL,
	));
}

function CoreStatus_setInIdle($last_error = FALSE) {
	$status_previous = '';
	$ret_val = CoreStatus_checkInIdle($status_previous);
	if ($ret_val == TRUE) {
		return TRUE; // we are already in idle
	}
	else if ($status_previous == CORESTATUS_VALUE_PRINT) {
		// stop camera http live streaming
		$ret_val = 0;
		
		$CI = &get_instance();
		$CI->load->helper('zimapi');
		$ret_val = ZimAPI_cameraOff();
		if ($ret_val != TRUE) {
			return FALSE;
		}
	}
// 	else if ($status_previous == CORESTATUS_VALUE_UNLOAD_FILA_L
// 			|| $status_previous == CORESTATUS_VALUE_UNLOAD_FILA_R) {
// 		$CI = &get_instance();
// 		$CI->load->helper('printerstate');
// 		$ret_val = PrinterState_afterUnloadFilament();
// 		if ($ret_val != ERROR_OK) {
// 			return FALSE;
// 		}
// 	}
	if ($last_error !== FALSE) {
		// add last_error for slicing
		//TODO perhaps also check $status_previous == CORESTATUS_VALUE_SLICE ?
		return CoreStatus__setInStatus(CORESTATUS_VALUE_IDLE,
				array(
						CORESTATUS_TITLE_STARTTIME	=> NULL,
						CORESTATUS_TITLE_MESSAGE	=> $last_error,
				)
		);
	}
	
	return CoreStatus__setInStatus(CORESTATUS_VALUE_IDLE,
			array(CORESTATUS_TITLE_STARTTIME => NULL)
	);
}

function CoreStatus_setInPrinting($stop_printing = FALSE) {
	if ($stop_printing == FALSE) {
		// start camera http live streaming
		$ret_val = 0;
		
		$CI = &get_instance();
		$CI->load->helper('zimapi');
		$ret_val = ZimAPI_cameraOn(ZIMAPI_PRM_CAMERA_PRINTSTART);
		if ($ret_val == FALSE) {
			return FALSE;
		}
	
		return CoreStatus__setInStatus(CORESTATUS_VALUE_PRINT,
				array(CORESTATUS_TITLE_STARTTIME => time())
		);
	}
	else {
		//TODO check if we need remaining time for canceling or not?
		return CoreStatus__setInStatus(CORESTATUS_VALUE_CANCEL);
// 		return CoreStatus__setInStatus(CORESTATUS_VALUE_CANCEL,
// 				array(CORESTATUS_TITLE_STARTTIME => time())
// 		);
	}
}

function CoreStatus_setInCanceling() {
	return CoreStatus_setInPrinting(TRUE);
}

function CoreStatus_setInLoading($abb_filament) {
	if (!in_array($abb_filament, array('l', 'r'))) {
		return FALSE;
	}
	$value_status = ($abb_filament == 'r')
			? CORESTATUS_VALUE_LOAD_FILA_R : CORESTATUS_VALUE_LOAD_FILA_L;
	
	return CoreStatus__setInStatus($value_status, array(CORESTATUS_TITLE_STARTTIME => time()));
}

function CoreStatus_setInUnloading($abb_filament) {
	if (!in_array($abb_filament, array('l', 'r'))) {
		return FALSE;
	}
	$value_status = ($abb_filament == 'r')
			? CORESTATUS_VALUE_UNLOAD_FILA_R : CORESTATUS_VALUE_UNLOAD_FILA_L;
	
	return CoreStatus__setInStatus($value_status, array(CORESTATUS_TITLE_STARTTIME => time()));
}

function CoreStatus_setInSlicing() {
	return CoreStatus__setInStatus(CORESTATUS_VALUE_SLICE,
			array(CORESTATUS_TITLE_MESSAGE => NULL)
	);
}

function CoreStatus_getStartTime(&$time_start) {
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
// 	if ($data_json[CORESTATUS_TITLE_STATUS] == CORESTATUS_VALUE_PRINT) {
	if (!isset($data_json[CORESTATUS_TITLE_STARTTIME]) || $data_json[CORESTATUS_TITLE_STARTTIME] == NULL) {
		return FALSE;
	}
	$time_start = $data_json[CORESTATUS_TITLE_STARTTIME];
// 	}
	
	return TRUE;
}

function CoreStatus_checkInWaitTime($time_wait) {
	$time_start = 0;
	$ret_val = CoreStatus_getStartTime($time_start);
	if ($ret_val != TRUE) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('get start time error', __FILE__, __LINE__);
	}
	if (time() - $time_start > $time_wait) {
		return FALSE;
	}
	
	return TRUE; // we treat getting start time error as still in wait time
}

function CoreStatus_wantConnection() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_CONNECT;
	
	if (file_exists($state_file)) {
		$ret_val = CoreStatus__setInStatus(CORESTATUS_VALUE_WAIT_CONNECT);
		if ($ret_val != TRUE) {
			return FALSE;
		}
		
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
	
	return CoreStatus_setInIdle();
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
			PrinterLog_logError('check call URI internal API error', __FILE__, __LINE__);
			return FALSE;
		}
		else {
			foreach ($array_URI[$CI->router->uri->uri_string] as $key => $value) {
				$real_value = $CI->input->get($key);
				if (is_array($value) && in_array($real_value, $value)) {
					continue; // compare with a data array
// 					return TRUE;
				} else if ($real_value == $value) {
					continue; // compare with an alone data
// 					return TRUE;
				}
				else {
					return FALSE;
					break; // never reach here
// 					continue;
				}
			}
		}
	}
	else {
		return FALSE;
	}
	
	return TRUE;
// 	return FALSE;
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

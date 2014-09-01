<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

if (!defined('CORESTATUS_FILENAME_WORK')) {
	define('CORESTATUS_FILENAME_WORK',		'Work.json');
	define('CORESTATUS_FILENAME_INIT',		'Boot.json');
	define('CORESTATUS_FILENAME_CONNECT',	'Connection.json');
	define('CORESTATUS_FILENAME_REMOTEOFF',	'tromboning_off');
	
	define('CORESTATUS_TITLE_VERSION',		'Version');
// 	define('CORESTATUS_TITLE_CMD',			'CommandLine');
	define('CORESTATUS_TITLE_STATUS',		'State');
// 	define('CORESTATUS_TITLE_CMD_CANCEL',	'Cancel');
// 	define('CORESTATUS_TITLE_CMD_PAUSE',	'PauseOrResume');
// 	define('CORESTATUS_TITLE_URL_REDIRECT',	'CallBackURL');
// 	define('CORESTATUS_TITLE_URL_REDIRECT',	'RedirectURL');
	define('CORESTATUS_TITLE_MESSAGE',		'Message');
	define('CORESTATUS_TITLE_STARTTIME',	'StartDate');
	define('CORESTATUS_TITLE_LASTERROR',	'LastError');
// 	define('CORESTATUS_TITLE_LASTSTATUS',	'LastState');
	define('CORESTATUS_TITLE_SUBSTATUS',	'Substate');
	
	define('CORESTATUS_VALUE_IDLE',				'idle');
	define('CORESTATUS_VALUE_PRINT',			'printing');
	define('CORESTATUS_VALUE_LOAD_FILA_L',		'loading_left');
	define('CORESTATUS_VALUE_LOAD_FILA_R',		'loading_right');
	define('CORESTATUS_VALUE_UNLOAD_FILA_L',	'unloading_left');
	define('CORESTATUS_VALUE_UNLOAD_FILA_R',	'unloading_right');
	define('CORESTATUS_VALUE_CANCEL',			'canceling');
	define('CORESTATUS_VALUE_WAIT_CONNECT',		'to_be_connected');
	define('CORESTATUS_VALUE_SLICE',			'slicing');
	define('CORESTATUS_VALUE_SLICED',			'sliced');
// 	define('CORESTATUS_VALUE_UPGRADE',			'upgrading');
	define('CORESTATUS_VALUE_RECOVERY',			'recovery');
	define('CORESTATUS_VALUE_USB',				'usb_connected');
	
	define('CORESTATUS_CMD_CHECK_SD',		'echo writable > ');
	define('CORESTATUS_SUFFIX_CONF',		'conf/');
	define('CORESTATUS_SUFFIX_PRESET',		'conf/presetlist/');
	define('CORESTATUS_FILE_SD_ON',			'_SD_On.tmp');
	define('CORESTATUS_FILE_SD_OFF',		'_SD_Off.tmp');
	define('CORESTATUS_FILE_LEVEL_DEBUG',	'_Level_Debug.tmp');
	define('CORESTATUS_FILE_LEVEL_MESSAGE',	'_Level_Message.tmp');
	define('CORESTATUS_FILE_LEVEL_ERROR',	'_Level_Error.tmp');
	define('CORESTATUS_FILE_LEVEL_NONE',	'_Level_None.tmp');
	define('CORESTATUS_FILENAME_PAUSE',		'_Printer_inPause.tmp');
	
	define('CORESTATUS_GLOBAL_URL_RDV',		'zeepro.com');
}

function CoreStatus_initialFile() {
	$CI = &get_instance();
	$state_file = NULL;
	$sdcard = FALSE;
	
	// for the first time, check if we can use all files in sdcard instead of config partition
	// then save the choice in a status file in the temp to remember it
	if (file_exists($CI->config->item('temp') . CORESTATUS_FILE_SD_ON)) {
		$sdcard = TRUE;
	}
	else if (file_exists($CI->config->item('temp') . CORESTATUS_FILE_SD_OFF)) {
		$sdcard = FALSE;
	}
	if (is_writable($CI->config->item('sdcard'))) {
		$cr = 0;
		$command = CORESTATUS_CMD_CHECK_SD . $CI->config->item('sdcard') . '.phptest.tmp';
		$output = array();
		
		$CI->load->helper('errorcode');
		exec($command, $output, $cr);
		
		if ($cr == ERROR_NORMAL_RC_OK) {
			$sdcard = TRUE;
			$command = CORESTATUS_CMD_CHECK_SD . $CI->config->item('temp') . CORESTATUS_FILE_SD_ON;
		}
		else {
			$command = CORESTATUS_CMD_CHECK_SD . $CI->config->item('temp') . CORESTATUS_FILE_SD_OFF;
		}
	}
	else {
		$command = CORESTATUS_CMD_CHECK_SD . $CI->config->item('temp') . CORESTATUS_FILE_SD_OFF;
	}
	exec($command);
	
	$array_change = array(
			'conf'			=> CORESTATUS_SUFFIX_CONF,
			'presetlist'	=> CORESTATUS_SUFFIX_PRESET,
	);
	foreach ($array_change as $key => $value) {
		$folder_path = NULL;
		if ($sdcard == TRUE) {
			$folder_path = $CI->config->item('sdcard') . $value;
		}
		else {
			$folder_path = $CI->config->item('nandconf') . $value;
		}
		
		// check folder exists or not, if not, create it
		if (!file_exists($folder_path)) {
			mkdir($folder_path);
		}
		
		// change config setting to right path
		$CI->config->set_item($key, $folder_path);
	}
	$CI->config->set_item('use_sdcard', $sdcard);
	
	$CI->load->helper('zimapi');
	if (!ZimAPI_initialFile()) {
		return FALSE;
	}
	
	$state_file = $CI->config->item('conf') . CORESTATUS_FILENAME_WORK;
	
	if (!file_exists($state_file)) {
		// prepare data array
		$data_json = array(
				CORESTATUS_TITLE_VERSION	=> '1.0',
				CORESTATUS_TITLE_STATUS		=> CORESTATUS_VALUE_IDLE,
				CORESTATUS_TITLE_LASTERROR	=> NULL,
				CORESTATUS_TITLE_MESSAGE	=> NULL,
		);
		
		// write json file
		$fp = fopen($state_file, 'w');
		if ($fp) {
			fwrite($fp, json_encode($data_json));
			fclose($fp);
			chmod($fp, '0777');
		}
		else {
			return FALSE;
		}
	}
	
	// debug level
	if (file_exists($CI->config->item('temp') . CORESTATUS_FILE_LEVEL_DEBUG)) {
		$CI->config->set_item('log_level', 3);
	} else if (file_exists($CI->config->item('temp') . CORESTATUS_FILE_LEVEL_MESSAGE)) {
		$CI->config->set_item('log_level', 2);
	} else if (file_exists($CI->config->item('temp') . CORESTATUS_FILE_LEVEL_ERROR)) {
		$CI->config->set_item('log_level', 1);
	} else if (file_exists($CI->config->item('temp') . CORESTATUS_FILE_LEVEL_NONE)) {
		$CI->config->set_item('log_level', 0);
	}
	
	return TRUE;
}

function CoreStatus_checkTromboning() {
	global $CFG;
	$state_file = $CFG->config['conf'] . CORESTATUS_FILENAME_REMOTEOFF;
	
	if (!file_exists($state_file)) {
		return FALSE;
	}
	
	if ($_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR']
	&& substr($_SERVER['HTTP_HOST'], -strlen(CORESTATUS_GLOBAL_URL_RDV)) === CORESTATUS_GLOBAL_URL_RDV) {
		// SSL connection
		return TRUE;
	}
	else {
		return FALSE;
	}
	
	return FALSE;
}

function CoreStatus_checkInIdle(&$status_current = '', &$array_status = array()) {
// 	global $CFG;
	$CI = &get_instance();
	$state_file = $CI->config->item('conf') . CORESTATUS_FILENAME_WORK;
	$tmp_array = array();
	
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
// 	if ($tmp_array['json'][CORESTATUS_TITLE_STATUS] == CORESTATUS_VALUE_IDLE
// 			|| $tmp_array['json'][CORESTATUS_TITLE_STATUS] == CORESTATUS_VALUE_SLICED) {
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

function CoreStatus_checkInUSB() {
	$CI = &get_instance();
	$CI->load->helper('zimapi');
	
	return ZimAPI_checkUSB();
}

function CoreStatus_checkInPause() {
	$CI = &get_instance();
	$status_file = $CI->config->item('temp') . CORESTATUS_FILENAME_PAUSE;
	
	if (file_exists($status_file)) {
		return TRUE;
	}
	
	return FALSE;
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

function CoreStatus_checkCallUSB(&$url_redirect = '') {
	$url_redirect = '/usb';
	
	return CoreStatus__checkCallController('usb');
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
			'/printerstate/changecartridge'							=> array(
					'v'	=> $abb_filament,
			),
			'/printerstate/changecartridge_ajax'					=> NULL,
			'/printerstate/changecartridge_action'					=> NULL,
			'/printerstate/changecartridge_action/cancel_unload'	=> NULL,
	));
}

function CoreStatus_checkCallloading(&$url_redirect = '') {
	$status_current = '';
	$abb_filament = '';
	CoreStatus_checkInIdle($status_current);
	if ($status_current == CORESTATUS_VALUE_LOAD_FILA_L) {
		$url_redirect = '/printerstate/changecartridge?v=l&f=0';
		$abb_filament = 'l';
	}
	else { // CORESTATUS_VALUE_LOAD_FILA_R
		$url_redirect = '/printerstate/changecartridge?v=r&f=0';
		$abb_filament = 'r';
	}
	
	return CoreStatus__checkCallURI(array(
			'/printerstate/changecartridge'							=> array(
					'v'	=> $abb_filament,
			),
			'/printerstate/changecartridge_ajax'					=> NULL,
			'/printerstate/changecartridge_action'					=> NULL,
			'/printerstate/changecartridge_action/cancel_unload'	=> NULL,
	));
}

function CoreStatus_checkCallRecovery(&$url_redirect = '') {
	//TODO finish and test me
	$url_redirect = '/printdetail/recovery';
	
	return CoreStatus__checkCallURI(array(
			'/printdetail/recovery'			=> NULL,
			'/printdetail/recovery_ajax'	=> NULL,
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
	$url_redirect = '/sliceupload/slicestatus';
	
	return CoreStatus__checkCallURI(array(
			'/sliceupload/preview'				=> NULL,
			'/sliceupload/slice'				=> NULL,
			'/sliceupload/slicestatus'			=> NULL,
			'/sliceupload/slice_status_ajax'	=> NULL,
			'/sliceupload/slice_action'			=> NULL,
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
	$CI = &get_instance();
	$CI->load->helper(array('printerstate', 'zimapi'));
	
	return CoreStatus__checkCallURI(array(
			'/rest/status'		=> NULL,
			'/rest/cancel'		=> NULL,
			'/rest/suspend'		=> NULL,
			'/rest/resume'		=> NULL,
			'/rest/get'			=> array(
					'p'	=> array(
							PRINTERSTATE_PRM_TEMPER,
							PRINTERSTATE_PRM_STRIPLED,
							PRINTERSTATE_PRM_HEADLED,
							ZIMAPI_PRM_VIDEO_MODE,
					),
			),
			'/rest/set'			=> array(
					'p'	=> array(
							PRINTERSTATE_PRM_STRIPLED,
							PRINTERSTATE_PRM_HEADLED,
							ZIMAPI_PRM_VIDEO_MODE,
					),
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

function CoreStatus_setInIdle($last_error = FALSE, $error_message = FALSE) {
	$status_previous = '';
	$ret_val = CoreStatus_checkInIdle($status_previous);
	if ($ret_val == TRUE) {
		return TRUE; // we are already in idle
	}
	else if ($status_previous == CORESTATUS_VALUE_PRINT
			|| $status_previous == CORESTATUS_VALUE_CANCEL) {
		// stop camera http live streaming
		$ret_val = 0;
		
// 		$CI = &get_instance();
// 		$CI->load->helper('zimapi');
// 		$ret_val = ZimAPI_cameraOff();
// 		if ($ret_val != TRUE) {
// 			return FALSE;
// 		}
		
		CoreStatus_setInPause(FALSE); // not necessary in any case, just a safty
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
						CORESTATUS_TITLE_LASTERROR	=> $last_error,
						CORESTATUS_TITLE_MESSAGE	=> $error_message ? $error_message : NULL,
				)
		);
	}
	
	return CoreStatus__setInStatus(CORESTATUS_VALUE_IDLE,
			array(CORESTATUS_TITLE_STARTTIME => NULL)
	);
}

function CoreStatus_cleanSliced() {
	$CI = &get_instance();
	$CI->load->helper('slicer');
	
	@unlink($CI->config->item('temp') . SLICER_FILE_MODEL);
	@unlink($CI->config->item('temp') . SLICER_FILE_TEMP_DATA);
	
	return TRUE;
}

// function CoreStatus_setInSliced($last_error = FALSE) {
// 	$status_previous = NULL;
// 	$ret_val = CoreStatus_checkInIdle($status_previous);
// 	if ($ret_val == TRUE && $status_previous == CORESTATUS_VALUE_SLICED) {
// 		return TRUE; // we are already in idle
// 	}
// 	else if ($ret_val != FALSE || $status_previous != CORESTATUS_VALUE_SLICE) {
// 		// reject the changment to sliced when not passing by slicing
// 		return FALSE;
// 	}
	
// 	if ($last_error !== FALSE) {
// 		// add last_error for slicing
// 		// perhaps also check $status_previous == CORESTATUS_VALUE_SLICE ?
// 		return CoreStatus__setInStatus(CORESTATUS_VALUE_SLICED,
// 				array(
// 						CORESTATUS_TITLE_STARTTIME	=> NULL,
// 						CORESTATUS_TITLE_MESSAGE	=> $last_error,
// 				)
// 		);
// 	}
	
// 	return CoreStatus__setInStatus(CORESTATUS_VALUE_SLICED,
// 			array(CORESTATUS_TITLE_STARTTIME => NULL)
// 	);
// }

function CoreStatus_setInPrinting($stop_printing = FALSE) {
	if ($stop_printing == FALSE) {
		// start camera http live streaming
		$ret_val = 0;
		
// 		$CI = &get_instance();
// 		$CI->load->helper('zimapi');
// 		$ret_val = ZimAPI_cameraOn(ZIMAPI_PRM_CAMERA_PRINTSTART);
// 		if ($ret_val == FALSE) {
// 			return FALSE;
// 		}
		
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

function CoreStatus_setInPause($value = TRUE) {
	$CI = &get_instance();
	$status_file = $CI->config->item('temp') . CORESTATUS_FILENAME_PAUSE;
	
	if ($value == TRUE) {
		try {
			$fp = fopen($status_file, 'w');
			if ($fp) {
				fwrite($fp, 'pause');
				fclose($fp);
			}
			else {
				$CI->load->helper('printerlog');
				PrinterLog_logError('open pause status file error', __FILE__, __LINE__);
				return FALSE;
			}
		} catch (Exception $e) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('write pause status file error', __FILE__, __LINE__);
			return FALSE;
		}
	}
	else {
		if (file_exists($status_file)) {
			@unlink($status_file);
		}
		else {
			$CI->load->helper('printerlog');
			PrinterLog_logMessage('call get out pause when not in pause', __FILE__, __LINE__);
		}
	}
	
	return TRUE;
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
			array(
					CORESTATUS_TITLE_LASTERROR	=> NULL,
					CORESTATUS_TITLE_MESSAGE	=> NULL,
			)
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

function CoreStatus_setDebugLevel($level = 1) {
	global $CFG;
	$array_unlink = array();
	$file_set = NULL;
	
	switch ($level) {
		case 0:
			$array_unlink = array(
				$CFG->config['temp'] . CORESTATUS_FILE_LEVEL_DEBUG,
				$CFG->config['temp'] . CORESTATUS_FILE_LEVEL_MESSAGE,
				$CFG->config['temp'] . CORESTATUS_FILE_LEVEL_ERROR,
			);
			$file_set = $CFG->config['temp'] . CORESTATUS_FILE_LEVEL_NONE;
			break;
			
		case 1:
			$array_unlink = array(
				$CFG->config['temp'] . CORESTATUS_FILE_LEVEL_DEBUG,
				$CFG->config['temp'] . CORESTATUS_FILE_LEVEL_MESSAGE,
			);
			$file_set = $CFG->config['temp'] . CORESTATUS_FILE_LEVEL_ERROR;
			break;
			
		case 2:
			$array_unlink = array(
				$CFG->config['temp'] . CORESTATUS_FILE_LEVEL_DEBUG,
			);
			$file_set = $CFG->config['temp'] . CORESTATUS_FILE_LEVEL_MESSAGE;
			break;
			
		case 3:
			$array_unlink = array();
			$file_set = $CFG->config['temp'] . CORESTATUS_FILE_LEVEL_DEBUG;
			break;
			
		default:
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('unknown debug level', __FILE__, __LINE__);
			return FALSE;
			break; // never reach here
	}
	
	foreach ($array_unlink as $file_unlink) {
		unlink($file_unlink);
	}
	
	try {
		$fp = fopen($file_set, 'w');
		if ($fp) {
			fwrite($fp, 'debug');
			fclose($fp);
		}
		else {
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('open debug level file error', __FILE__, __LINE__);
			return FALSE;
		}
	} catch (Exception $e) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('write debug level file error', __FILE__, __LINE__);
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

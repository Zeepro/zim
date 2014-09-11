<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// call error list if we want
$CI = &get_instance();
$CI->load->helper(array (
		'errorcode',
		'json',
));

if (!defined('PRINTER_FN_CHARGE')) {
	define('PRINTER_FN_CHARGE',			'_charge.gcode');
	define('PRINTER_FN_RETRACT',		'_retract.gcode');
	define('PRINTER_FN_PRINTPRIME_L',	'_print_prime_left.gcode');
	define('PRINTER_FN_PRINTPRIME_R',	'_print_prime_right.gcode');
	define('PRINTER_PRM_TEMPER_L_N',	' -ll ');	// left temperature for other layer (if exist)
	define('PRINTER_PRM_TEMPER_L_F',	' -l ');	// left temperature for first layer (or all layer)
	define('PRINTER_PRM_TEMPER_R_N',	' -rr ');	// right temperature for other layer (if exist)
	define('PRINTER_PRM_TEMPER_R_F',	' -r ');	// right temperature for first layer (or all layer)
	define('PRINTER_PRM_FILE',			' -f ');	// file path
	
// 	define('PRINTER_VALUE_DEFAULT_TEMPER',	230);
}

function Printer_preparePrint($need_prime = TRUE) {
	$cr = 0;
	$gcode_path = '';
	
	$CI = &get_instance();
	$CI->load->helper('printlist');
	
	if ($need_prime == TRUE) {
		$cr = Printer__getFileFromModel(ModelList_codeModelHash(PRINTLIST_MODEL_PRINTPRIME_L),
				$gcode_path, PRINTER_FN_PRINTPRIME_L);
		if ($cr != ERROR_OK) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('prepare print prime left gcode error', __FILE__, __LINE__);
			return $cr;
		}
		$cr = Printer__getFileFromModel(ModelList_codeModelHash(PRINTLIST_MODEL_PRINTPRIME_R),
				$gcode_path, PRINTER_FN_PRINTPRIME_R);
		if ($cr != ERROR_OK) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('prepare print prime right gcode error', __FILE__, __LINE__);
			return $cr;
		}
	}
	else {
		@unlink($CI->config->item('temp') . PRINTER_FN_PRINTPRIME_L);
		@unlink($CI->config->item('temp') . PRINTER_FN_PRINTPRIME_R);
	}
	
// 	$cr = Printer__getFileFromModel(ModelList_codeModelHash(PRINTLIST_MODEL_CHARGE),
// 			$gcode_path, PRINTER_FN_CHARGE);
// 	if ($cr != ERROR_OK) {
// 		$CI->load->helper('printerlog');
// 		PrinterLog_logError('prepare charge gcode error', __FILE__, __LINE__);
// 		return $cr;
// 	}
// 	$cr = Printer__getFileFromModel(ModelList_codeModelHash(PRINTLIST_MODEL_RETRACT),
// 			$gcode_path, PRINTER_FN_RETRACT);
// 	if ($cr != ERROR_OK) {
// 		$CI->load->helper('printerlog');
// 		PrinterLog_logError('prepare retract gcode error', __FILE__, __LINE__);
// 		return $cr;
// 	}
	
	return ERROR_OK;
}

function Printer_printFromPrime($abb_extruder, $first_run = TRUE) {
	$name_prime = '';
	$gcode_path = NULL;
	$ret_val = 0;
	$id_model = '';
	$array_info = array();
	
	$CI = &get_instance();
	$CI->load->helper('printlist');
	
	switch ($abb_extruder) {
		case 'l':
			if ($first_run == TRUE)
				$name_prime = PRINTLIST_MODEL_PRIME_L;
			else
				$name_prime = PRINTLIST_MODEL_REPRIME_L;
			break;
			
		case 'r':
			if ($first_run == TRUE)
				$name_prime = PRINTLIST_MODEL_PRIME_R;
			else
				$name_prime = PRINTLIST_MODEL_REPRIME_R;
			break;
			
		default:
			$CI->load->helper('printerlog');
			PrinterLog_logError('extruder type error in printing prime', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	$id_model = ModelList_codeModelHash($name_prime);
	
	$ret_val = Printer__getFileFromModel($id_model, $gcode_path, NULL, $array_info);
	if (($ret_val == ERROR_OK) && $gcode_path) {
		$array_filament = array();
		
		// modify the temperature of gcode file according to cartridge info
		//TODO test me
		$ret_val = Printer__changeTemperature($gcode_path);
		if ($ret_val != ERROR_OK) {
			return $ret_val;
		}
		
		if (Printer__getLengthFromJson($array_info, $array_filament)) {
			$ret_val = Printer_printFromFile($gcode_path, FALSE, $array_filament);
		}
		else {
			$ret_val = ERROR_INTERNAL;
		}
	}
	
	return $ret_val;
}

function Printer_printFromCalibration() {
	$CI = &get_instance();
	$CI->load->helper('printlist');
	
	return Printer_printFromModel(ModelList_codeModelHash(PRINTLIST_MODEL_CALIBRATION));
}

// function Printer_printFromModel($id_model, $stop_printing = FALSE) {
function Printer_printFromModel($id_model, $array_temper = array()) {
	$gcode_path = NULL;
	$ret_val = 0;
	$array_info = array();
	
	$ret_val = Printer__getFileFromModel($id_model, $gcode_path, NULL, $array_info);
	if (($ret_val == ERROR_OK) && $gcode_path) {
		$array_filament = array();
		
		// temporary change - modify the temperature of gcode file according to cartridge info
		//TODO test me and remove me if it is necessary
// 		$ret_val = Printer__changeTemperature($gcode_path);
		$ret_val = Printer__changeTemperature($gcode_path, $array_temper);
		if ($ret_val != ERROR_OK) {
			return $ret_val;
		}
		if (file_exists($gcode_path . '.new')) {
			$gcode_path .= '.new';
		}
		// temporary change end
		
// 		$ret_val = Printer_printFromFile($gcode_path, TRUE, $stop_printing);
		if (Printer__getLengthFromJson($array_info, $array_filament)) {
			$ret_val = Printer_printFromFile($gcode_path, TRUE, $array_filament);
		}
		else {
			$ret_val = ERROR_INTERNAL;
		}
	}
	
	return $ret_val;
}

function Printer_printFromSlice($array_temper = array()) {
	$ret_val = 0;
	$file_temp_data = NULL;
	$temp_json = array();
	$array_filament = array();
	
	$CI = &get_instance();
	$CI->load->helper('slicer');
	$gcode_path = $CI->config->item('temp') . SLICER_FILE_MODEL;
	
	if (!file_exists($gcode_path)) {
		return ERROR_NO_SLICED;
	}
	
	// check filaments
	//TODO test me
	$CI->load->helper(array('printerstate', 'json'));
	
	$file_temp_data = $CI->config->item('temp') . SLICER_FILE_TEMP_DATA;
	$temp_json = json_read($file_temp_data, TRUE);
	if (isset($temp_json['error'])) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('read temp data file error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		// move all the verification of filament into printFromFile by array_filament
		$data_json = $temp_json['json'];
		foreach ($data_json as $abb_filament => $array_temp) {
			$array_filament[$abb_filament] = $array_temp[PRINTERSTATE_TITLE_NEED_L];
		}
	}
	
	// temporary change
	//TODO remove me if it is necessary
	$ret_val = Printer__changeTemperature($gcode_path, $array_temper);
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}
	if (file_exists($gcode_path . '.new')) {
		$gcode_path .= '.new';
	}
	// temporary change end
	
	$ret_val = Printer_printFromFile($gcode_path, TRUE, $array_filament);
	
	return $ret_val;
}

// function Printer_printFromFile($gcode_path, $need_prime = TRUE, $stop_printing = FALSE) {
function Printer_printFromFile($gcode_path, $need_prime = TRUE, $array_filament = array()) {
	global $CFG;
	$command = '';
	$output = array();
	$ret_val = 0;
	
	$CI = &get_instance();
	$CI->load->helper(array('printerstate', 'errorcode', 'corestatus', 'printerlog', 'detectos'));
	
	// check if we have no file
	if (!file_exists($gcode_path)) {
		return ERROR_INTERNAL;
	}

	// only check if we are in printing when we are not called stopping printing
// 	if ($stop_printing == FALSE) {
		// check if in printing
		$ret_val = PrinterState_checkInPrint();
		if ($ret_val == TRUE) {
// 			return ERROR_IN_PRINT;
			PrinterLog_logMessage('already in printing', __FILE__, __LINE__);
			return ERROR_BUSY_PRINTER;
		}
// 	}
	
	// check extruder number
	if (PrinterState_getNbExtruder() < 2) {
		$tmp_array = array();
		
		$command = $CFG->config['gcanalyser'] . $gcode_path;
		exec($command, $output, $ret_val);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			PrinterLog_logError('gcanalyser error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		$tmp_array = json_decode($output[0]);
		if ($tmp_array['N'] > PrinterState_getNbExtruder()) {
			PrinterLog_logMessage('no enough extruder', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}

	// check if having enough filament
	$ret_val = PrinterState_checkFilaments($array_filament);
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}
	
	// prepare subprinting gcode files
	$ret_val = Printer_preparePrint($need_prime);
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}
	
// 	if ($stop_printing == FALSE) {
		if ($CFG->config['simulator']) {
			// just set temperature for simulation
			PrinterState_setExtruder('r');
			PrinterState_setTemperature(210);
			PrinterState_setExtruder('l');
			PrinterState_setTemperature(200);
			PrinterState_setExtruder('r');
		}
	
		// change status json file
		$ret_val = CoreStatus_setInPrinting();
// 	}
// 	else {
// 		$ret_val = CoreStatus_setInCanceling();
// 	}
	if ($ret_val == FALSE) {
		return ERROR_INTERNAL;
	}

	// pass gcode to printer
//	if (!PrinterState_beforeFileCommand()) {
//		return ERROR_INTERNAL;
//	}
	// use different command for priming
	if ($need_prime == FALSE) {
		$command = PrinterState_getPrintCommand(TRUE, TRUE) . $gcode_path;
	}
	else {
		$command = PrinterState_getPrintCommand() . $gcode_path;
	}
	// 		exec($command, $output, $ret_val);
	// 		if ($ret_val != ERROR_NORMAL_RC_OK) {
	// 			return ERROR_INTERNAL;
	// 		}
	if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
		pclose(popen($command, 'r')); // only for windows arcontrol client
		PrinterLog_logArduino($command);
	}
	else {
// 		exec($command, $output, $ret_val);
		pclose(popen($command . ' > ' . PRINTERSTATE_FILE_PRINTLOG . ' &', 'r'));
// 		if (!PrinterState_filterOutput($output)) {
// 			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
// 			return ERROR_INTERNAL;
// 		}
// 		if ($ret_val != ERROR_NORMAL_RC_OK) {
// 			return $ret_val;
// 		}
// 		PrinterLog_logArduino($command, $output);
		PrinterLog_logArduino($command);
	}
//	if (!PrinterState_afterFileCommand()) {
//		return ERROR_INTERNAL;
//	}

	// reduce the quantity of filament here
	$ret_val = PrinterState_changeFilament();
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}

	return ERROR_OK;
}

function Printer_stopPrint() {
	$CI = &get_instance();
	$CI->load->helper('corestatus');
	
	// check if we are in canceling / printing in json file
	$cr = CoreStatus_checkInIdle($status_current);
	if ($cr == FALSE) {
		if ($status_current == CORESTATUS_VALUE_CANCEL) {
			// in canceling
			return TRUE;
		}
		else if ($status_current != CORESTATUS_VALUE_PRINT) {
			// in other status
			$CI->load->helper('printerlog');
			PrinterLog_logError('no printing / canceling status when calling canceling', __FILE__, __LINE__);
			return FALSE;
		}
		else {
			// in printing
			$CI->load->helper(array('printlist', 'printerstate'));
			
			// call stop printing gcode status
			$cr = PrinterState_stopPrinting();
			if ($cr != ERROR_OK) {
				// log error here
				$CI->load->helper('printerlog');
				PrinterLog_logError('stop gcode failed', __FILE__, __LINE__);
				return FALSE;
			}
			
			// set status in cancelling
			if (!CoreStatus_setInCanceling()) {
				$CI->load->helper('printerlog');
				PrinterLog_logError('can not set status in cancel', __FILE__, __LINE__);
				return FALSE;
			}
// 			// start to call printing of a special model to reset printer
// 			$cr = Printer_printFromModel(ModelList_codeModelHash(PRINTLIST_MODEL_CANCEL), TRUE);
// 			if ($cr == ERROR_OK) {
// 				return TRUE;
// 			}
// 			else {
// 				// log error here
// 				$CI->load->helper('printerlog');
// 				PrinterLog_logError('start printing canceling model failed', __FILE__, __LINE__);
// 				return FALSE;
// 			}
			return TRUE;
		}
	}
	else {
		// in idle
		$CI->load->helper('printerlog');
		PrinterLog_logError('in idle when calling canceling', __FILE__, __LINE__);
		return FALSE;
	}
	
	return FALSE; // never reach here
}

function Printer_pausePrint() {
	$CI = &get_instance();
	$CI->load->helper(array('corestatus', 'printerstate'));
	
	if (CoreStatus_checkInPause()) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('call pause when in pausing', __FILE__, __LINE__);
		return TRUE;
	}
	else {
		$cr = CoreStatus_checkInIdle($status_current);
		if ($cr == FALSE && $status_current == CORESTATUS_VALUE_PRINT) {
			$cr = PrinterState_pausePrinting();
			if ($cr == ERROR_OK) {
				CoreStatus_setInPause();
				return TRUE;
			}
			else {
				$CI->load->helper('printerlog');
				PrinterLog_logError('pause printing error', __FILE__, __LINE__);
			}
		}
		else {
			$CI->load->helper('printerlog');
			PrinterLog_logError('call pause when not in printing: ' . $status_current, __FILE__, __LINE__);
		}
	}
	
	return FALSE;
}

function Printer_resumePrint() {
	$CI = &get_instance();
	$CI->load->helper(array('corestatus', 'printerstate'));

	$cr = CoreStatus_checkInIdle($status_current);
	if ($cr == FALSE && $status_current == CORESTATUS_VALUE_PRINT) {
		if (CoreStatus_checkInPause()) {
			$cr = PrinterState_resumePrinting();
			if ($cr == ERROR_OK) {
				CoreStatus_setInPause(FALSE);
				return TRUE;
			}
			else {
				$CI->load->helper('printerlog');
				PrinterLog_logError('resume printing error', __FILE__, __LINE__);
			}
		}
		else {
			$CI->load->helper('printerlog');
			PrinterLog_logError('call resume when not in pausing', __FILE__, __LINE__);
			return TRUE;
		}
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('call resume when not in printing: ' . $status_current, __FILE__, __LINE__);
	}
	
	return FALSE;
}

// return TRUE only when we are in printing
function Printer_checkPrintStatus(&$return_data) {
	global $CFG;
	$data_status = array();
	$temper_status = array();
	
	$CI = &get_instance();
	$CI->load->helper(array('printerstate', 'corestatus'));
	
// 	// check if we are in printing phase in printing json status file
// 	$ret_val = Printer_getStatus($printing_status);
// 	if ($ret_val == FALSE) {
// 		return FALSE;
// 	}
// 	if ($printing_status != PRINTER_VALUE_STATUS_PRINT) {
// 		return FALSE;
// 	}
	
	// check status if we are not in printing
	$data_status = PrinterState_checkStatusAsArray();
	if ($data_status[PRINTERSTATE_TITLE_STATUS] != CORESTATUS_VALUE_PRINT) {
		// delete printing status json file when we are not in printing
// 		unlink($CFG->config ['conf'] . PRINTER_PRINTING_JSON);
// 		echo 'finish'; // test
		return FALSE;
	}
	
	// get temperatures of extruders
	$temper_status = PrinterState_getExtruderTemperaturesAsArray();
	if (!is_array($temper_status)) {
		// log internal error
		$this->load->helper('printerlog');
		PrinterLog_logError('API error when getting temperatures in printing', __FILE__, __LINE__);
		return FALSE;
	}
	
	$return_data = array(
			'print_percent'	=> $data_status[PRINTERSTATE_TITLE_PERCENT],
			'print_temperL'	=> $temper_status[PRINTERSTATE_LEFT_EXTRUD],
			'print_temperR'	=> $temper_status[PRINTERSTATE_RIGHT_EXTRUD],
	);
	
	// get time remaining if exists
	if (isset($data_status[PRINTERSTATE_TITLE_DURATION])) {
		$return_data['print_remain'] = $data_status[PRINTERSTATE_TITLE_DURATION];
	}
	
	return TRUE;
}

function Printer_checkCancelStatus() {
	$data_status = array();
	$temper_status = array();

	$CI = &get_instance();
	$CI->load->helper(array('printerstate', 'corestatus'));

	// check status if we are not in canceling
	$data_status = PrinterState_checkStatusAsArray();
	if ($data_status[PRINTERSTATE_TITLE_STATUS] != CORESTATUS_VALUE_CANCEL) {
		$CI->load->helper('printerlog');
		PrinterLog_logMessage('not in canceling when checking cancel status', __FILE__, __LINE__);
		return FALSE;
	}
	
	return TRUE;
}

function Printer_checkPauseStatus() {
	$status_current = NULL;
	$temper_status = array();

	$CI = &get_instance();
	$CI->load->helper('corestatus');

	// check status if we are not in canceling
	CoreStatus_checkInIdle($status_current);
	if ($status_current == CORESTATUS_VALUE_PRINT && CoreStatus_checkInPause()) {
		return TRUE;
	}
	
	return FALSE;
}

// internal function
function Printer__getFileFromModel($id_model, &$gcode_path, $filename = NULL, &$array_info = NULL) {
	$model_path = NULL;
	$bz2_path = NULL;
	$command = '';
	$output = array();
	$ret_val = 0;
	
	$CI = &get_instance();
	$CI->load->helper('printlist');
	
	$model_cr = ModelList__find($id_model, $model_path);
	if (($model_cr == ERROR_OK) && $model_path) {
		$ret_val = 0;
		
		// get json info
		if (is_array($array_info)) {
			$json_data = array();
			
			try {
				$json_data = json_read($model_path . PRINTLIST_FILE_JSON, TRUE);
				if ($json_data['error']) {
					throw new Exception('read json error');
				}
			} catch (Exception $e) {
				return ERROR_INTERNAL;
			}
			
			$array_info = $json_data['json'];
		}
//		//if we don't fix the filename of gcode
// 		try {
// 			$json_data = json_read($model_path . PRINTLIST_FILE_JSON);
// 			if ($json_data['error']) {
// 				throw new Exception('read json error');
// 			}
// 		} catch (Exception $e) {
// 			return ERROR_INTERNAL;
// 		}
// 		$gcode_path = $json_data['json'][PRINTLIST_TITLE_GCODE];
		$bz2_path = $model_path . PRINTLIST_FILE_GCODE_BZ2;
		$filename = is_null($filename) ? PRINTLIST_FILE_GCODE : $filename;
		$gcode_path = $CI->config->item('temp') . $filename;
		$command = 'bzip2 -dkcf ' . $bz2_path . ' > ' . $gcode_path;
		@unlink($gcode_path); // delete old file
		exec($command, $output, $ret_val);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		return ERROR_OK;
	}
	else {
		return ERROR_UNKNOWN_MODEL;
	}
	
	return ERROR_OK; // never reach here
}

function Printer__getLengthFromJson($array_info, &$array_filament) {
	$CI = &get_instance();
	$CI->load->helper('printlist');
	
	if (!is_array($array_info)
			|| !array_key_exists(PRINTLIST_TITLE_LENG_F1, $array_info)
			|| !array_key_exists(PRINTLIST_TITLE_LENG_F2, $array_info)) {
		return FALSE;
	}
	$array_filament = array();
	
	if ($array_info[PRINTLIST_TITLE_LENG_F1] > 0) {
		$array_filament['r'] = $array_info[PRINTLIST_TITLE_LENG_F1];
	}
	if ($array_info[PRINTLIST_TITLE_LENG_F2] > 0) {
		$array_filament['l'] = $array_info[PRINTLIST_TITLE_LENG_F2];
	}
	
	return TRUE;
}

function Printer__changeTemperature(&$gcode_path, $array_temper = array()) {
	$temp_r = 0; // right normal temper
	$temp_rs = 0; // right start temper
	$temp_l = 0; // left normal temper
	$temp_ls = 0; // left start temper
	$cr = 0;
	$command = '';
	$output = array();
	$json_cartridge = array();
	$CI = &get_instance();
	
	$CI->load->helper('printerstate');
	
	// temporary change - make it possible to change temperature not according to cartridge
	//TODO remove me when it is necessary
	if (array_key_exists('r', $array_temper)) {
		$temp_r = $array_temper['r'];
		$temp_rs = $temp_r + 10;
// 		if ($temp_r > $temp_rs) {
// 			$temp_rs = $temp_r;
// 		}
	}
	// temporary change end
	else {
		$cr = PrinterState_getCartridgeAsArray($json_cartridge, 'r');
		if ($cr == ERROR_OK) {
			$temp_r = $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER];
			$temp_rs = $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1];
		}
		else if ($cr == ERROR_MISS_RIGT_CART) {
			$CI->load->helper('slicer');
			$temp_r = SLICER_VALUE_DEFAULT_TEMPER;
			$temp_rs = SLICER_VALUE_DEFAULT_FIRST_TEMPER;
// 			$temp_r = $temp_rs = PRINTER_VALUE_DEFAULT_TEMPER;
		}
	}
	
	if ($temp_r * $temp_rs == 0) {
		// we have at least one value not initialised to call change temper program
		return ($cr == ERROR_OK) ? ERROR_INTERNAL : $cr;
	}
	
	if (PrinterState_getNbExtruder() >= 2) {
		// temporary change - make it possible to change temperature not according to cartridge
		//TODO remove me when it is necessary
		if (array_key_exists('l', $array_temper)) {
			$temp_l = $array_temper['l'];
			$temp_ls = $temp_l + 10;
// 			if ($temp_l > $temp_ls) {
// 				$temp_ls = $temp_l;
// 			}
		}
		// temporary change end
		else {
			$cr = PrinterState_getCartridgeAsArray($json_cartridge, 'l');
			if ($cr == ERROR_OK) {
				$temp_l = $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER];
				$temp_ls = $json_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1];
			}
			else if ($cr == ERROR_MISS_LEFT_CART) {
				$CI->load->helper('slicer');
				$temp_l = SLICER_VALUE_DEFAULT_TEMPER;
				$temp_ls = SLICER_VALUE_DEFAULT_FIRST_TEMPER;
// 				$temp_l = $temp_ls = PRINTER_VALUE_DEFAULT_TEMPER;
			}
		}
		
		if ($temp_l * $temp_ls == 0) {
			// we have at least one value not initialised to call change temper program
			return ($cr == ERROR_OK) ? ERROR_INTERNAL : $cr;
		}
	}
	
	$command = $CI->config->item('gcdaemon')
			. PRINTER_PRM_TEMPER_R_F . $temp_rs . PRINTER_PRM_TEMPER_R_N . $temp_r
			. PRINTER_PRM_TEMPER_L_F . $temp_ls . PRINTER_PRM_TEMPER_L_N . $temp_l
			. PRINTER_PRM_FILE . $gcode_path . ' > ' . $gcode_path . '.new';
	
	//TODO remove the debug message after test
	$CI->load->helper('printerlog');
	PrinterLog_logDebug('change temperature: ' . $command, __FILE__, __LINE__);
	
	@unlink($gcode_path . '.new'); // delete old file
	exec($command, $output, $cr);
	if ($cr != ERROR_NORMAL_RC_OK) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('change temperature error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	$gcode_path = $gcode_path . '.new';
	
	return ERROR_OK;
}

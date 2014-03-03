<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// call error list if we want
$CI = &get_instance();
$CI->load->helper(array (
		'errorcode',
		'json',
));

// if (!defined('PRINTER_PRINTING_JSON')) {
// 	define('PRINTER_PRINTING_JSON',	'printing.json');
	
// 	define('PRINTER_TITLE_FILE',		'file');
// 	define('PRINTER_TITLE_STATUS',		'status');
// 	define('PRINTER_TITLE_START_T_L',	'left_start_temperature');
// 	define('PRINTER_TITLE_START_T_R',	'right_start_temperature');
	
// 	define('PRINTER_VALUE_STATUS_HEAT',		'heat');
// 	define('PRINTER_VALUE_STATUS_PRINT',	'print');
	
// }

function Printer_printFromPrime($abb_extruder, $first_run = TRUE) {
	$name_prime = '';
	$gcode_path = NULL;
	$ret_val = 0;
	$id_model = '';
	
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
			PrinterLog_logError('extruder type error in printing prime');
			return ERROR_WRONG_PRM;
			break;
	}
	$id_model = ModelList_codeModelHash($name_prime);
	
	$ret_val = Printer__getFileFromModel($id_model, $gcode_path);
	if (($ret_val == ERROR_OK) && $gcode_path) {
		//TODO modify the temperature of gcode file according to cartridge info
		;
		
		$ret_val = Printer_printFromFile($gcode_path);
	}
	
	return $ret_val;
}

function Printer_printFromModel($id_model, $stop_printing = FALSE) {
	$gcode_path = NULL;
	$ret_val = 0;
	
	$ret_val = Printer__getFileFromModel($id_model, $gcode_path);
	if (($ret_val == ERROR_OK) && $gcode_path) {
		$ret_val = Printer_printFromFile($gcode_path, $stop_printing);
	}
	
	return $ret_val;
}

function Printer_printFromFile($gcode_path, $stop_printing = FALSE) {
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
	if ($stop_printing == FALSE) {
		// check if in printing
		$ret_val = PrinterState_checkInPrint();
		if ($ret_val == TRUE) {
// 			return ERROR_IN_PRINT;
			PrinterLog_logDebug('already in printing');
			return ERROR_BUSY_PRINTER;
		}
	}
	
	// check extruder number
	if (PrinterState_getNbExtruder() < 2) {
		$tmp_array = array();
		
		$command = $CFG->config['gcanalyser'] . $gcode_path;
		exec($command, $output, $ret_val);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			PrinterLog_logError('gcanalyser error');
			return ERROR_INTERNAL;
		}
		$tmp_array = json_decode($output[0]);
		if ($tmp_array['N'] > PrinterState_getNbExtruder()) {
			PrinterLog_logMessage('no enough extruder');
			return ERROR_INTERNAL;
		}
	}

	// check if having enough filament
	//TODO get the quantity of filament needed by file
	$ret_val = PrinterState_checkFilaments();
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}
	
	if ($stop_printing == FALSE) {
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
	}
	else {
		$ret_val = CoreStatus_setInCanceling();
	}
// 	if ($ret_val == FALSE) {
// 		return ERROR_INTERNAL;
// 	}

	// pass gcode to printer
//	if (!PrinterState_beforeFileCommand()) {
//		return ERROR_INTERNAL;
//	}
	$command = PrinterState_getPrintCommand() . $gcode_path;
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
// 			PrinterLog_logError('filter arduino output error');
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
			PrinterLog_logError('no printing / canceling status when calling canceling');
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
				PrinterLog_logError('stop gcode failed');
				return FALSE;
			}
			
			// start to call printing of a special model to reset printer
			$cr = Printer_printFromModel(ModelList_codeModelHash(PRINTLIST_MODEL_CANCEL), TRUE);
			if ($cr == ERROR_OK) {
				return TRUE;
			}
			else {
				// log error here
				$CI->load->helper('printerlog');
				PrinterLog_logError('start printing canceling model failed');
				return FALSE;
			}
		}
	}
	else {
		// in idle
		$CI->load->helper('printerlog');
		PrinterLog_logError('in idle when calling canceling');
		return FALSE;
	}
	
	return FALSE; // never reach here
}

// function Printer_startPrintingStatusFromModel($id_model) {
// 	$gcode_path = NULL;
// 	$ret_val = 0;
	
// 	$ret_val = Printer__getFileFromModel($id_model, $gcode_path);
// 	if (($ret_val == ERROR_OK) && $gcode_path) {
// 		$ret_val = Printer_startPrintingStatusFromFile($gcode_path);
// 	}
	
// 	return $ret_val;
// }

// function Printer_startPrintingStatusFromFile($gcode_path) {
// 	global $CFG;
// 	$start_temper_l = 0;
// 	$start_temper_r = 0;
// 	$fh = 0;
// 	$ret_val = 0;
// 	$json_data = array();
// 	$CI = NULL;
	
// 	// get start temperature
// 	// need update because of Printer__getStartTemperature
// 	$ret_val = Printer__getStartTemperatureFromFile($gcode_path, $start_temper_l, $start_temper_r);
// 	if ($ret_val != ERROR_OK) {
// 		return $ret_val;
// 	}
	
// 	// start to heat extruder
// 	$CI = &get_instance();
// 	$CI->load->helper('printerstate');
// 	$ret_val = PrinterState_setExtruder('r');
// 	if ($ret_val != ERROR_OK) {
// 		return $ret_val;
// 	}
// 	$ret_val = PrinterState_setTemperature($start_temper_r, 'e');
// 	if ($ret_val != ERROR_OK) {
// 		return $ret_val;
// 	}
// 	$ret_val = PrinterState_setExtruder('l');
// 	if ($ret_val != ERROR_OK) {
// 		return $ret_val;
// 	}
// 	$ret_val = PrinterState_setTemperature($start_temper_l, 'e');
// 	if ($ret_val != ERROR_OK) {
// 		return $ret_val;
// 	}
	
// 	// generate and write json file data
// 	$json_data = array (
// 			PRINTER_TITLE_FILE		=> $gcode_path,
// 			PRINTER_TITLE_STATUS	=> PRINTER_VALUE_STATUS_HEAT,
// 			PRINTER_TITLE_START_T_L	=> $start_temper_l,
// 			PRINTER_TITLE_START_T_R	=> $start_temper_r,
// 	);
	
// 	$fh = fopen($CFG->config ['conf'] . PRINTER_PRINTING_JSON, 'w');
// 	fwrite($fh, json_encode($json_data));
// 	fclose($fh);
	
// 	return ERROR_OK;
// }

// // return TRUE only when we have reached extruder's start temperature 
// function Printer_checkStartTemperature(&$return_data) {
// 	global $CFG;
// 	$json_data = array();
// 	$temper_status = array();
// 	$printing_status = '';
// 	$ret_val = 0;
	
// 	$CI = &get_instance();
// 	$CI->load->helper('printerstate');

// 	// check if we are in heating phase in printing json status file
// 	$ret_val = Printer_getStatus($printing_status, $json_data);
// 	if ($ret_val == FALSE) {
// 		return FALSE;
// 	}
// 	if ($printing_status != PRINTER_VALUE_STATUS_HEAT) {
// 		return FALSE;
// 	}
	
// 	$temper_status = PrinterState_getExtruderTemperaturesAsArray();
	
// 	// generate return data array
// 	$return_data = array(
// 			'left_current'	=> $temper_status[PRINTERSTATE_LEFT_EXTRUD],
// 			'left_goal'		=> $json_data[PRINTER_TITLE_START_T_L],
// 			'right_current'	=> $temper_status[PRINTERSTATE_RIGHT_EXTRUD],
// 			'right_goal'	=> $json_data[PRINTER_TITLE_START_T_R],
// 	);
	
// 	if ($temper_status[PRINTERSTATE_LEFT_EXTRUD] == $json_data[PRINTER_TITLE_START_T_L]
// 			&& $temper_status[PRINTERSTATE_RIGHT_EXTRUD] == $json_data[PRINTER_TITLE_START_T_R]) {
// 		// generate and overwrite new printing status json file
// 		$json_data[PRINTER_TITLE_STATUS] = PRINTER_VALUE_STATUS_PRINT;
// 		unset($json_data[PRINTER_TITLE_START_T_L]);
// 		unset($json_data[PRINTER_TITLE_START_T_R]);

// 		$fh = fopen($CFG->config ['conf'] . PRINTER_PRINTING_JSON, 'w');
// 		fwrite($fh, json_encode($json_data));
// 		fclose($fh);
		
// 		return TRUE;
// 	}
// 	else {
// 		return FALSE;
// 	}
// }

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
		PrinterLog_logError('API error when getting temperatures in printing');
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
	global $CFG;
	$data_status = array();
	$temper_status = array();

	$CI = &get_instance();
	$CI->load->helper(array('printerstate', 'corestatus'));

	// check status if we are not in canceling
	$data_status = PrinterState_checkStatusAsArray();
	if ($data_status[PRINTERSTATE_TITLE_STATUS] != CORESTATUS_VALUE_CANCEL) {
		$CI->load->helper('printerlog');
		PrinterLog_logMessage('not in canceling when checking cancel status');
		return FALSE;
	}
	
	return TRUE;
}

// internal function
function Printer__getStartTemperatureFromModel($id_model, &$array_temper) {
	$gcode_path = NULL;
	$ret_val = 0;
	
	$ret_val = Printer__getFileFromModel($id_model, $gcode_path);
	if (($ret_val == ERROR_OK) && $gcode_path) {
		$ret_val = Printer__getStartTemperatureFromFile($gcode_path, $temper_l, $temper_r);
	}
	
	return $ret_val;
}

function Printer__getStartTemperatureFromFile($gcode_path, &$array_temper) {
// 	$command = '';
// 	$output = array();
// 	$ret_val = 0;
	
	//TODO get the right start temperature here
	$lines = file($gcode_path, FILE_SKIP_EMPTY_LINES);
	if (count($lines) == 0) {
		return ERROR_INTERNAL; // file not found
	}
	
	
	
	$temper_l = 200;
	$temper_r = 210;
	
	return ERROR_OK;
}

function Printer__getFileFromModel($id_model, &$gcode_path) {
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
		$gcode_path = $CI->config->item('temp') . PRINTLIST_FILE_GCODE;
		$command = 'bzip2 -dkcf ' . $bz2_path . ' > ' . $gcode_path;
		exec($command, $output, $ret_val);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		return ERROR_OK;
	} else {
		return ERROR_UNKNOWN_MODEL;
	}
	
}

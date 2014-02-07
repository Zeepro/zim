<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// call error list if we want
$CI = &get_instance();
$CI->load->helper(array (
		'errorcode',
		'printerlog', // test
));

if (!defined('PRINTERSTATE_CHECK_STATE')) {
	define('PRINTERSTATE_CHECK_STATE',		' M1600');
	define('PRINTERSTATE_GET_EXTRUD',		' M1601');
	define('PRINTERSTATE_SET_EXTRUDR',		' T0');
	define('PRINTERSTATE_SET_EXTRUDL',		' T1');
// 	define('PRINTERSTATE_GET_TEMPEREXT',	' M1300');
	define('PRINTERSTATE_GET_TEMPEREXT_R',	' M1300');
	define('PRINTERSTATE_GET_TEMPEREXT_L',	' M1301');
	define('PRINTERSTATE_SET_TEMPEREXT',	' M104\ '); // add space in the last
	define('PRINTERSTATE_GET_CARTRIDGER',	' M1602');
	define('PRINTERSTATE_GET_CARTRIDGEL',	' M1603');
	define('PRINTERSTATE_LOAD_FILAMENT_R',	' M1604');
	define('PRINTERSTATE_LOAD_FILAMENT_L',	' M1605');
	define('PRINTERSTATE_UNIN_FILAMENT_R',	' M1606');
	define('PRINTERSTATE_UNIN_FILAMENT_L',	' M1607');
	define('PRINTERSTATE_GET_FILAMENT_R',	' M1608');
	define('PRINTERSTATE_GET_FILAMENT_L',	' M1609');
	define('PRINTERSTATE_PRINT_FILE',		' -f '); // add space in the last
	define('PRINTERSTATE_STOP_PRINT',		' M1000');
	define('PRINTERSTATE_RESET_PRINTER',	' M1100');
	define('PRINTERSTATE_START_SD_WRITE',	' M28\ '); // add space in the last
	define('PRINTERSTATE_STOP_SD_WRITE',	' M29');
	define('PRINTERSTATE_SELECT_SD_FILE',	' M23\ '); // add space in the last
	define('PRINTERSTATE_START_SD_FILE',	' M24');
	define('PRINTERSTATE_DELETE_SD_FILE',	' M30\ '); // add space in the last
	define('PRINTERSTATE_SD_FILENAME',		'test.g'); // fix the name on SD card

	define('PRINTERSTATE_RIGHT_EXTRUD',	0);
	define('PRINTERSTATE_LEFT_EXTRUD',	1);
	define('PRINTERSTATE_TEMPER_MIN_E',	20);
	define('PRINTERSTATE_TEMPER_MAX_E',	250);
	define('PRINTERSTATE_TEMPER_MIN_H',	20);
	define('PRINTERSTATE_TEMPER_MAX_H',	100);

	define('PRINTERSTATE_TITLE_CARTRIDGE',	'type');
	define('PRINTERSTATE_TITLE_MATERIAL',	'material');
	define('PRINTERSTATE_TITLE_COLOR',		'color');
	define('PRINTERSTATE_TITLE_INITIAL',	'initial');
	define('PRINTERSTATE_TITLE_USED',		'used');
	define('PRINTERSTATE_TITLE_EXT_TEMPER',	'temperature');
	define('PRINTERSTATE_TITLE_SETUP_DATE',	'setup');
	define('PRINTERSTATE_TITLE_STATUS',		'status');
	define('PRINTERSTATE_TITLE_PERCENT',	'percentage');
	define('PRINTERSTATE_TITLE_DURATION',	'duration');
	define('PRINTERSTATE_TITLE_VERSION',	'ver');
	define('PRINTERSTATE_TITLE_TYPE',		'type');
	define('PRINTERSTATE_TITLE_SERIAL',		'sn');
	define('PRINTERSTATE_TITLE_NB_EXTRUD',	'extruder');

// 	define('PRINTERSTATE_VALUE_IDLE',				'idle');
// 	define('PRINTERSTATE_VALUE_IN_SLICE',			'slicing');
// 	define('PRINTERSTATE_VALUE_IN_PRINT',			'printing');
	define('PRINTERSTATE_MAGIC_NUMBER',				23567);
	define('PRINTERSTATE_VALUE_CARTRIDGE_NORMAL',	0);
	define('PRINTERSTATE_DESP_CARTRIDGE_NORMAL',	'normal');
	define('PRINTERSTATE_VALUE_CARTRIDGE_REFILL',	1);
	define('PRINTERSTATE_DESP_CARTRIDGE_REFILL',	'refillable');
	define('PRINTERSTATE_VALUE_MATERIAL_PLA',		0);
	define('PRINTERSTATE_DESP_MATERIAL_PLA',		'pla');
	define('PRINTERSTATE_VALUE_MATERIAL_ABS',		1);
	define('PRINTERSTATE_DESP_MATERIAL_ABS',		'abs');
	define('PRINTERSTATE_OFFSET_TEMPER',			100);
	define('PRINTERSTATE_OFFSET_YEAR_SETUP_DATE',	2014);
	define('PRINTERSTATE_VALUE_DEFAULT_COLOR',		'transparent');
	define('PRINTERSTATE_VALUE_OFFSET_TO_CAL_TIME',	10);
	
	define('PRINTERSTATE_PRM_EXTRUDER',				'extruder');
	define('PRINTERSTATE_PRM_TEMPER',				'temp');
	define('PRINTERSTATE_PRM_CARTRIDGE',			'cartridgeinfo');
	define('PRINTERSTATE_PRM_INFO',					'info');
	
	define('PRINTERSTATE_CHANGECART_UNLOAD_F',	'unload_filament');
	define('PRINTERSTATE_CHANGECART_REMOVE_C',	'remove_cartridge');
	define('PRINTERSTATE_CHANGECART_INSERT_C',	'insert_cartridge');
	define('PRINTERSTATE_CHANGECART_REINST_C',	'reinsert_cartridge');
	define('PRINTERSTATE_CHANGECART_LOAD_F',	'load_filament');
	define('PRINTERSTATE_CHANGECART_WAIT_F',	'wait_filament');
	define('PRINTERSTATE_CHANGECART_WAIT_F_C',	'wait_change_filament');
	define('PRINTERSTATE_CHANGECART_NEED_P',	'need_prime');
	define('PRINTERSTATE_CHANGECART_FINISH',	'finish_change');
}

//TODO make all 4 (or 5) simulator switch into 1 function internal (or DectectOS helper)

function PrinterState_getExtruder(&$abb_extruder) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$last_output = NULL;
	$ret_val = 0;
	
	$abb_extruder = NULL;
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		$command = $arcontrol_fullpath . PRINTERSTATE_GET_EXTRUD;
// 		$last_output = system($command, $ret_val);
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output)) {
			PrinterLog_logError('filter arduino output error');
			return ERROR_INTERNAL;
		}
		PrinterLog_LogArduino($command, $output);
		if ($ret_val == ERROR_NORMAL_RC_OK) {
			$last_output = $output[0];
			switch ($last_output) {
				case PRINTERSTATE_LEFT_EXTRUD:
					$abb_extruder = 'l';
					break;
					
				case PRINTERSTATE_RIGHT_EXTRUD:
					$abb_extruder = 'r';
					break;
					
				default:
					return ERROR_INTERNAL;
					break;
			}
		} else {
			PrinterLog_logError('get extruder command error');
			return ERROR_INTERNAL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not get extruder in printing');
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_setExtruder($abb_extruder = 'r') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	switch ($abb_extruder) {
		case 'l':
			$command = $arcontrol_fullpath . PRINTERSTATE_SET_EXTRUDL;
			break;
			
		case 'r':
			$command = $arcontrol_fullpath . PRINTERSTATE_SET_EXTRUDR;
			break;
			
		default:
			PrinterLog_logError('set extruder type error');
			return ERROR_WRONG_PRM;
			break;
	}
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output)) {
			PrinterLog_logError('filter arduino output error');
			return ERROR_INTERNAL;
		}
		PrinterLog_LogArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			PrinterLog_logError('set extruder command error');
			return ERROR_INTERNAL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not set extruder in printing');
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_getTemperature(&$val_temperature, $type = 'e') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$last_output = NULL;
	$ret_val = 0;
	$abb_extruder = '';
	
	switch ($type) {
		case 'e':
			// get current extruder
			$ret_val = PrinterState_getExtruder($abb_extruder);
			if ($ret_val != ERROR_OK) {
				return $ret_val;
			}
			if ($abb_extruder == 'l') {
				$command = $arcontrol_fullpath . PRINTERSTATE_GET_TEMPEREXT_L;
			}
			else if ($abb_extruder == 'r') {
				$command = $arcontrol_fullpath . PRINTERSTATE_GET_TEMPEREXT_R;
			}
			else {
				PrinterLog_logError('extruder type error');
				return ERROR_INTERNAL;
			}
			break;
			
		case 'h':
			//TODO finish this case in future when functions of platform are finished
			$command = 'echo -1'; // let default temperature of platform to be 20 CD
			break;
			
		default:
			PrinterLog_logError('temper type error');
			return ERROR_WRONG_PRM;
			break;
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output)) {
		PrinterLog_logError('filter arduino output error');
		return ERROR_INTERNAL;
	}
	PrinterLog_LogArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get temper command error');
		return ERROR_INTERNAL;
	}
	else {
		$last_output = $output[0];
		$val_temperature = (int)$last_output;
	}
	
	return ERROR_OK;
}

function PrinterState_setTemperature($val_temperature, $type = 'e') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	if ($type == 'e' && $val_temperature >= PRINTERSTATE_TEMPER_MIN_E && $val_temperature <= PRINTERSTATE_TEMPER_MAX_E) {
		$command = $arcontrol_fullpath . PRINTERSTATE_SET_TEMPEREXT . $val_temperature;
	} elseif ($type == 'h' && $val_temperature >= PRINTERSTATE_TEMPER_MIN_H && $val_temperature <= PRINTERSTATE_TEMPER_MAX_H) {
		$command = 'echo ok';
	} else {
		PrinterLog_logError('input parameter error');
		return ERROR_WRONG_PRM;
	}
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		$CI = &get_instance();
		$CI->load->helper('detectos');
		
		if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
			pclose(popen($command, 'r')); // only for windows arcontrol client
			PrinterLog_LogArduino($command);
		}
		else {
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output)) {
				PrinterLog_logError('filter arduino output error');
				return ERROR_INTERNAL;
			}
			PrinterLog_LogArduino($command, $output);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not set temperature in printing');
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_getCartridge(&$json_cartridge, $abb_cartridge = 'r') {
	// normally, no default value here, but we set it to right as default
	$array_data = array();
	$cr = 0;
	
	$cr = PrinterState_getCartridgeAsArray($array_data, $abb_cartridge);
	if ($cr == ERROR_OK) {
		$json_cartridge = json_encode($array_data);
	}
	else {
		$json_cartridge = array();
	}
	
	return $cr;
}

function PrinterState_checkStatus() {
	$data_json = PrinterState_checkStatusAsArray();
	
	return json_encode($data_json);
}

function PrinterState_getInfo() {
	$data_json = PrinterState__getInfoAsArray();
	
	return json_encode($data_json);
}

function PrinterState_getExtruderTemperaturesAsArray() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$ret_val = 0;
	$data_array = array();
	
	foreach (array(
			PRINTERSTATE_GET_TEMPEREXT_L => PRINTERSTATE_LEFT_EXTRUD,
			PRINTERSTATE_GET_TEMPEREXT_R => PRINTERSTATE_RIGHT_EXTRUD,
	) as $parameter_cmd => $data_key ) {
		$output = array();
		
		$command = $arcontrol_fullpath . $parameter_cmd;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output)) {
			PrinterLog_logError('filter arduino output error');
			return ERROR_INTERNAL;
		}
		PrinterLog_LogArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			PrinterLog_logError('get extruder temper (special) command error');
			return ERROR_INTERNAL;
		}
		else {
			$last_output = $output[0];
			$data_array[$data_key] = (int)$last_output;
		}
	}

	return $data_array;
}

function PrinterState_checkInPrint() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$ret_val = 0;

	$command = $arcontrol_fullpath . PRINTERSTATE_CHECK_STATE;
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output)) {
		PrinterLog_logError('filter arduino output error');
		return ERROR_INTERNAL;
	}
	PrinterLog_LogArduino($command, $output);
	if ($ret_val == ERROR_NORMAL_RC_OK && $output && (int)$output[0] == 0) {
		return FALSE;
	} else {
		return TRUE;
	}

	return FALSE;
}

function PrinterState_getCartridgeAsArray(&$json_cartridge, $abb_cartridge) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$last_output = NULL;
	$ret_val = 0;
	$data_json = array();
	$string_tmp = NULL;
	$hex_tmp = 0;
	$time_start = 0;
	$time_pack = 0;
	$hex_checksum = 0;
	$hex_cal = 0;
	
	switch ($abb_cartridge) {
		case 'l':
			$command = $arcontrol_fullpath . PRINTERSTATE_GET_CARTRIDGEL;
			break;
			
		case 'r':
			$command = $arcontrol_fullpath . PRINTERSTATE_GET_CARTRIDGER;
			break;
			
		default:
			PrinterLog_logError('input parameter error, $abb_cartridge: "' . $abb_cartridge . '"');
			return ERROR_WRONG_PRM;
			break;
	}
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output)) {
			PrinterLog_logError('filter arduino output error');
			return ERROR_INTERNAL;
		}
		PrinterLog_LogArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		else {
			$last_output = $output ? $output[0] : NULL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not get info in printing');
		return ERROR_BUSY_PRINTER;
	}
	
	// check and treat output data
	if ($last_output) {
		// checksum 0 to 13
		for($i=0; $i<=13; $i++) {
			$string_tmp = substr($last_output, $i*2, 2);
			$hex_tmp = hexdec($string_tmp);
			$hex_cal = $hex_cal ^ $hex_tmp;
		}
		$hex_checksum = hexdec(substr($last_output, 30, 2));
		if ($hex_cal != $hex_checksum) {
			PrinterLog_logError('checksum error, $hex_cal: ' . $hex_cal . ', $hex_data: ' . $hex_checksum);
			return ERROR_INTERNAL; // checksum failed
		}
		
		// magic number
		$string_tmp = substr($last_output, 0, 4);
		if (hexdec($string_tmp) != PRINTERSTATE_MAGIC_NUMBER) {
			PrinterLog_logError('magic number error');
			return ERROR_INTERNAL;
		}
		
		// type of cartridge
		$string_tmp = substr($last_output, 4, 2);
		$hex_tmp = hexdec($string_tmp);
		switch($hex_tmp) {
			case PRINTERSTATE_VALUE_CARTRIDGE_NORMAL:
				$data_json[PRINTERSTATE_TITLE_CARTRIDGE] = PRINTERSTATE_DESP_CARTRIDGE_NORMAL;
				break;
				
			case PRINTERSTATE_VALUE_CARTRIDGE_REFILL:
				$data_json[PRINTERSTATE_TITLE_CARTRIDGE] = PRINTERSTATE_DESP_CARTRIDGE_REFILL;
				break;
				
			default:
				PrinterLog_logError('cartridge type error');
				return ERROR_INTERNAL;
		}
		
		// type of material
		$string_tmp = substr($last_output, 6, 2);
		$hex_tmp = hexdec($string_tmp);
		switch($hex_tmp) {
			case PRINTERSTATE_VALUE_MATERIAL_PLA:
				$data_json[PRINTERSTATE_TITLE_MATERIAL] = PRINTERSTATE_DESP_MATERIAL_PLA;
				break;
				
			case PRINTERSTATE_VALUE_MATERIAL_ABS:
				$data_json[PRINTERSTATE_TITLE_MATERIAL] = PRINTERSTATE_DESP_MATERIAL_ABS;
				break;
				
			default:
				PrinterLog_logError('filament type error');
				return ERROR_INTERNAL;
		}
		
		// color
		$string_tmp = substr($last_output, 8, 6);
		$data_json[PRINTERSTATE_TITLE_COLOR] = '#' . $string_tmp;
		
		// initial quantity
		$string_tmp = substr($last_output, 14, 4);
		$hex_tmp = hexdec($string_tmp);
		$data_json[PRINTERSTATE_TITLE_INITIAL] = $hex_tmp;
		
		// used quantity
		$string_tmp = substr($last_output, 18, 4);
		$hex_tmp = hexdec($string_tmp);
		$data_json[PRINTERSTATE_TITLE_USED] = $hex_tmp;
		
		// normal extrusion temperature
		$string_tmp = substr($last_output, 22, 2);
		$hex_tmp = hexdec($string_tmp) + PRINTERSTATE_OFFSET_TEMPER;
		$data_json[PRINTERSTATE_TITLE_EXT_TEMPER] = $hex_tmp;
		
		// packing date
		$string_tmp = substr($last_output, 24, 4);
		$hex_tmp = hexdec($string_tmp);
		$time_start = gmmktime(0, 0, 0, 1, 1, PRINTERSTATE_OFFSET_YEAR_SETUP_DATE);
		$time_pack = $time_start + $hex_tmp * 60 * 60 * 24;
		// $data_json[PRINTERSTATE_TITLE_SETUP_DATE] = date("Y-m-d\TH:i:sO", $time_pack);
		$data_json[PRINTERSTATE_TITLE_SETUP_DATE] = date("Y-m-d\TH:i:s\Z", $time_pack);
		
		$json_cartridge = $data_json;
	} else {
		PrinterLog_logMessage('missing cartridge');
		$json_cartridge = array();
		if ($abb_cartridge == 'l') {
			return ERROR_MISS_LEFT_CART;
		}
		else {
			return ERROR_MISS_RIGT_CART;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_checkFilaments($array_filament = array(
		PRINTERSTATE_RIGHT_EXTRUD => 0, PRINTERSTATE_LEFT_EXTRUD => 0),
		&$data_json_array = array()) {
	$ret_val = 0;
	$need_filament = 0;
	$data_json = array();
	
	foreach(array('l', 'r') as $abb_cartridge) {
		$need_filament = ($abb_cartridge == 'r')
		? $array_filament[PRINTERSTATE_RIGHT_EXTRUD] : $array_filament[PRINTERSTATE_LEFT_EXTRUD];
		
		$ret_val = PrinterState_checkFilament($abb_cartridge, $need_filament, $data_json);
		if ($ret_val != ERROR_OK) {
			return $ret_val;
		}
		
		// copy cartridge data
		$data_json_array[$abb_cartridge] = $data_json;
	}
	
	return ERROR_OK;
}

function PrinterState_checkFilament($abb_cartridge, $need_filament = 0, &$data_json = array()) {
	$ret_val = 0;
	$cr = 0;
	
	$ret_val = PrinterState_getCartridgeAsArray($data_json, $abb_cartridge);
	if ($ret_val == ERROR_OK) {
		// check if cartridge is not enough
		$has_filament = $data_json[PRINTERSTATE_TITLE_INITIAL] - $data_json[PRINTERSTATE_TITLE_USED];
		if ($need_filament > $has_filament) {
			PrinterLog_logMessage('low filament error');
			$cr = ($abb_cartridge == 'r') ? ERROR_LOW_RIGT_FILA : ERROR_LOW_LEFT_FILA;
			return $cr;
		}
		
		// check if filament is missing
		$ret_val = PrinterState_getFilamentStatus($abb_cartridge);
		if ($ret_val == FALSE) {
			$cr = ($abb_cartridge == 'r') ? ERROR_MISS_RIGT_FILA : ERROR_MISS_LEFT_FILA;
			return $cr;
		}
	}
	else {
		return $ret_val;
	}
	
	return ERROR_OK;
}

function PrinterState_getPrintCommand() {
	global $CFG;
	$command = $CFG->config['arcontrol_c'] . PRINTERSTATE_PRINT_FILE;
	
	return $command;
}

function PrinterState_beforeFileCommand() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$ret_val = 0;
	
	$command = $arcontrol_fullpath . PRINTERSTATE_START_SD_WRITE . PRINTERSTATE_SD_FILENAME;
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output)) {
		PrinterLog_logError('filter arduino output error');
		return ERROR_INTERNAL;
	}
	PrinterLog_LogArduino($command, $output);
	
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('before gcode file command error');
		return FALSE;
	}
	
	return TRUE;
}

function PrinterState_afterFileCommand() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$ret_val = 0;
	$array_command = array(
			PRINTERSTATE_STOP_SD_WRITE,
			PRINTERSTATE_SELECT_SD_FILE . PRINTERSTATE_SD_FILENAME,
			PRINTERSTATE_START_SD_FILE,
	);
	
	foreach($array_command as $parameter) {
		$command = $arcontrol_fullpath . $parameter;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output)) {
			PrinterLog_logError('filter arduino output error');
			return ERROR_INTERNAL;
		}
		PrinterLog_LogArduino($command, $output);
		
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('after gcode file command error, command: ' . $parameter);
			return FALSE;
		}
	}
	
	return TRUE;
}

function PrinterState_checkStatusAsArray() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$ret_val = 0;
	$data_json = array();
	$time_start = NULL;
	
	// if we need duration, the function that get duration by id is necessary
	// and we must stock print list id somewhere in json file
	$CI = &get_instance();
	$CI->load->helper('corestatus');
	
	$command = $arcontrol_fullpath . PRINTERSTATE_CHECK_STATE;
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output)) {
		$CI->load->helper('printerlog');
		PrinterLog_logError('filter arduino output error');
		return ERROR_INTERNAL;
	}
	PrinterLog_LogArduino($command, $output);
	if ($ret_val == ERROR_NORMAL_RC_OK && $output) {
		// we have right return
		if ((int)$output[0] == 0) {
			// not in printing(?), now we consider it is just idle (no slicing)
			$CI->load->helper('printerlog');
			PrinterLog_logDebug('check in idle - checkstatusasarray');
			$data_json[PRINTERSTATE_TITLE_STATUS] = CORESTATUS_VALUE_IDLE;
		} else {
			// in printing / canceling, then check their difference in json
			CoreStatus_checkInIdle($status_current);
			if ($status_current == CORESTATUS_VALUE_CANCEL) {
				$data_json[PRINTERSTATE_TITLE_STATUS] = CORESTATUS_VALUE_CANCEL;
				return $data_json;
			}
			$data_json[PRINTERSTATE_TITLE_STATUS] = CORESTATUS_VALUE_PRINT;
			$data_json[PRINTERSTATE_TITLE_PERCENT] = $output[0];
			// we can calculate duration by mid(to get total duration) and percentage
		}
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('print check status command error');
		return ERROR_INTERNAL;
	}
	
	// try to calculate time remained when percentage is passed offset
	$ret_val = CoreStatus_getStartPrintTime($time_start);
	if ($ret_val == ERROR_NORMAL_RC_OK || $time_start) {
		if ($data_json[PRINTERSTATE_TITLE_PERCENT] >= PRINTERSTATE_VALUE_OFFSET_TO_CAL_TIME) {
			$percentage_finish = $data_json[PRINTERSTATE_TITLE_PERCENT];
			$time_pass = time() - $time_start;
			
			$data_json[PRINTERSTATE_TITLE_DURATION] = (int)($time_pass / $percentage_finish * (100 - $percentage_finish));
		}
	}
	
	return $data_json;
}

function PrinterState_changeFilament($left_filament = 0, $right_filament = 0) {
	//TODO we need this function to reduce the quantity of filament (add used value)
	// so we also need a function to change the cartridge info
	// in the other hand, when we stop a printing task, how can we get the quantity that was used
	
	return ERROR_OK;
}

function PrinterState_getFilamentStatus($abb_filament) {
	// return TRUE only when filament is loaded
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	switch ($abb_filament) {
		case 'l':
			$command = $arcontrol_fullpath . PRINTERSTATE_GET_FILAMENT_L;
			break;
			
		case 'r':
			$command = $arcontrol_fullpath . PRINTERSTATE_GET_FILAMENT_R;
			break;
			
		default:
			PrinterLog_logError('input filament type error');
			return FALSE;
			break; // never reach here
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output)) {
		PrinterLog_logError('filter arduino output error');
		return ERROR_INTERNAL;
	}
	PrinterLog_LogArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get filament status command error');
		return FALSE;
	}
	else {
		$last_output = $output[0];
		if ($last_output == 'ok') {
			return TRUE;
		}
		else if ($last_output == 'no filament') {
			return FALSE;
		}
		else {
			PrinterLog_logError('get filament api error');
			return FALSE;
		}
	}
}

function PrinterState_loadFilament($abb_filament) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	switch ($abb_filament) {
		case 'l':
			$command = $arcontrol_fullpath . PRINTERSTATE_LOAD_FILAMENT_L;
			break;
				
		case 'r':
			$command = $arcontrol_fullpath . PRINTERSTATE_LOAD_FILAMENT_R;
			break;
				
		default:
			PrinterLog_logError('input filament type error');
			return ERROR_WRONG_PRM;
			break; // never reach here
	}
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
// 		exec($command, $output, $ret_val);
// 		PrinterLog_LogArduino($command, $output);
// 		if ($ret_val != ERROR_NORMAL_RC_OK) {
// 			return ERROR_INTERNAL;
// 		}
		
		// change status json file
		$ret_val = CoreStatus_setInLoading($abb_filament);
		if ($ret_val == FALSE) {
			return ERROR_INTERNAL;
		}
		
		$CI = &get_instance();
		$CI->load->helper('detectos');
		
		if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
			pclose(popen($command, 'r')); // only for windows arcontrol client
			PrinterLog_LogArduino($command); //FIXME we can't check return output when using simulator
		}
		else {
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output)) {
				PrinterLog_logError('filter arduino output error');
				return ERROR_INTERNAL;
			}
			PrinterLog_LogArduino($command, $output);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not load filament in printing');
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_unloadFilament($abb_filament) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	switch ($abb_filament) {
		case 'l':
			$command = $arcontrol_fullpath . PRINTERSTATE_UNIN_FILAMENT_L;
			break;
				
		case 'r':
			$command = $arcontrol_fullpath . PRINTERSTATE_UNIN_FILAMENT_R;
			break;
				
		default:
			PrinterLog_logError('input filament type error');
			return ERROR_WRONG_PRM;
			break; // never reach here
	}
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
// 		exec($command, $output, $ret_val);
// 		PrinterLog_LogArduino($command, $output);
// 		if ($ret_val != ERROR_NORMAL_RC_OK) {
// 			return ERROR_INTERNAL;
// 		}
		
		// change status json file
		$ret_val = CoreStatus_setInUnloading($abb_filament);
		if ($ret_val == FALSE) {
			return ERROR_INTERNAL;
		}
		
		$CI = &get_instance();
		$CI->load->helper('detectos');
		
		if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
			pclose(popen($command, 'r')); // only for windows arcontrol client
			PrinterLog_LogArduino($command); //FIXME we can't check return output when using simulator
		}
		else {
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output)) {
				PrinterLog_logError('filter arduino output error');
				return ERROR_INTERNAL;
			}
			PrinterLog_LogArduino($command, $output);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not unload filament in printing');
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_stopPrinting() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == TRUE) {
		// send stop gcode
		$command = $arcontrol_fullpath . PRINTERSTATE_STOP_PRINT;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output)) {
			PrinterLog_logError('filter arduino output error');
			return ERROR_INTERNAL;
		}
		PrinterLog_LogArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		// print special gcode model to reset printer's temperatures and position
		// we leave this printing call function in Printer_stopPrint()
	} else {
		PrinterLog_logMessage('we are not in printing when calling stop printing');
		return ERROR_NO_PRINT;
	}
	
	return TRUE;
}

function PrinterState_runGcode($gcodes, $need_return = FALSE, &$return_data = '') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$tmpfile_fullpath = $CFG->config['temp'] . '_runGcode.gcode';
	$output = array();
	$command = '';
	$ret_val = 0;
	
	if ($need_return && is_array($gcodes)) {
		foreach ($gcodes as $gcode) {
			$command = $arcontrol_fullpath . ' ' . $gcode;
			//TODO some gcode will not be responsed directly when using simulator
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output)) {
				PrinterLog_logError('filter arduino output error');
				return ERROR_INTERNAL;
			}
// 			if (count($output)) {
// 				$return_data .= $output[0] . "\n";
// 			}
// 			else {
// 				$return_data .= "\n";
// 			}
		}
		foreach ($output as $line) {
			$return_data .= $line . "\n";
		}
	}
	else if (!$need_return && !is_array($gcodes)) {
		$fp = fopen($tmpfile_fullpath, 'w');
		if ($fp) {
			fwrite($fp, $gcodes);
			fclose($fp);
		}
		
		if (!PrinterState_beforeFileCommand()) {
			return ERROR_INTERNAL;
		}
		
		$command = PrinterState_getPrintCommand();
		$command .= $tmpfile_fullpath;
		
		$CI = &get_instance();
		$CI->load->helper('detectos');
		
		if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
			pclose(popen($command, 'r')); // only for windows arcontrol client
			PrinterLog_LogArduino($command); //FIXME we can't check return output when using simulator
		}
		else {
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output)) {
				PrinterLog_logError('filter arduino output error');
				return ERROR_INTERNAL;
			}
			PrinterLog_LogArduino($command, $output);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
		}
		
		if (!PrinterState_afterFileCommand()) {
			return ERROR_INTERNAL;
		}
	}
	else {
		return FALSE;
	}
	
	return TRUE;
}

function PrinterState_filterOutput(&$output) {
	if (!is_array($output)) {
		return FALSE;
	}
	else if (empty($output)) {
		return TRUE;
	}
	else {
		// assign output to temp and empty output array
		$array_tmp = $output;
		$output = array();
		
		// filter empty line
// 		$array_tmp = array_filter($array_tmp, "PrinterState__checkLine");
		
		// filter the output not necessary
		foreach($array_tmp as $line) {
			// jump the empty line
			$line = trim($line, " \t\n\r\0\x0B");
			if ($line == '') {
				continue;
			}
			
			//TODO check it start with [<-] or [->], then filter it
			$line = preg_replace('[\[<-\]]', '', $line, 1);
			$line = preg_replace('[\[->\]]', '', $line, 1);
			$line = trim($line, " \t\n\r\0\x0B");
			$output[] = $line;
		}
		
		// filter the ok message in the end of array
		if (strtolower($output[count($output) - 1]) == 'ok') {
			unset($output[count($output) - 1]);
		}
	}
	
	return TRUE;
}

//internal function
function PrinterState__getInfoAsArray() {
	$json_info = array();
	
	//TODO make me depend on config file
	$json_info = array(
			PRINTERSTATE_TITLE_VERSION		=> 1,
			PRINTERSTATE_TITLE_TYPE			=> 'zim',
			PRINTERSTATE_TITLE_SERIAL		=> 1,
			PRINTERSTATE_TITLE_NB_EXTRUD	=> 2,
	);
	
	return $json_info;
}

// function PrinterState__checkLine($line) {
// 	$line = str_replace(array("\n", "\r"), '', $line);
// 	if ($line == '') {
// 		return FALSE;
// 	}
// 	else {
// 		return TRUE;
// 	}
// }

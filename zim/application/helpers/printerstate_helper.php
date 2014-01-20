<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// call error list if we want
$CI = &get_instance();
$CI->load->helper(array (
		'errorcode',
));

if (!defined('PRINTERSTATE_CHECK_STATE')) {
	define('PRINTERSTATE_CHECK_STATE',		' M1600');
	define('PRINTERSTATE_GET_EXTRUD',		' M1601');
	define('PRINTERSTATE_SET_EXTRUDR',		' T0');
	define('PRINTERSTATE_SET_EXTRUDL',		' T1');
// 	define('PRINTERSTATE_GET_TEMPEREXT',	' M1300');
	define('PRINTERSTATE_GET_TEMPEREXT_R',	' M1300');
	define('PRINTERSTATE_GET_TEMPEREXT_L',	' M1301');
	define('PRINTERSTATE_SET_TEMPEREXT',	' M104 '); // add space in the last
	define('PRINTERSTATE_GET_CARTRIDGER',	' M1602');
	define('PRINTERSTATE_GET_CARTRIDGEL',	' M1603');
	define('PRINTERSTATE_LOAD_FILAMENT_R',	' M1604');
	define('PRINTERSTATE_LOAD_FILAMENT_L',	' M1605');
	define('PRINTERSTATE_UNIN_FILAMENT_R',	' M1606');
	define('PRINTERSTATE_UNIN_FILAMENT_L',	' M1607');
	define('PRINTERSTATE_GET_FILAMENT_R',	' M1608');
	define('PRINTERSTATE_GET_FILAMENT_L',	' M1609');

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

	define('PRINTERSTATE_VALUE_IDLE',				'idle');
	define('PRINTERSTATE_VALUE_IN_SLICE',			'slicing');
	define('PRINTERSTATE_VALUE_IN_PRINT',			'printing');
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
	
	define('PRINTERSTATE_PRM_EXTRUDER',				'extruder');
	define('PRINTERSTATE_PRM_TEMPER',				'temp');
	define('PRINTERSTATE_PRM_CARTRIDGE',			'cartridgeinfo');
	define('PRINTERSTATE_PRM_INFO',					'info');
}

function PrinterState_getExtruder(&$abb_extruder) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
	$command = '';
	$output = array();
	$last_output = NULL;
	$ret_val = 0;
	
	$abb_extruder = NULL;
	
	// check if we are in printing
	$ret_val = PrinterState__checkInPrint();
	if ($ret_val == FALSE) {
		$command = $arcontrol_fullpath . PRINTERSTATE_GET_EXTRUD;
// 		$last_output = system($command, $ret_val);
		exec($command, $output, $ret_val);
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
			}
		} else {
			return ERROR_INTERNAL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_setExtruder($abb_extruder = 'r') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
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
			return ERROR_WRONG_PRM;
			break;
	}
	
	// check if we are in printing
	$ret_val = PrinterState__checkInPrint();
	if ($ret_val == FALSE) {
		exec($command, $output, $ret_val);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_getTemperature(&$val_temperature, $type = 'e') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
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
				return ERROR_INTERNAL;
			}
			break;
			
		case 'h':
			//TODO finish this case in future when functions of platform are finished
			$command = 'echo -1'; // let default temperature of platform to be 20 CD
			break;
			
		default:
			return ERROR_WRONG_PRM;
			break;
	}
	
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
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
	$arcontrol_fullpath = $CFG->config['arcontrol'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	if ($type == 'e' && $val_temperature >= PRINTERSTATE_TEMPER_MIN_E && $val_temperature <= PRINTERSTATE_TEMPER_MAX_E) {
		$command = $arcontrol_fullpath . PRINTERSTATE_SET_TEMPEREXT . $val_temperature;
	} elseif ($type == 'h' && $val_temperature >= PRINTERSTATE_TEMPER_MIN_H && $val_temperature <= PRINTERSTATE_TEMPER_MAX_H) {
		$command = 'echo ok';
	} else {
		return ERROR_WRONG_PRM;
	}
	
	// check if we are in printing
	$ret_val = PrinterState__checkInPrint();
	if ($ret_val == FALSE) {
// 		exec($command, $output, $ret_val);
// 		if ($ret_val != ERROR_NORMAL_RC_OK) {
// 			return ERROR_INTERNAL;
// 		}
		pclose(popen($command, 'r')); // only for windows arcontrol client
	} else {
// 		return ERROR_IN_PRINT;
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_getCartridge(&$json_cartridge, $abb_cartridge = 'r') { //TODO verify in spec: no default value here
	$array_data = array();
	$cr = 0;
	
	$cr = PrinterState__getCartridgeAsArray($array_data, $abb_cartridge);
	if ($cr == ERROR_OK) {
		$json_cartridge = json_encode($array_data);
	}
	else {
		$json_cartridge = array();
	}
	
	return $cr;
}

function PrinterState_checkStatus() {
	$data_json = PrinterState__checkStatusAsArray();
	
	return json_encode($data_json);
}

function PrinterState_getInfo() {
	$data_json = PrinterState__getInfoAsArray();
	
	return json_encode($data_json);
}

function PrinterState_getExtruderTemperaturesAsArray() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
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
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		else {
			$last_output = $output[0];
			$data_array[$data_key] = (int)$last_output;
		}
	}

	return $data_array;
}

//internal function
function PrinterState__checkInPrint() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
	$command = '';
	$output = array();
	$ret_val = 0;

	$command = $arcontrol_fullpath . PRINTERSTATE_CHECK_STATE;
	exec($command, $output, $ret_val);
	if ($ret_val == ERROR_NORMAL_RC_OK && count($output) == 0) {
		return FALSE;
	} else {
		return TRUE;
	}
	
}

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

function PrinterState__getCartridgeAsArray(&$json_cartridge, $abb_cartridge) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
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
			return ERROR_WRONG_PRM;
			break;
	}
	
	// check if we are in printing
	$ret_val = PrinterState__checkInPrint();
	if ($ret_val == FALSE) {
		exec($command, $output, $ret_val);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		else {
			$last_output = $output ? $output[0] : NULL;
		}
	} else {
// 		return ERROR_IN_PRINT;
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
		if ($hex_cal == $hex_checksum) {
			return ERROR_INTERNAL; // checksum failed
		}
		
		// magic number
		$string_tmp = substr($last_output, 0, 4);
		if (hexdec($string_tmp) != PRINTERSTATE_MAGIC_NUMBER) {
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
		$time_pack = $time_start + $hex_tmp * 60 *60 * 24;
		// $data_json[PRINTERSTATE_TITLE_SETUP_DATE] = date("Y-m-d\TH:i:sO", $time_pack);
		$data_json[PRINTERSTATE_TITLE_SETUP_DATE] = date("Y-m-d\TH:i:s\Z", $time_pack);
		
		$json_cartridge = $data_json;
	} else {
		$json_cartridge = array();
		if ($abb_cartridge == 'l') {
			return ERROR_MISS_LEFT_FILA;
		}
		else {
			return ERROR_MISS_RIGT_FILA;
		}
	}
	
	return ERROR_OK;
}

function PrinterState__checkFilament($left_filament = 0, $right_filament = 0) {
	$ret_val = 0;
	$need_filament = 0;
	$cr = 0;
	$has_filament = 0;
	$data_json = array();
	
	foreach(array('l', 'r') as $abb_cartridge) {
		$ret_val = PrinterState__getCartridgeAsArray($data_json, $abb_cartridge);
		if ($ret_val == ERROR_OK) {
			//TODO check if cartridge is missing
			
			// check if cartridge is not enough
			$need_filament = ($abb_cartridge == 'r') ? $right_filament : $left_filament;
			$has_filament = $data_json[PRINTERSTATE_TITLE_INITIAL] - $data_json[PRINTERSTATE_TITLE_USED];
			if ($need_filament < $has_filament) {
				$cr = ($abb_cartridge == 'r') ? ERROR_LOW_RIGT_FILA : ERROR_LOW_LEFT_FILA;
				return $cr;
			}
		}
		else {
			return $ret_val;
		}
	}
	
	return ERROR_OK;
}

function PrinterState__getPrintCommand() {
	global $CFG;
	$command = $CFG->config['arcontrol'] . ' -f ';
	
	return $command;
}

function PrinterState__checkStatusAsArray() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
	$command = '';
	$output = array();
	$ret_val = 0;
	$data_json = array();
	
	// if we need duration, the function that get duration by id is necessary
	// and we must stock print list id somewhere in json file
// 	$CI = &get_instance();
// 	$CI->load->helper('printlist');

	$command = $arcontrol_fullpath . PRINTERSTATE_CHECK_STATE;
	exec($command, $output, $ret_val);
	if ($ret_val == ERROR_NORMAL_RC_OK && count($output) == 0) {
		// not in printing(?), now we consider it is just idle (no slicing)
		$data_json[PRINTERSTATE_TITLE_STATUS] = PRINTERSTATE_VALUE_IDLE;
	} else {
		// in printing
		$data_json[PRINTERSTATE_TITLE_STATUS] = PRINTERSTATE_VALUE_IN_PRINT;
		$data_json[PRINTERSTATE_TITLE_PERCENT] = $output[0];
		// we can calculate duration by mid(to get total duration) and percentage
	}
	
	return $data_json;	
}

function PrinterState__changeFilament($left_filament = 0, $right_filament = 0) {
	//TODO we need this function to reduce the quantity of filament (add used value)
	// so we also need a function to change the cartridge info
	// in the other hand, when we stop a printing task, how can we get the quantity that was used
	return ERROR_OK;
}

function PrinterState__getFilamentStatus($abb_filament) {
	// return TRUE only when filament is loaded
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol'];
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
			return FALSE; //TODO generate a way to indicate internal error
			break; // never reach here
	}
	
	exec($command, $output, $ret_val);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
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
			return FALSE;
		}
	}
}

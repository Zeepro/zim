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
	define('PRINTERSTATE_GET_TEMPEREXT_C',	' M1401');
	define('PRINTERSTATE_GET_ALL_TEMPER',	' M1402');
	define('PRINTERSTATE_SET_TEMPEREXT',	' M104\ '); // add space in the last
	define('PRINTERSTATE_GET_CARTRIDGER',	' M1602');
	define('PRINTERSTATE_GET_CARTRIDGEL',	' M1603');
	define('PRINTERSTATE_LOAD_FILAMENT_R',	' M1604');
	define('PRINTERSTATE_LOAD_FILAMENT_L',	' M1605');
	define('PRINTERSTATE_UNIN_FILAMENT_R',	' M1606');
	define('PRINTERSTATE_UNIN_FILAMENT_L',	' M1607');
	define('PRINTERSTATE_UNLOAD_FILAMENT',	' unload ');
	define('PRINTERSTATE_GET_FILAMENT_R',	' M1608');
	define('PRINTERSTATE_GET_FILAMENT_L',	' M1609');
	define('PRINTERSTATE_PRINT_FILE',		' -f '); // add space in the last
// 	define('PRINTERSTATE_STOP_PRINT',		' M1000');
	define('PRINTERSTATE_STOP_PRINT',		' -s');
	define('PRINTERSTATE_RESET_PRINTER',	' M1100');
	define('PRINTERSTATE_START_SD_WRITE',	' M28\ '); // add space in the last
	define('PRINTERSTATE_STOP_SD_WRITE',	' M29');
	define('PRINTERSTATE_SELECT_SD_FILE',	' M23\ '); // add space in the last
	define('PRINTERSTATE_START_SD_FILE',	' M24');
	define('PRINTERSTATE_DELETE_SD_FILE',	' M30\ '); // add space in the last
	define('PRINTERSTATE_SD_FILENAME',		'test.g'); // fix the name on SD card
	define('PRINTERSTATE_AFTER_UNIN_FILA',	' G99');
	define('PRINTERSTATE_HOMING',			' G28');
	define('PRINTERSTATE_MOVE',				' G1\ '); // add space in the last
	define('PRINTERSTATE_HEAD_LED_ON',		' M1200');
	define('PRINTERSTATE_HEAD_LED_OFF',		' M1201');
	define('PRINTERSTATE_STRIP_LED_ON',		' M1202');
	define('PRINTERSTATE_STRIP_LED_OFF',	' M1203');
	define('PRINTERSTATE_STEPPER_OFF',		' M84');
	define('PRINTERSTATE_PAUSE_PRINT',		' -p');
	define('PRINTERSTATE_RESUME_PRINT',		' -r');
	define('PRINTERSTATE_COLDEXTRUDE_E',	' M302');
	define('PRINTERSTATE_POSITION_RELAT',	' G91');
	define('PRINTERSTATE_POSITION_ABSOL',	' G90');
	define('PRINTERSTATE_VERBATIM',			' -d ');
	define('PRINTERSTATE_PRIME_END',		' -l ');
	define('PRINTERSTATE_EXTRUDE_RELAT',	' M83');
	define('PRINTERSTATE_GET_STRIP_LED',	' M1614');
	define('PRINTERSTATE_GET_TOP_LED',		' M1615');
	define('PRINTERSTATE_GET_ENDSTOPS',		' M119');
	define('PRINTERSTATE_GET_SPEED',		' M1620');
	define('PRINTERSTATE_SET_SPEED',		' M1621\ V');
	define('PRINTERSTATE_GET_ACCELERATION', ' M1623');
	define('PRINTERSTATE_SET_ACCELERATION',	' M1624\ A');
	define('PRINTERSTATE_GET_COLDEXTRUDE',	' M1622');
	define('PRINTERSTATE_GET_MARLIN_VER',	' M1400');
	define('PRINTERSTATE_RFID_POWER_ON',	' M1616');
	define('PRINTERSTATE_RFID_POWER_OFF',	' M1617');
	define('PRINTERSTATE_GET_RFID_POWER',	' M1618');
	define('PRINTERSTATE_SET_CARTRIDGER',	' M1610\ ');
	define('PRINTERSTATE_SET_CARTRIDGEL',	' M1611\ ');
	define('PRINTERSTATE_RAISE_PLATFORM',	' M1905');
	define('PRINTERSTATE_GET_OFFSET_X',		' M1661');
	define('PRINTERSTATE_GET_OFFSET_Y',		' M1662');
	define('PRINTERSTATE_SET_OFFSET',		' M1660');
	define('PRINTERSTATE_OFFSET_X_LABEL',	'\ X');
	define('PRINTERSTATE_OFFSET_Y_LABEL',	'\ Y');
	
	global $CFG;
	if ($CFG->config['simulator']) {
// 		define('PRINTERSTATE_TEMP_PRINT_FILENAME',	'./tmp/printer_percentage'); // fix the name on SD card
		define('PRINTERSTATE_FILE_PRINTLOG',	'./tmp/printlog.log');
		define('PRINTERSTATE_FILE_RESPONSE',	'./tmp/printer_response.log');
		define('PRINTERSTATE_FILE_STOPFILE',	'./tmp/printer_stop');
		define('PRINTERSTATE_FILE_PAUSEFILE',	'./tmp/printer_pause');
		define('PRINTERSTATE_FILE_RESUMEFILE',	'./tmp/printer_resume');
		define('PRINTERSTATE_FILE_UNLOAD_HEAT',	'./tmp/printer_unload_heat');
	}
	else {
// 		define('PRINTERSTATE_TEMP_PRINT_FILENAME',	'/tmp/printer_percentage'); // fix the name on SD card
		define('PRINTERSTATE_FILE_PRINTLOG',	'/tmp/printlog.log');
		define('PRINTERSTATE_FILE_RESPONSE',	'/tmp/printer_response.log');
		define('PRINTERSTATE_FILE_STOPFILE',	'/tmp/printer_stop');
		define('PRINTERSTATE_FILE_PAUSEFILE',	'/tmp/printer_pause');
		define('PRINTERSTATE_FILE_RESUMEFILE',	'/tmp/printer_resume');
		define('PRINTERSTATE_FILE_UNLOAD_HEAT',	'/tmp/printer_unload_heat');
	}
	
	define('PRINTERSTATE_RIGHT_EXTRUD',	0);
	define('PRINTERSTATE_LEFT_EXTRUD',	1);
	define('PRINTERSTATE_TEMPER_MIN_E',	0);
	define('PRINTERSTATE_TEMPER_MAX_E',	260);
	define('PRINTERSTATE_TEMPER_MIN_H',	0);
	define('PRINTERSTATE_TEMPER_MAX_H',	100);
	define('PRINTERSTATE_TEMPER_CHANGE_MAX',	260);
	define('PRINTERSTATE_TEMPER_CHANGE_MIN',	165);
	define('PRINTERSTATE_TEMPER_CHANGE_VAL',	20);
	
	define('PRINTERSTATE_TITLE_CARTRIDGE',	'type');
	define('PRINTERSTATE_TITLE_MATERIAL',	'material');
	define('PRINTERSTATE_TITLE_COLOR',		'color');
	define('PRINTERSTATE_TITLE_INITIAL',	'initial');
	define('PRINTERSTATE_TITLE_USED',		'used');
	define('PRINTERSTATE_TITLE_EXT_TEMPER',	'temperature');
	define('PRINTERSTATE_TITLE_EXT_TEMP_1',	'temperature_first');
	define('PRINTERSTATE_TITLE_SETUP_DATE',	'setup');
	define('PRINTERSTATE_TITLE_STATUS',		'status');
	define('PRINTERSTATE_TITLE_PERCENT',	'percentage');
	define('PRINTERSTATE_TITLE_DURATION',	'duration');
	define('PRINTERSTATE_TITLE_VERSION',	'ver');
	define('PRINTERSTATE_TITLE_VERSION_N',	'ver_next');
	define('PRINTERSTATE_TITLE_TYPE',		'type');
	define('PRINTERSTATE_TITLE_SERIAL',		'sn');
	define('PRINTERSTATE_TITLE_NB_EXTRUD',	'extruder');
	define('PRINTERSTATE_TITLE_LASTERROR',	'e');
	define('PRINTERSTATE_TITLE_NEED_L',		'need');
	define('PRINTERSTATE_TITLE_VER_MARLIN',	'marlin');
	define('PRINTERSTATE_TITLE_SSO_NAME',	'name');
	define('PRINTERSTATE_TITLE_HOSTNAME',	'hostname');
	define('PRINTERSTATE_TITLE_EXTEND_PRM',	'eXtended_parameters');
	define('PRINTERSTATE_TITLE_EXT_TEMP_L',	'l_temperature');
	define('PRINTERSTATE_TITLE_EXT_TEMP_R',	'r_temperature');
	define('PRINTERSTATE_TITLE_EXT_LENG_L',	'l_length');
	define('PRINTERSTATE_TITLE_EXT_LENG_R',	'r_length');
	define('PRINTERSTATE_TITLE_SLICE_ERR',	'slicing_error');
	define('PRINTERSTATE_TITLE_DETAILMSG',	'Message');
	define('PRINTERSTATE_TITLE_PRINT_XMAX',	'xmax');
	define('PRINTERSTATE_TITLE_PRINT_YMAX',	'ymax');
	define('PRINTERSTATE_TITLE_PRINT_ZMAX',	'zmax');
	
	define('PRINTERSTATE_JSON_PRINTER', 		'Printer.json');
	define('PRINTERSTATE_TITLE_JSON_NB_EXTRUD', 'ExtrudersNumber');
	define('PRINTERSTATE_JSON_REFILL_TEMPER',	'RefillTemperature.json');
	define('PRINTERSTATE_FILE_UPDATE_RFID',		'CartridgeUpdate.json');
	
	define('PRINTERSTATE_MAGIC_NUMBER_V1',			23567); //v1.0
	define('PRINTERSTATE_MAGIC_NUMBER_V2',			23568); //v1.1
	define('PRINTERSTATE_MAGIC_NUMBER_V3',			23569); //v1.2
	define('PRINTERSTATE_MAGIC_NUMBER_V4',			23570); //v1.3
	define('PRINTERSTATE_VALUE_CARTRIDGE_NORMAL',	0);
	define('PRINTERSTATE_DESP_CARTRIDGE_NORMAL',	'normal');
	define('PRINTERSTATE_VALUE_CARTRIDGE_REFILL',	1);
	define('PRINTERSTATE_DESP_CARTRIDGE_REFILL',	'refillable');
	define('PRINTERSTATE_VALUE_MATERIAL_PLA',		0);
	define('PRINTERSTATE_DESP_MATERIAL_PLA',		'pla');
	define('PRINTERSTATE_VALUE_MATERIAL_ABS',		1);
	define('PRINTERSTATE_DESP_MATERIAL_ABS',		'abs');
	define('PRINTERSTATE_VALUE_MATERIAL_PVA',		2);
	define('PRINTERSTATE_DESP_MATERIAL_PVA',		'pva');
	define('PRINTERSTATE_OFFSET_TEMPER',			100);
	define('PRINTERSTATE_OFFSET_TEMPER_V2',			150);
	define('PRINTERSTATE_OFFSET_YEAR_SETUP_DATE',	2014);
	define('PRINTERSTATE_VALUE_DEFAULT_COLOR',		'transparent');
	define('PRINTERSTATE_VALUE_OFFSET_TO_CAL_TIME',	10);
	define('PRINTERSTATE_CARTRIDGE_ERR_MAGIC',		1);
	define('PRINTERSTATE_CARTRIDGE_ERR_CRC',		2);
	define('PRINTERSTATE_CARTRIDGE_ERR_CART',		3);
	define('PRINTERSTATE_CARTRIDGE_ERR_FILA',		4);
	
	define('PRINTERSTATE_VALUE_DEFAULT_EXTRUD',				5);
	define('PRINTERSTATE_VALUE_OFFSET_TO_CHECK_LOAD',		90);
	define('PRINTERSTATE_VALUE_OFFSET_TO_CHECK_UNLOAD',		10);
	define('PRINTERSTATE_VALUE_TIMEOUT_TO_CHECK_LOAD',		180);
	define('PRINTERSTATE_VALUE_TIMEOUT_TO_CHECK_UNLOAD',	180);
	define('PRINTERSTATE_VALUE_TIMEOUT_UNLOAD_HEAT',		600);
	define('PRINTERSTATE_VALUE_ENDSTOP_OPEN',				'open');
	define('PRINTERSTATE_VALUE_MAXTEMPER_BEFORE_UNLOAD',	50);
	define('PRINTERSTATE_VALUE_FACTOR_NOZZLE_OFFSET',		10);
	
	define('PRINTERSTATE_PRM_EXTRUDER',			'extruder');
	define('PRINTERSTATE_PRM_TEMPER',			'temp');
	define('PRINTERSTATE_PRM_CARTRIDGE',		'cartridgeinfo');
	define('PRINTERSTATE_PRM_INFO',				'info');
	define('PRINTERSTATE_PRM_ACCELERATION',		'acceleration');
	define('PRINTERSTATE_PRM_SPEED_MOVE',		'speed');
	define('PRINTERSTATE_PRM_SPEED_EXTRUDE',	'extrusionspeed');
	define('PRINTERSTATE_PRM_COLDEXTRUSION',	'coldextrusion');
	define('PRINTERSTATE_PRM_STRIPLED',			'stripled');
	define('PRINTERSTATE_PRM_HEADLED',			'headlight');
	define('PRINTERSTATE_PRM_MOTOR_OFF',		'motor');
	define('PRINTERSTATE_PRM_ENDSTOP',			'endstop');
	define('PRINTERSTATE_PRM_FILAMENT',			'filament');
	define('PRINTERSTATE_PRM_OFFSET',			'offsetadjustment');
	
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
	$ret_val = FALSE; // PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		$command = $arcontrol_fullpath . PRINTERSTATE_GET_EXTRUD;
// 		$last_output = system($command, $ret_val);
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
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
			PrinterLog_logError('get extruder command error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not get extruder in printing', __FILE__, __LINE__);
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
			PrinterLog_logError('set extruder type error', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			PrinterLog_logError('set extruder command error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not set extruder in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_getTemperature(&$val_temperature, $type = 'e', $abb_extruder = NULL) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$last_output = NULL;
	$ret_val = 0;
// 	$abb_extruder = '';
	
	switch ($type) {
		case 'e':
			if ($abb_extruder == NULL) {
			// get current extruder
				$command = $arcontrol_fullpath . PRINTERSTATE_GET_TEMPEREXT_C;
			}
// 			$ret_val = PrinterState_getExtruder($abb_extruder);
// 			if ($ret_val != ERROR_OK) {
// 				return $ret_val;
// 			}
			else if ($abb_extruder == 'l') {
				if (PrinterState_getNbExtruder() <= 1) {
					return ERROR_WRONG_PRM; //ERROR_INTERNAL
				}
				$command = $arcontrol_fullpath . PRINTERSTATE_GET_TEMPEREXT_L;
			}
			else if ($abb_extruder == 'r') {
				$command = $arcontrol_fullpath . PRINTERSTATE_GET_TEMPEREXT_R;
			}
			else {
				PrinterLog_logError('extruder type error', __FILE__, __LINE__);
				return ERROR_INTERNAL;
			}
			break;
			
		case 'h':
			//TODO finish this case in future when functions of platform are finished
			$command = 'echo -1'; // let default temperature of platform to be 20 CD
			break;
			
		default:
			PrinterLog_logError('temper type error', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get temper command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$last_output = $output[0];
		$val_temperature = (int)$last_output;
	}
	
	return ERROR_OK;
}

function PrinterState_setTemperature($val_temperature, $type = 'e', $abb_extruder = NULL) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	if ($type == 'e' && $val_temperature >= PRINTERSTATE_TEMPER_MIN_E && $val_temperature <= PRINTERSTATE_TEMPER_MAX_E) {
		$command = $arcontrol_fullpath . PRINTERSTATE_SET_TEMPEREXT . 'S' . $val_temperature;
		switch ($abb_extruder) {
			case 'l':
				$command .= '\\' . PRINTERSTATE_SET_EXTRUDL;
				break;
				
			case 'r':
				$command .= '\\' . PRINTERSTATE_SET_EXTRUDR;
				break;
				
			default:
				break;
		}
	} elseif ($type == 'h' && $val_temperature >= PRINTERSTATE_TEMPER_MIN_H && $val_temperature <= PRINTERSTATE_TEMPER_MAX_H) {
		$command = 'echo ok';
	} else {
		PrinterLog_logError('input parameter error', __FILE__, __LINE__);
		return ERROR_WRONG_PRM;
	}
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		$CI = &get_instance();
		$CI->load->helper('detectos');
		
		if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
			// remove the symbol "\" for simulator
			$command = str_replace('\ ', ' ', $command);
			
			pclose(popen($command, 'r')); // only for windows arcontrol client
			PrinterLog_logArduino($command);
		}
		else {
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output, $command)) {
				PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
				return ERROR_INTERNAL;
			}
			PrinterLog_logArduino($command, $output);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not set temperature in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_getCartridge(&$json_cartridge, $abb_cartridge = 'r', $power_off = TRUE) {
	// normally, no default value here, but we set it to right as default
	$array_data = array();
	$cr = 0;
	
	$cr = PrinterState_getCartridgeAsArray($array_data, $abb_cartridge, $power_off);
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
	$data_json = PrinterState_getInfoAsArray();
	
	return json_encode($data_json);
}

function PrinterState_getExtruderTemperaturesAsArray() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$ret_val = 0;
	$data_array = array();
	$output = array();
	
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_ALL_TEMPER;
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	
	if (count($output) > 0) {
		$ret_val = ERROR_OK;
		$explode_array = explode('-', $output[0]);
		if (count($explode_array) == 0) {
			$ret_val = ERROR_INTERNAL;
			PrinterLog_logError('no extruder detected, context: ' . $output[0], __FILE__, __LINE__);
		}
		foreach ($explode_array as $key_value) {
			$tmp_array = explode(':', $key_value);
			if (count($tmp_array) != 2) {
				$ret_val = ERROR_INTERNAL;
				PrinterLog_logError('no correct structure detected, context: ' . $key_value, __FILE__, __LINE__);
				break;
			}
			$abb_filament = PrinterState_temperatureAbb2Number(trim($tmp_array[0]));
			$data_array[$abb_filament] = ceil($tmp_array[1]);
		}
		if ($ret_val != ERROR_OK) {
			return $ret_val;
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
//	exec($command, $output, $ret_val);
//	if (!PrinterState_filterOutput($output, $command)) {
//		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
//		return ERROR_INTERNAL;
//	}
	PrinterLog_logArduino($command, $output);
//	if ($ret_val == ERROR_NORMAL_RC_OK && $output && (int)$output[0] == 0) {
//		return FALSE;
//	} else {
//		return TRUE;
//	}
	if(file_exists($CFG->config['printstatus'])) {
		return TRUE;
	} else {
		return FALSE;
	}

	return FALSE;
}

function PrinterState_setCartridgeCode($code_cartridge, $abb_cartridge, $power_off = TRUE) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$ret_val = 0;
	$CI = &get_instance();
	$CI->load->helper('detectos');
	
	switch ($abb_cartridge) {
		case 'l':
			$command = $arcontrol_fullpath . PRINTERSTATE_SET_CARTRIDGEL;
			break;
			
		case 'r':
			$command = $arcontrol_fullpath . PRINTERSTATE_SET_CARTRIDGER;
			break;
			
		default:
			PrinterLog_logError('input parameter error, $abb_cartridge: "' . $abb_cartridge . '"', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	$command .= $code_cartridge;
	
	if ($CFG->config['simulator']) {
		// remove the symbol "\" for simulator
		$command = str_replace('\ ', ' ', $command);
	}
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not get info in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_getCartridgeCode(&$code_cartridge, $abb_cartridge, $power_off = TRUE) {
	global $CFG;
	global $PRINTER;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$ret_val = 0;
	
	switch ($abb_cartridge) {
		case 'l':
			$command = $arcontrol_fullpath . PRINTERSTATE_GET_CARTRIDGEL;
			break;
			
		case 'r':
			$command = $arcontrol_fullpath . PRINTERSTATE_GET_CARTRIDGER;
			break;
			
		default:
			PrinterLog_logError('input parameter error, $abb_cartridge: "' . $abb_cartridge . '"', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		// check already read or not
		if (!is_array($PRINTER)) {
			$PRINTER = array();
		}
		if (array_key_exists($abb_cartridge, $PRINTER)
				&& array_key_exists(PRINTERSTATE_PRM_CARTRIDGE, $PRINTER[$abb_cartridge])) {
			$code_cartridge = $PRINTER[$abb_cartridge][PRINTERSTATE_PRM_CARTRIDGE];
		}
		else {
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output, $command)) {
				PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
				return ERROR_INTERNAL;
			}
			PrinterLog_logArduino($command, $output);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
			else {
				$code_cartridge = $output ? $output[0] : NULL;
				
				// rewrite cartridge when necessary
				if (strlen($code_cartridge) == 32) {
					PrinterState__updateCartridge($code_cartridge, $abb_cartridge);
				}
				
				$PRINTER[$abb_cartridge][PRINTERSTATE_PRM_CARTRIDGE] = $code_cartridge;
			}
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not get info in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	if ($power_off == TRUE) {
		$ret_val = PrinterState_setRFIDPower(FALSE);
		if ($ret_val != ERROR_OK) {
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_getCartridgeAsArray(&$json_cartridge, $abb_cartridge, $power_off = TRUE, $error_type = NULL) {
	$last_output = NULL;
	$ret_val = 0;
	
	$ret_val = PrinterState_getCartridgeCode($last_output, $abb_cartridge, $power_off);
	if ($ret_val != ERROR_OK) {
		return $ret_val;
	}
	
	// check and treat output data
	if ($last_output && strlen($last_output) == 32) {
		$version_rfid = 0;
		$string_tmp = NULL;
		$hex_checksum = 0;
		$hex_cal = 0;
		$hex_tmp = 0;
		$time_start = 0;
		$time_pack = 0;
		$data_json = array();
		
		// checksum 0 to 14
		for($i=0; $i<=14; $i++) {
			$string_tmp = substr($last_output, $i*2, 2);
			$hex_tmp = hexdec($string_tmp);
			$hex_cal = $hex_cal ^ $hex_tmp;
		}
		$hex_checksum = hexdec(substr($last_output, 30, 2));
		if ($hex_cal != $hex_checksum) {
			PrinterLog_logError('checksum error, $hex_cal: ' . $hex_cal . ', $hex_data: ' . $hex_checksum, __FILE__, __LINE__);
			$error_type = PRINTERSTATE_CARTRIDGE_ERR_CRC;
			return ERROR_INTERNAL; // checksum failed
		}
		
		// magic number
		$string_tmp = substr($last_output, 0, 4);
		switch (hexdec($string_tmp)) {
			case PRINTERSTATE_MAGIC_NUMBER_V1:
				$version_rfid = 1;
				break;
				
			case PRINTERSTATE_MAGIC_NUMBER_V2:
				$version_rfid = 2;
				break;
				
			case PRINTERSTATE_MAGIC_NUMBER_V3:
				$version_rfid = 3;
				break;
				
			case PRINTERSTATE_MAGIC_NUMBER_V4:
				$version_rfid = 4;
				break;
				
			default:
				PrinterLog_logError('magic number error', __FILE__, __LINE__);
				$error_type = PRINTERSTATE_CARTRIDGE_ERR_MAGIC;
				return ERROR_INTERNAL;
				break;
		}
		
		if ($version_rfid <= 3) {
			$length_cartridge = 2;
			$offset_material = 6;
			$length_material = 2;
			$offset_color = 8;
			$length_color = 6;
			$offset_init = 14;
			if ($version_rfid == 1) {
				$length_init = 4;
				$offset_used = 18;
				$length_used = 4;
				$offset_temp = 22;
				$length_temp = 2;
				$offset_pack = 24;
				$length_pack = 4;
			}
			else if ($version_rfid == 2) { //$version_rfid == 2
				$length_init = 5;
				$offset_used = 19;
				$length_used = 5;
				$offset_temp = 24;
				$length_temp = 2;
				$offset_pack = 26;
				$length_pack = 4;
			}
			else if ($version_rfid == 3) { //$version_rfid == 3
				$length_init = 5;
				$offset_used = 19;
				$length_used = 5;
				$offset_temp = 24;
				$length_temp = 1;
				$offset_temp_f = 25;
				$length_temp_f = 1;
				$offset_pack = 26;
				$length_pack = 4;
			}
		}
		else { //$version_rfid == 4
			$length_cartridge = 1;
			$offset_material = 5;
			$length_material = 1;
			$offset_color = 6;
			$length_color = 6;
			$offset_init = 12;
			$length_init = 5;
			$offset_used = 17;
			$length_used = 5;
			$offset_temp = 22;
			$length_temp = 2;
			$offset_temp_f = 24;
			$length_temp_f = 2;
			$offset_pack = 26;
			$length_pack = 4;
		}
		
		// type of cartridge
		$string_tmp = substr($last_output, 4, $length_cartridge);
		$hex_tmp = hexdec($string_tmp);
		switch($hex_tmp) {
			case PRINTERSTATE_VALUE_CARTRIDGE_NORMAL:
				$data_json[PRINTERSTATE_TITLE_CARTRIDGE] = PRINTERSTATE_DESP_CARTRIDGE_NORMAL;
				break;
				
			case PRINTERSTATE_VALUE_CARTRIDGE_REFILL:
				$data_json[PRINTERSTATE_TITLE_CARTRIDGE] = PRINTERSTATE_DESP_CARTRIDGE_REFILL;
				break;
				
			default:
				PrinterLog_logError('cartridge type error', __FILE__, __LINE__);
				$error_type = PRINTERSTATE_CARTRIDGE_ERR_CART;
				return ERROR_INTERNAL;
		}
		
		// type of material
		$string_tmp = substr($last_output, $offset_material, $length_material);
		$hex_tmp = hexdec($string_tmp);
		switch($hex_tmp) {
			case PRINTERSTATE_VALUE_MATERIAL_PLA:
				$data_json[PRINTERSTATE_TITLE_MATERIAL] = PRINTERSTATE_DESP_MATERIAL_PLA;
				break;
				
			case PRINTERSTATE_VALUE_MATERIAL_ABS:
				$data_json[PRINTERSTATE_TITLE_MATERIAL] = PRINTERSTATE_DESP_MATERIAL_ABS;
				break;
				
			case PRINTERSTATE_VALUE_MATERIAL_PVA:
				$data_json[PRINTERSTATE_TITLE_MATERIAL] = PRINTERSTATE_DESP_MATERIAL_PVA;
				break;
				
			default:
				PrinterLog_logError('filament type error', __FILE__, __LINE__);
				$error_type = PRINTERSTATE_CARTRIDGE_ERR_FILA;
				return ERROR_INTERNAL;
		}
		
		// color
		$string_tmp = substr($last_output, $offset_color, $length_color);
		$data_json[PRINTERSTATE_TITLE_COLOR] = '#' . $string_tmp;
		
		// initial quantity
		$string_tmp = substr($last_output, $offset_init, $length_init);
		$hex_tmp = hexdec($string_tmp);
		$data_json[PRINTERSTATE_TITLE_INITIAL] = $hex_tmp;
		
		// used quantity
		$string_tmp = substr($last_output, $offset_used, $length_used);
		$hex_tmp = hexdec($string_tmp);
		$data_json[PRINTERSTATE_TITLE_USED] = $hex_tmp;
		
		// normal extrusion temperature
		$string_tmp = substr($last_output, $offset_temp, $length_temp);
		if ($version_rfid == 3) {
			$hex_tmp = hexdec($string_tmp) + PRINTERSTATE_OFFSET_TEMPER_V2;
		}
		else {
			$hex_tmp = hexdec($string_tmp) + PRINTERSTATE_OFFSET_TEMPER;
		}
		$data_json[PRINTERSTATE_TITLE_EXT_TEMPER] = $hex_tmp;
		
		// first layer extrusion temperature
		if ($version_rfid > 2) {
			$string_tmp = substr($last_output, $offset_temp_f, $length_temp_f);
			if ($version_rfid == 3) {
				$hex_tmp = hexdec($string_tmp) + PRINTERSTATE_OFFSET_TEMPER_V2;
			}
			else {
				$hex_tmp = hexdec($string_tmp) + PRINTERSTATE_OFFSET_TEMPER;
			}
			$data_json[PRINTERSTATE_TITLE_EXT_TEMP_1] = $hex_tmp;
		}
		else {
			$data_json[PRINTERSTATE_TITLE_EXT_TEMP_1] = $data_json[PRINTERSTATE_TITLE_EXT_TEMPER] + 10;
		}
		
		// packing date
		//TODO argument max acceptable date (Unix timestamp, 2038-01-19)
		$string_tmp = substr($last_output, $offset_pack, $length_pack);
		$hex_tmp = hexdec($string_tmp);
		$time_start = gmmktime(0, 0, 0, 1, 1, PRINTERSTATE_OFFSET_YEAR_SETUP_DATE);
		$time_pack = $time_start + $hex_tmp * 60 * 60 * 24;
		// $data_json[PRINTERSTATE_TITLE_SETUP_DATE] = date("Y-m-d\TH:i:sO", $time_pack);
		$data_json[PRINTERSTATE_TITLE_SETUP_DATE] = date("Y-m-d\TH:i:s\Z", $time_pack);
		
		// change temperature values to user settings if cartridge is refillable
		//TODO test me
		if ($data_json[PRINTERSTATE_TITLE_TYPE] == PRINTERSTATE_DESP_CARTRIDGE_REFILL) {
			$CI = &get_instance();
			$tmp_array = array();
			$temper_filepath = $CI->config->item('conf') . PRINTERSTATE_JSON_REFILL_TEMPER;
			
			$CI->load->helper('json');
			try {
				$tmp_array = json_read($temper_filepath, TRUE);
				if ($tmp_array['error']) {
					throw new Exception('read json error');
				}
			} catch (Exception $e) {
				$CI->load->helper('printerlog');
				PrinterLog_logMessage('read refillable cartridge user temperature json error', __FILE__, __LINE__);
				
				// log error message and use the default temperature in RFID
				$json_cartridge = $data_json;
				return ERROR_OK;
			}
			
			$data_json[PRINTERSTATE_TITLE_EXT_TEMPER] = $tmp_array['json'][$abb_cartridge][PRINTERSTATE_TITLE_EXT_TEMPER];
			$data_json[PRINTERSTATE_TITLE_EXT_TEMP_1] = $tmp_array['json'][$abb_cartridge][PRINTERSTATE_TITLE_EXT_TEMP_1];
		}
		
		$json_cartridge = $data_json;
	} else {
		PrinterLog_logMessage('missing cartridge', __FILE__, __LINE__);
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

function PrinterState_setCartridgeAsArray($abb_cartridge, $data_json = array(), $power_off = TRUE) {
	$temp_hex = NULL;
	$time_code = NULL;
	$time_offset = NULL;
	$time_rfid = NULL;
	$code_write = dechex(PRINTERSTATE_MAGIC_NUMBER_V4); // use always the latest version
	
	// check necessary key
	foreach (array(PRINTERSTATE_TITLE_INITIAL, PRINTERSTATE_TITLE_EXT_TEMPER) as $test_key) {
		if (!array_key_exists($test_key, $data_json)) {
			return ERROR_MISS_PRM;
		}
	}
	
	// type of cartridge
	if (array_key_exists(PRINTERSTATE_TITLE_TYPE, $data_json)) {
		$code_write .= dechex($data_json[PRINTERSTATE_TITLE_TYPE]);
	}
	else {
		$code_write .= dechex(PRINTERSTATE_VALUE_CARTRIDGE_NORMAL); // normal as default
	}
	// type of material
	if (array_key_exists(PRINTERSTATE_TITLE_MATERIAL, $data_json)) {
		$code_write .= dechex($data_json[PRINTERSTATE_TITLE_MATERIAL]);
	}
	else {
		$code_write .= dechex(PRINTERSTATE_VALUE_MATERIAL_ABS); // abs as default
	}
	// color
	if (array_key_exists(PRINTERSTATE_TITLE_COLOR, $data_json)) {
		$code_write .= $data_json[PRINTERSTATE_TITLE_COLOR];
	}
	else {
		$code_write .= 'FFFFFF'; // white as default
	}
	// inital quantity
	$temp_hex = dechex($data_json[PRINTERSTATE_TITLE_INITIAL]);
	while (strlen($temp_hex) < 5) {
		$temp_hex = '0' . $temp_hex;
	}
	$code_write .= $temp_hex;
	// used quantity
	if (array_key_exists(PRINTERSTATE_TITLE_USED, $data_json)) {
		$temp_hex = dechex($data_json[PRINTERSTATE_TITLE_USED]);
		while (strlen($temp_hex) < 5) {
			$temp_hex = '0' . $temp_hex;
		}
		$code_write .= $temp_hex;
	}
	else {
		$code_write .= '00000'; // 0 as default
	}
	// normal extrusion temperature
	$temp_hex = dechex($data_json[PRINTERSTATE_TITLE_EXT_TEMPER] - PRINTERSTATE_OFFSET_TEMPER);
	if (strlen($temp_hex) == 1) {
		$temp_hex = '0' . $temp_hex;
	}
	$code_write .= $temp_hex;
	// first layer extrusion temperature
	$temp_hex = NULL;
	if (array_key_exists(PRINTERSTATE_TITLE_EXT_TEMP_1, $data_json)) {
		$temp_hex = dechex($data_json[PRINTERSTATE_TITLE_EXT_TEMP_1] - PRINTERSTATE_OFFSET_TEMPER);
	}
	else {
		$temp_hex = dechex($data_json[PRINTERSTATE_TITLE_EXT_TEMPER] - PRINTERSTATE_OFFSET_TEMPER + 10);
	}
	if (strlen($temp_hex) == 1) {
		$temp_hex = '0' . $temp_hex;
	}
	$code_write .= $temp_hex;
	// package date
	if (array_key_exists(PRINTERSTATE_TITLE_SETUP_DATE, $data_json)) {
		$time_code = dechex($data_json[PRINTERSTATE_TITLE_SETUP_DATE]);
	}
	else {
		$time_code = time(); // use current date as default
	}
	$time_offset = gmmktime(0, 0, 0, 1, 1, PRINTERSTATE_OFFSET_YEAR_SETUP_DATE);
	$time_rfid = ($time_code - $time_offset) / 60 / 60 / 24;
	if ($time_rfid < 0) $time_rfid = 0; // start at offset if we have a wrong time
	$temp_hex = strtoupper(dechex($time_rfid));
	while (strlen($temp_hex) < 4) {
		$temp_hex = '0' . $temp_hex;
	}
	$code_write .= $temp_hex;
	
	// check length
	if (strlen($code_write) != 30) {
		return ERROR_INTERNAL;
	}
	
	// Checksum
	$temp_hex = 0;
	for($i=0; $i<=14; $i++) {
		$string_tmp = substr($code_write, $i*2, 2);
		$hex_tmp = hexdec($string_tmp);
		$temp_hex = $temp_hex ^ $hex_tmp;
	}
	$temp_hex = dechex($temp_hex);
	if (strlen($temp_hex) == 1) {
		$temp_hex = '0' . $temp_hex;
	}
	$code_write .= $temp_hex;
	
	// change to uppercase
	$code_write = strtoupper($code_write);
	
	return PrinterState_setCartridgeCode($code_write, $abb_cartridge, $power_off);
}

function PrinterState_checkFilaments($array_filament = array(), &$data_json_array = array()) {
	$ret_val = 0;
	$need_filament = 0;
	$data_json = array();
	$array_abb = array();
	
	switch (PrinterState_getNbExtruder()) {
		case 1:
			$array_abb = array('r');
			break;
			
		case 2:
			$array_abb = array('l', 'r');
			break;
			
		default:
			PrinterLog_logError('number of extruder error when check filaments', __FILE__, __LINE__);
			return ERROR_INTERNAL;
			break;
	}
	
	foreach($array_abb as $abb_cartridge) {
		$need_filament = ($abb_cartridge == 'r')
		? (array_key_exists('r', $array_filament) ? $array_filament['r'] : 0)
		: (array_key_exists('l', $array_filament) ? $array_filament['l'] : 0);
		
		// jump checking if not needed
		if ($need_filament == 0) {
			PrinterLog_logMessage('Do not need filament ' . $abb_cartridge, __FILE__, __LINE__);
			continue;
		}
		
		$ret_val = PrinterState_checkFilament($abb_cartridge, $need_filament, $data_json);
		if ($ret_val != ERROR_OK) {
			return $ret_val;
		}
		
		// copy cartridge data
		$data_json_array[$abb_cartridge] = $data_json;
	}
	
	return ERROR_OK;
}

function PrinterState_checkFilament($abb_cartridge, $need_filament = 0, &$data_json = array(), $power_off = TRUE) {
	$ret_val = 0;
	$cr = 0;
	
	$ret_val = PrinterState_getCartridgeAsArray($data_json, $abb_cartridge, $power_off);
	if ($ret_val == ERROR_OK) {
		// check if cartridge is not enough
		$has_filament = $data_json[PRINTERSTATE_TITLE_INITIAL] - $data_json[PRINTERSTATE_TITLE_USED];
		if ($need_filament > $has_filament) {
			PrinterLog_logMessage('low filament error', __FILE__, __LINE__);
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

function PrinterState_getPrintCommand($rewrite = TRUE, $is_prime = FALSE) {
	global $CFG;
	$command = $CFG->config['arcontrol_p'];
	
	if ($rewrite == FALSE) {
		$command .= PRINTERSTATE_VERBATIM;
	}
	if ($is_prime == TRUE) {
		$command .= PRINTERSTATE_PRIME_END;
	}
	$command .= PRINTERSTATE_PRINT_FILE;
	
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
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('before gcode file command error', __FILE__, __LINE__);
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
			PRINTERSTATE_DELETE_SD_FILE . PRINTERSTATE_SD_FILENAME,
	);
	
	foreach($array_command as $parameter) {
		$command = $arcontrol_fullpath . $parameter;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('after gcode file command error, command: ' . $parameter, __FILE__, __LINE__);
			return FALSE;
		}
	}
	
	return TRUE;
}

function PrinterState_checkBusyStatus(&$status_current, &$array_data = array(), $printafterslice = TRUE) {
	$ret_val = 0;
	$time_wait = NULL;
	$time_max = NULL;
	
	//FIXME add timeout for checking loading / unloading
	switch ($status_current) {
		case CORESTATUS_VALUE_WAIT_CONNECT:
			$ret_val = CoreStatus_checkInConnection();
			if ($ret_val == FALSE) {
				CoreStatus_setInIdle();
				$status_current = CORESTATUS_VALUE_IDLE;
			}
			break;
			
		case CORESTATUS_VALUE_CANCEL:
			$CI = &get_instance();
			
			// jump out if it's a simulator
			if ($CI->config->item('simulator')) {
				CoreStatus_setInIdle();
				$status_current = CORESTATUS_VALUE_IDLE;
				break;
			}
			if (!file_exists(PRINTERSTATE_FILE_STOPFILE)) {
				CoreStatus_setInIdle();
				$status_current = CORESTATUS_VALUE_IDLE;
			}
			break;
			
		case CORESTATUS_VALUE_SLICE:
			// get percentage and check finished or not
			$CI = &get_instance();
			$progress = 0;
			$array_slicer = array();
			
			$CI->load->helper('slicer');
			$ret_val = Slicer_checkSlice($progress, $array_slicer);
			if ($ret_val != ERROR_OK) {
				$error_message = NULL;
				
				// handle error for slicing
				$CI->load->helper('printerlog');
				
				$array_data[PRINTERSTATE_TITLE_LASTERROR] = $ret_val;
				$status_current = CORESTATUS_VALUE_IDLE;
				switch ($ret_val) {
					//TODO treat the error with api and ui
					case ERROR_NO_SLICING:
						$error_message = 'not in slicing';
						PrinterLog_logMessage($error_message, __FILE__, __LINE__);
						break;
						
					case ERROR_WRONG_PRM:
						$error_message = 'slicer error'; // perhaps because of parameter
						PrinterLog_logMessage($error_message, __FILE__, __LINE__);
						break;
						
					case ERROR_UNKNOWN_MODEL:
						$error_message = 'slicer export error'; // perhaps because of model
						PrinterLog_logMessage($error_message, __FILE__, __LINE__);
						break;
						
					default:
						$error_message = 'slicer internal error'; // internal system error
						PrinterLog_logError($error_message, __FILE__, __LINE__);
						PrinterLog_logDebug('return: ' . $ret_val . ', progress: ' . $progress);
						break;
				}
				CoreStatus_setInIdle($ret_val, $error_message);
				$array_data[PRINTERSTATE_TITLE_DETAILMSG] = $error_message;
				
				return TRUE;
			}
			elseif ($progress == 100) {
				// copy the data we need and check filament first
				$ret_val = ERROR_OK;
				foreach ($array_slicer as $abb_filament => $volume_need) {
					$data_cartridge = array();
					$tmp_ret = 0;
					
					$tmp_ret = PrinterState_checkFilament($abb_filament, $volume_need, $data_cartridge);
// 					$array_data[$abb_filament] = array(
// 							PRINTERSTATE_TITLE_COLOR		=> $data_cartridge[PRINTERSTATE_TITLE_COLOR],
// 							PRINTERSTATE_TITLE_EXT_TEMPER	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER],
// 							PRINTERSTATE_TITLE_EXT_TEMP_1	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1],
// 							PRINTERSTATE_TITLE_NEED_L		=> $volume_need,
// 							PRINTERSTATE_TITLE_MATERIAL		=> $data_cartridge[PRINTERSTATE_TITLE_MATERIAL], // for different material check
// 					);
					if (in_array($tmp_ret, array(
							ERROR_OK, ERROR_MISS_LEFT_FILA, ERROR_MISS_RIGT_FILA,
							ERROR_LOW_LEFT_FILA, ERROR_LOW_RIGT_FILA,
					))) {
						$array_data[$abb_filament] = array(
								PRINTERSTATE_TITLE_COLOR		=> $data_cartridge[PRINTERSTATE_TITLE_COLOR],
								PRINTERSTATE_TITLE_EXT_TEMPER	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMPER],
								PRINTERSTATE_TITLE_EXT_TEMP_1	=> $data_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1],
								PRINTERSTATE_TITLE_NEED_L		=> $volume_need,
								PRINTERSTATE_TITLE_MATERIAL		=> $data_cartridge[PRINTERSTATE_TITLE_MATERIAL], // for different material check
						);
					}
					else {
						$array_data[$abb_filament] = array(
								PRINTERSTATE_TITLE_COLOR		=> PRINTERSTATE_VALUE_DEFAULT_COLOR,
								PRINTERSTATE_TITLE_EXT_TEMPER	=> SLICER_VALUE_DEFAULT_TEMPER,
								PRINTERSTATE_TITLE_EXT_TEMP_1	=> SLICER_VALUE_DEFAULT_FIRST_TEMPER,
								PRINTERSTATE_TITLE_NEED_L		=> $volume_need,
								PRINTERSTATE_TITLE_MATERIAL		=> PRINTERSTATE_DESP_MATERIAL_PLA, // for different material check
						);
					}
					
					// only assign return value when no error, so we can grap all data we need in checking loop
					if ($ret_val == ERROR_OK) {
						$ret_val = $tmp_ret;
					}
				}
				
				// save the temp file for every service (attention: do not change $ret_val in this block!)
				$status_current = CORESTATUS_VALUE_IDLE;
				try {
					$fp = fopen($CI->config->item('temp') . SLICER_FILE_TEMP_DATA, 'w');
					if ($fp) {
						fwrite($fp, json_encode($array_data));
						fclose($fp);
					}
					else {
						throw new Exception('can not open file');
					}
				} catch (Exception $e) {
					$this->load->helper('printerlog');
					PrinterLog_logError('can not save temp json file', __FILE__, __LINE__);
// 					$cr = ERROR_INTERNAL;
// 					$ret_val = ERROR_INTERNAL;
				}
				
				if ($ret_val != ERROR_OK) {
					$CI->load->helper('printerlog');
					PrinterLog_logError('check filament error after slicing, cr: ' . $ret_val, __FILE__, __LINE__);
					$array_data[PRINTERSTATE_TITLE_LASTERROR] = $ret_val;
				}
				
// 				// try to start printing after slicing if necessary
// 				if ($printafterslice == FALSE) {
				
// 				CoreStatus_setInIdle($ret_val);
				if ($ret_val == ERROR_INTERNAL) {
					CoreStatus_setInIdle(ERROR_INTERNAL);
				}
				else {
					CoreStatus_setInIdle();
				}
					
				return TRUE;
			}
			else { // still in slicing, so get percentage (estimated time is useless for now, slicer exports percentage badly)
				$array_data[PRINTERSTATE_TITLE_PERCENT] = $progress;
			}
			break;
			
		case CORESTATUS_VALUE_LOAD_FILA_L:
		case CORESTATUS_VALUE_LOAD_FILA_R:
			$time_wait = PRINTERSTATE_VALUE_OFFSET_TO_CHECK_LOAD;
			$time_max = PRINTERSTATE_VALUE_TIMEOUT_TO_CHECK_LOAD;
			
		case CORESTATUS_VALUE_UNLOAD_FILA_L:
		case CORESTATUS_VALUE_UNLOAD_FILA_R:
			$abb_filament =
					(($status_current == CORESTATUS_VALUE_LOAD_FILA_L)
							|| ($status_current == CORESTATUS_VALUE_UNLOAD_FILA_L))
					? 'l' : 'r';
// 			$time_wait = $time_wait ? $time_wait : PRINTERSTATE_VALUE_OFFSET_TO_CHECK_UNLOAD;
// 			$time_max = $time_max ? $time_max : PRINTERSTATE_VALUE_TIMEOUT_TO_CHECK_UNLOAD;
			if (is_null($time_wait) || is_null($time_max)) {
				if (file_exists(PRINTERSTATE_FILE_UNLOAD_HEAT)) {
					$time_start = @file_get_contents(PRINTERSTATE_FILE_UNLOAD_HEAT);
					if (is_null($time_start)) {
						PrinterLog_logError('check unload heat status file error', __FILE__, __LINE__);
						break;
					}
					else if (time() - $time_start <= PRINTERSTATE_VALUE_TIMEOUT_UNLOAD_HEAT) {
						// block the status if in timeout, and refresh the start time for the following state
						CoreStatus_setInUnloading($abb_filament);
						break;
					}
					else {
						// always in heating when we passed timeout, we unlock the mobile site
						PrinterLog_logError('always in heating process when we unload filament', __FILE__, __LINE__);
						@unlink(PRINTERSTATE_FILE_UNLOAD_HEAT);
						$ret_val = CoreStatus_setInIdle();
						if ($ret_val == TRUE) {
							$status_current = CORESTATUS_VALUE_IDLE;
							return TRUE;
						}
						$CI = &get_instance();
						$CI->load->helper('printerlog');
						PrinterLog_logError('can not set status into idle', __FILE__, __LINE__);
						break;
					}
				}
				
				$time_wait = PRINTERSTATE_VALUE_OFFSET_TO_CHECK_UNLOAD;
				$time_max = PRINTERSTATE_VALUE_TIMEOUT_TO_CHECK_UNLOAD;
			}
			
			// wait the time for arduino before checking filament when loading / unloading filament
			if (CoreStatus_checkInWaitTime($time_wait)) {
// 				if ($time_wait == PRINTERSTATE_VALUE_OFFSET_TO_CHECK_UNLOAD) {
// 					// check if we have finished action within max wait time only for unloading
// 					$cr = PrinterState_checkAsynchronousResponse();
// 					if ($cr == ERROR_INTERNAL) {
// 						PrinterLog_logError('check asynchronous response error', __FILE__, __LINE__);
// 						break;
// 					}
// 					else if ($cr != ERROR_OK) { // do not break if we have finished (ERROR_OK)
// 						break;
// 					}
// 				}
// 				else { // the loading filament case, we always wait the fixed time
					break;
// 				}
			}
			
			// generate parameters by different status
			$status_fin_filament =
					($status_current == CORESTATUS_VALUE_LOAD_FILA_L || $status_current == CORESTATUS_VALUE_LOAD_FILA_R)
					? TRUE : FALSE;
			
			$ret_val = PrinterState_getFilamentStatus($abb_filament);
			if ($ret_val == $status_fin_filament
					|| !CoreStatus_checkInWaitTime($time_max)) {
				if ($ret_val != $status_fin_filament) {
					$CI = &get_instance();
					$CI->load->helper('printerlog');
					PrinterLog_logError('we pass timeout when we are in changing catridge, status: ' . $status_current, __FILE__, __LINE__);
				}
				$ret_val = CoreStatus_setInIdle();
				if ($ret_val == TRUE) {
					$status_current = CORESTATUS_VALUE_IDLE;
					return TRUE; // continue to generate if we are now in idle
				}
				$CI = &get_instance();
				$CI->load->helper('printerlog');
				PrinterLog_logError('can not set status into idle', __FILE__, __LINE__);
			}
			break;
			
		default:
			// log internal API error
			$CI = &get_instance();
			$CI->load->helper('printerlog');
			PrinterLog_logError('unknown status in work json', __FILE__, __LINE__);
			break;
	}
	
	return FALSE; // status has not changed
}

function PrinterState_checkSlicedCondition(&$data_json) {
	$temp_data = array();
	$in_sliced = TRUE;
	$CI = &get_instance();
	
	// check if we need to change idle into sliced or not
	$CI->load->helper(array('corestatus', 'slicer'));
	$temp_data = array(
			$CI->config->item('temp') . SLICER_FILE_TEMP_DATA,
			$CI->config->item('temp') . SLICER_FILE_MODEL,
	);
	foreach ($temp_data as $filename) {
		if (!file_exists($filename)) {
			$in_sliced = FALSE;
		}
	}
	
	if ($in_sliced == TRUE) {
		$array_tmp = array();
		
		$data_json[PRINTERSTATE_TITLE_STATUS] = CORESTATUS_VALUE_SLICED;
		
		// try to get information of slicing
		$CI->load->helper('json');
		$array_tmp = json_read($temp_data[0], TRUE);
			
		if (isset($array_tmp['error'])) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('read json error', __FILE__, __LINE__);
		}
		else {
			$temp_data = $array_tmp['json'];
			foreach ($temp_data as $abb_filament => $array_temp) {
				$title_length = NULL;
				$title_temperature = NULL;
				
				switch ($abb_filament) {
					case 'r':
						$title_length = PRINTERSTATE_TITLE_EXT_LENG_R;
						$title_temperature = PRINTERSTATE_TITLE_EXT_TEMP_R;
						break;
						
					case 'l':
						$title_length = PRINTERSTATE_TITLE_EXT_LENG_L;
						$title_temperature = PRINTERSTATE_TITLE_EXT_TEMP_L;
						break;
							
					default:
						$CI->load->helper('printerlog');
						PrinterLog_logError('unknown extruder abb name', __FILE__, __LINE__);
						
						return ERROR_INTERNAL;
						break; // never reach here
				}
				
				$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][$title_length] = $array_temp[PRINTERSTATE_TITLE_NEED_L];
				$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][$title_temperature] = $array_temp[PRINTERSTATE_TITLE_EXT_TEMPER];
			}
		}
	}
	
	return ERROR_OK;
}

function PrinterState_checkStatusAsArray() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$output = array();
	$ret_val = 0;
	$data_json = array();
	$temp_data[PRINTERSTATE_TITLE_LASTERROR] = array();
	$time_start = NULL;
	$status_current = '';
	
	// if we need duration, the function that get duration by id is necessary
	// and we must stock print list id somewhere in json file
	$CI = &get_instance();
	$CI->load->helper('corestatus');
	
	if (CoreStatus_checkInUSB()) {
		$data_json[PRINTERSTATE_TITLE_STATUS] = CORESTATUS_VALUE_USB;
		
		return $data_json;
	}
	else if (CoreStatus_checkInConnection()) {
		$data_json[PRINTERSTATE_TITLE_STATUS] = CORESTATUS_VALUE_WAIT_CONNECT;
		
		return $data_json;
	}
	
	$ret_val = CoreStatus_checkInIdle($status_current, $status_json);
	if ($ret_val == TRUE) {
		$data_json[PRINTERSTATE_TITLE_STATUS] = CORESTATUS_VALUE_IDLE;
		//TODO think about if we need to display last error as 200 (error_ok) or not
		if (array_key_exists(CORESTATUS_TITLE_LASTERROR, $status_json) && !is_null($status_json[CORESTATUS_TITLE_LASTERROR])
				&& $status_json[CORESTATUS_TITLE_LASTERROR] != ERROR_OK) {
// 			$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_LASTERROR] = $status_json[CORESTATUS_TITLE_LASTERROR];
// 			$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_DETAILMSG] = $status_json[CORESTATUS_TITLE_MESSAGE];
			$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_SLICE_ERR]
					= $status_json[CORESTATUS_TITLE_LASTERROR] . ' ' . $status_json[CORESTATUS_TITLE_MESSAGE];
		}
		
		// check if we need to change idle into sliced or not
		PrinterState_checkSlicedCondition($data_json);
		
		return $data_json;
	}
// 	else if ($ret_val == FALSE && !in_array($status_current, array(CORESTATUS_VALUE_PRINT, CORESTATUS_VALUE_CANCEL))) {
	else if ($ret_val == FALSE && $status_current != CORESTATUS_VALUE_PRINT) {
		$temp_data = array();
		$status_old = $status_current;
		
// 		PrinterState_checkBusyStatus($status_current, $data_json);
		PrinterState_checkBusyStatus($status_current, $temp_data);
		if ($status_current == CORESTATUS_VALUE_SLICE) {
			$data_json[PRINTERSTATE_TITLE_PERCENT] = $temp_data[PRINTERSTATE_TITLE_PERCENT];
		}
		$data_json[PRINTERSTATE_TITLE_STATUS] = $status_current;
		
		// return error code in the first time when we have error from slicing=>idle
		if ($status_old == CORESTATUS_VALUE_SLICE && $status_current == CORESTATUS_VALUE_IDLE
				&& array_key_exists(PRINTERSTATE_TITLE_LASTERROR, $temp_data)) {
// 			$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_LASTERROR] = $temp_data[PRINTERSTATE_TITLE_LASTERROR];
// 			$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_DETAILMSG] = $temp_data[PRINTERSTATE_TITLE_DETAILMSG];
			$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_SLICE_ERR]
					= $temp_data[PRINTERSTATE_TITLE_LASTERROR] . ' ' . $temp_data[PRINTERSTATE_TITLE_DETAILMSG];
		}
		
		// try to change idle into sliced if necessary
		if ($status_current == CORESTATUS_VALUE_IDLE) {
			PrinterState_checkSlicedCondition($data_json);
		}
		
		return $data_json;
	}
	
	$ret_val = 0;
	$command = $arcontrol_fullpath . PRINTERSTATE_CHECK_STATE;
//	exec($command, $output, $ret_val);
//	if (!PrinterState_filterOutput($output, $command)) {
//		$CI->load->helper('printerlog');
//		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
//		return ERROR_INTERNAL;
//	}
	if (file_exists($CFG->config['printstatus'])) {
		$output = @file($CFG->config['printstatus']);
		if (count($output) == 0) {
			// case: read the percentage status file when arcontrol_cli is writing in it
			// so we let the percentage as 1 to continue printing
			$output = array('1');
			
			$CI->load->helper('printerlog');
			PrinterLog_logDebug('read percentage file when arcontrol_cli wrinting in it', __FILE__, __LINE__);
		}
	} else {
		$output = array('0');
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val == ERROR_NORMAL_RC_OK && $output) {
		// we have right return
		if ((int)$output[0] == 0) {
			// not in printing(?), now we consider it is just idle (no slicing)
			$CI->load->helper('printerlog');
			PrinterLog_logDebug('check in idle - checkstatusasarray', __FILE__, __LINE__);
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
			
			// add temperature
			if ($data_json[PRINTERSTATE_TITLE_PERCENT] != 100) {
				$data_temperature = PrinterState_getExtruderTemperaturesAsArray();
				if (!is_array($data_temperature)) {
					// log internal error
					$this->load->helper('printerlog');
					PrinterLog_logError('API error when getting temperatures in printing', __FILE__, __LINE__);
				}
				else {
					$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_EXT_TEMP_L] = $data_temperature[PRINTERSTATE_LEFT_EXTRUD];
					$data_json[PRINTERSTATE_TITLE_EXTEND_PRM][PRINTERSTATE_TITLE_EXT_TEMP_R] = $data_temperature[PRINTERSTATE_RIGHT_EXTRUD];
				}
			}
		}
	}
	else {
		$CI->load->helper('printerlog');
		PrinterLog_logError('print check status command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	// try to calculate time remained when percentage is passed offset
	$ret_val = CoreStatus_getStartTime($time_start);
	if ($ret_val == ERROR_NORMAL_RC_OK || $time_start) {
		if (isset($data_json[PRINTERSTATE_TITLE_PERCENT]) &&
				$data_json[PRINTERSTATE_TITLE_PERCENT] >= PRINTERSTATE_VALUE_OFFSET_TO_CAL_TIME) {
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

function PrinterState_cartridgeNumber2Abbreviate($number) {
	$abb_cartridge = '';
	switch ($number) {
		case PRINTERSTATE_RIGHT_EXTRUD:
			$abb_cartridge = 'r';
			break;
			
		case PRINTERSTATE_LEFT_EXTRUD:
			$abb_cartridge = 'l';
			break;
			
		default:
			$abb_cartridge = 'error';
			PrinterLog_logError('change cartridge number to abbreviate error', __FILE__, __LINE__);
			break;
	}
	
	return $abb_cartridge;
}

function PrinterState_temperatureAbb2Number($abb) {
	$num_cartridge = '';
	switch ($abb) {
		case 'TEMP 1':
			$num_cartridge = PRINTERSTATE_RIGHT_EXTRUD;
			break;
			
		case 'TEMP 2':
			$num_cartridge = PRINTERSTATE_LEFT_EXTRUD;
			break;
			
		default:
			$num_cartridge = 'error';
			PrinterLog_logError('change temperature number to cartridge error', __FILE__, __LINE__);
			break;
	}
	
	return $num_cartridge;
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
			PrinterLog_logError('input filament type error', __FILE__, __LINE__);
			return FALSE;
			break; // never reach here
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get filament status command error', __FILE__, __LINE__);
		return FALSE;
	}
	else {
		$last_output = $output[0];
		if ($last_output == 'filament') {
			return TRUE;
		}
		else if ($last_output == 'no filament') {
			return FALSE;
		}
		else {
			PrinterLog_logError('get filament api error', __FILE__, __LINE__);
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
			PrinterLog_logError('input filament type error', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break; // never reach here
	}
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
// 		exec($command, $output, $ret_val);
// 		PrinterLog_logArduino($command, $output);
// 		if ($ret_val != ERROR_NORMAL_RC_OK) {
// 			return ERROR_INTERNAL;
// 		}
		
		// check already loaded
		if (PrinterState_getFilamentStatus($abb_filament) == TRUE) {
			return ERROR_LOADED_UNLOAD;
		}
		
		// change status json file
		$ret_val = CoreStatus_setInLoading($abb_filament);
		if ($ret_val == FALSE) {
			return ERROR_INTERNAL;
		}
		
		$CI = &get_instance();
		$CI->load->helper('detectos');
		
		if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
			pclose(popen($command, 'r')); // only for windows arcontrol client
			PrinterLog_logArduino($command); // we can't check return output when using simulator
		}
		else {
			$array_cartridge = array();
			
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output, $command)) {
				PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
				return ERROR_INTERNAL;
			}
			PrinterLog_logArduino($command, $output);
			if ($ret_val != ERROR_NORMAL_RC_OK) {
				return ERROR_INTERNAL;
			}
			
			// pre-heat nozzle
			$ret_val = PrinterState_getCartridgeAsArray($array_cartridge, $abb_filament);
			if ($ret_val == ERROR_OK) {
				PrinterState_setTemperature($array_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1], 'e', $abb_filament);
			}
			else {
				PrinterLog_logError('set temperature after loading error (reading cartridge)', __FILE__, __LINE__);
			}
		}
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not load filament in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

// function PrinterState_unloadFilament($abb_filament) {
// 	global $CFG;
// 	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
// 	$output = array();
// 	$command = '';
// 	$ret_val = 0;
	
// 	// start temporary solution
// 	//TO_DO find another solution for unloading
// 	if ($CFG->config['simulator'] == TRUE) {
// 		switch ($abb_filament) {
// 			case 'l':
// 				$command = $arcontrol_fullpath . PRINTERSTATE_UNIN_FILAMENT_L;
// 				break;
					
// 			case 'r':
// 				$command = $arcontrol_fullpath . PRINTERSTATE_UNIN_FILAMENT_R;
// 				break;
					
// 			default:
// 				PrinterLog_logError('input filament type error', __FILE__, __LINE__);
// 				return ERROR_WRONG_PRM;
// 				break; // never reach here
// 		}
// 	}
// 	else {
// 		switch ($abb_filament) {
// 			case 'l':
// 				$command = $arcontrol_fullpath . PRINTERSTATE_SET_EXTRUDL;
// 				break;
					
// 			case 'r':
// 				$command = $arcontrol_fullpath . PRINTERSTATE_SET_EXTRUDR;
// 				break;
					
// 			default:
// 				PrinterLog_logError('input filament type error', __FILE__, __LINE__);
// 				return ERROR_WRONG_PRM;
// 				break; // never reach here
// 		}
// 		// M302 allow cold extrusion
// 		// G91 relative movement
// 		// G1 E-3000 F2000 unload filament until endstop
// 		$command .= '; ' . $arcontrol_fullpath . PRINTERSTATE_COLDEXTRUDE_E . '; '
// 				. $arcontrol_fullpath . PRINTERSTATE_POSITION_RELAT . '; '
// 				. $arcontrol_fullpath . ' "G1 E-3000 F2000"';
// 	}
	
// 	// check if we are in printing
// 	$ret_val = FALSE; //PrinterState_checkInPrint();
// 	if ($ret_val == FALSE) {
// // 		exec($command, $output, $ret_val);
// // 		PrinterLog_logArduino($command, $output);
// // 		if ($ret_val != ERROR_NORMAL_RC_OK) {
// // 			return ERROR_INTERNAL;
// // 		}
		
// 		// check already unloaded
// 		if (PrinterState_getFilamentStatus($abb_filament) == FALSE) {
// 			return ERROR_LOADED_UNLOAD;
// 		}
		
// 		// change status json file
// 		$ret_val = CoreStatus_setInUnloading($abb_filament);
// 		if ($ret_val == FALSE) {
// 			return ERROR_INTERNAL;
// 		}
		
// 		$CI = &get_instance();
// 		$CI->load->helper('detectos');
		
// 		if ($CFG->config['simulator'] || DectectOS_checkWindows()) {
// 			pclose(popen($command, 'r')); // only for windows arcontrol client
// 			PrinterLog_logArduino($command); //FIX_ME we can't check return output when using simulator
// 		}
// 		else {
// 			exec($command, $output, $ret_val);
// 			if (!PrinterState_filterOutput($output, $command)) {
// 				PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
// 				return ERROR_INTERNAL;
// 			}
// 			PrinterLog_logArduino($command, $output);
// 			if ($ret_val != ERROR_NORMAL_RC_OK) {
// 				PrinterLog_logError('unload filament error', __FILE__, __LINE__);
// 				return ERROR_INTERNAL;
// 			}
// 		}
// 	} else {
// // 		return ERROR_IN_PRINT;
// 		PrinterLog_logError('can not unload filament in printing', __FILE__, __LINE__);
// 		return ERROR_BUSY_PRINTER;
// 	}
	
// 	return ERROR_OK;
// }

function PrinterState_unloadFilament($abb_filament) {
	$CI = &get_instance();
// 	$arcontrol_fullpath = $CI->config->item('arcontrol_c');
	$arcontrol_fullpath = $CI->config->item('siteutil');
	$output = array();
	$array_cartridge = array();
	$command = '';
	$ret_val = 0;
	
	// start alter temporary solution
// 	switch ($abb_filament) {
// 		case 'l':
// 			$command = $arcontrol_fullpath . PRINTERSTATE_UNIN_FILAMENT_L;
// 			break;
				
// 		case 'r':
// 			$command = $arcontrol_fullpath . PRINTERSTATE_UNIN_FILAMENT_R;
// 			break;
				
// 		default:
// 			PrinterLog_logError('input filament type error', __FILE__, __LINE__);
// 			return ERROR_WRONG_PRM;
// 			break; // never reach here
// 	}
	$ret_val = PrinterState_getCartridgeAsArray($array_cartridge, $abb_filament);
	if ($ret_val != ERROR_OK) {
		PrinterLog_logError('read cartridge error when unloading', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	$command = $arcontrol_fullpath . PRINTERSTATE_UNLOAD_FILAMENT . $abb_filament . ' ' . $array_cartridge[PRINTERSTATE_TITLE_EXT_TEMP_1];
	
	$CI->load->helper('detectos');
	if ($CI->config->item('simulator') == FALSE && !DectectOS_checkWindows()) {
// 		$command .= ' > ' . PRINTERSTATE_FILE_RESPONSE . ' &';
		$command .= ' &';
	}
	else {
		$command = 'start /B ' . $command;
	}
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		
		// check already unloaded
		if (PrinterState_getFilamentStatus($abb_filament) == FALSE) {
			return ERROR_LOADED_UNLOAD;
		}
		
		// change status json file
		$ret_val = CoreStatus_setInUnloading($abb_filament);
		if ($ret_val == FALSE) {
			return ERROR_INTERNAL;
		}
		
		pclose(popen($command, 'r')); // only for windows arcontrol client
		PrinterLog_logArduino($command); // we can't log return output when using this solution
	} else {
// 		return ERROR_IN_PRINT;
		PrinterLog_logError('can not unload filament in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_OK;
}

function PrinterState_checkAsynchronousResponse() {
	// this function is only for real printer
	$array_response = array();
	$CI = &get_instance();
	$CI->load->helper('detectos');
	
	if ($CI->config->item('simulator') == TRUE) {
		PrinterLog_logDebug('call check asynchronous response in simulator', __FILE__, __LINE__);
	}
	else if (!file_exists(PRINTERSTATE_FILE_RESPONSE)) {
		PrinterLog_logError('no asynchronous response file found', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$array_response = @file(PRINTERSTATE_FILE_RESPONSE);
		if (!PrinterState_filterOutput($array_response, '', FALSE)) { //TODO test me with empty command
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		
		if ($array_response) {
			if (strtolower($array_response[count($array_response) - 1]) == 'ok') {
				return ERROR_OK;
			}
		}
		else {
			PrinterLog_logMessage('no message in asynchronous response file', __FILE__, __LINE__);
		}
	}
	
	return ERROR_BUSY_PRINTER;
}

// function PrinterState_afterUnloadFilament() {
// 	global $CFG;
// 	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
// 	$output = array();
// 	$command = $arcontrol_fullpath . PRINTERSTATE_AFTER_UNIN_FILA;
// 	$ret_val = 0;
	
// 	exec($command, $output, $ret_val);
// 	if (!PrinterState_filterOutput($output, $command)) {
// 		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
// 		return ERROR_INTERNAL;
// 	}
// 	PrinterLog_logArduino($command, $output);
// 	if ($ret_val != ERROR_NORMAL_RC_OK) {
// 		PrinterLog_logError('after unload filament error', __FILE__, __LINE__);
// 		return ERROR_INTERNAL;
// 	}
	
// 	return ERROR_OK;
// }

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
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		// print special gcode model to reset printer's temperatures and position
		// we leave this printing call function in Printer_stopPrint()
	} else {
		PrinterLog_logMessage('we are not in printing when calling stop printing', __FILE__, __LINE__);
		return ERROR_NO_PRINT;
	}
	
	return ERROR_OK;
}

function PrinterState_runGcodeFile($gcode_path, $rewrite = TRUE) {
	global $CFG;
	$command = '';
	$CI = &get_instance();

	$CI->load->helper(array('detectos', 'printer'));
	
// 	if (!PrinterState_beforeFileCommand()) {
// 		return ERROR_INTERNAL;
// 	}
	if ($rewrite == TRUE) {
		Printer_preparePrint();
	}
	$command = PrinterState_getPrintCommand($rewrite) . $gcode_path;
	
	// we can't check return output
	if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
		pclose(popen($command, 'r')); // only for windows arcontrol client
		PrinterLog_logArduino($command);
	}
	else {
		pclose(popen($command . ' > ' . PRINTERSTATE_FILE_PRINTLOG . ' &', 'r'));
		PrinterLog_logArduino($command);
		
// 		exec($command, $output, $ret_val);
// 		if (!PrinterState_filterOutput($output, $command)) {
// 			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
// 			return ERROR_INTERNAL;
// 		}
// 		PrinterLog_logArduino($command, $output);
// 		if ($ret_val != ERROR_NORMAL_RC_OK) {
// 			return ERROR_INTERNAL;
// 		}
	}
	
// 	if (!PrinterState_afterFileCommand()) {
// 		return ERROR_INTERNAL;
// 	}
	
	return TRUE;
}

function PrinterState_runGcode($gcodes, $rewrite = TRUE, $need_return = FALSE, &$return_data = '') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$tmpfile_fullpath = $CFG->config['temp'] . '_runGcode.gcode';
	$output = array();
	$command = '';
	$ret_val = 0;
	
	if ($need_return && is_array($gcodes)) {
		foreach ($gcodes as $gcode) {
			$command = $arcontrol_fullpath . ' "' . $gcode . '"';
			//TO_DO some gcode will not be responsed directly when using simulator
			exec($command, $output, $ret_val);
			if (!PrinterState_filterOutput($output, $command)) {
				PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
				return ERROR_INTERNAL;
			}
			PrinterLog_logArduino($command, $output);
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
		
		return PrinterState_runGcodeFile($tmpfile_fullpath, $rewrite);
	}
	else {
		return FALSE;
	}
	
	return TRUE;
}

function PrinterState_filterOutput(&$output, $command = '', $trim_ok = TRUE) {
	global $PRINTER;
	
	if (!is_array($output)) {
		return FALSE;
	}
	else if (empty($output)) {
		$PRINTER['last_command'] = $command;
		return TRUE;
	}
	else {
		$retry = 0;
		
		do {
			if ($retry != 0) {
				$output = array();
				exec($command, $output);
			}
			
			// assign output to temp and empty output array
			$array_tmp = $output;
			$output = array();
			$retry_flag = FALSE;
			
			// filter empty line
// 			$array_tmp = array_filter($array_tmp, "PrinterState__checkLine");
			
			// filter the output not necessary
			foreach($array_tmp as $line) {
				// jump the empty line
				$line = trim($line, " \t\n\r\0\x0B");
				if ($line == '') {
					continue;
				}
				
				// check it start with [<-] or [->], then filter it
				//filter the input
				if (strpos($line, '[->]') === 0) {
					continue;
				}
// 				$line = preg_replace('[\[->\]]', '', $line, 1);
				$line = preg_replace('[\[<-\]]', '', $line, 1);
				$line = trim($line, " \t\n\r\0\x0B");
				if ($line == '') {
					continue;
				}
				$output[] = $line;
				if ($line == 'END_INITIALISATION') {
					$sso_message = 'Marlin reset; current: ' . $command . ', last: '
							. ((is_array($PRINTER) && array_key_exists('last_command', $PRINTER))
									 ? $PRINTER['last_command'] : 'N/A');
					PrinterLog_logSSO(3, 503, $sso_message);
					$PRINTER['last_command'] = $command;
					
					PrinterLog_logArduino($command, json_encode($output));
					++$retry;
					$retry_flag = TRUE;
					break;
				}
			}
			
			if ($retry_flag == FALSE) { 
				break; // it's ok if we reach here, so breakout the loop
			}
		} while ($retry < 2);
		
		if (empty($output)) {
			$PRINTER['last_command'] = $command;
			PrinterLog_logMessage('no return from arduino', __FILE__, __LINE__);
			return TRUE;
		}
		
		// filter the ok message in the end of array
		if ($trim_ok == TRUE && strtolower($output[count($output) - 1]) == 'ok') {
			unset($output[count($output) - 1]);
		}
	}
	
	$PRINTER['last_command'] = $command;
	
	return TRUE;
}

function PrinterState_getNbExtruder() {
	global $CFG;
	$tmp_array = array();
	$printerinfo_fullpath = $CFG->config['hardconf'] . PRINTERSTATE_JSON_PRINTER;
	
	$CI= &get_instance();
	$CI->load->helper('json');
	
	try {
		$tmp_array = json_read($printerinfo_fullpath, TRUE);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('read printer json error', __FILE__, __LINE__);
		return 0;
	}
	
	return $tmp_array['json'][PRINTERSTATE_TITLE_JSON_NB_EXTRUD];
}

function PrinterState_getPrintSize(&$size_array) {
	global $CFG;
	$tmp_array = array();
	$printerinfo_fullpath = $CFG->config['hardconf'] . PRINTERSTATE_JSON_PRINTER;
	
	$CI= &get_instance();
	$CI->load->helper('json');
	
	$size_array = array();
	try {
		$tmp_array = json_read($printerinfo_fullpath, TRUE);
		if ($tmp_array['error']) {
			throw new Exception('read json error');
		}
	} catch (Exception $e) {
		$CI = &get_instance();
		$CI->load->helper('printerlog');
		PrinterLog_logError('read printer json error', __FILE__, __LINE__);
		return 0;
	}
	
	foreach (array(PRINTERSTATE_TITLE_PRINT_XMAX, PRINTERSTATE_TITLE_PRINT_YMAX, PRINTERSTATE_TITLE_PRINT_ZMAX) as $key) {
		if (isset($tmp_array['json'][$key])) {
			$size_array[$key] = $tmp_array['json'][$key];
		}
	}
	
	return;
}

function PrinterState_getMarlinVersion(&$version_marlin) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = '';
	$ret_val = 0;
	$output = array();
	
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_MARLIN_VER;
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		return ERROR_INTERNAL;
	}
	else {
		$version_marlin = $output ? trim($output[0]) : NULL;
	}
	
	return ERROR_OK;
}

function PrinterState_getInfoAsArray() {
	$CI = &get_instance();
	$CI->load->helper('zimapi');
	$version_marlin = NULL;
	$name_sso = NULL;
	$hostname = NULL;
	$network_data = array();
	$platform_size = array();
	$array_return= array();
	$cr = 0;
	
	$cr = PrinterState_getMarlinVersion($version_marlin);
	if ($cr != ERROR_OK) {
		$version_marlin = 'N/A';
	}
	$cr = ZimAPI_getHostname($hostname);
	if ($cr != ERROR_OK) {
		$hostname = 'N/A';
	}
	PrinterState_getPrintSize($platform_size);
	
	$cr = ZimAPI_getNetworkInfoAsArray($network_data);
	$array_return = array(
			PRINTERSTATE_TITLE_VERSION		=> ZimAPI_getVersion(),
			PRINTERSTATE_TITLE_VERSION_N	=> ZimAPI_getVersion(TRUE),
			PRINTERSTATE_TITLE_TYPE			=> ZimAPI_getType(),
			PRINTERSTATE_TITLE_SERIAL		=> ZimAPI_getSerial(),
			PRINTERSTATE_TITLE_NB_EXTRUD	=> PrinterState_getNbExtruder(),
			PRINTERSTATE_TITLE_VER_MARLIN	=> $version_marlin,
			PRINTERSTATE_TITLE_HOSTNAME		=> $hostname,
			ZIMAPI_TITLE_IP					=> ($cr == ERROR_OK && !is_null($network_data[ZIMAPI_TITLE_IP]))
					 ? $network_data[ZIMAPI_TITLE_IP] : 'N/A',
	);
	
	foreach ($platform_size as $key => $value) {
		$array_return[$key] = $value;
	}

	$cr = ZimAPI_getPrinterSSOName($name_sso);
	if ($cr == ERROR_OK && $name_sso != NULL) {
		$array_return[PRINTERSTATE_TITLE_SSO_NAME] = $name_sso;
	}
	
	return $array_return;
}

function PrinterState_homing($axis = 'ALL') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	$axis = strtoupper($axis);
	
	switch($axis) {
		case 'X':
		case 'Y':
		case 'Z':
			$command = $arcontrol_fullpath . PRINTERSTATE_HOMING . '\ ' . $axis;
			break;
			
		case 'ALL':
			$command = $arcontrol_fullpath . PRINTERSTATE_HOMING;
			break;
			
		default:
			PrinterLog_logError('axis type error', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	
	if ($CFG->config['simulator']) {
		$command = str_replace('\ ', ' ', $command);
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('homeing error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_move($axis, $value, $speed = NULL) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	$axis = strtoupper($axis);
	
	if ($axis != 'E' && ($value < -150 || $value > 150)) {
		return ERROR_WRONG_PRM;
	}
	else if ($axis == 'E') {
		$command = $arcontrol_fullpath . PRINTERSTATE_EXTRUDE_RELAT;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			PrinterLog_logError('relative extrude error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		$output = array();
	}
	
	switch($axis) {
		case 'X':
		case 'Y':
		case 'Z':
		case 'E':
			$command = $arcontrol_fullpath . PRINTERSTATE_MOVE . $axis . $value;
			if (!is_null($speed)) {
				$command .= '\ F' . $speed;
			}
			break;
			
		default:
			PrinterLog_logError('axis type error', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	
	if ($CFG->config['simulator']) {
		$command = str_replace('\ ', ' ', $command);
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('move error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_extrude($value = PRINTERSTATE_VALUE_DEFAULT_EXTRUD) {
	if ($value == 0) {
		return ERROR_WRONG_PRM;
	}
	
	return PrinterState_move('E', $value);
}

function PrinterState_setStripLed($value = 'off') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	switch($value) {
		case 'off':
			$command = $arcontrol_fullpath . PRINTERSTATE_STRIP_LED_OFF;
			break;
			
		case 'on':
			$command = $arcontrol_fullpath . PRINTERSTATE_STRIP_LED_ON;
			break;
			
		default:
			PrinterLog_logError('set strip led value error', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set strip led error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_getStripLedStatus(&$value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_STRIP_LED;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get strip LED status command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$last_output = $output[0];
		if ($last_output == '1') {
			$value = TRUE;
		}
		else if ($last_output == '0') {
			$value = FALSE;
		}
		else {
			PrinterLog_logError('get strip api error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_getTopLedStatus(&$value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_TOP_LED;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get top LED status command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;;
	}
	else {
		$last_output = $output[0];
		if ($last_output == '1') {
			$value = TRUE;
		}
		else if ($last_output == '0') {
			$value = FALSE;
		}
		else {
			PrinterLog_logError('get top api error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_setHeadLed($value = 'off') {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	switch($value) {
		case 'off':
			$command = $arcontrol_fullpath . PRINTERSTATE_HEAD_LED_OFF;
			break;
			
		case 'on':
			$command = $arcontrol_fullpath . PRINTERSTATE_HEAD_LED_ON;
			break;
			
		default:
			PrinterLog_logError('set head led value error', __FILE__, __LINE__);
			return ERROR_WRONG_PRM;
			break;
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set head led error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_disableSteppers() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	$command = $arcontrol_fullpath . PRINTERSTATE_STEPPER_OFF;
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set motors off error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_relativePositioning($on = TRUE) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	if ($on == TRUE) {
		$command = $arcontrol_fullpath . PRINTERSTATE_POSITION_RELAT;
	}
	else {
		$command = $arcontrol_fullpath . PRINTERSTATE_POSITION_ABSOL;
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set relative positioning error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_pausePrinting() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == TRUE) {
		// send stop gcode
		$command = $arcontrol_fullpath . PRINTERSTATE_PAUSE_PRINT;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		//TODO print special gcode model to reset printer's temperatures and position
	} else {
		PrinterLog_logMessage('we are not in printing when calling pause printing', __FILE__, __LINE__);
		return ERROR_NO_PRINT;
	}
	
	return ERROR_OK;
}

function PrinterState_resumePrinting() {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	// check if we are in printing
	$ret_val = PrinterState_checkInPrint();
	if ($ret_val == TRUE) {
		// send stop gcode
		$command = $arcontrol_fullpath . PRINTERSTATE_RESUME_PRINT;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		//TODO print special gcode model to reset printer's temperatures and position
	} else {
		PrinterLog_logMessage('we are not in printing when calling resume printing', __FILE__, __LINE__);
		return ERROR_NO_PRINT;
	}
	
	return ERROR_OK;
}

function PrinterState_getEndstop($abb_endstop, &$status) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	// check input
	if (!in_array($abb_endstop, array('xmin', 'xmax', 'ymin', 'ymax', 'zmin', 'zmax'))) {
		return ERROR_WRONG_PRM;
	}
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		$command = $arcontrol_fullpath . PRINTERSTATE_GET_ENDSTOPS;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		// treat return of arduino
		if (count($output) > 1) {
			$status = NULL;
			foreach ($output as $line) {
				if (strpos($line, ':') === FALSE) {
					continue;
				}
				$tmp_array = explode(':', $line);
				$endstop_line = str_replace('_', '', trim($tmp_array[0]));
				if ($endstop_line == $abb_endstop) {
					$status = (strtolower(trim($tmp_array[1])) == PRINTERSTATE_VALUE_ENDSTOP_OPEN) ? FALSE : TRUE;
					return ERROR_OK;
					break;
				}
			}
		}
		else {
			// no usful return
			PrinterLog_logError('no arduino return', __FILE__, __LINE__);
		}
	} else {
		PrinterLog_logMessage('call getting end stop in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_INTERNAL;
}

function PrinterState_getEndstopList(&$array_status = array()) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	// check if we are in printing
	$ret_val = FALSE; //PrinterState_checkInPrint();
	if ($ret_val == FALSE) {
		$command = $arcontrol_fullpath . PRINTERSTATE_GET_ENDSTOPS;
		exec($command, $output, $ret_val);
		if (!PrinterState_filterOutput($output, $command)) {
			PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
		PrinterLog_logArduino($command, $output);
		if ($ret_val != ERROR_NORMAL_RC_OK) {
			return ERROR_INTERNAL;
		}
		
		// treat return of arduino
		if (count($output) > 1) {
			$status = NULL;
			foreach ($output as $line) {
				if (strpos($line, ':') === FALSE) {
					continue;
				}
				$tmp_array = explode(':', $line);
				$endstop_line = str_replace('_', '', trim($tmp_array[0]));
				$array_status[$endstop_line] = (strtolower(trim($tmp_array[1])) == PRINTERSTATE_VALUE_ENDSTOP_OPEN)
						? FALSE : TRUE;
			}
			
			return ERROR_OK;
		}
		else {
			// no usful return
			PrinterLog_logError('no arduino return', __FILE__, __LINE__);
		}
	} else {
		PrinterLog_logMessage('call getting end stop in printing', __FILE__, __LINE__);
		return ERROR_BUSY_PRINTER;
	}
	
	return ERROR_INTERNAL;
}

function PrinterState_getSpeed(&$value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_SPEED;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get speed command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$last_output = $output[0];
		$value = (int)$last_output;
		if ($value == 0) {
			PrinterLog_logError('get speed api error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_getAcceleration(&$value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_ACCELERATION;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get acceleration command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$last_output = $output[0];
		$value = (int)$last_output;
		if ($value == 0) {
			PrinterLog_logError('get acceleration api error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_getColdExtrusion(&$value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_COLDEXTRUDE;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get cold extrude status command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$last_output = $output[0];
		if ($last_output == '1') {
			$value = TRUE;
		}
		else if ($last_output == '0') {
			$value = FALSE;
		}
		else {
			PrinterLog_logError('get cold extrude api error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_setSpeed($value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_SET_SPEED;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	if ($value <= 0) {
		return ERROR_WRONG_PRM;
	}
	else {
		$command .= $value;
	}
	
	if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
		// remove the symbol "\" for simulator
		$command = str_replace('\ ', ' ', $command);
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set speed command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_setAcceleration($value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_SET_ACCELERATION;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	if ($value <= 0) {
		return ERROR_WRONG_PRM;
	}
	else {
		$command .= $value;
	}
	
	if ($CFG->config['simulator'] && DectectOS_checkWindows()) {
		// remove the symbol "\" for simulator
		$command = str_replace('\ ', ' ', $command);
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set acceleration command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_getRFIDPower(&$value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath . PRINTERSTATE_GET_RFID_POWER;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get rfid power status command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$last_output = $output[0];
		if ($last_output == '1') {
			$value = TRUE;
		}
		else if ($last_output == '0') {
			$value = FALSE;
		}
		else {
			PrinterLog_logError('get rfid power api error', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_setRFIDPower($on = TRUE) {
	// as preview, the RFID will automatically active when we read and write
	// so normally, we need only turn off the power
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = '';
	$ret_val = 0;
	
	// temporary change - disable all rfid reader power control
	return ERROR_OK;
	//FIXME change it as soon as possible
	
	if ($on == TRUE) {
		$command = $arcontrol_fullpath . PRINTERSTATE_RFID_POWER_ON;
	}
	else {
		$command = $arcontrol_fullpath . PRINTERSTATE_RFID_POWER_OFF;
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set rfid power error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_raisePlatform() {
	// as preview, the RFID will automatically active when we read and write
	// so normally, we need only turn off the power
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$output = array();
	$command = $arcontrol_fullpath . PRINTERSTATE_RAISE_PLATFORM;
	$ret_val = 0;
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('raise platform error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	
	return ERROR_OK;
}

function PrinterState_getOffset($axis, &$value) {
	global $CFG;
	$arcontrol_fullpath = $CFG->config['arcontrol_c'];
	$command = $arcontrol_fullpath;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	$axis = strtoupper($axis);
	switch ($axis) {
		case 'X':
			$command .= PRINTERSTATE_GET_OFFSET_X;
			break;
			
		case 'Y':
			$command .= PRINTERSTATE_GET_OFFSET_Y;
			break;
			
		default:
			return ERROR_WRONG_PRM;
			break;
	}
	
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('get offset command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else {
		$value = $output[0];
		if ($value > 100 || $value < -100) {
			PrinterLog_logError('get offset value out of region', __FILE__, __LINE__);
			return ERROR_INTERNAL;
		}
	}
	
	return ERROR_OK;
}

function PrinterState_setOffset($array_data = array()) {
	//TODO finish me
	$CI = &get_instance();
	$arcontrol_fullpath = $CI->config->item('arcontrol_c');
	$command = $arcontrol_fullpath . PRINTERSTATE_SET_OFFSET;
	$ret_val = 0;
	$output = array();
	$last_output = '';
	
	foreach ($array_data as $axis => $value) {
		if ($value < -100 || $value > 100) {
			return ERROR_WRONG_PRM;
		}
		
		$axis = strtoupper($axis);
		switch ($axis) {
			case 'X':
				$command .= PRINTERSTATE_OFFSET_X_LABEL . $value;
				break;
				
			case 'Y':
				$command .= PRINTERSTATE_OFFSET_Y_LABEL . $value;
				break;
				
			default:
				return ERROR_WRONG_PRM;
				break; // never reach here
		}
	}
	
	if ($CI->config->item('simulator')) {
		$command = str_replace('\ ', ' ', $command);
	}
	exec($command, $output, $ret_val);
	if (!PrinterState_filterOutput($output, $command)) {
		PrinterLog_logError('filter arduino output error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	PrinterLog_logArduino($command, $output);
	if ($ret_val != ERROR_NORMAL_RC_OK) {
		PrinterLog_logError('set offset command error', __FILE__, __LINE__);
		return ERROR_INTERNAL;
	}
	else if (count($output) && FALSE !== strpos($output[0], 'Error')) {
		PrinterLog_logError('set offset command message error', __FILE__, __LINE__);
		return ERROR_WRONG_PRM;
	}
	
	return ERROR_OK;
}

//internal function
function PrinterState__updateCartridge(&$code_cartridge, $abb_cartridge) {
	$CI = &get_instance();
	$file_path = $CI->config->item('base_data') . PRINTERSTATE_FILE_UPDATE_RFID;
	
	if (file_exists($file_path)) {
		$data_json = array();
		$temp_code = NULL;
		
		try {
			$tmp_array = @json_read($file_path, TRUE);
			if ($tmp_array['error']) {
				throw new Exception('read json error');
			}
			else {
				$data_json = $tmp_array['json'];
			}
		} catch (Exception $e) {
			$CI->load->helper('printerlog');
			PrinterLog_logError('read cartridge update data error', __FILE__, __LINE__);
			
			return; // log error and return
		}
		
		$temp_code = substr($code_cartridge, 0, 26);
		if (array_key_exists($temp_code, $data_json)) {
			$temp_hex = 0;
			$ret_val = 0;
			
			$CI->load->helper('printerlog');
			PrinterLog_logDebug('detected a cartridge to update', __FILE__, __LINE__);
			// add date in the end
			$temp_code = $data_json[$temp_code] . substr($code_cartridge, 26, 4);
			
			// calculate checksum
			for($i=0; $i<=14; $i++) {
				$string_tmp = substr($temp_code, $i*2, 2);
				$hex_tmp = hexdec($string_tmp);
				$temp_hex = $temp_hex ^ $hex_tmp;
			}
			$temp_hex = dechex($temp_hex);
			if (strlen($temp_hex) == 1) {
				$temp_hex = '0' . $temp_hex;
			}
			$temp_code .= strtoupper($temp_hex);
			$code_cartridge = $temp_code;
			
			$ret_val = PrinterState_setCartridgeCode($temp_code, $abb_cartridge);
			if ($ret_val != ERROR_OK) {
				// log error and return
				$CI->load->helper('printerlog');
				PrinterLog_logError('write cartridge error when in updating cartridge from database', __FILE__, __LINE__);
			}
		}
	}
	
	return;
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
